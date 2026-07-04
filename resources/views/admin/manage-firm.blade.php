<x-admin.layout active="firms">

 @php
    $isEdit = isset($editFirm) && !isset($viewOnly);
    $isView = isset($editFirm) && isset($viewOnly);
    $isCreate = !isset($editFirm);
@endphp

<x-admin.filter-card
    title="Search Firms"
    action="{{ route('admin.firms.index') }}"
    search-value="{{ request('search') }}"
    search-placeholder="Search by firm, type, email, phone, or contact person"
    reset-url="{{ route('admin.firms.index') }}"
>
    <x-slot name="filters">
        <div class="col-lg-3">
            <label class="form-label fw-semibold">Status</label>
            <select name="status" class="form-select">
                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
            </select>
        </div>
    </x-slot>
</x-admin.filter-card>

    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addFirmModal">
        <i class="bi bi-plus-lg"></i> Add New Firm
    </button>

    <div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Registered Firms</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Firm / Organization</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($firms as $firm)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $firm->photo ? asset('storage/' . $firm->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($firm->org_name ?? 'Unknown Firm') }}"
                                     class="rounded me-2" width="42" height="42" style="object-fit: cover;" alt="Profile">
                                {{ $firm->user->name ?? 'Unknown Firm' }} /
                                {{ $firm->org_name ?? 'Unknown Firm' }}
                            </div>
                        </td>
                        <td>{{ $firm->user->email ?? 'N/A' }}</td>
                        <td>{{ $firm->phone ?? 'N/A' }}</td>
                        <td><span class="badge bg-info-subtle text-info border border-info">{{ $firm->org_type ?? 'N/A' }}</span></td>
                        <td>
                            @if(($firm->user->status ?? null) == 'active')
                                <span class="badge bg-success-subtle text-success border border-success">Active</span>
                            @elseif(($firm->user->status ?? null) == 'blocked')
                                <span class="badge bg-danger-subtle text-danger border border-danger">Blocked</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm gap-1">
                                <a href="{{ route('admin.firms.show', $firm->id) }}">
                                <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.firms.edit', $firm->id) }}">
                                <button class="btn btn-sm btn-outline-warning" title="Edit Firm">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </a>
                            <a href="{{ route('admin.firms.edit', [$firm->id, 'mode' => 'delete']) }}">
                                <button class="btn btn-sm btn-outline-danger" title="Delete Firm">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No firms registered yet. Click "Add New Firm" to begin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="modal fade" id="addFirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"><div class="modal-content">
                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title">
    {{ $isEdit ? 'Edit Firm: ' . $editFirm->org_name : ($isView ? 'Firm Profile: ' . $editFirm->org_name : 'Add New Firm') }}
</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($editFirm) ? route('admin.firms.update', $editFirm->id) : route('admin.firms.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editFirm))
                        @method('PATCH')
                    @endif
                    <div class="modal-body">
                        @if(!$isCreate)
                            <div class="text-center mb-4">
                                <img src="{{ $editFirm->photo ? asset('storage/' . $editFirm->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($editFirm->org_name) }}"
                                    class="rounded img-thumbnail shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                @if($isView)
                                    <h4 class="mt-2">{{ $editFirm->org_name }}</h4>
                                @endif
                            </div>
                        @endif

                        @if(!$isCreate && !empty($editFirm->verification_doc))
                            @php
                                $verificationUrl = asset('storage/' . $editFirm->verification_doc);
                                $verificationExt = strtolower(pathinfo($editFirm->verification_doc, PATHINFO_EXTENSION));
                                $isPdfDoc = $verificationExt === 'pdf';
                            @endphp
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2">Verification Document Preview</h6>
                                <div class="border rounded p-2 bg-light-subtle">
                                    @if($isPdfDoc)
                                        <iframe src="{{ $verificationUrl }}" title="Verification Document" style="width: 100%; height: 340px; border: 0;"></iframe>
                                    @else
                                        <img src="{{ $verificationUrl }}" class="img-fluid rounded" style="max-height: 340px; width: 100%; object-fit: contain;" alt="Verification Document">
                                    @endif
                                    <div class="mt-2 text-end">
                                        <a href="{{ $verificationUrl }}" target="_blank" class="btn btn-sm btn-outline-primary">Open Document</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <h6 class="border-bottom pb-2">Account Information</h6>
                            <div class="col-md-6">
                                <label class="form-label">Acronym</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editFirm->user->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $editFirm->user->email ?? '') }}" {{ $isView ? 'disabled' : '' }}>
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
                                    <option value="pending" {{ old('status', $editFirm->user->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status', $editFirm->user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="blocked" {{ old('status', $editFirm->user->status ?? '') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <h6 class="border-bottom pb-2 mt-4">Firm Details</h6>
                            <div class="col-md-6">
                                <label class="form-label">Organization Name</label>
                                <input type="text" name="org_name" class="form-control @error('org_name') is-invalid @enderror" value="{{ old('org_name', $editFirm->org_name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('org_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Organization Type</label>
                                <input type="text" name="org_type" class="form-control @error('org_type') is-invalid @enderror" value="{{ old('org_type', $editFirm->org_type ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('org_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" {{ $isView ? 'disabled' : '' }}>{{ old('address', $editFirm->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if (!$isView)
                            <div class="col-md-8">
                                <label class="form-label">Verification Document</label>
                                <input type="file" name="verification_doc" class="form-control @error('verification_doc') is-invalid @enderror">
                                @error('verification_doc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Firm Photo</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <h6 class="border-bottom pb-2 mt-4">Contact Person</h6>
                            <div class="col-md-4">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $editFirm->contact_person ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $editFirm->designation ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $editFirm->phone ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                                @if(isset($editFirm))
                                    <a href="{{ route('admin.firms.index') }}" class="btn btn-secondary">Cancel</a>
                                @else
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif

                                @if (!$isView)
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editFirm) ? 'Update Firm' : 'Create Firm Account' }}
                                </button>
                                @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

@if(isset($deleteFirm) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Firm</h5>
                    <a href="{{ route('admin.firms.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteFirm->org_name ?? 'Unknown Firm' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.firms.destroy', $deleteFirm->id) }}" method="POST">
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
                    window.location.href = "{{ route('admin.firms.index') }}";
                }
            });

        });
    </script>
@endif

<x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addFirmModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif

@if(isset($editFirm))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addFirmModal'));
            editModal.show();

            var myModalElement = document.getElementById('addFirmModal');
            myModalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ route('admin.firms.index') }}";
                }
            });
        });
    </script>
@endif

</x-admin.layout>
