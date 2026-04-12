@props(['active' => null])

<x-base.layout>
@push('styles')
    <style>

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
    min-width: 270px;
    max-width: 270px;
    height: 100vh;
    background:#0e172a; /* Dark Sidebar */
    color: #fff;
    transition: all 0.3s;
    overflow-y: auto;
    flex-shrink: 0;
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
    overflow-y: auto;
    min-height: 0;
}

.topbar {
    position: sticky;
    top: 0;
    z-index: 1030;
}

.nav-link {
    color: rgba(213, 213, 213, 0.75);
}

.nav-link:hover, .nav-link.active {
    color: #fff;
    background: #162c53;
    border-right: #3a82f6 4px solid;
}</style>
@endpush
<div id="wrapper">
    <nav id="sidebar" class="border-end">
        <div class="p-4">
            <h4 class="text-white mb-4">EduConnect <span class="fs-6 text-primary">Admin</span></h4>
            <ul class="nav flex-column">
                <li class="nav-item">
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
                <li class="nav-item">
                    <a class="nav-link py-3 {{ $active === 'enrollments' ? 'active' : '' }}" href="{{ route('admin.enrollments.index') }}">
                        <i class="bi bi-clipboard-check me-2"></i> Manage Enrollments
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