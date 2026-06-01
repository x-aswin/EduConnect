<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Mentor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $college = Auth::user()->college()->firstOrFail();
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

        $results = collect();
        $summary = [];
        $applyDateRange = static function ($query) use ($start, $end) {
            return $query->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        };

        if ($type === 'enrollments') {
            $query = Enrollment::query()->with(['user', 'course.college', 'participants'])
                ->whereHas('course', function ($query) use ($college) {
                    $query->where('college_id', $college->id);
                });
            $query = $applyDateRange($query);

            if (in_array($subFilter, ['student', 'firm'], true)) {
                $query->where('type', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['pending'] = (clone $query)->where('status', 'pending')->count();
            $summary['confirmed'] = (clone $query)->where('status', 'confirmed')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        } elseif ($type === 'courses') {
            $query = Course::query()->with(['college', 'category', 'mentor.user'])
                ->where('college_id', $college->id);
            $query = $applyDateRange($query);

            if (in_array($subFilter, ['student_only', 'firm_only'], true)) {
                $query->where('course_type', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['active'] = (clone $query)->where('status', 'active')->count();
            $summary['inactive'] = (clone $query)->where('status', 'inactive')->count();
            $summary['by_category'] = (clone $query)
                ->get()
                ->groupBy(fn ($course) => $course->category?->name ?? 'Uncategorized')
                ->map(fn ($items) => $items->count())
                ->toArray();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        } elseif ($type === 'mentors') {
            $query = Mentor::query()->with(['user', 'college'])->withCount('courses')
                ->where('college_id', $college->id);
            $query = $applyDateRange($query);

            if (in_array($subFilter, ['pending', 'active', 'blocked'], true)) {
                $query->whereHas('user', function ($query) use ($subFilter) {
                    $query->where('status', $subFilter);
                });
            }

            $summary['total'] = $query->count();
            $summary['courses_total'] = (clone $query)->sum('courses_count');
            $summary['active_users'] = (clone $query)->whereHas('user', function ($query) {
                $query->where('status', 'active');
            })->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        } else {
            $summary['total'] = 0;
            $results = collect();
        }

        return view('college.report', compact('college', 'type', 'subFilter', 'start', 'end', 'results', 'summary'));
    }
}