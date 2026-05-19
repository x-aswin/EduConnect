<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\College;
use App\Models\User;
use App\Models\Firm;
use App\Models\Student;
use App\Models\Mentor;
use App\Models\Category;
use App\Models\FirmParticipant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'enrollments');
        $subFilter = $request->get('sub_filter', 'all');

        // parse dates, default to all-time window
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
            $query = Enrollment::query()->with(['user', 'course.college']);
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['student', 'firm'], true)) {
                $query->where('type', $subFilter);
            }

            $summary['total'] = $query->count();
            $summary['pending'] = (clone $query)->where('status', 'pending')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'colleges') {
            $query = College::query()->with('user');
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['pending', 'active', 'blocked'], true)) {
                $query->whereHas('user', function ($q) use ($subFilter) {
                    $q->where('status', $subFilter);
                });
            }
            $summary['total'] = $query->count();
            $collegeUserIds = (clone $query)->pluck('user_id');
            $summary['pending'] = DB::table('users')->whereIn('id', $collegeUserIds->all())->where('status', 'pending')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'users') {
            $query = User::query();
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['admin', 'college', 'mentor', 'student', 'firm'], true)) {
                $query->where('role', $subFilter);
            }
            $summary['total'] = $query->count();
            $summary['students'] = (clone $query)->where('role', 'student')->count();
            $summary['mentors'] = (clone $query)->where('role', 'mentor')->count();
            $summary['colleges'] = (clone $query)->where('role', 'college')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'courses') {
            $query = \App\Models\Course::query()->with(['college', 'category', 'mentor.user']);
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['student_only', 'firm_only'], true)) {
                $query->where('course_type', $subFilter);
            }
            $summary['total'] = $query->count();
            $summary['by_category'] = $applyDateRange(\App\Models\Course::query()->with('category'))
                ->when(in_array($subFilter, ['student_only', 'firm_only'], true), function ($q) use ($subFilter) {
                    $q->where('course_type', $subFilter);
                })
                ->get()
                ->groupBy(fn ($course) => $course->category?->name ?? 'Uncategorized')
                ->map(fn ($items) => $items->count())
                ->toArray();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'firms') {
            $query = Firm::query()->with('user');
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['pending', 'active', 'blocked'], true)) {
                $query->whereHas('user', function ($q) use ($subFilter) {
                    $q->where('status', $subFilter);
                });
            }
            $summary['total'] = $query->count();
            $summary['participants'] = $applyDateRange(FirmParticipant::query())->count();
            $firmUserIds = (clone $query)->pluck('user_id');
            $summary['active_users'] = DB::table('users')->whereIn('id', $firmUserIds->all())->where('status', 'active')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'students') {
            $query = Student::query()->with('user');
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['pending', 'active', 'blocked'], true)) {
                $query->whereHas('user', function ($q) use ($subFilter) {
                    $q->where('status', $subFilter);
                });
            }
            $summary['total'] = $query->count();
            $studentUserIds = (clone $query)->pluck('user_id');
            $summary['active_users'] = DB::table('users')->whereIn('id', $studentUserIds->all())->where('status', 'active')->count();
            // enrollments per user mapping
            $summary['enroll_counts'] = DB::table('enrollments')->selectRaw('user_id, COUNT(*) as cnt')
                ->where('created_at', '>=', $start)
                ->where('created_at', '<=', $end)
                ->groupBy('user_id')
                ->pluck('cnt', 'user_id')
                ->toArray();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'mentors') {
            $query = Mentor::query()->with(['user', 'college'])->withCount('courses');
            $query = $applyDateRange($query);
            if (in_array($subFilter, ['pending', 'active', 'blocked'], true)) {
                $query->whereHas('user', function ($q) use ($subFilter) {
                    $q->where('status', $subFilter);
                });
            }
            $summary['total'] = $query->count();
            $summary['courses_total'] = $applyDateRange(\App\Models\Course::query())->count();
            $mentorUserIds = (clone $query)->pluck('user_id');
            $summary['active_users'] = DB::table('users')->whereIn('id', $mentorUserIds->all())->where('status', 'active')->count();
            $results = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        } elseif ($type === 'categories') {
            $query = Category::query()->orderBy('name', 'asc');
            $summary['total'] = DB::table('categories')->count();
            $summary['courses_by_category'] = $applyDateRange(\App\Models\Course::query()->with('category'))
                ->get()
                ->groupBy('category_id')
                ->map(fn ($items) => $items->count())
                ->toArray();
            $results = $query->paginate(25)->withQueryString();

        } else {
            $summary['total'] = 0;
            $results = collect();
        }

        return view('admin.report', compact('type', 'subFilter', 'start', 'end', 'results', 'summary'));
    }
}

