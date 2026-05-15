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

        $query = Chat::with(['student.user', 'course.college'])
            ->where('mentor_id', $mentorUserId)
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest();

        $requests = $query->paginate(10)->withQueryString();

        $courses = Course::where('mentor_id', $mentorUserId)->get(); // courses assigned to mentor

        return view('mentor.chat-request', compact('requests', 'courses'));
    }
}
