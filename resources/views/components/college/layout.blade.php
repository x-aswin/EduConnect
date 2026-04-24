@props(['active' => null])
<x-base.layout>
    @push('styles')
    <style>
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: #f4f7fc;
      padding-top: 70px; /* offset for fixed navbar */
    }
    .navbar-brand {
      font-weight: 600;
      letter-spacing: -0.02em;
    }
    .navbar {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
      background-color: #ffffff;
    }
    .navbar-nav .nav-link {
      font-weight: 500;
      color: #1e293b;
      padding: 0.5rem 1rem;
      border-radius: 12px;
      transition: all 0.15s;
    }
    .navbar-nav .nav-link:hover {
      background-color: #eef2ff;
      color: #2563eb;
    }
    .navbar-nav .nav-link.active {
      background-color: #2563eb;
      color: white !important;
    }
    .dropdown-menu {
      border: none;
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
      border-radius: 16px;
      padding: 0.5rem 0;
    }
    .dropdown-item {
      padding: 0.6rem 1.5rem;
      font-weight: 500;
    }
    .dropdown-item i {
      width: 1.4rem;
      color: #64748b;
    }
    .dropdown-item:hover {
      background-color: #f8fafc;
    }
    .institution-badge {
      background: #f1f5f9;
      border-radius: 40px;
      padding: 0.3rem 1rem;
      font-size: 0.85rem;
      font-weight: 500;
      color: #0f172a;
      border-left: 3px solid #2563eb;
    }
    .main-container {
      max-width: 1400px;
      margin: 0 auto;
    }
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.8rem;
    }
    .card-placeholder {
      background: white;
      border-radius: 24px;
      border: none;
      box-shadow: 0 8px 20px -6px rgba(0, 0, 0, 0.05);
      padding: 1.8rem 2rem;
      margin-bottom: 2rem;
    }
    .welcome-tag {
      color: #475569;
    }
    /* responsive fine-tune */
    @media (max-width: 992px) {
      .navbar-collapse {
        background: white;
        padding: 1rem;
        border-radius: 20px;
        margin-top: 10px;
        box-shadow: 0 20px 30px -10px rgba(0,0,0,0.1);
      }
    }
  </style>
    @endpush
<!-- ========== COLLEGE MASTER NAVBAR (TOP) ========== -->
<nav class="navbar navbar-expand-lg fixed-top bg-white py-2">
  <div class="container-fluid px-4">
    
    <!-- Brand / Logo -->
    <a class="navbar-brand d-flex align-items-center" href="#">
      <i class="bi bi-mortarboard-fill fs-3 me-2" style="color: #2563eb;"></i>
      <span style="font-size: 1.5rem;">EduConnect</span>
      <span class="badge ms-2 px-3 py-2 fw-normal" style="background: #e6edfb; color: #1e4bd2; border-radius: 40px; font-size: 0.8rem;">
        <i class="bi bi-building me-1"></i>College Portal
      </span>
    </a>

    <!-- Mobile toggler -->
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#collegeNavbar" 
            aria-controls="collegeNavbar" aria-expanded="false" aria-label="Toggle navigation" 
            style="box-shadow: none; background: #f1f5f9; border-radius: 12px; padding: 8px 12px;">
      <i class="bi bi-list fs-4"></i>
    </button>

    <!-- Navbar links - College specific -->
    <div class="collapse navbar-collapse" id="collegeNavbar">
      <!-- Left side navigation (main modules) -->
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" aria-current="page" href="{{ route('college.dashboard') }}">
            <i class="bi bi-speedometer2 me-1 d-inline-block d-lg-none d-xl-inline"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'courses' ? 'active' : '' }}" href="{{ route('college.courses.index') }}">
            <i class="bi bi-journal-bookmark-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Courses
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'mentors' ? 'active' : '' }}" href="{{ route('college.mentors.index') }}">
            <i class="bi bi-people-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Mentors
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'enrollments' ? 'active' : '' }}" href="{{ route('college.enrollments.index') }}">
            <i class="bi bi-person-lines-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Enrollments
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $active === 'reports' ? 'active' : '' }}" href="#">
            <i class="bi bi-bar-chart-line me-1 d-inline-block d-lg-none d-xl-inline"></i> Reports
          </a>
        </li>
        <!-- Additional quick action: Mentor management shortcut (optional) -->
        <li class="nav-item dropdown d-none d-lg-block">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-gear me-1"></i> Manage
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#"><i class="bi bi-plus-circle"></i> Add Course</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person-add"></i> Assign Mentor</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-building"></i> Institution Profile</a></li>
          </ul>
        </li>
      </ul>

      <!-- Right side: Institution info + profile dropdown -->
      <div class="d-flex align-items-center ms-lg-3">
        <!-- Institution chip (college name) -->
        <div class="institution-badge d-none d-md-block me-3">
          <i class="bi bi-pin-map-fill me-1" style="color:#2563eb;"></i> 
          <span>Cochin University College</span>
          <span class="vr mx-2"></span>
          <i class="bi bi-check-circle-fill text-success me-1" style="font-size: 0.7rem;"></i>
          <span class="text-success">Verified</span>
        </div>
        
        <!-- Notifications icon (mock) -->
        <a href="#" class="text-dark me-3 position-relative d-none d-md-block">
          <i class="bi bi-bell fs-5"></i>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; padding: 0.25rem 0.4rem;">
            3
          </span>
        </a>

        <!-- College admin / profile dropdown -->
        <div class="dropdown">
          <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="collegeUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
              <i class="bi bi-person-workspace fs-5" style="color:#2563eb;"></i>
            </div>
            <div class="d-none d-lg-block ms-2 text-start lh-sm">
              <span class="d-block fw-semibold" style="font-size: 0.95rem;">{{ auth()->user()->name ?? 'College Admin' }}</span>
              <span class="d-block small text-secondary" style="font-size: 0.75rem;">College</span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-circle"></i> My Profile</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-building"></i> College Settings</a></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-shield-check"></i> Verification Status</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-question-circle"></i> Help & Support</a></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</nav>

<main class="main-container px-3 px-md-4 py-3">
  {{ $slot }}
</main>

@push('scripts')
<script>
  // Simple active link simulation (just to keep consistency)
  document.querySelectorAll('.navbar-nav .nav-link').forEach((link) => {
    link.addEventListener('click', function () {
      document.querySelectorAll('.navbar-nav .nav-link').forEach((l) => l.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>
@endpush
</x-base.layout>