<x-guest.layout title="Complete Your College Profile - EduConnect" active="profile">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-building-fill fs-1 text-primary mb-3"></i>
                            <h4 class="fw-bold mb-1">Complete Your College Profile</h4>
                            <p class="text-muted small">Fill in the details below to finalise your institutional registration</p>
                        </div>

                        <form action="{{ route('college.complete.profile.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')

                            <!-- Institution Name -->
                            <div class="mb-3">
                                <label for="institution_name" class="form-label fw-semibold small text-secondary">Institution Name</label>
                                <input type="text" id="institution_name" name="institution_name"
                                       class="form-control rounded-pill py-2 @error('institution_name') is-invalid @enderror"
                                       value="{{ old('institution_name', $college->institution_name ?? $college->user->name ?? auth()->user()->name) }}" required>
                                @error('institution_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- College Phone & Website -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="college_phone" class="form-label fw-semibold small text-secondary">College Phone</label>
                                    <input type="text" id="college_phone" name="college_phone"
                                           class="form-control rounded-pill py-2 @error('college_phone') is-invalid @enderror"
                                           value="{{ old('college_phone', $college->college_phone ?? '') }}" required>
                                    @error('college_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="website" class="form-label fw-semibold small text-secondary">Website</label>
                                    <input type="url" id="website" name="website"
                                           class="form-control rounded-pill py-2 @error('website') is-invalid @enderror"
                                           value="{{ old('website', $college->website ?? '') }}" placeholder="https://example.com">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Person -->
                            <div class="mb-3">
                                <label for="contact_person" class="form-label fw-semibold small text-secondary">Contact Person Name</label>
                                <input type="text" id="contact_person" name="contact_person"
                                       class="form-control rounded-pill py-2 @error('contact_person') is-invalid @enderror"
                                       value="{{ old('contact_person', $college->contact_person ?? '') }}" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Designation & Contact Number -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="designation" class="form-label fw-semibold small text-secondary">Designation</label>
                                    <select id="designation" name="designation" class="form-select rounded-pill py-2 @error('designation') is-invalid @enderror" required>
                                        <option value="">-- Select Designation --</option>
                                        <option value="Principal" {{ old('designation', $college->designation ?? '') == 'Principal' ? 'selected' : '' }}>Principal</option>
                                        <option value="HOD" {{ old('designation', $college->designation ?? '') == 'HOD' ? 'selected' : '' }}>HOD</option>
                                        <option value="Placement Officer" {{ old('designation', $college->designation ?? '') == 'Placement Officer' ? 'selected' : '' }}>Placement Officer</option>
                                        <option value="Coordinator" {{ old('designation', $college->designation ?? '') == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                                        <option value="Other" {{ old('designation', $college->designation ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('designation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact_number" class="form-label fw-semibold small text-secondary">Contact Number</label>
                                    <input type="text" id="contact_number" name="contact_number"
                                           class="form-control rounded-pill py-2 @error('contact_number') is-invalid @enderror"
                                           value="{{ old('contact_number', $college->contact_number ?? '') }}" required>
                                    @error('contact_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-3">
                                <label for="address" class="form-label fw-semibold small text-secondary">College Address</label>
                                <textarea id="address" name="address" rows="3"
                                          class="form-control rounded-4 py-2 @error('address') is-invalid @enderror" required>{{ old('address', $college->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- College Photo -->
                            <div class="mb-3">
                                <label for="photo" class="form-label fw-semibold small text-secondary">College Logo/Photo</label>
                                <input type="file" id="photo" name="photo" accept="image/*"
                                       class="form-control rounded-pill py-2 @error('photo') is-invalid @enderror">
                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Verification Document -->
                            <div class="mb-4">
                                <label for="verification_doc" class="form-label fw-semibold small text-secondary">Verification Document</label>
                                <p class="text-muted small mb-2">Upload a document that verifies your institution (registration certificate, affiliation letter, etc.)</p>
                                <input type="file" id="verification_doc" name="verification_doc"
                                       class="form-control rounded-pill py-2 @error('verification_doc') is-invalid @enderror"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                @error('verification_doc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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