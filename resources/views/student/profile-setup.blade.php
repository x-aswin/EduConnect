<form action="{{ route('student.profile.update') }}" method="POST">
    @csrf
    @method('PATCH')

    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $student->full_name) }}" required>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="dob" class="form-control" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Gender</label>
        <select name="gender" class="form-select" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Current Qualification</label>
        <select name="current_qualification" class="form-select" required>
            <option value="">Select Qualification</option>
            <option value="+2">Higher Secondary (+2)</option>
            <option value="BCA">BCA</option>
            <option value="BSc">BSc Computer Science</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Permanent Address</label>
        <textarea name="address" class="form-control" rows="3" required></textarea>
    </div>

    <button type="submit" class="btn btn-primary w-100">Save Profile & Continue</button>
</form>