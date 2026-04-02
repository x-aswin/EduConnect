<x-admin.layout active="students">
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
    <i class="bi bi-plus-lg"></i> Add New Student
</button>


<div class="modal fade" id="addStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Register New Student</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <h6 class="border-bottom pb-2">Account Information</h6>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>

                        <h6 class="border-bottom pb-2 mt-4">Personal Details</h6>
                        <div class="col-md-4">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Current Qualification (e.g. +2, BCA)</label>
                            <input type="text" name="current_qualification" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Profile Photo</label>
                            <input type="file" name="photo" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Student Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        var myModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
        myModal.show();
    </script>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
</x-admin.layout>