<x-admin.layout active="enrollments">

@php
    $isEdit = isset($editEnrollment) && !isset($viewOnly);
    $isView = isset($editEnrollment) && isset($viewOnly);
@endphp

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
    <i class="bi bi-plus-lg"></i> Add New Enrollment
</button>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold">Manage Enrollments</h6>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $enrollments->count() }} Total</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>User / Firm</th>
                        <th>Course</th>
                        <th>College</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Participants</th>
                        <th>Total Cost</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $enrollment)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $enrollment->user?->name ?? 'N/A' }}</td>
                            <td>{{ $enrollment->course?->title ?? 'N/A' }}</td>
                            <td>{{ $enrollment->course?->college?->institution_name ?? 'N/A' }}</td>
                            <td>
                                @if($enrollment->type === 'firm')
                                    <span class="badge bg-info-subtle text-info border border-info">Firm</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">Student</span>
                                @endif
                            </td>
                            <td>
                                @if($enrollment->status === 'confirmed')
                                    <span class="badge bg-success-subtle text-success border border-success">Confirmed</span>
                                @elseif($enrollment->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($enrollment->payment_status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success">Paid</span>
                                @elseif($enrollment->payment_status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                @else
                                    <span class="badge bg-light text-dark border">N/A</span>
                                @endif
                            </td>
                            <td>{{ $enrollment->type === 'firm' ? ($enrollment->participants->count() ?: $enrollment->participant_count) : '-' }}</td>
                            <td>
                                @if(!is_null($enrollment->total_amount))
                                    ₹ {{ number_format((float) $enrollment->total_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm gap-1">
                                    <a href="{{ route('admin.enrollments.show', $enrollment->id) }}">
                                        <button class="btn btn-sm btn-outline-primary" title="View Enrollment">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </a>
                                    <a href="{{ route('admin.enrollments.edit', $enrollment->id) }}">
                                        <button class="btn btn-sm btn-outline-warning" title="Edit Enrollment">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </a>
                                    <a href="{{ route('admin.enrollments.edit', [$enrollment->id, 'mode' => 'delete']) }}">
                                        <button class="btn btn-sm btn-outline-danger" title="Delete Enrollment">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No enrollments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Enrollment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.enrollments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">User / Firm</label>
                            <select name="user_id" id="enrollment_user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" data-user-role="{{ $user->role }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ ucfirst($user->role) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Course</label>
                            <select name="course_id" id="enrollment_course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" data-course-type="{{ $course->course_type }}" data-course-price="{{ $course->price ?? 0 }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }} ({{ $course->college?->institution_name ?? 'N/A' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('course_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Status</label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                <option value="na" {{ old('payment_status', 'na') === 'na' ? 'selected' : '' }}>N/A</option>
                                <option value="pending" {{ old('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Participant Count</label>
                            <input type="number" min="1" id="participant_count" class="form-control" value="{{ old('participant_count') }}" readonly>
                            @error('participants')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Total Amount</label>
                            <input type="number" min="0" step="0.01" id="total_amount" name="total_amount" class="form-control @error('total_amount') is-invalid @enderror" value="{{ old('total_amount') }}" readonly onkeydown="return false" onpaste="return false">
                            <small class="text-muted">Auto-calculated from course price x participant count.</small>
                            @error('total_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Requested Venue</label>
                            <input type="text" name="requested_venue" class="form-control @error('requested_venue') is-invalid @enderror" value="{{ old('requested_venue') }}">
                            @error('requested_venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 firm-only-field d-none">
                            <label class="form-label">Proposed Schedule</label>
                            <input type="datetime-local" name="proposed_schedule" class="form-control @error('proposed_schedule') is-invalid @enderror" value="{{ old('proposed_schedule') }}">
                            @error('proposed_schedule')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 firm-only-field d-none">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Firm Participants</label>
                                <button type="button" id="add_participant_btn" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-lg"></i> Add Participant
                                </button>
                            </div>

                            @php
                                $oldParticipants = old('participants', [['name' => '', 'contact_info' => '']]);
                                if (count($oldParticipants) === 0) {
                                    $oldParticipants = [['name' => '', 'contact_info' => '']];
                                }
                            @endphp

                            <div id="participants_container" class="d-grid gap-2">
                                @foreach($oldParticipants as $index => $participant)
                                    <div class="row g-2 participant-row" data-index="{{ $index }}">
                                        <div class="col-md-5">
                                            <input
                                                type="text"
                                                name="participants[{{ $index }}][name]"
                                                class="form-control"
                                                placeholder="Participant Name"
                                                value="{{ $participant['name'] ?? '' }}"
                                            >
                                        </div>
                                        <div class="col-md-5">
                                            <input
                                                type="text"
                                                name="participants[{{ $index }}][contact_info]"
                                                class="form-control"
                                                placeholder="Contact Info (Phone/Email)"
                                                value="{{ $participant['contact_info'] ?? '' }}"
                                            >
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger w-100 remove-participant-btn">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('participants')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Enrollment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="manageEnrollmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    {{ $isEdit ? 'Edit Enrollment' : ($isView ? 'Enrollment Details' : 'Enrollment') }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ isset($editEnrollment) ? route('admin.enrollments.update', $editEnrollment->id) : '#' }}" method="POST">
                @csrf
                @if(isset($editEnrollment))
                    @method('PATCH')
                @endif

                <div class="modal-body">
                    @if(isset($editEnrollment))
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">User / Firm</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->user?->name ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Course</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->course?->title ?? 'N/A' }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <input type="text" class="form-control" value="{{ ucfirst($editEnrollment->type) }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Participants</label>
                                <input type="text" class="form-control" value="{{ $editEnrollment->type === 'firm' ? ($editEnrollment->participants->count() ?: $editEnrollment->participant_count) : '-' }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Total Cost</label>
                                <input type="text" class="form-control" value="{{ !is_null($editEnrollment->total_amount) ? '₹ ' . number_format((float) $editEnrollment->total_amount, 2) : '-' }}" disabled>
                            </div>

                            @if($editEnrollment->type === 'firm')
                                <div class="col-12">
                                    @if($isView)
                                        <label class="form-label">Participant Details</label>
                                        @if($editEnrollment->participants->isNotEmpty())
                                            <ul class="list-group">
                                                @foreach($editEnrollment->participants as $participant)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>{{ $participant->name }}</span>
                                                        <small class="text-muted">{{ $participant->contact_info }}</small>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="form-control bg-light">No participant details available.</div>
                                        @endif
                                    @else
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Participant Details</label>
                                            <button type="button" id="edit_add_participant_btn" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-plus-lg"></i> Add Participant
                                            </button>
                                        </div>

                                        @php
                                            $editParticipants = old('participants', $editEnrollment->participants->map(function ($participant) {
                                                return [
                                                    'name' => $participant->name,
                                                    'contact_info' => $participant->contact_info,
                                                ];
                                            })->all());
                                            if (count($editParticipants) === 0) {
                                                $editParticipants = [['name' => '', 'contact_info' => '']];
                                            }
                                        @endphp

                                        <div id="edit_participants_container" class="d-grid gap-2">
                                            @foreach($editParticipants as $index => $participant)
                                                <div class="row g-2 edit-participant-row" data-index="{{ $index }}">
                                                    <div class="col-md-5">
                                                        <input
                                                            type="text"
                                                            name="participants[{{ $index }}][name]"
                                                            class="form-control"
                                                            placeholder="Participant Name"
                                                            value="{{ $participant['name'] ?? '' }}"
                                                        >
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input
                                                            type="text"
                                                            name="participants[{{ $index }}][contact_info]"
                                                            class="form-control"
                                                            placeholder="Contact Info (Phone/Email)"
                                                            value="{{ $participant['contact_info'] ?? '' }}"
                                                        >
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-outline-danger w-100 edit-remove-participant-btn">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @error('participants')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">Enrollment Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="pending" {{ old('status', $editEnrollment->status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $editEnrollment->status ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="rejected" {{ old('status', $editEnrollment->status ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="na" {{ old('payment_status', $editEnrollment->payment_status ?? 'na') === 'na' ? 'selected' : '' }}>N/A</option>
                                    <option value="pending" {{ old('payment_status', $editEnrollment->payment_status ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $editEnrollment->payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                                </select>
                                @error('payment_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    @endif
                </div>

                <div class="modal-footer">
                    @if(isset($editEnrollment))
                        <a href="{{ route('admin.enrollments.index') }}" class="btn btn-secondary">Cancel</a>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @endif

                    @if(!$isView && isset($editEnrollment))
                        <button type="submit" class="btn btn-primary">Update Enrollment</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($deleteEnrollment) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Enrollment</h5>
                    <a href="{{ route('admin.enrollments.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>
                        Delete enrollment for
                        <strong>{{ $deleteEnrollment->user?->name ?? 'N/A' }}</strong>
                        in
                        <strong>{{ $deleteEnrollment->course?->title ?? 'N/A' }}</strong>?
                    </p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.enrollments.destroy', $deleteEnrollment->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            const deleteModal = document.getElementById('deleteModal');
            deleteModal.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit')) {
                    window.location.href = "{{ route('admin.enrollments.index') }}";
                }
            });
        });
    </script>
@endif

<x-toast />

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editPath = window.location.pathname.includes('/edit') || window.location.pathname.match(/\/\d+$/);
            const targetModal = editPath ? document.getElementById('manageEnrollmentModal') : document.getElementById('addEnrollmentModal');

            if (targetModal) {
                new bootstrap.Modal(targetModal).show();
            }
        });
    </script>
@endif

@if(isset($editEnrollment))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('manageEnrollmentModal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();

            modalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ route('admin.enrollments.index') }}";
                }
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userSelect = document.getElementById('enrollment_user_id');
        const courseSelect = document.getElementById('enrollment_course_id');
        const participantCountInput = document.getElementById('participant_count');
        const totalAmountInput = document.getElementById('total_amount');
        const firmFields = document.querySelectorAll('.firm-only-field');
        const participantsContainer = document.getElementById('participants_container');
        const addParticipantBtn = document.getElementById('add_participant_btn');
        const editParticipantsContainer = document.getElementById('edit_participants_container');
        const editAddParticipantBtn = document.getElementById('edit_add_participant_btn');

        let participantIndex = participantsContainer
            ? Math.max(0, ...Array.from(participantsContainer.querySelectorAll('.participant-row')).map(function (row) {
                return parseInt(row.getAttribute('data-index') || '0', 10);
            })) + 1
            : 0;

        let editParticipantIndex = editParticipantsContainer
            ? Math.max(0, ...Array.from(editParticipantsContainer.querySelectorAll('.edit-participant-row')).map(function (row) {
                return parseInt(row.getAttribute('data-index') || '0', 10);
            })) + 1
            : 0;

        function getParticipantRows() {
            if (!participantsContainer) {
                return [];
            }

            return Array.from(participantsContainer.querySelectorAll('.participant-row'));
        }

        function updateParticipantCount() {
            if (!participantCountInput) {
                return;
            }

            participantCountInput.value = getParticipantRows().length || '';
        }

        function buildParticipantRow(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 participant-row';
            wrapper.setAttribute('data-index', index.toString());
            wrapper.innerHTML = [
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][name]" class="form-control" placeholder="Participant Name">',
                '</div>',
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][contact_info]" class="form-control" placeholder="Contact Info (Phone/Email)">',
                '</div>',
                '<div class="col-md-2">',
                '  <button type="button" class="btn btn-outline-danger w-100 remove-participant-btn"><i class="bi bi-trash"></i></button>',
                '</div>'
            ].join('');

            return wrapper;
        }

        function addParticipantRow() {
            if (!participantsContainer) {
                return;
            }

            participantsContainer.appendChild(buildParticipantRow(participantIndex));
            participantIndex += 1;
            updateParticipantCount();
        }

        function getEditParticipantRows() {
            if (!editParticipantsContainer) {
                return [];
            }

            return Array.from(editParticipantsContainer.querySelectorAll('.edit-participant-row'));
        }

        function buildEditParticipantRow(index) {
            const wrapper = document.createElement('div');
            wrapper.className = 'row g-2 edit-participant-row';
            wrapper.setAttribute('data-index', index.toString());
            wrapper.innerHTML = [
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][name]" class="form-control" placeholder="Participant Name" required>',
                '</div>',
                '<div class="col-md-5">',
                '  <input type="text" name="participants[' + index + '][contact_info]" class="form-control" placeholder="Contact Info (Phone/Email)" required>',
                '</div>',
                '<div class="col-md-2">',
                '  <button type="button" class="btn btn-outline-danger w-100 edit-remove-participant-btn"><i class="bi bi-trash"></i></button>',
                '</div>'
            ].join('');

            return wrapper;
        }

        function addEditParticipantRow() {
            if (!editParticipantsContainer) {
                return;
            }

            editParticipantsContainer.appendChild(buildEditParticipantRow(editParticipantIndex));
            editParticipantIndex += 1;
        }

        function calculateTotalAmount(isFirm) {
            if (!totalAmountInput || !courseSelect) {
                return;
            }

            const selectedCourseOption = courseSelect.options[courseSelect.selectedIndex];
            const price = selectedCourseOption ? parseFloat(selectedCourseOption.getAttribute('data-course-price') || '0') : 0;

            if (!isFirm) {
                totalAmountInput.value = '';
                return;
            }

            const participantCount = participantCountInput ? parseInt(participantCountInput.value || '0', 10) : 0;
            const total = price * (participantCount > 0 ? participantCount : 0);

            totalAmountInput.value = total > 0 ? total.toFixed(2) : '';
        }

        function applyUserBasedFiltering() {
            if (!userSelect || !courseSelect) {
                return;
            }

            const selectedUserOption = userSelect.options[userSelect.selectedIndex];
            const selectedRole = selectedUserOption ? selectedUserOption.getAttribute('data-user-role') : null;
            const isFirm = selectedRole === 'firm';
            const expectedCourseType = isFirm ? 'firm_only' : 'student_only';

            firmFields.forEach(function (field) {
                field.classList.toggle('d-none', !isFirm);
            });

            getParticipantRows().forEach(function (row) {
                const inputs = row.querySelectorAll('input');
                inputs.forEach(function (input) {
                    input.required = isFirm;
                });
            });

            if (isFirm && getParticipantRows().length === 0) {
                addParticipantRow();
            }

            updateParticipantCount();

            const courseOptions = courseSelect.querySelectorAll('option[data-course-type]');
            let hasSelectedVisibleOption = false;

            courseOptions.forEach(function (option) {
                const matches = option.getAttribute('data-course-type') === expectedCourseType;
                option.hidden = !matches;

                if (!matches && option.selected) {
                    option.selected = false;
                }

                if (matches && option.selected) {
                    hasSelectedVisibleOption = true;
                }
            });

            if (!hasSelectedVisibleOption) {
                const firstVisibleOption = Array.from(courseOptions).find(function (option) {
                    return !option.hidden;
                });

                if (firstVisibleOption) {
                    firstVisibleOption.selected = true;
                }
            }

            calculateTotalAmount(isFirm);
        }

        if (userSelect && courseSelect) {
            applyUserBasedFiltering();
            userSelect.addEventListener('change', applyUserBasedFiltering);
            courseSelect.addEventListener('change', function () {
                const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                calculateTotalAmount(isFirm);
            });
        }

        if (addParticipantBtn) {
            addParticipantBtn.addEventListener('click', function () {
                addParticipantRow();

                if (!userSelect) {
                    return;
                }

                const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                calculateTotalAmount(isFirm);
            });
        }

        if (participantsContainer) {
            participantsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.remove-participant-btn');
                if (!removeButton) {
                    return;
                }

                const rows = getParticipantRows();
                if (rows.length <= 1) {
                    return;
                }

                const row = removeButton.closest('.participant-row');
                if (row) {
                    row.remove();
                    updateParticipantCount();

                    if (!userSelect) {
                        return;
                    }

                    const selectedUserOption = userSelect.options[userSelect.selectedIndex];
                    const isFirm = selectedUserOption && selectedUserOption.getAttribute('data-user-role') === 'firm';
                    calculateTotalAmount(isFirm);
                }
            });

            updateParticipantCount();
        }

        if (editAddParticipantBtn) {
            editAddParticipantBtn.addEventListener('click', function () {
                addEditParticipantRow();
            });
        }

        if (editParticipantsContainer) {
            editParticipantsContainer.addEventListener('click', function (event) {
                const removeButton = event.target.closest('.edit-remove-participant-btn');
                if (!removeButton) {
                    return;
                }

                const rows = getEditParticipantRows();
                if (rows.length <= 1) {
                    return;
                }

                const row = removeButton.closest('.edit-participant-row');
                if (row) {
                    row.remove();
                }
            });
        }
    });
</script>

</x-admin.layout>
