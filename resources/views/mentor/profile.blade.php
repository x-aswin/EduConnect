<x-mentor.layout title="Profile - EduConnect" active="profile">
    <div class="container-fluid py-4 px-md-4">
        
        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold mb-1">Manage Profile</h3>
                <p class="text-muted mb-0">Update your academic qualifications, area of expertise, and personal bio.</p>
            </div>
        </div>

        {{-- Alerts --}}
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

        <form action="{{ route('mentor.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row g-4">
                
                {{-- Left Side: Avatar & College Affiliation Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body text-center p-4">
                            <h5 class="fw-bold card-title mb-4 text-start">Profile Image</h5>
                            
                            {{-- Interactive Photo Preview --}}
                            <div class="position-relative d-inline-block mb-3">
                                <img id="photo-preview" 
                                     src="{{ !empty($mentor->photo) ? asset('storage/' . ltrim($mentor->photo, '/')) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle img-thumbnail shadow-sm object-fit-cover" 
                                     style="width: 140px; height: 140px;">
                                <label for="photo-input" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 p-2 shadow" title="Change Photo">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            
                            <input type="file" id="photo-input" name="photo" class="d-none" accept="image/*" onchange="previewImage(event)">
                            <p class="text-muted small mb-0">Allowed: JPG, PNG, WEBP (Max 2MB)</p>
                        </div>
                    </div>

                    {{-- College Info Badge (Read Only) --}}
                    @if($mentor->college)
                        <div class="card border-0 shadow-sm bg-light-subtle">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-muted mb-3">
                                    <i class="bi bi-building me-1"></i> Associated College
                                </h6>
                                <h5 class="fw-semibold text-dark mb-1">{{ $mentor->college->institution_name }}</h5>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-geo-alt me-1"></i> {{ $mentor->college->address ?? 'Location not provided' }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Side: Profile Details Form --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold card-title mb-4">Personal & Professional Details</h5>
                            
                            <div class="row g-3">
                                {{-- Full Name --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                        <input type="text" name="name" class="form-control border-start-0" value="{{ old('name', $user->name) }}" placeholder="e.g. Dr. Jane Doe" required>
                                    </div>
                                </div>

                                {{-- Email Address --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                        <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $user->email) }}" placeholder="mentor@domain.com" required>
                                    </div>
                                </div>

                                {{-- Qualification --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Qualification <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-mortarboard text-muted"></i></span>
                                        <input type="text" name="qualification" class="form-control border-start-0" value="{{ old('qualification', $mentor->qualification) }}" placeholder="e.g. M.Tech in CS, Ph.D." required>
                                    </div>
                                </div>

                                {{-- Expertise --}}
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Expertise / Domain <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-star text-muted"></i></span>
                                        <input type="text" name="expertise" class="form-control border-start-0" value="{{ old('expertise', $mentor->expertise) }}" placeholder="e.g. Machine Learning, Web Dev" required>
                                    </div>
                                </div>
                            </div>

                            {{-- Bio --}}
                            <div class="mt-3">
                                <label class="form-label fw-semibold">Professional Bio</label>
                                <textarea name="bio" class="form-control" rows="4" placeholder="Briefly describe your academic background, teaching experience, or interests...">{{ old('bio', $mentor->bio) }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Action Buttons --}}
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

    {{-- Image Preview Script --}}
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
</x-mentor.layout>
