{{-- resources/views/components/common/course-card.blade.php --}}
@props(['course', 'type' => null])

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
        {{-- Header: use actual image if available, otherwise gradient + icon --}}
        @if(!empty($course['image']))
            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 140px; background-image: url('{{ $course['image'] }}'); background-size: cover; background-position: center;">
            </div>
        @else
            <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 140px; background: {{ $course['image_bg'] ?? 'linear-gradient(135deg, #e0e7ff, #c7d2fe)' }};">
                <i class="bi {{ $course['icon'] ?? 'bi-code-slash' }} fs-1 {{ $course['icon_color'] ?? 'text-primary' }}"></i>
            </div>
        @endif

        <div class="card-body d-flex flex-column">
            {{-- Type and category badges --}}
            <div class="d-flex align-items-center gap-2 mb-2">
                @if(($course['type'] ?? '') === 'student_only')
                    <span class="badge bg-primary bg-opacity-10 text-primary">Student Only</span>
                @elseif(($course['type'] ?? '') === 'firm_only')
                    <span class="badge bg-warning bg-opacity-10 text-warning">Firm Only</span>
                @endif

                @if(!empty($course['category']))
                    @php
                        $catBadgeClass = 'bg-light text-secondary';
                        // Use a colour from the icon_color if available, otherwise light grey
                        if (!empty($course['icon_color'])) {
                            $catBadgeClass = str_replace('text-', 'bg-', $course['icon_color']) . ' bg-opacity-10 ' . $course['icon_color'];
                        } else {
                            $catBadgeClass = 'bg-light text-secondary';
                        }
                    @endphp
                    <span class="badge {{ $catBadgeClass }}">{{ $course['category'] }}</span>
                @endif
            </div>

            <h5 class="fw-bold card-title">{{ $course['title'] }}</h5>
            <p class="small text-secondary mb-1"><i class="bi bi-building me-1"></i> {{ $course['college'] ?? 'Unknown College' }}</p>

            {{-- Venue & date: only for students (or when type is not firm) --}}
            @if($type === 'student' || !$type)
                <p class="small text-secondary mb-2">
                    <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] ?? 'TBA' }} ·
                    <i class="bi bi-calendar3 ms-2"></i> Starts {{ $course['start_date'] ?? 'TBA' }}
                </p>
            @elseif($type === 'firm')
                <p class="small text-secondary mb-2">
                    <i class="bi bi-geo-alt me-1"></i> Flexible venue ·
                    <i class="bi bi-calendar3 ms-2"></i> Schedule up to you
                </p>
            @endif

            {{-- Price --}}
            <div class="d-flex justify-content-between align-items-center mt-auto">
                <span class="fw-bold text-primary fs-5">{{ $course['price'] ?? 'Free' }}</span>

                {{-- Seat info: only for students --}}
                @if($type === 'student')
                    @php
                        $totalSeats = (int) ($course['seats_total'] ?? 0);
                        $availableSeats = (int) ($course['seats_available'] ?? 0);
                        $booked = max(0, $totalSeats - $availableSeats);
                    @endphp
                    @if($totalSeats > 0)
                        <span class="small text-secondary">
                            <i class="bi bi-person"></i> {{ $booked }} / {{ $totalSeats }} seats
                        </span>
                    @else
                        <span class="small text-secondary"><i class="bi bi-person"></i> Seats: N/A</span>
                    @endif
                @elseif($type === 'firm' || !$type)
                    {{-- Firm & guest: no seat count --}}
                    @if(!$type)
                        <span class="small text-muted"><i class="bi bi-lock"></i> Login to view</span>
                    @endif
                @endif
            </div>

            {{-- Progress bar: only for students --}}
            @if($type === 'student')
                @php
                    $percent = 0;
                    if (($course['seats_total'] ?? 0) > 0) {
                        $percent = round((($course['seats_total'] - ($course['seats_available'] ?? 0)) / $course['seats_total']) * 100);
                    }
                @endphp
                <div class="progress mt-2 mb-3" style="height: 6px;">
                    <div class="progress-bar bg-{{ $percent > 80 ? 'danger' : ($percent > 50 ? 'warning' : 'primary') }}" style="width: {{ $percent }}%"></div>
                </div>
            @else
                {{-- Firm / guest: keep spacing consistent --}}
                <div class="mb-3"></div>
            @endif

            {{-- Action button --}}
            @if($type === 'student')
                <a href="{{ route('student.course.show', ['slug' => $course['slug'] ?? '']) }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                    <i class="bi bi-box-arrow-in-right"></i> View Details
                </a>
            @elseif($type === 'firm')
                <a href="{{ route('firm.course.show', ['slug' => $course['slug'] ?? '']) }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                    <i class="bi bi-building"></i> View Details
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold mt-auto">
                    <i class="bi bi-lock"></i> View Details
                </a>
            @endif
        </div>
    </div>
</div>