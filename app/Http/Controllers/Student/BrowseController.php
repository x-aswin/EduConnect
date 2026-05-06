<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BrowseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::with(['college', 'category'])
            ->where('course_type', 'student_only')
            ->where('status', 'active')
            ->latest()
            ->get()
            ->map(function (Course $course) {
                $categoryName = strtolower($course->category?->name ?? $course->title ?? '');

                $visuals = match (true) {
                    str_contains($categoryName, 'security') => [
                        'image_bg' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                        'icon' => 'bi-shield-shaded',
                        'icon_color' => 'text-success',
                    ],
                    str_contains($categoryName, 'data') => [
                        'image_bg' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                        'icon' => 'bi-graph-up-arrow',
                        'icon_color' => 'text-danger',
                    ],
                    str_contains($categoryName, 'design') => [
                        'image_bg' => 'linear-gradient(135deg, #fef3c7, #fde68a)',
                        'icon' => 'bi-palette2',
                        'icon_color' => 'text-warning',
                    ],
                    str_contains($categoryName, 'business') => [
                        'image_bg' => 'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                        'icon' => 'bi-bar-chart-line',
                        'icon_color' => 'text-primary',
                    ],
                    default => [
                        'image_bg' => 'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                        'icon' => 'bi-code-slash',
                        'icon_color' => 'text-primary',
                    ],
                };

                return [
                    'title' => $course->title,
                    'college' => $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College',
                    'venue' => $course->venue ?? 'TBA',
                    'start_date' => $course->start_date ? Carbon::parse($course->start_date)->format('M d, Y') : 'TBA',
                    'price' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seats_total' => (int) ($course->total_seats ?? 0),
                    'seats_available' => (int) ($course->available_seats ?? 0),
                    'type' => $course->course_type,
                    ...$visuals,
                ];
            });

        return view('student.browse-course', compact('courses'));
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
