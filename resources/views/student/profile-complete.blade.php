<x-guest.layout title="Complete Your Profile - EduConnect" active="profile" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-badge-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Complete Your Student Profile</h4>
                            <p class="text-muted small">Fill in the details below to get started</p>
                        </div>

                        <form action="{{ route('student.complete.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label for="full_name" class="form-label fw-semibold small text-secondary">Full Name</label>
                                <input type="text" id="full_name" name="full_name" 
                                       class="form-control rounded-pill py-2 @error('full_name') is-invalid @enderror"
                                       value="{{ old('full_name', $student->full_name ?? auth()->user()->name) }}" required>
                                @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone & DOB -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold small text-secondary">Phone Number</label>
                                    <input type="text" id="phone" name="phone" 
                                           class="form-control rounded-pill py-2 @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $student->phone ?? '') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="dob" class="form-label fw-semibold small text-secondary">Date of Birth</label>
                                    <input type="date" id="dob" name="dob" 
                                           class="form-control rounded-pill py-2 @error('dob') is-invalid @enderror"
                                           value="{{ old('dob', $student->dob ?? '') }}" required>
                                    @error('dob')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Gender -->
                            <div class="mb-3">
                                <label for="gender" class="form-label fw-semibold small text-secondary">Gender</label>
                                <select id="gender" name="gender" class="form-select rounded-pill py-2 @error('gender') is-invalid @enderror" required>
                                    <option value="">-- Select Gender --</option>
                                    <option value="male"   {{ old('gender', $student->gender ?? '') == 'male'   ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other"  {{ old('gender', $student->gender ?? '') == 'other'  ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Current Qualification -->
                            <div class="mb-3">
                                <label for="current_qualification" class="form-label fw-semibold small text-secondary">Current Qualification</label>
                                <select id="current_qualification" name="current_qualification" class="form-select rounded-pill py-2 @error('current_qualification') is-invalid @enderror" required>
                                    <option value="">-- Select Qualification --</option>
                                    <option value="+2"   {{ old('current_qualification', $student->current_qualification ?? '') == '+2'   ? 'selected' : '' }}>Higher Secondary (+2)</option>
                                    <option value="BCA"  {{ old('current_qualification', $student->current_qualification ?? '') == 'BCA'  ? 'selected' : '' }}>BCA</option>
                                    <option value="BSc"  {{ old('current_qualification', $student->current_qualification ?? '') == 'BSc'  ? 'selected' : '' }}>BSc Computer Science</option>
                                    <option value="Other"{{ old('current_qualification', $student->current_qualification ?? '') == 'Other'? 'selected' : '' }}>Other</option>
                                </select>
                                @error('current_qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold small text-secondary">Permanent Address</label>
                                <textarea id="address" name="address" rows="3" 
                                          class="form-control rounded-4 py-2 @error('address') is-invalid @enderror" required>{{ old('address', $student->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Photo Upload -->
                            <div class="mb-4">
                                <label for="photo" class="form-label fw-semibold small text-secondary">Profile Photo</label>
                                <input type="file" id="photo" name="photo" accept="image/*" 
                                       class="form-control rounded-pill py-2 @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Verification Document Upload -->
                            <div class="mb-4">
                                <label for="verification_doc" class="form-label fw-semibold small text-secondary">Verification ID Document (Aadhaar, College ID, etc.)</label>
                                <input type="file" id="verification_doc" name="verification_doc" accept="image/*,application/pdf" 
                                       class="form-control rounded-pill py-2 @error('verification_doc') is-invalid @enderror">
                                @error('verification_doc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small text-muted ms-2">Upload an image or a PDF file (Max 2MB)</div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-check2-circle me-2"></i> Save Profile & Continue
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest.layout>