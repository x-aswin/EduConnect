<x-firm.layout title="Complete Profile - EduConnect" active="profile">
    <div class="container py-4">
        <h1 class="mb-3">Complete Your Firm Profile</h1>

        <p class="text-muted">Please provide details to complete your firm account. This step is usually shown immediately after registration.</p>

        <form action="{{ route('firm.complete.profile.update') }}" method="POST" enctype="multipart/form-data">
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
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $firm->phone ?? '') }}">
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $firm->address ?? '') }}</textarea>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Photo</label>
                    <input type="file" name="photo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Verification Document</label>
                    <input type="file" name="verification_doc" class="form-control">
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary">Complete Profile</button>
            </div>
        </form>
    </div>
</x-firm.layout>
