<?php

namespace App\Http\Controllers\College;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\College;
use App\Models\Course;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'mentor_id' => 'nullable|exists:mentors,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'course_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'course_type' => 'required|in:student_only,firm_only',
            'price' => 'nullable|numeric|min:0',
            'is_certified' => 'nullable|boolean',
            'total_seats' => 'required_if:course_type,student_only|nullable|integer|min:1',
            'start_date' => 'required_if:course_type,student_only|nullable|date',
            'end_date' => 'required_if:course_type,student_only|nullable|date|after_or_equal:start_date',
            'time_slot' => 'required_if:course_type,student_only|nullable|string|max:255',
            'venue' => 'required_if:course_type,student_only|nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ];

        if ($request->filled('mentor_id')) {
            $rules['mentor_id'] = [
                'nullable',
                Rule::exists('mentors', 'id')->where(function ($query) use ($college) {
                    $query->where('college_id', $college->id);
                }),
            ];
        }

        $validated = $request->validate($rules);
        $isFirmOnly = $validated['course_type'] === 'firm_only';

        DB::transaction(function () use ($validated, $request, $isFirmOnly, $college) {
            $imagePath = null;
            if ($request->hasFile('course_image')) {
                $imagePath = $request->file('course_image')->store('courses/images', 'public');
            }

            Course::create([
                'college_id' => $college->id,
                'category_id' => $validated['category_id'],
                'mentor_id' => $validated['mentor_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'course_image' => $imagePath,
                'course_type' => $validated['course_type'],
                'price' => $validated['price'] ?? 0,
                'is_certified' => $request->boolean('is_certified'),
                'total_seats' => $isFirmOnly ? null : ($validated['total_seats'] ?? null),
                'available_seats' => $isFirmOnly ? null : ($validated['total_seats'] ?? null),
                'start_date' => $isFirmOnly ? null : ($validated['start_date'] ?? null),
                'end_date' => $isFirmOnly ? null : ($validated['end_date'] ?? null),
                'time_slot' => $isFirmOnly ? null : ($validated['time_slot'] ?? null),
                'venue' => $isFirmOnly ? null : ($validated['venue'] ?? null),
                'status' => $validated['status'],
            ]);
        });

        return redirect()->route('college.courses.index')->with('success', 'Course created successfully!');
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