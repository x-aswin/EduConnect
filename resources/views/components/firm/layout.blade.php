{{-- resources/views/components/firm/layout.blade.php --}}
@props(['active' => null, 'title' => 'EduConnect'])
<x-base.layout :title="$title">
    @push('styles')
    <style>
    /* ========================================
       CORE LAYOUT STYLES (same as student)
       ======================================== */
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

    .dropdown-menu {
      border: none;
      box-shadow: 0 20px 40px rgba(0,0,0,0.08);
      border-radius: 20px;
      padding: 0.5rem;
      background: rgba(255,255,255,0.9);
      backdrop-filter: blur(12px);
    }

    .dropdown-item {
      border-radius: 12px;
      padding: 0.6rem 1.2rem;
      font-weight: 500;
      transition: 0.15s;
    }

    .dropdown-item:hover {
      background: #f1f5f9;
    }

    .avatar-circle {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      background: linear-gradient(135deg, #2563eb, #4f46e5);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 600;
      font-size: 1.2rem;
      overflow: hidden; /* ensure image stays round */
    }

    .avatar-circle img {
      width: 100%;
      height: 100%;
      object-fit: cover;
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
    <a class="navbar-brand d-flex align-items-center" href="{{ route('firm.dashboard') }}">
      <i class="bi bi-mortarboard-fill fs-2 me-2" style="color: #2563eb;"></i>
      <span>EduConnect</span>
    </a>

    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#firmNavbar" 
            aria-controls="firmNavbar" aria-expanded="false" aria-label="Toggle navigation"
            style="background: #f1f5f9; border-radius: 12px; padding: 8px 12px;">
      <i class="bi bi-list fs-4"></i>
    </button>

    <div class="collapse navbar-collapse" id="firmNavbar">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('firm.dashboard') }}">
            <i class="bi bi-house-door-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'explore' ? 'active' : '' }}" href="{{ route('firm.explore.index') }}">
            <i class="bi bi-compass me-1 d-inline-block d-lg-none d-xl-inline"></i> Browse Courses
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'bookings' ? 'active' : '' }}" href="{{ route('firm.bookings.index') }}">
            <i class="bi bi-journal-check me-1 d-inline-block d-lg-none d-xl-inline"></i> My Bookings
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'reports' ? 'active' : '' }}" href="{{ route('firm.reports') }}">
            <i class="bi bi-bar-chart-line me-1 d-inline-block d-lg-none d-xl-inline"></i> Reports
          </a>
        </li>
        <li class="nav-item dropdown d-none d-lg-block">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-building me-1"></i> Organisation
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('firm.profile.edit') }}"><i class="bi bi-pencil-square"></i> Edit Profile</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-people-fill"></i> Manage Participants</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('firm.bookings.index') }}"><i class="bi bi-calendar-check"></i> Upcoming Sessions</a></li>
          </ul>
        </li>
      </ul>

      <div class="d-flex align-items-center ms-lg-3">
        <!-- Notification -->
        <a href="#" class="text-dark position-relative me-3 d-none d-md-block">
          <i class="bi bi-bell fs-5"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">
            2
          </span>
        </a>

        <!-- Firm profile dropdown -->
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="firmDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="avatar-circle me-2 d-none d-md-flex">
              @if(auth()->user()->firm?->photo)
                <img src="{{ asset('storage/' . auth()->user()->firm->photo) }}" alt="Firm Logo">
              @else
                <span>{{ strtoupper(substr(auth()->user()->firm?->org_name ?? 'F', 0, 1)) }}</span>
              @endif
            </div>
            <div class="d-none d-lg-block text-start lh-sm">
              <span class="d-block fw-semibold" style="font-size: 0.95rem;">{{ auth()->user()->firm?->org_name ?? auth()->user()->name }}</span>
              <span class="d-block small text-secondary" style="font-size: 0.75rem;">
                {{ auth()->user()->firm?->contact_person ?? 'Firm Admin' }}{{ auth()->user()->firm?->designation ? ' · ' . auth()->user()->firm->designation : '' }}
              </span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('firm.profile.edit') }}"><i class="bi bi-building"></i> Organisation Profile</a></li>
            <li><a class="dropdown-item" href="{{ route('firm.profile.edit') }}"><i class="bi bi-person-circle"></i> My Account</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger">
                  <i class="bi bi-box-arrow-right"></i> Logout
                </button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

<x-toast />

<main class="container py-2">
  {{ $slot }}
</main>

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