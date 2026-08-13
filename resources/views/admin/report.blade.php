<x-admin.layout active="reports" title="Admin Reports">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Analytics & Reports</h1>
            <p class="text-muted">View your analytics and generate reports.</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i> {{ date('F j, Y') }}
        </div>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mb-4">
        @php($currentType = request('type', 'enrollments'))
        <div class="row g-2 align-items-end">
            <div class="col-auto">
                <label class="form-label">Report Type</label>
                <select name="type" class="form-select auto-submit">
                    <option value="enrollments" {{ (request('type')=='enrollments') ? 'selected' : '' }}>Enrollment Report</option>
                    <option value="colleges" {{ (request('type')=='colleges') ? 'selected' : '' }}>College Report</option>
                    <option value="users" {{ (request('type')=='users') ? 'selected' : '' }}>Users Report</option>
                    <option value="courses" {{ (request('type')=='courses') ? 'selected' : '' }}>Courses Report</option>
                    <option value="firms" {{ (request('type')=='firms') ? 'selected' : '' }}>Firm Report</option>
                    <option value="students" {{ (request('type')=='students') ? 'selected' : '' }}>Student Report</option>
                    <option value="mentors" {{ (request('type')=='mentors') ? 'selected' : '' }}>Mentor Report</option>
                    <option value="categories" {{ (request('type')=='categories') ? 'selected' : '' }}>Category Report</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Sub Filter</label>
                <select name="sub_filter" class="form-select auto-submit">
                    @if($currentType === 'enrollments')
                        <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Enrollments</option>
                        <option value="student" {{ request('sub_filter') === 'student' ? 'selected' : '' }}>Students Only</option>
                        <option value="firm" {{ request('sub_filter') === 'firm' ? 'selected' : '' }}>Firms Only</option>
                    @elseif($currentType === 'users')
                        <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Roles</option>
                        <option value="admin" {{ request('sub_filter') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="college" {{ request('sub_filter') === 'college' ? 'selected' : '' }}>College</option>
                        <option value="mentor" {{ request('sub_filter') === 'mentor' ? 'selected' : '' }}>Mentor</option>
                        <option value="student" {{ request('sub_filter') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="firm" {{ request('sub_filter') === 'firm' ? 'selected' : '' }}>Firm</option>
                    @elseif($currentType === 'courses')
                        <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Course Types</option>
                        <option value="student_only" {{ request('sub_filter') === 'student_only' ? 'selected' : '' }}>Student Only</option>
                        <option value="firm_only" {{ request('sub_filter') === 'firm_only' ? 'selected' : '' }}>Firm Only</option>
                    @elseif(in_array($currentType, ['colleges', 'firms', 'students', 'mentors'], true))
                        <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ request('sub_filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('sub_filter') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="blocked" {{ request('sub_filter') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    @else
                        <option value="all" selected>All</option>
                    @endif
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label">Start</label>
                <input type="date" name="start_date" value="{{ request('start_date', optional($start ?? null)->toDateString() ?? '') }}" class="form-control">
            </div>
            <div class="col-auto">
                <label class="form-label">End</label>
                <input type="date" name="end_date" value="{{ request('end_date', optional($end ?? null)->toDateString() ?? '') }}" class="form-control">
            </div>
            
            <div class="col-auto">
                <div class="btn-group" role="group" aria-label="Quick ranges">
                    <button type="button" class="btn btn-outline-secondary quick-range" data-range="today">Today</button>
                    <button type="button" class="btn btn-outline-secondary quick-range" data-range="7">Last 7</button>
                    <button type="button" class="btn btn-outline-secondary quick-range" data-range="30">Last 30</button>
                    <button type="button" class="btn btn-outline-secondary quick-range" data-range="reset">Reset</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card mb-4">
        <div class="card-body">
            @if(isset($summary))
                <div class="d-flex gap-3 mb-3">
                    <div><strong>Total:</strong> {{ $summary['total'] ?? 0 }}</div>
                    @if(request('type','enrollments') === 'enrollments' || request('type') === 'colleges')
                        <div><strong>Pending:</strong> {{ $summary['pending'] ?? 0 }}</div>
                    @elseif(request('type') === 'users')
                        <div><strong>Students:</strong> {{ $summary['students'] ?? 0 }}</div>
                        <div><strong>Mentors:</strong> {{ $summary['mentors'] ?? 0 }}</div>
                        <div><strong>Colleges:</strong> {{ $summary['colleges'] ?? 0 }}</div>
                    @elseif(request('type') === 'courses')
                        <div>
                            <strong>By Category:</strong>
                            @if(!empty($summary['by_category']))
                                @foreach($summary['by_category'] as $cat => $cnt)
                                    <span class="badge bg-secondary me-1">{{ $cat ?? 'Uncategorized' }}: {{ $cnt }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No data</span>
                            @endif
                        </div>
                    @elseif(request('type') === 'firms')
                        <div><strong>Total Participants:</strong> {{ $summary['participants'] ?? 0 }}</div>
                        <div><strong>Active Accounts:</strong> {{ $summary['active_users'] ?? 0 }}</div>
                    @elseif(request('type') === 'students')
                        <div><strong>Active Accounts:</strong> {{ $summary['active_users'] ?? 0 }}</div>
                    @elseif(request('type') === 'mentors')
                        <div><strong>Total Courses:</strong> {{ $summary['courses_total'] ?? 0 }}</div>
                        <div><strong>Active Accounts:</strong> {{ $summary['active_users'] ?? 0 }}</div>
                    @endif
                </div>
            @endif

            @if(request('type','enrollments') === 'enrollments')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>College</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Participants</th>
                                <th>Total Amount</th>
                                <th>Venue</th>
                                <th>Schedule</th>
                                <th>College Note</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->id }}</td>
                                    <td>{{ $enrollment->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->user?->email ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->course?->title ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->course?->college?->institution_name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($enrollment->type) }}</td>
                                    <td>{{ ucfirst($enrollment->status) }}</td>
                                    <td>{{ strtoupper($enrollment->payment_status ?? 'na') }}</td>
                                    <td>{{ $enrollment->participant_count ?? 'N/A' }}</td>
                                    <td>
                                        @if(isset($enrollment->total_amount) && $enrollment->total_amount == 0)
                                            <span class="badge bg-success-subtle text-success">Free</span>
                                        @elseif(!empty($enrollment->total_amount))
                                            ₹{{ number_format($enrollment->total_amount, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($enrollment->type) === 'student')
                                            {{ $enrollment->course?->venue ?? '-' }}
                                        @else
                                            {{ $enrollment->requested_venue ?? '-' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(strtolower($enrollment->type) === 'student')
                                            {{-- Student Courses: Dates from Course model --}}
                                            @if($enrollment->course?->start_date && $enrollment->course?->end_date)
                                                {{ \Carbon\Carbon::parse($enrollment->course->start_date)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($enrollment->course->end_date)->format('d M Y') }}
                                            @elseif($enrollment->course?->start_date)
                                                {{ \Carbon\Carbon::parse($enrollment->course->start_date)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        @else
                                            {{-- Firm Courses: Dates from Enrollment model --}}
                                            @if($enrollment->proposed_start && $enrollment->proposed_end)
                                                {{ \Carbon\Carbon::parse($enrollment->proposed_start)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($enrollment->proposed_end)->format('d M Y') }}
                                            @elseif($enrollment->proposed_start)
                                                {{ \Carbon\Carbon::parse($enrollment->proposed_start)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $enrollment->college_note ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ $enrollment->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="15" class="text-center text-muted">No enrollments in this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @elseif(request('type') === 'colleges')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Institution</th>
            <th>Acronym</th>
            <th>Email</th>
            <th>Email Verified</th>
            <th>Status</th>
            <th>College Phone</th>
            <th>Address</th>
            <th>Website</th>
            <th>Contact Person</th>
            <th>Designation</th>
            <th>Contact Number</th>
            <th>Mentors Count</th>
            <th>Courses Count</th>
            <th>Verification Doc</th>
            <th>Photo</th>
            <th>Created</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $college)
            <tr>
                <td>{{ $college->id }}</td>
                <td>{{ $college->user_id }}</td>
                <td>{{ $college->institution_name ?? 'N/A' }}</td>
                <td>{{ $college->user?->name ?? 'N/A' }}</td>
                <td>{{ $college->user?->email ?? 'N/A' }}</td>
                <td>
                    @if($college->user?->email_verified_at)
                        <span class="badge bg-success">Verified</span>
                    @else
                        <span class="badge bg-warning text-dark">Unverified</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-{{ $college->user?->status === 'active' ? 'success' : ($college->user?->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($college->user?->status ?? 'n/a') }}
                    </span>
                </td>
                <td>{{ $college->college_phone ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($college->address ?? '-', 40) }}</td>
                <td>
                    @if(!empty($college->website))
                        <a href="{{ \Illuminate\Support\Str::startsWith($college->website, ['http://', 'https://']) ? $college->website : 'https://' . $college->website }}" target="_blank" rel="noopener noreferrer">
                            {{ $college->website }}
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $college->contact_person ?? '-' }}</td>
                <td>{{ $college->designation ?? '-' }}</td>
                <td>{{ $college->contact_number ?? '-' }}</td>
                <td class="text-center">{{ $college->mentors_count ?? $college->mentors()->count() }}</td>
                <td class="text-center">{{ $college->courses_count ?? $college->courses()->count() }}</td>
                <td>
                    @if(!empty($college->verification_doc))
                        @php($docUrl = \Illuminate\Support\Str::startsWith($college->verification_doc, ['http://', 'https://', '/']) ? $college->verification_doc : asset('storage/' . ltrim($college->verification_doc, '/')))
                        <a href="{{ $docUrl }}" target="_blank" class="btn btn-xs btn-outline-primary ms-1">
                            <i class="fas fa-file-alt"></i> View Doc
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if(!empty($college->photo))
                        @php($photoUrl = \Illuminate\Support\Str::startsWith($college->photo, ['http://', 'https://', '/']) ? $college->photo : asset('storage/' . ltrim($college->photo, '/')))
                        <img src="{{ $photoUrl }}" alt="College photo" class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;">
                    @else
                        -
                    @endif
                </td>
                <td>{{ $college->created_at ? $college->created_at->format('d M Y h:i A') : '-' }}</td>
                <td>{{ $college->updated_at ? $college->updated_at->format('d M Y h:i A') : '-' }}</td>
            </tr>
        @empty
            <tr><td colspan="19" class="text-center text-muted">No colleges found</td></tr>
        @endforelse
    </tbody>
</table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif
            @if(request('type') === 'firms')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Organization</th>
                                <th>Type</th>
                                <th>Owner</th>
                                <th>Owner Email</th>
                                <th>Owner Status</th>
                                <th>Contact Person</th>
                                <th>Designation</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Verification Doc</th>
                                <th>Photo</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $firm)
                                <tr>
                                    <td>{{ $firm->id }}</td>
                                    <td>{{ $firm->org_name ?? 'N/A' }}</td>
                                    <td>{{ $firm->org_type ?? 'N/A' }}</td>
                                    <td>{{ $firm->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $firm->user?->email ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($firm->user?->status ?? 'n/a') }}</td>
                                    <td>{{ $firm->contact_person ?? 'N/A' }}</td>
                                    <td>{{ $firm->designation ?? '-' }}</td>
                                    <td>{{ $firm->phone ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($firm->address ?? '-', 40) }}</td>
                                    <td>
                                        @if(!empty($firm->verification_doc))
                                            <a href="{{ \Illuminate\Support\Str::startsWith($firm->verification_doc, ['http://', 'https://']) ? $firm->verification_doc : asset('storage/' . ltrim($firm->verification_doc, '/')) }}" 
                                            target="_blank" 
                                            class="btn btn-sm btn-outline-primary py-0 px-2">
                                                <i class="bi bi-file-earmark-text me-1"></i> View Doc
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!empty($firm->photo))
                                            @php($photoUrl = \Illuminate\Support\Str::startsWith($firm->photo, ['http://', 'https://', '/']) ? $firm->photo : asset('storage/' . ltrim($firm->photo, '/')))
                                            <img src="{{ $photoUrl }}" alt="Firm photo" class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $firm->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ $firm->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="13" class="text-center text-muted">No firms in this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif

            @if(request('type') === 'students')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Enrollments</th>
            <th>Phone</th>
            <th>DOB</th>
            <th>Gender</th>
            <th>Qualification</th>
            <th>Address</th>
            <th>Photo</th>
            <th>Verification Doc</th>
            <th>Created</th>
            <th>Updated</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $student)
            <tr>
                <td>{{ $student->id }}</td>
                <td>{{ $student->user?->name ?? 'N/A' }}</td>
                <td>{{ $student->user?->email ?? 'N/A' }}</td>
                <td>{{ ucfirst($student->user?->status ?? 'n/a') }}</td>
                <td>{{ $summary['enroll_counts'][$student->user_id] ?? 0 }}</td>
                <td>{{ $student->phone ?? 'N/A' }}</td>
                <td>{{ $student->dob ? $student->dob->format('d M Y') : '-' }}</td>
                <td>{{ $student->gender ?? '-' }}</td>
                <td>{{ $student->current_qualification ?? '-' }}</td>
                <td>{{ \Illuminate\Support\Str::limit($student->address ?? '-', 40) }}</td>
                
                {{-- Photo Column --}}
                <td>
                    @if(!empty($student->photo))
                        <img src="{{ \Illuminate\Support\Str::startsWith($student->photo, ['http://', 'https://', '/']) ? $student->photo : asset('storage/' . ltrim($student->photo, '/')) }}" 
                             alt="Student photo" 
                             class="img-thumbnail" 
                             style="width: 56px; height: 56px; object-fit: cover;">
                    @else
                        -
                    @endif
                </td>

                {{-- Verification Doc Column --}}
                <td>
                    @if(!empty($student->verification_doc))
                        <a href="{{ \Illuminate\Support\Str::startsWith($student->verification_doc, ['http://', 'https://']) ? $student->verification_doc : asset('storage/' . ltrim($student->verification_doc, '/')) }}" 
                           target="_blank" 
                           class="btn btn-sm btn-outline-primary py-0 px-2">
                            <i class="bi bi-file-earmark-text me-1"></i> View Doc
                        </a>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>

                <td>{{ $student->created_at->format('d M Y h:i A') }}</td>
                <td>{{ $student->updated_at->format('d M Y h:i A') }}</td>
            </tr>
        @empty
            <tr><td colspan="14" class="text-center text-muted">No students in this range</td></tr>
        @endforelse
    </tbody>
</table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif

            @if(request('type') === 'mentors')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>College</th>
                                <th>Qualification</th>
                                <th>Expertise</th>
                                <th>Bio</th>
                                <th>Photo</th>
                                <th>Courses</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $mentor)
                                <tr>
                                    <td>{{ $mentor->id }}</td>
                                    <td>{{ $mentor->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $mentor->user?->email ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($mentor->user?->status ?? 'n/a') }}</td>
                                    <td>{{ $mentor->college?->institution_name ?? 'N/A' }}</td>
                                    <td>{{ $mentor->qualification ?? '-' }}</td>
                                    <td>{{ $mentor->expertise ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($mentor->bio ?? '-', 40) }}</td>
                                    <td>
                                        @if(!empty($mentor->photo))
                                            @php($photoUrl = \Illuminate\Support\Str::startsWith($mentor->photo, ['http://', 'https://', '/']) ? $mentor->photo : asset('storage/' . ltrim($mentor->photo, '/')))
                                            <img src="{{ $photoUrl }}" alt="Mentor photo" class="img-thumbnail" style="width: 56px; height: 56px; object-fit: cover;">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $mentor->courses_count ?? 0 }}</td>
                                    <td>{{ $mentor->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ $mentor->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="text-center text-muted">No mentors in this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif

            @if(request('type') === 'categories')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Slug</th>
                                <th>Icon</th>
                                <th>Courses (in range)</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>
    @if(!empty($category->icon))
        <img src="{{ \Illuminate\Support\Str::startsWith($category->icon, ['http://', 'https://', '/']) ? $category->icon : asset('storage/' . ltrim($category->icon, '/')) }}" 
             alt="{{ $category->name ?? 'Category Icon' }}" 
             class="img-thumbnail" 
             style="width: 40px; height: 40px; object-fit: cover;">
    @else
        <span class="text-muted">-</span>
    @endif
</td>
                                    <td>{{ $summary['courses_by_category'][$category->id] ?? 0 }}</td>
                                    <td>{{ $category->description ?? '' }}</td>
                                    <td>{{ $category->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ $category->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">No categories</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif
            @if(request('type') === 'users')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Email Verified At</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst($user->role) }}</td>
                                    <td>{{ ucfirst($user->status ?? 'n/a') }}</td>
                                    <td>{{ $user->email_verified_at ? \Carbon\Carbon::parse($user->email_verified_at)->format('d M Y h:i A') : '-' }}</td>
                                    <td>{{ $user->created_at->format('d M Y h:i A') }}</td>
                                    <td>{{ $user->updated_at->format('d M Y h:i A') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">No users in this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif

            @if(request('type') === 'courses')
                <div class="table-responsive report-table-scroll">
                    <table class="table table-hover align-middle table-sm export-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>College</th>
                                <th>Category</th>
                                <th>Mentor</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Certified</th>
                                <th>Total Seats</th>
                                <th>Available</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Time Slot</th>
                                <th>Venue</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Created</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $course)
                                <tr>
                                    <td>{{ $course->id }}</td>
                                    <td><strong>{{ $course->title ?? 'N/A' }}</strong></td>
                                    <td>{{ $course->slug ?? 'N/A' }}</td>
                                    <td>{{ $course->college?->institution_name ?? 'N/A' }}</td>
                                    <td>{{ $course->category?->name ?? 'N/A' }}</td>
                                    <td>{{ $course->mentor?->user?->name ?? 'N/A' }}</td>
                                    <td>
                                        @if(($course->course_type ?? '') === 'firm_only')
                                            <span class="badge bg-info text-dark">Firm Only</span>
                                        @elseif(($course->course_type ?? '') === 'student_only')
                                            <span class="badge bg-primary">Student Only</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($course->course_type ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if((float) ($course->price ?? 0) === 0.0)
                                            <span class="badge bg-success">Free</span>
                                        @else
                                            ₹{{ number_format($course->price, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $course->is_certified ? 'success' : 'secondary' }}">
                                            {{ $course->is_certified ? 'Yes' : 'No' }}
                                        </span>
                                    </td>

                                    {{-- Individual 6 cells to maintain exact 20-column count --}}
                                    @if(($course->course_type ?? '') === 'firm_only')
                                        <td class="text-muted small fst-italic">N/A</td>
                                        <td class="text-muted small fst-italic">N/A</td>
                                        <td class="text-muted small fst-italic">N/A</td>
                                        <td class="text-muted small fst-italic">N/A</td>
                                        <td class="text-muted small fst-italic">N/A</td>
                                        <td class="text-muted small fst-italic">N/A</td>
                                    @else
                                        <td>{{ $course->total_seats ?? '-' }}</td>
                                        <td>{{ $course->available_seats ?? '-' }}</td>
                                        <td>{{ $course->start_date ? \Carbon\Carbon::parse($course->start_date)->format('d M Y') : '-' }}</td>
                                        <td>{{ $course->end_date ? \Carbon\Carbon::parse($course->end_date)->format('d M Y') : '-' }}</td>
                                        <td>{{ $course->time_slot ?? '-' }}</td>
                                        <td>{{ $course->venue ?? '-' }}</td>
                                    @endif

                                    <td>
                                        <span class="badge bg-{{ $course->status === 'active' ? 'success' : ($course->status === 'draft' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($course->status ?? '-') }}
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($course->description ?? '-', 40) }}</td>
                                    <td>
                                        @if(!empty($course->course_image))
                                            @php($photoUrl = \Illuminate\Support\Str::startsWith($course->course_image, ['http://', 'https://', '/']) ? $course->course_image : asset('storage/' . ltrim($course->course_image, '/')))
                                            <img src="{{ $photoUrl }}" alt="Course image" class="img-thumbnail rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $course->created_at ? $course->created_at->format('d M Y h:i A') : '-' }}</td>
                                    <td>{{ $course->updated_at ? $course->updated_at->format('d M Y h:i A') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="20" class="text-center text-muted">No courses in this range</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">{{ $results->links() }}</div>
                @endif
            @endif
        </div>
    </div>

@push('styles')
<style>
.report-table-scroll {
    max-height: 65vh;
    overflow-y: auto;
}

.report-table-scroll thead th {
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
}
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.quick-range').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const range = btn.dataset.range;
        const startInput = document.querySelector('input[name="start_date"]');
        const endInput = document.querySelector('input[name="end_date"]');
        const today = new Date();
        let start, end;
        if (range === 'today') {
            start = end = today;
        } else if (range === 'reset') {
            // clear dates to allow controller defaults
            startInput.value = '';
            endInput.value = '';
            startInput.closest('form').submit();
            return;
        } else if (range === 'month') {
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        } else {
            // numeric days
            const days = parseInt(range, 10);
            end = today;
            start = new Date();
            start.setDate(today.getDate() - (days - 1));
        }

        function toInputDate(d){
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth()+1).padStart(2,'0');
            const dd = String(d.getDate()).padStart(2,'0');
            return `${yyyy}-${mm}-${dd}`;
        }

        startInput.value = toInputDate(start);
        endInput.value = toInputDate(end);
        // submit the form
        startInput.closest('form').submit();
    });
});
// auto-submit when report type or dates change
document.querySelectorAll('.auto-submit').forEach(el => el.addEventListener('change', () => el.closest('form').submit()));
document.querySelectorAll('input[name="start_date"], input[name="end_date"]').forEach(el => el.addEventListener('change', () => el.closest('form').submit()));

   $(document).ready(function() {
        if ($('.table').length > 0) {
            $('.table').DataTable({
                paging: false,
                searching: true,
                info: false,
                ordering: true,
                dom: '<"d-flex justify-content-between align-items-center mb-3"fB>rt',
                buttons: {
                    dom: {
                        button: {
                            tag: 'button',
                            className: 'btn btn-sm'
                        }
                    },
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            className: 'btn-outline-secondary',
                            text: '<i class="fas fa-copy me-1"></i> Copy Data'
                        },
                        {
                            extend: 'excelHtml5',
                            className: 'btn-outline-success',
                            text: '<i class="fas fa-file-excel me-1"></i> Export Excel',
                            title: '{{ ucfirst($currentType) }} Report - {{ date("Y-m-d") }}'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn-outline-info',
                            text: '<i class="fas fa-file-csv me-1"></i> Export CSV',
                            title: '{{ ucfirst($currentType) }} Report - {{ date("Y-m-d") }}'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn-outline-danger',
                            text: '<i class="fas fa-file-pdf me-1"></i> Download PDF',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            title: '{{ ucfirst($currentType) }} Report - {{ date("Y-m-d") }}'
                        },
                        {
                            extend: 'print',
                            className: 'btn-outline-dark',
                            text: '<i class="fas fa-print me-1"></i> Print Table'
                        }
                    ]
                }
            });
        }
    });

</script>
@endpush

</x-admin.layout>
