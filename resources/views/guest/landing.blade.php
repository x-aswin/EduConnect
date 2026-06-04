<!DOCTYPE html>

<html class="light" lang="en" style=""><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>EduConnect | Professional Offline Training</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&amp;family=Plus+Jakarta+Sans:wght@600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "secondary-fixed": "#dae2fd",
                        "surface-container-lowest": "#ffffff",
                        "tertiary-fixed": "#c4e7ff",
                        "on-secondary-fixed-variant": "#3f465c",
                        "tertiary": "#005b7c",
                        "on-secondary-container": "#5c647a",
                        "surface-container-high": "#e6e8ea",
                        "outline": "#737686",
                        "on-primary-container": "#eeefff",
                        "surface-container-highest": "#e0e3e5",
                        "primary": "#004ac6",
                        "on-primary-fixed-variant": "#003ea8",
                        "on-primary": "#ffffff",
                        "inverse-primary": "#b4c5ff",
                        "on-primary-fixed": "#00174b",
                        "surface": "#f7f9fb",
                        "secondary-fixed-dim": "#bec6e0",
                        "on-secondary-fixed": "#131b2e",
                        "background": "#f7f9fb",
                        "surface-container-low": "#f2f4f6",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "on-tertiary": "#ffffff",
                        "secondary": "#565e74",
                        "on-surface-variant": "#434655",
                        "inverse-surface": "#2d3133",
                        "primary-fixed": "#dbe1ff",
                        "on-tertiary-fixed": "#001e2c",
                        "on-tertiary-container": "#e1f2ff",
                        "tertiary-fixed-dim": "#7bd0ff",
                        "inverse-on-surface": "#eff1f3",
                        "error": "#ba1a1a",
                        "on-error-container": "#93000a",
                        "tertiary-container": "#00759f",
                        "primary-fixed-dim": "#b4c5ff",
                        "outline-variant": "#c3c6d7",
                        "error-container": "#ffdad6",
                        "primary-container": "#2563eb",
                        "surface-dim": "#d8dadc",
                        "surface-bright": "#f7f9fb",
                        "on-surface": "#191c1e",
                        "on-background": "#191c1e",
                        "secondary-container": "#dae2fd"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "xl": "64px",
                        "gutter": "24px",
                        "base": "4px",
                        "md": "24px",
                        "container-max": "1280px",
                        "xs": "8px",
                        "sm": "16px",
                        "lg": "40px"
                    },
                    "fontFamily": {
                        "label-sm": ["Inter"],
                        "label-md": ["Inter"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "display-lg": ["Plus Jakarta Sans"],
                        "body-md": ["Inter"],
                        "headline-lg-mobile": ["Plus Jakarta Sans"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "body-lg": ["Inter"]
                    },
                    "fontSize": {
                        "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "600"}],
                        "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "700"}],
                        "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
            scroll-behavior: smooth;
        }
        h1, h2, h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .hero-gradient {
            background: radial-gradient(circle at top right, rgba(0, 74, 198, 0.05) 0%, transparent 40%),
                        radial-gradient(circle at bottom left, rgba(0, 74, 198, 0.03) 0%, transparent 40%);
        }
    </style>
</head>
<body class="text-on-background">
<!-- TopNavBar -->
<nav class="bg-surface fixed top-0 w-full z-50 shadow-sm border-b border-outline-variant">
<div class="flex justify-between items-center max-w-container-max mx-auto px-md h-16">
<div class="text-headline-md font-headline-md font-bold text-primary">EduConnect</div>
<!-- Desktop Nav -->
<div class="hidden md:flex items-center space-x-lg">
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-md text-label-md py-2" href="#courses">Explore Courses</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-md text-label-md py-2" href="#learn">Students / Firms</a>
<a class="text-on-surface-variant hover:text-primary transition-colors duration-200 font-label-md text-label-md py-2" href="#colleges">Colleges</a>
</div>
<div class="flex items-center space-x-sm">
    @auth
                        <a href="{{ url('/dashboard') }}">
                            <button class="bg-primary-container text-on-primary px-sm py-xs rounded-lg font-label-md text-label-md hover:opacity-90 transition-opacity">Dashboard</button>
                        </a>
    @else
                        <a href="{{ route('login') }}"><button class="px-sm py-xs font-label-md text-label-md text-on-surface-variant hover:text-primary transition-colors">Login</button></a>
                        <a href="{{ route('register') }}"><button class="bg-primary-container text-on-primary px-sm py-xs rounded-lg font-label-md text-label-md hover:opacity-90 transition-opacity">Register</button></a>
    @endauth
</div>
</div>
</nav>
<main class="pt-16">
<!-- Section 1: Hero -->
<section class="hero-gradient pt-xl pb-xl md:pt-[120px] md:pb-[120px]">
<div class="max-w-container-max mx-auto px-md text-center">
<span class="inline-block px-sm py-xs bg-secondary-container text-primary rounded-full font-label-sm text-label-sm mb-sm">Professional Excellence</span>
<h1 class="font-display-lg text-display-lg md:text-[64px] text-on-surface max-w-4xl mx-auto mb-md leading-tight">
                    Empower Your Career with <span class="text-primary">Offline Training</span> from Top Colleges
                </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto mb-lg">
                    Connect with leading institutions for professional certifications and direct mentorship. High-impact learning in institutional environments.
                </p>
<div class="flex flex-col md:flex-row justify-center gap-sm">
<a class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-md text-label-md shadow-md hover:bg-opacity-90 transition-all flex items-center justify-center gap-xs" href="#learn">
                        Start Your Journey
                        <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
</a>
<a class="border border-primary text-primary px-lg py-sm rounded-lg font-label-md text-label-md hover:bg-primary-fixed transition-all flex items-center justify-center" href="#colleges">
                        Partner as a College
                    </a>
</div>
</div>
</section>
<!-- Section 2: Value Propositions (Learners & Firms) -->
<section class="bg-surface-container-low py-xl" id="learn">
<div class="max-w-container-max mx-auto px-md">
<div class="grid grid-cols-1 md:grid-cols-2 gap-md mb-md">
<div class="flex flex-col justify-center items-start">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Flexible Learning Paths</h2>
<p class="font-body-md text-body-md text-on-surface-variant">Whether you're an individual student or a growing firm, we have a path for your professional growth.</p>
</div>
<div class="flex items-center md:justify-end">
<a class="bg-primary text-on-primary px-lg py-sm rounded-lg font-label-md text-label-md shadow-md hover:bg-opacity-90 transition-all" href="/register">
            Register as Student / Firm
        </a>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-md">
<!-- Column 1: Students -->
<div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all">
<div class="w-12 h-12 bg-primary-fixed rounded-lg flex items-center justify-center text-primary mb-md">
<span class="material-symbols-outlined" data-icon="school">school</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-sm">For Students</h3>
<ul class="space-y-sm text-on-surface-variant font-body-md text-body-md">
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                Access exclusive offline courses
                            </li>
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                Learn at prestigious campus venues
                            </li>
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                                1:1 Professional Mentorship
                            </li>
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-primary text-[20px]">info</span>
                                College-scheduled sessions with limited seats
                            </li>
</ul>
</div>
<!-- Column 2: Firms -->
<div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all">
<div class="w-12 h-12 bg-tertiary-fixed rounded-lg flex items-center justify-center text-tertiary mb-md">
<span class="material-symbols-outlined" data-icon="corporate_fare">corporate_fare</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-sm">For Firms</h3>
<ul class="space-y-sm text-on-surface-variant font-body-md text-body-md">
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                                Bulk enrollment &amp; talent management
                            </li>
<li class="flex items-center gap-xs"><span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                                Flexible scheduling for working staff</li>
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                                Custom venue &amp; training options
                            </li>
<li class="flex items-center gap-xs">
<span class="material-symbols-outlined text-primary text-[20px]">info</span>
                                You add the Participants, set Time &amp; Venue
                            </li>
</ul>
</div>
</div>
</div>
</section>
<!-- Section 3: Course Marketplace Preview -->
<section class="py-xl bg-surface" id="courses">
    <div class="max-w-container-max mx-auto px-md">
        <div class="flex justify-between items-end mb-lg">
            <div>
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Explore Top Courses</h2>
                <p class="font-body-md text-body-md text-on-surface-variant">Handpicked certifications and organizational tracks from world-class institutions.</p>
            </div>
            <a href="{{ route('firm.explore.index') }}" class="text-primary font-label-md text-label-md flex items-center gap-xs hover:underline transition-all">
                View All Courses <span class="material-symbols-outlined text-[18px]">open_in_new</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-md">
            @forelse($recommendedCourses as $course)
                @if($course['type'] === 'student')
                    <!-- STUDENT TRACK CARD PATTERN -->
                    <div class="group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="h-48 flex items-center justify-center relative overflow-hidden" style="background: {{ $course['gradient'] }}">
                                <span class="material-symbols-outlined text-[52px] {{ $course['icon_color'] }} opacity-90">
                                    {{ $course['icon'] }}
                                </span>
                                <div class="absolute top-4 left-4 bg-primary text-on-primary px-sm py-1 rounded-full font-label-sm text-label-sm shadow-sm">
                                    {{ $course['badge_label'] }}
                                </div>
                                @if(!empty($course['category_label']))
                                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-sm py-1 rounded-full text-primary font-label-sm text-label-sm">
                                        {{ $course['category_label'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-md">
                                <h4 class="font-headline-md text-headline-md text-on-surface mb-xs">{{ $course['title'] }}</h4>
                                <p class="font-label-md text-label-md text-primary mb-md">{{ $course['college_name'] }}</p>
                                
                                <div class="space-y-2 mb-md text-on-surface-variant font-label-md text-label-md">
                                    <div class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                                        <span>{{ $course['venue'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                                        <span>Starts {{ $course['start_date'] }}</span>
                                    </div>
                                </div>

                                <div class="mt-xs pt-md border-t border-outline-variant">
                                    <div class="flex justify-between items-center text-[13px] font-label-md text-on-surface-variant mb-1">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[16px]">group</span>
                                            {{ $course['seats_label'] }}
                                        </span>
                                        <span class="font-bold text-primary">{{ $course['progress'] }}% Filled</span>
                                    </div>
                                    <div class="w-full bg-surface-container-highest rounded-full h-2 overflow-hidden">
                                        <div class="bg-primary h-2 rounded-full transition-all duration-300" style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-md pb-md">
                            <div class="flex items-center justify-between pt-md border-t border-outline-variant mb-md">
                                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Tuition Fee</span>
                                <div class="font-headline-md text-headline-md text-primary font-bold">{{ $course['price_label'] }}</div>
                            </div>
                            <a href="{{ $course['details_url'] }}" class="w-full bg-primary text-on-primary py-sm rounded-lg font-label-md text-label-md shadow-sm hover:bg-opacity-90 transition-all flex items-center justify-center gap-xs group-hover:bg-primary-container">
                                <span class="material-symbols-outlined text-[18px]">school</span>
                                View Details
                                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @else
                    <!-- CORPORATE FIRM TRACK CARD PATTERN -->
                    <div class="group bg-white rounded-xl overflow-hidden border border-outline-variant shadow-sm hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="h-48 flex items-center justify-center relative overflow-hidden" style="background: {{ $course['gradient'] }}">
                                <span class="material-symbols-outlined text-[52px] {{ $course['icon_color'] }} opacity-90">
                                    {{ $course['icon'] }}
                                </span>
                                <div class="absolute top-4 left-4 bg-tertiary text-on-primary px-sm py-1 rounded-full font-label-sm text-label-sm shadow-sm">
                                    {{ $course['badge_label'] }}
                                </div>
                                @if(!empty($course['category_label']))
                                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-sm py-1 rounded-full text-tertiary font-label-sm text-label-sm">
                                        {{ $course['category_label'] }}
                                    </div>
                                @endif
                            </div>

                            <div class="p-md">
                                <h4 class="font-headline-md text-headline-md text-on-surface mb-xs">{{ $course['title'] }}</h4>
                                <p class="font-label-md text-label-md text-tertiary mb-md">{{ $course['college_name'] }}</p>
                                
                                <div class="space-y-2 mb-md text-on-surface-variant font-label-md text-label-md">
                                    <div class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[20px]">location_on</span>
                                        <span>{{ $course['venue'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                                        <span>{{ $course['start_date'] }} </span>
                                    </div>
                                    <div class="flex items-center gap-xs">
                                        <span class="material-symbols-outlined text-[20px]">groups</span>
                                        <span>{{ $course['seat_label'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="px-md pb-md">
                            <div class="flex items-center justify-between pt-md border-t border-outline-variant mb-md">
                                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Per Participant</span>
                                <div class="font-headline-md text-headline-md text-tertiary font-bold">{{ $course['price_label'] }}</div>
                            </div>
                            <a href="{{ $course['book_url'] }}" class="w-full bg-tertiary text-on-primary py-sm rounded-lg font-label-md text-label-md shadow-sm hover:bg-opacity-90 transition-all flex items-center justify-center gap-xs group-hover:bg-tertiary-container">
                                <span class="material-symbols-outlined text-[18px]">business</span>
                                View Details
                                <span class="material-symbols-outlined text-[18px] transition-transform group-hover:translate-x-1">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                @endif
            @empty
                <!-- UNIFIED SINGLE EMPTY STATE FALLBACK -->
                <div class="col-span-1 md:col-span-3 text-center py-xl px-md bg-white rounded-xl border border-outline-variant flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-surface-container-low text-outline rounded-full flex items-center justify-center mb-sm">
                        <span class="material-symbols-outlined text-[32px]">school_disabled</span>
                    </div>
                    <h4 class="font-headline-md text-on-surface mb-xs">No active courses available</h4>
                    <p class="text-on-surface-variant font-body-md max-w-md mx-auto mb-md">We are currently updating our scheduling system. Check back shortly to register.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
<!-- Section 4: Institutional Partners (New Detailed Section) -->
<section class="py-[100px] border-y border-outline-variant bg-primary-fixed/20 py-xl" id="colleges">
<div class="max-w-container-max mx-auto px-md"><div class="text-center mb-xl">
<span class="text-primary font-label-md text-label-md uppercase tracking-widest mb-sm block">Institutional Portal</span>
<h2 class="font-display-lg text-display-lg text-on-surface mb-md leading-tight">Elevate Your Institution's <span class="text-primary">Global Reach</span></h2>
<p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">Join a global network of elite institutions providing certified offline professional training to students and corporate firms.</p>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-lg bg-white rounded-[32px] overflow-hidden border border-outline-variant shadow-lg">
<!-- Benefits Content -->
<div class="lg:col-span-7 p-lg md:p-xl">
<h3 class="font-headline-lg text-headline-lg text-on-surface mb-lg">Why partner with EduConnect?</h3>
<div class="space-y-lg">
<div class="flex gap-md">
<div class="w-12 h-12 bg-primary-container text-on-primary rounded-xl flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined">monetization_on</span>
</div>
<div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Monetize Campus Resources</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Maximize the utility of your lecture halls and labs during off-peak hours by hosting professional certifications.</p>
</div>
</div>
<div class="flex gap-md">
<div class="w-12 h-12 bg-primary-container text-on-primary rounded-xl flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined">groups</span>
</div>
<div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Manage Expert Mentors</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Onboard and manage your faculty or industry partners as verified mentors within our professional ecosystem.</p>
</div>
</div>
<div class="flex gap-md">
<div class="w-12 h-12 bg-primary-container text-on-primary rounded-xl flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined">corporate_fare</span>
</div>
<div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Institutional Enrollments</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Handle bulk registrations from corporate firms and regional organizations directly through your dedicated dashboard.</p>
</div>
</div>
</div>
</div>
<!-- CTA Card -->
<div class="lg:col-span-5 bg-inverse-surface p-lg md:p-xl flex flex-col justify-center text-center border-l border-outline-variant/20">
<div class="w-20 h-20 bg-primary text-on-primary rounded-full flex items-center justify-center mx-auto mb-lg shadow-lg">
<span class="material-symbols-outlined text-[48px]">account_balance</span>
</div>
<h3 class="font-headline-lg text-headline-lg text-white mb-md">List Your Institution</h3>
<p class="text-white/80 font-body-md text-body-md mb-lg">Ready to scale your professional impact? Apply today to join 50+ world-class institutions.</p>
<a class="inline-flex items-center justify-center px-lg py-md bg-primary text-on-primary rounded-xl font-label-md text-label-md hover:bg-opacity-90 transition-all uppercase tracking-widest shadow-xl font-bold" href="/register">
            Apply for Partnership
        </a>
<div class="mt-lg flex items-center justify-center gap-xs text-white/60 font-label-sm text-label-sm">
<span class="material-symbols-outlined text-[16px]">verified</span>
            Trusted Official Partner Program
        </div>
</div>
</div></div>
</section>
<!-- Section 5: Mentorship -->
<section class="py-xl overflow-hidden">
<div class="max-w-container-max mx-auto px-md">
<div class="bg-primary rounded-3xl p-lg md:p-xl flex flex-col md:flex-row items-center gap-lg relative">
<div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
<div class="md:w-1/2 text-on-primary z-10 text-left">
<h2 class="font-display-lg text-display-lg mb-md">Direct College Mentorship</h2>
<p class="font-body-lg text-body-lg mb-lg opacity-90">
                            Don't just learn from a screen. Our 'Live Chat Mentorship' feature connects you directly with official college mentors for real-time guidance, project support, and career advice.
                        </p>
<div class="space-y-sm">
<div class="flex items-center gap-md">
<div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">forum</span>
</div>
<div>
<p class="font-label-md text-label-md font-bold">Real-time Responses</p>
<p class="font-body-md text-body-md opacity-80">Get answers from experts in minutes.</p>
</div>
</div>
<div class="flex items-center gap-md">
<div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
<span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">verified_user</span>
</div>
<div>
<p class="font-label-md text-label-md font-bold">Verified Mentors</p>
<p class="font-body-md text-body-md opacity-80">Only official college professors and alumni.</p>
</div>
</div>
</div>
</div>
<div class="md:w-1/2 w-full z-10">
<div class="bg-white rounded-2xl shadow-2xl p-md">
<div class="flex items-center gap-sm mb-md pb-md border-b border-outline-variant">
<div class="w-10 h-10 bg-secondary rounded-full overflow-hidden">
<div class="w-full h-full bg-secondary-fixed flex items-center justify-center text-primary font-bold">PM</div>
</div>
<div>
<p class="font-label-md text-label-md font-bold text-on-surface">Prof. Michael Reed</p>
<p class="text-[12px] text-green-600 font-bold flex items-center gap-1">
<span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online
                                    </p>
</div>
</div>
<div class="space-y-md mb-md">
<div class="bg-surface-container-low p-sm rounded-lg rounded-tl-none max-w-[80%]">
<p class="font-body-md text-body-md text-on-surface">Hello! I'm your mentor for the ASP.NET course. How can I help you today?</p>
</div>
<div class="bg-primary-container p-sm rounded-lg rounded-tr-none ml-auto max-w-[80%] text-on-primary">
<p class="font-body-md text-body-md">Can you explain the dependency injection pattern in Chapter 3?</p>
</div>
</div>
<div class="flex gap-sm">
<input class="flex-1 bg-surface-container border-none rounded-lg font-body-md text-body-md focus:ring-2 focus:ring-primary" placeholder="Type your question..." type="text"/>
<button class="w-12 h-12 bg-primary text-on-primary rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined">send</span>
</button>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Section 6: How it Works -->
<section class="py-xl bg-surface-container-low">
<div class="max-w-container-max mx-auto px-md">
<h2 class="font-headline-lg text-headline-lg text-on-surface text-center mb-xl">Your Journey to Success</h2>
<div class="relative grid grid-cols-1 md:grid-cols-4 gap-lg">
<!-- Step 1 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-white border-2 border-primary rounded-full flex items-center justify-center text-primary font-bold text-xl mb-md shadow-sm relative z-10">1</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Register</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Create your profile as a student or corporate entity.</p>
</div>
<!-- Step 2 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-white border-2 border-primary rounded-full flex items-center justify-center text-primary font-bold text-xl mb-md shadow-sm relative z-10">2</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Browse Courses</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Find the perfect certification from our partnered colleges.</p>
</div>
<!-- Step 3 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-white border-2 border-primary rounded-full flex items-center justify-center text-primary font-bold text-xl mb-md shadow-sm relative z-10">3</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Enroll</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Secure your spot in an upcoming offline session.</p>
</div>
<!-- Step 4 -->
<div class="flex flex-col items-center text-center">
<div class="w-16 h-16 bg-primary border-2 border-primary rounded-full flex items-center justify-center text-on-primary font-bold text-xl mb-md shadow-sm relative z-10">4</div>
<h4 class="font-headline-md text-headline-md text-on-surface mb-xs">Start Training</h4>
<p class="font-body-md text-body-md text-on-surface-variant">Head to campus and begin your professional growth.</p>
</div>
<!-- Connector Line (Desktop) -->
<div class="hidden md:block absolute top-8 left-0 w-full h-[2px] bg-outline-variant -z-0"></div>
</div>
</div>
</section>
<!-- Partners Section (Before Footer) -->
<section class="py-lg border-b border-outline-variant bg-white">
<div class="max-w-container-max mx-auto px-md">
<p class="text-center font-label-md text-label-md text-on-surface-variant mb-lg uppercase tracking-widest">Our Institutional Partners</p>
<div class="flex flex-wrap justify-center items-center gap-xl grayscale opacity-60">
<div class="flex items-center gap-xs"><span class="material-symbols-outlined text-4xl">account_balance</span> <span class="font-bold">OXFORD</span></div>
<div class="flex items-center gap-xs"><span class="material-symbols-outlined text-4xl">architecture</span> <span class="font-bold">MIT</span></div>
<div class="flex items-center gap-xs"><span class="material-symbols-outlined text-4xl">science</span> <span class="font-bold">STANFORD</span></div>
<div class="flex items-center gap-xs"><span class="material-symbols-outlined text-4xl">gavel</span> <span class="font-bold">HARVARD</span></div>
<div class="flex items-center gap-xs"><span class="material-symbols-outlined text-4xl">public</span> <span class="font-bold">CAMBRIDGE</span></div>
</div>
</div>
</section>
</main>
<!-- Footer -->
<footer class="bg-surface-container-highest w-full border-t border-outline-variant">
<div class="flex flex-col md:flex-row justify-between items-start max-w-container-max mx-auto px-md py-lg gap-lg">
<div class="max-w-xs">
<div class="text-headline-md font-headline-md font-bold text-on-surface mb-sm">EduConnect</div>
<p class="font-body-md text-body-md text-on-surface-variant">Bridging the gap between prestigious institutions and professional career growth through high-fidelity offline training.</p>
</div>
<div class="grid grid-cols-2 md:grid-cols-2 gap-lg">
<div>
<p class="font-label-md text-label-md font-bold text-on-surface mb-sm">Quick Links</p>
<ul class="space-y-xs">
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">About Us</a></li>
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">Contact</a></li>
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">Privacy Policy</a></li>
</ul>
</div>
<div>
<p class="font-label-md text-label-md font-bold text-on-surface mb-sm">Resources</p>
<ul class="space-y-xs">
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">Help Center</a></li>
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">Partner Program</a></li>
<li class=""><a class="text-on-surface-variant hover:text-on-surface transition-colors font-label-sm text-label-sm" href="#">Terms of Service</a></li>
</ul>
</div>
</div>
<div class="w-full md:w-auto">
<p class="font-label-md text-label-md font-bold text-on-surface mb-sm">Connect with us</p>
<div class="flex gap-sm mb-md">
<a class="w-10 h-10 bg-surface rounded-full flex items-center justify-center text-on-surface-variant hover:bg-primary-container hover:text-on-primary transition-all" href="#">
<span class="material-symbols-outlined text-[20px]">alternate_email</span>
</a>
<a class="w-10 h-10 bg-surface rounded-full flex items-center justify-center text-on-surface-variant hover:bg-primary-container hover:text-on-primary transition-all" href="#">
<span class="material-symbols-outlined text-[20px]">share</span>
</a>
</div>
</div>
</div>
<div class="max-w-container-max mx-auto px-md py-md border-t border-outline-variant flex flex-col md:flex-row justify-between items-center text-on-surface-variant font-label-sm text-label-sm">
<span class="">© 2024 EduConnect. All rights reserved.</span>
<div class="flex gap-md mt-sm md:mt-0">
<a class="hover:text-primary transition-colors" href="#">Privacy</a>
<a class="hover:text-primary transition-colors" href="#">Terms</a>
<a class="hover:text-primary transition-colors" href="#">Cookies</a>
</div>
</div>
</footer>
<script>
        // Micro-interactions and subtle effects
        document.querySelectorAll('button, a').forEach(elem => {
            elem.addEventListener('mousedown', () => {
                elem.style.transform = 'scale(0.98)';
            });
            elem.addEventListener('mouseup', () => {
                elem.style.transform = 'scale(1)';
            });
            elem.addEventListener('mouseleave', () => {
                elem.style.transform = 'scale(1)';
            });
        });
    </script>
</body></html>