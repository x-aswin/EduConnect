<x-mentor.layout title="Dashboard - EduConnect" active="dashboard">
    @push('styles')
    <style>
        .dashboard-hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 55%, #eef2ff 100%);
        }
        .dashboard-hero::after {
            content: '';
            position: absolute;
            inset: auto -10% -40% auto;
            width: 18rem;
            height: 18rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, rgba(59,130,246,0) 70%);
            pointer-events: none;
        }
        .welcome-card {
            background: white;
            border-radius: 2rem;
            padding: 2rem;
            border: none;
            box-shadow: 0 15px 35px rgba(0,0,0,0.03);
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            border-radius: 1.5rem;
            padding: 1.4rem;
            border: none;
            box-shadow: 0 10px 20px -10px rgba(0,0,0,0.04);
            transition: 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 24px -8px rgba(0,0,0,0.08);
        }
        .course-card {
            border-radius: 1.4rem;
            border: none;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            transition: all 0.2s;
            background: white;
        }
        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 32px rgba(0,0,0,0.08);
        }
        .course-visual {
            min-height: 108px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .section-title {
            font-weight: 700;
            font-size: 1.4rem;
        }
        .progress {
            height: 8px;
            border-radius: 10px;
        }
        .request-card {
            border-radius: 1.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            transition: 0.2s;
        }
        .request-card:hover {
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
    </style>
    @endpush

    @php
        $courseGradients = [
            'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
            'linear-gradient(135deg, #d1fae5, #a7f3d0)',
            'linear-gradient(135deg, #fce7f3, #fbcfe8)',
        ];

        $courseIcons = ['bi-code-slash', 'bi-shield-shaded', 'bi-graph-up-arrow'];
        $courseIconColors = ['text-primary', 'text-success', 'text-danger'];
    @endphp

    <div class="welcome-card dashboard-hero d-flex flex-wrap align-items-center justify-content-between mb-4 mt-4 position-relative">
        <div class="position-relative">
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-person-workspace me-1"></i> Mentor Dashboard
            </span>
            <h1 class="fw-bold mt-2 mb-1">Welcome back, {{ $user->name }} 👋</h1>
            <p class="text-secondary mb-2">
                Guide your students, manage mentorship requests, and engage in live chats.
            </p>
            <div class="small text-muted">
                @if($mentorProfile?->qualification)
                    <span class="me-3"><i class="bi bi-award me-1"></i>{{ $mentorProfile->qualification }}</span>
                @endif
                @if($mentorProfile?->college?->institution_name)
                    <span><i class="bi bi-building me-1"></i>{{ $mentorProfile->college->institution_name }}</span>
                @endif
            </div>
        </div>
        <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2">
            <a href="{{ route('mentor.mycourses') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-journal-bookmark-fill me-1"></i> My Courses
            </a>
            <a href="{{ route('mentor.chat.requests') }}" class="btn btn-outline-primary rounded-pill px-4 py-2">
                <i class="bi bi-chat-dots-fill me-1"></i> Requests ({{ $pendingRequestsCount }})
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-bookmark-fill fs-4 text-primary"></i>
                </div>
                <div>
                    <span class="text-secondary small">Assigned Courses</span>
                    <h4 class="fw-bold mb-0">{{ $totalCourses }}</h4>
                    <small class="text-success">{{ $activeCourses }} active</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                </div>
                <div>
                    <span class="text-secondary small">Pending Requests</span>
                    <h4 class="fw-bold mb-0">{{ $pendingRequestsCount }}</h4>
                    <small>From students</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-people-fill fs-4 text-success"></i>
                </div>
                <div>
                    <span class="text-secondary small">Active Mentees</span>
                    <h4 class="fw-bold mb-0">{{ $activeMenteesCount }}</h4>
                    <small>Across courses</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-chat-dots-fill fs-4 text-info"></i>
                </div>
                <div>
                    <span class="text-secondary small">Unread Messages</span>
                    <h4 class="fw-bold mb-0">{{ $unreadMessagesCount }}</h4>
                    <small>From active chats</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Courses -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>My Courses</h2>
        <a href="{{ route('mentor.mycourses') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4 mb-5">
        @forelse($recentCourses as $course)
            @php
                $theme = $courseGradients[$loop->index % count($courseGradients)];
                $icon = $courseIcons[$loop->index % count($courseIcons)];
                $iconColor = $courseIconColors[$loop->index % count($courseIconColors)];
                $collegeName = $course->college?->institution_name ?? $course->college?->user?->name ?? 'Unknown College';
                $courseImageUrl = $course->course_image
                    ? asset('storage/' . ltrim($course->course_image, '/'))
                    : ($course->college?->photo ? asset('storage/' . ltrim($course->college->photo, '/')) : null);
                $dateRange = $course->start_date
                    ? \Illuminate\Support\Carbon::parse($course->start_date)->format('M d, Y')
                    : 'Schedule pending';

                if ($course->end_date) {
                    $dateRange .= ' – ' . \Illuminate\Support\Carbon::parse($course->end_date)->format('M d, Y');
                }

                $courseTypeLabel = ucfirst(str_replace('_', ' ', $course->course_type ?? 'course'));
            @endphp
            <div class="col-md-4">
                <div class="course-card p-0 h-120">
                    <div class="course-visual" style="background: {{ $theme }};">
                        @if($courseImageUrl)
                            <img src="{{ $courseImageUrl }}" alt="{{ $course->title }}" class="img-fluid w-100 h-100" style="max-height: 108px; object-fit: cover;">
                        @else
                            <i class="bi {{ $icon }} fs-1 {{ $iconColor }}"></i>
                        @endif
                    </div>
                    <div class="p-3 d-flex flex-column h-100">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $course->title }}</h5>
                                <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> {{ $collegeName }}</p>
                            </div>
                            <span class="badge {{ $course->status === 'active' ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-secondary border border-secondary' }}">
                                {{ ucfirst($course->status ?? 'inactive') }}
                            </span>
                        </div>
                        <p class="small text-secondary mb-1"><i class="bi bi-tag me-1"></i> {{ $course->category?->name ?? $courseTypeLabel }}</p>
                        <p class="small text-secondary mb-1"><i class="bi bi-people me-1"></i> {{ $course->enrollments_count }} enrolled students</p>
                        <p class="small text-secondary mb-3"><i class="bi bi-calendar3 me-1"></i> {{ $dateRange }}</p>
                        <a href="{{ route('mentor.coursedetails', ['slug' => $course->slug]) }}" class="btn btn-outline-primary rounded-pill w-100 mt-auto py-2 fw-semibold">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="bi bi-journal-bookmark fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">No courses assigned yet</h5>
                    <p class="text-secondary mb-0">Courses assigned to your mentor profile will appear here.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pending Mentorship Requests -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-chat-dots-fill text-warning me-2"></i>Pending Requests</h2>
        <a href="{{ route('mentor.chat.requests') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3 mb-4">
        @forelse($pendingRequests as $request)
            @php
                $studentName = $request->student?->name ?? 'Student';
                $studentInitials = collect(explode(' ', $studentName))
                    ->filter()
                    ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                    ->take(2)
                    ->implode('');
                $studentInitials = $studentInitials ?: 'S';
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="request-card card border-0 p-3 bg-white h-100">
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0 avatar-circle me-2" style="width: 40px; height: 40px; font-size: 0.9rem;">
                            {{ $studentInitials }}
                        </div>
                        <div>
                            <strong>{{ $studentName }}</strong>
                            <small class="d-block text-muted">{{ $request->course?->title ?? 'Mentorship Request' }}</small>
                        </div>
                    </div>
                    <p class="small text-secondary mb-2">
                        Requested {{ $request->created_at?->diffForHumans() ?? 'recently' }}
                    </p>
                    <div class="d-flex gap-2">
                        <form action="{{ route('mentor.chat.accept', $request->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success rounded-pill w-100">
                                <i class="bi bi-check-circle"></i> Accept
                            </button>
                        </form>
                        <form action="{{ route('mentor.chat.decline', $request->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill w-100">
                                <i class="bi bi-x-circle"></i> Decline
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-warning"></i>
                    <h5 class="fw-bold mt-2">No pending requests</h5>
                    <p class="text-secondary mb-0">New mentorship requests will appear here as students reach out.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Footer -->
    <div class="text-muted mt-5 mb-3 d-flex justify-content-between small">
        <span><i class="bi bi-mortarboard"></i> EduConnect · Guiding the next generation</span>
        <span><a href="#" class="text-decoration-none">Help Center</a> · <a href="#" class="text-decoration-none">Privacy</a></span>
    </div>
</x-mentor.layout>