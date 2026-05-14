<x-mentor.layout title="Dashboard - EduConnect" active="dashboard">
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

    <!-- Welcome Section -->
    <div class="welcome-card d-flex flex-wrap align-items-center justify-content-between mb-4 mt-4">
        <div>
            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-2">
                <i class="bi bi-person-workspace me-1"></i> Mentor Dashboard
            </span>
            <h1 class="fw-bold mt-2 mb-1">Welcome back, Prof. Ravi Menon 👋</h1>
            <p class="text-secondary mb-0">
                Guide your students, manage mentorship requests, and engage in live chats.
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('mentor.courses') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-journal-bookmark-fill me-1"></i> My Courses
            </a>
            <a href="{{ route('mentor.chat.requests') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 ms-2">
                <i class="bi bi-chat-dots-fill"></i> Requests (3)
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
                    <h4 class="fw-bold mb-0">5</h4>
                    <small class="text-success">3 active</small>
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
                    <h4 class="fw-bold mb-0">3</h4>
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
                    <h4 class="fw-bold mb-0">12</h4>
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
                    <h4 class="fw-bold mb-0">7</h4>
                    <small>From 3 chats</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Courses (hardcoded) -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-journal-bookmark-fill text-primary me-2"></i>My Courses</h2>
        <a href="{{ route('mentor.courses') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-4 mb-5">
        <!-- Course Card 1 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                    <i class="bi bi-code-slash fs-1 text-primary"></i>
                </div>
                <div class="p-3">
                    <h5 class="fw-bold">Full Stack Web Dev</h5>
                    <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> Cochin University College</p>
                    <p class="small text-secondary mb-1"><i class="bi bi-people me-1"></i> 8 enrolled students</p>
                    <p class="small text-secondary"><i class="bi bi-calendar3 me-1"></i> Apr 20 – Jun 20</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill w-100 mt-2 py-2 fw-semibold">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <!-- Course Card 2 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                    <i class="bi bi-shield-shaded fs-1 text-success"></i>
                </div>
                <div class="p-3">
                    <h5 class="fw-bold">Ethical Hacking</h5>
                    <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> CUSAT Tech Hub</p>
                    <p class="small text-secondary mb-1"><i class="bi bi-people me-1"></i> 12 enrolled students</p>
                    <p class="small text-secondary"><i class="bi bi-calendar3 me-1"></i> Apr 22 – May 22</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill w-100 mt-2 py-2 fw-semibold">
                        View Details
                    </a>
                </div>
            </div>
        </div>
        <!-- Course Card 3 -->
        <div class="col-md-4">
            <div class="course-card p-0">
                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 100px; background: linear-gradient(135deg, #fce7f3, #fbcfe8);">
                    <i class="bi bi-graph-up-arrow fs-1 text-danger"></i>
                </div>
                <div class="p-3">
                    <h5 class="fw-bold">Data Analytics</h5>
                    <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> Cochin University College</p>
                    <p class="small text-secondary mb-1"><i class="bi bi-people me-1"></i> 15 enrolled students</p>
                    <p class="small text-secondary"><i class="bi bi-calendar3 me-1"></i> Apr 25 – Jul 25</p>
                    <a href="#" class="btn btn-outline-primary rounded-pill w-100 mt-2 py-2 fw-semibold">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Mentorship Requests (hardcoded) -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="section-title"><i class="bi bi-chat-dots-fill text-warning me-2"></i>Pending Requests</h2>
        <a href="{{ route('mentor.chat.requests') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 avatar-circle me-2" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        AS
                    </div>
                    <div>
                        <strong>Aswin S.</strong>
                        <small class="d-block text-muted">Full Stack Web Dev</small>
                    </div>
                </div>
                <p class="small text-secondary mb-2">Requested 2 days ago</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success rounded-pill flex-grow-1"><i class="bi bi-check-circle"></i> Accept</button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1"><i class="bi bi-x-circle"></i> Decline</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 avatar-circle me-2" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        AM
                    </div>
                    <div>
                        <strong>Alice M.</strong>
                        <small class="d-block text-muted">Data Analytics</small>
                    </div>
                </div>
                <p class="small text-secondary mb-2">Requested 1 day ago</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success rounded-pill flex-grow-1"><i class="bi bi-check-circle"></i> Accept</button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1"><i class="bi bi-x-circle"></i> Decline</button>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-shrink-0 avatar-circle me-2" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        RS
                    </div>
                    <div>
                        <strong>Rahul S.</strong>
                        <small class="d-block text-muted">Ethical Hacking</small>
                    </div>
                </div>
                <p class="small text-secondary mb-2">Requested 3 hours ago</p>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-success rounded-pill flex-grow-1"><i class="bi bi-check-circle"></i> Accept</button>
                    <button class="btn btn-sm btn-outline-danger rounded-pill flex-grow-1"><i class="bi bi-x-circle"></i> Decline</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="text-muted mt-5 mb-3 d-flex justify-content-between small">
        <span><i class="bi bi-mortarboard"></i> EduConnect · Guiding the next generation</span>
        <span><a href="#" class="text-decoration-none">Help Center</a> · <a href="#" class="text-decoration-none">Privacy</a></span>
    </div>
</x-mentor.layout>