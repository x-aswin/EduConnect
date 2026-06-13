<x-firm.layout title="Complete Profile - EduConnect" active="profile">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-building-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Complete Your Firm Profile</h4>
                            <p class="text-muted small">Provide details to complete your organisation account</p>
                        </div>

                        <form action="{{ route('firm.complete.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- Organization Name -->
                            <div class="mb-3">
                                <label for="org_name" class="form-label fw-semibold small text-secondary">Organization Name</label>
                                <input type="text" id="org_name" name="org_name"
                                       class="form-control rounded-pill py-2 @error('org_name') is-invalid @enderror"
                                       value="{{ old('org_name', $firm->org_name ?? '') }}" required>
                                @error('org_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact Person & Phone -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_person" class="form-label fw-semibold small text-secondary">Contact Person</label>
                                    <input type="text" id="contact_person" name="contact_person"
                                           class="form-control rounded-pill py-2 @error('contact_person') is-invalid @enderror"
                                           value="{{ old('contact_person', $firm->contact_person ?? '') }}" required>
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-semibold small text-secondary">Phone</label>
                                    <input type="text" id="phone" name="phone"
                                           class="form-control rounded-pill py-2 @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', $firm->phone ?? '') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Designation -->
                            <div class="mb-3">
                                <label for="designation" class="form-label fw-semibold small text-secondary">Designation</label>
                                <input type="text" id="designation" name="designation"
                                       class="form-control rounded-pill py-2 @error('designation') is-invalid @enderror"
                                       value="{{ old('designation', $firm->designation ?? '') }}"
                                       placeholder="e.g., HR Manager, Training Head">
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold small text-secondary">Address</label>
                                <textarea id="address" name="address" rows="3"
                                          class="form-control rounded-4 py-2 @error('address') is-invalid @enderror">{{ old('address', $firm->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Photo & Verification Document -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="photo" class="form-label fw-semibold small text-secondary">Organisation Logo / Photo</label>
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           class="form-control rounded-pill py-2 @error('photo') is-invalid @enderror">
                                    @error('photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="verification_doc" class="form-label fw-semibold small text-secondary">Verification Document</label>
                                    <input type="file" id="verification_doc" name="verification_doc"
                                           class="form-control rounded-pill py-2 @error('verification_doc') is-invalid @enderror"
                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                    <div class="form-text small">Allowed: PDF or image (max 5MB)</div>
                                    @error('verification_doc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm mt-2">
                                <i class="bi bi-check2-circle me-2"></i> Complete Profile
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-firm.layout>