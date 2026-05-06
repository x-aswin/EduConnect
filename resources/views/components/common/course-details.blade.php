
@props(['course'])
@php
    $totalSeats = (int) ($course->total_seats ?? 0);
    $availableSeats = (int) ($course->available_seats ?? 0);
    $seatsFilled = max(0, $totalSeats - $availableSeats);
    $seatPercent = $totalSeats > 0 ? round(($seatsFilled / $totalSeats) * 100) : 0;

    $sections = $course->sections ?? collect();
    $mentor = $course->mentor ?? null;

    $startDisplay = 'TBA';
    if (!empty($course->start_date)) {
        try {
            $startDisplay = \Illuminate\Support\Carbon::parse($course->start_date)->format('M d, Y');
        } catch (\Throwable $e) {
            $startDisplay = (string) $course->start_date;
        }
    }

    $endDisplay = null;
    if (!empty($course->end_date)) {
        try {
            $endDisplay = \Illuminate\Support\Carbon::parse($course->end_date)->format('M d, Y');
        } catch (\Throwable $e) {
            $endDisplay = (string) $course->end_date;
        }
    }
@endphp

<div class="container py-4">
    <div class="course-header mb-4">
        <div class="row g-0">
            <div class="col-md-5">
                @if(!empty($course->course_image))
                    <img src="{{ asset('storage/' . ltrim($course->course_image, '/')) }}" alt="{{ $course->title ?? 'Course' }}" style="width: 100%; height: 100%; object-fit: cover; min-height: 220px;">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe); min-height: 220px;">
                        <i class="bi bi-code-slash fs-1 text-primary"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-7 p-4 d-flex flex-column justify-content-center">
                <span class="badge bg-{{ ($course->course_type ?? '') === 'student_only' ? 'primary' : 'warning' }}-subtle text-{{ ($course->course_type ?? '') === 'student_only' ? 'primary' : 'warning' }} mb-2" style="font-size: 0.75rem; padding: 0.25rem 0.8rem;">
                    {{ ($course->course_type ?? '') === 'student_only' ? 'Student Only' : 'Firm Available' }}
                </span>

                <h1 class="fw-bold mb-2">{{ $course->title ?? 'Course' }}</h1>
                <p class="text-secondary mb-1">
                    <i class="bi bi-building me-1"></i> {{ $course->college?->institution_name ?? $course->college?->user?->name ?? 'College' }}
                    <span class="mx-2">|</span>
                    <i class="bi bi-geo-alt me-1"></i> {{ $course->venue ?? 'TBA' }}
                </p>
                <div class="small text-muted mb-3">
                    <i class="bi bi-calendar3 me-1"></i> {{ $startDisplay }}
                    @if(!empty($endDisplay))
                        – {{ $endDisplay }}
                    @endif
                    @if(!empty($course->time_slot))
                        <span class="mx-2">|</span>
                        <i class="bi bi-clock me-1"></i> {{ $course->time_slot }}
                    @endif
                </div>

                @auth
                    @if(auth()->user()->role === 'student')
                        <button type="button" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold shadow-sm" disabled>
                            <i class="bi bi-box-arrow-in-right me-2"></i> Enroll Now
                        </button>
                    @elseif(auth()->user()->role === 'firm')
                        <button type="button" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold shadow-sm" disabled>
                            <i class="bi bi-building me-2"></i> Book for Firm
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-semibold">
                        <i class="bi bi-lock me-2"></i> Login to Enroll
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h4 class="fw-bold mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>About this course</h4>
                <p class="text-secondary">{{ $course->description ?? '' }}</p>
                <div class="d-flex flex-wrap gap-3 mt-2">
                    <span class="badge bg-light text-dark px-3 py-2 rounded-pill" style="font-size: 0.8rem;">
                        <i class="bi bi-journal-check me-1"></i> {{ $sections->count() }} Modules
                    </span>
                    @if(!empty($course->is_certified))
                        <span class="badge bg-light text-dark px-3 py-2 rounded-pill" style="font-size: 0.8rem;">
                            <i class="bi bi-patch-check-fill text-success me-1"></i> Certificate
                        </span>
                    @endif
                </div>
            </div>

            @if($sections->count())
                <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                    <h4 class="fw-bold mb-3"><i class="bi bi-list-ol text-primary me-2"></i>Course Modules</h4>
                    <div class="accordion section-accordion" id="courseModulesAccordion">
                        @foreach($sections->sortBy('priority_order') as $section)
                            <div class="accordion-item border-0 mb-3">
                                <h2 class="accordion-header" id="heading{{ $section->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $section->id }}" aria-expanded="false" aria-controls="collapse{{ $section->id }}">
                                        <span class="me-2 fw-bold">{{ $loop->iteration }}.</span> {{ $section->section_heading ?? 'Module' }}
                                    </button>
                                </h2>
                                <div id="collapse{{ $section->id }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $section->id }}" data-bs-parent="#courseModulesAccordion">
                                    <div class="accordion-body text-secondary">
                                        {{ $section->section_content ?? '' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Enrollment</h5>

                @auth
                    @if(auth()->user()->role === 'student')
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary">Seats filled</span>
                            <span class="fw-semibold">{{ $seatsFilled }} / {{ $totalSeats }}</span>
                        </div>
                        <div class="progress mb-3" style="height: 10px; border-radius: 10px;">
                            <div class="progress-bar bg-{{ $seatPercent > 80 ? 'danger' : ($seatPercent > 50 ? 'warning' : 'primary') }}" role="progressbar" style="width: {{ $seatPercent }}%;" aria-valuenow="{{ $seatPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    @endif
                @endauth

                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span><i class="bi bi-tag-fill"></i> Price</span>
                    <span class="fw-bold text-primary fs-5">{{ ((float) ($course->price ?? 0) <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0) }}</span>
                </div>
                <hr>
                @auth
                    @if(auth()->user()->role === 'student')
                        <div class="d-flex justify-content-between small">
                            <span>Certification</span>
                            <span class="text-success fw-semibold"><i class="bi bi-patch-check-fill"></i> {{ !empty($course->is_certified) ? 'Yes' : 'No' }}</span>
                        </div>
                    @endif
                @endauth
            </div>

            @if($mentor)
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge-fill text-primary me-2"></i>Your Mentor</h5>
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            @if(!empty($mentor->photo))
                                <img src="{{ asset('storage/' . ltrim($mentor->photo, '/')) }}" class="rounded-circle" width="64" height="64" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="bi bi-person-workspace fs-3 text-primary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-0">{{ $mentor->user->name ?? 'Mentor' }}</h6>
                            <small class="text-muted">{{ $mentor->qualification ?? '' }}</small>
                            <div class="mt-1 small">
                                @if(!empty($mentor->expertise))
                                    @foreach(explode(',', $mentor->expertise) as $skill)
                                        <span class="badge bg-light text-dark me-1">{{ trim($skill) }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($course->venue))
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-light">
                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                    <strong>Venue:</strong> {{ $course->venue }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .course-header {
        background: white;
        border-radius: 2rem;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(0,0,0,0.03);
    }
    .section-accordion .accordion-button {
        font-weight: 600;
        border-radius: 16px !important;
        background: #f8fafc;
        padding: 1rem 1.5rem;
        box-shadow: none;
    }
    .section-accordion .accordion-button:not(.collapsed) {
        background: #eef2ff;
        color: #1d4ed8;
    }
</style>
@endpush