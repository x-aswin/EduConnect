<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $student = Auth::user();

        abort_unless($student && $student->role === 'student', 403);

        $enrollments = Enrollment::with(['course.college.user', 'course.mentor.user'])
            ->where('user_id', $student->id)
            ->where('type', 'student')
            ->latest('updated_at')
            ->get();

        $enrolledCount = $enrollments->count();

        $upcomingSessions = $enrollments
            ->filter(function (Enrollment $enrollment) {
                $course = $enrollment->course;

                return $enrollment->status === 'confirmed'
                    && $course
                    && $course->start_date
                    && Carbon::parse($course->start_date)->greaterThanOrEqualTo(Carbon::today());
            })
            ->sortBy(function (Enrollment $enrollment) {
                return $enrollment->course?->start_date ?? Carbon::now()->addYear();
            })
            ->take(3)
            ->values()
            ->map(function (Enrollment $enrollment, int $index) {
                $course = $enrollment->course;
                $startDate = $course?->start_date ? Carbon::parse($course->start_date) : null;
                $badgeVariants = [
                    'bg-primary bg-opacity-10 text-primary',
                    'bg-warning bg-opacity-10 text-warning',
                    'bg-info bg-opacity-10 text-info',
                ];

                return [
                    'day' => $startDate?->format('d') ?? '--',
                    'month' => $startDate?->format('M') ?? 'TBA',
                    'title' => $course?->title ?? 'Upcoming session',
                    'venue' => $course?->venue ?? 'Venue TBA',
                    'time_slot' => $course?->time_slot ?? 'Time TBA',
                    'mentor' => $course?->mentor?->user?->name ?? 'Mentor TBA',
                    'badge_class' => $badgeVariants[$index % count($badgeVariants)],
                ];
            });

        $mentorCount = $enrollments
            ->pluck('course.mentor_id')
            ->filter()
            ->unique()
            ->count();

        $unreadChatsCount = Message::query()
            ->whereHas('chat', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where('sender_id', '!=', $student->id)
            ->where('is_read', false)
            ->count();

        $recentChats = Chat::with([
                'mentor',
                'course.college',
                'messages' => fn ($query) => $query->latest(),
            ])
            ->where('student_id', $student->id)
            ->latest()
            ->take(3)
            ->get()
            ->values()
            ->map(function (Chat $chat, int $index) use ($student) {
                $latestMessage = $chat->messages->first();
                $statusMap = [
                    'pending' => ['label' => 'pending', 'class' => 'bg-warning text-dark'],
                    'accepted' => ['label' => 'online', 'class' => 'bg-success'],
                    'declined' => ['label' => 'closed', 'class' => 'bg-secondary'],
                ];

                $status = $statusMap[$chat->status] ?? ['label' => ucfirst($chat->status), 'class' => 'bg-secondary'];

                return [
                    'mentor_name' => $chat->mentor?->name ?? 'Mentor',
                    'course_title' => $chat->course?->title ?? 'Mentorship chat',
                    'message' => Str::limit($latestMessage?->message ?? 'No messages yet.', 96),
                    'time' => $latestMessage?->created_at?->diffForHumans() ?? $chat->created_at?->diffForHumans() ?? 'Just now',
                    'unread_count' => $chat->messages
                        ->where('sender_id', '!=', $student->id)
                        ->where('is_read', false)
                        ->count(),
                    'status_label' => $status['label'],
                    'status_class' => $status['class'],
                    'avatar_class' => [
                        'bg-primary bg-opacity-10 text-primary',
                        'bg-warning bg-opacity-10 text-warning',
                        'bg-info bg-opacity-10 text-info',
                    ][$index % 3],
                ];
            });

        $enrolledCourseIds = $enrollments->pluck('course_id')->all();

        $recommendedCourses = Course::with(['college.user', 'category', 'mentor.user'])
            ->where('course_type', 'student_only')
            ->where('status', 'active')
            ->when(! empty($enrolledCourseIds), function ($query) use ($enrolledCourseIds) {
                $query->whereNotIn('id', $enrolledCourseIds);
            })
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
                $visuals = [
                    [
                        'gradient' => 'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                        'icon' => 'bi-code-slash',
                        'icon_color' => 'text-primary',
                    ],
                    [
                        'gradient' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                        'icon' => 'bi-shield-shaded',
                        'icon_color' => 'text-success',
                    ],
                    [
                        'gradient' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                        'icon' => 'bi-graph-up-arrow',
                        'icon_color' => 'text-danger',
                    ],
                ];
                $visual = $visuals[$index % count($visuals)];

                return [
                    'title' => $course->title,
                    'details_url' => route('student.course.show', $course->slug),
                    'badge_class' => $course->mentor_id ? 'bg-warning bg-opacity-10 text-warning' : 'bg-primary bg-opacity-10 text-primary',
                    'badge_label' => $course->mentor_id ? 'Mentored' : 'Student Only',
                    'venue' => $course->venue ?? 'Venue TBA',
                    'start_date' => $startDate?->format('M d') ?? 'TBA',
                    'price_label' => ((float) $course->price <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0),
                    'seats_label' => $totalSeats > 0 ? $filledSeats . '/' . $totalSeats . ' seats' : 'Seats TBA',
                    'progress' => $progress,
                    'gradient' => $visual['gradient'],
                    'icon' => $visual['icon'],
                    'icon_color' => $visual['icon_color'],
                ];
            });

        return view('student.dashboard', [
            'studentName' => $student->name,
            'enrolledCount' => $enrolledCount,
            'upcomingCount' => $upcomingSessions->count(),
            'mentorCount' => $mentorCount,
            'unreadChatsCount' => $unreadChatsCount,
            'recommendedCourses' => $recommendedCourses,
            'upcomingSessions' => $upcomingSessions,
            'recentChats' => $recentChats,
        ]);
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
