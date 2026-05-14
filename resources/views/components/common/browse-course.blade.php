{{-- resources/views/components/common/explore-courses.blade.php --}}
@props(['courses', 'type', 'categories' => [], 'statuses' => [], 'sort' => 'newest'])

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
    <!-- Page heading (dynamic for firm/mentor) -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="fw-bold mb-1">
                <i class="bi {{ $type === 'mentor' ? 'bi-journal-bookmark-fill' : 'bi-compass' }} me-2 text-primary"></i>
                @if($type === 'mentor')
                    My Assigned Courses
                @elseif($type === 'firm')
                    Explore Offline Courses
                @else
                    Explore Offline Courses
                @endif
            </h1>
            <p class="text-secondary mb-0">
                @if($type === 'mentor')
                    Manage your mentorship courses and track student enrollment.
                @elseif($type === 'firm')
                    Browse courses available for your organisation. Book group training and propose your own venue & schedule.
                @else
                    Discover skill‑building programs from top colleges.
                @endif
            </p>
        </div>
        <div class="mt-3 mt-md-0"></div>
    </div>

    <!-- Search and filters -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
        <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-secondary">Search courses</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0 rounded-start-4"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control bg-light border-0 rounded-end-4" placeholder="e.g., Python, web development...">
                </div>
            </div>
            <div class="col-md-4">
                @if($type === 'mentor')
                    <label class="form-label fw-semibold small text-secondary">Status</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('status') ? 'bg-light text-secondary' : 'bg-primary bg-opacity-10 text-primary active-filter' }}">All</a>
                        @foreach($statuses as $status)
                            <a href="{{ request()->fullUrlWithQuery(['status' => $status, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('status') === $status ? 'bg-primary bg-opacity-10 text-primary active-filter' : 'bg-light text-secondary' }}">{{ ucfirst($status) }}</a>
                        @endforeach
                    </div>
                @else
                    <label class="form-label fw-semibold small text-secondary">Category</label>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') ? 'bg-light text-secondary' : 'bg-primary bg-opacity-10 text-primary active-filter' }}">All</a>
                        @foreach($categories as $category)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $category, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') === $category ? 'bg-primary bg-opacity-10 text-primary active-filter' : 'bg-light text-secondary' }}">{{ $category }}</a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Only show sort for student and mentor (firm courses don't need it) --}}
            @if ($type === 'student')
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Sort</label>
                    <select name="sort" class="form-select rounded-pill py-2 border-0 shadow-sm bg-light">
                        <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Sort by: Newest</option>
                        <option value="price_asc" {{ ($sort ?? 'newest') === 'price_asc' ? 'selected' : '' }}>Sort by: Price (low-high)</option>
                        <option value="start_soon" {{ ($sort ?? 'newest') === 'start_soon' ? 'selected' : '' }}>Sort by: Start Date</option>
                    </select>
                </div>
            @elseif ($type === 'firm')
                {{-- Firm courses: no sort dropdown, maybe just a note --}}
                <div class="col-md-3 d-flex align-items-end">
                    <span class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i> Venue & schedule proposed by you</span>
                </div>
            @elseif ($type === 'mentor')
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Sort</label>
                    <select name="sort" class="form-select rounded-pill py-2 border-0 shadow-sm bg-light">
                        <option value="newest" {{ ($sort ?? 'newest') === 'newest' ? 'selected' : '' }}>Sort by: Newest</option>
                        <option value="start_soon" {{ ($sort ?? 'newest') === 'start_soon' ? 'selected' : '' }}>Sort by: Start Date</option>
                    </select>
                </div>
            @else
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Enrollment type</label>
                    <select class="form-select rounded-pill py-2 border-0 shadow-sm bg-light">
                        <option>All types</option>
                        <option>Student only</option>
                        <option>Firm available</option>
                    </select>
                </div>
            @endif

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <a href="{{ url()->current() }}" class="btn btn-light rounded-pill px-4">Reset</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Apply</button>
            </div>
        </form>
    </div>

    {{-- Optional: Firm/Mentor info banner --}}
    @if($type === 'firm')
        <div class="alert alert-info border-0 rounded-4 d-flex align-items-center" role="alert">
            <i class="bi bi-building me-2 fs-5"></i>
            <div>
                <strong>For Organisations:</strong> You'll propose your preferred venue, date, and participant list when you book a course.
            </div>
        </div>
    @elseif($type === 'mentor')
        <div class="alert alert-success border-0 rounded-4 d-flex align-items-center" role="alert">
            <i class="bi bi-person-workspace me-2 fs-5"></i>
            <div>
                <strong>Mentor Courses:</strong> View your assigned courses, track student enrollment, and manage course details.
            </div>
        </div>
    @endif

    <!-- Course cards grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <x-common.course-card :course="$course" :type="$type" />
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-secondary mb-0 rounded-4">
                    No active courses found.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($courses, 'links'))
        <nav class="mt-5 d-flex justify-content-center">
            {{ $courses->links() }}
        </nav>
    @endif
</div>