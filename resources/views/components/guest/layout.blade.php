@props(['active' => null, 'title' => 'EduConnect', 'hideButtons' => false])
<x-base.layout :title="$title">
    @push('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #eef1fa 100%);
            padding-top: 80px;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.6rem;
            background: linear-gradient(135deg, #1e3ce0, #4f6ef6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #1e293b;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            transition: all 0.25s;
            margin: 0 0.1rem;
        }
        .navbar-nav .nav-link:hover {
            background: #eef2ff;
            color: #1d4ed8;
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background: #1d4ed8;
            color: white !important;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.3);
        }
        .btn-soft-primary {
            background: #eef2ff;
            color: #1e3a8a;
            border: none;
            font-weight: 600;
            border-radius: 50px;
        }
        .btn-soft-primary:hover {
            background: #dbeafe;
            color: #1e3a8a;
        }
        @media (max-width: 768px) {
            .navbar-collapse {
                background: white;
                border-radius: 24px;
                padding: 1rem;
                margin-top: 10px;
            }
        }
    </style>
    @endpush

    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('landing') }}">
                <i class="bi bi-mortarboard-fill fs-2 me-2" style="color: #2563eb;"></i>
                <span>EduConnect</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#guestNavbar"
                    style="background: #f1f5f9; border-radius: 12px; padding: 8px 12px;">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="guestNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                    <!-- <li class="nav-item">
                        <a class="nav-link {{ $active === 'home' ? 'active' : '' }}" href="{{ route('landing') }}">
                            <i class="bi bi-house-door-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'explore' ? 'active' : '' }}" href="{{ route('explore') }}">
                            <i class="bi bi-compass me-1 d-inline-block d-lg-none d-xl-inline"></i> Browse Courses
                        </a>
                    </li> -->
                </ul>
                @if(!$hideButtons)
                <div class="d-flex align-items-center ms-lg-3">
                    @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary rounded-pill">Dashboard</a>
                    @else
                     <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill me-2">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-primary rounded-pill">Register</a>
                    @endauth
                </div>
                @endif
            </div>
        </div>
    </nav>

    <main class="container py-2">
        {{ $slot }}
    </main>
    @include('components.common.chatbot')

    @push('scripts')
    <script>
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                document.querySelectorAll('.navbar-nav .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    @endpush
</x-base.layout>