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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $courses = $this->filteredCoursesQuery($request, $college->id)->get();
        $mentors = Mentor::with(['user', 'college'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();
        $categories = Category::orderByDesc('id')->get();

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
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|integer|exists:course_sections,id',
            'sections.*.heading' => 'nullable|string|max:255',
            'sections.*.content' => 'nullable|string|max:5000',
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

            $course = Course::create([
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

            $this->syncSections($course, $validated['sections'] ?? []);
        });

        return redirect()->route('college.courses.index')->with('success', 'Course created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $courses = $this->filteredCoursesQuery($request, $college->id)->get();
        $mentors = Mentor::with(['user', 'college'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();
        $categories = Category::orderByDesc('id')->get();
        $editCourse = Course::with(['college.user', 'mentor.user', 'category'])->findOrFail($id);
        $viewOnly = true;

        return view('college.manage-course', compact('courses', 'college', 'mentors', 'categories', 'editCourse', 'viewOnly'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $college = College::with('user')->where('user_id', Auth::id())->firstOrFail();
        $courses = $this->filteredCoursesQuery($request, $college->id)->get();
        $mentors = Mentor::with(['user', 'college'])
            ->where('college_id', $college->id)
            ->latest()
            ->get();
        $categories = Category::orderByDesc('id')->get();
        $isDeleteMode = $request->query('mode') === 'delete';

        if (!$isDeleteMode) {
            $editCourse = Course::with(['college.user', 'mentor.user', 'category'])->findOrFail($id);
            return view('college.manage-course', compact('courses', 'college', 'mentors', 'categories', 'editCourse'));
        }

        $deleteCourse = Course::with(['college.user', 'mentor.user', 'category'])->findOrFail($id);
        return view('college.manage-course', compact('courses', 'college', 'mentors', 'categories', 'deleteCourse', 'isDeleteMode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);
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
            'sections' => 'nullable|array',
            'sections.*.id' => 'nullable|integer|exists:course_sections,id',
            'sections.*.heading' => 'nullable|string|max:255',
            'sections.*.content' => 'nullable|string|max:5000',
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

        $enrolledCount = $course->enrollments()
                        ->where('status', 'confirmed')
                        ->count();

        $newAvailableSeats = ($validated['total_seats'] ?? $course->total_seats) - $enrolledCount;

        DB::transaction(function () use ($newAvailableSeats, $request, $validated, $course, $isFirmOnly) {
            $updateData = [
                'category_id' => $validated['category_id'],
                'mentor_id' => $validated['mentor_id'] ?? null,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'course_type' => $validated['course_type'],
                'price' => $validated['price'] ?? 0,
                'is_certified' => $request->boolean('is_certified'),
                'total_seats' => $isFirmOnly ? null : ($validated['total_seats'] ?? null),
                'available_seats' => $isFirmOnly ? null : max(0, $newAvailableSeats),
                'start_date' => $isFirmOnly ? null : ($validated['start_date'] ?? null),
                'end_date' => $isFirmOnly ? null : ($validated['end_date'] ?? null),
                'time_slot' => $isFirmOnly ? null : ($validated['time_slot'] ?? null),
                'venue' => $isFirmOnly ? null : ($validated['venue'] ?? null),
                'status' => $validated['status'],
            ];

            if ($request->hasFile('course_image')) {
                if ($course->course_image && Storage::disk('public')->exists($course->course_image)) {
                    Storage::disk('public')->delete($course->course_image);
                }
                $updateData['course_image'] = $request->file('course_image')->store('courses/images', 'public');
            }

            $course->update($updateData);
            $this->syncSections($course, $validated['sections'] ?? []);
        });

        return redirect()->route('college.courses.index')->with('success', 'Course updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);

        DB::transaction(function () use ($course) {
            if ($course->course_image && Storage::disk('public')->exists($course->course_image)) {
                Storage::disk('public')->delete($course->course_image);
            }

            $course->delete();
        });

        return redirect()->route('college.courses.index')->with('success', 'Course deleted successfully!');
    }

    /**
     * Sync submitted course sections with the database.
     */
    private function syncSections(Course $course, array $sections): void
    {
        $existingSections = $course->sections()->get()->keyBy('id');
        $keptIds = [];

        foreach (array_values($sections) as $index => $sectionData) {
            $heading = trim((string) ($sectionData['heading'] ?? ''));
            $content = trim((string) ($sectionData['content'] ?? ''));
            $sectionId = $sectionData['id'] ?? null;

            if ($heading === '' && $content === '') {
                continue;
            }

            $payload = [
                'section_heading' => $heading,
                'section_content' => $content,
                'priority_order' => $index + 1,
            ];

            if ($sectionId && $existingSections->has((int) $sectionId)) {
                $existingSections->get((int) $sectionId)->update($payload);
                $keptIds[] = (int) $sectionId;
                continue;
            }

            $createdSection = $course->sections()->create($payload);
            $keptIds[] = $createdSection->id;
        }

        $course->sections()
            ->when(!empty($keptIds), fn ($query) => $query->whereNotIn('id', $keptIds), fn ($query) => $query)
            ->delete();
    }

    private function filteredCoursesQuery(Request $request, int $collegeId)
    {
        $query = Course::with(['college.user', 'mentor.user', 'category'])
            ->where('college_id', $collegeId);

        $search = trim((string) $request->input('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%")
                    ->orWhereHas('mentor.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $courseType = $request->input('course_type_filter');
        if (in_array($courseType, ['student_only', 'firm_only'], true)) {
            $query->where('course_type', $courseType);
        }

        $categoryId = $request->input('category_id_filter');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $status = $request->input('status_filter');
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->where('status', $status);
        }

        $sort = $request->input('sort', 'latest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price_desc') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'seats_desc') {
            $query->orderBy('total_seats', 'desc');
        } else {
            $query->latest();
        }

        return $query;
    }
}