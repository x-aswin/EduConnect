<x-college.layout active="mentors">

@php
    $isEdit = isset($editMentor) && !isset($viewOnly);
    $isView = isset($editMentor) && isset($viewOnly);
    $isCreate = !isset($editMentor);
    $queryParams = collect(request()->query())->except('mode')->toArray();
    $indexUrl = route('college.mentors.index', $queryParams);
    $totalCount = $mentors->count();
    $activeCount = $mentors->filter(fn ($mentor) => ($mentor->user->status ?? 'pending') === 'active')->count();
    $pendingCount = $mentors->filter(fn ($mentor) => ($mentor->user->status ?? 'pending') === 'pending')->count();
    $blockedCount = $mentors->filter(fn ($mentor) => ($mentor->user->status ?? 'pending') === 'blocked')->count();
@endphp

<div class="mentor-hero p-4 p-md-5 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <h3 class="fw-bold mb-1 text-white">Mentor Workspace</h3>
            <p class="mb-0 text-white-50">Manage mentor profiles, discover expertise, and keep account statuses under control.</p>
        </div>
        <button type="button" class="btn btn-light fw-semibold" data-bs-toggle="modal" data-bs-target="#addMentorModal">
            <i class="bi bi-plus-lg"></i> Add New Mentor
        </button>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Total</div>
                <div class="text-white fw-bold fs-4">{{ $totalCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Active</div>
                <div class="text-white fw-bold fs-4">{{ $activeCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Pending</div>
                <div class="text-white fw-bold fs-4">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="hero-stat p-3 rounded-3">
                <div class="text-white-50 small">Blocked</div>
                <div class="text-white fw-bold fs-4">{{ $blockedCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4 border-0 filter-studio">
    <div class="card-body p-4 p-md-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <div>
                <h6 class="mb-0 fw-bold">Filter Workspace</h6>
                <p class="mb-0 small text-muted">Refine mentors by profile, expertise, status, and registration period.</p>
            </div>
            <span class="filter-chip"><i class="bi bi-funnel me-1"></i> Precision Filters</span>
        </div>
        <form method="GET" action="{{ route('college.mentors.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control filter-control" placeholder="Name, email, expertise, qualification">
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Status</label>
                    <select name="status" class="form-select filter-control">
                        <option value="">All</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>Blocked</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Sort</label>
                    <select name="sort" class="form-select filter-control">
                        <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Most Recent</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Date Range</label>
                    <select name="date_filter" id="mentor_date_filter" class="form-select filter-control">
                        <option value="all" {{ request('date_filter', 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="last_7" {{ request('date_filter') === 'last_7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last_30" {{ request('date_filter') === 'last_30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last_90" {{ request('date_filter') === 'last_90' ? 'selected' : '' }}>Last 90 Days</option>
                        <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <div class="col-md-3 mentor-custom-date {{ request('date_filter') === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control filter-control">
                </div>

                <div class="col-md-3 mentor-custom-date {{ request('date_filter') === 'custom' ? '' : 'd-none' }}">
                    <label class="form-label text-muted fw-semibold small text-uppercase mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control filter-control">
                </div>

                <div class="col-12 d-flex flex-wrap gap-2 pt-1">
                    <button type="submit" class="btn btn-primary px-4 filter-btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="{{ route('college.mentors.index') }}" class="btn btn-outline-secondary filter-btn-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4 shadow-sm border-0 mentor-hub">
    <div class="card-header mentor-hub-header py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h6 class="mb-0 fw-bold">Manage Mentors</h6>
            <p class="mb-0 small text-muted">Browse profiles and take actions from one focused list.</p>
        </div>
        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">{{ $mentors->count() }} Found</span>
    </div>
    <div class="card-body p-4 p-md-5">
        @if($mentors->isEmpty())
            <div class="text-center py-5">
                <div class="empty-state-icon mb-2"><i class="bi bi-search"></i></div>
                <h6 class="fw-bold mb-1">No mentors match the current filters</h6>
                <p class="text-muted mb-3">Try resetting filters or adding a new mentor profile.</p>
                <a href="{{ route('college.mentors.index') }}" class="btn btn-outline-secondary me-2">Reset Filters</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMentorModal">
                    <i class="bi bi-plus-lg"></i> Add New Mentor
                </button>
            </div>
        @else
            <div class="d-grid gap-3">
                @foreach($mentors as $mentor)
                    <div class="mentor-item">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="d-flex gap-3 align-items-start">
                                <img
                                    src="{{ $mentor->photo ? asset('storage/' . $mentor->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($mentor->user->name ?? 'Unknown Mentor') }}"
                                    class="rounded-circle mentor-avatar"
                                    alt="Mentor avatar"
                                >
                                <div>
                                    <div class="fw-semibold fs-6">{{ $mentor->user->name ?? 'Unknown Mentor' }}</div>
                                    <div class="text-muted small d-flex align-items-center gap-1 flex-wrap">
                                        <i class="bi bi-envelope"></i>
                                        <span>{{ $mentor->user->email ?? 'N/A' }}</span>
                                    </div>
                                    <div class="small text-secondary mt-2 d-flex align-items-center gap-2 flex-wrap">
                                        <span class="profile-chip"><i class="bi bi-mortarboard"></i> {{ $mentor->qualification ?? 'N/A' }}</span>
                                        <span class="profile-chip"><i class="bi bi-stars"></i> {{ $mentor->expertise ?? 'N/A' }}</span>
                                    </div>
                                    @if($mentor->bio)
                                        <div class="small text-secondary mt-2 mentor-bio">{{ \Illuminate\Support\Str::limit($mentor->bio, 140) }}</div>
                                    @endif
                                    <div class="small text-secondary mt-2 d-flex align-items-center gap-1">
                                        <i class="bi bi-clock-history"></i> Joined: {{ $mentor->created_at?->format('d M Y') }}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                @if(($mentor->user->status ?? null) === 'active')
                                    <span class="badge bg-success-subtle text-success border border-success">Active</span>
                                @elseif(($mentor->user->status ?? null) === 'blocked')
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Blocked</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                @endif
                            </div>

                        <div class="d-flex justify-content-end mt-3">
                            <div class="btn-group action-group gap-2" role="group">
                                <a href="{{ route('college.mentors.show', array_merge([$mentor->id], $queryParams)) }}">
                                    <button class="btn btn-sm btn-outline-primary" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </a>
                                <a href="{{ route('college.mentors.edit', array_merge([$mentor->id], $queryParams)) }}">
                                    <button class="btn btn-sm btn-outline-warning" title="Edit Mentor">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </a>
                                <a href="{{ route('college.mentors.edit', array_merge([$mentor->id], $queryParams, ['mode' => 'delete'])) }}">
                                    <button class="btn btn-sm btn-outline-danger" title="Delete Mentor">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="addMentorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"><div class="modal-content">
            <div class="modal-header bg-primary text-white">
               <h5 class="modal-title">
{{ $isEdit ? 'Edit Mentor: ' . $editMentor->user->name : ($isView ? 'Mentor Profile: ' . $editMentor->user->name : 'Add New Mentor') }}
</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ isset($editMentor) ? route('college.mentors.update', array_merge([$editMentor->id], $queryParams)) : route('college.mentors.store', $queryParams) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($editMentor))
                    @method('PATCH')
                @endif
                <div class="modal-body">
                    @if(!$isCreate)
                        <div class="text-center mb-4">
                            <img src="{{ $editMentor->photo ? asset('storage/' . $editMentor->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($editMentor->user->name) }}"
                                class="rounded-circle img-thumbnail shadow-sm"
                                style="width: 120px; height: 120px; object-fit: cover;"
                                alt="Mentor profile">
                            @if($isView)
                                <h4 class="mt-2">{{ $editMentor->user->name }}</h4>
                            @endif
                        </div>
                    @endif

                    <div class="row g-3">
                        <h6 class="border-bottom pb-2">Account Information</h6>
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $editMentor->user->name ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $editMentor->user->email ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if(!$isView)
                        <div class="col-md-6">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="col-md-6">
                            <label class="form-label">Account Status</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" {{ $isView ? 'disabled' : '' }}>
                                <option value="pending" {{ old('status', $editMentor->user->status ?? 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="active" {{ old('status', $editMentor->user->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="blocked" {{ old('status', $editMentor->user->status ?? '') == 'blocked' ? 'selected' : '' }}>Blocked</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h6 class="border-bottom pb-2 mt-4">Mentor Details</h6>
                        <div class="col-md-6">
                            <label class="form-label">Qualification</label>
                            <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $editMentor->qualification ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Expertise</label>
                            <input type="text" name="expertise" class="form-control @error('expertise') is-invalid @enderror" value="{{ old('expertise', $editMentor->expertise ?? '') }}" {{ $isView ? 'disabled' : '' }}>
                            @error('expertise')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (!$isView)
                        <div class="col-md-6">
                            <label class="form-label">Mentor Photo</label>
                            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="col-md-12">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3" {{ $isView ? 'disabled' : '' }}>{{ old('bio', $editMentor->bio ?? '') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    @if(isset($editMentor))
                        <a href="{{ $indexUrl }}" class="btn btn-secondary">Cancel</a>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    @endif

                    @if (!$isView)
                    <button type="submit" class="btn btn-primary">
                        {{ isset($editMentor) ? 'Update Mentor' : 'Create Mentor Account' }}
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

@if(isset($deleteMentor) && isset($isDeleteMode) && $isDeleteMode)
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete Mentor</h5>
                    <a href="{{ $indexUrl }}" class="btn-close btn-close-white"></a>
                </div>
                <div class="modal-body text-center">
                    <p>Delete <strong>{{ $deleteMentor->user->name ?? 'Unknown Mentor' }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{ route('college.mentors.destroy', array_merge([$deleteMentor->id], $queryParams)) }}" method="POST">
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
                    window.location.href = "{{ $indexUrl }}";
                }
            });
        });
    </script>
@endif

<x-toast />
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var myModalElement = document.getElementById('addMentorModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
        });
    </script>
@endif

@if(isset($editMentor))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editModal = new bootstrap.Modal(document.getElementById('addMentorModal'));
            editModal.show();

            var myModalElement = document.getElementById('addMentorModal');
            myModalElement.addEventListener('hidden.bs.modal', function () {
                if (window.location.pathname.includes('/edit') || window.location.pathname.match(/\d+$/)) {
                    window.location.href = "{{ $indexUrl }}";
                }
            });
        });
    </script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var dateFilter = document.getElementById('mentor_date_filter');
        var customDateFields = document.querySelectorAll('.mentor-custom-date');

        function toggleCustomDateFields() {
            var showCustom = dateFilter && dateFilter.value === 'custom';
            customDateFields.forEach(function (field) {
                field.classList.toggle('d-none', !showCustom);
            });
        }

        if (dateFilter) {
            dateFilter.addEventListener('change', toggleCustomDateFields);
            toggleCustomDateFields();
        }
    });
</script>

<style>
    .mentor-hero {
        border-radius: 1.25rem;
        background: linear-gradient(130deg, #0d47a1 0%, #0288d1 55%, #26c6da 100%);
        box-shadow: 0 14px 35px rgba(13, 71, 161, 0.26);
    }

    .hero-stat {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(4px);
    }

    .filter-studio {
        border-radius: 1.1rem;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 22px rgba(16, 24, 40, 0.08);
    }

    .filter-chip {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
        text-transform: uppercase;
        color: #0d47a1;
        background: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 999px;
        padding: 0.38rem 0.72rem;
    }

    .filter-control {
        border-radius: 0.72rem;
        border-color: #d0d5dd;
    }

    .filter-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.16);
    }

    .filter-btn-primary,
    .filter-btn-reset {
        border-radius: 0.72rem;
        font-weight: 600;
    }

    .mentor-hub {
        border-radius: 1.1rem;
        overflow: hidden;
    }

    .mentor-hub-header {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid #e4e7ec;
    }

    .mentor-item {
        border: 1px solid #eaecf0;
        border-left: 4px solid #0288d1;
        border-radius: 1rem;
        padding: 1rem;
        background: #ffffff;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .mentor-item:hover {
        transform: translateY(-2px);
        border-color: #90caf9;
        box-shadow: 0 10px 24px rgba(2, 136, 209, 0.14);
    }

    .mentor-avatar {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border: 2px solid #e3f2fd;
        box-shadow: 0 4px 10px rgba(2, 136, 209, 0.16);
    }

    .profile-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.78rem;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 0.3rem 0.55rem;
    }

    .mentor-bio {
        line-height: 1.45;
        max-width: 75ch;
    }

    .action-group .btn {
        border-radius: 0.65rem;
    }

    .empty-state-icon {
        width: 52px;
        height: 52px;
        margin-inline: auto;
        border-radius: 999px;
        background: #eff6ff;
        color: #2563eb;
        display: grid;
        place-items: center;
        font-size: 1.15rem;
    }

    @media (max-width: 768px) {
        .mentor-item {
            padding: 0.9rem;
        }

        .mentor-avatar {
            width: 48px;
            height: 48px;
        }

        .mentor-bio {
            max-width: 100%;
        }
    }
</style>

</x-college.layout>
