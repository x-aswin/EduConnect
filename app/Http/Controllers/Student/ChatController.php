<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Mentor;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function requestMentor(Request $request)
    {
        $mentorId = $request->input('mentor_id');
        $courseId = $request->input('course_id');

        $mentor = Mentor::findOrFail($mentorId);
        $course = Course::findOrFail($courseId);

        // Is this a student_only course?
        $isStudentOnly = ($course->course_type === 'student_only');

        // Is the student enrolled and is the enrollment confirmed?
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        if (! $enrollment) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to request a mentor.');
        }

        if ($enrollment->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Your enrollment is not confirmed yet.');
        }

        if (! $isStudentOnly) {
            return redirect()->back()->with('error', 'Mentor requests are only allowed for student only courses.');
        }

        // Does this course have a mentor assigned?
        if (empty($course->mentor_id)) {
            return redirect()->back()->with('error', 'No mentor is assigned to this course.');
        }

        // Does a chat request already exist? If exists, do not create again
        $existingChat = Chat::where('student_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();

        if ($existingChat) {
            return redirect()->back()->with('info', 'A request or chat already exists for this course.');
        }

        // Create a new chat request with course context (mentor->user_id is user table id)
        Chat::create([
            'mentor_id' => $mentor->user_id,
            'student_id' => auth()->id(),
            'course_id' => $courseId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Mentor request sent successfully!');
    }
}
