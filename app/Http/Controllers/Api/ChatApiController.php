<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatApiController extends Controller
{
    public function show(Chat $chat)
    {
        $role = $this->determineRole($chat);
        $this->authorizeAccess($chat, $role);
        $this->markMessagesAsRead($chat);

        $otherUser = $this->getOtherUser($chat, $role);
        $otherPhoto = $this->getOtherPhoto($otherUser, $role);

        $messages = $chat->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'sender_id'  => $m->sender_id,
                'message'    => $m->message,
                'time'       => $m->created_at->format('h:i A'),
                'is_read'    => (bool) $m->is_read,
            ]);

        return response()->json([
            'id'           => $chat->id,
            'other_name'   => $otherUser->name ?? 'Unknown',
            'other_photo'  => $otherPhoto,
            'course_title' => $chat->course->title ?? '',
            'college_name' => $chat->course->college?->institution_name ?? null,
            'messages'     => $messages,
        ]);
    }

    public function sendMessage(Request $request, Chat $chat)
    {
        $role = $this->determineRole($chat);
        $this->authorizeAccess($chat, $role);

        abort_if($chat->status !== 'accepted', 403);

        $request->validate(['message' => 'required|string|max:2000']);

        $message = $chat->messages()->create([
            'sender_id' => Auth::id(),
            'message'   => $request->message,
        ]);

        return response()->json([
            'id'      => $message->id,
            'message' => $message->message,
            'time'    => $message->created_at->format('h:i A'),
        ]);
    }

    public function getMessages(Chat $chat)
    {
        $role = $this->determineRole($chat);
        $this->authorizeAccess($chat, $role);

        $messages = $chat->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'id'        => $m->id,
                'sender_id' => $m->sender_id,
                'message'   => $m->message,
                'time'      => $m->created_at->format('h:i A'),
                'is_read'   => (bool) $m->is_read,
            ]);

        return response()->json($messages);
    }

    // --- Helper methods ---

    private function determineRole(Chat $chat): string
    {
        if (Auth::id() === $chat->student_id) return 'student';
        if (Auth::id() === $chat->mentor_id) return 'mentor';
        abort(403);
    }


    private function authorizeAccess(Chat $chat, string $role): void
    {
        if ($role === 'student' && $chat->student_id !== Auth::id()) abort(403);
        if ($role === 'mentor' && $chat->mentor_id !== Auth::id()) abort(403);
    }

    private function markMessagesAsRead(Chat $chat): void
    {
        $chat->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    private function getOtherUser(Chat $chat, string $role)
    {
        return $role === 'student' ? $chat->mentor : $chat->student;
    }

    private function getOtherPhoto($otherUser, string $role): ?string
    {
        if ($role === 'student') {
            $path = $otherUser->mentor->photo ?? null;
        } else {
            $path = $otherUser->student->photo ?? null;
        }
        return $path ? asset('storage/' . ltrim($path, '/')) : null;
    }
}