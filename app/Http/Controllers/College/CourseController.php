<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\College;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $courses = Course::with(['college.user', 'mentor.user', 'category'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();
        $mentors = Mentor::with(['user', 'college'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();
        $categories = Category::latest()->get();

        return view('college.manage-course', compact('courses', 'college', 'mentors', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}