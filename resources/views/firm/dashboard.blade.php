<x-firm.layout title="Dashboard - EduConnect" active="dashboard">
    @push('styles')
    <style>
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
        .section-title {
            font-weight: 700;
            font-size: 1.4rem;
        }
        .progress {
            height: 8px;
            border-radius: 10px;
        }
    </style>
    @endpush

    <div class="welcome-card d-flex flex-wrap align-items-center justify-content-between mb-4 mt-4">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-building me-1"></i> Organisation Dashboard
            </span>
            <h1 class="fw-bold mt-2 mb-1">Hello, {{ $firmName }} 👋</h1>
            <p class="text-secondary mb-0">
                Manage your group training bookings, track participants, and discover new courses for your team.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('firm.explore.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-search me-1"></i> Browse Courses
            </a>
            <a href="{{ route('firm.bookings.index') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 ms-2">
                <i class="bi bi-calendar-week"></i> My Bookings
            </a>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-bookmark-fill fs-4 text-primary"></i>
                </div>
                <div>
                    <span class="text-secondary small">Active Bookings</span>
                    <h4 class="fw-bold mb-0">{{ $activeBookings }}</h4>
                    <small class="text-success">{{ $confirmedCount }} confirmed · {{ $pendingBookings->count() }} pending</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-calendar-check fs-4 text-warning"></i>
                </div>
                <div>
                    <span class="text-secondary small">Upcoming Sessions</span>
                    <h4 class="fw-bold mb-0">{{ $upcomingCount }}</h4>
                    <small>{{ $nextSession['time'] ?? 'Next: TBA' }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-people-fill fs-4 text-info"></i>
                </div>
                <div>
                    <span class="text-secondary small">Participants</span>
                    <h4 class="fw-bold mb-0">{{ $totalParticipants }}</h4>
                    <small>Across all bookings</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-cash-coin fs-4 text-success"></i>
                </div>
                <div>
                    <span class="text-secondary small">Total Spent</span>
                    <h4 class="fw-bold mb-0">₹{{ number_format((float) $totalSpent, 0) }}</h4>
                    <small>This year</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-stars text-warning me-2"></i>Recommended for Your Team</h2>
        <a href="{{ route('firm.explore.index') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="row g-4 mb-5">
        @forelse($recommendedCourses as $course)
            <div class="col-md-4">
                <div class="course-card p-0 h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: {{ $course['gradient'] }};">
                        <i class="bi {{ $course['icon'] }} fs-1 {{ $course['icon_color'] }}"></i>
                    </div>
                    <div class="p-3">
                        <span class="badge bg-warning bg-opacity-10 text-warning mb-2">{{ $course['badge_label'] }}</span>
                        <h5 class="fw-bold mt-1">{{ $course['title'] }}</h5>
                        <p class="small text-secondary mb-2">
                            @if(!empty($course['category']))
                                <i class="bi bi-tag me-1"></i> {{ $course['category'] }} ·
                            @endif
                            <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-bold text-primary">{{ $course['price_label'] }} / participant</span>
                            <span class="small"><i class="bi bi-people"></i> {{ $course['seat_label'] }}</span>
                        </div>
                        <a href="{{ $course['book_url'] }}" class="btn btn-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
                            <i class="bi bi-building"></i> Book for Firm
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 py-4 mb-0">
                    <div class="fw-semibold mb-1">No firm-only courses are available right now.</div>
                    <div class="text-secondary mb-3">Check back later or browse the course catalogue.</div>
                    <a href="{{ route('firm.explore.index') }}" class="btn btn-primary rounded-pill">
                        <i class="bi bi-compass me-2"></i> Explore Courses
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h3 class="fw-bold mb-4"><i class="bi bi-calendar2-week text-primary me-2"></i>Your Upcoming Sessions</h3>

                @forelse($upcomingSessions as $session)
                    <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
                        <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
                            <span class="d-block fw-bold">{{ $session['day'] }}</span>
                            <small class="text-muted">{{ $session['month'] }}</small>
                        </div>
                        <div class="flex-grow-1">
                            <strong>{{ $session['title'] }}</strong><br>
                            <small>
                                <i class="bi bi-geo-alt"></i> {{ $session['venue'] }} · {{ $session['time'] }}
                            </small>
                        </div>
                        <span class="badge {{ $session['badge_class'] }} px-3 py-2">
                            {{ $session['participants'] }} participants · {{ $session['badge_label'] }}
                        </span>
                    </div>
                @empty
                    <div class="alert alert-light border rounded-4 mb-0">
                        No confirmed sessions yet. Once a booking is approved, it will show up here.
                    </div>
                @endforelse

                <div class="mt-4 text-end">
                    <a href="{{ route('firm.bookings.index') }}" class="text-decoration-none fw-semibold">
                        <i class="bi bi-calendar-plus"></i> Manage all bookings
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h3 class="fw-bold mb-4"><i class="bi bi-people-fill text-primary me-2"></i>Recent Participants</h3>
                <ul class="list-group list-group-flush">
                    @forelse($recentParticipants as $participant)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle {{ $participant['avatar_class'] }} d-inline-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px;">
                                    <span class="fw-bold">{{ strtoupper(substr($participant['name'], 0, 1)) }}</span>
                                </div>
                                <div>
                                    <strong>{{ $participant['name'] }}</strong><br>
                                    <small class="text-muted">{{ $participant['course_title'] }} · Added {{ $participant['added_at'] }}</small>
                                </div>
                            </div>
                            <span class="badge {{ $participant['badge_class'] }}">{{ $participant['badge_label'] }}</span>
                        </li>
                    @empty
                        <li class="list-group-item px-0 py-4 text-center text-muted border-bottom-0">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No participants added yet.
                        </li>
                    @endforelse
                </ul>
                <div class="mt-4">
                    <a href="{{ route('firm.bookings.index') }}" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold">
                        <i class="bi bi-people"></i> View All Participants
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-muted mt-5 mb-3 d-flex justify-content-between small">
        <span><i class="bi bi-mortarboard"></i> EduConnect · Empowering organisational training</span>
        <span><a href="#" class="text-decoration-none">Help Center</a> · <a href="#" class="text-decoration-none">Privacy</a></span>
    </div>
</x-firm.layout>