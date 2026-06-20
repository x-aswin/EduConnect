<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'student', 403);

        $type = $request->get('type', 'enrollments');
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

        // Student Overview Stats (overall, not bounded by date filter, for high-level context)
        $overview = [
            'total_enrolled' => Enrollment::where('user_id', $user->id)->count(),
            'total_completed' => Enrollment::where('user_id', $user->id)->where('certificate_issued', true)->count(),
            'total_spent' => Enrollment::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total_amount'),
            'active_chats' => Chat::where('student_id', $user->id)->where('status', 'accepted')->count(),
        ];

        $results = collect();
        $summary = [];

        if ($type === 'enrollments') {
            $query = Enrollment::query()
                ->with(['course.college.user', 'course.mentor.user'])
                ->where('user_id', $user->id);

            $query = $applyDateRange($query);

            if ($subFilter !== 'all') {
                $query->where('status', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['pending'] = (clone $query)->where('status', 'pending')->count();
            $summary['confirmed'] = (clone $query)->where('status', 'confirmed')->count();

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        } elseif ($type === 'payments') {
            $query = Enrollment::query()
                ->with(['course'])
                ->where('user_id', $user->id);

            $query = $applyDateRange($query);

            if ($subFilter === 'paid') {
                $query->where('payment_status', 'paid');
            } elseif ($subFilter === 'unpaid') {
                $query->where('payment_status', '!=', 'paid');
            }

            $summary['total'] = $query->count();
            $summary['total_amount'] = (clone $query)->where('payment_status', 'paid')->sum('total_amount');
            $summary['pending_amount'] = (clone $query)->where('payment_status', '!=', 'paid')->sum('total_amount');

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        } elseif ($type === 'mentorships') {
            $query = Chat::query()
                ->with(['mentor', 'course.college', 'messages'])
                ->where('student_id', $user->id);

            $query = $applyDateRange($query);

            if ($subFilter !== 'all') {
                $query->where('status', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['accepted'] = (clone $query)->where('status', 'accepted')->count();
            $summary['pending'] = (clone $query)->where('status', 'pending')->count();

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        } elseif ($type === 'certificates') {
            $query = Enrollment::query()
                ->with(['course.college.user'])
                ->where('user_id', $user->id)
                ->where('certificate_issued', true);

            $query = $applyDateRange($query);

            $summary['total'] = $query->count();

            $results = $query->orderBy('certificate_issued_at', 'desc')->paginate(15)->withQueryString();
        }

        return view('student.report', compact('user', 'type', 'subFilter', 'start', 'end', 'results', 'summary', 'overview'));
    }
}
