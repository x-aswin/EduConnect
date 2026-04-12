@props(['active' => null])

<x-base.layout>
@push('styles')
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            overflow: hidden;
        }

body {
    background-color: #f8f9fa;
}

#wrapper {
    display: flex;
    width: 100%;
    align-items: stretch;
    height: 100vh;
    overflow: hidden;
}

#sidebar {
    min-width: 250px;
    max-width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #111827 0%, #1f2937 100%);
    color: #fff;
    transition: all 0.3s;
    overflow-y: auto;
    flex-shrink: 0;
    box-shadow: 0 18px 45px rgba(15, 23, 42, 0.24);
}

#content {
    width: 100%;
    height: 100vh;
    overflow: hidden;
    padding: 20px;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.content-scroll {
    flex: 1;
        color: rgba(255,255,255,.78);
        border-radius: 14px;
        margin-bottom: 6px;
        padding-left: 16px;
        padding-right: 16px;
        transition: all 0.2s ease;
    min-height: 0;
}

.topbar {
        background: rgba(255,255,255,.12);
        transform: translateX(2px);
    }

    .sidebar-brand {
        padding: 1.25rem 1.25rem 0.75rem;
    }

    .sidebar-brand-badge {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        color: #93c5fd;
        font-size: 1.2rem;
    }

    .sidebar-section-label {
        font-size: 0.72rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(255,255,255,.45);
        padding: 0 1rem 0.5rem;
    top: 0;
    z-index: 1030;
}

.nav-link {
            <div class="sidebar-brand">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="sidebar-brand-badge">
                        <i class="bi bi-grid-1x2-fill"></i>
                    </div>
                    <div>
                        <h4 class="text-white mb-0 fw-semibold">EduConnect</h4>
                        <div class="text-primary small fw-medium">Admin Panel</div>
                    </div>
                </div>
            </div>

            <div class="px-3 pb-3">
                <div class="sidebar-section-label">Navigation</div>

.nav-link:hover, .nav-link.active {
    color: #fff;
    background: rgba(255,255,255,.1);
}</style>
@endpush
<div id="wrapper">
    <nav id="sidebar" class="border-end">
        <div class="p-4">
            <h4 class="text-white mb-4">EduConnect <span class="fs-6 text-primary">Admin</span></h4>
            <ul class="nav flex-column">
                    <hr class="text-white-50 my-3">
                    <a class="nav-link py-3 {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'students' ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                        <i class="bi bi-people me-2"></i> Manage Students
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'colleges' ? 'active' : '' }}" href="{{ route('admin.colleges.index') }}">
                        <i class="bi bi-building me-2"></i> Manage Colleges
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'mentors' ? 'active' : '' }}" href="{{ route('admin.mentors.index') }}">
                        <i class="bi bi-person-workspace me-2"></i> Manage Mentors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'firms' ? 'active' : '' }}" href="{{ route('admin.firms.index') }}">
                        <i class="bi bi-briefcase me-2"></i> Manage Firms
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'categories' ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                        <i class="bi bi-tags me-2"></i> Manage Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'courses' ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">
                        <i class="bi bi-journal-text me-2"></i> Manage Courses
                    </a>
                </li>
                <hr class="text-secondary">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start py-3 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <div id="content" class="d-flex flex-column">
        
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 rounded px-3 topbar">
            <div class="container-fluid">
                <span class="navbar-text fw-bold">Welcome, {{ Auth::user()->name }}</span>
                <div class="ms-auto">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Admin Account
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            {{-- <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Settings</a></li> --}}
                            {{-- <li><hr class="dropdown-divider"></li> --}}
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item text-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="content-scroll">
            <main class="flex-grow-1">
                {{ $slot }}
            </main>

            <footer class="text-center py-4 text-muted border-top mt-5">
                <p class="mb-0">&copy; {{ date('Y') }} EduConnect</p>
            </footer>
        </div>
    </div>
</div>
</x-base.layout>