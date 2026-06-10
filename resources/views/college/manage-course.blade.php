<x-college.layout active="courses">

@php
    $isEdit = isset($editCourse) && !isset($viewOnly);
    $isView = isset($editCourse) && isset($viewOnly);
    $isCreate = !isset($editCourse);
    $queryParams = collect(request()->query())->except('mode')->toArray();
    $indexUrl = route('college.courses.index', $queryParams);

    $oldSections = old('sections');
    if (is_array($oldSections)) {
        $sectionRows = array_values($oldSections);
    } elseif (isset($editCourse)) {
        $sectionRows = ($editCourse->sections ?? collect())->map(function ($section) {
            return [
                'id' => $section->id,
                'heading' => $section->section_heading,
                'content' => $section->section_content,
            ];
        })->values()->all();
    } else {
        $sectionRows = [];
    }

    if (empty($sectionRows)) {
        $sectionRows = [[
            'id' => '',
            'heading' => '',
            'content' => '',
        ]];
    }

    $totalCount = $courses->count();
    $activeCount = $courses->filter(fn ($course) => ($course->status ?? 'inactive') === 'active')->count();
    $studentCount = $courses->filter(fn ($course) => ($course->course_type ?? '') === 'student_only')->count();
    $firmCount = $courses->filter(fn ($course) => ($course->course_type ?? '') === 'firm_only')->count();
@endphp

<div class="course-hero p-4 p-md-5 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-white">Course Workspace</h3>
            <p class="mb-0 text-white-50">Create, edit, and organize your offline training programs for students and corporate firms.</p>
        </div>
        <button type="button" class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="bi bi-plus-lg"></i> Add New Course
        </button>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Total Courses</div>
                <div class="text-white fw-bold fs-4">{{ $totalCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Active</div>
                <div class="text-white fw-bold fs-4">{{ $activeCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Student Only</div>
                <div class="text-white fw-bold fs-4">{{ $studentCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Firm Only</div>
                <div class="text-white fw-bold fs-4">{{ $firmCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 border-0 filter-studio">
    <div class="card-body p-4 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="mb-0 fw-bold">Filter Workspace</h6>
                <p class="mb-0 small text-muted">Refine courses by type, category, status, and search keywords.</p>
            </div>
            <span class="filter-chip"><i class="bi bi-sliders me-1"></i> Precision Filters</span>
        </div>
        <form method="GET" action="{{ route('college.courses.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="Title, mentor, category, venue...">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Category</label>
                    <select name="category_id_filter" class="form-select filter-control">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id_filter') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Type</label>
                    <select name="course_type_filter" class="form-select filter-control">
                        <option value="">All Types</option>
                        <option value="student_only" {{ request('course_type_filter') === 'student_only' ? 'selected' : '' }}>Student Only</option>
                        <option value="firm_only" {{ request('course_type_filter') === 'firm_only' ? 'selected' : '' }}>Firm Only</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Status</label>
                    <select name="status_filter" class="form-select filter-control">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status_filter') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status_filter') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Sort</label>
                    <select name="sort" class="form-select filter-control">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Most Recent</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="seats_desc" {{ request('sort') === 'seats_desc' ? 'selected' : '' }}>Seats: High to Low</option>
                    </select>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                    <button type="submit" class="btn btn-primary px-4 filter-btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="{{ route('college.courses.index') }}" class="btn btn-outline-secondary filter-btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4 shadow-sm border-0 course-hub">
    <div class="card-header course-hub-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Manage Courses</h6>
            <p class="mb-0 small text-muted">Browse your course catalog, enrollment capacities, and active schedules.</p>
        </div>
        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">{{ $courses->count() }} Found</span>
    </div>
    <div class="card-body p-4 p-md-4">
        @if($courses->isEmpty())
            <div class="text-center py-5">
                <div class="empty-state-icon mb-2"><i class="bi bi-search"></i></div>
                <h6 class="fw-bold mb-1">No courses match the current filters</h6>
                <p class="text-muted mb-3">Try resetting filters or adding a new course.</p>
                <a href="{{ route('college.courses.index') }}" class="btn btn-outline-secondary me-2">Reset Filters</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="bi bi-plus-lg"></i> Add New Course
                </button>
            </div>
        @else
            <div class="row g-4">
                @foreach($courses as $course)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="course-card h-100 d-flex flex-column">
                            <div class="course-card-image-wrapper position-relative">
                                <img src="{{ $course->course_image ? asset('storage/' . $course->course_image) : 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=600&auto=format&fit=crop' }}" 
                                     class="course-card-image" alt="{{ $course->title }}">
                                
                                <div class="course-card-badges position-absolute top-0 start-0 p-3 d-flex flex-column gap-2">
                                    <span class="badge bg-blur text-white shadow-sm border border-white-50">
                                        {{ $course->course_type === 'student_only' ? '🎓 Student' : '🏢 Firm' }}
                                    </span>
                                    @if($course->is_certified)
                                        <span class="badge bg-success-blur text-white shadow-sm border border-success-50">
                                            🏆 Certified
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="position-absolute top-0 end-0 p-3">
                                    @if($course->status === 'active')
                                        <span class="badge bg-success shadow-sm">Active</span>
                                    @else
                                        <span class="badge bg-secondary shadow-sm">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="course-card-body p-4 d-flex flex-column flex-grow-1">
                                <div class="text-primary small fw-semibold mb-1 text-uppercase tracking-wider">
                                    {{ $course->category->name ?? 'Uncategorized' }}
                                </div>
                                <h5 class="fw-bold course-card-title mb-2 text-dark text-truncate" title="{{ $course->title }}">{{ $course->title }}</h5>
                                <p class="text-muted small course-card-desc mb-3 flex-grow-1">
                                    {{ \Illuminate\Support\Str::limit($course->description, 120, '...') ?: 'No description provided.' }}
                                </p>
                                
                                <hr class="my-3 text-muted opacity-25">
                                
                                <div class="course-card-details d-grid gap-2 mb-3">
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="bi bi-person-badge me-2 text-primary"></i>
                                        <span class="text-truncate">
                                            <strong>Mentor:</strong> 

                                            @if($course->course_type === 'student_only')
                                            {{ $course->mentor->user->name ?? 'Not Assigned' }}
                                            @else
                                            Not applicable for Firm Courses
                                            @endif
                                        </span>
                                    </div>
                                    
                                    @if($course->course_type === 'student_only')
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="bi bi-people me-2 text-primary"></i>
                                            <span>
                                                <strong>Seats:</strong> {{ $course->available_seats }} / {{ $course->total_seats }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="bi bi-calendar-event me-2 text-primary"></i>
                                            <span class="text-truncate">
                                                <strong>Schedule:</strong> {{ $course->start_date ? \Carbon\Carbon::parse($course->start_date)->format('M d') : 'TBD' }} - {{ $course->end_date ? \Carbon\Carbon::parse($course->end_date)->format('M d') : 'TBD' }}
                                            </span>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center text-secondary small">
                                            <i class="bi bi-info-circle me-2 text-primary"></i>
                                            <span>Logistics set by booking firm</span>
                                        </div>
                                    @endif
                                    
                                    @if($course->course_type === 'student_only')
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="bi bi-geo-alt me-2 text-primary"></i>
                                        <span class="text-truncate">
                                            <strong>Venue:</strong> {{ $course->venue ?: 'TBD' }}
                                        </span>
                                    </div>
                                    @else
                                    <div class="d-flex align-items-center text-secondary small">
                                        <i class="bi bi-geo-alt me-2 text-primary"></i>
                                        <span class="text-truncate">
                                            <strong>Venue:</strong> Set by the firm
                                        </span>
                                    </div>
                                    @endif
                                </div>
                                
                                <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top">
                                    <div class="course-card-price">
                                        <span class="text-muted small d-block" style="font-size: 0.72rem;">Price</span>
                                        <div class="fw-bold text-dark fs-6">
                                            @if($course->price > 0)
                                                ₹{{ number_format($course->price, 2) }}
                                            @else
                                                <span class="text-success fw-bold">Free</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="btn-group action-group gap-1">
                                        <a href="{{ route('college.courses.show', array_merge([$course->id], $queryParams)) }}" class="btn btn-sm btn-outline-primary" title="View Course">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('college.courses.edit', array_merge([$course->id], $queryParams)) }}" class="btn btn-sm btn-outline-warning" title="Edit Course">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="{{ route('college.courses.edit', array_merge([$course->id], $queryParams, ['mode' => 'delete'])) }}" class="btn btn-sm btn-outline-danger" title="Delete Course">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
                {{ $isEdit ? 'Edit Course: ' . $editCourse->title : ($isView ? 'Course: ' . $editCourse->title : 'Add New Course') }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ isset($editCourse) ? route('college.courses.update', array_merge([$editCourse->id], $queryParams)) : route('college.courses.store', $queryParams) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($editCourse))
                @method('PATCH')
            @endif
            <div class="modal-body">
                @if(!$isCreate)
                    <div class="text-center mb-4">
                        <img src="{{ $editCourse->course_image ? asset('storage/' . $editCourse->course_image) : 'https://ui-avatars.com/api/?name=' . urlencode($editCourse->title) }}"
                             class="rounded img-thumbnail shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;" alt="Course Image">
                        @if($isView)
                            <h4 class="mt-2">{{ $editCourse->title }}</h4>
                        @endif
                    </div>
                @endif

                <div class="row g-3">
                    <h6 class="border-bottom pb-2">Course Information</h6>

                    <div class="col-md-6">
                        <label class="form-label">Course Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $editCourse->title ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Course Category</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $editCourse->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Assign Mentor (Optional)</label>
                        <select name="mentor_id" id="mentor_id" class="form-select @error('mentor_id') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="">No Mentor</option>
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}" data-college-id="{{ $mentor->college_id }}" {{ old('mentor_id', $editCourse->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->user->name ?? 'Unknown Mentor' }} - {{ $mentor->college->institution_name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Course Type</label>
                        <select name="course_type" id="course_type" class="form-select @error('course_type') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="student_only" {{ old('course_type', $editCourse->course_type ?? 'student_only') == 'student_only' ? 'selected' : '' }}>Student Only</option>
                            <option value="firm_only" {{ old('course_type', $editCourse->course_type ?? '') == 'firm_only' ? 'selected' : '' }}>Firm Only</option>
                        </select>
                        @error('course_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="firmOnlyInfo" class="col-md-12 d-none">
                        <div class="alert alert-info py-2 mb-0">
                            For <strong>Firm Only</strong> courses, seat count, schedule, time, and venue are set by the firm during booking.
                        </div>
                    </div>

                    <div id="collegeFirmDurationWrapper" class="col-md-4 d-none">
                    <label class="form-label">Firm Duration (days)</label>
                    <input type="number" step="1" min="1" name="firm_duration" class="form-control @error('firm_duration') is-invalid @enderror" value="{{ old('firm_duration', $editCourse->firm_duration ?? 1) }}" {{ $isView ? 'disabled' : '' }}>
                    @error('firm_duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $editCourse->price ?? 0) }}" {{ $isView ? 'disabled' : '' }}>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Total Seats</label>
                        <input type="number" min="1" name="total_seats" id="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', $editCourse->total_seats ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('total_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', isset($editCourse) && $editCourse->start_date ? \Carbon\Carbon::parse($editCourse->start_date)->format('Y-m-d') : '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', isset($editCourse) && $editCourse->end_date ? \Carbon\Carbon::parse($editCourse->end_date)->format('Y-m-d') : '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Time Slot</label>
                        <input type="text" name="time_slot" id="time_slot" class="form-control @error('time_slot') is-invalid @enderror" value="{{ old('time_slot', $editCourse->time_slot ?? '') }}" placeholder="10:00 AM - 01:00 PM" {{ $isView ? 'disabled' : '' }}>
                        @error('time_slot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 logistics-field">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" id="venue" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue', $editCourse->venue ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('venue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $editCourse->status ?? 'inactive') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $editCourse->status ?? 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_certified" id="is_certified" value="1" {{ old('is_certified', $editCourse->is_certified ?? false) ? 'checked' : '' }} {{ $isView ? 'disabled' : '' }}>
                            <label class="form-check-label" for="is_certified">Certified Course</label>
                        </div>
                    </div>

                    @if(!$isView)
                    <div class="col-md-6">
                        <label class="form-label">Course Image</label>
                        <input type="file" name="course_image" class="form-control @error('course_image') is-invalid @enderror">
                        @error('course_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" {{ $isView ? 'disabled' : '' }}>{{ old('description', $editCourse->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="border-bottom pb-2 mb-0">Course Sections</h6>
                            @unless($isView)
                                <button type="button" class="btn btn-outline-primary btn-sm" id="college-add-course-section">
                                    <i class="bi bi-plus-lg"></i> Add Section
                                </button>
                            @endunless
                        </div>

                        @if($isView)
                            <div class="d-grid gap-3">
                                @forelse($sectionRows as $index => $sectionRow)
                                    <div class="border rounded-3 p-3 bg-light">
                                        <div class="fw-semibold mb-1">Section {{ $index + 1 }}: {{ $sectionRow['heading'] ?? 'Untitled Section' }}</div>
                                        <div class="text-secondary small" style="white-space: pre-wrap;">{{ $sectionRow['content'] ?? '' }}</div>
                                    </div>
                                @empty
                                    <div class="text-muted">No sections added yet.</div>
                                @endforelse
                            </div>
                        @else
                            <div id="college-course-sections-list" class="d-grid gap-3">
                                @foreach($sectionRows as $index => $sectionRow)
                                    <div class="border rounded-3 p-3 bg-white" data-college-section-row>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="fw-semibold">Section <span data-college-section-order>{{ $index + 1 }}</span></div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-remove-college-section>
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <input type="hidden" value="{{ $sectionRow['id'] ?? '' }}" data-college-section-field="id">

                                        <div class="mb-3">
                                            <label class="form-label">Heading</label>
                                            <input type="text" class="form-control" value="{{ $sectionRow['heading'] ?? '' }}" data-college-section-field="heading" placeholder="Section heading">
                                        </div>

                                        <div>
                                            <label class="form-label">Content</label>
                                            <textarea class="form-control" rows="3" data-college-section-field="content" placeholder="Section content">{{ $sectionRow['content'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <template id="college-course-section-template">
                                <div class="border rounded-3 p-3 bg-white" data-college-section-row>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="fw-semibold">Section <span data-college-section-order></span></div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove-college-section>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <input type="hidden" value="" data-college-section-field="id">

                                    <div class="mb-3">
                                        <label class="form-label">Heading</label>
                                        <input type="text" class="form-control" value="" data-college-section-field="heading" placeholder="Section heading">
                                    </div>

                                    <div>
                                        <label class="form-label">Content</label>
                                        <textarea class="form-control" rows="3" data-college-section-field="content" placeholder="Section content"></textarea>
                                    </div>
                                </div>
                            </template>
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @if(isset($editCourse))
                    <a href="{{ $indexUrl }}" class="btn btn-secondary">Cancel</a>
                @else
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @endif

                @if(!$isView)
                    <button type="submit" class="btn btn-primary">
                        {{ isset($editCourse) ? 'Update Course' : 'Create Course' }}
                    </button>
                @endif
            </div>
        </form>
    </div></div>
</div>

@if(isset($deleteCourse) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Course</h5>
                    <a href="{{ $indexUrl }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteCourse->title ?? 'Unknown Course' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('college.courses.destroy', array_merge([$deleteCourse->id], $queryParams)) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            var myModalDelete = document.getElementById('deleteModal');
            myModalDelete.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit')) {
                    window.location.href = "{{ $indexUrl }}";
                }
            });
        });
    </script>
@endif

<x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addCourseModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif

@if(isset($editCourse))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            editModal.show();

            var myModalElement = document.getElementById('addCourseModal');
            myModalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ $indexUrl }}";
                }
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var isViewMode = @json($isView);
        var collegeSelect = document.getElementById('college_id');
        var mentorSelect = document.getElementById('mentor_id');
        var courseTypeSelect = document.getElementById('course_type');
        var firmOnlyInfo = document.getElementById('firmOnlyInfo');
        var logisticsFields = document.querySelectorAll('.logistics-field input');
        var sectionsList = document.getElementById('college-course-sections-list');
        var addSectionButton = document.getElementById('college-add-course-section');
        var sectionTemplate = document.getElementById('college-course-section-template');

        var collegeFirmDurationWrapper = document.getElementById('collegeFirmDurationWrapper');

        function filterMentorsByCollege() {
            if (!collegeSelect || !mentorSelect || mentorSelect.disabled) {
                return;
            }

            var selectedCollegeId = collegeSelect.value;
            var mentorOptions = mentorSelect.querySelectorAll('option[data-college-id]');

            mentorOptions.forEach(function (option) {
                var mentorCollegeId = option.getAttribute('data-college-id');
                var shouldShow = !selectedCollegeId || mentorCollegeId === selectedCollegeId;

                option.hidden = !shouldShow;
                if (!shouldShow && option.selected) {
                    option.selected = false;
                }
            });
        }

        if (collegeSelect && mentorSelect) {
            filterMentorsByCollege();
            collegeSelect.addEventListener('change', filterMentorsByCollege);
        }

        function toggleFirmOnlyFields() {
            if (!courseTypeSelect) {
                return;
            }

            var isFirmOnly = courseTypeSelect.value === 'firm_only';

            if (firmOnlyInfo) {
                firmOnlyInfo.classList.toggle('d-none', !isFirmOnly);
            }
            if (collegeFirmDurationWrapper) {
                collegeFirmDurationWrapper.classList.toggle('d-none', !isFirmOnly);
            }

            if (!logisticsFields.length) {
                return;
            }

            logisticsFields.forEach(function (field) {
                if (field.closest('.logistics-field')) {
                    field.disabled = isViewMode || isFirmOnly;
                }
            });
        }

        if (courseTypeSelect) {
            toggleFirmOnlyFields();
            courseTypeSelect.addEventListener('change', toggleFirmOnlyFields);
        }

        function renumberSections() {
            if (!sectionsList) {
                return;
            }

            var rows = sectionsList.querySelectorAll('[data-college-section-row]');

            rows.forEach(function (row, index) {
                var numberNode = row.querySelector('[data-college-section-order]');
                var idField = row.querySelector('[data-college-section-field="id"]');
                var headingField = row.querySelector('[data-college-section-field="heading"]');
                var contentField = row.querySelector('[data-college-section-field="content"]');

                if (numberNode) {
                    numberNode.textContent = index + 1;
                }

                if (idField) {
                    idField.name = 'sections[' + index + '][id]';
                }

                if (headingField) {
                    headingField.name = 'sections[' + index + '][heading]';
                }

                if (contentField) {
                    contentField.name = 'sections[' + index + '][content]';
                }
            });
        }

        function bindSectionRow(row) {
            var removeButton = row.querySelector('[data-remove-college-section]');

            if (removeButton) {
                removeButton.addEventListener('click', function () {
                    row.remove();

                    if (!sectionsList.querySelectorAll('[data-college-section-row]').length && sectionTemplate) {
                        sectionsList.appendChild(sectionTemplate.content.cloneNode(true));
                        bindAllSectionRows();
                    }

                    renumberSections();
                });
            }
        }

        function bindAllSectionRows() {
            if (!sectionsList) {
                return;
            }

            sectionsList.querySelectorAll('[data-college-section-row]').forEach(function (row) {
                bindSectionRow(row);
            });
        }

        if (addSectionButton && sectionsList && sectionTemplate) {
            addSectionButton.addEventListener('click', function () {
                sectionsList.appendChild(sectionTemplate.content.cloneNode(true));
                bindAllSectionRows();
                renumberSections();
            });
        }

        bindAllSectionRows();
        renumberSections();


            const params = new URLSearchParams(window.location.search);
    if (params.get('open') === 'add') {
  bootstrap.Modal.getOrCreateInstance(document.getElementById('addCourseModal')).show();
}
    });
</script>

<style>
    .course-hero {
        border-radius: 1.25rem;
        background: linear-gradient(130deg, #4f46e5 0%, #6366f1 55%, #a855f7 100%);
        box-shadow: 0 14px 35px rgba(79, 70, 229, 0.26);
    }

    .hero-stat {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
    }

    .filter-studio {
        border-radius: 1.1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 22px rgba(16, 24, 40, 0.08);
    }

    .filter-chip {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #4f46e5;
        background: #e0e7ff;
        border: 1px solid #c7d2fe;
        border-radius: 999px;
        padding: 0.38rem 0.72rem;
    }

    .filter-control {
        border-radius: 0.72rem;
        border-color: #d0d5dd;
    }

    .filter-control:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.16);
    }

    .filter-btn-primary,
    .filter-btn-reset {
        border-radius: 0.72rem;
        font-weight: 600;
    }

    .course-hub {
        border-radius: 1.1rem;
        overflow: hidden;
    }

    .course-hub-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e4e7ec;
    }

    .course-card {
        background: #ffffff;
        border: 1px solid #eaecf0;
        border-radius: 1rem;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 24px -4px rgba(16, 24, 40, 0.08), 0 8px 8px -4px rgba(16, 24, 40, 0.03);
        border-color: #d0d5dd;
    }

    .course-card-image-wrapper {
        height: 180px;
        overflow: hidden;
        background: #f2f4f7;
    }

    .course-card-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .course-card:hover .course-card-image {
        transform: scale(1.05);
    }

    .bg-blur {
        backdrop-filter: blur(8px);
        background: rgba(15, 23, 42, 0.6);
        font-size: 0.72rem;
        font-weight: 600;
    }

    .bg-success-blur {
        backdrop-filter: blur(8px);
        background: rgba(22, 163, 74, 0.6);
        font-size: 0.72rem;
        font-weight: 600;
    }

    .course-card-title {
        font-size: 1.15rem;
        line-height: 1.4;
    }

    .course-card-desc {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.5;
        height: 4.5em;
    }

    .course-card-details {
        font-size: 0.85rem;
    }

    .action-group .btn {
        border-radius: 0.65rem;
    }

    .empty-state-icon {
        width: 52px;
        height: 52px;
        margin-inline: auto;
        border-radius: 999px;
        background: #eff6ff;
        color: #2563eb;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
    }

    @media (max-width: 768px) {
        .course-card-image-wrapper {
            height: 150px;
        }
    }
</style>

</x-college.layout>