<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display the specified course by slug.
     */
    public function show(string $slug)
    {
        $course = Course::with(['college', 'category'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        return view('student.course-details', ['course' => $course]);
    }
}
