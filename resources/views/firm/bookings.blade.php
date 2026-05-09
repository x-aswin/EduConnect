<x-firm.layout title="Manage Participants - EduConnect" active="bookings">
    @if(isset($bookings))
        <div class="mb-4 mt-4">
            <h1 class="fw-bold mb-1">
                <i class="bi bi-journal-check me-2 text-primary"></i>My Bookings
            </h1>
            <p class="text-secondary mb-0">View your firm bookings, participant status, venue details, and payment actions.</p>
            <p class="text-secondary mb-0">Pending bookings can be edited or removed. Confirmed bookings show payment.</p>
        </div>

        @if($bookings->isEmpty())
            <div class="alert alert-light border rounded-4 text-center py-5" style="background: linear-gradient(135deg, #f5f7ff, #eef1fa);">
                <div class="mb-3">
                    <i class="bi bi-inbox fs-1 text-primary"></i>
                </div>
                <h5 class="fw-bold mb-2">No Bookings Yet</h5>
                <p class="text-secondary mb-3">Start by exploring firm-only courses and submitting a booking request.</p>
                <a href="{{ route('firm.explore.index') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-compass me-2"></i> Explore Courses
                </a>
            </div>
        @else
        <div class="row g-3">
            @forelse($bookings as $booking)
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="fw-bold mb-1">{{ $booking->course?->title ?? 'Untitled Course' }}</h5>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $booking->course?->college?->institution_name ?? 'College' }}
                                    </small>
                                </div>
                                @if($booking->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="bi bi-check-circle-fill me-1"></i> Approved
                                    </span>
                                @elseif($booking->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">
                                        <i class="bi bi-x-circle-fill me-1"></i> Rejected
                                    </span>
                                @endif
                            </div>

                            <div class="row g-2 mb-3 pb-3 border-bottom small">
                                <div class="col-6">
                                    <span class="text-secondary d-block">Venue</span>
                                    <strong>{{ $booking->requested_venue ?? 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-secondary d-block">Time</span>
                                    <strong>
                                        @if($booking->proposed_schedule)
                                            {{ \Carbon\Carbon::parse($booking->proposed_schedule)->format('M d, Y h:i A') }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-secondary d-block">Participants</span>
                                    <strong>{{ $booking->participants->count() ?: $booking->participant_count }}</strong>
                                </div>
                                <div class="col-6">
                                    <span class="text-secondary d-block">Total Amount</span>
                                    <strong>{{ !is_null($booking->total_amount) ? '₹' . number_format((float) $booking->total_amount, 2) : '-' }}</strong>
                                </div>
                                @if(!empty($booking->college_note))
                                    <div class="col-12">
                                        <span class="text-secondary d-block">Message</span>
                                        <strong>{{ $booking->college_note }}</strong>
                                    </div>
                                @endif
                                @if(!empty($booking->course?->mentor?->user?->name))
                                    <div class="col-12">
                                        <span class="text-secondary d-block">Course Mentor</span>
                                        <strong>{{ $booking->course->mentor->user->name }}</strong>
                                    </div>
                                @endif
                            </div>

                            <div class="small text-muted mb-3">
                                <i class="bi bi-calendar3 me-1"></i>
                                Booking on {{ $booking->created_at->format('M d, Y') }}
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('firm.bookings.show', ['enrollment' => $booking, 'mode' => 'view']) }}" class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>

                                @if($booking->status === 'pending')
                                    <a href="{{ route('firm.bookings.show', ['enrollment' => $booking, 'mode' => 'edit']) }}" class="btn btn-outline-warning btn-sm rounded-pill">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('firm.bookings.destroy', $booking) }}" class="m-0" onsubmit="return confirm('Remove this booking request?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                            <i class="bi bi-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                @elseif($booking->status === 'confirmed' && $booking->payment_status === 'pending')
                                    <form method="POST" action="{{ route('firm.booking.pay', $booking) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm rounded-pill">
                                            <i class="bi bi-credit-card me-1"></i> Pay
                                        </button>
                                    </form>
                                @elseif($booking->status === 'confirmed' && $booking->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success align-self-center">Paid</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
            @endforelse
        </div>
        @endif

    @elseif(isset($enrollment))
        <div class="mb-4 mt-4">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h1 class="fw-bold mb-1"><i class="bi bi-people-fill me-2 text-primary"></i>Manage Participants</h1>
                    <p class="text-secondary mb-0">
                        {{ $enrollment->course->title }} – {{ $enrollment->participant_count }} participant(s) expected
                    </p>
                    <p class="text-secondary mb-0">
                        Venue: {{ $enrollment->requested_venue ?? 'N/A' }} · 
                        Time: {{ $enrollment->proposed_schedule ? \Carbon\Carbon::parse($enrollment->proposed_schedule)->format('M d, Y h:i A') : 'N/A' }} ·
                        <a href="{{ route('firm.course.show', $enrollment->course->slug) }}" class="text-decoration-none">View course</a>
                    </p>
                    @if(!empty($enrollment->college_note))
                        <p class="text-secondary mb-0">Message: {{ $enrollment->college_note }}</p>
                    @endif
                </div>
                <a href="{{ route('firm.bookings.index') }}" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i> Back to Bookings
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3">
            <div class="col-lg-6">
                <!-- Add Participant Form -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-person-plus me-2 text-primary"></i>Add Participant</h5>
                    @if($enrollment->status === 'pending' && ($mode ?? 'view') === 'edit')
                    <form action="{{ route('firm.booking.participants.store', $enrollment) }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Full Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Rahul Sharma" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small">Contact Info</label>
                                <input type="text" name="contact_info" class="form-control" placeholder="Phone or Email">
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-plus-lg"></i> Add
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                        <div class="alert alert-light border rounded-3 mb-0">
                            Editing is available only while the booking is pending and in edit mode.
                        </div>
                    @endif
                </div>

                <!-- Participant List -->
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-list-ul me-2 text-primary"></i>Participant List</h5>
                        @if($participants->isEmpty())
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1"></i>
                                <p class="mt-2">No participants added yet.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Contact</th>
                                            <th style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($participants as $participant)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $participant->name }}</td>
                                                <td>{{ $participant->contact_info }}</td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#participantViewModal{{ $participant->id }}">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        @if($enrollment->status === 'pending' && ($mode ?? 'view') === 'edit')
                                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill" data-bs-toggle="modal" data-bs-target="#participantEditModal{{ $participant->id }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <form action="{{ route('firm.booking.participants.destroy', [$enrollment, $participant]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" onclick="return confirm('Remove this participant?')">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            <div class="modal fade" id="participantViewModal{{ $participant->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Participant Details</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-2"><strong>Name:</strong> {{ $participant->name }}</div>
                                                            <div><strong>Contact:</strong> {{ $participant->contact_info ?: 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @if($enrollment->status === 'pending' && ($mode ?? 'view') === 'edit')
                                            <div class="modal fade" id="participantEditModal{{ $participant->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Participant</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="#">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Full Name</label>
                                                                    <input type="text" class="form-control" value="{{ $participant->name }}" readonly>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">Contact Info</label>
                                                                    <input type="text" class="form-control" value="{{ $participant->contact_info }}" readonly>
                                                                </div>
                                                                <div class="text-muted small">Inline edit can be added here if needed; current flow supports add/remove while pending.</div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 small text-muted">
                                <i class="bi bi-info-circle me-1"></i> 
                                {{ $participants->count() }} of {{ $enrollment->participant_count }} participant(s) added
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-primary"></i>Booking Summary</h5>
                    <div class="small text-muted mb-2">Course</div>
                    <h6 class="fw-bold">{{ $enrollment->course->title }}</h6>
                    <div class="small text-muted mt-3">College</div>
                    <div>{{ $enrollment->course->college?->institution_name ?? 'N/A' }}</div>
                    <div class="small text-muted mt-3">Requested Venue</div>
                    <div>{{ $enrollment->requested_venue ?? 'N/A' }}</div>
                    <div class="small text-muted mt-3">Proposed Schedule</div>
                    <div>{{ $enrollment->proposed_schedule ? \Carbon\Carbon::parse($enrollment->proposed_schedule)->format('M d, Y h:i A') : 'N/A' }}</div>
                    <div class="small text-muted mt-3">Payment</div>
                    <div>
                        @if($enrollment->status === 'confirmed' && $enrollment->payment_status === 'pending')
                            Pending
                        @elseif($enrollment->status === 'confirmed' && $enrollment->payment_status === 'paid')
                            Paid
                        @else
                            N/A
                        @endif
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        @if($enrollment->status === 'confirmed' && $enrollment->payment_status === 'pending')
                            <form method="POST" action="{{ route('firm.booking.pay', $enrollment) }}">
                                @csrf
                                <button class="btn btn-primary">Pay ₹ {{ number_format((float) $enrollment->total_amount, 2) }}</button>
                            </form>
                        @elseif($enrollment->status === 'pending')
                            @if(($mode ?? 'view') === 'edit')
                                <span class="badge bg-warning-subtle text-warning border border-warning align-self-center">Editing enabled</span>
                            @else
                                <a href="{{ route('firm.bookings.show', ['enrollment' => $enrollment, 'mode' => 'edit']) }}" class="btn btn-outline-warning">Edit Participants</a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @endif
</x-firm.layout>