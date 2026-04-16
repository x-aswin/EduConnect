<x-base.layout>
    <x-guest-layout>
<div class="m-5 min-h-screen overflow-y-auto">
    <h2 class="text-2xl font-semibold mb-4">Complete Your College Profile</h2>
    <p class="mb-6 text-gray-600">Please fill in the details below to complete your college profile.</p>

    <form action="{{ route('college.complete.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- Institution Name --}}
        <div class="mb-3">
            <x-input-label for="institution_name" :value="__('Institution Name')" />
            <x-text-input 
                id="institution_name" 
                name="institution_name" 
                type="text" 
                class="block mt-1 w-full" 
                :value="old('institution_name', $college->institution_name ?? auth()->user()->name)" 
                required />
            <x-input-error :messages="$errors->get('institution_name')" class="mt-2" />
        </div>

        {{-- College Phone & Website --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="college_phone" :value="__('College Phone')" />
                <x-text-input 
                    id="college_phone" 
                    name="college_phone" 
                    type="text" 
                    class="block mt-1 w-full" 
                    :value="old('college_phone', $college->college_phone ?? '')" 
                    required />
                <x-input-error :messages="$errors->get('college_phone')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="website" :value="__('Website')" />
                <x-text-input 
                    id="website" 
                    name="website" 
                    type="url" 
                    class="block mt-1 w-full" 
                    :value="old('website', $college->website ?? '')" 
                    placeholder="https://example.com" />
                <x-input-error :messages="$errors->get('website')" class="mt-2" />
            </div>
        </div>

        {{-- Contact Person --}}
        <div class="mb-3">
            <x-input-label for="contact_person" :value="__('Contact Person Name')" />
            <x-text-input 
                id="contact_person" 
                name="contact_person" 
                type="text" 
                class="block mt-1 w-full" 
                :value="old('contact_person', $college->contact_person ?? '')" 
                required />
            <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
        </div>

        {{-- Designation & Contact Number --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="designation" :value="__('Designation')" />
                <select 
                    id="designation" 
                    name="designation" 
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                    required>
                    <option value="">-- Select Designation --</option>
                    <option value="Principal" {{ old('designation', $college->designation ?? '') == 'Principal' ? 'selected' : '' }}>Principal</option>
                    <option value="HOD" {{ old('designation', $college->designation ?? '') == 'HOD' ? 'selected' : '' }}>HOD</option>
                    <option value="Placement Officer" {{ old('designation', $college->designation ?? '') == 'Placement Officer' ? 'selected' : '' }}>Placement Officer</option>
                    <option value="Coordinator" {{ old('designation', $college->designation ?? '') == 'Coordinator' ? 'selected' : '' }}>Coordinator</option>
                    <option value="Other" {{ old('designation', $college->designation ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                <x-input-error :messages="$errors->get('designation')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="contact_number" :value="__('Contact Number')" />
                <x-text-input 
                    id="contact_number" 
                    name="contact_number" 
                    type="text" 
                    class="block mt-1 w-full" 
                    :value="old('contact_number', $college->contact_number ?? '')" 
                    required />
                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
            </div>
        </div>

        {{-- Address --}}
        <div class="mb-3">
            <x-input-label for="address" :value="__('College Address')" />
            <textarea 
                id="address" 
                name="address" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                rows="3" 
                required>{{ old('address', $college->address ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        {{-- College Photo --}}
        <div class="mb-3">
            <x-input-label for="photo" :value="__('College Logo/Photo')" />
            <input 
                id="photo" 
                name="photo" 
                type="file" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                accept="image/*" />
            <x-input-error :messages="$errors->get('photo')" class="mt-2" />
        </div>

        {{-- Verification Document --}}
        <div class="mb-3">
            <x-input-label for="verification_doc" :value="__('Verification Document')" />
            <p class="text-sm text-gray-500 mb-2">Please upload a document that verifies your institution (e.g., registration certificate, affiliation letter)</p>
            <input 
                id="verification_doc" 
                name="verification_doc" 
                type="file" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" 
                required />
            <x-input-error :messages="$errors->get('verification_doc')" class="mt-2" />
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Save Profile & Continue
        </button>

    </form>
</div>
    </x-guest-layout>
</x-base.layout>