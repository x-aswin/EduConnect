<x-firm.layout title="Profile - EduConnect" active="profile">
    <div class="container py-4">
        <h1 class="mb-3">Manage Firm Profile</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('firm.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Organization Name</label>
                <input type="text" name="org_name" class="form-control" value="{{ old('org_name', $firm->org_name ?? '') }}" required>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $firm->contact_person ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Designation</label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $firm->designation ?? '') }}">
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $firm->phone ?? '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $firm->email ?? $user->email ?? '') }}">
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $firm->address ?? '') }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control" accept="image/*">
                    @if(!empty($firm->photo))
                        <div class="mt-2"><img src="{{ asset('storage/' . ltrim($firm->photo, '/')) }}" alt="Firm photo" width="96" style="object-fit:cover; border-radius:8px;"></div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Verification Document</label>
                    <input type="file" name="verification_doc" class="form-control" accept=".pdf,image/*">
                    <div class="form-text">Allowed: PDF or image (max 5MB)</div>
                    @if(!empty($firm->verification_doc))
                        <div class="mt-2"><a href="{{ asset('storage/' . ltrim($firm->verification_doc, '/')) }}" target="_blank">Current document</a></div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">Save Profile</button>
            </div>
        </form>
    </div>
</x-firm.layout>
