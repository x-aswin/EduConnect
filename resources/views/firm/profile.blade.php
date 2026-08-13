<x-firm.layout title="Profile - EduConnect" active="profile">
    <div class="container-fluid py-4 px-md-4">
        
        {{-- Header Section --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Manage Firm Profile</h3>
                <p class="text-muted mb-0">Update your organization details, contact information, and verification documents.</p>
            </div>
        </div>

        {{-- Session Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center mb-1 fw-semibold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Please correct the following errors:
                </div>
                <ul class="mb-0 ps-3 small">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('firm.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row g-4">
                
                {{-- Left Column: Logo & Media Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold card-title mb-4 text-start">Organization Branding</h5>
                            
                            {{-- Profile Photo Avatar Preview --}}
                            <div class="position-relative d-inline-block mb-3">
                                <img id="photo-preview" 
                                     src="{{ !empty($firm->photo) ? asset('storage/' . ltrim($firm->photo, '/')) : asset('images/default-company.png') }}" 
                                     alt="Firm Logo" 
                                     class="rounded-circle img-thumbnail shadow-sm object-fit-cover" 
                                     style="width: 140px; height: 140px;">
                                <label for="photo-input" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow" title="Change Logo">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            
                            <input type="file" id="photo-input" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                            <p class="text-muted small mb-0">Allowed: JPG, PNG, WEBP (Max 2MB)</p>

                            <hr class="my-4 text-muted opacity-25">

                            {{-- Verification Document Section --}}
                            <div class="text-start">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-file-earmark-check me-1 text-primary"></i> Verification Document
                                </label>
                                <input type="file" name="verification_doc" class="form-control form-control-sm mb-2" accept=".pdf,image/*">
                                <small class="text-muted d-block mb-2 fs-7">Allowed formats: PDF or Image (max 5MB)</small>
                                
                                @if(!empty($firm->verification_doc))
                                    <div class="p-2 rounded bg-light border d-flex align-items-center justify-content-between">
                                        <div class="text-truncate me-2 small">
                                            <i class="bi bi-paperclip me-1 text-secondary"></i> Current Document
                                        </div>
                                        <a href="{{ asset('storage/' . ltrim($firm->verification_doc, '/')) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill text-nowrap px-2 py-1 small">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Main Profile Details --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold card-title mb-4">Organization Details</h5>
                            
                            {{-- Org Name --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Organization Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-building text-muted"></i></span>
                                    <input type="text" name="org_name" class="form-control border-start-0" value="{{ old('org_name', $firm->org_name ?? '') }}" placeholder="Enter Organization Name" required>
                                </div>
                            </div>

                            <div class="row g-3">
                                {{-- Contact Person --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Contact Person</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="contact_person" class="form-control border-start-0" value="{{ old('contact_person', $firm->contact_person ?? '') }}" placeholder="Full Name">
                                    </div>
                                </div>

                                {{-- Designation --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Designation</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-briefcase text-muted"></i></span>
                                        <input type="text" name="designation" class="form-control border-start-0" value="{{ old('designation', $firm->designation ?? '') }}" placeholder="e.g. HR Manager / Director">
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Phone Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                        <input type="text" name="phone" class="form-control border-start-0" value="{{ old('phone', $firm->phone ?? '') }}" placeholder="+91 00000 00000">
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $firm->email ?? $user->email ?? '') }}" placeholder="organization@email.com">
                                    </div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">Office Address</label>
                                <textarea name="address" class="form-control" rows="3" placeholder="Street, City, State, ZIP code">{{ old('address', $firm->address ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Actions Bar --}}
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light px-4 rounded-pill">Reset</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- Avatar Preview Script --}}
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('photo-preview');
                output.src = reader.result;
            }
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
</x-firm.layout>