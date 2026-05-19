<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index() {
        $totalStudents  = User::where('role', 'student')->count();
        $totalColleges  = User::where('role', 'college')->count();
        $totalFirms     = User::where('role', 'firm')->count();
        $totalMentors   = User::where('role', 'mentor')->count();
        $totalCourses   = Course::count();
        $pendingColleges = User::where('role', 'college')
                            ->where('status', 'pending')->count();
        $pendingFirms   = User::where('role', 'firm')
                            ->where('status', 'pending')->count();
        $totalCategories = Category::count();
        $totalEnrollments = Enrollment::count();
        $pendingEnrollments = Enrollment::where('status', 'pending')->count();
        $totalUsers = User::count();

        $pendingList = User::whereIn('role', ['college', 'firm'])->where('status', 'pending')->orderBy('created_at', 'desc')->take(5)->get();

        $recentEnrollments = Enrollment::with(['user', 'course.college'])
                                        ->orderBy('created_at', 'desc')
                                        ->take(5)
                                        ->get();

        // Get enrollment trends by month
        $enrollmentTrends = Enrollment::with('user')
            ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("strftime('%m', created_at)"))
            ->orderBy(DB::raw("strftime('%m', created_at)"))
            ->get();

        // Get student vs firm enrollments by month
        $studentEnrollments = Enrollment::whereHas('user', function($q) { $q->where('role', 'student'); })
            ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("strftime('%m', created_at)"))
            ->orderBy(DB::raw("strftime('%m', created_at)"))
            ->pluck('count', 'month')
            ->toArray();

        $firmEnrollments = Enrollment::whereHas('user', function($q) { $q->where('role', 'firm'); })
            ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as count")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy(DB::raw("strftime('%m', created_at)"))
            ->orderBy(DB::raw("strftime('%m', created_at)"))
            ->pluck('count', 'month')
            ->toArray();

        // Format data for Chart.js (fill in missing months with 0)
        $studentData = [];
        $firmData = [];
        for ($i = 6; $i >= 1; $i--) {
            $month = str_pad(now()->subMonths($i - 1)->month, 2, '0', STR_PAD_LEFT);
            $studentData[] = $studentEnrollments[$month] ?? 0;
            $firmData[] = $firmEnrollments[$month] ?? 0;
        }

        return view('admin.dashboard', compact(
            'totalStudents', 'totalColleges', 'totalFirms',
            'totalMentors', 'totalCourses', 
            'pendingColleges', 'pendingFirms', 'totalCategories', 'totalEnrollments', 'totalUsers', 'pendingEnrollments', 'pendingList', 'recentEnrollments',
            'studentData', 'firmData'
        ));
    }
}
