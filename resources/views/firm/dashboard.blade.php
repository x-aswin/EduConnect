<x-firm.layout title="Dashboard - EduConnect" active="dashboard">
    @push('styles')
    <style>
        /* Reuse the same attractive dashboard styles (already in layout, but we can extend) */
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

    <!-- Welcome Section (firm-specific) -->
    <div class="welcome-card d-flex flex-wrap align-items-center justify-content-between mb-4 mt-4">
        <div>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-building me-1"></i> Organisation Dashboard
            </span>
            <h1 class="fw-bold mt-2 mb-1">Hello, {{ auth()->user()->firm?->org_name ?? 'Your Organisation' }} 👋</h1>
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

    <!-- Quick Stats Cards (firm metrics) -->
    <div class="row g-4 mb-5">
        <div class="col-6 col-md-3">
            <div class="stat-card d-flex align-items-center">
                <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-bookmark-fill fs-4 text-primary"></i>
                </div>
                <div>
                    <span class="text-secondary small">Active Bookings</span>
                    <h4 class="fw-bold mb-0">5</h4>
                    <small class="text-success">2 confirmed</small>
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
                    <h4 class="fw-bold mb-0">3</h4>
                    <small>Next: May 10</small>
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
                    <h4 class="fw-bold mb-0">24</h4>
                    <small>Across all courses</small>
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
                    <h4 class="fw-bold mb-0">₹18,500</h4>
                    <small>This year</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Courses for Your Organisation -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-stars text-warning me-2"></i>Recommended for Your Team</h2>
        <a href="{{ route('firm.explore.index') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4 mb-5">
        <!-- Course Card 1 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #e0e7ff, #a5b4fc);">
                    <i class="bi bi-shield-shaded fs-1 text-success"></i>
                </div>
                <div class="p-3">
                    <span class="badge bg-warning bg-opacity-10 text-warning mb-2">Firm Only</span>
                    <h5 class="fw-bold mt-1">Ethical Hacking Bootcamp</h5>
                    <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> Propose your venue · <i class="bi bi-calendar3 ms-2"></i> Flexible</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-primary">₹2,500 / participant</span>
                        <span class="small"><i class="bi bi-people"></i> Min 8 seats</span>
                    </div>
                    <a href="{{ route('firm.explore.show', 'ethical-hacking') }}" class="btn btn-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
                        <i class="bi bi-building"></i> Book for Firm
                    </a>
                </div>
            </div>
        </div>
        <!-- Course Card 2 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <i class="bi bi-bar-chart-line fs-1 text-primary"></i>
                </div>
                <div class="p-3">
                    <span class="badge bg-warning bg-opacity-10 text-warning mb-2">Firm Only</span>
                    <h5 class="fw-bold mt-1">Business Analytics</h5>
                    <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> Propose your venue · <i class="bi bi-calendar3 ms-2"></i> Flexible</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-primary">₹4,500 / participant</span>
                        <span class="small"><i class="bi bi-people"></i> Min 10 seats</span>
                    </div>
                    <a href="{{ route('firm.explore.show', 'business-analytics') }}" class="btn btn-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
                        <i class="bi bi-building"></i> Book for Firm
                    </a>
                </div>
            </div>
        </div>
        <!-- Course Card 3 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #d1fae5, #6ee7b7);">
                    <i class="bi bi-camera-video fs-1 text-success"></i>
                </div>
                <div class="p-3">
                    <span class="badge bg-warning bg-opacity-10 text-warning mb-2">Firm Only</span>
                    <h5 class="fw-bold mt-1">Digital Marketing</h5>
                    <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> Propose your venue · <i class="bi bi-calendar3 ms-2"></i> Flexible</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <span class="fw-bold text-primary">₹1,800 / participant</span>
                        <span class="small"><i class="bi bi-people"></i> Min 6 seats</span>
                    </div>
                    <a href="{{ route('firm.explore.show', 'digital-marketing') }}" class="btn btn-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
                        <i class="bi bi-building"></i> Book for Firm
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Bookings & Recent Activity -->
    <div class="row g-4">
        <!-- Upcoming Firm Sessions -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                <h3 class="fw-bold mb-4"><i class="bi bi-calendar2-week text-primary me-2"></i>Your Upcoming Sessions</h3>
                <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
                    <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
                        <span class="d-block fw-bold">10</span>
                        <small class="text-muted">MAY</small>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Ethical Hacking (Group Booking)</strong><br>
                        <small><i class="bi bi-geo-alt"></i> Your Office, Mumbai · 09:00 – 17:00</small>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">12 participants</span>
                </div>
                <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
                    <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
                        <span class="d-block fw-bold">15</span>
                        <small class="text-muted">MAY</small>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Business Analytics</strong><br>
                        <small><i class="bi bi-geo-alt"></i> Your Office, Bengaluru · 10:00 – 16:00</small>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Pending</span>
                </div>
                <div class="d-flex align-items-center bg-light rounded-4 p-3">
                    <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
                        <span class="d-block fw-bold">20</span>
                        <small class="text-muted">MAY</small>
                    </div>
                    <div class="flex-grow-1">
                        <strong>Digital Marketing Workshop</strong><br>
                        <small><i class="bi bi-geo-alt"></i> Your Office, Delhi · 11:00 – 15:00</small>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">8 participants</span>
                </div>
                <div class="mt-4 text-end">
                    <a href="{{ route('firm.bookings.index') }}" class="text-decoration-none fw-semibold"><i class="bi bi-calendar-plus"></i> Manage all bookings</a>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments / Participants -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
                <h3 class="fw-bold mb-4"><i class="bi bi-people-fill text-primary me-2"></i>Recent Participants</h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                        <div>
                            <strong>Rahul Sharma</strong><br>
                            <small class="text-muted">Ethical Hacking · Added 2 days ago</small>
                        </div>
                        <span class="badge bg-success">Confirmed</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                        <div>
                            <strong>Anita Patel</strong><br>
                            <small class="text-muted">Business Analytics · Added 3 days ago</small>
                        </div>
                        <span class="badge bg-warning text-dark">Pending</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                        <div>
                            <strong>Sandeep Kumar</strong><br>
                            <small class="text-muted">Digital Marketing · Added 1 week ago</small>
                        </div>
                        <span class="badge bg-success">Confirmed</span>
                    </li>
                </ul>
                <div class="mt-4">
                    <a href="{{ route('firm.bookings.index') }}" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold">
                        <i class="bi bi-people"></i> View All Participants
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-muted mt-5 mb-3 d-flex justify-content-between small">
        <span><i class="bi bi-mortarboard"></i> EduConnect · Empowering organisational training</span>
        <span><a href="#" class="text-decoration-none">Help Center</a> · <a href="#" class="text-decoration-none">Privacy</a></span>
    </div>
</x-firm.layout>