<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
{
    $recommendedStudentCourses = Course::with(['college.user', 'category', 'mentor.user'])
        ->where('course_type', 'student_only')
        ->where('status', 'active')
        ->latest()
        ->take(3)
        ->get()
        ->values()
        ->map(function (Course $course, int $index) {
            $totalSeats = max(0, (int) ($course->total_seats ?? 0));
            $availableSeats = max(0, (int) ($course->available_seats ?? 0));
            $filledSeats = $totalSeats > 0 ? max(0, $totalSeats - $availableSeats) : 0;
            $progress = $totalSeats > 0 ? (int) round(($filledSeats / $totalSeats) * 100) : 0;
            $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;
            
            // Material Symbols & Color Schemes matching Primary theme
            $visuals = [
                ['gradient' => 'linear-gradient(135deg, #e0e7ff, #c7d2fe)', 'icon' => 'code', 'icon_color' => 'text-primary'],
                ['gradient' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)', 'icon' => 'verified_user', 'icon_color' => 'text-success'],
                ['gradient' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)', 'icon' => 'trending_up', 'icon_color' => 'text-danger'],
            ];
            $visual = $visuals[$index % count($visuals)];
            $imagePath = $course->course_image ?? $course->college?->photo ?? null;
            $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : asset('images/default-course.png');
            return [
                'type' => 'student',
                'title' => $course->title,
                'image_url' => $imageUrl,
                'has_real_image' => !empty($imagePath), // Boolean flag to help conditional rendering in Blade
                'college_name' => $course->college->user->name ?? 'Partner Institution',
                'details_url' => route('course.show', $course->slug),
                'badge_label' => 'Student Only',
                'category_label' => $course->category?->name,
                'venue' => $course->venue ?? 'Venue TBA',
                'start_date' => $startDate?->format('M d') ?? 'TBA',
                'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                'seats_label' => $totalSeats > 0 ? $filledSeats . '/' . $totalSeats . ' spaces filled' : 'Seats TBA',
                'progress' => $progress,
                'gradient' => $visual['gradient'],
                'icon' => $visual['icon'],
                'icon_color' => $visual['icon_color'],
            ];
        });

    $recommendedFirmCourses = Course::with(['college.user', 'category'])
        ->where('course_type', 'firm_only')
        ->where('status', 'active')
        ->latest()
        ->take(3)
        ->get()
        ->values()
        ->map(function (Course $course, int $index) {
            // Material Symbols & Color Schemes matching Tertiary theme
            $visuals = [
                ['gradient' => 'linear-gradient(135deg, #e0e7ff, #a5b4fc)', 'icon' => 'shield_with_heart', 'icon_color' => 'text-primary'],
                ['gradient' => 'linear-gradient(135deg, #fef3c7, #fde68a)', 'icon' => 'leaderboard', 'icon_color' => 'text-warning'],
                ['gradient' => 'linear-gradient(135deg, #d1fae5, #6ee7b7)', 'icon' => 'video_cam', 'icon_color' => 'text-success'],
            ];
            $visual = $visuals[$index % count($visuals)];
            $imagePath = $course->course_image ?? $course->college?->photo ?? null;
            $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : asset('images/default-course.png');
            return [
                'type' => 'firm',
                'title' => $course->title,
                'image_url' => $imageUrl,
                'has_real_image' => !empty($imagePath), // Boolean flag to help conditional rendering in Blade
                'college_name' => $course->college->user->name ?? 'Partner Institution',
                'details_url' => route('course.show', $course->slug),
                'book_url' => route('course.show', $course->slug),
                'badge_label' => 'Firm Only',
                'start_date' => 'Decided by the Firm',
                'category_label' => $course->category?->name,
                'venue' => $course->venue ?? 'Chosen by the Firm',
                'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                'seat_label' => $course->available_seats > 0
                    ? $course->available_seats . ' seats left'
                    : ($course->total_seats > 0 ? $course->total_seats . ' seats' : 'Flexible group size by Firm'),
                'gradient' => $visual['gradient'],
                'icon' => $visual['icon'],
                'icon_color' => $visual['icon_color'],
            ];
        });

    // Merge both types, optionally shuffle them or leave side by side, and take up to 6 total items
    $recommendedCourses = $recommendedStudentCourses->concat($recommendedFirmCourses);

    return view('guest.landing', compact('recommendedCourses'));
}

public function explore(Request $request){
    // 1. Initialize Base Active Query Filter
    $query = Course::with(['college.user', 'category'])
        ->where('status', 'active');

    // 2. Handle Segment/Track Switching Logic (Student Only vs Firm Only vs All)
    if ($request->filled('track') && in_array($request->track, ['student', 'firm'])) {
        $query->where('course_type', $request->track . '_only');
    } else {
        // Fallback or explicit 'all' / mixed state: only include public structural formats
        $query->whereIn('course_type', ['student_only', 'firm_only']);
    }

    // 3. Search Keyphrase Constraint Logic
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

    // 4. Category Structural Filter
    if ($request->filled('category')) {
        $categoryName = trim((string) $request->category);
        $query->whereHas('category', function ($categoryQuery) use ($categoryName) {
            $categoryQuery->where('name', $categoryName);
        });
    }

    // 5. Global Sort Criteria Processing
    $sort = $request->get('sort', 'newest');
    if ($sort === 'price_asc') {
        $query->orderBy('price', 'asc')->orderBy('created_at', 'desc');
    } elseif ($sort === 'start_soon') {
        $query->orderBy('start_date', 'asc')->orderBy('created_at', 'desc');
    } else {
        $query->latest();
        $sort = 'newest';
    }

    // 6. Paginate Data and Standardize Keys for the Combined Bootstrap Grid
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

            // Specific default overrides based on user context track
            $isStudent = ($course->course_type === 'student_only');
            
            $iconColor = $isStudent ? 'text-primary' : 'text-success';
            $icon = $isStudent ? 'bi-code-slash' : 'bi-building';
            $gradient = $isStudent ? 'linear-gradient(135deg, #e0e7ff, #c7d2fe)' : 'linear-gradient(135deg, #e0e7ff, #a5b4fc)';

            // Safe data variables for standard fields
            $totalSeats = max(0, (int) ($course->total_seats ?? 0));
            $availableSeats = max(0, (int) ($course->available_seats ?? 0));
            $filledSeats = $totalSeats > 0 ? max(0, $totalSeats - $availableSeats) : 0;
            $progress = $totalSeats > 0 ? (int) round(($filledSeats / $totalSeats) * 100) : 0;
            
            if(!$isStudent)
                $time_firm="Date & Time Slot set by Firm";
            else
                $time_firm=null;

            return [
                'id' => $course->id,
                'slug' => $course->slug,
                'title' => $course->title,
                'type' => $isStudent ? 'student' : 'firm',
                'time_firm' => $time_firm,
                'badge_label' => $isStudent ? 'Student Only' : 'Firm Only',
                'category_label' => $course->category?->name ?? null,
                'college_name' => $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College',
                'venue' => $course->venue ?? 'Venue TBA',
                'start_date' => $course->start_date ? Carbon::parse($course->start_date)->format('M d, Y') : 'TBA',
                'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                
                // Student Context metrics
                'seats_label' => $totalSeats > 0 ? $filledSeats . '/' . $totalSeats . ' spaces filled' : 'Seats TBA',
                'progress' => $progress,
                
                // Firm Context metrics
                'seat_label' => $availableSeats > 0 
                    ? $availableSeats . ' seats left' 
                    : ($totalSeats > 0 ? $totalSeats . ' seats' : 'Flexible group size'),
                
                // URLs Map Routing
                'details_url' => route('course.show', $course->slug), // Public tracking view info
                'book_url' => route('course.show', $course->slug),       // Firm module interaction booking URL
                
                // UI Cosmetics Setup variables
                'gradient' => $gradient,
                'icon' => $icon,
                'icon_color' => $iconColor,
            ];
        });

    // 7. Pull Alphabetized Filtering Keynames
    $categories = Category::query()
        ->orderBy('name', 'asc')
        ->pluck('name')
        ->values();

    return view('guest.explore', compact('courses', 'categories', 'sort'));

}


//  public function bootstrap()
// {
//     $recommendedStudentCourses = Course::with(['college.user', 'category', 'mentor.user'])
//         ->where('course_type', 'student_only')
//         ->where('status', 'active')
//         ->latest()
//         ->take(3)
//         ->get()
//         ->values()
//         ->map(function (Course $course, int $index) {
//             $totalSeats = max(0, (int) ($course->total_seats ?? 0));
//             $availableSeats = max(0, (int) ($course->available_seats ?? 0));
//             $filledSeats = $totalSeats > 0 ? max(0, $totalSeats - $availableSeats) : 0;
//             $progress = $totalSeats > 0 ? (int) round(($filledSeats / $totalSeats) * 100) : 0;
//             $startDate = $course->start_date ? Carbon::parse($course->start_date) : null;
            
//             // Material Symbols & Color Schemes matching Primary theme
//             $visuals = [
//                 ['gradient' => 'linear-gradient(135deg, #e0e7ff, #c7d2fe)', 'icon' => 'code', 'icon_color' => 'text-primary'],
//                 ['gradient' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)', 'icon' => 'verified_user', 'icon_color' => 'text-success'],
//                 ['gradient' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)', 'icon' => 'trending_up', 'icon_color' => 'text-danger'],
//             ];
//             $visual = $visuals[$index % count($visuals)];
//             $imagePath = $course->course_image ?? $course->college?->photo ?? null;
//             $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : asset('images/default-course.png');
//             return [
//                 'type' => 'student',
//                 'title' => $course->title,
//                 'image_url' => $imageUrl,
//                 'has_real_image' => !empty($imagePath), // Boolean flag to help conditional rendering in Blade
//                 'college_name' => $course->college->user->name ?? 'Partner Institution',
//                 'details_url' => route('student.course.show', $course->slug),
//                 'badge_label' => 'Student Only',
//                 'category_label' => $course->category?->name,
//                 'venue' => $course->venue ?? 'Venue TBA',
//                 'start_date' => $startDate?->format('M d') ?? 'TBA',
//                 'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
//                 'seats_label' => $totalSeats > 0 ? $filledSeats . '/' . $totalSeats . ' spaces filled' : 'Seats TBA',
//                 'progress' => $progress,
//                 'gradient' => $visual['gradient'],
//                 'icon' => $visual['icon'],
//                 'icon_color' => $visual['icon_color'],
//             ];
//         });

//     $recommendedFirmCourses = Course::with(['college.user', 'category'])
//         ->where('course_type', 'firm_only')
//         ->where('status', 'active')
//         ->latest()
//         ->take(3)
//         ->get()
//         ->values()
//         ->map(function (Course $course, int $index) {
//             // Material Symbols & Color Schemes matching Tertiary theme
//             $visuals = [
//                 ['gradient' => 'linear-gradient(135deg, #e0e7ff, #a5b4fc)', 'icon' => 'shield_with_heart', 'icon_color' => 'text-primary'],
//                 ['gradient' => 'linear-gradient(135deg, #fef3c7, #fde68a)', 'icon' => 'leaderboard', 'icon_color' => 'text-warning'],
//                 ['gradient' => 'linear-gradient(135deg, #d1fae5, #6ee7b7)', 'icon' => 'video_cam', 'icon_color' => 'text-success'],
//             ];
//             $visual = $visuals[$index % count($visuals)];
//             $imagePath = $course->course_image ?? $course->college?->photo ?? null;
//             $imageUrl = $imagePath ? asset('storage/' . ltrim($imagePath, '/')) : asset('images/default-course.png');
//             return [
//                 'type' => 'firm',
//                 'title' => $course->title,
//                 'image_url' => $imageUrl,
//                 'has_real_image' => !empty($imagePath), // Boolean flag to help conditional rendering in Blade
//                 'college_name' => $course->college->user->name ?? 'Partner Institution',
//                 'details_url' => route('firm.course.show', $course->slug),
//                 'book_url' => route('firm.course.show', $course->slug),
//                 'badge_label' => 'Firm Only',
//                 'start_date' => 'Decided by the Firm',
//                 'category_label' => $course->category?->name,
//                 'venue' => $course->venue ?? 'Chosen by the Firm',
//                 'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
//                 'seat_label' => $course->available_seats > 0
//                     ? $course->available_seats . ' seats left'
//                     : ($course->total_seats > 0 ? $course->total_seats . ' seats' : 'Flexible group size by Firm'),
//                 'gradient' => $visual['gradient'],
//                 'icon' => $visual['icon'],
//                 'icon_color' => $visual['icon_color'],
//             ];
//         });

//     // Merge both types, optionally shuffle them or leave side by side, and take up to 6 total items
//     $recommendedCourses = $recommendedStudentCourses->concat($recommendedFirmCourses);

//     return view('guest.landingbootstrap', compact('recommendedCourses'));
// }

public function show(string $slug)
    {

        $course = Course::with(['college', 'category', 'enrollments'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        if (Auth::check()) {
        $user = Auth::user();
        if ($user->role === 'firm' && $course->course_type === 'firm_only')
            return redirect()->route('firm.course.show', $slug);
        else if ($user->role === 'student' && $course->course_type === 'student_only')
            return redirect()->route('student.course.show', $slug);
        }


        return view('guest.course-details', ['course' => $course]);
    }

    
}

