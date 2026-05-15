<x-mentor.layout title="Mentorship Requests - EduConnect" active="requests">
    @push('styles')
    <style>
        .request-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.04);
            transition: 0.2s;
        }
        .request-card:hover {
            box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        }
        .filter-select {
            border-radius: 50px;
            padding: 0.6rem 1.5rem;
            border: none;
            background: #f1f5f9;
            font-weight: 500;
        }
    </style>
    @endpush

    <div class="mb-4 mt-4">
        <h1 class="fw-bold mb-1">
            <i class="bi bi-chat-dots-fill me-2 text-primary"></i> Mentorship Requests
        </h1>
        <p class="text-secondary mb-0">Review and manage student mentorship requests across your assigned courses.</p>
    </div>

    <!-- Filter Controls -->
    <div class="card border-0 shadow-sm rounded-4 p-3 p-md-4 mb-4 bg-white">
        <form method="GET" action="{{ url()->current() }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold small text-secondary">Filter by Course</label>
                <select name="course_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small text-secondary">Status</label>
                <select name="status" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                    <option value="declined" {{ request('status') == 'declined' ? 'selected' : '' }}>Declined</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                <a href="{{ url()->current() }}" class="btn btn-light rounded-pill px-4">Reset</a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">Apply</button>
            </div>
        </form>
    </div>

    <!-- Requests List -->
    <div class="row g-3">
        @forelse($requests as $request)
            <div class="col-lg-6">
                <div class="request-card p-4 d-flex flex-column h-100">
                    <!-- Student info header -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            @if($request->studentProfile?->photo)
                                <img src="{{ asset('storage/' . $request->studentProfile->photo) }}" class="rounded-circle" width="48" height="48" style="object-fit: cover;">
                            @else
                                <div class="avatar-circle" style="width:48px;height:48px;font-size:1rem;">
                                    {{ strtoupper(substr($request->student?->name ?? 'S', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="ms-3">
                            <h6 class="fw-bold mb-0">{{ $request->student?->name ?? 'Student' }}</h6>
                            <small class="text-muted">{{ $request->studentProfile?->current_qualification ?? 'Student' }}</small>
                        </div>
                        <div class="ms-auto">
                            @if($request->status === 'pending')
                                <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                            @elseif($request->status === 'accepted')
                                <span class="badge bg-success-subtle text-success border border-success">Accepted</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger">Declined</span>
                            @endif
                        </div>
                    </div>

                    <!-- Course info -->
                    <div class="mb-3 p-3 bg-light rounded-4 small">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-book me-1"></i> Course:</span>
                            <strong>{{ $request->course?->title ?? 'Course' }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <span><i class="bi bi-building me-1"></i> College:</span>
                            <span>{{ $request->course?->college?->institution_name ?? 'N/A' }}</span>
                        </div>
                        @if($request->course)
                            <div class="d-flex justify-content-between mt-1">
                                <span><i class="bi bi-people me-1"></i> Enrolled:</span>
                                <span>{{ $request->course->enrollments_count ?? 0 }} students</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mt-1">
                            <span><i class="bi bi-calendar3 me-1"></i> Requested:</span>
                            <span>{{ $request->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="mt-auto d-flex gap-2">
                        @if($request->status === 'pending')
                            <form action="{{ route('mentor.chat.accept', $request->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-success rounded-pill w-100 py-2 fw-semibold">
                                    <i class="bi bi-check-circle me-1"></i> Accept
                                </button>
                            </form>
                            <form action="{{ route('mentor.chat.decline', $request->id) }}" method="POST" class="flex-grow-1">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-semibold">
                                    <i class="bi bi-x-circle me-1"></i> Decline
                                </button>
                            </form>

                        @elseif($request->status === 'accepted')
                            <a href="{{ route('mentor.chat.show', $request->id) }}" 
                            class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">
                                <i class="bi bi-chat-square-text-fill me-1"></i> Open Chat
                            </a>

                        @else
                            <button class="btn btn-light rounded-pill w-100 py-2 fw-semibold" disabled>
                                Request Declined
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border rounded-4 text-center py-5" style="background: linear-gradient(135deg, #f5f7ff, #eef1fa);">
                    <i class="bi bi-inbox fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">No Requests Found</h5>
                    <p class="text-secondary">No mentorship requests match the current filters.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(method_exists($requests, 'links'))
        <nav class="mt-5 d-flex justify-content-center">
            {{ $requests->links() }}
        </nav>
    @endif
</x-mentor.layout>