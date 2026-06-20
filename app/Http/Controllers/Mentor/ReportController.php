<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'mentor', 403);
        $mentorProfile = $user->mentor;

        $type = $request->get('type', 'chats');
        $subFilter = $request->get('sub_filter', 'all');

        try {
            $start = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : Carbon::create(2000, 1, 1, 0, 0, 0);
        } catch (\Exception $e) {
            $start = Carbon::create(2000, 1, 1, 0, 0, 0);
        }

        try {
            $end = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : Carbon::now()->endOfDay();
        } catch (\Exception $e) {
            $end = Carbon::now()->endOfDay();
        }

        $applyDateRange = static function ($query) use ($start, $end) {
            return $query->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        };

        // Mentor Overview Stats
        $overview = [
            'total_requests' => Chat::where('mentor_id', $user->id)->count(),
            'active_chats' => Chat::where('mentor_id', $user->id)->where('status', 'accepted')->count(),
            'total_messages' => Message::whereHas('chat', function ($query) use ($user) {
                $query->where('mentor_id', $user->id);
            })->count(),
            'assigned_courses' => $mentorProfile ? Course::where('mentor_id', $mentorProfile->id)->count() : 0,
        ];

        $results = collect();
        $summary = [];

        if ($type === 'chats') {
            $query = Chat::query()
                ->with(['student', 'studentProfile', 'course.college', 'messages'])
                ->withCount('messages')
                ->where('mentor_id', $user->id);

            $query = $applyDateRange($query);

            if ($subFilter !== 'all') {
                $query->where('status', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['pending'] = (clone $query)->where('status', 'pending')->count();
            $summary['accepted'] = (clone $query)->where('status', 'accepted')->count();
            $summary['declined'] = (clone $query)->where('status', 'declined')->count();
            $summary['total_messages'] = (clone $query)->get()->sum('messages_count');

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        } elseif ($type === 'courses') {
            $query = Course::query()
                ->with(['college.user', 'category'])
                ->withCount('enrollments')
                ->where('mentor_id', $mentorProfile?->id ?? 0);

            $query = $applyDateRange($query);

            if ($subFilter !== 'all') {
                $query->where('status', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['active'] = (clone $query)->where('status', 'active')->count();
            $summary['inactive'] = (clone $query)->where('status', 'inactive')->count();
            $summary['total_enrollments'] = (clone $query)->get()->sum('enrollments_count');

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        }

        return view('mentor.report', compact('user', 'type', 'subFilter', 'start', 'end', 'results', 'summary', 'overview'));
    }
}
