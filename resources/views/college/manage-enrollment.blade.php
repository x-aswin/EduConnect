<x-college.layout active="enrollments">

@php
    $isEdit = isset($editEnrollment) && !isset($viewOnly);
    $isView = isset($editEnrollment) && isset($viewOnly);
    $totalCount = $enrollments->count();
    $studentCount = $enrollments->where('type', 'student')->count();
    $firmCount = $enrollments->where('type', 'firm')->count();
    $pendingCount = $enrollments->where('status', 'pending')->count();
@endphp

<div class="enrollment-hero p-4 p-md-5 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-white">Enrollment Workspace</h3>
            <p class="mb-0 text-white-50">Track student and firm enrollments with focused filters and split views.</p>
        </div>
        <button type="button" class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
            <i class="bi bi-plus-lg"></i> Add New Enrollment
        </button>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Total</div>
                <div class="text-white fw-bold fs-4">{{ $totalCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Students</div>
                <div class="text-white fw-bold fs-4">{{ $studentCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Firms</div>
                <div class="text-white fw-bold fs-4">{{ $firmCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Pending</div>
                <div class="text-white fw-bold fs-4">{{ $pendingCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 border-0 filter-studio">
    <div class="card-body p-4 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="mb-0 fw-bold">Filter Workspace</h6>
                <p class="mb-0 small text-muted">Refine enrollments by date, course, status, and payment.</p>
            </div>
            <span class="filter-chip"><i class="bi bi-sliders me-1"></i> Smart Filters</span>
        </div>
        <form method="GET" action="{{ route('college.enrollments.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="User, email, course">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Course</label>
                    <select name="course_id" class="form-select filter-control">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (string) request('course_id') === (string) $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Status</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Payment</label>
                    <select name="payment_status" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="na" {{ request('payment_status') === 'na' ? 'selected' : '' }}>N/A</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Sort</label>
                    <select name="sort" class="form-select filter-control">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Most Recent</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Date Range</label>
                    <select name="date_filter" id="date_filter" class="form-select filter-control">
                        <option value="all" {{ request('date_filter', 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="last_7" {{ request('date_filter') === 'last_7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30" {{ request('date_filter') === 'last_30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last_90" {{ request('date_filter') === 'last_90' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <div class="col-md-3 custom-date-field {{ request('date_filter') === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control filter-control">
                </div>

                <div class="col-md-3 custom-date-field {{ request('date_filter') === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control filter-control">
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                    <button type="submit" class="btn btn-primary px-4 filter-btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="{{ route('college.enrollments.index') }}" class="btn btn-outline-secondary filter-btn-reset">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    $studentEnrollments = $enrollments->where('type', 'student')->values();
    $firmEnrollments = $enrollments->where('type', 'firm')->values();
@endphp

<div class="card mt-4 shadow-sm border-0 enrollment-hub">
    <div class="card-header enrollment-hub-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Manage Enrollments</h6>
            <p class="mb-0 small text-muted">Student and firm enrollments are grouped by workflow.</p>
        </div>
        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">{{ $enrollments->count() }} Total</span>
    </div>
    <div class="card-body p-4 p-md-5">
        <ul class="nav nav-pills enrollment-tabs mb-4" id="enrollmentTypeTabs" role="tablist">
            <li class="nav-item me-2" role="presentation">
                <button class="nav-link active enrollment-tab-btn" id="student-tab" data-bs-toggle="pill" data-bs-target="#student-pane" type="button" role="tab" aria-controls="student-pane" aria-selected="true">
                    <i class="bi bi-person me-1"></i> Student Enrollments
                    <span class="badge enrollment-tab-count ms-2">{{ $studentEnrollments->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link enrollment-tab-btn" id="firm-tab" data-bs-toggle="pill" data-bs-target="#firm-pane" type="button" role="tab" aria-controls="firm-pane" aria-selected="false">
                    <i class="bi bi-buildings me-1"></i> Firm Enrollments
                    <span class="badge enrollment-tab-count ms-2">{{ $firmEnrollments->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="enrollmentTypeTabsContent">
            <div class="tab-pane fade show active" id="student-pane" role="tabpanel" aria-labelledby="student-tab" tabindex="0">
                @if($studentEnrollments->isEmpty())
                    <div class="text-center py-5 text-muted">No student enrollments found.</div>
                @else
                    <div class="d-grid gap-3">
                        @foreach($studentEnrollments as $enrollment)
                            <div class="enrollment-item student-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold fs-6">{{ $enrollment->user?->name ?? 'N/A' }}</div>
                                        <div class="text-muted small d-flex align-items-center gap-1">
                                            <i class="bi bi-journal-text"></i> {{ $enrollment->course?->title ?? 'N/A' }}
                                        </div>
                                        <div class="small text-secondary mt-2 d-flex align-items-center gap-1">
                                            <i class="bi bi-clock-history"></i> Created: {{ $enrollment->created_at?->format('d M Y h:i A') }}
                                        </div>
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        @if($enrollment->status === 'confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success">Confirmed</span>
                                        @elseif($enrollment->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                        @endif

                                        @if($enrollment->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border border-success">Paid</span>
                                        @elseif($enrollment->payment_status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Payment Pending</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Payment N/A</span>
                                        @endif

                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            ₹ {{ number_format((float) ($enrollment->total_amount ?? 0), 2) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <div class="btn-group action-group gap-2" role="group">
                                        <a href="{{ route('college.enrollments.show', $enrollment->id) }}">
                                            <button class="btn btn-sm btn-outline-primary" title="View Enrollment">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </a>
                                        <a href="{{ route('college.enrollments.edit', $enrollment->id) }}">
                                            <button class="btn btn-sm btn-outline-warning" title="Edit Enrollment">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </a>
                                        <a href="{{ route('college.enrollments.edit', [$enrollment->id, 'mode' => 'delete']) }}">
                                            <button class="btn btn-sm btn-outline-danger" title="Delete Enrollment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="firm-pane" role="tabpanel" aria-labelledby="firm-tab" tabindex="0">
                @if($firmEnrollments->isEmpty())
                    <div class="text-center py-5 text-muted">No firm enrollments found.</div>
                @else
                    <div class="d-grid gap-3">
                        @foreach($firmEnrollments as $enrollment)
                            <div class="enrollment-item firm-item">
                                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold fs-6">{{ $enrollment->user?->name ?? 'N/A' }}</div>
                                        <div class="text-muted small d-flex align-items-center gap-1">
                                            <i class="bi bi-journal-text"></i> {{ $enrollment->course?->title ?? 'N/A' }}
                                        </div>
                                        <div class="small text-secondary mt-2 d-flex align-items-center gap-1 flex-wrap">
                                            <i class="bi bi-people"></i>
                                            <span>Participants: {{ $enrollment->participants->count() ?: ($enrollment->participant_count ?? 0) }}</span>
                                            @if($enrollment->requested_venue)
                                                <span>· Venue: {{ $enrollment->requested_venue }}</span>
                                            @endif
                                        </div>
                                        @if($enrollment->college_note)
                                            <div class="small text-secondary mt-2 note-chip">
                                                <strong>Note:</strong> {{ $enrollment->college_note }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        @if($enrollment->status === 'confirmed')
                                            <span class="badge bg-success-subtle text-success border border-success">Confirmed</span>
                                        @elseif($enrollment->status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                        @endif

                                        @if($enrollment->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border border-success">Paid</span>
                                        @elseif($enrollment->payment_status === 'pending')
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Payment Pending</span>
                                        @else
                                            <span class="badge bg-light text-dark border">Payment N/A</span>
                                        @endif

                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                            ₹ {{ number_format((float) ($enrollment->total_amount ?? 0), 2) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end mt-3">
                                    <div class="btn-group action-group gap-2" role="group">
                                        <a href="{{ route('college.enrollments.show', $enrollment->id) }}">
                                            <button class="btn btn-sm btn-outline-primary" title="View Enrollment">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </a>
                                        <a href="{{ route('college.enrollments.edit', $enrollment->id) }}">
                                            <button class="btn btn-sm btn-outline-warning" title="Edit Enrollment">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </a>
                                        <a href="{{ route('college.enrollments.edit', [$enrollment->id, 'mode' => 'delete']) }}">
                                            <button class="btn btn-sm btn-outline-danger" title="Delete Enrollment">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Enrollment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('college.enrollments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">User / Firm</label>
                            <select name="user_id" id="enrollment_user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-user-role="{{ $user->role }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select name="course_id" id="enrollment_course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-course-type="{{ $course->course_type }}" data-course-price="{{ $course->price ?? 0 }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                <option value="na" {{ old('payment_status', 'na') === 'na' ? 'selected' : '' }}>N/A</option>
                                <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Participant Count</label>
                            <input type="number" min="1" id="participant_count" class="form-control" value="{{ old('participant_count') }}" readonly>
                            @error('participants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Total Amount</label>
                            <input type="number" min="0" step="0.01" id="total_amount" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" readonly onkeydown="return false" onpaste="return false">
                            <small class="text-muted">Auto-calculated from course price x participant count.</small>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Requested Venue</label>
                            <input type="text" name="requested_venue" class="form-control @error('requested_venue') is-invalid @enderror" value="{{ old('requested_venue') }}">
                            @error('requested_venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Proposed Schedule</label>
                            <input type="datetime-local" name="proposed_schedule" class="form-control @error('proposed_schedule') is-invalid @enderror" value="{{ old('proposed_schedule') }}">
                            @error('proposed_schedule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 firm-only-field d-none">
                            <label class="form-label">College Note</label>
                            <textarea name="college_note" rows="2" class="form-control @error('college_note') is-invalid @enderror" placeholder="Internal note for this firm enrollment">{{ old('college_note') }}</textarea>
                            @error('college_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 firm-only-field d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Firm Participants</label>
                                <button type="button" id="add_participant_btn" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-lg"></i> Add Participant
                                </button>
                            </div>

                            @php
                                $oldParticipants = old('participants', [['name' => '', 'contact_info' => '']]);
                                if (count($oldParticipants) === 0) {
                                    $oldParticipants = [['name' => '', 'contact_info' => '']];
                                }
                            @endphp

                            <div id="participants_container" class="d-grid gap-2">
                                @foreach($oldParticipants as $index => $participant)
                                    <div class="row g-2 participant-row" data-index="{{ $index }}">
                                        <div class="col-md-5">
                                            <input
                                                type="text"
                                                name="participants[{{ $index }}][name]"
                                                class="form-control"
                                                placeholder="Participant Name"
                                                value="{{ $participant['name'] ?? '' }}"
                                            >
                                        </div>
                                        <div class="col-md-5">
                                            <input
                                                type="text"
                                                name="participants[{{ $index }}][contact_info]"
                                                class="form-control"
                                                placeholder="Contact Info (Phone/Email)"
                                                value="{{ $participant['contact_info'] ?? '' }}"
                                            >
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-participant-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('participants')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="manageEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    {{ $isEdit ? 'Edit Enrollment' : ($isView ? 'Enrollment Details' : 'Enrollment') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ isset($editEnrollment) ? route('college.enrollments.update', $editEnrollment->id) : '#' }}" method="POST">
                @csrf
                @if(isset($editEnrollment))
                    @method('PATCH')
                @endif

                <div class="modal-body">
                    @if(isset($editEnrollment))
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">User / Firm</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->user?->name ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->course?->title ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <input type="text" class="form-control" value="{{ ucfirst($editEnrollment->type) }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Participants</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->type === 'firm' ? ($editEnrollment->participants->count() ?: $editEnrollment->participant_count) : '-' }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Total Cost</label>
                                <input type="text" class="form-control" value="{{ !is_null($editEnrollment->total_amount) ? '₹ ' . number_format((float) $editEnrollment->total_amount, 2) : '-' }}" disabled>
                            </div>

                            @if($editEnrollment->type === 'firm')
                                <div class="col-12">
                                    <label class="form-label">College Note</label>
                                    @if($isView)
                                        <textarea class="form-control" rows="2" disabled>{{ $editEnrollment->college_note ?? '' }}</textarea>
                                    @else
                                        <textarea name="college_note" rows="2" class="form-control @error('college_note') is-invalid @enderror" placeholder="Internal note for this firm enrollment">{{ old('college_note', $editEnrollment->college_note) }}</textarea>
                                        @error('college_note')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>

                                <div class="col-12">
                                    @if($isView)
                                        <label class="form-label">Participant Details</label>
                                        @if($editEnrollment->participants->isNotEmpty())
                                            <ul class="list-group">
                                                @foreach($editEnrollment->participants as $participant)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $participant->name }}</span>
                                                        <small class="text-muted">{{ $participant->contact_info }}</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="form-control bg-light">No participant details available.</div>
                                        @endif
                                    @else
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Participant Details</label>
                                            <button type="button" id="edit_add_participant_btn" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-plus-lg"></i> Add Participant
                                            </button>
                                        </div>

                                        @php
                                            $editParticipants = old('participants', $editEnrollment->participants->map(function ($participant) {
                                                return [
                                                    'name' => $participant->name,
                                                    'contact_info' => $participant->contact_info,
                                                ];
                                            })->all());
                                            if (count($editParticipants) === 0) {
                                                $editParticipants = [['name' => '', 'contact_info' => '']];
                                            }
                                        @endphp

                                        <div id="edit_participants_container" class="d-grid gap-2">
                                            @foreach($editParticipants as $index => $participant)
                                                <div class="row g-2 edit-participant-row" data-index="{{ $index }}">
                                                    <div class="col-md-5">
                                                        <input
                                                            type="text"
                                                            name="participants[{{ $index }}][name]"
                                                            class="form-control"
                                                            placeholder="Participant Name"
                                                            value="{{ $participant['name'] ?? '' }}"
                                                        >
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input
                                                            type="text"
                                                            name="participants[{{ $index }}][contact_info]"
                                                            class="form-control"
                                                            placeholder="Contact Info (Phone/Email)"
                                                            value="{{ $participant['contact_info'] ?? '' }}"
                                                        >
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-danger w-100 edit-remove-participant-btn">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @error('participants')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">Enrollment Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="pending" {{ old('status', $editEnrollment->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $editEnrollment->status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="rejected" {{ old('status', $editEnrollment->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="na" {{ old('payment_status', $editEnrollment->payment_status ?? 'na') === 'na' ? 'selected' : '' }}>N/A</option>
                                    <option value="pending" {{ old('payment_status', $editEnrollment->payment_status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $editEnrollment->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    @if(isset($editEnrollment))
                        <a href="{{ route('college.enrollments.index') }}" class="btn btn-secondary">Cancel</a>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @endif

                    @if(!$isView && isset($editEnrollment))
                        <button type="submit" class="btn btn-primary">Update Enrollment</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($deleteEnrollment) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Enrollment</h5>
                    <a href="{{ route('college.enrollments.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>
                        Delete enrollment for
                        <strong>{{ $deleteEnrollment->user?->name ?? 'N/A' }}</strong>
                        in
                        <strong>{{ $deleteEnrollment->course?->title ?? 'N/A' }}</strong>?
                    </p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('college.enrollments.destroy', $deleteEnrollment->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            const deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit')) {
                    window.location.href = "{{ route('college.enrollments.index') }}";
                }
            });
        });
    </script>
@endif

<x-toast />

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editPath = window.location.pathname.includes('/edit') || window.location.pathname.match(/\/\d+$/);
            const targetModal = editPath ? document.getElementById('manageEnrollmentModal') : document.getElementById('addEnrollmentModal');

            if (targetModal) {
                new bootstrap.Modal(targetModal).show();
            }
        });
    </script>
@endif

@if(isset($editEnrollment))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('manageEnrollmentModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            modalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ route('college.enrollments.index') }}";
                }
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userSelect = document.getElementById('enrollment_user_id');
        const courseSelect = document.getElementById('enrollment_course_id');
        const participantCountInput = document.getElementById('participant_count');
        const totalAmountInput = document.getElementById('total_amount');
        const firmFields = document.querySelectorAll('.firm-only-field');
        const participantsContainer = document.getElementById('participants_container');
        const addParticipantBtn = document.getElementById('add_participant_btn');
        const editParticipantsContainer = document.getElementById('edit_participants_container');
        const editAddParticipantBtn = document.getElementById('edit_add_participant_btn');

        let participantIndex = participantsContainer
            ? Math.max(0, ...Array.from(participantsContainer.querySelectorAll('.participant-row')).map(function (row) {
                return parseInt(row.getAttribute('data-index') || '0', 10);
            })) + 1
            : 0;

        let editParticipantIndex = editParticipantsContainer
            ? Math.max(0, ...Array.from(editParticipantsContainer.querySelectorAll('.edit-participant-row')).map(function (row) {
                return parseInt(row.getAttribute('data-index') || '0', 10);
            })) + 1
            : 0;

        function getParticipantRows() {
            if (!participantsContainer) {
                return [];
            }

            return Array.from(participantsContainer.querySelectorAll('.participant-row'));
        }

        function updateParticipantCount() {
            if (!participantCountInput) {
                return;
            }

            participantCountInput.value = getParticipantRows().length || '';
        }

        function buildParticipantRow(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 participant-row';
            wrapper.setAttribute('data-index', index.toString());
            wrapper.innerHTML = [
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][name]" class="form-control" placeholder="Participant Name">',
                '</div>',
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][contact_info]" class="form-control" placeholder="Contact Info (Phone/Email)">',
                '</div>',
                '<div class="col-md-2">',
                '  <button type="button" class="btn btn-outline-danger w-100 remove-participant-btn"><i class="bi bi-trash"></i></button>',
                '</div>'
            ].join('');

            return wrapper;
        }

        function addParticipantRow() {
            if (!participantsContainer) {
                return;
            }

            participantsContainer.appendChild(buildParticipantRow(participantIndex));
            participantIndex += 1;
            updateParticipantCount();
        }

        function getEditParticipantRows() {
            if (!editParticipantsContainer) {
                return [];
            }

            return Array.from(editParticipantsContainer.querySelectorAll('.edit-participant-row'));
        }

        function buildEditParticipantRow(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 edit-participant-row';
            wrapper.setAttribute('data-index', index.toString());
            wrapper.innerHTML = [
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][name]" class="form-control" placeholder="Participant Name" required>',
                '</div>',
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][contact_info]" class="form-control" placeholder="Contact Info (Phone/Email)" required>',
                '</div>',
                '<div class="col-md-2">',
                '  <button type="button" class="btn btn-outline-danger w-100 edit-remove-participant-btn"><i class="bi bi-trash"></i></button>',
                '</div>'
            ].join('');

            return wrapper;
        }

        function addEditParticipantRow() {
            if (!editParticipantsContainer) {
                return;
            }

            editParticipantsContainer.appendChild(buildEditParticipantRow(editParticipantIndex));
            editParticipantIndex += 1;
        }

        function calculateTotalAmount(isFirm) {
            if (!totalAmountInput || !courseSelect) {
                return;
            }

            const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
            const price = selectedCourseOption ? parseFloat(selectedCourseOption.getAttribute('data-course-price') || '0') : 0;

            if (!isFirm) {
                totalAmountInput.value = '';
                return;
            }

            const participantCount = participantCountInput ? parseInt(participantCountInput.value || '0', 10) : 0;
            const total = price * (participantCount > 0 ? participantCount : 0);

            totalAmountInput.value = total > 0 ? total.toFixed(2) : '';
        }

        function applyUserBasedFiltering() {
            if (!userSelect || !courseSelect) {
                return;
            }

            const selectedUserOption = userSelect.options[userSelect.selectedIndex];
            const selectedRole = selectedUserOption ? selectedUserOption.getAttribute('data-user-role') : null;
            const isFirm = selectedRole === 'firm';
            const expectedCourseType = isFirm ? 'firm_only' : 'student_only';

            firmFields.forEach(function (field) {
                field.classList.toggle('d-none', !isFirm);
            });

            getParticipantRows().forEach(function (row) {
                const inputs = row.querySelectorAll('input');
                inputs.forEach(function (input) {
                    input.required = isFirm;
                });
            });

            if (isFirm && getParticipantRows().length === 0) {
                addParticipantRow();
            }

            updateParticipantCount();

            const courseOptions = courseSelect.querySelectorAll('option[data-course-type]');
            let hasSelectedVisibleOption = false;

            courseOptions.forEach(function (option) {
                const matches = option.getAttribute('data-course-type') === expectedCourseType;
                option.hidden = !matches;

                if (!matches && option.selected) {
                    option.selected = false;
                }

                if (matches && option.selected) {
                    hasSelectedVisibleOption = true;
                }
            });

            if (!hasSelectedVisibleOption) {
                const firstVisibleOption = Array.from(courseOptions).find(function (option) {
                    return !option.hidden;
                });

                if (firstVisibleOption) {
                    firstVisibleOption.selected = true;
                }
            }

            calculateTotalAmount(isFirm);
        }

        if (userSelect && courseSelect) {
            applyUserBasedFiltering();
            userSelect.addEventListener('change', applyUserBasedFiltering);
            courseSelect.addEventListener('change', function () {
                const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                calculateTotalAmount(isFirm);
            });
        }

        if (addParticipantBtn) {
            addParticipantBtn.addEventListener('click', function () {
                addParticipantRow();

                if (!userSelect) {
                    return;
                }

                const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                calculateTotalAmount(isFirm);
            });
        }

        if (participantsContainer) {
            participantsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.remove-participant-btn');
                if (!removeButton) {
                    return;
                }

                const rows = getParticipantRows();
                if (rows.length <= 1) {
                    return;
                }

                const row = removeButton.closest('.participant-row');
                if (row) {
                    row.remove();
                    updateParticipantCount();

                    if (!userSelect) {
                        return;
                    }

                    const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                    const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                    calculateTotalAmount(isFirm);
                }
            });

            updateParticipantCount();
        }

        if (editAddParticipantBtn) {
            editAddParticipantBtn.addEventListener('click', function () {
                addEditParticipantRow();
            });
        }

        if (editParticipantsContainer) {
            editParticipantsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.edit-remove-participant-btn');
                if (!removeButton) {
                    return;
                }

                const rows = getEditParticipantRows();
                if (rows.length <= 1) {
                    return;
                }

                const row = removeButton.closest('.edit-participant-row');
                if (row) {
                    row.remove();
                }
            });
        }
    });
</script>

<style>
    .enrollment-hero {
        background: linear-gradient(120deg, #0f4c81 0%, #13678a 55%, #4db6ac 100%);
        border-radius: 20px;
        box-shadow: 0 14px 35px -14px rgba(13, 58, 96, 0.6);
    }

    .hero-stat {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .filter-studio {
        border-radius: 18px;
        overflow: hidden;
        border: 1px solid #e9eff7;
        box-shadow: 0 16px 28px -24px rgba(16, 63, 104, 0.8);
    }

    .filter-studio .card-body {
        background: linear-gradient(180deg, #fbfdff 0%, #f5f9ff 100%);
    }

    .filter-chip {
        display: inline-flex;
        align-items: center;
        background: #e9f3ff;
        color: #0c4e86;
        border: 1px solid #d0e4fb;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        padding: 0.35rem 0.75rem;
    }

    .filter-control {
        background: #ffffff;
        border: 1px solid #d5e2f0;
        border-radius: 12px;
        min-height: 42px;
        box-shadow: 0 1px 0 rgba(255, 255, 255, 0.7) inset;
        transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
    }

    .filter-control:focus {
        border-color: #4f96de;
        box-shadow: 0 0 0 0.2rem rgba(79, 150, 222, 0.2);
        transform: translateY(-1px);
    }

    .filter-btn-primary {
        border-radius: 12px;
        font-weight: 600;
        box-shadow: 0 10px 16px -12px rgba(17, 106, 185, 0.9);
    }

    .filter-btn-reset {
        border-radius: 12px;
        font-weight: 600;
    }

    .enrollment-hub {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 16px 32px -24px rgba(13, 58, 96, 0.7);
    }

    .enrollment-hub-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border-bottom: 1px solid #e5edf7;
    }

    .enrollment-tabs {
        gap: 0.75rem;
        border-bottom: 1px solid #edf2f8;
        padding-bottom: 1rem;
    }

    .enrollment-tab-btn {
        border-radius: 12px;
        border: 1px solid #dfe8f3;
        color: #37506b;
        background: #f7fafe;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .enrollment-tab-btn:hover {
        border-color: #b8cbe2;
        color: #0b4f84;
        background: #edf5ff;
    }

    .enrollment-tab-btn.active {
        background: linear-gradient(135deg, #0f72cf 0%, #0d5ca9 100%) !important;
        border-color: #0d5ca9 !important;
        color: #fff !important;
        box-shadow: 0 10px 16px -12px rgba(13, 92, 169, 0.9);
    }

    .enrollment-tab-count {
        background: rgba(255, 255, 255, 0.92);
        color: #17486f;
        border-radius: 999px;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
    }

    .enrollment-item {
        background: #ffffff;
        border: 1px solid #e8eef6;
        border-radius: 14px;
        padding: 1rem 1.1rem;
        box-shadow: 0 8px 18px -16px rgba(15, 47, 77, 0.7);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .enrollment-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 24px -18px rgba(15, 47, 77, 0.75);
    }

    .student-item {
        border-left: 4px solid #2b78e4;
    }

    .firm-item {
        border-left: 4px solid #1f9d8a;
    }

    .note-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: #f4f8ff;
        border: 1px solid #dfe9f8;
        border-radius: 10px;
        padding: 0.35rem 0.6rem;
    }

    .action-group .btn {
        border-radius: 10px !important;
        min-width: 34px;
    }

    @media (max-width: 768px) {
        .filter-studio .card-body {
            padding: 1rem !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateFilter = document.getElementById('date_filter');
        const customFields = document.querySelectorAll('.custom-date-field');

        if (!dateFilter) {
            return;
        }

        const toggleCustomFields = function () {
            const isCustom = dateFilter.value === 'custom';
            customFields.forEach(function (field) {
                field.classList.toggle('d-none', !isCustom);
            });
        };

        dateFilter.addEventListener('change', toggleCustomFields);
        toggleCustomFields();
    });
</script>

</x-college.layout>
