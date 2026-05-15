<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function requests(Request $request)
    {
        $mentorUserId = Auth::id();
        $mentorProfile = Auth::user()->mentor;

        $query = Chat::with([
                'student',
                'studentProfile',
                'course' => fn($q) => $q->with('college')->withCount('enrollments')
            ])
            ->where('mentor_id', $mentorUserId)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest();

        $requests = $query->paginate(10)->withQueryString();

        $courses = Course::query()
            ->where('mentor_id', '=', $mentorProfile->id, 'and')
            ->withCount('enrollments')
            ->get(); // courses assigned to mentor

        return view('mentor.chat-request', compact('requests', 'courses'));
    }
    public function accept(Chat $chat)
    {
        // Make sure this chat belongs to this mentor
        if ($chat->mentor_id !== Auth::id()) {
            abort(403);
        }

        // Only pending chats can be accepted
        if ($chat->status !== 'pending') {
            return back()->with('error', 'This request can no longer be accepted.');
        }

        $chat->update(['status' => 'accepted']);

        return redirect()->route('mentor.chat.show', $chat)
                        ->with('success', 'Request accepted! You can now chat with the student.');
    }

    public function decline(Chat $chat)
    {
        if ($chat->mentor_id !== Auth::id()) {
            abort(403);
        }

        if ($chat->status !== 'pending') {
            return back()->with('error', 'This request cannot be declined.');
        }

        $chat->update(['status' => 'declined']);

        return back()->with('success', 'Request declined.');
    }

    public function show(?Chat $chat = null)
    {
        $mentorProfile = Auth::user()->mentor;

        $chats = Chat::with([
                'student',
                'studentProfile',
                'course.college',
            ])
            ->where('mentor_id', Auth::id())
            ->where('status', 'accepted')
            ->latest()
            ->get();

        $courses = Course::where('mentor_id', $mentorProfile->id)
            ->withCount('enrollments')
            ->get();

        // Only do this if a chat was selected
        if ($chat) {
            if ($chat->mentor_id !== Auth::id()) abort(403);

            if ($chat->status !== 'accepted') {
                return redirect()->route('mentor.chat.show')
                                ->with('error', 'This chat is not active yet.');
            }

            $chat->load(['course.college', 'messages.sender']);

            $chat->messages()
                ->where('sender_id', $chat->student_id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        return view('mentor.chat-show', [
            'chats'        => $chats,
            'selectedChat' => $chat,  // null when no chat selected
            'courses'      => $courses,
            'role'         => 'mentor',
        ]);
    }
    

    public function sendMessage(Request $request, Chat $chat)
    {
        if ($chat->mentor_id !== Auth::id()) {
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
}
