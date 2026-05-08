<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
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
}
