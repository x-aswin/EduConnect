<?php

namespace App\Http\Controllers\Firm;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\FirmParticipant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless($user && $user->role === 'firm', 403);

        $firmName = $user->firm?->org_name ?? 'Your Organisation';

        $bookings = Enrollment::with(['course.college.user', 'participants'])
            ->where('user_id', $user->id)
            ->where('type', 'firm')
            ->latest()
            ->get();

        $activeBookings = $bookings->whereIn('status', ['pending', 'confirmed'])->count();
        $confirmedBookings = $bookings->where('status', 'confirmed');
        $pendingBookings = $bookings->where('status', 'pending');

        $totalParticipants = $bookings->sum(function (Enrollment $booking) {
            return max((int) $booking->participants->count(), (int) ($booking->participant_count ?? 0));
        });

        $totalSpent = $confirmedBookings
            ->filter(function (Enrollment $booking) {
                return $booking->payment_status === 'paid' && $booking->created_at?->year === now()->year;
            })
            ->sum(function (Enrollment $booking) {
                return (float) $booking->total_amount;
            });

        $upcomingSessions = $confirmedBookings
            ->filter(function (Enrollment $booking) {
                return $booking->proposed_schedule && Carbon::parse($booking->proposed_schedule)->greaterThanOrEqualTo(Carbon::now());
            })
            ->sortBy('proposed_schedule')
            ->take(3)
            ->values()
            ->map(function (Enrollment $booking, int $index) {
                $schedule = $booking->proposed_schedule ? Carbon::parse($booking->proposed_schedule) : null;
                $badgeVariants = [
                    'bg-success bg-opacity-10 text-success',
                    'bg-warning bg-opacity-10 text-warning',
                    'bg-info bg-opacity-10 text-info',
                ];

                return [
                    'day' => $schedule?->format('d') ?? '--',
                    'month' => $schedule?->format('M') ?? 'TBA',
                    'title' => $booking->course?->title ?? 'Booked course',
                    'venue' => $booking->requested_venue ?? $booking->course?->venue ?? 'Venue TBA',
                    'time' => $schedule?->format('M d, Y h:i A') ?? 'Schedule TBA',
                    'participants' => max((int) $booking->participants->count(), (int) ($booking->participant_count ?? 0)),
                    'badge_class' => $badgeVariants[$index % count($badgeVariants)],
                    'badge_label' => $booking->payment_status === 'paid' ? 'Paid' : 'Pending payment',
                ];
            });

        $recentParticipants = FirmParticipant::with(['enrollment.course'])
            ->whereHas('enrollment', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('type', 'firm');
            })
            ->latest()
            ->take(3)
            ->get()
            ->values()
            ->map(function (FirmParticipant $participant, int $index) {
                $status = $participant->enrollment?->status;
                $statusMap = [
                    'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-success'],
                    'pending' => ['label' => 'Pending', 'class' => 'bg-warning text-dark'],
                ];
                $statusInfo = $statusMap[$status] ?? ['label' => ucfirst((string) $status ?: 'Unknown'), 'class' => 'bg-secondary'];

                return [
                    'name' => $participant->name,
                    'course_title' => $participant->enrollment?->course?->title ?? 'Booked course',
                    'added_at' => $participant->created_at?->diffForHumans() ?? 'Just now',
                    'badge_label' => $statusInfo['label'],
                    'badge_class' => $statusInfo['class'],
                    'avatar_class' => [
                        'bg-primary bg-opacity-10 text-primary',
                        'bg-warning bg-opacity-10 text-warning',
                        'bg-info bg-opacity-10 text-info',
                    ][$index % 3],
                ];
            });

        $bookedCourseIds = $bookings->pluck('course_id')->filter()->unique()->values()->all();

        $recommendedCourses = Course::with(['college.user', 'category'])
            ->where('course_type', 'firm_only')
            ->where('status', 'active')
            ->when(! empty($bookedCourseIds), function ($query) use ($bookedCourseIds) {
                $query->whereNotIn('id', $bookedCourseIds);
            })
            ->latest()
            ->take(3)
            ->get()
            ->values()
            ->map(function (Course $course, int $index) {
                $gradients = [
                    'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                ];

                $icons = [
                    'bi-shield-shaded',
                    'bi-bar-chart-line',
                    'bi-camera-video',
                ];

                $colors = [
                    'text-success',
                    'text-primary',
                    'text-info',
                ];

                return [
                    'title' => $course->title,
                    'details_url' => route('firm.course.show', $course->slug),
                    'book_url' => route('firm.course.show', $course->slug),
                    'badge_label' => 'Firm Only',
                    'category' => $course->category?->name,
                    'venue' => $course->venue ?? 'Venue TBA',
                    'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seat_label' => $course->available_seats > 0
                        ? $course->available_seats . ' seats left'
                        : ($course->total_seats > 0 ? $course->total_seats . ' seats' : 'Flexible group size'),
                    'gradient' => $gradients[$index % count($gradients)],
                    'icon' => $icons[$index % count($icons)],
                    'icon_color' => $colors[$index % count($colors)],
                ];
            });

        $upcomingCount = $upcomingSessions->count();
        $confirmedCount = $confirmedBookings->count();
        $nextSession = $upcomingSessions->first();

        return view('firm.dashboard', compact(
            'firmName',
            'activeBookings',
            'confirmedCount',
            'pendingBookings',
            'upcomingCount',
            'totalParticipants',
            'totalSpent',
            'nextSession',
            'recommendedCourses',
            'upcomingSessions',
            'recentParticipants'
        ));
    }
}
