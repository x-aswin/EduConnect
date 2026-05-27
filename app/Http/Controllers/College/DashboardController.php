<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Mentor;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $college = $user->college;
        $college_name = $college->institution_name;
        $college_id = $college->id;

        $totalcourses = Course::where('college_id', '=', $college_id, 'and')->count();
        $totalCoursesCurrentMonth = Course::where('college_id', '=', $college_id, 'and')->whereMonth('created_at', '=', now()->month, 'and')->count();

        $totalMentors = Mentor::where('college_id', '=', $college_id, 'and')->count();
        $pendingMentors = Mentor::where('college_id', '=', $college_id, 'and')->where('status', '=', 'pending', 'and')->count();

        $totalEntrollments=Enrollment::whereHas('course', function($query) use ($college_id) {
            $query->where('college_id', '=', $college_id, 'and');
        })->count();
        $pendingEntrollments=Enrollment::whereHas('course', function($query) use ($college_id) {
            $query->where('college_id', '=', $college_id, 'and');
        })->where('status', '=', 'pending', 'and')->count();

        $revenue = Enrollment::whereHas('course', function($query) use ($college_id) {
            $query->where('college_id', '=', $college_id, 'and');
        })->where('status', '=', 'confirmed', 'and')->sum('total_amount');
        $revenueThisMonth = Enrollment::whereHas('course', function($query) use ($college_id) {
            $query->where('college_id', '=', $college_id, 'and');
        })->where('status', '=', 'confirmed', 'and')->whereMonth('created_at', '=', now()->month, 'and')->sum('total_amount');

        $recentCourses = Course::with(['mentor.user', 'enrollments'])
            ->withCount(['enrollments as confirmed_enrollments_count' => function ($query) {
                $query->where('status', '=', 'confirmed', 'and');
            }])
            ->where('college_id', '=', $college_id, 'and')
            ->latest()
            ->take(5)
            ->get();

        $pendingEnrollmentRequests = Enrollment::with(['course.mentor.user', 'user'])
            ->whereHas('course', function ($query) use ($college_id) {
                $query->where('college_id', '=', $college_id, 'and');
            })
            ->where('status', '=', 'pending', 'and')
            ->latest()
            ->take(5)
            ->get();

        $upcomingSessions = Course::with(['mentor.user', 'enrollments'])
            ->where('college_id', '=', $college_id, 'and')
            ->where('status', '=', 'active', 'and')
            ->whereNotNull('start_date')
            ->orderBy('start_date')
            ->take(3)
            ->get();

        $activeConversations = Chat::whereHas('course', function ($query) use ($college_id) {
            $query->where('college_id', '=', $college_id, 'and');
        })->where('status', '=', 'accepted', 'and')->count();

        return view('college.dashboard', compact(
            'totalcourses',
            'college_name',
            'totalCoursesCurrentMonth',
            'totalMentors',
            'pendingMentors',
            'totalEntrollments',
            'pendingEntrollments',
            'revenue',
            'revenueThisMonth',
            'recentCourses',
            'pendingEnrollmentRequests',
            'upcomingSessions',
            'activeConversations'
        ));
    }
}
