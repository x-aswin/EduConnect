<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\CertificateSignatory;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class CertificateController extends Controller
{
    /**
     * Display the list of certified courses and their certificate status.
     */
    public function index(Request $request)
    {
        $college = auth()->user()->college;

        $courses = $this->filteredCoursesQuery($request, $college->id)->get();

        // Stats calculation based on all certified courses of the college
        $allCertifiedCourses = Course::where('college_id', $college->id)
            ->where('is_certified', true)
            ->with(['enrollments' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->get();

        $totalCourses = $allCertifiedCourses->count();
        $endedCourses = $allCertifiedCourses->filter(fn($c) => \Carbon\Carbon::parse($c->end_date)->isPast())->count();
        $totalConfirmedEnrollments = $allCertifiedCourses->sum(fn($c) => $c->enrollments->count());
        $totalIssuedCertificates = $allCertifiedCourses->sum(fn($c) => $c->enrollments->where('certificate_issued', true)->count());

        return view('college.certificate', compact(
            'courses',
            'totalCourses',
            'endedCourses',
            'totalConfirmedEnrollments',
            'totalIssuedCertificates'
        ));
    }

    /**
     * Show the signatory configuration form for a specific course (via Modal).
     */
    public function edit(Request $request, Course $course)
    {
        abort_unless($course->college_id === auth()->user()->college->id, 403, 'Unauthorized access.');

        $college = auth()->user()->college;

        $courses = $this->filteredCoursesQuery($request, $college->id)->get();
        $editCourse = $course->load('signatories');
        $queryParams = collect($request->query())->toArray();
        $indexUrl = route('college.certificate', $queryParams);

        // Stats calculation based on all certified courses of the college
        $allCertifiedCourses = Course::where('college_id', $college->id)
            ->where('is_certified', true)
            ->with(['enrollments' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->get();

        $totalCourses = $allCertifiedCourses->count();
        $endedCourses = $allCertifiedCourses->filter(fn($c) => \Carbon\Carbon::parse($c->end_date)->isPast())->count();
        $totalConfirmedEnrollments = $allCertifiedCourses->sum(fn($c) => $c->enrollments->count());
        $totalIssuedCertificates = $allCertifiedCourses->sum(fn($c) => $c->enrollments->where('certificate_issued', true)->count());

        return view('college.certificate', compact(
            'courses',
            'editCourse',
            'indexUrl',
            'totalCourses',
            'endedCourses',
            'totalConfirmedEnrollments',
            'totalIssuedCertificates'
        ));
    }

    /**
     * Helper to build the filtered query for courses.
     */
    private function filteredCoursesQuery(Request $request, int $collegeId)
    {
        $query = Course::where('college_id', $collegeId)
            ->where('is_certified', true)
            ->with(['signatories', 'enrollments' => function ($query) {
                $query->where('status', 'confirmed')
                      ->with('user.firm'); // Load firm name for firm enrollments
            }]);

        // Search filter
        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where('title', 'like', "%{$search}%");
        }

        // Signatory status filter
        $signatoriesStatus = $request->input('signatories_status');
        if ($signatoriesStatus === 'configured') {
            $query->has('signatories');
        } elseif ($signatoriesStatus === 'pending') {
            $query->doesntHave('signatories');
        }

        // Course status filter
        $status = $request->input('status');
        if ($status === 'ended') {
            $query->whereDate('end_date', '<', now()->toDateString());
        } elseif ($status === 'ongoing') {
            $query->whereDate('end_date', '>=', now()->toDateString());
        }

        // Sort
        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->orderBy('end_date', 'asc');
        } elseif ($sort === 'title_asc') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'title_desc') {
            $query->orderBy('title', 'desc');
        } else {
            $query->orderBy('end_date', 'desc');
        }

        return $query;
    }

    /**
     * Save signatories and mark certificates as issued for all eligible enrollments.
     */
    public function issue(Request $request, Course $course)
    {
        // 1. Security check: Ensure the college actually owns this course
        abort_unless($course->college_id === auth()->user()->college->id, 403, 'Unauthorized access.');

        // 2. Validate the incoming signatory data
        $request->validate([
            'signatories' => 'required|array|min:1|max:2',
            // First signatory is strictly required
            'signatories.0.name' => 'required|string|max:255',
            'signatories.0.designation' => 'required|string|max:255',
            // Second signatory is optional
            'signatories.1.name' => 'nullable|string|max:255',
            'signatories.1.designation' => 'nullable|string|max:255',
            // Images
            'signatories.*.signature_image' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signatories.*.existing_image' => 'nullable|string',
        ]);

        // 3. Database Transaction to ensure both signatures and issuances save together or fail together
        DB::transaction(function () use ($request, $course) {
            
            // --- A. Process Signatories ---
            foreach ($request->signatories as $index => $sigData) {
                // Skip the optional second signatory if it was left completely blank
                if ($index === 1 && empty($sigData['name']) && empty($sigData['designation']) && !isset($sigData['signature_image']) && empty($sigData['existing_image'])) {
                    continue;
                }

                $imagePath = $sigData['existing_image'] ?? null;

                // Handle a new image upload
                if ($request->hasFile("signatories.$index.signature_image")) {
                    // Delete the old signature image from storage to save space
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    
                    // Store the new image in 'storage/app/public/signatures'
                    $imagePath = $request->file("signatories.$index.signature_image")->store('signatures', 'public');
                }

                // Create or update the signatory record
                CertificateSignatory::updateOrCreate(
                    [
                        'course_id' => $course->id, 
                        'display_order' => $index + 1 // 1 for first, 2 for second
                    ],
                    [
                        'name' => $sigData['name'],
                        'designation' => $sigData['designation'],
                        'signature_image' => $imagePath,
                    ]
                );
            }

            // --- B. Issue Certificates to Learners ---
            // Update all confirmed enrollments for this course that haven't received a cert yet
            Enrollment::where('course_id', $course->id)
                ->where('status', 'confirmed')
                ->where('certificate_issued', false)
                ->update([
                    'certificate_issued' => true,
                    'certificate_issued_at' => now(),
                    // Note: We leave 'certificate_code' null here. Best practice is to 
                    // generate the unique code later, right when the student hits "Download".
                ]);
        });

        return redirect()->route('college.certificate')
            ->with('success', 'Signatories saved and certificates successfully issued for ' . $course->title);
    }

    /**
     * Issue a certificate for a single specific enrollment (used for firm courses
     * where the same course can have multiple separate enrollments).
     * Signatories are shared at the course level; only this enrollment is marked as issued.
     */
    public function issueForEnrollment(Request $request, Course $course, Enrollment $enrollment)
    {
        // 1. Security: college must own the course, and enrollment must belong to it
        abort_unless($course->college_id === auth()->user()->college->id, 403, 'Unauthorized access.');
        abort_unless($enrollment->course_id === $course->id, 404, 'Enrollment does not belong to this course.');
        abort_unless($enrollment->status === 'confirmed', 422, 'Enrollment is not confirmed.');

        // 2. Validate signatories
        $request->validate([
            'signatories'                    => 'required|array|min:1|max:2',
            'signatories.0.name'             => 'required|string|max:255',
            'signatories.0.designation'      => 'required|string|max:255',
            'signatories.1.name'             => 'nullable|string|max:255',
            'signatories.1.designation'      => 'nullable|string|max:255',
            'signatories.*.signature_image'  => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signatories.*.existing_image'   => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $course, $enrollment) {

            // --- A. Process Signatories (shared at course level) ---
            foreach ($request->signatories as $index => $sigData) {
                if ($index === 1 && empty($sigData['name']) && empty($sigData['designation']) && !isset($sigData['signature_image']) && empty($sigData['existing_image'])) {
                    continue;
                }

                $imagePath = $sigData['existing_image'] ?? null;

                if ($request->hasFile("signatories.$index.signature_image")) {
                    if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    $imagePath = $request->file("signatories.$index.signature_image")->store('signatures', 'public');
                }

                CertificateSignatory::updateOrCreate(
                    ['course_id' => $course->id, 'display_order' => $index + 1],
                    [
                        'name'            => $sigData['name'],
                        'designation'     => $sigData['designation'],
                        'signature_image' => $imagePath,
                    ]
                );
            }

            // --- B. Issue certificate for this specific enrollment only ---
            if (!$enrollment->certificate_issued) {
                $enrollment->update([
                    'certificate_issued'    => true,
                    'certificate_issued_at' => now(),
                ]);
            }
        });

        $firmName = $enrollment->user?->firm?->org_name ?? $enrollment->user?->name ?? 'the firm';

        return redirect()->route('college.certificate')
            ->with('success', 'Certificate issued for enrollment by ' . $firmName . ' in ' . $course->title . '.');
    }
}
