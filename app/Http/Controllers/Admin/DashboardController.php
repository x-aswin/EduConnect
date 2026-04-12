<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('admin.dashboard', compact(
            'totalStudents', 'totalColleges', 'totalFirms',
            'totalMentors', 'totalCourses', 
            'pendingColleges', 'pendingFirms'
        ));
    }
}
