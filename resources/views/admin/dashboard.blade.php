
<x-admin.layout active="dashboard">


    <!-- Dashboard Content -->
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Dashboard</h1>
            <p class="text-muted">Welcome back, Admin. Here's what's happening today.</p>
        </div>
        <div class="text-muted">
            <i class="fas fa-calendar-alt me-1"></i> {{ date('F j, Y') }}
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.students.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalStudents }}</h3>
                    <p class="stat-label">Total Students</p>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.colleges.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalColleges }}</h3>
                    <p class="stat-label">Colleges</p>
                    <small class="text-warning">{{ $pendingColleges }} pending</small>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.firms.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalFirms }}</h3>
                    <p class="stat-label">Firms</p>
                    <small class="text-warning">{{ $pendingFirms }} pending</small>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.mentors.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fas fa-chalkboard-user"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalMentors }}</h3>
                    <p class="stat-label">Mentors</p>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalCourses }}</h3>
                    <p class="stat-label">Total Courses</p>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.categories.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalCategories }}</h3>
                    <p class="stat-label">Categories</p>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('admin.enrollments.index') }}" class="text-decoration-none">
            <div class="stat-card">
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalEnrollments }}</h3>
                    <p class="stat-label">Enrollments</p>
                    <small class="text-warning">{{ $pendingEnrollments }} pending</small>
                </div>
            </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="stat-icon bg-dark-subtle text-dark">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-details">
                    <h3 class="stat-value">{{ $totalUsers }}</h3>
                    <p class="stat-label">Total Users</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Activity Row -->
    <div class="row g-4">
        <!-- Chart Card -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="card-title mb-0">Enrollment Trends</h5>
                    <p class="text-muted small">Monthly course enrollments</p>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Pending Approvals Table -->
        <div class="col-xl-6">
            <div class="card h-100">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="card-title mb-0">Pending Approvals</h5>
                    <p class="text-muted small">Colleges & Firms awaiting verification</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Registered On</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingList as $item)
                                <tr>
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($item->role) }}</span></td>
                                    <td>{{ $item->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if ($item->role =="firm")
                                            <a href="{{ route('admin.firms.edit', $item->firm->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-check-circle"></i> Review
                                        </a>
                                        @else
                                         <a href="{{ route('admin.colleges.edit', $item->college->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-check-circle"></i> Review
                                        </a>
                                            
                                        @endif

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No pending approvals</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 text-end">
                    <a href="{{ route('admin.enrollments.index') }}" class="text-decoration-none">View all <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments Table (Optional) -->
    <div class="row mt-4">
        <div class="col-12">
            <a href="{{ route('admin.enrollments.index') }}" class="text-decoration-none">
            <div class="card">
                <div class="card-header bg-white border-bottom-0 pt-3">
                    <h5 class="card-title mb-0">Recent Enrollments</h5>
                    <p class="text-muted small">Latest student and firm enrollments</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User / Firm</th>
                                    <th>Course</th>
                                    <th>College</th>
                                    <th>Enrolled On</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentEnrollments as $enrollment)
                                <tr>
                                    <td>{{ $enrollment->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->course?->title ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->course?->college?->institution_name ?? 'N/A' }}</td>
                                    <td>{{ $enrollment->created_at->format('d M Y') }}</td>
                                    <td>
                                        @if($enrollment->status == 'confirmed')
                                            <span class="badge bg-success">Confirmed</span>
                                        @elseif($enrollment->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No recent enrollments</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        border: 1px solid #eef2f6;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .bg-primary-subtle { background-color: #e0f2fe; }
    .bg-success-subtle { background-color: #dcfce7; }
    .bg-info-subtle { background-color: #e0f2fe; }
    .bg-warning-subtle { background-color: #fef9c3; }
    .bg-danger-subtle { background-color: #fee2e2; }

    .stat-details {
        flex: 1;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
        color: #0f172a;
    }

    .stat-label {
        margin: 0;
        color: #475569;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transition: box-shadow 0.2s;
    }

    .card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .table {
        font-size: 0.9rem;
    }

    .table th {
        font-weight: 600;
        color: #334155;
        border-bottom-width: 1px;
    }

    .badge {
        font-weight: 500;
        padding: 0.35em 0.8em;
    }

    @media (max-width: 768px) {
        .stat-card {
            padding: 1rem;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            font-size: 1.4rem;
        }
        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<!-- Chart.js CDN - include once in master layout or here -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Actual enrollment data from backend
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Student Enrollments',
                    data: {{ json_encode($studentData) }},
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0d6efd',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }, {
                    label: 'Firm Bulk Enrollments',
                    data: {{ json_encode($firmData) }},
                    borderColor: '#20c997',
                    backgroundColor: 'rgba(32, 201, 151, 0.05)',
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#20c997',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#eef2f6'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush

</x-admin.layout>