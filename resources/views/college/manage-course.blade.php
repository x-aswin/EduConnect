<x-college.layout active="courses">
  <!-- Page header with greeting & quick stats -->
<div class="page-header flex-wrap">
    <div>
      <h1 class="h3 fw-bold mb-1">Manage Your Courses</h1>
      <p class="welcome-tag mb-0">
        <i class="bi bi-journal-text me-1"></i> 
        Create, edit, and organize your offline courses to provide the best learning experience for your students.
      </p>
    </div>
</div>


@php
    $isEdit = isset($editCourse) && !isset($viewOnly);
    $isView = isset($editCourse) && isset($viewOnly);
    $isCreate = !isset($editCourse);
@endphp

<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
    <i class="bi bi-plus-lg"></i> Add New Course
</button>

<div class="card mt-4 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Registered Courses</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        {{-- <th>College</th> --}}
                        <th>Mentor</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $course->course_image ? asset('storage/' . $course->course_image) : 'https://ui-avatars.com/api/?name=' . urlencode($course->title ?? 'Course') }}"
                                         class="rounded me-2" width="42" height="42" style="object-fit: cover;" alt="Course Image">
                                    {{ $course->title ?? 'Untitled Course' }}
                                </div>
                            </td>
                            {{-- <td>{{ $course->college->institution_name ?? 'N/A' }}</td> --}}
                            <td>{{ $course->mentor->user->name ?? 'Not Assigned' }}</td>
                            <td>{{ $course->category->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-info-subtle text-info border border-info">
                                    {{ $course->course_type === 'student_only' ? 'Student Only' : 'Firm Only' }}
                                </span>
                            </td>
                            <td>
                                @if($course->status === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group shadow-sm gap-1">
                                    <a href="{{ route('college.courses.show', $course->id) }}">
                                        <button class="btn btn-sm btn-outline-primary" title="View Course">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </a>
                                    <a href="{{ route('college.courses.edit', $course->id) }}">
                                        <button class="btn btn-sm btn-outline-warning" title="Edit Course">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    </a>
                                    <a href="{{ route('college.courses.edit', [$course->id, 'mode' => 'delete']) }}">
                                        <button class="btn btn-sm btn-outline-danger" title="Delete Course">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                No courses yet. Click "Add New Course" to begin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"><div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
                {{ $isEdit ? 'Edit Course: ' . $editCourse->title : ($isView ? 'Course: ' . $editCourse->title : 'Add New Course') }}
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <form action="{{ isset($editCourse) ? route('college.courses.update', $editCourse->id) : route('college.courses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($editCourse))
                @method('PATCH')
            @endif
            <div class="modal-body">
                @if(!$isCreate)
                    <div class="text-center mb-4">
                        <img src="{{ $editCourse->course_image ? asset('storage/' . $editCourse->course_image) : 'https://ui-avatars.com/api/?name=' . urlencode($editCourse->title) }}"
                             class="rounded img-thumbnail shadow-sm"
                             style="width: 120px; height: 120px; object-fit: cover;" alt="Course Image">
                        @if($isView)
                            <h4 class="mt-2">{{ $editCourse->title }}</h4>
                        @endif
                    </div>
                @endif

                <div class="row g-3">
                    <h6 class="border-bottom pb-2">Course Information</h6>

                    <div class="col-md-6">
                        <label class="form-label">Course Title</label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $editCourse->title ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Course Category</label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $editCourse->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Assign Mentor (Optional)</label>
                        <select name="mentor_id" id="mentor_id" class="form-select @error('mentor_id') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="">No Mentor</option>
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}" data-college-id="{{ $mentor->college_id }}" {{ old('mentor_id', $editCourse->mentor_id ?? '') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->user->name ?? 'Unknown Mentor' }} - {{ $mentor->college->institution_name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('mentor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Course Type</label>
                        <select name="course_type" id="course_type" class="form-select @error('course_type') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="student_only" {{ old('course_type', $editCourse->course_type ?? 'student_only') == 'student_only' ? 'selected' : '' }}>Student Only</option>
                            <option value="firm_only" {{ old('course_type', $editCourse->course_type ?? '') == 'firm_only' ? 'selected' : '' }}>Firm Only</option>
                        </select>
                        @error('course_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div id="firmOnlyInfo" class="col-md-12 d-none">
                        <div class="alert alert-info py-2 mb-0">
                            For <strong>Firm Only</strong> courses, seat count, schedule, time, and venue are set by the firm during booking.
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" min="0" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $editCourse->price ?? 0) }}" {{ $isView ? 'disabled' : '' }}>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Total Seats</label>
                        <input type="number" min="1" name="total_seats" id="total_seats" class="form-control @error('total_seats') is-invalid @enderror" value="{{ old('total_seats', $editCourse->total_seats ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('total_seats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', isset($editCourse) && $editCourse->start_date ? \Carbon\Carbon::parse($editCourse->start_date)->format('Y-m-d') : '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', isset($editCourse) && $editCourse->end_date ? \Carbon\Carbon::parse($editCourse->end_date)->format('Y-m-d') : '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 logistics-field">
                        <label class="form-label">Time Slot</label>
                        <input type="text" name="time_slot" id="time_slot" class="form-control @error('time_slot') is-invalid @enderror" value="{{ old('time_slot', $editCourse->time_slot ?? '') }}" placeholder="10:00 AM - 01:00 PM" {{ $isView ? 'disabled' : '' }}>
                        @error('time_slot')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 logistics-field">
                        <label class="form-label">Venue</label>
                        <input type="text" name="venue" id="venue" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue', $editCourse->venue ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                        @error('venue')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                            <option value="active" {{ old('status', $editCourse->status ?? 'inactive') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $editCourse->status ?? 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="is_certified" id="is_certified" value="1" {{ old('is_certified', $editCourse->is_certified ?? false) ? 'checked' : '' }} {{ $isView ? 'disabled' : '' }}>
                            <label class="form-check-label" for="is_certified">Certified Course</label>
                        </div>
                    </div>

                    @if(!$isView)
                    <div class="col-md-6">
                        <label class="form-label">Course Image</label>
                        <input type="file" name="course_image" class="form-control @error('course_image') is-invalid @enderror">
                        @error('course_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" {{ $isView ? 'disabled' : '' }}>{{ old('description', $editCourse->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                @if(isset($editCourse))
                    <a href="{{ route('college.courses.index') }}" class="btn btn-secondary">Cancel</a>
                @else
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @endif

                @if(!$isView)
                    <button type="submit" class="btn btn-primary">
                        {{ isset($editCourse) ? 'Update Course' : 'Create Course' }}
                    </button>
                @endif
            </div>
        </form>
    </div></div>
</div>

@if(isset($deleteCourse) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Course</h5>
                    <a href="{{ route('college.courses.index') }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteCourse->title ?? 'Unknown Course' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('college.courses.destroy', $deleteCourse->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">Confirm Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new bootstrap.Modal(document.getElementById('deleteModal')).show();

            var myModalDelete = document.getElementById('deleteModal');
            myModalDelete.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit')) {
                    window.location.href = "{{ route('college.courses.index') }}";
                }
            });
        });
    </script>
@endif

<x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addCourseModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif

@if(isset($editCourse))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addCourseModal'));
            editModal.show();

            var myModalElement = document.getElementById('addCourseModal');
            myModalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ route('college.courses.index') }}";
                }
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var isViewMode = @json($isView);
        var collegeSelect = document.getElementById('college_id');
        var mentorSelect = document.getElementById('mentor_id');
        var courseTypeSelect = document.getElementById('course_type');
        var firmOnlyInfo = document.getElementById('firmOnlyInfo');
        var logisticsFields = document.querySelectorAll('.logistics-field input');

        function filterMentorsByCollege() {
            if (!collegeSelect || !mentorSelect || mentorSelect.disabled) {
                return;
            }

            var selectedCollegeId = collegeSelect.value;
            var mentorOptions = mentorSelect.querySelectorAll('option[data-college-id]');

            mentorOptions.forEach(function (option) {
                var mentorCollegeId = option.getAttribute('data-college-id');
                var shouldShow = !selectedCollegeId || mentorCollegeId === selectedCollegeId;

                option.hidden = !shouldShow;
                if (!shouldShow && option.selected) {
                    option.selected = false;
                }
            });
        }

        if (collegeSelect && mentorSelect) {
            filterMentorsByCollege();
            collegeSelect.addEventListener('change', filterMentorsByCollege);
        }

        function toggleFirmOnlyFields() {
            if (!courseTypeSelect) {
                return;
            }

            var isFirmOnly = courseTypeSelect.value === 'firm_only';

            if (firmOnlyInfo) {
                firmOnlyInfo.classList.toggle('d-none', !isFirmOnly);
            }

            if (!logisticsFields.length) {
                return;
            }

            logisticsFields.forEach(function (field) {
                if (field.closest('.logistics-field')) {
                    field.disabled = isViewMode || isFirmOnly;
                }
            });
        }

        if (courseTypeSelect) {
            toggleFirmOnlyFields();
            courseTypeSelect.addEventListener('change', toggleFirmOnlyFields);
        }
    });
</script>


</x-college.layout>