<x-firm.layout title="Explore Courses - EduConnect" active="coursedetails">
    <x-common.course-details :course="$course" type="firm" />
        {{-- Firm Booking Modal --}}
    <div class="modal fade" id="firmBookingModal" tabindex="-1" aria-labelledby="firmBookingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="firmBookingModalLabel">
                        <i class="bi bi-building me-2"></i> Book "{{ $course->title }}" for Your Organisation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('firm.course.book', $course->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            You'll propose your preferred venue, schedule, and number of participants.
                            The college will review and confirm your booking.
                        </p>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Proposed Venue *</label>
                                <input type="text" name="requested_venue" class="form-control" 
                                       placeholder="e.g., Conference Room, Mumbai Office" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Proposed Date & Time *</label>
                                <input type="datetime-local" name="proposed_schedule" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Number of Participants *</label>
                                <input type="number" name="participant_count" class="form-control" min="1" 
                                       placeholder="e.g., 10" required>
                                <div class="form-text">You can add individual names later from your bookings.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Any special request</label>
                                <textarea name="college_note" class="form-control" rows="2" 
                                          placeholder="Optional note to the college"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send me-1"></i> Submit Booking Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-firm.layout>