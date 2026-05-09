<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CourseController extends Controller
{
     public function index(Request $request)
    {
        $query = Course::with(['college.user', 'category'])
            ->where('course_type', 'firm_only')
            ->where('status', 'active');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%')
                    ->orWhere('venue', 'like', '%' . $keyword . '%')
                    ->orWhereHas('college', function ($collegeQuery) use ($keyword) {
                        $collegeQuery->where('institution_name', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($userQuery) use ($keyword) {
                                $userQuery->where('name', 'like', '%' . $keyword . '%');
                            });
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($keyword) {
                        $categoryQuery->where('name', 'like', '%' . $keyword . '%');
                    });
            });
        }

        if ($request->filled('category')) {
            $categoryName = trim((string) $request->category);
            $query->whereHas('category', function ($categoryQuery) use ($categoryName) {
                $categoryQuery->where('name', $categoryName);
            });
        }

        $sort = $request->get('sort', 'newest');
        if ($sort === 'price_asc') {
            $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
        } elseif ($sort === 'start_soon') {
            $query->orderBy('start_date', 'asc')->orderBy('created_at', 'desc');
        } else {
            $query->latest();
            $sort = 'newest';
        }

        $courses = $query
            ->paginate(9)
            ->withQueryString()
            ->through(function (Course $course) {
                $gradients = [
                    'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                    'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                    'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                    'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                ];

                $icons = [
                    'bi-shield-shaded', 'bi-graph-up-arrow', 'bi-palette2',
                    'bi-bar-chart-line', 'bi-code-slash', 'bi-camera-video',
                ];

                $colors = ['text-success', 'text-danger', 'text-warning', 'text-primary', 'text-info'];

                $visuals = [
                    'image_bg' => $gradients[array_rand($gradients)],
                    'icon' => $icons[array_rand($icons)],
                    'icon_color' => $colors[array_rand($colors)],
                ];

                $imagePath = $course->course_image ?? $course->college?->photo ?? null;
                $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : null;

                return [
                    'id' => $course->id,
                    'slug' => $course->slug,
                    'title' => $course->title,
                    'image' => $imageUrl,
                    'category' => $course->category?->name ?? null,
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

        $categories = Category::query()
            ->orderBy('name')
            ->pluck('name')
            ->values();

        return view('firm.browse-course', compact('courses', 'categories', 'sort'));
    }
       public function show(string $slug)
    {
        $course = Course::with(['college', 'category'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();
        $isEnrolled = false;
        return view('firm.course-details', ['course' => $course, 'isEnrolled' => $isEnrolled]);
    }
    public function book(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'firm') {
            return redirect()->route('login');
        }

        // Only allow booking for firm_only courses
        if ($course->course_type !== 'firm_only') {
            return back()->with('error', 'This course is not available for firm booking.');
        }

        // Validate the form input
        $validated = $request->validate([
            'requested_venue'    => 'required|string|max:255',
            'proposed_schedule'  => 'required|date',
            'participant_count'  => 'required|integer|min:1',
            'college_note'       => 'nullable|string|max:500',
        ]);

        // Create a new enrollment record
        $enrollment = Enrollment::create([
            'course_id'         => $course->id,
            'user_id'           => $user->id,
            'type'              => 'firm',
            'status'            => 'pending',
            'requested_venue'   => $validated['requested_venue'],
            'proposed_schedule' => $validated['proposed_schedule'],
            'participant_count' => $validated['participant_count'],
            'total_amount'      => $course->price * $validated['participant_count'],
            'payment_status'    => 'na', // or 'pending' depending on your logic
            'college_note'      => $validated['college_note'] ?? null,
        ]);

        return redirect()->route('firm.bookings')
            ->with('success', 'Booking request submitted successfully.');
    }
    function bookings()
    {
        $user = Auth::user();
        $bookings = Enrollment::with('course')
            ->where('user_id', $user->id)
            ->where('type', 'firm')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('firm.bookings', compact('bookings'));
    }
}
