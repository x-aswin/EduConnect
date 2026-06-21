<x-college.layout active="certificates">

@php
    $queryParams = collect(request()->query())->except('mode')->toArray();
    $indexUrl = route('college.certificate', $queryParams);
@endphp

<div class="enrollment-hero p-4 p-md-5 mb-4 rounded-4" style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-white">Certificate Management</h3>
            <p class="mb-0 text-white-50">Manage signatories and issue completion certificates for your courses.</p>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <div class="text-white-50 small">Eligible Courses</div>
                <div class="text-white fw-bold fs-4">{{ $totalCourses }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <div class="text-white-50 small">Ended Courses</div>
                <div class="text-white fw-bold fs-4">{{ $endedCourses }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <div class="text-white-50 small">Confirmed Learners</div>
                <div class="text-white fw-bold fs-4">{{ $totalConfirmedEnrollments }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">
                <div class="text-white-50 small">Certificates Issued</div>
                <div class="text-white fw-bold fs-4">{{ $totalIssuedCertificates }}</div>
            </div>
        </div>
    </div>
</div>

<x-toast />

<div class="card shadow-sm mb-4 border-0 filter-studio">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="mb-0 fw-bold">Filter Workspace</h6>
                <p class="mb-0 small text-muted">Refine certified courses by title, signatories status, and timeline.</p>
            </div>
            <span class="filter-chip"><i class="bi bi-funnel me-1"></i> Precision Filters</span>
        </div>
        <form method="GET" action="{{ route('college.certificate') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Search Course</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="Search by course title...">
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Signatories</label>
                    <select name="signatories_status" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="configured" {{ request('signatories_status') === 'configured' ? 'selected' : '' }}>Configured</option>
                        <option value="pending" {{ request('signatories_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Status</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                        <option value="ended" {{ request('status') === 'ended' ? 'selected' : '' }}>Ended</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Sort</label>
                    <select name="sort" class="form-select filter-control">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Most Recent</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title A-Z</option>
                        <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title Z-A</option>
                    </select>
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                    <button type="submit" class="btn btn-primary px-4 filter-btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="{{ route('college.certificate') }}" class="btn btn-outline-secondary filter-btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 enrollment-hub">
    <div class="card-header enrollment-hub-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white border-bottom">
        <div>
            <h6 class="mb-0 fw-bold">Course Certificates</h6>
            <p class="mb-0 small text-muted">Select a course to set up signatories and issue certificates to learners.</p>
        </div>
        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">{{ $courses->count() }} Found</span>
    </div>
    
    <div class="card-body p-4 p-md-5">
        @if($courses->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-award fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                No certified courses match your criteria.
            </div>
        @else
            <div class="d-grid gap-3">
                @foreach($courses as $course)
                    @php
                        $isFirmCourse = $course->course_type === 'firm_only';
                        $isEnded = $course->end_date ? \Carbon\Carbon::parse($course->end_date)->isPast() : false;
                        $confirmedCount = $course->enrollments->count();
                        $issuedCount = $course->enrollments->where('certificate_issued', true)->count();
                        $allIssued = $confirmedCount > 0 && $issuedCount === $confirmedCount;
                        // Existing signatories for this course (used in data attributes)
                        $sig1 = $course->signatories->where('display_order', 1)->first();
                        $sig2 = $course->signatories->where('display_order', 2)->first();
                    @endphp

                    <div class="enrollment-item border rounded-3 p-3 p-md-4 bg-light">
                        {{-- Course Header --}}
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold fs-5 mb-1">
                                    {{ $course->title }}
                                    @if($isFirmCourse)
                                        <span class="badge bg-info-subtle text-info border border-info ms-2" style="font-size:0.7rem;">Firm Course</span>
                                    @endif
                                </div>

                                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                                    @if($course->end_date)
                                        <span class="d-flex align-items-center gap-1">
                                            <i class="bi bi-calendar-event"></i>
                                            Ends: {{ \Carbon\Carbon::parse($course->end_date)->format('d M Y') }}
                                        </span>
                                        @if($isEnded)
                                            <span class="badge bg-danger-subtle text-danger border border-danger">Ended</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success">Ongoing</span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary">Open / On-demand</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 align-items-center bg-white p-2 px-3 rounded border">
                                <div class="text-center">
                                    <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Bookings</div>
                                    <div class="fw-bold fs-5">{{ $confirmedCount }}</div>
                                </div>
                                <div class="vr"></div>
                                <div class="text-center">
                                    <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Issued</div>
                                    <div class="fw-bold fs-5 text-primary">{{ $issuedCount }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Signatory status + action --}}
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <div class="small text-secondary">
                                @if($course->signatories->count() > 0)
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Signatories configured
                                @else
                                    <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Signatories pending
                                @endif
                            </div>

                            @if(!$isFirmCourse)
                                {{-- Student course: bulk issue button --}}
                                <div class="btn-group gap-2">
                                    <a href="{{ route('college.certificates.edit', array_merge([$course->id], $queryParams)) }}" class="btn btn-primary rounded-pill px-4">
                                        @if($allIssued)
                                            <i class="bi bi-pencil-square me-1"></i> Update Signatories
                                        @else
                                            <i class="bi bi-award me-1"></i> Provide Certificates
                                        @endif
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Firm-only: expand each enrollment as its own actionable sub-row --}}
                        @if($isFirmCourse && $confirmedCount > 0)
                            <div class="mt-3 d-grid gap-2">
                                @foreach($course->enrollments as $enrollment)
                                    @php
                                        $firmName = $enrollment->user?->firm?->org_name ?? $enrollment->user?->name ?? 'Unknown Firm';
                                        $enrollStart = $enrollment->proposed_start ? \Carbon\Carbon::parse($enrollment->proposed_start)->format('d M Y') : '—';
                                        $enrollEnd   = $enrollment->proposed_end   ? \Carbon\Carbon::parse($enrollment->proposed_end)->format('d M Y')   : '—';
                                        $enrollVenue = $enrollment->requested_venue ?? '—';
                                    @endphp
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 rounded-3 px-3 py-2 border bg-white">
                                        <div>
                                            <div class="fw-semibold small">{{ $firmName }}</div>
                                            <div class="text-muted" style="font-size:0.78rem;">
                                                <i class="bi bi-calendar2-range me-1"></i>{{ $enrollStart }} – {{ $enrollEnd }}
                                                &nbsp;·&nbsp;
                                                <i class="bi bi-geo-alt me-1"></i>{{ $enrollVenue }}
                                                @if($enrollment->participant_count)
                                                    &nbsp;·&nbsp;
                                                    <i class="bi bi-people me-1"></i>{{ $enrollment->participant_count }} participants
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($enrollment->certificate_issued)
                                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                                    <i class="bi bi-patch-check-fill me-1"></i>Certificate Issued
                                                </span>
                                            @else
                                                <button type="button"
                                                    class="btn btn-sm btn-primary rounded-pill px-3 firm-issue-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#firmIssueModal"
                                                    data-action="{{ route('college.certificates.issue.enrollment', [$course->id, $enrollment->id]) }}"
                                                    data-course-title="{{ $course->title }}"
                                                    data-firm-name="{{ $firmName }}"
                                                    data-sig1-name="{{ $sig1?->name }}"
                                                    data-sig1-designation="{{ $sig1?->designation }}"
                                                    data-sig1-existing="{{ $sig1?->signature_image }}"
                                                    data-sig1-image="{{ $sig1?->signature_image ? asset('storage/' . $sig1->signature_image) : '' }}"
                                                    data-sig2-name="{{ $sig2?->name }}"
                                                    data-sig2-designation="{{ $sig2?->designation }}"
                                                    data-sig2-existing="{{ $sig2?->signature_image }}"
                                                    data-sig2-image="{{ $sig2?->signature_image ? asset('storage/' . $sig2->signature_image) : '' }}"
                                                    data-start="{{ $enrollStart }}"
                                                    data-end="{{ $enrollEnd }}"
                                                    data-venue="{{ $enrollVenue }}">
                                                    <i class="bi bi-award me-1"></i> Issue Certificate
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($isFirmCourse && $confirmedCount === 0)
                            <div class="mt-3 text-muted small text-center py-2 border-top">
                                <i class="bi bi-info-circle me-1"></i> No confirmed enrollments yet.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Single Firm Issue Modal (reused for all enrollments) --}}
<div class="modal fade" id="firmIssueModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="firmIssueModalTitle">
                    <i class="bi bi-award me-2"></i>Issue Certificate
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="firmIssueForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle-fill fs-4 flex-shrink-0"></i>
                        <div id="firmIssueAlertText"></div>
                    </div>

                    <h6 class="fw-bold mb-3 border-bottom pb-2">Certificate Signatories</h6>
                    <p class="small text-muted mb-4">Add up to 2 authorized signatories to appear on the generated certificate.</p>

                    <div class="row g-4">
                        @for ($i = 0; $i < 2; $i++)
                            <div class="col-md-6">
                                <div class="card border border-2 shadow-sm h-100">
                                    <div class="card-header bg-light fw-semibold text-secondary">
                                        Signatory {{ $i + 1 }} {{ $i === 0 ? '(Required)' : '(Optional)' }}
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Name</label>
                                            <input type="text" name="signatories[{{ $i }}][name]" 
                                                   id="firm_signatory_name_{{ $i }}"
                                                   class="form-control" 
                                                   placeholder="e.g. Dr. John Doe" 
                                                   {{ $i === 0 ? 'required' : '' }}>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Designation</label>
                                            <input type="text" name="signatories[{{ $i }}][designation]" 
                                                   id="firm_signatory_designation_{{ $i }}"
                                                   class="form-control" 
                                                   placeholder="e.g. Principal" 
                                                   {{ $i === 0 ? 'required' : '' }}>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small fw-semibold">Signature Image</label>
                                            <input type="file" name="signatories[{{ $i }}][signature_image]" 
                                                   class="form-control form-control-sm" 
                                                   accept="image/png, image/jpeg">
                                            <div class="form-text" style="font-size: 0.75rem;">Transparent PNG recommended. Max 1MB.</div>
                                        </div>
                                        <div id="firm_existing_sig_{{ $i }}" class="mt-2 bg-light p-2 rounded text-center border d-none">
                                            <span class="d-block small text-muted mb-1">Current Signature:</span>
                                            <img src="" alt="Signature" height="40" class="object-fit-contain" id="firm_sig_img_{{ $i }}">
                                            <input type="hidden" name="signatories[{{ $i }}][existing_image]" id="firm_sig_hidden_{{ $i }}" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send-check me-1"></i> Save & Issue Certificate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Student course modal (unchanged) --}}
@if(isset($editCourse))
    @php
        $confirmedCount = $editCourse->enrollments->count();
        $issuedCount = $editCourse->enrollments->where('certificate_issued', true)->count();
    @endphp
    <div class="modal fade" id="issueModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-award me-2"></i> Issue Certificates: {{ $editCourse->title }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                
                <form action="{{ route('college.certificates.issue', $editCourse) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                Saving will update these signatories and <strong>immediately issue certificates</strong> to all <strong>{{ $confirmedCount - $issuedCount }}</strong> eligible confirmed learners who haven't received one yet.
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3 border-bottom pb-2">Certificate Signatories</h6>
                        <p class="small text-muted mb-4">Add up to 2 authorized signatories (e.g., Principal, Course Mentor) to appear on the generated certificates.</p>

                        <div class="row g-4">
                            @for ($i = 0; $i < 2; $i++)
                                @php
                                    $existing = $editCourse->signatories->where('display_order', $i + 1)->first();
                                @endphp
                                <div class="col-md-6">
                                    <div class="card border border-2 shadow-sm h-100">
                                        <div class="card-header bg-light fw-semibold text-secondary">
                                            Signatory {{ $i + 1 }} {{ $i === 0 ? '(Required)' : '(Optional)' }}
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Name</label>
                                                <input type="text" name="signatories[{{ $i }}][name]" class="form-control" value="{{ old("signatories.$i.name", $existing?->name) }}" placeholder="e.g. Dr. John Doe" {{ $i === 0 ? 'required' : '' }}>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-semibold">Designation</label>
                                                <input type="text" name="signatories[{{ $i }}][designation]" class="form-control" value="{{ old("signatories.$i.designation", $existing?->designation) }}" placeholder="e.g. Principal" {{ $i === 0 ? 'required' : '' }}>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <label class="form-label small fw-semibold">Signature Image</label>
                                                <input type="file" name="signatories[{{ $i }}][signature_image]" class="form-control form-control-sm" accept="image/png, image/jpeg" {{ $i === 0 && !$existing ? 'required' : '' }}>
                                                <div class="form-text" style="font-size: 0.75rem;">Transparent PNG recommended. Max 1MB.</div>
                                            </div>

                                            @if($existing && $existing->signature_image)
                                                <div class="mt-2 bg-light p-2 rounded text-center border">
                                                    <span class="d-block small text-muted mb-1">Current Signature:</span>
                                                    <img src="{{ asset('storage/' . $existing->signature_image) }}" alt="Signature" height="40" class="object-fit-contain">
                                                    <input type="hidden" name="signatories[{{ $i }}][existing_image]" value="{{ $existing->signature_image }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send-check me-1"></i> Save & Issue Certificates
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModal = new bootstrap.Modal(document.getElementById('issueModal'));
            myModal.show();

            document.getElementById('issueModal').addEventListener('hidden.bs.modal', function () {
                window.location.href = "{{ $indexUrl }}";
            });
        });
    </script>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const firmModal = document.getElementById('firmIssueModal');
        const firmForm = document.getElementById('firmIssueForm');
        const alertText = document.getElementById('firmIssueAlertText');
        const modalTitle = document.getElementById('firmIssueModalTitle');

        // Populate modal when any firm issue button is clicked
        firmModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    const action = button.getAttribute('data-action');
    const courseTitle = button.getAttribute('data-course-title');
    const firmName = button.getAttribute('data-firm-name');
    const start = button.getAttribute('data-start');
    const end = button.getAttribute('data-end');
    const venue = button.getAttribute('data-venue');

    firmForm.action = action;
    modalTitle.innerHTML = '<i class="bi bi-award me-2"></i>Issue Certificate — ' + courseTitle;
    alertText.innerHTML =
        'Issuing certificate for <strong>' + firmName + '</strong>\'s booking (' +
        start + ' – ' + end + ', ' + venue + ').<br>Signatories are shared across all bookings for this course.';

    // Pre‑fill signatory fields from button data attributes
    for (let i = 0; i < 2; i++) {
        const idx = i + 1;
        const nameField = document.getElementById('firm_signatory_name_' + i);
        const designationField = document.getElementById('firm_signatory_designation_' + i);
        const existingImageDiv = document.getElementById('firm_existing_sig_' + i);
        const sigImg = document.getElementById('firm_sig_img_' + i);
        const sigHidden = document.getElementById('firm_sig_hidden_' + i);

        const sigName = button.getAttribute('data-sig' + idx + '-name') || '';
        const sigDesignation = button.getAttribute('data-sig' + idx + '-designation') || '';
        const sigImageUrl = button.getAttribute('data-sig' + idx + '-image') || '';
        const sigExisting = button.getAttribute('data-sig' + idx + '-existing') || '';

        if (nameField) nameField.value = sigName;
        if (designationField) designationField.value = sigDesignation;
        if (sigHidden) sigHidden.value = sigExisting;

        if (sigImageUrl) {
            if (sigImg) sigImg.src = sigImageUrl;
            if (existingImageDiv) existingImageDiv.classList.remove('d-none');
        } else {
            if (existingImageDiv) existingImageDiv.classList.add('d-none');
        }
    }
});

        // Reset form fields when modal is closed
        firmModal.addEventListener('hidden.bs.modal', function () {
    firmForm.reset();
    document.querySelectorAll('[id^="firm_existing_sig_"]').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('[id^="firm_sig_img_"]').forEach(img => img.src = '');
});
    });
</script>
@endpush

<style>
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
        color: #0d47a1;
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 999px;
        padding: 0.38rem 0.72rem;
    }
    .filter-control {
        border-radius: 0.72rem;
        border-color: #d0d5dd;
    }
    .filter-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.16);
    }
    .filter-btn-primary,
    .filter-btn-reset {
        border-radius: 0.72rem;
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
    .enrollment-item {
        background: #ffffff;
        border: 1px solid #e8eef6;
        border-radius: 14px;
        padding: 1rem 1.1rem;
        box-shadow: 0 8px 18px -16px rgba(15, 47, 77, 0.7);
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }
    .enrollment-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -12px rgba(15, 47, 77, 0.9);
    }
</style>
</x-college.layout>