<x-guest.layout title="Explore Courses - EduConnect" active="explore">
    @push('styles')
    <style>
        :root {
            --bs-primary: #2563eb;
            --bs-primary-rgb: 37, 99, 235;
        }
        body {
            background-color: #f8fafc;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }
        .active-filter {
            background: #2563eb !important;
            color: white !important;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
        }
        .badge-filter {
            cursor: pointer;
            transition: 0.15s;
        }
        .badge-filter:hover {
            opacity: 0.8;
        }
        .course-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid #e2e8f0;
            background: #ffffff;
        }
        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05) !important;
        }
        .rounded-4 {
            border-radius: 1rem !important;
        }
        .progress-bar-track {
            background-color: #f1f5f9;
            border-radius: 9999px;
            height: 0.5rem;
            overflow: hidden;
        }
    </style>
    @endpush

    {{-- No <nav> here – it's provided by the guest layout --}}

    <div class="container py-5">
        
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
            <div>
                <h1 class="fw-bold mb-1">
                    <i class="bi bi-compass me-2 text-primary"></i>Explore Course Catalogue
                </h1>
                <p class="text-secondary mb-0">
                    Discover specialized local tech-tracks and flexible certifications structured for self-learning students or entire company groups.
                </p>
            </div>
        </div>

        <!-- Search / Filters -->
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
                        <a href="{{ request()->fullUrlWithQuery(['category' => null, 'page' => 1]) }}" class="badge-filter badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') ? 'bg-light text-secondary' : 'bg-primary bg-opacity-10 text-primary active-filter' }}">All Fields</a>
                        @foreach($categories as $category)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $category, 'page' => 1]) }}" class="badge-filter badge text-decoration-none px-3 py-2 rounded-pill {{ request('category') === $category ? 'bg-primary bg-opacity-10 text-primary active-filter' : 'bg-light text-secondary' }}">{{ $category }}</a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold small text-secondary">Audience Segment</label>
                    <select name="track" onchange="this.form.submit()" class="form-select rounded-pill py-2 border-0 shadow-sm bg-light">
                        <option value="" {{ !request('track') ? 'selected' : '' }}>All Programs (Mixed)</option>
                        <option value="student" {{ request('track') === 'student' ? 'selected' : '' }}>Student-Only Tracks</option>
                        <option value="firm" {{ request('track') === 'firm' ? 'selected' : '' }}>Corporate Group Format</option>
                    </select>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ url()->current() }}" class="btn btn-light rounded-pill px-4">Reset</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Apply Filters</button>
                </div>
            </form>
        </div>

        <!-- Info cards -->
        <div class="row g-3 mb-5">
            <div class="col-md-6">
                <div class="p-3 bg-primary bg-opacity-10 border-0 text-primary rounded-4 h-100 d-flex align-items-start">
                    <i class="bi bi-mortarboard-fill me-2 fs-4 mt-0"></i>
                    <div>
                        <strong class="d-block mb-1">Student Enrollment Tracks</strong>
                        <span class="small text-secondary-emphasis">Individual access seats on campus routes with static calendars, transparent fills, and live tutor sessions.</span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 bg-info bg-opacity-10 border-0 text-dark-emphasis rounded-4 h-100 d-flex align-items-start" style="background-color: rgba(13, 202, 240, 0.12) !important;">
                    <i class="bi bi-building-fill me-2 fs-4 mt-0"></i>
                    <div>
                        <strong class="d-block mb-1">Corporate Booking Tracks</strong>
                        <span class="small text-secondary-emphasis">Reserved explicitly for company entities. Flexible sizing models that let you propose target venues and custom setup dates.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Cards -->
        <div class="row g-4">
            @forelse($courses as $course)
                {{-- student card --}}
                @if(($course['type'] ?? 'student') === 'student')
                    <div class="col-md-4">
                        <div class="course-card p-0 h-100 rounded-4 overflow-hidden d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-center position-relative" style="height: 140px; background: {{ $course['gradient'] ?? 'linear-gradient(135deg, #e0e7ff, #c7d2fe)' }};">
                                    <i class="bi {{ $course['icon'] ?? 'bi-code-slash' }} fs-1 {{ $course['icon_color'] ?? 'text-primary' }}"></i>
                                    <span class="badge position-absolute top-0 start-0 m-3 bg-primary text-white shadow-sm font-semibold rounded-pill">
                                        {{ $course['badge_label'] ?? 'Student Only' }}
                                    </span>
                                    @if(!empty($course['category_label']))
                                        <span class="badge position-absolute top-0 end-0 m-3 bg-white text-primary shadow-sm rounded-pill">
                                            {{ $course['category_label'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 pb-0">
                                    <h5 class="fw-bold text-dark mb-1">{{ $course['title'] }}</h5>
                                    <p class="small text-primary mb-3 fw-medium">{{ $course['college_name'] ?? 'Partner Institution' }}</p>
                                    <p class="small text-secondary mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] }}
                                    </p>
                                    <p class="small text-secondary mb-3 d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar-event me-1"></i> Starts: {{ $course['start_date'] ?? 'TBA' }}
                                    </p>
                                    <div class="pt-2 border-top border-light-subtle">
                                        <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 12px;">
                                            <span class="text-secondary"><i class="bi bi-people me-1"></i> {{ $course['seats_label'] ?? 'Seats Availability' }}</span>
                                            <span class="fw-bold text-primary">{{ $course['progress'] ?? 0 }}% Filled</span>
                                        </div>
                                        <div class="progress-bar-track w-100">
                                            <div class="bg-primary h-100 rounded-pill transition-all" style="width: {{ $course['progress'] ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle mb-3">
                                    <span class="small text-muted text-uppercase tracking-wider" style="font-size: 11px;">Tuition Fee</span>
                                    <span class="fw-bold text-primary fs-5">{{ $course['price_label'] }}</span>
                                </div>
                                <a href="{{ $course['details_url'] }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-1">
                                    <i class="bi bi-mortarboard"></i> View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- firm card --}}
                    <div class="col-md-4">
                        <div class="course-card p-0 h-100 rounded-4 overflow-hidden d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-center justify-content-center position-relative" style="height: 140px; background: {{ $course['gradient'] ?? 'linear-gradient(135deg, #e0e7ff, #a5b4fc)' }};">
                                    <i class="bi {{ $course['icon'] ?? 'bi-building' }} fs-1 {{ $course['icon_color'] ?? 'text-success' }}"></i>
                                    <span class="badge position-absolute top-0 start-0 m-3 text-white shadow-sm font-semibold rounded-pill" style="background-color: #005b7c !important;">
                                        {{ $course['badge_label'] ?? 'Firm Only' }}
                                    </span>
                                    @if(!empty($course['category_label']))
                                        <span class="badge position-absolute top-0 end-0 m-3 bg-white text-dark shadow-sm rounded-pill">
                                            {{ $course['category_label'] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="p-3 pb-0">
                                    <h5 class="fw-bold text-dark mb-1">{{ $course['title'] }}</h5>
                                    <p class="small text-secondary mb-3 fw-medium" style="color: #005b7c !important;">{{ $course['college_name'] ?? 'Partner Institution' }}</p>
                                    <p class="small text-secondary mb-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] }}
                                    </p>
                                    <p class="small text-secondary mb-3 d-flex align-items-center gap-1">
                                        <i class="bi bi-building-gear me-1"></i> {{ $course['seat_label'] ?? 'Flexible Group Options' }}
                                    </p>
                                </div>
                            </div>
                            <div class="p-3">
                                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle mb-3">
                                    <span class="small text-muted text-uppercase tracking-wider" style="font-size: 11px;">Per Participant</span>
                                    <span class="fw-bold fs-5" style="color: #005b7c !important;">{{ $course['price_label'] }}</span>
                                </div>
                                <a href="{{ $course['book_url'] ?? $course['details_url'] }}" class="btn text-white rounded-pill w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-1" style="background-color: #005b7c !important;">
                                    <i class="bi bi-building"></i> Book for Firm
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="col-12 text-center py-5 bg-white rounded-4 border">
                    <i class="bi bi-search-heart text-muted fs-1 d-block mb-2"></i>
                    <h5 class="fw-semibold text-dark">No scheduled matches active</h5>
                    <p class="text-secondary max-w-sm mx-auto mb-0">Try redefining alternative tracking configurations, resetting input lines, or removing your string search.</p>
                </div>
            @endforelse
        </div>

        @if(method_exists($courses, 'links'))
            <div class="d-flex justify-content-center mt-5">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</x-guest.layout>