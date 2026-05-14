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
        
        $mentor = Mentor::findOrFail($mentorId);
        
        // Check if a chat already exists
        $existingChat = Chat::where('mentor_id', $mentorId)
            ->where('student_id', auth()->id())
            ->first();
        
        if ($existingChat) {
            return redirect()->back()->with('info', 'You have already requested this mentor.');
        }
        
        // Create a new chat request
        Chat::create([
            'mentor_id' => $mentorId,
            'student_id' => auth()->id(),
            'status' => 'pending',
        ]);
        
        return redirect()->back()->with('success', 'Mentor request sent successfully!');
    }
}
