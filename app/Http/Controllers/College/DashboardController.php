<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('college.dashboard');
    }
}
