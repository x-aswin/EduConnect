<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Mentor;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function requestMentor(Request $request)
    {
        $mentorId = $request->input('mentor_id');
        $courseId = $request->input('course_id');
        
        $mentor = Mentor::findOrFail($mentorId);
        
        // Check if a chat already exists for this mentor and student and course
        $existingChat = Chat::where('mentor_id', $mentorId)
            ->where('student_id', auth()->id())
            ->where('course_id', $courseId)
            ->first();
        
        if ($existingChat) {
            return redirect()->back()->with('info', 'You have already requested this mentor.');
        }
        
        // Create a new chat request with course context
        Chat::create([
            'mentor_id' => $mentor->user_id,
            'student_id' => auth()->id(),
            'course_id' => $courseId,
            'status' => 'pending',
        ]);
        
        return redirect()->back()->with('success', 'Mentor request sent successfully!');
    }
}
