<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\FirmParticipant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && $user->role === 'firm', 403);

        $type = $request->get('type', 'bookings');
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

        // Firm Overview Stats (all time / context)
        $overview = [
            'total_bookings' => Enrollment::where('user_id', $user->id)->where('type', 'firm')->count(),
            'total_completed' => Enrollment::where('user_id', $user->id)->where('type', 'firm')->where('certificate_issued', true)->count(),
            'total_spent' => Enrollment::where('user_id', $user->id)->where('type', 'firm')->where('payment_status', 'paid')->sum('total_amount'),
            'active_participants' => FirmParticipant::whereHas('enrollment', function ($q) use ($user) {
                $q->where('user_id', $user->id)->where('type', 'firm');
            })->count(),
        ];

        $results = collect();
        $summary = [];

        if ($type === 'bookings') {
            $query = Enrollment::query()
                ->with(['course.college.user', 'participants'])
                ->where('user_id', $user->id)
                ->where('type', 'firm');

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
                ->where('user_id', $user->id)
                ->where('type', 'firm');

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

        } elseif ($type === 'participants') {
            $query = FirmParticipant::query()
                ->with(['enrollment.course.college.user'])
                ->whereHas('enrollment', function ($q) use ($user) {
                    $q->where('user_id', $user->id)->where('type', 'firm');
                });

            $query = $applyDateRange($query);

            if ($subFilter !== 'all') {
                $query->whereHas('enrollment', function ($q) use ($subFilter) {
                    $q->where('status', $subFilter);
                });
            }

            $summary['total'] = $query->count();

            $results = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        }

        return view('firm.report', compact('user', 'type', 'subFilter', 'start', 'end', 'results', 'summary', 'overview'));
    }
}
