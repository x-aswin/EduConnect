<x-mentor.layout title="Mentor Reports - EduConnect" active="reports">
    @push('styles')
    <style>
        .report-header {
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            border-radius: 2rem;
            color: white;
            padding: 2.5rem;
            box-shadow: 0 15px 30px rgba(37, 99, 235, 0.15);
            margin-bottom: 2rem;
        }

        .report-stat-card {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .report-stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        }

        .filter-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
            margin-bottom: 2rem;
        }

        .results-card {
            background: white;
            border-radius: 1.5rem;
            padding: 2rem;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
        }

        .table-responsive {
            border-radius: 1rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody td {
            padding: 1.2rem 1rem;
            color: #334155;
            font-size: 0.9rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-status {
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-accepted {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-declined {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-inactive {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>
    @endpush

    <!-- Header Section -->
    <div class="report-header mt-4">
        <span class="badge bg-white bg-opacity-20 text-black px-3 py-2 rounded-pill mb-2">
            <i class="bi bi-graph-up me-1"></i> Mentor Performance Analytics
        </span>
        <h1 class="fw-bold mt-2 mb-1">Reports & Mentorship Statistics</h1>
        <p class="text-white text-opacity-80 mb-0">Monitor student requests, message exchange activity, and course assignments.</p>
    </div>

    <!-- Overview Stats Rows -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-chat-left-dots fs-4 text-primary"></i>
                </div>
                <div>
                    <span class="text-secondary small">Total Requests</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['total_requests'] }}</h4>
                    <small class="text-muted">Chat requests</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-chat-fill fs-4 text-success"></i>
                </div>
                <div>
                    <span class="text-secondary small">Active Chats</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['active_chats'] }}</h4>
                    <small class="text-muted">Accepted chats</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-warning bg-opacity-10 p-3 me-3">
                    <i class="bi bi-envelope-paper fs-4 text-warning"></i>
                </div>
                <div>
                    <span class="text-secondary small">Total Messages</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['total_messages'] }}</h4>
                    <small class="text-muted">Exchanged</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-bookmark fs-4 text-info"></i>
                </div>
                <div>
                    <span class="text-secondary small">Assigned Courses</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['assigned_courses'] }}</h4>
                    <small class="text-muted">In curriculum</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Filter Report Data</h4>
        <form method="GET" action="{{ route('mentor.reports.index') }}" id="reportFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Report Category</label>
                    <select name="type" class="form-select rounded-3 py-2 auto-submit border-light-subtle">
                        <option value="chats" {{ $type === 'chats' ? 'selected' : '' }}>Chats & Requests</option>
                        <option value="courses" {{ $type === 'courses' ? 'selected' : '' }}>My Courses</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Sub-Filter</label>
                    <select name="sub_filter" class="form-select rounded-3 py-2 auto-submit border-light-subtle">
                        @if($type === 'chats')
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ $subFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ $subFilter === 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="declined" {{ $subFilter === 'declined' ? 'selected' : '' }}>Declined</option>
                        @elseif($type === 'courses')
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="active" {{ $subFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="inactive" {{ $subFilter === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                        @else
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>No filter needed</option>
                        @endif
                    </select>
                </div>

                <div class="col-md-2 col-6">
                    <label class="form-label text-secondary fw-semibold small">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date', optional($start ?? null)->toDateString() ?? '') }}" class="form-control rounded-3 py-2 border-light-subtle">
                </div>

                <div class="col-md-2 col-6">
                    <label class="form-label text-secondary fw-semibold small">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date', optional($end ?? null)->toDateString() ?? '') }}" class="form-control rounded-3 py-2 border-light-subtle">
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-semibold">
                        <i class="bi bi-search me-1"></i> Search
                    </button>
                </div>
            </div>

            <!-- Quick range shortcuts -->
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-range" data-range="today">Today</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-range" data-range="7">Last 7 Days</button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 quick-range" data-range="30">Last 30 Days</button>
                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 text-secondary quick-range" data-range="reset">Reset Filters</button>
            </div>
        </form>
    </div>

    <!-- Results Table Card -->
    <div class="results-card mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="fw-bold text-dark mb-2 mb-md-0">
                <i class="bi bi-table text-primary me-2"></i>
                @if($type === 'chats') Mentorship Requests & Chats @else My Courses @endif
            </h4>
            <div class="d-flex gap-3 text-secondary fs-6">
                <div>Records: <strong class="text-dark">{{ $summary['total'] ?? 0 }}</strong></div>
                @if($type === 'chats')
                    <div>Messages Exchanged: <strong class="text-success">{{ $summary['total_messages'] ?? 0 }}</strong></div>
                @elseif($type === 'courses')
                    <div>Total Enrolled: <strong class="text-success">{{ $summary['total_enrollments'] ?? 0 }}</strong></div>
                @endif
            </div>
        </div>

        @if($type === 'chats')
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Contact Info</th>
                            <th>Course</th>
                            <th>College</th>
                            <th>Status</th>
                            <th>Messages</th>
                            <th>Requested Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $chat)
                            <tr>
                                <td class="fw-bold">{{ $chat->student?->name ?? 'N/A' }}</td>
                                <td>{{ $chat->student?->email ?? '-' }}</td>
                                <td class="fw-semibold">{{ $chat->course?->title ?? 'N/A' }}</td>
                                <td>{{ $chat->course?->college?->institution_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge-status badge-{{ $chat->status }}">
                                        {{ ucfirst($chat->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary rounded-pill px-2">
                                        {{ $chat->messages_count }}
                                    </span>
                                </td>
                                <td>{{ $chat->created_at->format('d M Y h:i A') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No mentorship requests match the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        @elseif($type === 'courses')
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Course Code / ID</th>
                            <th>Course Title</th>
                            <th>Category</th>
                            <th>College</th>
                            <th>Price</th>
                            <th>Enrollments</th>
                            <th>Status</th>
                            <th>Assigned Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $course)
                            <tr>
                                <td class="fw-mono">#{{ $course->id }}</td>
                                <td class="fw-bold">{{ $course->title }}</td>
                                <td>{{ $course->category?->name ?? 'N/A' }}</td>
                                <td>{{ $course->college?->institution_name ?? 'N/A' }}</td>
                                <td>
                                    @if($course->price == 0)
                                        <span class="badge bg-success-subtle text-success">Free</span>
                                    @else
                                        ₹{{ number_format($course->price, 2) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary rounded-pill px-2">
                                        {{ $course->enrollments_count }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-status badge-{{ $course->status }}">
                                        {{ ucfirst($course->status) }}
                                    </span>
                                </td>
                                <td>{{ $course->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-secondary">
                                    <i class="bi bi-journal-x fs-2 d-block mb-2"></i> No courses match the filter criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Pagination -->
        @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->hasPages())
            <div class="mt-4">
                {{ $results->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.querySelectorAll('.auto-submit').forEach(el => {
            el.addEventListener('change', () => {
                document.getElementById('reportFilterForm').submit();
            });
        });

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
                    document.getElementById('reportFilterForm').submit();
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
                document.getElementById('reportFilterForm').submit();
            });
        });
    </script>
    @endpush
</x-mentor.layout>
