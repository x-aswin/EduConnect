<x-student.layout title="Explore Courses - EduConnect" active="explore">
{{-- resources/views/explore.blade.php --}}
@push('styles')
<style>
    .active-filter {
        background: #2563eb !important;
        color: white !important;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
    }
    .badge {
        cursor: pointer;
        transition: 0.15s;
    }
    .badge:hover {
        opacity: 0.8;
    }
</style>
@endpush

<div class="container py-4">
    <!-- Page heading -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1"><i class="bi bi-compass me-2 text-primary"></i>Explore Offline Courses</h1>
            <p class="text-secondary mb-0">Discover skill‑building programs from top colleges.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <select class="form-select rounded-pill px-4 py-2 border-0 shadow-sm" style="width: auto; background: white;">
                <option>Sort by: Newest</option>
                <option>Sort by: Price (low‑high)</option>
                <option>Sort by: Popularity</option>
            </select>
        </div>
    </div>

    <!-- Search and filters -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-secondary">Search courses</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-4"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control bg-light border-0 rounded-end-4" placeholder="e.g., Python, web development...">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small text-secondary">Category</label>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill active-filter">All</span>
                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">Programming</span>
                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">Cybersecurity</span>
                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">Data Science</span>
                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">Design</span>
                    <span class="badge bg-light text-secondary px-3 py-2 rounded-pill">Business</span>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Enrollment type</label>
                <select class="form-select rounded-pill py-2 border-0 shadow-sm bg-light">
                    <option>All types</option>
                    <option>Student only</option>
                    <option>Firm available</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Course cards grid -->
    <div class="row g-4">
        {{-- Example course card (repeat for each course) --}}
        {{-- We'll show 6 courses as an example --}}
        @php
            $courses = [
                [
                    'title' => 'Full Stack Web Development',
                    'college' => 'Cochin University College',
                    'venue' => 'CS Lab 204',
                    'start_date' => 'Apr 20, 2026',
                    'price' => 'Free',
                    'seats_total' => 30,
                    'seats_available' => 12,
                    'type' => 'student_only',
                    'image_bg' => 'linear-gradient(135deg, #e0e7ff, #c7d2fe)',
                    'icon' => 'bi-code-slash',
                    'icon_color' => 'text-primary',
                ],
                [
                    'title' => 'Ethical Hacking & Cyber Defense',
                    'college' => 'CUSAT Tech Hub',
                    'venue' => 'Seminar Hall',
                    'start_date' => 'Apr 22, 2026',
                    'price' => '₹1,200',
                    'seats_total' => 25,
                    'seats_available' => 7,
                    'type' => 'firm_available',
                    'image_bg' => 'linear-gradient(135deg, #d1fae5, #a7f3d0)',
                    'icon' => 'bi-shield-shaded',
                    'icon_color' => 'text-success',
                ],
                [
                    'title' => 'Data Analytics with Python',
                    'college' => 'Cochin University College',
                    'venue' => 'IT Lab 1',
                    'start_date' => 'Apr 25, 2026',
                    'price' => '₹950',
                    'seats_total' => 30,
                    'seats_available' => 2,
                    'type' => 'student_only',
                    'image_bg' => 'linear-gradient(135deg, #fce7f3, #fbcfe8)',
                    'icon' => 'bi-graph-up-arrow',
                    'icon_color' => 'text-danger',
                ],
                [
                    'title' => 'UI/UX Design Fundamentals',
                    'college' => 'MES College of Engineering',
                    'venue' => 'Design Studio',
                    'start_date' => 'May 2, 2026',
                    'price' => '₹750',
                    'seats_total' => 20,
                    'seats_available' => 12,
                    'type' => 'student_only',
                    'image_bg' => 'linear-gradient(135deg, #fef3c7, #fde68a)',
                    'icon' => 'bi-palette2',
                    'icon_color' => 'text-warning',
                ],
                [
                    'title' => 'Business Analytics for Managers',
                    'college' => 'IIM Kozhikode (offline cohort)',
                    'venue' => 'Auditorium',
                    'start_date' => 'May 10, 2026',
                    'price' => '₹4,500',
                    'seats_total' => 40,
                    'seats_available' => 25,
                    'type' => 'firm_available',
                    'image_bg' => 'linear-gradient(135deg, #e0e7ff, #a5b4fc)',
                    'icon' => 'bi-bar-chart-line',
                    'icon_color' => 'text-primary',
                ],
                [
                    'title' => 'Digital Marketing Bootcamp',
                    'college' => 'Rajagiri College',
                    'venue' => 'Media Lab',
                    'start_date' => 'May 15, 2026',
                    'price' => '₹1,800',
                    'seats_total' => 35,
                    'seats_available' => 13,
                    'type' => 'student_only',
                    'image_bg' => 'linear-gradient(135deg, #d1fae5, #6ee7b7)',
                    'icon' => 'bi-camera-video',
                    'icon_color' => 'text-success',
                ],
            ];
        @endphp

        @foreach($courses as $course)
            <x-common.course-card :course="$course" />
        @endforeach
    </div>

    <!-- Pagination -->
    <nav class="mt-5 d-flex justify-content-center">
        <ul class="pagination rounded-pill shadow-sm bg-white">
            <li class="page-item disabled"><a class="page-link border-0 rounded-start-pill px-3" href="#">Previous</a></li>
            <li class="page-item active"><a class="page-link border-0" href="#">1</a></li>
            <li class="page-item"><a class="page-link border-0" href="#">2</a></li>
            <li class="page-item"><a class="page-link border-0" href="#">3</a></li>
            <li class="page-item"><a class="page-link border-0 rounded-end-pill px-3" href="#">Next</a></li>
        </ul>
    </nav>
</div>
</x-student.layout>