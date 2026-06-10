{{-- resources/views/components/common/course-details.blade.php --}}
@props(['course', 'type' => 'student', 'isguest' => false]) {{-- 💡 Added isguest here with a default value of false --}}

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
    {{-- Course Header --}}
    <div class="course-header mb-4 ">
        <div class="row g-0">
            <div class="col-md-5 course-image-col">
                @if(!empty($course->course_image))
                    <img src="{{ asset('storage/' . ltrim($course->course_image, '/')) }}" alt="{{ $course->title ?? 'Course' }}" class="course-detail-image">
                @else
                    <div class="d-flex align-items-center justify-content-center h-100 course-image-placeholder">
                        <i class="bi bi-code-slash fs-1 text-primary"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-7 p-4 d-flex flex-column justify-content-center">
                <span class="badge d-inline-flex align-items-center bg-{{ ($course->course_type ?? '') === 'student_only' ? 'primary' : 'warning' }}-subtle text-{{ ($course->course_type ?? '') === 'student_only' ? 'primary' : 'warning' }} mb-2" style="font-size: 0.75rem; padding: 0.25rem 0.8rem; width: fit-content; white-space: nowrap;">
                    {{ ($course->course_type ?? '') === 'student_only' ? 'Student Only' : 'Firm Available' }}
                </span>

                <h1 class="fw-bold mb-2">{{ $course->title ?? 'Course' }}</h1>
                <p class="text-secondary mb-1">
                    <i class="bi bi-building me-1"></i> {{ $course->college?->institution_name ?? $course->college?->user?->name ?? 'College' }}
                    <span class="mx-2">|</span>
                    @if($type === 'firm')
                        <i class="bi bi-geo-alt me-1"></i> Venue set by you
                    @else
                        <i class="bi bi-geo-alt me-1"></i> {{ $course->venue ?? 'TBA' }}
                    @endif
                </p>
                <div class="small text-muted mb-3">
                    <i class="bi bi-calendar3 me-1"></i>
                    @if($type === 'firm')
                        Date set by you
                        <span class="mx-2">|</span>
                        <i class="bi bi-clock me-1"></i> Time slot set by you
                    @else
                        {{ $startDisplay }}
                        @if(!empty($endDisplay)) – {{ $endDisplay }} @endif
                        @if(!empty($course->time_slot))
                            <span class="mx-2">|</span>
                            <i class="bi bi-clock me-1"></i> {{ $course->time_slot }}
                        @endif
                    @endif
                </div>
            @if(isset($isguest) && ($isguest === true || $isguest === 'true' || $isguest == 1))
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-semibold">
                        <i class="bi bi-lock me-2"></i> Login as correct user to Enroll
                </a>
            @else
                @auth
                    @if(auth()->user()->role === 'student')
                        @php
                            $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
                                ->where('course_id', $course->id)
                                ->first();
                        @endphp
                        @if($enrollment && $enrollment->status === 'confirmed')
                            <button type="button" class="btn btn-success rounded-pill px-5 py-3 fw-semibold shadow-sm" disabled>
                                <i class="bi bi-check-circle me-2"></i> Enrolled
                            </button>
                        @elseif($enrollment && $enrollment->status === 'pending')
                            <button type="button" class="btn btn-warning rounded-pill px-5 py-3 fw-semibold shadow-sm" disabled>
                                <i class="bi bi-hourglass-split me-2"></i> Waiting for Approval
                            </button>
                        @elseif($enrollment && $enrollment->status === 'rejected')
                            <button type="button" class="btn btn-danger rounded-pill px-5 py-3 fw-semibold shadow-sm" disabled>
                                <i class="bi bi-x-circle me-2"></i> Request Rejected
                            </button>
                        @else
                            <form action="{{ route('student.course.enroll', $course->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold shadow-sm">
                                    <i class="bi bi-box-arrow-in-right me-2"></i> Enroll Now
                                </button>
                            </form>
                        @endif
                    @elseif(auth()->user()->role === 'firm')
                        <button type="button" class="btn btn-primary rounded-pill px-5 py-3 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#firmBookingModal">
                            <i class="bi bi-building me-2"></i> Book for Firm
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill px-5 py-3 fw-semibold">
                        <i class="bi bi-lock me-2"></i> Login to Enroll
                    </a>
                @endauth
            @endif
            </div>
        </div>
    </div>

    {{-- Two‑column layout --}}
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
            @if($type === 'mentor')
                @php
                    $enrolledStudents = $course->enrollments
                        ->where('type', 'student')
                        ->where('status', 'confirmed')
                        ->map(function ($enrollment) {
                            return [
                                'id' => $enrollment->user->id,
                                'name' => $enrollment->user->name,
                                'email' => $enrollment->user->email,
                                'enrollment_id' => $enrollment->id,
                            ];
                        })
                        ->values();
                @endphp
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Enrolled Students ({{ $enrolledStudents->count() }})</h5>
                    
                    @if($enrolledStudents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($enrolledStudents as $student)
                                @php
                                    $chat = \App\Models\Chat::where('mentor_id', auth()->user()->mentor?->id)
                                        ->where('student_id', $student['id'])
                                        ->first();
                                    $chatStatus = $chat ? ($chat->status === 'active' ? 'active' : 'pending') : 'none';
                                @endphp
                                <div class="list-group-item border-0 px-0 py-3 d-flex justify-content-between align-items-center">
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-semibold mb-1 text-truncate">{{ $student['name'] }}</p>
                                        <small class="text-muted text-truncate d-block">{{ $student['email'] }}</small>
                                    </div>
                                    <div class="ms-2 flex-shrink-0">
                                        @if($chatStatus === 'active')
                                            <a href="" class="btn btn-sm btn-primary rounded-pill">
                                                <i class="bi bi-chat-left-text me-1"></i> Open Chat
                                            </a>
                                        @elseif($chatStatus === 'pending')
                                            <button type="button" class="btn btn-sm btn-warning rounded-pill" disabled>
                                                <i class="bi bi-hourglass-split me-1"></i> Pending
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" disabled>
                                                <i class="bi bi-dash me-1"></i> No Request
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3 mb-0"><i class="bi bi-inbox me-2"></i> No enrolled students yet</p>
                    @endif
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary me-2"></i>Enrollment</h5>

                    @auth
                        @if(auth()->user()->role === 'student' && ($course->course_type === "student_only"))
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary">Seats filled</span>
                                <span class="fw-semibold">{{ $seatsFilled }} / {{ $totalSeats }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 10px; border-radius: 10px;">
                                <div class="progress-bar bg-{{ $seatPercent > 80 ? 'danger' : ($seatPercent > 50 ? 'warning' : 'primary') }}" role="progressbar" style="width: {{ $seatPercent }}%;" aria-valuenow="{{ $seatPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        @endif
                    @endauth

                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span><i class="bi bi-tag-fill"></i> Price</span>
                        <span class="fw-bold text-primary fs-5">
                            {{ ((float) ($course->price ?? 0) <= 0) ? 'Free' : '₹' . number_format((float) $course->price, 0) }}
                        </span>
                    </div>
                    @if($type === 'firm' && ((float) ($course->price ?? 0) > 0))
                        <p class="text-muted small mb-2"><i class="bi bi-info-circle me-1"></i> Price is per participant</p>
                    @endif
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
            @endif

            @if($mentor && $type !== 'mentor')
                @php
                    $mentorChat = null;
                    $chatStatus = 'none';
                    
                        $mentorChat = \App\Models\Chat::where('mentor_id', $mentor->user->id)
                            ->where('student_id', auth()->id())
                            ->first();
                        if($mentorChat) {
                            $chatStatus = $mentorChat->status;
                        }
                @endphp
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-badge-fill text-primary me-2"></i>Your Mentor</h5>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            @if(!empty($mentor->photo))
                                <img src="{{ asset('storage/' . ltrim($mentor->photo, '/')) }}" class="rounded-circle" width="64" height="64" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                    <i class="bi bi-person-workspace fs-3 text-primary"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ms-3 flex-grow-1">
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
                    
                    @auth
                        @if(auth()->user()->role === 'student')
                            @php
                                $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
                                    ->where('course_id', $course->id)
                                    ->first();
                                $isEnrolledConfirmed = $enrollment && $enrollment->status === 'confirmed';
                                $hasMentorAssigned = !empty($course->mentor_id);
                            @endphp

                            @if(! $hasMentorAssigned)
                                <button type="button" class="btn btn-secondary rounded-pill py-2 fw-semibold w-100" disabled>
                                    <i class="bi bi-person-x me-2"></i> Mentor Not Assigned
                                </button>
                            @elseif(! $isEnrolledConfirmed)
                                <button type="button" class="btn btn-warning rounded-pill py-2 fw-semibold w-100" disabled>
                                    <i class="bi bi-hourglass-split me-2"></i> Enroll & Confirm to Request
                                </button>
                            @else
                                @if($chatStatus === 'none')
                                    <form action="{{ route('student.mentor.request') }}" method="POST" class="d-grid">
                                        @csrf
                                        <input type="hidden" name="mentor_id" value="{{ $mentor->id }}">
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-semibold">
                                            <i class="bi bi-hand-thumbs-up me-2"></i> Request Mentor
                                        </button>
                                    </form>
                                @elseif($chatStatus === 'pending')
                                    <button type="button" class="btn btn-warning rounded-pill py-2 fw-semibold w-100" disabled>
                                        <i class="bi bi-hourglass-split me-2"></i> View Request Status
                                    </button>
                                @elseif($chatStatus === 'active')
                                    <a href="{{ route('mentor.chats.show', $mentorChat->id) }}" class="btn btn-success rounded-pill py-2 fw-semibold d-block text-center">
                                        <i class="bi bi-chat-left-text me-2"></i> Open Chat
                                    </a>
                                @endif
                            @endif
                        @endif
                    @endauth
                </div>
            @endif
            <x-common.college-details-card :college="$course->college" />
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
    /* Force image to cover like landscape even if portrait */
    .course-image-col {
        height: 240px;
        position: relative;
        overflow: hidden;
    }
    .course-detail-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
    }
    .course-image-placeholder {
        height: 100%;
        background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
    }
    @media (max-width: 767.98px) {
        .course-image-col {
            height: 220px;
        }
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