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

                            <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold small">Proposed Start Date</label>
                            <input type="date" name="proposed_start" id="proposed_start" class="form-control" required>
                            <div class="invalid-feedback" id="proposed_start_feedback" style="display: none;"></div>
                        </div>

                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold small">Proposed End Date</label>
                            <input type="date" name="proposed_end" id="proposed_end" class="form-control" required>
                            <div class="invalid-feedback" id="proposed_end_feedback" style="display: none;"></div>
                        </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Proposed Time Slot</label>
                                <input type="text" name="proposed_time" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Number of Participants *</label>
                                <input type="number" name="participant_count" id="firm_participant_count" class="form-control" min="1" 
                                    placeholder="e.g., 10" required readonly>
                                <div class="form-text">Will be updated when using the + button.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Any special request</label>
                                <textarea name="college_note" class="form-control" rows="2" 
                                          placeholder="Optional note to the college"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Price per participant</label>
                                <input type="text" id="firm_unit_price_display" class="form-control" readonly value="₹ {{ number_format((float) ($course->price ?? 0), 2) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Total Amount</label>
                                <div class="input-group">
                                    <input type="text" id="firm_total_amount_display" class="form-control" readonly value="₹ 0.00">
                                    <input type="hidden" name="total_amount" id="firm_total_amount_hidden" value="0">
                                </div>
                            </div>
                            <div class="col-12 mt-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label mb-0 small fw-semibold">Firm Participants</label>
                                    <div class="d-flex gap-2">
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="participant_name_input" class="form-control" placeholder="Name">
                                            <input type="text" id="participant_contact_input" class="form-control" placeholder="Phone / Email">
                                            <button type="button" id="participant_add_btn" class="btn btn-outline-primary">
                                                <i class="bi bi-plus-lg"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered" id="firmParticipantsTable">
                                        <thead class="table-light small">
                                            <tr>
                                                <th style="width:60%">Name</th>
                                                <th style="width:30%">Contact</th>
                                                <th style="width:10%">&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody id="firmParticipantsTableBody">
                                            @php
                                                $oldParticipants = old('participants', []);
                                            @endphp
                                            @if(count($oldParticipants) > 0)
                                                @foreach($oldParticipants as $index => $p)
                                                    <tr>
                                                        <td>
                                                            {{ $p['name'] ?? '' }}
                                                            <input type="hidden" name="participants[{{ $index }}][name]" value="{{ $p['name'] ?? '' }}">
                                                        </td>
                                                        <td>
                                                            {{ $p['contact_info'] ?? '' }}
                                                            <input type="hidden" name="participants[{{ $index }}][contact_info]" value="{{ $p['contact_info'] ?? '' }}">
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-outline-danger remove-participant-row"><i class="bi bi-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addBtn = document.getElementById('participant_add_btn');
        const nameInput = document.getElementById('participant_name_input');
        const contactInput = document.getElementById('participant_contact_input');
        const tbody = document.getElementById('firmParticipantsTableBody');
        const participantCountInput = document.getElementById('firm_participant_count');

        const startFeedback = document.getElementById('proposed_start_feedback');
        const endFeedback = document.getElementById('proposed_end_feedback');
        const bookingForm = document.querySelector('#firmBookingModal form');

        const startDateInput = document.getElementById('proposed_start');
        const endDateInput = document.getElementById('proposed_end');

        const requiredDurationDays = Number(@json($course->firm_duration ?? 0));

        function validateCourseDuration() {
            if (!startDateInput.value || !endDateInput.value || requiredDurationDays <= 0) {
                return true;
            }

            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);
            
            // 1. Reset everything to baseline clean states
            startDateInput.classList.remove('is-invalid');
            endDateInput.classList.remove('is-invalid');
            if (startFeedback) startFeedback.style.display = 'none';
            if (endFeedback) endFeedback.style.display = 'none';

            // 2. Date comparison validation rule check
            if (end < start) {
                endDateInput.classList.add('is-invalid');
                if (endFeedback) {
                    endFeedback.textContent = 'End date cannot be before start date.';
                    endFeedback.style.display = 'block';
                }
                return false;
            }

            // 3. Exact matching sequence evaluation loop
            const timeDiff = Math.abs(end.getTime() - start.getTime());
            const calculatedDays = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)) + 1;

            if (calculatedDays !== requiredDurationDays) {
                endDateInput.classList.add('is-invalid');
                if (endFeedback) {
                    endFeedback.textContent = `The duration must be exactly ${requiredDurationDays} days. Currently selected: ${calculatedDays} days.`;
                    endFeedback.style.display = 'block';
                }
                return false;
            }

            return true;
        } // <-- Only ONE closing brace here

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', validateCourseDuration);
            endDateInput.addEventListener('change', validateCourseDuration);
        }

        if (bookingForm) {
            bookingForm.addEventListener('submit', function (e) {
                if (!validateCourseDuration()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }
        

        if (startDateInput && endDateInput) {
            startDateInput.addEventListener('change', validateCourseDuration);
            endDateInput.addEventListener('change', validateCourseDuration);
        }

        if (bookingForm) {
            bookingForm.addEventListener('submit', function (e) {
                if (!validateCourseDuration()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }

        const unitPrice = Number(@json($course->price ?? 0));

        function formatCurrency(num) {
            try {
                return '₹ ' + Number(num).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } catch (e) {
                return '₹ ' + Number(num).toFixed(2);
            }
        }

        function updateParticipantCount() {
            const count = tbody.querySelectorAll('tr').length;
            if (participantCountInput) participantCountInput.value = count;
            const total = Number(unitPrice || 0) * Number(count || 0);
            const totalHidden = document.getElementById('firm_total_amount_hidden');
            const totalDisplay = document.getElementById('firm_total_amount_display');
            if (totalHidden) totalHidden.value = total.toFixed(2);
            if (totalDisplay) totalDisplay.value = formatCurrency(total);
        }

        function reindexHiddenInputs() {
            Array.from(tbody.querySelectorAll('tr')).forEach((tr, idx) => {
                const nameHidden = tr.querySelector('input[type="hidden"][name$="[name]"]');
                const contactHidden = tr.querySelector('input[type="hidden"][name$="[contact_info]"]');
                if (nameHidden) nameHidden.name = `participants[${idx}][name]`;
                if (contactHidden) contactHidden.name = `participants[${idx}][contact_info]`;
            });
        }

        function addParticipant(name, contact) {
            const tr = document.createElement('tr');

            const tdName = document.createElement('td');
            tdName.textContent = name;
            const nameHidden = document.createElement('input');
            nameHidden.type = 'hidden';
            nameHidden.name = `participants[][name]`;
            nameHidden.value = name;
            tdName.appendChild(nameHidden);

            const tdContact = document.createElement('td');
            tdContact.textContent = contact;
            const contactHidden = document.createElement('input');
            contactHidden.type = 'hidden';
            contactHidden.name = `participants[][contact_info]`;
            contactHidden.value = contact;
            tdContact.appendChild(contactHidden);

            const tdAction = document.createElement('td');
            tdAction.className = 'text-center';
            const remBtn = document.createElement('button');
            remBtn.type = 'button';
            remBtn.className = 'btn btn-sm btn-outline-danger remove-participant-row';
            remBtn.innerHTML = '<i class="bi bi-trash"></i>';
            tdAction.appendChild(remBtn);

            tr.appendChild(tdName);
            tr.appendChild(tdContact);
            tr.appendChild(tdAction);

            tbody.appendChild(tr);
            reindexHiddenInputs();
            updateParticipantCount();
        }

        if (addBtn) {
            addBtn.addEventListener('click', function () {
                const name = (nameInput.value || '').trim();
                const contact = (contactInput.value || '').trim();
                if (!name) {
                    nameInput.classList.add('is-invalid');
                    nameInput.focus();
                    return;
                }
                nameInput.classList.remove('is-invalid');
                addParticipant(name, contact);
                nameInput.value = '';
                contactInput.value = '';
                nameInput.focus();
            });
        }

        // Delegate remove
        tbody.addEventListener('click', function (e) {
            if (e.target.closest('.remove-participant-row')) {
                const btn = e.target.closest('.remove-participant-row');
                const tr = btn.closest('tr');
                if (tr) tr.remove();
                reindexHiddenInputs();
                updateParticipantCount();
            }
        });

        // initialize participant count from existing rows
        updateParticipantCount();
    });
</script>