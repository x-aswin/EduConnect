<x-student.layout title="Manage Profile - EduConnect" active="profile">
    @push('styles')
    <style>
        .profile-photo-container {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .profile-photo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-photo-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #e0e7ff, #c7d2fe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #4f6ef6;
        }
    </style>
    @endpush

    <div class="container py-4">
        <div class="mb-4">
            <h1 class="fw-bold mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>Manage Profile</h1>
            <p class="text-secondary mb-0">Update your personal information and academic details.</p>
        </div>

        @if(session('success') || session('status'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') ?? session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="row g-4">
                {{-- Left column: Photo + Account Info --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white text-center">
                        <div class="profile-photo-container mx-auto mb-3">
                            @if(auth()->user()->student?->photo)
                                <img src="{{ asset('storage/' . auth()->user()->student->photo) }}" alt="Profile Photo" id="profilePhotoPreview">
                            @else
                                <div class="profile-photo-placeholder" id="profilePhotoPlaceholder">
                                    <i class="bi bi-person"></i>
                                </div>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small">{{ auth()->user()->student?->current_qualification ?? 'Student' }}</p>

                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold small">Change Photo</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*" onchange="previewPhoto(event)">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text small">Max 2MB, jpg/png/webp</div>
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold small">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', auth()->user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 text-start">
                            <label class="form-label fw-semibold small">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Password Change Card (optional) --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mt-4">
                        <h5 class="fw-bold mb-3"><i class="bi bi-lock me-2 text-primary"></i>Change Password</h5>
                        <p class="text-muted small">Leave blank if you don't want to change password.</p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">New Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- Right column: Student specific details --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <h5 class="fw-bold mb-3"><i class="bi bi-person-vcard me-2 text-primary"></i>Personal Details</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', auth()->user()->student?->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Date of Birth</label>
                                <input type="date" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob', auth()->user()->student?->dob ? \Carbon\Carbon::parse(auth()->user()->student->dob)->format('Y-m-d') : '') }}">
                                @error('dob')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Gender</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">Choose...</option>
                                    <option value="male" {{ old('gender', auth()->user()->student?->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', auth()->user()->student?->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', auth()->user()->student?->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold small">Current Qualification</label>
                                <input type="text" name="current_qualification" class="form-control @error('current_qualification') is-invalid @enderror" value="{{ old('current_qualification', auth()->user()->student?->current_qualification) }}" placeholder="e.g., BCA, B.Tech">
                                @error('current_qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Your full address">{{ old('address', auth()->user()->student?->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold small">Verification ID Document (Aadhaar, College ID, etc.)</label>
                                <input type="file" name="verification_doc" class="form-control @error('verification_doc') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('verification_doc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text small text-muted">Upload an image or a PDF file (Max 2MB) to update your verification ID.</div>
                                
                                @if(auth()->user()->student?->verification_doc)
                                    <div class="mt-2 p-3 border rounded-3 bg-light d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-file-earmark-check-fill text-success fs-3 me-3"></i>
                                            <div>
                                                <span class="d-block small fw-bold text-secondary">Uploaded Document</span>
                                                <span class="text-muted small">Verification ID is on file</span>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . auth()->user()->student->verification_doc) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> View Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-light rounded-pill px-4 me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold shadow-sm">
                            <i class="bi bi-check2-circle me-2"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewPhoto(event) {
            const input = event.target;
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const placeholder = document.getElementById('profilePhotoPlaceholder');
                    if (placeholder) {
                        // replace placeholder with an img
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.id = 'profilePhotoPreview';
                        img.alt = 'Preview';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        placeholder.parentNode.replaceChild(img, placeholder);
                    } else {
                        const existingImg = document.getElementById('profilePhotoPreview');
                        if (existingImg) {
                            existingImg.src = e.target.result;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
    @endpush
</x-student.layout>