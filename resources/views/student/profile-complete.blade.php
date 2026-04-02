<x-base.layout>
    <x-guest-layout>
<div class="m-5">
    <h2 class="text-2xl font-semibold mb-4">Complete Your Profile</h2>
    <p class="mb-6 text-gray-600">Please fill in the details below to complete your student profile.</p>

    <form action="{{ route('student.complete.profile.update') }}" method="POST">
        @csrf
        @method('PATCH')

        {{-- Full Name --}}
        <div class="mb-3">
            <x-input-label for="full_name" :value="__('Full Name')" />
            <x-text-input 
                id="full_name" 
                name="full_name" 
                type="text" 
                class="block mt-1 w-full" 
                :value="old('full_name', $student->full_name ?? auth()->user()->name)" 
                required />
            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        {{-- Phone & DOB --}}
        <div class="row">
            <div class="col-md-6 mb-3">
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input 
                    id="phone" 
                    name="phone" 
                    type="text" 
                    class="block mt-1 w-full" 
                    :value="old('phone', $student->phone)" 
                    required />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <div class="col-md-6 mb-3">
                <x-input-label for="dob" :value="__('Date of Birth')" />
                <x-text-input 
                    id="dob" 
                    name="dob" 
                    type="date" 
                    class="block mt-1 w-full" 
                    :value="old('dob', $student->dob)" 
                    required />
                <x-input-error :messages="$errors->get('dob')" class="mt-2" />
            </div>
        </div>

        {{-- Gender --}}
        <div class="mb-3">
            <x-input-label for="gender" :value="__('Gender')" />
            <select 
                id="gender" 
                name="gender" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                required>
                <option value="">-- Select Gender --</option>
                <option value="male"   {{ old('gender', $student->gender) == 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                <option value="other"  {{ old('gender', $student->gender) == 'other'  ? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        {{-- Current Qualification --}}
        <div class="mb-3">
            <x-input-label for="current_qualification" :value="__('Current Qualification')" />
            <select 
                id="current_qualification" 
                name="current_qualification" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                required>
                <option value="">-- Select Qualification --</option>
                <option value="+2"   {{ old('current_qualification', $student->current_qualification) == '+2'   ? 'selected' : '' }}>Higher Secondary (+2)</option>
                <option value="BCA"  {{ old('current_qualification', $student->current_qualification) == 'BCA'  ? 'selected' : '' }}>BCA</option>
                <option value="BSc"  {{ old('current_qualification', $student->current_qualification) == 'BSc'  ? 'selected' : '' }}>BSc Computer Science</option>
                <option value="Other"{{ old('current_qualification', $student->current_qualification) == 'Other'? 'selected' : '' }}>Other</option>
            </select>
            <x-input-error :messages="$errors->get('current_qualification')" class="mt-2" />
        </div>

        {{-- Address --}}
        <div class="mb-3">
            <x-input-label for="address" :value="__('Permanent Address')" />
            <textarea 
                id="address" 
                name="address" 
                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
                rows="3" 
                required>{{ old('address', $student->address) }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Save Profile & Continue
        </button>

    </form>
</div>
    </x-guest-layout>
</x-base.layout>