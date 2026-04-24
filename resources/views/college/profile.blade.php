<x-college.layout active="profile">
    <div class="row g-4 align-items-start">
        <div class="col-lg-4">
            <div class="card-placeholder h-100">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 72px; height: 72px; overflow: hidden;">
                        @if (!empty($college->photo))
                            <img src="{{ asset('storage/' . $college->photo) }}" alt="College photo" class="w-100 h-100 object-fit-cover">
                        @else
                            <i class="bi bi-building fs-2 text-primary"></i>
                        @endif
                    </div>
                    <div>
                        <h2 class="h4 fw-bold mb-1">{{ $college->institution_name }}</h2>
                        <p class="text-secondary mb-1">College profile management</p>
                        <span class="badge {{ !empty($college->verification_doc) ? 'bg-success' : 'bg-warning text-dark' }}">
                            {{ !empty($college->verification_doc) ? 'Verified' : 'Verification pending' }}
                        </span>
                    </div>
                </div>

                <div class="border-top pt-3 small text-secondary">
                    <div class="mb-2"><strong class="text-dark">Contact person:</strong> {{ $college->contact_person }}</div>
                    <div class="mb-2"><strong class="text-dark">Phone:</strong> {{ $college->college_phone }}</div>
                    <div class="mb-2"><strong class="text-dark">Website:</strong> {{ $college->website ?? 'Not set' }}</div>
                    @if (!empty($college->verification_doc))
                        <div>
                            <strong class="text-dark">Verification file:</strong>
                            <a href="{{ asset('storage/' . $college->verification_doc) }}" target="_blank" rel="noopener">View document</a>
                        </div>
                    @endif
                </div>
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

                    <div class="mb-3">
                        <x-input-label for="institution_name" :value="__('Institution Name')" />
                        <x-text-input id="institution_name" name="institution_name" type="text" class="block mt-1 w-full" :value="old('institution_name', $college->institution_name)" required />
                        <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="college_phone" :value="__('College Phone')" />
                            <x-text-input id="college_phone" name="college_phone" type="text" class="block mt-1 w-full" :value="old('college_phone', $college->college_phone)" required />
                            <x-input-error :messages="$errors->get('college_phone')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-input-label for="website" :value="__('Website')" />
                            <x-text-input id="website" name="website" type="url" class="block mt-1 w-full" :value="old('website', $college->website)" placeholder="https://example.com" />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="contact_person" :value="__('Contact Person Name')" />
                        <x-text-input id="contact_person" name="contact_person" type="text" class="block mt-1 w-full" :value="old('contact_person', $college->contact_person)" required />
                        <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="designation" :value="__('Designation')" />
                            <select id="designation" name="designation" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">-- Select Designation --</option>
                                <option value="Principal" {{ old('designation', $college->designation) == 'Principal' ? 'selected' : '' }}>Principal</option>
                                <option value="HOD" {{ old('designation', $college->designation) == 'HOD' ? 'selected' : '' }}>HOD</option>
                                <option value="Placement Officer" {{ old('designation', $college->designation) == 'Placement Officer' ? 'selected' : '' }}>Placement Officer</option>
                                <option value="Coordinator" {{ old('designation', $college->designation) == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                                <option value="Other" {{ old('designation', $college->designation) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            <x-input-error :messages="$errors->get('designation')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <x-text-input id="contact_number" name="contact_number" type="text" class="block mt-1 w-full" :value="old('contact_number', $college->contact_number)" required />
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-input-label for="address" :value="__('College Address')" />
                        <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4" required>{{ old('address', $college->address) }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <x-input-label for="photo" :value="__('College Logo / Photo')" />
                            <input id="photo" name="photo" type="file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" accept="image/*" />
                            <p class="text-sm text-secondary mt-2">Leave blank to keep the current image.</p>
                            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                        </div>

                        <div class="col-md-6 mb-3">
                            <x-input-label for="verification_doc" :value="__('Verification Document')" />
                            <input id="verification_doc" name="verification_doc" type="file" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" />
                            <p class="text-sm text-secondary mt-2">Upload a new file only if you want to replace the current verification document.</p>
                            <x-input-error :messages="$errors->get('verification_doc')" class="mt-2" />
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
</x-college.layout>