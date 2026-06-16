<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\CertificateSignatory;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    /**
     * Display the list of certified courses and their certificate status.
     */
    public function index()
    {
        $college = auth()->user()->college;

        // Fetch courses that are certified, belonging to the logged-in college.
        // Eager load signatories and ONLY 'confirmed' enrollments to count them efficiently.
        $courses = Course::where('college_id', $college->id)
            ->where('is_certified', true)
            ->with(['signatories', 'enrollments' => function ($query) {
                $query->where('status', 'confirmed');
            }])
            ->orderBy('end_date', 'desc')
            ->get();

        return view('college.certificate', compact('courses'));
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
}
