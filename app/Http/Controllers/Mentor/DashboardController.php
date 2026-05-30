<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mentorProfile = $user->mentor;

        $coursesQuery = Course::query()
            ->with(['college.user', 'category'])
            ->withCount('enrollments')
            ->where('mentor_id', $mentorProfile?->id);

        $totalCourses = (clone $coursesQuery)->count();
        $activeCourses = (clone $coursesQuery)->where('status', 'active')->count();
        $recentCourses = (clone $coursesQuery)
            ->latest()
            ->take(3)
            ->get();

        $pendingRequests = Chat::with(['student', 'studentProfile', 'course.college'])
            ->where('mentor_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->take(3)
            ->get();

        $pendingRequestsCount = Chat::where('mentor_id', '=', $user->id, 'and')
            ->where('status', '=', 'pending', 'and')
            ->count();

        $activeMenteesCount = Chat::where('mentor_id', '=', $user->id, 'and')
            ->where('status', '=', 'accepted', 'and')
            ->distinct()
            ->count('student_id');

        $unreadMessagesCount = Message::whereHas('chat', function ($query) use ($user) {
                $query->where('mentor_id', $user->id);
            })
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return view('mentor.dashboard', compact(
            'user',
            'mentorProfile',
            'totalCourses',
            'activeCourses',
            'pendingRequestsCount',
            'activeMenteesCount',
            'unreadMessagesCount',
            'recentCourses',
            'pendingRequests'
        ));
    }
}
