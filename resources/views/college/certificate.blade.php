<x-college.layout active="certificates">

@php
    $totalCourses = $courses->count();
    $endedCourses = $courses->filter(fn($c) => \Carbon\Carbon::parse($c->end_date)->isPast())->count();
    $totalConfirmedEnrollments = $courses->sum(fn($c) => $c->enrollments->count());
    $totalIssuedCertificates = $courses->sum(fn($c) => $c->enrollments->where('certificate_issued', true)->count());
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
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                <div class="text-white-50 small">Eligible Courses</div>
                <div class="text-white fw-bold fs-4">{{ $totalCourses }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                <div class="text-white-50 small">Ended Courses</div>
                <div class="text-white fw-bold fs-4">{{ $endedCourses }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                <div class="text-white-50 small">Confirmed Learners</div>
                <div class="text-white fw-bold fs-4">{{ $totalConfirmedEnrollments }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3" style="background: rgba(255,255,255,0.1);">
                <div class="text-white-50 small">Certificates Issued</div>
                <div class="text-white fw-bold fs-4">{{ $totalIssuedCertificates }}</div>
            </div>
        </div>
    </div>
</div>

<x-toast />

<div class="card mt-4 shadow-sm border-0 enrollment-hub">
    <div class="card-header enrollment-hub-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2 bg-white border-bottom">
        <div>
            <h6 class="mb-0 fw-bold">Course Certificates</h6>
            <p class="mb-0 small text-muted">Select a course to set up signatories and issue certificates to learners.</p>
        </div>
    </div>
    
    <div class="card-body p-4 p-md-5">
        @if($courses->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-award fs-1 text-secondary opacity-50 mb-3 d-block"></i>
                No certified courses available yet.
            </div>
        @else
            <div class="d-grid gap-3">
                @foreach($courses as $course)
                    @php
                        $isEnded = \Carbon\Carbon::parse($course->end_date)->isPast();
                        $confirmedCount = $course->enrollments->count();
                        $issuedCount = $course->enrollments->where('certificate_issued', true)->count();
                        $allIssued = $confirmedCount > 0 && $issuedCount === $confirmedCount;
                    @endphp

                    <div class="enrollment-item border rounded-3 p-3 p-md-4 bg-light">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <div class="fw-semibold fs-5 mb-1">{{ $course->title }}</div>
                                
                                <div class="d-flex flex-wrap gap-3 text-muted small mt-2">
                                    <span class="d-flex align-items-center gap-1">
                                        <i class="bi bi-calendar-event"></i> 
                                        Ends: {{ \Carbon\Carbon::parse($course->end_date)->format('d M Y') }}
                                    </span>
                                    
                                    @if($isEnded)
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Ended</span>
                                    @else
                                        <span class="badge bg-success-subtle text-success border border-success">Ongoing</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-3 align-items-center bg-white p-2 px-3 rounded border">
                                <div class="text-center">
                                    <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Confirmed</div>
                                    <div class="fw-bold fs-5">{{ $confirmedCount }}</div>
                                </div>
                                <div class="vr"></div>
                                <div class="text-center">
                                    <div class="small text-muted text-uppercase fw-semibold" style="font-size: 0.7rem;">Issued</div>
                                    <div class="fw-bold fs-5 text-primary">{{ $issuedCount }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <div class="small text-secondary">
                                @if($course->signatories->count() > 0)
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Signatories configured
                                @else
                                    <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Signatories pending
                                @endif
                            </div>
                            
                            <div class="btn-group gap-2">
                                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#issueModal-{{ $course->id }}">
                                    @if($allIssued)
                                        <i class="bi bi-pencil-square me-1"></i> Update Signatories
                                    @else
                                        <i class="bi bi-award me-1"></i> Provide Certificates
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="issueModal-{{ $course->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-award me-2"></i> Issue Certificates: {{ $course->title }}
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>
                                
                                <form action="{{ route('college.certificates.issue', $course) }}" method="POST" enctype="multipart/form-data">
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
                                                    // safely grab existing signatory if it exists
                                                    $existing = $course->signatories->where('display_order', $i + 1)->first();
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
                    @endforeach
            </div>
        @endif
    </div>
</div>
</x-college.layout>