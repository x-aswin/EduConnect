<x-student.layout title="My Reports - EduConnect" active="reports">
    @push('styles')
    <style>
        .report-header {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            border-radius: 2rem;
            color: white;
            padding: 2.5rem;
            box-shadow: 0 15px 30px rgba(59, 130, 246, 0.15);
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

        .badge-confirmed {
            background-color: #dcfce7;
            color: #15803d;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .badge-paid {
            background-color: #ecfdf5;
            color: #047857;
        }

        .badge-unpaid {
            background-color: #fff7ed;
            color: #c2410c;
        }

        .badge-accepted {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-declined {
            background-color: #f1f5f9;
            color: #475569;
        }
    </style>
    @endpush

    <!-- Header Section -->
    <div class="report-header mt-4">
        <span class="badge bg-white bg-opacity-20 text-black px-3 py-2 rounded-pill mb-2">
            <i class="bi bi-graph-up me-1"></i> Personal Performance & Activity
        </span>
        <h1 class="fw-bold mt-2 mb-1">My Reports & Statistics</h1>
        <p class="text-white text-opacity-80 mb-0">Track your enrollment statuses, paid fees, certificates issued, and mentorship history.</p>
    </div>

    <!-- Overview Stats Rows -->
    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
                    <i class="bi bi-journal-album fs-4 text-primary"></i>
                </div>
                <div>
                    <span class="text-secondary small">Total Enrolled</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['total_enrolled'] }}</h4>
                    <small class="text-muted">Courses</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3">
                    <i class="bi bi-patch-check-fill fs-4 text-success"></i>
                </div>
                <div>
                    <span class="text-secondary small">Completed</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['total_completed'] }}</h4>
                    <small class="text-muted">Certificates issued</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-purple bg-opacity-10 p-3 me-3" style="background-color: rgba(139, 92, 246, 0.1);">
                    <i class="bi bi-wallet2 fs-4" style="color: #8b5cf6;"></i>
                </div>
                <div>
                    <span class="text-secondary small">Total Paid</span>
                    <h4 class="fw-bold mb-0 text-dark">₹{{ number_format($overview['total_spent'], 2) }}</h4>
                    <small class="text-muted">Invested in learning</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="report-stat-card d-flex align-items-center">
                <div class="rounded-4 bg-info bg-opacity-10 p-3 me-3">
                    <i class="bi bi-chat-heart fs-4 text-info"></i>
                </div>
                <div>
                    <span class="text-secondary small">Mentorships</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $overview['active_chats'] }}</h4>
                    <small class="text-muted">Active connections</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filter-card">
        <h4 class="fw-bold mb-3 text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Filter Report Data</h4>
        <form method="GET" action="{{ route('student.reports.index') }}" id="reportFilterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Report Category</label>
                    <select name="type" class="form-select rounded-3 py-2 auto-submit border-light-subtle">
                        <option value="enrollments" {{ $type === 'enrollments' ? 'selected' : '' }}>Enrollments Report</option>
                        <option value="payments" {{ $type === 'payments' ? 'selected' : '' }}>Payments & Billing</option>
                        <option value="mentorships" {{ $type === 'mentorships' ? 'selected' : '' }}>Mentorship History</option>
                        <option value="certificates" {{ $type === 'certificates' ? 'selected' : '' }}>Certificates Earned</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-secondary fw-semibold small">Sub-Filter</label>
                    <select name="sub_filter" class="form-select rounded-3 py-2 auto-submit border-light-subtle">
                        @if($type === 'enrollments')
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                            <option value="pending" {{ $subFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $subFilter === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ $subFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        @elseif($type === 'payments')
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>All Payments</option>
                            <option value="paid" {{ $subFilter === 'paid' ? 'selected' : '' }}>Paid Only</option>
                            <option value="unpaid" {{ $subFilter === 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                        @elseif($type === 'mentorships')
                            <option value="all" {{ $subFilter === 'all' ? 'selected' : '' }}>All Mentor Statuses</option>
                            <option value="pending" {{ $subFilter === 'pending' ? 'selected' : '' }}>Pending Request</option>
                            <option value="accepted" {{ $subFilter === 'accepted' ? 'selected' : '' }}>Active Chat</option>
                            <option value="declined" {{ $subFilter === 'declined' ? 'selected' : '' }}>Declined</option>
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
                @if($type === 'enrollments') Enrollments @elseif($type === 'payments') Payments @elseif($type === 'mentorships') Mentorships @else Certificates @endif
            </h4>
            <div class="d-flex gap-3 text-secondary fs-6">
                <div>Total Records: <strong class="text-dark">{{ $summary['total'] ?? 0 }}</strong></div>
                @if($type === 'payments')
                    <div>Total Paid: <strong class="text-success">₹{{ number_format($summary['total_amount'] ?? 0, 2) }}</strong></div>
                @endif
            </div>
        </div>

        @if($type === 'enrollments')
            <div class="table-responsive">
                <table class="table align-middle mb-0 export-data">
    <thead>
        <tr>
            <th>Enrollment ID</th>
            <th>Course Details</th>
            <th>College</th>
            <th>Venue & Schedule</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Payment</th>
            <th>Certificate</th>
            <th>Enrolled Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $enrollment)
            <tr>
                {{-- ID --}}
                <td class="fw-bold">#{{ $enrollment->id }}</td>

                {{-- Course Title & Category --}}
                <td>
                    <span class="fw-semibold d-block">{{ $enrollment->course?->title ?? 'N/A' }}</span>
                    @if($enrollment->course?->category)
                        <small class="text-muted">{{ $enrollment->course->category->name }}</small>
                    @endif
                </td>

                {{-- College Name --}}
                <td>{{ $enrollment->course?->college?->institution_name ?? 'N/A' }}</td>

                {{-- Venue & Time Slot / Dates --}}
                <td class="small">
                    <div>
                        <strong>Venue:</strong> {{ $enrollment->course?->venue ?? 'Online / TBD' }}
                    </div>
                    @if($enrollment->course?->start_date)
                        <div>
                            <strong>Dates:</strong> 
                            {{ \Carbon\Carbon::parse($enrollment->course->start_date)->format('d M Y') }}
                            @if($enrollment->course->end_date)
                                - {{ \Carbon\Carbon::parse($enrollment->course->end_date)->format('d M Y') }}
                            @endif
                        </div>
                    @endif
                    @if($enrollment->course?->time_slot)
                        <div class="text-muted"><strong>Time:</strong> {{ $enrollment->course->time_slot }}</div>
                    @endif
                </td>

                {{-- Total Amount / Price --}}
                <td>
                    @if((float)($enrollment->total_amount ?? $enrollment->course?->price ?? 0) === 0.0)
                        <span class="badge bg-success">Free</span>
                    @else
                        ₹{{ number_format($enrollment->total_amount ?? $enrollment->course->price, 2) }}
                    @endif
                </td>

                {{-- Status --}}
                <td>
                    <span class="badge-status badge-{{ $enrollment->status }}">
                        {{ ucfirst($enrollment->status ?? 'pending') }}
                    </span>
                </td>

                {{-- Payment Status --}}
                <td>
                    @if((float)($enrollment->total_amount ?? $enrollment->course?->price ?? 0) === 0.0)
                        <span class="badge-status badge-paid">FREE</span>
                    @else
                        <span class="badge-status badge-{{ $enrollment->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                            {{ strtoupper($enrollment->payment_status ?? 'unpaid') }}
                        </span>
                    @endif
                </td>

                {{-- Certificate Badge / Code --}}
                <td>
                    @if($enrollment->certificate_issued)
                        <span class="badge bg-success" title="Code: {{ $enrollment->certificate_code }}">
                            <i class="bi bi-patch-check-fill me-1"></i> Issued
                        </span>
                    @elseif($enrollment->course?->is_certified)
                        <span class="badge bg-light text-dark border">Pending</span>
                    @else
                        <span class="text-muted small">N/A</span>
                    @endif
                </td>

                {{-- Created At --}}
                <td>{{ $enrollment->created_at ? $enrollment->created_at->format('d M Y h:i A') : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-5 text-secondary">
                    <i class="bi bi-inbox fs-2 d-block mb-2"></i> No enrollment data matches the filter criteria.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>

        @elseif($type === 'payments')
            <div class="table-responsive">
                <table class="table align-middle mb-0 export-data">
    <thead>
        <tr>
            <th>Transaction ID</th>
            <th>Course & Provider</th>
            <th>Original Price</th>
            <th>Amount Billed</th>
            <th>Enrollment Status</th>
            <th>Payment Status</th>
            <th>Payment Date</th>
            {{-- <th>Action</th> --}}
        </tr>
    </thead>
    <tbody>
        @forelse($results as $enrollment)
            <tr>
                {{-- Transaction / Invoice Ref --}}
                <td class="fw-bold text-nowrap">#TXN-{{ str_pad($enrollment->id, 5, '0', STR_PAD_LEFT) }}</td>

                {{-- Course Title & Offering College --}}
                <td>
                    <div class="fw-semibold">{{ $enrollment->course?->title ?? 'N/A' }}</div>
                    <small class="text-muted d-block">
                        <i class="bi bi-building me-1"></i>{{ $enrollment->course?->college?->institution_name ?? 'N/A' }}
                    </small>
                </td>

                {{-- Listed Course Price --}}
                <td class="text-muted">
                    @if((float)($enrollment->course?->price ?? 0) === 0.0)
                        Free
                    @else
                        ₹{{ number_format($enrollment->course->price, 2) }}
                    @endif
                </td>

                {{-- Billed Amount --}}
                <td class="fw-bold text-primary">
                    @if((float)($enrollment->total_amount ?? $enrollment->course?->price ?? 0) === 0.0)
                        <span class="text-success">₹0.00</span>
                    @else
                        ₹{{ number_format($enrollment->total_amount ?? $enrollment->course->price, 2) }}
                    @endif
                </td>

                {{-- Academic/Enrollment Status --}}
                <td>
                    <span class="badge-status badge-{{ $enrollment->status }}">
                        {{ ucfirst($enrollment->status ?? 'pending') }}
                    </span>
                </td>

                {{-- Financial Payment Status --}}
                <td>
                    @if((float)($enrollment->total_amount ?? $enrollment->course?->price ?? 0) === 0.0)
                        <span class="badge-status badge-paid">FREE</span>
                    @else
                        <span class="badge-status badge-{{ $enrollment->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
                            {{ strtoupper($enrollment->payment_status ?? 'unpaid') }}
                        </span>
                    @endif
                </td>

                {{-- Updated/Paid Timestamp --}}
                <td class="text-nowrap small text-muted">
                    {{ $enrollment->updated_at ? $enrollment->updated_at->format('d M Y, h:i A') : '-' }}
                </td>

                {{-- Action / Invoice button --}}
                {{-- <td>
                    @if($enrollment->payment_status === 'paid' || (float)($enrollment->total_amount ?? 0) === 0.0)
                        <button class="btn btn-sm btn-outline-secondary" onclick="window.print()" title="Print Receipt">
                            <i class="bi bi-receipt"></i>
                        </button>
                    @else
                        <span class="badge bg-warning text-dark">Pending Payment</span>
                    @endif
                </td> --}}
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-5 text-secondary">
                    <i class="bi bi-credit-card-2-front fs-2 d-block mb-2"></i> No billing or transaction records match the selected criteria.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>

        @elseif($type === 'mentorships')
            <div class="table-responsive">
                 <table class="table align-middle mb-0 export-data">
    <thead>
        <tr>
            <th>Mentor</th>
            <th>Related Course</th>
            <th>Request Status</th>
            <th>Messages</th>
            <th>Last Active</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $chat)
            <tr>
                {{-- Mentor Name & Expertise --}}
                <td>
                    <div class="fw-bold">{{ $chat->mentor?->name ?? 'N/A' }}</div>
                    @if($chat->mentorProfile?->expertise)
                        <small class="text-muted d-block">{{ $chat->mentorProfile->expertise }}</small>
                    @endif
                </td>

                {{-- Related Course Title --}}
                <td class="fw-semibold">{{ $chat->course?->title ?? 'N/A' }}</td>

                {{-- Chat Status --}}
                <td>
                    <span class="badge-status badge-{{ $chat->status }}">
                        {{ ucfirst($chat->status ?? 'pending') }}
                    </span>
                </td>

                {{-- Messages Count + Unread Badge --}}
                <td>
                    <span class="badge bg-light text-dark border me-1">
                        {{ $chat->messages_count ?? $chat->messages->count() }} msgs
                    </span>
                    @php
                        $unreadCount = $chat->messages->where('is_read', false)->where('sender_id', '!=', auth()->id())->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-danger">{{ $unreadCount }} new</span>
                    @endif
                </td>

                {{-- Last Updated --}}
                <td class="small text-muted">{{ $chat->updated_at ? $chat->updated_at->diffForHumans() : '-' }}</td>

                {{-- Chat Action Link --}}
                <td>
                   {{-- Change student.chats.show to student.chat.show --}}
<a href="{{ route('student.chat.show', $chat->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
    <i class="bi bi-chat-dots me-1"></i> Open Chat
</a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-5 text-secondary">
                    <i class="bi bi-chat-quote fs-2 d-block mb-2"></i> No mentorship request history found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
            </div>

        @elseif($type === 'certificates')
            <div class="table-responsive">
                <table class="table align-middle mb-0 export-data">
    <thead>
        <tr>
            <th>Certificate Code</th>
            <th>Course</th>
            <th>Issuing Institution</th>
            <th>Issued Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $enrollment)
            <tr>
                {{-- Certificate Code --}}
                <td>
                    <code class="fw-bold text-primary fs-6">{{ $enrollment->certificate_code ?? 'N/A' }}</code>
                </td>

                {{-- Course Title --}}
                <td class="fw-semibold">{{ $enrollment->course?->title ?? 'N/A' }}</td>

                {{-- Issuing Institution (College) --}}
                <td class="text-muted small">
                    <i class="bi bi-building me-1"></i>
                    {{ $enrollment->course?->college?->institution_name ?? ($enrollment->course?->college?->user?->name ?? 'N/A') }}
                </td>

                {{-- Issued Date --}}
                <td>
                    @if($enrollment->certificate_issued_at)
                        {{ $enrollment->certificate_issued_at->format('d M Y') }}
                    @else
                        <span class="badge bg-light text-secondary">N/A</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('student.certificates.download', $enrollment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                        
                        {{-- Public Verification Link --}}
                        @if($enrollment->certificate_code)
                            <a href="{{ route('certificate.verify', ['code' => $enrollment->certificate_code]) }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 text-secondary" title="Verify Certificate">
                                <i class="bi bi-patch-check me-1"></i> Verify
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-secondary">
                    <i class="bi bi-award fs-2 d-block mb-2"></i> No certificates earned yet. Keep learning!
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
                            title: '{{ ucfirst($type) }} Report - {{ date("Y-m-d") }}'
                        },
                        {
                            extend: 'csvHtml5',
                            className: 'btn-outline-info',
                            text: '<i class="fas fa-file-csv me-1"></i> Export CSV',
                            title: '{{ ucfirst($type) }} Report - {{ date("Y-m-d") }}'
                        },
                        {
                            extend: 'pdfHtml5',
                            className: 'btn-outline-danger',
                            text: '<i class="fas fa-file-pdf me-1"></i> Download PDF',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            title: '{{ ucfirst($type) }} Report - {{ date("Y-m-d") }}'
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
</x-student.layout>
