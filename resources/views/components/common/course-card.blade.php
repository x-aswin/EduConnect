@props(['course'])

@once
    @push('styles')
    <style>
        .course-card {
            border-radius: 1.4rem;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.08) !important;
        }
    </style>
    @endpush
@endonce

<div class="col-lg-4 col-md-6">
            <div class="card course-card border-0 shadow-sm h-100">
                {{-- Gradient header with icon --}}
                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 140px; background: {{ $course['image_bg'] }};">
                    <i class="bi {{ $course['icon'] }} fs-1 {{ $course['icon_color'] }}"></i>
                </div>
                <div class="card-body d-flex flex-column">
                    {{-- Course type badge --}}
                    @if($course['type'] == 'student_only')
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2 align-self-start">Student Only</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-warning mb-2 align-self-start">Firm Available</span>
                    @endif

                    <h5 class="fw-bold card-title">{{ $course['title'] }}</h5>
                    <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> {{ $course['college'] }}</p>
                    <p class="small text-secondary mb-2">
                        <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] }} · 
                        <i class="bi bi-calendar3 ms-2"></i> Starts {{ $course['start_date'] }}
                    </p>

                    {{-- Price --}}
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="fw-bold text-primary fs-5">{{ $course['price'] }}</span>
                        
                        {{-- Seats: conditional display --}}
                        @auth
                            @if(auth()->user()->role == 'student')
                                {{-- Student sees seat counter --}}
                                <span class="small text-secondary">
                                    <i class="bi bi-person"></i> 
                                    {{ $course['seats_available'] }} / {{ $course['seats_total'] }} seats
                                </span>
                            @elseif(auth()->user()->role == 'firm')
                                {{-- Firm does NOT see seat count (or maybe sees only total capacity if needed) --}}
                                {{-- Intentionally left empty, or you could show nothing --}}
                            @endif
                        @else
                            {{-- Guest: maybe a lock icon or no seat info --}}
                            <span class="small text-muted"><i class="bi bi-lock"></i> Login to view</span>
                        @endauth
                    </div>

                    {{-- Progress bar - only for students --}}
                    @auth
                        @if(auth()->user()->role == 'student')
                            @php
                                $percent = ($course['seats_total'] > 0) ? round((($course['seats_total'] - $course['seats_available']) / $course['seats_total']) * 100) : 0;
                            @endphp
                            <div class="progress mt-2 mb-3" style="height: 6px;">
                                <div class="progress-bar bg-{{ $percent > 80 ? 'danger' : ($percent > 50 ? 'warning' : 'primary') }}" 
                                     style="width: {{ $percent }}%"></div>
                            </div>
                        @else
                            {{-- Firm sees no progress bar --}}
                            <div class="mb-3"></div> {{-- spacer to keep card height consistent --}}
                        @endif
                    @else
                        <div class="mb-3"></div>
                    @endauth

                    {{-- Enroll button - changes based on role --}}
                    @auth
                        @if(auth()->user()->role == 'student')
                            <button class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                                <i class="bi bi-box-arrow-in-right"></i> Enroll Now
                            </button>
                        @elseif(auth()->user()->role == 'firm')
                            <button class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                                <i class="bi bi-building"></i> Book for Firm
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                            <i class="bi bi-lock"></i> Login to Enroll
                        </a>
                    @endauth
                </div>
            </div>
</div>