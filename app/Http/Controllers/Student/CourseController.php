<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CourseController extends Controller
{
    /**
     * Display a listing of student-available courses.
     */
    public function index()
    {
        $courses = Course::with(['college', 'category'])
            ->where('course_type', 'student_only')
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(function (Course $course) {
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

        return view('student.browse-course', compact('courses'));
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
}
