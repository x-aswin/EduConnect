<x-admin.layout active="mentors">

@php
    $isEdit = isset($editMentor) && !isset($viewOnly);
    $isView = isset($editMentor) && isset($viewOnly);
    $isCreate = !isset($editMentor);
@endphp

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMentorModal">
        <i class="bi bi-plus-lg"></i> Add New Mentor
    </button>

    <div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Registered Mentors</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>College</th>
                        <th>Expertise</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mentors as $mentor)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $mentor->photo ? asset('storage/' . $mentor->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($mentor->user->name ?? 'Unknown Mentor') }}"
                                     class="rounded-circle me-2" width="35" height="35" alt="Profile" style="object-fit: cover;" alt="Profile">
                                {{ $mentor->user->name ?? 'Unknown Mentor' }}
                            </div>
                        </td>
                        <td>{{ $mentor->user->email ?? 'N/A' }}</td>
                        <td>{{ $mentor->college->institution_name ?? 'N/A' }}</td>
                        <td><span class="badge bg-info-subtle text-info border border-info">{{ $mentor->expertise ?? 'N/A' }}</span></td>
                        <td>
                            @if(($mentor->user->status ?? null) == 'active')
                                <span class="badge bg-success-subtle text-success border border-success">Active</span>
                            @elseif(($mentor->user->status ?? null) == 'blocked')
                                <span class="badge bg-danger-subtle text-danger border border-danger">Blocked</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm gap-1">
                                <a href="{{ route('admin.mentors.show', $mentor->id) }}">
                                <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.mentors.edit', $mentor->id) }}">
                                <button class="btn btn-sm btn-outline-warning" title="Edit Mentor">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.mentors.edit', [$mentor->id, 'mode' => 'delete']) }}">
                                    <button class="btn btn-sm btn-outline-danger" title="Delete Mentor">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No mentors registered yet. Click "Add New Mentor" to begin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="modal fade" id="addMentorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"><div class="modal-content">
                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title">
    {{ $isEdit ? 'Edit Mentor: ' . $editMentor->user->name : ($isView ? 'Mentor Profile: ' . $editMentor->user->name : 'Add New Mentor') }}
</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($editMentor) ? route('admin.mentors.update', $editMentor->id) : route('admin.mentors.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editMentor))
                        @method('PATCH')
                    @endif
                    <div class="modal-body">
                        @if(!$isCreate)
                            <div class="text-center mb-4">
                                <img src="{{ $editMentor->photo ? asset('storage/' . $editMentor->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($editMentor->user->name) }}"
                                    class="rounded-circle img-thumbnail shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                @if($isView)
                                    <h4 class="mt-2">{{ $editMentor->user->name }}</h4>
                                @endif
                            </div>
                        @endif

                        <div class="row g-3">
                            <h6 class="border-bottom pb-2">Account Information</h6>
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editMentor->user->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $editMentor->user->email ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if(!$isView)
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">Account Status</label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="pending" {{ old('status', $editMentor->user->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status', $editMentor->user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="blocked" {{ old('status', $editMentor->user->status ?? '') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <h6 class="border-bottom pb-2 mt-4">Mentor Details</h6>
                            <div class="col-md-6">
                                <label class="form-label">Associated College</label>
                                <select name="college_id" class="form-select @error('college_id') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="">Select College</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ old('college_id', $editMentor->college_id ?? '') == $college->id ? 'selected' : '' }}>
                                            {{ $college->institution_name }} ({{ $college->user->name ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('college_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Qualification</label>
                                <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $editMentor->qualification ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Expertise</label>
                                <input type="text" name="expertise" class="form-control @error('expertise') is-invalid @enderror" value="{{ old('expertise', $editMentor->expertise ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('expertise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (!$isView)
                            <div class="col-md-6">
                                <label class="form-label">Mentor Photo</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12">
                                <label class="form-label">Bio</label>
                                <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3" {{ $isView ? 'disabled' : '' }}>{{ old('bio', $editMentor->bio ?? '') }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                                @if(isset($editMentor))
                                    <a href="{{ route('admin.mentors.index') }}" class="btn btn-secondary">Cancel</a>
                                @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif

                                @if (!$isView)
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editMentor) ? 'Update Mentor' : 'Create Mentor Account' }}
                                </button>
                                @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

@if(isset($deleteMentor) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Mentor</h5>
                    <a href="{{ route('admin.mentors.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteMentor->user->name ?? 'Unknown Mentor' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.mentors.destroy', $deleteMentor->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            var myModalDelete = document.getElementById('deleteModal');
            myModalDelete.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit')) {
                    window.location.href = "{{ route('admin.mentors.index') }}";
                }
            });
        });
    </script>
@endif

<x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addMentorModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif

@if(isset($editMentor))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addMentorModal'));
            editModal.show();

            var myModalElement = document.getElementById('addMentorModal');
            myModalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ route('admin.mentors.index') }}";
                }
            });
        });
    </script>
@endif

</x-admin.layout>
