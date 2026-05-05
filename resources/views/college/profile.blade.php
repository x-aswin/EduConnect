<x-college.layout active="profile">
    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            {{-- Profile Header --}}
            <div class="card-placeholder mb-3">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; overflow: hidden;">
                        @if (!empty($college->photo))
                            <img src="{{ asset('storage/' . $college->photo) }}" alt="College photo" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="bi bi-building fs-3 text-primary"></i>
                        @endif
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">{{ $college->institution_name }}</h5>
                        <small class="text-secondary">{{ $college->user->name ?? 'College' }}</small>
                    </div>
                </div>
            </div>

            {{-- Verification Status Card --}}
            <div class="card-placeholder mb-3 border-2 {{ !empty($college->verification_doc) ? 'border-success border-opacity-25' : 'border-warning border-opacity-25' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-shield-check me-2 {{ !empty($college->verification_doc) ? 'text-success' : 'text-warning' }}"></i>
                        Verification Status
                    </h6>
                    <span class="badge {{ !empty($college->verification_doc) ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ !empty($college->verification_doc) ? 'Verified' : 'Pending' }}
                    </span>
                </div>
                <p class="text-sm text-secondary mb-0">
                    @if (!empty($college->verification_doc))
                        <i class="bi bi-check-circle text-success me-1"></i> Your verification document has been uploaded.
                    @else
                        <i class="bi bi-exclamation-circle text-warning me-1"></i> Upload a verification document to complete your profile.
                    @endif
                </p>
            </div>

            {{-- Uploaded Documents Section --}}
            <div class="card-placeholder">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-files me-2"></i> Uploaded Documents
                </h6>
                
                @if (!empty($college->verification_doc))
                    @php
                        $verificationUrl = asset('storage/' . $college->verification_doc);
                        $verificationExt = strtolower(pathinfo($college->verification_doc, PATHINFO_EXTENSION));
                        $isPdf = $verificationExt === 'pdf';
                        $fileName = basename($college->verification_doc);
                    @endphp
                    <div class="border rounded-2 p-3 mb-3 bg-light-subtle">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <div class="flex-shrink-0">
                                @if ($isPdf)
                                    <i class="bi bi-file-pdf fs-4 text-danger"></i>
                                @else
                                    <i class="bi bi-file-image fs-4 text-info"></i>
                                @endif
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <p class="fw-semibold mb-0 text-truncate" title="{{ $fileName }}">Verification Document</p>
                                <small class="text-secondary">{{ $verificationExt }}</small>
                            </div>
                        </div>
                        <div class="btn-group w-100" role="group">
                            <a href="{{ $verificationUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View">
                                <i class="bi bi-eye"></i> View
                            </a>
                            <a href="{{ $verificationUrl }}" download class="btn btn-sm btn-outline-secondary" title="Download">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-3 bg-light rounded-2">
                        <i class="bi bi-inbox fs-3 text-secondary mb-2 d-block"></i>
                        <p class="text-secondary small mb-0">No documents uploaded yet</p>
                    </div>
                @endif
                
                <small class="text-secondary d-block mt-2">
                    <i class="bi bi-info-circle me-1"></i> Update your verification document in the form on the right
                </small>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card-placeholder">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
                    <div>
                        <h1 class="h3 fw-bold mb-1">Manage College Profile</h1>
                        <p class="mb-0 text-secondary">Update the institution details shown across the college portal.</p>
                    </div>
                    <a href="{{ route('college.dashboard') }}" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>

                <form action="{{ route('college.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="acronym" class="form-label">Institution Acronym</label>
                        <input id="acronym" name="acronym" type="text" class="form-control" value="{{ old('acronym', $college->user->name) }}" required>
                        @error('acronym')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="institution_name" class="form-label">Institution Name</label>
                        <input id="institution_name" name="institution_name" type="text" class="form-control" value="{{ old('institution_name', $college->institution_name) }}" required>
                        @error('institution_name')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="college_phone" class="form-label">College Phone</label>
                            <input id="college_phone" name="college_phone" type="text" class="form-control" value="{{ old('college_phone', $college->college_phone) }}" required>
                            @error('college_phone')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input id="website" name="website" type="url" class="form-control" value="{{ old('website', $college->website) }}" placeholder="https://example.com">
                            @error('website')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Contact Person Name</label>
                        <input id="contact_person" name="contact_person" type="text" class="form-control" value="{{ old('contact_person', $college->contact_person) }}" required>
                        @error('contact_person')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="designation" class="form-label">Designation</label>
                            <select id="designation" name="designation" class="form-select" required>
                                <option value="">-- Select Designation --</option>
                                <option value="Principal" {{ old('designation', $college->designation) == 'Principal' ? 'selected' : '' }}>Principal</option>
                                <option value="HOD" {{ old('designation', $college->designation) == 'HOD' ? 'selected' : '' }}>HOD</option>
                                <option value="Placement Officer" {{ old('designation', $college->designation) == 'Placement Officer' ? 'selected' : '' }}>Placement Officer</option>
                                <option value="Coordinator" {{ old('designation', $college->designation) == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                                <option value="Other" {{ old('designation', $college->designation) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('designation')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input id="contact_number" name="contact_number" type="text" class="form-control" value="{{ old('contact_number', $college->contact_number) }}" required>
                            @error('contact_number')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <hr>

                    <div class="mb-3">
                        <label for="address" class="form-label">College Address</label>
                        <textarea id="address" name="address" class="form-control" rows="4" required>{{ old('address', $college->address) }}</textarea>
                        @error('address')
                            <div class="text-danger small mt-2">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="photo" class="form-label">College Logo / Photo</label>
                            <input id="photo" name="photo" type="file" class="form-control" accept="image/*">
                            <p class="text-sm text-secondary mt-2">Leave blank to keep the current image.</p>
                            @error('photo')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="verification_doc" class="form-label">Verification Document</label>
                            <input id="verification_doc" name="verification_doc" type="file" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <p class="text-sm text-secondary mt-2">Upload a new file only if you want to replace the current verification document.</p>
                            @error('verification_doc')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-wrap mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Save Changes</button>
                        <a href="{{ route('college.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <x-toast />
</x-college.layout>