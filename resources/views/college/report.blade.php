<x-college.layout active="reports">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">College Reports</h1>
            <p class="welcome-tag mb-0">Generate reports for {{ $college->institution_name ?? 'your college' }}.</p>
        </div>
        <div class="text-muted">
            <i class="bi bi-calendar-event me-1"></i> {{ date('F j, Y') }}
        </div>
    </div>

    <div class="card-placeholder">
        <form method="GET" action="{{ route('college.reports.index') }}" class="mb-4">
            @php($currentType = request('type', 'enrollments'))
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-select auto-submit">
                        <option value="enrollments" {{ request('type', 'enrollments') === 'enrollments' ? 'selected' : '' }}>Enrollment Report</option>
                        <option value="courses" {{ request('type') === 'courses' ? 'selected' : '' }}>Course Report</option>
                        <option value="mentors" {{ request('type') === 'mentors' ? 'selected' : '' }}>Mentor Report</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label">Sub Filter</label>
                    <select name="sub_filter" class="form-select auto-submit">
                        @if($currentType === 'enrollments')
                            <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Enrollments</option>
                            <option value="student" {{ request('sub_filter') === 'student' ? 'selected' : '' }}>Students Only</option>
                            <option value="firm" {{ request('sub_filter') === 'firm' ? 'selected' : '' }}>Firms Only</option>
                        @elseif($currentType === 'courses')
                            <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Course Types</option>
                            <option value="student_only" {{ request('sub_filter') === 'student_only' ? 'selected' : '' }}>Student Only</option>
                            <option value="firm_only" {{ request('sub_filter') === 'firm_only' ? 'selected' : '' }}>Firm Only</option>
                        @elseif($currentType === 'mentors')
                            <option value="all" {{ request('sub_filter', 'all') === 'all' ? 'selected' : '' }}>All Status</option>
                            <option value="pending" {{ request('sub_filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="active" {{ request('sub_filter') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="blocked" {{ request('sub_filter') === 'blocked' ? 'selected' : '' }}>Blocked</option>
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

        <div class="mb-4">
            <div class="d-flex gap-3 flex-wrap align-items-center">
                <div><strong>Total:</strong> {{ $summary['total'] ?? 0 }}</div>
                @if(request('type', 'enrollments') === 'enrollments')
                    <div><strong>Pending:</strong> {{ $summary['pending'] ?? 0 }}</div>
                    <div><strong>Confirmed:</strong> {{ $summary['confirmed'] ?? 0 }}</div>
                @elseif(request('type') === 'courses')
                    <div><strong>Active:</strong> {{ $summary['active'] ?? 0 }}</div>
                    <div><strong>Inactive:</strong> {{ $summary['inactive'] ?? 0 }}</div>
                    <div>
                        <strong>By Category:</strong>
                        @forelse(($summary['by_category'] ?? []) as $category => $count)
                            <span class="badge bg-secondary me-1">{{ $category }}: {{ $count }}</span>
                        @empty
                            <span class="text-muted">No data</span>
                        @endforelse
                    </div>
                @elseif(request('type') === 'mentors')
                    <div><strong>Total Courses:</strong> {{ $summary['courses_total'] ?? 0 }}</div>
                    <div><strong>Active Accounts:</strong> {{ $summary['active_users'] ?? 0 }}</div>
                @endif
            </div>
        </div>

        @if(request('type', 'enrollments') === 'enrollments')
            <div class="table-responsive report-table-scroll">
                <table class="table table-hover align-middle table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Participants</th>
                            <th>Total Amount</th>
                            <th>Requested Venue</th>
                            <th>Proposed Schedule</th>
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
                                <td>{{ ucfirst($enrollment->type) }}</td>
                                <td>{{ ucfirst($enrollment->status) }}</td>
                                <td>{{ strtoupper($enrollment->payment_status ?? 'na') }}</td>
                                <td>{{ $enrollment->participant_count ?? 0 }}</td>
                                <td>{{ $enrollment->total_amount ?? '0.00' }}</td>
                                <td>{{ $enrollment->requested_venue ?? '-' }}</td>
                                <td>{{ $enrollment->proposed_schedule ? \Carbon\Carbon::parse($enrollment->proposed_schedule)->format('d M Y h:i A') : '-' }}</td>
                                <td>{{ $enrollment->created_at->format('d M Y h:i A') }}</td>
                                <td>{{ $enrollment->updated_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="13" class="text-center text-muted">No enrollments in this range</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-3">{{ $results->links() }}</div>
            @endif
        @elseif(request('type') === 'courses')
            <div class="table-responsive report-table-scroll">
                <table class="table table-hover align-middle table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Mentor</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Seats</th>
                            <th>Available</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $course)
                            <tr>
                                <td>{{ $course->id }}</td>
                                <td>{{ $course->title ?? 'N/A' }}</td>
                                <td>{{ $course->category?->name ?? 'N/A' }}</td>
                                <td>{{ $course->mentor?->user?->name ?? 'N/A' }}</td>
                                <td>{{ $course->course_type ?? '-' }}</td>
                                <td>{{ $course->price ?? '0.00' }}</td>
                                <td>{{ $course->total_seats ?? '-' }}</td>
                                <td>{{ $course->available_seats ?? '-' }}</td>
                                <td>{{ ucfirst($course->status ?? '-') }}</td>
                                <td>{{ $course->created_at->format('d M Y h:i A') }}</td>
                                <td>{{ $course->updated_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center text-muted">No courses in this range</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-3">{{ $results->links() }}</div>
            @endif
        @elseif(request('type') === 'mentors')
            <div class="table-responsive report-table-scroll">
                <table class="table table-hover align-middle table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Qualification</th>
                            <th>Expertise</th>
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
                                <td>{{ $mentor->qualification ?? '-' }}</td>
                                <td>{{ $mentor->expertise ?? '-' }}</td>
                                <td>{{ $mentor->courses_count ?? 0 }}</td>
                                <td>{{ $mentor->created_at->format('d M Y h:i A') }}</td>
                                <td>{{ $mentor->updated_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted">No mentors in this range</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-3">{{ $results->links() }}</div>
            @endif
        @endif
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
        btn.addEventListener('click', () => {
            const range = btn.dataset.range;
            const startInput = document.querySelector('input[name="start_date"]');
            const endInput = document.querySelector('input[name="end_date"]');
            const today = new Date();
            let start;
            let end;

            if (range === 'today') {
                start = end = today;
            } else if (range === 'reset') {
                startInput.value = '';
                endInput.value = '';
                startInput.closest('form').submit();
                return;
            } else {
                const days = parseInt(range, 10);
                end = today;
                start = new Date();
                start.setDate(today.getDate() - (days - 1));
            }

            function toInputDate(date) {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            }

            startInput.value = toInputDate(start);
            endInput.value = toInputDate(end);
            startInput.closest('form').submit();
        });
    });

    document.querySelectorAll('.auto-submit').forEach(el => el.addEventListener('change', () => el.closest('form').submit()));
    document.querySelectorAll('input[name="start_date"], input[name="end_date"]').forEach(el => el.addEventListener('change', () => el.closest('form').submit()));
    </script>
    @endpush
</x-college.layout>