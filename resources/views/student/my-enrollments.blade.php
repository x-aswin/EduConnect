<x-student.layout title="My Enrollments - EduConnect" active="myenrollments">
    <div class="mb-4 mt-4">
    <h1 class="fw-bold mb-1">
        <i class="bi bi-journal-check me-2 text-primary"></i>My Enrollments
    </h1>
    <p class="text-secondary mb-0">View all your course enrollments and their status.</p>
    <p class="text-secondary mb-0">You can make payments for approved entrollment or cancel entrollment that are yet to be approved here.</p>
</div>

    @if($enrollments->isEmpty())
        <div class="alert alert-light border rounded-4 text-center py-5" style="background: linear-gradient(135deg, #f5f7ff, #eef1fa);">
            <div class="mb-3">
                <i class="bi bi-inbox fs-1 text-primary"></i>
            </div>
            <h5 class="fw-bold mb-2">No Enrollments Yet</h5>
            <p class="text-secondary mb-3">You haven't enrolled in any courses yet. Start exploring to find courses that interest you!</p>
            <a href="{{ route('student.explore.index') }}" class="btn btn-primary rounded-pill">
                <i class="bi bi-compass me-2"></i> Explore Courses
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($enrollments as $enrollment)
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-body d-flex flex-column">
                            <!-- Header with course title and status -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $enrollment->course->title ?? 'Course' }}</h5>
                                    <small class="text-muted">
                                        <i class="bi bi-building me-1"></i> {{ $enrollment->course->college?->institution_name ?? 'College' }}
                                    </small>
                                </div>
                                {{-- Status Badge --}}
                                @if($enrollment->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Confirmed
                                    </span>
                                @elseif($enrollment->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                                @elseif($enrollment->status === 'rejected')
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                @endif
                            </div>

                            <!-- Mentor Info -->
                            @if($enrollment->course->mentor)
                                <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                                    <div >
                                        {{-- {{ strtoupper(substr($enrollment->course->mentor->user->name ?? 'M', 0, 1)) }} --}}
                                        <img src="{{ asset('storage/' . $enrollment->course->mentor->photo) }}" alt="Profile Photo" id="profilePhotoPreview" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #2563eb, #4f46e5); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.9rem; font-weight: 600;">
                                    </div>
                                    <div>
                                        <small class="text-secondary d-block">Mentor</small>
                                        <strong class="d-block">{{ $enrollment->course->mentor->user->name ?? 'Mentor' }}</strong>
                                    </div>
                                </div>
                            @endif

                            <!-- Course Details -->
                            <div class="row g-2 mb-3 pb-3 border-bottom small">
                                <div class="col-6">
                                    <span class="text-secondary d-block">Price</span>
                                    <strong>{{ ((float) ($enrollment->course->price ?? 0) <= 0) ? 'Free' : '₹' . number_format((float) $enrollment->course->price, 0) }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-secondary d-block">Total Amount</span>
                                    <strong>{{ !is_null($enrollment->total_amount) ? '₹' . number_format((float) $enrollment->total_amount, 2) : '-' }}</strong>
                                </div>
                                @if(!empty($enrollment->course->venue))
                                    <div class="col-12">
                                        <span class="text-secondary d-block">Venue</span>
                                        <strong>{{ $enrollment->course->venue }}</strong>
                                    </div>
                                @endif
                                @if(!empty($enrollment->course->start_date))
                                    <div class="col-12">
                                        <span class="text-secondary d-block">Starts</span>
                                        <strong>
                                            @php
                                                try {
                                                    echo \Carbon\Carbon::parse($enrollment->course->start_date)->format('M d, Y');
                                                } catch (\Throwable $e) {
                                                    echo $enrollment->course->start_date;
                                                }
                                            @endphp
                                        </strong>
                                    </div>
                                @endif
                            </div>

                            <!-- Enrollment Date -->
                            <div class="small text-muted mb-3">
                                <i class="bi bi-calendar3 me-1"></i> 
                                Enrolled on {{ $enrollment->created_at->format('M d, Y') }}
                            </div>

                            <!-- Payment Status -->
                            @if($enrollment->status === 'confirmed')
                                <div class="d-flex align-items-center gap-2 p-2 bg-light rounded-3 mb-3 small">
                                    @if($enrollment->payment_status === 'pending')
                                        <i class="bi bi-exclamation-circle text-warning"></i>
                                        <span><strong>Payment Pending</strong> - Complete payment</span>
                                    @elseif($enrollment->payment_status === 'paid')
                                        <i class="bi bi-check-circle text-success"></i>
                                        <span><strong>Payment Completed</strong> - You have access to this course</span>
                                    @elseif($enrollment->payment_status === 'na')
                                        <i class="bi bi-check-circle text-success"></i>
                                        <span>Free course - You have access</span>
                                    @endif
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('student.course.show', $enrollment->course->slug) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View Course
                                </a>
                                @if($enrollment->status === 'confirmed' && $enrollment->payment_status === 'pending')
                                    <a href="{{ route('student.enrollment.payment', $enrollment) }}" class="btn btn-warning btn-sm rounded-pill">
                                        <i class="bi bi-credit-card me-1"></i> Pay
                                    </a>
                                @elseif($enrollment->status === 'pending')
                                    <form method="POST" action="{{ route('student.my.enrollments.destroy', $enrollment) }}" class="m-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" onclick="return confirm('Remove this enrollment request?')">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Filter/Sort Info -->
        <div class="alert alert-light border rounded-3 mt-4 small text-secondary">
            <i class="bi bi-info-circle me-2"></i>
            Showing <strong>{{ $enrollments->count() }}</strong> enrollment{{ $enrollments->count() !== 1 ? 's' : '' }}
        </div>
    @endif
</x-student.layout>
