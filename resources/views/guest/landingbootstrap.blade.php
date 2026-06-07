<x-guest.layout title="EduConnect | Professional Offline Training" active="home">
    @push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary: #004ac6;
            --bs-primary-rgb: 0, 74, 198;
            --bs-tertiary: #005b7c;
            --bs-tertiary-rgb: 0, 91, 124;
            --bs-surface: #f7f9fb;
            --bs-on-surface: #191c1e;
            --bs-on-surface-variant: #434655;
            --bs-outline-variant: #c3c6d7;
            --bs-primary-container: #2563eb;
            --bs-primary-fixed: #dbe1ff;
            --bs-secondary-container: #dae2fd;
            --bs-surface-container-lowest: #ffffff;
            --bs-surface-container-high: #e6e8ea;
            --bs-surface-container-highest: #e0e3e5;
            --bs-surface-container-low: #f2f4f6;
            --bs-inverse-surface: #2d3133;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
            scroll-behavior: smooth;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(0, 74, 198, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at bottom left, rgba(0, 74, 198, 0.03) 0%, transparent 40%);
        }
        .btn-primary {
            background-color: #004ac6;
            border-color: #004ac6;
        }
        .btn-primary:hover {
            background-color: #003ea8;
            border-color: #003ea8;
        }
        .btn-tertiary {
            background-color: #005b7c;
            border-color: #005b7c;
            color: #fff;
        }
        .btn-tertiary:hover {
            background-color: #004a64;
            border-color: #004a64;
            color: #fff;
        }
        .text-primary {
            color: #004ac6 !important;
        }
        .text-tertiary {
            color: #005b7c !important;
        }
        .bg-primary-container {
            background-color: #2563eb !important;
        }
        .bg-secondary-container {
            background-color: #dae2fd !important;
        }
        .bg-tertiary-fixed {
            background-color: #c4e7ff !important;
        }
        .bg-primary-fixed {
            background-color: #dbe1ff !important;
        }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .rounded-4 {
            border-radius: 1rem;
        }
        .rounded-5 {
            border-radius: 1.5rem;
        }
        .navbar {
            background-color: var(--bs-surface);
            border-bottom: 1px solid var(--bs-outline-variant);
        }
        /* Override guest layout's navbar background to match landing */
        .navbar {
            background-color: #f7f9fb !important;
            backdrop-filter: none !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }
        .navbar-brand {
            color: #004ac6 !important;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
        }
        .navbar-nav .nav-link {
            color: #434655 !important;
        }
        .navbar-nav .nav-link:hover {
            color: #004ac6 !important;
        }
    </style>
    @endpush

    <!-- Hero Section -->
    <section class="hero-gradient pt-5 pb-5" style="padding-top: 100px; padding-bottom: 100px;">
        <div class="container text-center">
            <span class="badge bg-secondary-container text-primary px-3 py-2 rounded-pill mb-3 fw-semibold" style="font-size: 12px; background-color: #dae2fd;">
                Professional Excellence
            </span>
            <h1 class="fw-bold mb-4 mx-auto" style="max-width: 800px; font-size: 3rem; line-height: 1.2;">
                Empower Your Career with <span class="text-primary">Offline Training</span> from Top Colleges
            </h1>
            <p class="lead text-secondary mb-5 mx-auto" style="max-width: 700px; font-size: 1.15rem;">
                Connect with leading institutions for professional certifications and direct mentorship. High-impact learning in institutional environments.
            </p>
            <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                <a href="#learn" class="btn btn-primary px-4 py-3 fw-semibold d-flex align-items-center justify-content-center gap-2">
                    Start Your Journey
                    <span class="material-symbols-outlined">arrow_forward</span>
                </a>
                <a href="#colleges" class="btn btn-outline-primary px-4 py-3 fw-semibold">
                    Partner as a College
                </a>
            </div>
        </div>
    </section>

    <!-- Value Propositions -->
    <section class="py-5" style="background-color: #f2f4f6;" id="learn">
        <div class="container">
            <div class="row mb-5">
                <div class="col-md-6 d-flex flex-column justify-content-center">
                    <h2 class="fw-bold mb-2">Flexible Learning Paths</h2>
                    <p class="text-secondary" style="color: #434655;">Whether you're an individual student or a growing firm, we have a path for your professional growth.</p>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-md-end mt-3 mt-md-0">
                    <a href="/register" class="btn btn-primary px-4 py-3 fw-semibold">
                        Register as Student / Firm
                    </a>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-white p-4 rounded-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background-color: #dbe1ff; border-radius: 0.5rem;">
                            <span class="material-symbols-outlined text-primary">school</span>
                        </div>
                        <h3 class="fw-semibold mb-3">For Students</h3>
                        <ul class="list-unstyled text-secondary" style="color: #434655;">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">check_circle</span>
                                Access exclusive offline courses
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">check_circle</span>
                                Learn at prestigious campus venues
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">check_circle</span>
                                1:1 Professional Mentorship
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">info</span>
                                College-scheduled sessions with limited seats
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white p-4 rounded-4 border shadow-sm">
                        <div class="d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px; background-color: #c4e7ff; border-radius: 0.5rem;">
                            <span class="material-symbols-outlined" style="color: #005b7c;">corporate_fare</span>
                        </div>
                        <h3 class="fw-semibold mb-3">For Firms</h3>
                        <ul class="list-unstyled text-secondary" style="color: #434655;">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined" style="font-size: 20px; color: #005b7c;">check_circle</span>
                                Bulk enrollment & talent management
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined" style="font-size: 20px; color: #005b7c;">check_circle</span>
                                Flexible scheduling for working staff
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <span class="material-symbols-outlined" style="font-size: 20px; color: #005b7c;">check_circle</span>
                                Custom venue & training options
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <span class="material-symbols-outlined text-primary" style="font-size: 20px;">info</span>
                                You add the Participants, set Time & Venue
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Course Marketplace Preview -->
    <section class="py-5" style="background-color: #f7f9fb;" id="courses">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h2 class="fw-bold mb-2">Explore Top Courses</h2>
                    <p class="text-secondary">Handpicked certifications and organizational tracks from world-class institutions.</p>
                </div>
                <a href="{{ route('explore') }}" class="text-primary fw-semibold d-flex align-items-center gap-1 text-decoration-none">
                    View All Courses <span class="material-symbols-outlined" style="font-size: 18px;">open_in_new</span>
                </a>
            </div>
            <div class="row g-4">
                @forelse($recommendedCourses as $course)
                    @if($course['type'] === 'student')
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="position-relative d-flex align-items-center justify-content-center" style="height: 200px; background: {{ $course['gradient'] }};">
                                @if($course['has_real_image'])
                                    <img src="{{ $course['image_url'] }}" alt="{{ $course['title'] }}" class="position-absolute w-100 h-100 top-0 start-0 object-fit-cover">
                                @else
                                    <span class="material-symbols-outlined fs-1 {{ $course['icon_color'] }} opacity-75" style="font-size: 52px;">{{ $course['icon'] }}</span>
                                @endif
                                <span class="badge bg-primary position-absolute top-0 start-0 m-3">{{ $course['badge_label'] }}</span>
                                @if(!empty($course['category_label']))
                                    <span class="badge bg-white text-primary position-absolute top-0 end-0 m-3">{{ $course['category_label'] }}</span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold mb-1">{{ $course['title'] }}</h5>
                                <p class="text-primary mb-3">{{ $course['college_name'] }}</p>
                                <div class="text-secondary small mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">location_on</span>
                                        {{ $course['venue'] }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">calendar_month</span>
                                        Starts {{ $course['start_date'] }}
                                    </div>
                                </div>
                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span class="d-flex align-items-center gap-1">
                                            <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                                            {{ $course['seats_label'] }}
                                        </span>
                                        <span class="fw-bold text-primary">{{ $course['progress'] }}% Filled</span>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small text-uppercase">Tuition Fee</span>
                                    <span class="fw-bold fs-5 text-primary">{{ $course['price_label'] }}</span>
                                </div>
                                <a href="{{ $course['details_url'] }}" class="btn btn-primary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                    <span class="material-symbols-outlined">school</span> View Details
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="position-relative d-flex align-items-center justify-content-center" style="height: 200px; background: {{ $course['gradient'] }};">
                                @if($course['has_real_image'])
                                    <img src="{{ $course['image_url'] }}" alt="{{ $course['title'] }}" class="position-absolute w-100 h-100 top-0 start-0 object-fit-cover">
                                @else
                                    <span class="material-symbols-outlined fs-1 {{ $course['icon_color'] }} opacity-75" style="font-size: 52px;">{{ $course['icon'] }}</span>
                                @endif
                                <span class="badge bg-tertiary position-absolute top-0 start-0 m-3" style="background-color: #005b7c !important;">{{ $course['badge_label'] }}</span>
                                @if(!empty($course['category_label']))
                                    <span class="badge bg-white text-tertiary position-absolute top-0 end-0 m-3" style="color: #005b7c !important;">{{ $course['category_label'] }}</span>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="fw-bold mb-1">{{ $course['title'] }}</h5>
                                <p class="text-tertiary mb-3" style="color: #005b7c !important;">{{ $course['college_name'] }}</p>
                                <div class="text-secondary small mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">location_on</span>
                                        {{ $course['venue'] }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">calendar_month</span>
                                        {{ $course['start_date'] }}
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">groups</span>
                                        {{ $course['seat_label'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="text-muted small text-uppercase">Per Participant</span>
                                    <span class="fw-bold fs-5 text-tertiary" style="color: #005b7c !important;">{{ $course['price_label'] }}</span>
                                </div>
                                <a href="{{ $course['book_url'] }}" class="btn btn-tertiary w-100 fw-semibold d-flex align-items-center justify-content-center gap-2">
                                    <span class="material-symbols-outlined">business</span> View Details
                                    <span class="material-symbols-outlined">arrow_forward</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="col-12 text-center py-5">
                        <span class="material-symbols-outlined text-muted" style="font-size: 48px;">school_disabled</span>
                        <h4 class="mt-3">No active courses available</h4>
                        <p class="text-secondary">We are currently updating our scheduling system. Check back shortly.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Institutional Partners -->
    <section class="py-5" style="border-top: 1px solid var(--bs-outline-variant); border-bottom: 1px solid var(--bs-outline-variant); background-color: rgba(219,225,255,0.2);" id="colleges">
        <div class="container">
            <div class="text-center mb-5">
                <span class="text-primary fw-semibold text-uppercase small d-block mb-2">Institutional Portal</span>
                <h2 class="fw-bold mb-3">Elevate Your Institution's <span class="text-primary">Global Reach</span></h2>
                <p class="text-secondary mx-auto" style="max-width: 700px;">Join a global network of elite institutions providing certified offline professional training to students and corporate firms.</p>
            </div>
            <div class="row g-0 bg-white rounded-4 overflow-hidden border shadow-lg">
                <div class="col-lg-7 p-4 p-lg-5">
                    <h3 class="fw-bold mb-4">Why partner with EduConnect?</h3>
                    <div class="d-flex gap-3 mb-4">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 bg-primary-container text-white" style="width: 48px; height: 48px;">
                            <span class="material-symbols-outlined">monetization_on</span>
                        </div>
                        <div>
                            <h4 class="fw-semibold">Monetize Campus Resources</h4>
                            <p class="text-secondary">Maximize the utility of your lecture halls and labs during off-peak hours by hosting professional certifications.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mb-4">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 bg-primary-container text-white" style="width: 48px; height: 48px;">
                            <span class="material-symbols-outlined">groups</span>
                        </div>
                        <div>
                            <h4 class="fw-semibold">Manage Expert Mentors</h4>
                            <p class="text-secondary">Onboard and manage your faculty or industry partners as verified mentors within our professional ecosystem.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3 bg-primary-container text-white" style="width: 48px; height: 48px;">
                            <span class="material-symbols-outlined">corporate_fare</span>
                        </div>
                        <div>
                            <h4 class="fw-semibold">Institutional Enrollments</h4>
                            <p class="text-secondary">Handle bulk registrations from corporate firms and regional organizations directly through your dedicated dashboard.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 p-4 p-lg-5 d-flex flex-column justify-content-center align-items-center text-center text-white" style="background-color: #2d3133;">
                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-4 shadow-lg" style="width: 80px; height: 80px;">
                        <span class="material-symbols-outlined" style="font-size: 48px;">account_balance</span>
                    </div>
                    <h3 class="fw-bold mb-3">List Your Institution</h3>
                    <p class="text-white-50 mb-4">Ready to scale your professional impact? Apply today to join 50+ world-class institutions.</p>
                    <a href="/register" class="btn btn-primary px-4 py-3 fw-bold text-uppercase shadow-lg w-75">Apply for Partnership</a>
                    <div class="mt-4 d-flex align-items-center justify-content-center gap-2 text-white-50 small">
                        <span class="material-symbols-outlined" style="font-size: 16px;">verified</span>
                        Trusted Official Partner Program
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mentorship Section -->
    <section class="py-5 overflow-hidden">
        <div class="container">
            <div class="bg-primary rounded-5 p-4 p-lg-5 d-flex flex-column flex-lg-row align-items-center position-relative shadow-lg">
                <div class="position-absolute top-0 end-0 w-75 h-75 bg-white opacity-10 rounded-circle translate-middle" style="transform: translate(30%, -30%);"></div>
                <div class="col-lg-6 text-white position-relative z-1">
                    <h2 class="fw-bold mb-4">Direct College Mentorship</h2>
                    <p class="lead mb-4 opacity-90">
                        Don't just learn from a screen. Our 'Live Chat Mentorship' feature connects you directly with official college mentors for real-time guidance, project support, and career advice.
                    </p>
                    <div class="mb-3 d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle" style="width: 48px; height: 48px;">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">forum</span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0">Real-time Responses</p>
                            <p class="small opacity-75">Get answers from experts in minutes.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-20 rounded-circle" style="width: 48px; height: 48px;">
                            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                        </div>
                        <div>
                            <p class="fw-semibold mb-0">Verified Mentors</p>
                            <p class="small opacity-75">Only official college professors and alumni.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-4 mt-lg-0 position-relative z-1">
                    <div class="bg-white rounded-4 p-4 shadow-lg">
                        <div class="d-flex align-items-center gap-2 pb-3 border-bottom mb-3">
                            <div class="bg-secondary rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-primary fw-bold">PM</div>
                            </div>
                            <div>
                                <p class="fw-semibold mb-0">Prof. Michael Reed</p>
                                <p class="small text-success fw-bold d-flex align-items-center gap-1 mb-0">
                                    <span class="bg-success rounded-circle d-inline-block" style="width: 8px; height: 8px; animation: pulse 1.5s infinite;"></span> Online
                                </p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="bg-light p-3 rounded-3 mb-3" style="max-width: 80%;">
                                <p class="mb-0">Hello! I'm your mentor for the ASP.NET course. How can I help you today?</p>
                            </div>
                            <div class="bg-primary text-white p-3 rounded-3 ms-auto" style="max-width: 80%;">
                                <p class="mb-0">Can you explain the dependency injection pattern in Chapter 3?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <input class="form-control rounded-3" placeholder="Type your question...">
                            <button class="btn btn-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="material-symbols-outlined">send</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works -->
    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="fw-bold mb-5">Your Journey to Success</h2>
            <div class="row g-4 position-relative">
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-primary bg-white text-primary fw-bold mb-3 position-relative" style="width: 64px; height: 64px; z-index: 1;">1</div>
                        <h4 class="fw-semibold">Register</h4>
                        <p class="text-secondary">Create your profile as a student or corporate entity.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-primary bg-white text-primary fw-bold mb-3 position-relative" style="width: 64px; height: 64px; z-index: 1;">2</div>
                        <h4 class="fw-semibold">Browse Courses</h4>
                        <p class="text-secondary">Find the perfect certification from our partnered colleges.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle border border-primary bg-white text-primary fw-bold mb-3 position-relative" style="width: 64px; height: 64px; z-index: 1;">3</div>
                        <h4 class="fw-semibold">Enroll</h4>
                        <p class="text-secondary">Secure your spot in an upcoming offline session.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex flex-column align-items-center">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white fw-bold mb-3 position-relative" style="width: 64px; height: 64px; z-index: 1;">4</div>
                        <h4 class="fw-semibold">Start Training</h4>
                        <p class="text-secondary">Head to campus and begin your professional growth.</p>
                    </div>
                </div>
                <!-- Connector line (desktop) -->
                <div class="d-none d-md-block position-absolute top-0 start-0 w-100 h-100" style="z-index: 0;">
                    <div style="width: 100%; height: 2px; background-color: #c3c6d7; position: absolute; top: 32px;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Bar -->
    <section class="py-4 bg-white border-top border-bottom">
        <div class="container">
            <p class="text-center text-muted small text-uppercase fw-semibold mb-4">Our Institutional Partners</p>
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-5 opacity-75 grayscale">
                <div class="d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size: 2rem;">account_balance</span> <strong>OXFORD</strong></div>
                <div class="d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size: 2rem;">architecture</span> <strong>MIT</strong></div>
                <div class="d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size: 2rem;">science</span> <strong>STANFORD</strong></div>
                <div class="d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size: 2rem;">gavel</span> <strong>HARVARD</strong></div>
                <div class="d-flex align-items-center gap-1"><span class="material-symbols-outlined" style="font-size: 2rem;">public</span> <strong>CAMBRIDGE</strong></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-light py-5">
        <div class="container">
            <div class="row gy-4">
                <div class="col-md-5">
                    <h5 class="fw-bold mb-2">EduConnect</h5>
                    <p class="text-secondary small">Bridging the gap between prestigious institutions and professional career growth through high-fidelity offline training.</p>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold mb-3">Quick Links</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-secondary text-decoration-none">About Us</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3">Resources</h6>
                    <ul class="list-unstyled small">
                        <li><a href="#" class="text-secondary text-decoration-none">Help Center</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Partner Program</a></li>
                        <li><a href="#" class="text-secondary text-decoration-none">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="col-md-2">
                    <h6 class="fw-bold mb-3">Connect</h6>
                    <div class="d-flex gap-2">
                        <a href="#" class="d-flex align-items-center justify-content-center rounded-circle bg-white border text-secondary text-decoration-none" style="width: 40px; height: 40px;">
                            <span class="material-symbols-outlined">alternate_email</span>
                        </a>
                        <a href="#" class="d-flex align-items-center justify-content-center rounded-circle bg-white border text-secondary text-decoration-none" style="width: 40px; height: 40px;">
                            <span class="material-symbols-outlined">share</span>
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted small">
                <span>© 2024 EduConnect. All rights reserved.</span>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none">Privacy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms</a>
                    <a href="#" class="text-muted text-decoration-none">Cookies</a>
                </div>
            </div>
        </div>
    </footer>

    @push('scripts')
    <script>
        // Micro-interactions
        document.querySelectorAll('button, a').forEach(elem => {
            elem.addEventListener('mousedown', () => elem.style.transform = 'scale(0.98)');
            elem.addEventListener('mouseup', () => elem.style.transform = 'scale(1)');
            elem.addEventListener('mouseleave', () => elem.style.transform = 'scale(1)');
        });
    </script>
    @endpush
</x-guest.layout>