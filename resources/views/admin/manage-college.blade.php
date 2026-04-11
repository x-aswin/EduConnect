<x-admin.layout active="colleges">

 @php
    $isEdit = isset($editCollege) && !isset($viewOnly);
    $isView = isset($editCollege) && isset($viewOnly);
    $isCreate = !isset($editCollege);
@endphp    


    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCollegeModal">
        <i class="bi bi-plus-lg"></i> Add New College
    </button>

    <div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Registered Colleges</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Acronym / Institution Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Website</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($colleges as $college)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $college->photo ? asset('storage/' . $college->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($college->institution_name ?? 'Unknown College') }}" 
                                     class="rounded me-2" width="42" height="42" style="object-fit: cover;" alt="Profile">
                                {{ $college->user->name ?? 'Unknown College' }} / 
                                {{ $college->institution_name ?? 'Unknown College' }}
                            </div>
                        </td>
                        <td>{{ $college->user->email ?? 'N/A' }}</td>
                        <td>{{ $college->college_phone ?? 'N/A' }}</td>
                        <td><a href="{{ $college->website ?? '#' }}" target="_blank" class="text-decoration-underline">{{ $college->website ?? 'N/A' }}</a></td>
                        <td>
                            @if(($college->user->status ?? null) == 'active')
                                <span class="badge bg-success-subtle text-success border border-success">Active</span>
                            @elseif(($college->user->status ?? null) == 'blocked')
                                <span class="badge bg-danger-subtle text-danger border border-danger">blocked</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm gap-1">
                                <a href="{{ route('admin.colleges.show', $college->id) }}">
                                <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.colleges.edit', $college->id) }}">
                                <button class="btn btn-sm btn-outline-warning" title="Edit College">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </a>
                            <a href="{{ route('admin.colleges.edit', [$college->id, 'mode' => 'delete']) }}">
                                <button class="btn btn-sm btn-outline-danger" title="Delete College">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No colleges registered yet. Click "Add New College" to begin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



  
    <div class="modal fade" id="addCollegeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title">
    {{ $isEdit ? 'Edit College: ' . $editCollege->institution_name : ($isView ? 'College Profile: ' . $editCollege->institution_name : 'Add New College') }}
</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($editCollege) ? route('admin.colleges.update', $editCollege->id) : route('admin.colleges.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editCollege))
                        @method('PATCH')
                    @endif
                    <div class="modal-body">
                        
                        {{-- 1. Profile Image Preview (Visible in Edit/View) --}}
                            @if(!$isCreate)
                                <div class="text-center mb-4">
                                    <img src="{{ $editCollege->photo ? asset('storage/' . $editCollege->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($editCollege->institution_name) }}" 
                                        class="rounded img-thumbnail shadow-sm" 
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                    @if($isView)
                                        <h4 class="mt-2">{{ $editCollege->institution_name }}</h4>
                                        {{-- <span class="badge bg-success">Active College</span> --}}
                                    @endif
                                </div>
                                
                            @endif

                            @if(!$isCreate && !empty($editCollege->verification_doc))
                                @php
                                    $verificationUrl = asset('storage/' . $editCollege->verification_doc);
                                    $verificationExt = strtolower(pathinfo($editCollege->verification_doc, PATHINFO_EXTENSION));
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
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editCollege->user->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $editCollege->user->email ?? '') }}" {{ $isView ? 'disabled' : '' }}>
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
                                    <option value="pending" {{ old('status', $editCollege->user->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ old('status', $editCollege->user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="blocked" {{ old('status', $editCollege->user->status ?? '') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            

                            <h6 class="border-bottom pb-2 mt-4">Institution Details</h6>
                            <div class="col-md-4">
                                <label class="form-label">Institution Name</label>
                                <input type="text" name="institution_name" class="form-control @error('institution_name') is-invalid @enderror" value="{{ old('institution_name', $editCollege->institution_name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('institution_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">College Phone</label>
                                <input type="text" name="college_phone" class="form-control @error('college_phone') is-invalid @enderror" value="{{ old('college_phone', $editCollege->college_phone ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('college_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Website</label>
                                <input type="url" name="website" class="form-control @error('website') is-invalid @enderror" value="{{ old('website', $editCollege->website ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" {{ $isView ? 'disabled' : '' }}>{{ old('address', $editCollege->address ?? '') }}</textarea>
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
                                <label class="form-label">College Photo</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @endif

                            <h6 class="border-bottom pb-2 mt-4">Contact Person</h6>
                            <div class="col-md-4">
                                <label class="form-label">Contact Person</label>
                                <input type="text" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" value="{{ old('contact_person', $editCollege->contact_person ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Designation</label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror" value="{{ old('designation', $editCollege->designation ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact_number" class="form-control @error('contact_number') is-invalid @enderror" value="{{ old('contact_number', $editCollege->contact_number ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                  
                        
                  
                    <div class="modal-footer">
                                @if(isset($editCollege))
                                    {{-- If editing, link physically back to the index --}}
                                    <a href="{{ route('admin.colleges.index') }}" class="btn btn-secondary">Cancel</a>
                                @else
                                    {{-- If creating, just close the modal normally --}}
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif
                                
                                  @if (!$isView)
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editCollege) ? 'Update College' : 'Create College Account' }}
                                </button>
                                 @endif
                    </div>
                     
                </form>
            </div>
        </div>
    </div>



@if(isset($deleteCollege) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete College</h5>
                    <a href="{{ route('admin.colleges.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteCollege->institution_name ?? 'Unknown College' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.colleges.destroy', $deleteCollege->id) }}" method="POST">
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
        
        // Listen for the modal being hidden
        myModalDelete.addEventListener('hidden.bs.modal', function () {
            // Check if we are currently on an "edit" URL
            if (window.location.pathname.includes('/edit')) {
                // Redirect back to the main index to clean the URL
                window.location.href = "{{ route('admin.colleges.index') }}";
            }
        });

        });
    </script>
@endif



     <x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addCollegeModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif
{{-- edit modal show --}}
@if(isset($editCollege))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addCollegeModal'));
            editModal.show();
       
        var myModalElement = document.getElementById('addCollegeModal');
        
        // Listen for the modal being hidden
        myModalElement.addEventListener('hidden.bs.modal', function () {
            // Check if we are currently on an "edit" URL
            if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                // Redirect back to the main index to clean the URL
                window.location.href = "{{ route('admin.colleges.index') }}";
            }
        });

    });

   
    </script>
@endif

    </x-admin.layout>