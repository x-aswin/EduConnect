@props(['courses', 'type', 'categories' => [], 'sort' => 'newest'])

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
                <label class="form-label fw-semibold small text-secondary">Category</label>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') ? 'bg-light text-secondary' : 'bg-primary bg-opacity-10 text-primary active-filter' }}">All</a>
                    @foreach($categories as $category)
                        <a href="{{ request()->fullUrlWithQuery(['category' => $category, 'page' => 1]) }}" class="badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') === $category ? 'bg-primary bg-opacity-10 text-primary active-filter' : 'bg-light text-secondary' }}">{{ $category }}</a>
                    @endforeach
                </div>
            </div>
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
                 {{-- nothing for now --}}
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

    <!-- Course cards grid -->
    <div class="row g-4">
        @forelse($courses as $course)
            <x-common.course-card :course="$course" />
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