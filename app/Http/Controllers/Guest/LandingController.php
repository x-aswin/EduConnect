<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

            return [
                'type' => 'student',
                'title' => $course->title,
                'college_name' => $course->college->user->name ?? 'Partner Institution',
                'details_url' => route('student.course.show', $course->slug),
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

            return [
                'type' => 'firm',
                'title' => $course->title,
                'college_name' => $course->college->user->name ?? 'Partner Institution',
                'details_url' => route('firm.course.show', $course->slug),
                'book_url' => route('firm.course.show', $course->slug),
                'badge_label' => 'Firm Only',
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
}
