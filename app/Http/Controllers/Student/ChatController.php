<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Mentor;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $enrollment = Enrollment::query()
            ->where('user_id', '=', Auth::id(), 'and')
            ->where('course_id', '=', $courseId, 'and')
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
        $existingChat = Chat::query()
            ->where('student_id', '=', Auth::id(), 'and')
            ->where('course_id', '=', $courseId, 'and')
            ->first();

        if ($existingChat) {
            return redirect()->back()->with('info', 'A request or chat already exists for this course.');
        }

        // Create a new chat request with course context (mentor->user_id is user table id)
        Chat::create([
            'mentor_id' => $mentor->user_id,
            'student_id' => Auth::id(),
            'course_id' => $courseId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Mentor request sent successfully!');
    }

    public function show(?Chat $chat = null)
    {
        $activeTab = request('q', $chat ? 'live-chat' : 'requests');

        $requests = Chat::with([
                'mentor',
                'course' => fn($q) => $q->with('college')->withCount('enrollments')
            ])
            ->where('student_id', Auth::id())
            ->latest()
            ->get();

        $chats = Chat::with([
                'mentor',
                'course.college',
            ])
            ->where('student_id', Auth::id())
            ->where('status', 'accepted')
            ->latest()
            ->get();

        $courses = Course::query()
            ->whereHas('enrollments', fn($q) => $q->where('user_id', Auth::id()))
            ->get();

        if ($chat) {
            if ($chat->student_id !== Auth::id()) {
                abort(403);
            }

            if ($chat->status !== 'accepted') {
                return redirect()->route('student.chat.show')
                                ->with('error', 'This chat is not active yet.');
            }

            $chat->load(['course.college', 'messages.sender']);

            $chat->messages()
                ->where('sender_id', '!=', $chat->student_id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('student.chat', [
            'requests'     => $requests,
            'chats'        => $chats,
            'selectedChat' => $chat,
            'courses'      => $courses,
            'role'         => 'student',
            'activeTab'    => $activeTab,
        ]);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        if ($chat->student_id !== Auth::id()) {
            abort(403);
        }

        if ($chat->status !== 'accepted') {
            abort(403);
        }

        $request->validate([
            'message' => 'required|string|max:2000'
        ]);

        $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message'   => $request->message,
        ]);

        return back();
    }

    public function cancelRequest(Chat $chat)
    {
        if ($chat->student_id !== Auth::id()) {
            abort(403);
        }

        if ($chat->status !== 'pending') {
            return back()->with('error', 'This request cannot be cancelled.');
        }

        Chat::destroy($chat->id);

        return back()->with('success', 'Request cancelled.');
    }

}

