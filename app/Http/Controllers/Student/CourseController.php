<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CourseController extends Controller
{
    /**
     * Display a listing of student-available courses.
     */
    public function index(Request $request)
    {
        $query = Course::with(['college.user', 'category'])
            ->where('course_type', 'student_only')
            ->where('status', 'active');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('venue', 'like', '%' . $keyword . '%')
                    ->orWhereHas('college', function ($collegeQuery) use ($keyword) {
                        $collegeQuery->where('institution_name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($userQuery) use ($keyword) {
                                $userQuery->where('name', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $categoryName = trim((string) $request->category);
            $query->whereHas('category', function ($categoryQuery) use ($categoryName) {
                $categoryQuery->where('name', $categoryName);
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
        } elseif ($sort === 'start_soon') {
            $query->orderBy('start_date', 'asc')->orderBy('created_at', 'desc');
        } else {
            $query->latest();
            $sort = 'newest';
        }

        $courses = $query
            ->paginate(9)
            ->withQueryString()
            ->through(function (Course $course) {
                $gradients = [
                    'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                    'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                    'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                    'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                ];

                $icons = [
                    'bi-shield-shaded', 'bi-graph-up-arrow', 'bi-palette2',
                    'bi-bar-chart-line', 'bi-code-slash', 'bi-camera-video',
                ];

                $colors = ['text-success', 'text-danger', 'text-warning', 'text-primary', 'text-info'];

                $visuals = [
                    'image_bg' => $gradients[array_rand($gradients)],
                    'icon' => $icons[array_rand($icons)],
                    'icon_color' => $colors[array_rand($colors)],
                ];

                $imagePath = $course->course_image ?? $course->college?->photo ?? null;
                $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : null;

                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'image' => $imageUrl,
                    'category' => $course->category?->name ?? null,
                    'college' => $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College',
                    'venue' => $course->venue ?? 'TBA',
                    'start_date' => $course->start_date ? Carbon::parse($course->start_date)->format('M d, Y') : 'TBA',
                    'price' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seats_total' => (int) ($course->total_seats ?? 0),
                    'seats_available' => (int) ($course->available_seats ?? 0),
                    'type' => $course->course_type,
                    ...$visuals,
                ];
            });

        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return view('student.browse-course', compact('courses', 'categories', 'sort'));
    }

    /**
     * Display the specified course by slug.
     */
    public function show(string $slug)
    {
        $course = Course::with(['college', 'category'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        // Check if authenticated user already enrolled
        $isEnrolled = false;
        if (Auth::check()) {
            $isEnrolled = Enrollment::query()
                ->where([
                    ['user_id', Auth::id()],
                    ['course_id', $course->id],
                ])
                ->exists();
        }

        return view('student.course-details', ['course' => $course, 'isEnrolled' => $isEnrolled]);
    }

    /**
     * Store an enrollment request for the current user.
     */
    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student') {
            return back()->with('error', 'Only students can enroll from this page.');
        }

        if ($course->course_type !== 'student_only') {
            return back()->with('error', 'This course is not available for your role.');
        }

        $already = Enrollment::query()
            ->where([
                ['user_id', $user->id],
                ['course_id', $course->id],
            ])
            ->exists();
        if ($already) {
            return back()->with('info', 'You have already requested enrollment for this course.');
        }

        $totalAmount = (float) ($course->price ?? 0);

        Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'type' => 'student',
            'status' => 'pending',
            'payment_status' => $totalAmount > 0 ? 'pending' : 'na',
            'participant_count' => 1,
            'total_amount' => $totalAmount,
        ]);

        return redirect()->route('student.course.show', $course->slug)
            ->with('success', 'Enrollment request submitted.');
    }

    /**
     * Show all enrollments for the current student.
     */
    public function myEnrollments()
    {
        $enrollments = Enrollment::with(['course.college', 'course.mentor.user'])
            ->where('user_id', Auth::id())
            ->where('type', 'student')
            ->latest('updated_at')
            ->get();

        return view('student.my-enrollments', ['enrollments' => $enrollments]);
    }

    /**
     * Remove a pending enrollment request for the current student.
     */
    public function destroy(Enrollment $enrollment)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role !== 'student' || $enrollment->user_id !== $user->id || $enrollment->type !== 'student') {
            abort(403);
        }

        if ($enrollment->status !== 'pending') {
            return back()->with('error', 'The enrollment was already approved or rejected. Please contact the college if you wish to cancel it.');
        }

        $deleted = Enrollment::query()
            ->where([
                ['id', $enrollment->id],
                ['user_id', $user->id],
                ['type', 'student'],
                ['status', 'pending'],
            ])
            ->delete();

        if (!$deleted) {
            return back()->with('error', 'Unable to remove this enrollment request.');
        }

        return back()->with('success', 'Enrollment request removed.');
    }

    /**
     * Show the payment page for a student's confirmed enrollment with pending payment.
     */
    public function payment(Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($enrollment->user_id !== $user->id || $enrollment->type !== 'student') {
            abort(403);
        }

        if ($enrollment->status !== 'confirmed') {
            return back()->with('error', 'Enrollment is not approved yet.');
        }

        if ($enrollment->payment_status !== 'pending') {
            return back()->with('info', 'Payment is already completed or not required.');
        }

        return view('student.payment', ['enrollment' => $enrollment]);
    }

    /**
     * Process a simulated payment and mark enrollment as paid.
     */
    public function processPayment(Request $request, Enrollment $enrollment)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($enrollment->user_id !== $user->id || $enrollment->type !== 'student') {
            abort(403);
        }

        if ($enrollment->status !== 'confirmed' || $enrollment->payment_status !== 'pending') {
            return back()->with('error', 'Payment cannot be processed for this enrollment.');
        }

        // In a real integration you'd verify the payment gateway response here.
        $enrollment->payment_status = 'paid';
        $enrollment->save();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Payment recorded — access granted.',
                'redirect' => route('student.my.enrollments'),
            ]);
        }

        return redirect()->route('student.my.enrollments')->with('success', 'Payment recorded — access granted.');
    }

    public function download(Enrollment $enrollment)
{
    // 1. Authorization Guard
    abort_unless($enrollment->user_id === auth()->id(), 403, 'Unauthorized access.');
    
    // 2. Issuance Security Guard
    abort_unless($enrollment->certificate_issued, 404, 'Certificate not yet available.');

    // 3. Lazy code generation: build the code if it doesn't exist yet
    if (!$enrollment->certificate_code) {
        $enrollment->update([
            'certificate_code' => 'EDU-' . now()->year . '-' . strtoupper(Str::random(8))
        ]);
    }

    // 4. Eager load relationships needed for the template layout map
    $enrollment->load(['course.college.user', 'course.signatories', 'user']);

    // 5. Generate Base64 QR Code safely for DomPDF inclusion via SVG Base64 encoding
        $verificationUrl = url('/verify?code=' . $enrollment->certificate_code);

        // Generate as raw SVG string (this requires NO server-side image extensions like Imagick or GD)
        $qrCodeSvgRaw = QrCode::format('svg')
            ->size(120) // High-fidelity scaling for the container vector canvas
            ->margin(0)
            ->errorCorrection('M')
            ->generate($verificationUrl);
            
        // Encode the raw XML/SVG string into a safe inline Data URI string
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvgRaw);

        
        
    // 6. Map model entities into variables expected by your template layout
    $data = [
        'student'          => $enrollment->user,
        'course'           => $enrollment->course,
        'signatories'      => $enrollment->course->signatories->toArray(),
        'verificationCode' => $enrollment->certificate_code,
        'qrCode'           => $qrCodeBase64,
        'verificationUrl' => $verificationUrl,
    ];

    // 7. Render using your precise template layout name
    $pdf = Pdf::loadView('templates.certificate_pdf', $data)
        ->setPaper('a4', 'landscape')
        ->setWarnings(false); // Silences non-fatal asset parsing notifications

    return $pdf->stream('Certificate-' . $enrollment->certificate_code . '.pdf');
}
}
