
    
<x-admin.layout active="students">

 @php
    $isEdit = isset($editStudent) && !isset($viewOnly);
    $isView = isset($editStudent) && isset($viewOnly);
    $isCreate = !isset($editStudent);
@endphp    


    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
        <i class="bi bi-plus-lg"></i> Add New Student
    </button>

    <div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Registered Students</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Qualification</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="{{ $student->photo ? asset('storage/' . $student->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($student->user->name) }}" 
                                     class="rounded-circle me-2" width="35" height="35" alt="Profile">
                                {{ $student->user->name }}
                            </div>
                        </td>
                        <td>{{ $student->user->email }}</td>
                        <td>{{ $student->phone }}</td>
                        <td><span class="badge bg-info text-dark">{{ $student->current_qualification }}</span></td>
                        <td>
                            @if($student->user->status == 'active')
                                <span class="badge bg-success-subtle text-success border border-success">Active</span>
                            @elseif($student->user->status == 'blocked')
                                <span class="badge bg-danger-subtle text-danger border border-danger">blocked</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm gap-1">
                                <a href="{{ route('admin.students.show', $student->id) }}">
                                <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </button>
                                </a>
                                <a href="{{ route('admin.students.edit', $student->id) }}">
                                <button class="btn btn-sm btn-outline-warning" title="Edit Student">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </a>
                            <a href="{{ route('admin.students.edit', [$student->id, 'mode' => 'delete']) }}">
                                <button class="btn btn-sm btn-outline-danger" title="Delete Student">
                                    <i class="bi bi-trash"></i>
                                </button>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No students registered yet. Click "Add New Student" to begin.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>



  
    <div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                   <h5 class="modal-title">
    {{ $isEdit ? 'Edit Student: ' . $editStudent->user->name : ($isView ? 'Student Profile: ' . $editStudent->user->name : 'Add New Student') }}
</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ isset($editStudent) ? route('admin.students.update', $editStudent->id) : route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($editStudent))
                        @method('PATCH')
                    @endif
                    <div class="modal-body">
                        
                        {{-- 1. Profile Image Preview (Visible in Edit/View) --}}
                            @if(!$isCreate)
                                <div class="text-center mb-4">
                                    <img src="{{ $editStudent->photo ? asset('storage/' . $editStudent->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($editStudent->user->name) }}" 
                                        class="rounded-circle img-thumbnail shadow-sm" 
                                        style="width: 120px; height: 120px; object-fit: cover;">
                                    @if($isView)
                                        <h4 class="mt-2">{{ $editStudent->user->name }}</h4>
                                        {{-- <span class="badge bg-success">Active Student</span> --}}
                                    @endif
                                </div>
                                
                            @endif




                        <div class="row g-3">
                            <h6 class="border-bottom pb-2">Account Information</h6>
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editStudent->user->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $editStudent->user->email ?? '') }}" {{ $isView ? 'disabled' : '' }}>
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

                            <h6 class="border-bottom pb-2 mt-4">Personal Details</h6>
                            <div class="col-md-4">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $editStudent->phone ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', isset($editStudent) ? \Carbon\Carbon::parse($editStudent->dob)->format('Y-m-d') : '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                               <select name="gender" class="form-select @error('gender') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender', $editStudent->gender ?? '') == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female" {{ old('gender', $editStudent->gender ?? '') == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                    <option value="other" {{ old('gender', $editStudent->gender ?? '') == 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Current Qualification</label>
                                <input type="text" name="current_qualification" class="form-control @error('current_qualification') is-invalid @enderror" value="{{ old('current_qualification', $editStudent->current_qualification ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                                @error('current_qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                            <label class="form-label">Account Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                <option value="active" {{ old('status', $editStudent->user->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blocked" {{ old('status', $editStudent->user->status ?? '') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                            @if (!$isView)

                            <div class="col-md-4">
                                <label class="form-label">Profile Photo</label>
                                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif

                            <div class="col-md-12">
                                <label class="form-label">Full Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" {{ $isView ? 'disabled' : '' }}>{{ old('address', $editStudent->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                  
                        
                  
                    <div class="modal-footer">
                                @if(isset($editStudent))
                                    {{-- If editing, link physically back to the index --}}
                                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">Cancel</a>
                                @else
                                    {{-- If creating, just close the modal normally --}}
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                @endif
                                
                                  @if (!$isView)
                                <button type="submit" class="btn btn-primary">
                                    {{ isset($editStudent) ? 'Update Student' : 'Create Student Account' }}
                                </button>
                                 @endif
                    </div>
                     
                </form>
            </div>
        </div>
    </div>



@if(isset($deleteStudent) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Student</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteStudent->user->name }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('admin.students.destroy', $deleteStudent->id) }}" method="POST">
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
                window.location.href = "{{ route('admin.students.index') }}";
            }
        });

        });
    </script>
@endif



     <x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addStudentModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif
{{-- edit modal show --}}
@if(isset($editStudent))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
            editModal.show();
       
        var myModalElement = document.getElementById('addStudentModal');
        
        // Listen for the modal being hidden
        myModalElement.addEventListener('hidden.bs.modal', function () {
            // Check if we are currently on an "edit" URL
            if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                // Redirect back to the main index to clean the URL
                window.location.href = "{{ route('admin.students.index') }}";
            }
        });

    });

   
    </script>
@endif
   
</script>

    </x-admin.layout>