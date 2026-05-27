<x-college.layout active="dashboard">
  <!-- Page header with greeting & quick stats -->
  <div class="page-header flex-wrap">
    <div>
      <h1 class="h3 fw-bold mb-1">Welcome back, {{ $college_name }} 👋</h1>
      <p class="welcome-tag mb-0">
        <i class="bi bi-calendar-week me-1"></i> 
        Manage your offline courses, mentors, and enrollments — all in one place.
      </p>
    </div>
    <div class="mt-3 mt-sm-0">
      <a href="{{ route('college.courses.index', ['open' => 'add']) }}" class="btn btn-outline-primary rounded-pill px-4 me-2">
        <i class="bi bi-plus-lg"></i> New Course
      </a>
      <a href="#" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-download"></i> Report
      </a>
    </div>
  </div>

  <!-- Quick stats cards (college focused) -->
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-lg-3">
      <div class="card-placeholder p-3 d-flex align-items-center">
        <div class="rounded-3 bg-primary bg-opacity-10 p-3 me-3">
          <i class="bi bi-journal-text fs-3 text-primary"></i>
        </div>
        <div>
          <span class="text-secondary text-uppercase small fw-semibold">Active Courses</span>
          <h3 class="mb-0 fw-bold">{{ $totalcourses }}</h3>
          <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $totalCoursesCurrentMonth }} new this month</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card-placeholder p-3 d-flex align-items-center">
        <div class="rounded-3 bg-warning bg-opacity-10 p-3 me-3">
          <i class="bi bi-people fs-3 text-warning"></i>
        </div>
        <div>
          <span class="text-secondary text-uppercase small fw-semibold">Total Mentors</span>
          <h3 class="mb-0 fw-bold">{{ $totalMentors }}</h3>
          <small class="text-muted">+{{ $pendingMentors }} pending approval</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card-placeholder p-3 d-flex align-items-center">
        <div class="rounded-3 bg-success bg-opacity-10 p-3 me-3">
          <i class="bi bi-person-check fs-3 text-success"></i>
        </div>
        <div>
          <span class="text-secondary text-uppercase small fw-semibold">Enrollments</span>
          <h3 class="mb-0 fw-bold">{{ $totalEntrollments }}</h3>
          <small>{{ $pendingEntrollments }} pending review</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card-placeholder p-3 d-flex align-items-center">
        <div class="rounded-3 bg-info bg-opacity-10 p-3 me-3">
          <i class="bi bi-cash-stack fs-3 text-info"></i>
        </div>
        <div>
          <span class="text-secondary text-uppercase small fw-semibold">Revenue</span>
          <h3 class="mb-0 fw-bold">₹ {{ number_format($revenue, 2) }}</h3>
          <small>₹ {{ number_format($revenueThisMonth, 2) }} this month</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Placeholder: College dashboard content (example of what appears below navbar) -->
  <div class="card-placeholder">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="fw-semibold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Your Offline Courses</h5>
      <a href="{{ route('college.courses.index') }}" class="text-decoration-none small fw-semibold">View all <i class="bi bi-chevron-right"></i></a>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Course Title</th>
            <th>Type</th>
            <th>Mentor</th>
            <th>Seats</th>
            <th>Venue</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse ($recentCourses as $course)
            <tr>
              <td>
                <i class="bi bi-book me-2"></i>{{ $course->title }}
              </td>
              <td>
                <span class="badge {{ $course->course_type === 'student_only' ? 'bg-primary bg-opacity-10 text-primary' : 'bg-warning bg-opacity-10 text-warning' }} px-3 py-2">
                  {{ $course->course_type === 'student_only' ? 'Student' : 'Firm' }}
                </span>
              </td>

              <td>{{ $course->course_type === 'student_only' ? ($course->mentor?->user?->name ?? 'Unassigned') : 'N/A' }}</td>

              <td>
                @if($course->course_type === 'student_only')
                  {{ ($course->total_seats && $course->available_seats) ? ($course->total_seats - $course->available_seats).'/'.$course->total_seats : '—' }}
                @else
                  N/A
                @endif
              </td>

              <td>
                @if($course->course_type === 'student_only')
                  {{ $course->venue ?? '—' }}
                @else
                  N/A
                @endif
              </td>
              <td>
                <span class="badge {{ $course->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                  {{ ucfirst($course->status) }}
                </span>
              </td>
              <td><a href="{{ route('college.courses.edit', $course->id) }}" class="btn-sm btn-outline-secondary border-0"><i class="bi bi-three-dots"></i></a></td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center text-secondary py-4">No courses found for this college yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3 border-top pt-3 d-flex justify-content-between">
      <div><i class="bi bi-chat-dots me-1"></i> <strong>Mentorship chat</strong> — {{ $activeConversations }} active conversations</div>
      <a href="{{ route('college.mentors.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-4">Manage Mentors</a>
    </div>
  </div>

  <!-- Second row: enrollment requests & upcoming schedule (college specific) -->
  <div class="row g-4">
    <div class="col-md-7">
      <div class="card-placeholder h-100">
        <h5 class="fw-semibold mb-3"><i class="bi bi-inbox-fill me-2 text-primary"></i>Pending Enrollment Requests</h5>
        <ul class="list-group list-group-flush">
          @forelse ($pendingEnrollmentRequests as $request)
            <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom gap-3">
              <div>
                <span class="fw-semibold">
                  {{ $request->user?->name ?? 'Unknown user' }}
                  <span class="text-secondary">({{ ucfirst($request->type) }})</span>
                </span>
                <br>
                <small class="text-secondary">
                  Course: {{ $request->course?->title ?? 'Unknown course' }}
                  @if($request->participant_count)
                    · {{ $request->participant_count }} participants
                  @endif
                  @if($request->requested_venue)
                    · {{ $request->requested_venue }}
                  @endif
                </small>
              </div>
              <div class="text-end">
                @if($request->proposed_schedule)
                  <span class="badge bg-info text-dark px-3 py-2 me-2">
                    {{ \Carbon\Carbon::parse($request->proposed_schedule)->format('d M, h:i A') }}
                  </span>
                @endif
                <a href="{{ route('college.enrollments.edit', $request->id) }}"><button class="btn btn-sm btn-success rounded-pill px-3 me-1"><i class="bi bi-check-lg"></i> Review</button></a>
                {{-- <button class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-x-lg"></i> Reject</button> --}}
              </div>
            </li>
          @empty
            <li class="list-group-item px-0 py-3 text-secondary border-bottom-0">No pending enrollment requests.</li>
          @endforelse
        </ul>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card-placeholder h-100">
        <h5 class="fw-semibold mb-3"><i class="bi bi-calendar2-week me-2 text-primary"></i>Upcoming Offline Sessions</h5>
        @forelse ($upcomingSessions as $session)
          <div class="d-flex mb-3 border-bottom pb-3">
            <div class="me-3 text-center bg-light rounded-3 px-3 py-2">
              <span class="d-block fw-bold">{{ \Carbon\Carbon::parse($session->start_date)->format('d') }}</span>
              <small>{{ \Carbon\Carbon::parse($session->start_date)->format('M') }}</small>
            </div>
            <div>
              <strong>{{ $session->title }}</strong><br>
              <small>
                {{ $session->time_slot ?? 'Scheduled session' }}
                @if($session->mentor?->user?->name)
                  · Mentor: {{ $session->mentor?->user?->name }}
                @endif
                @if($session->venue)
                  · {{ $session->venue }}
                @endif
              </small>
            </div>
          </div>
        @empty
          <div class="text-secondary">No upcoming sessions have been scheduled yet.</div>
        @endforelse
        <div class="mt-4">
          <a href="{{ route('college.courses.index') }}" class="text-decoration-none fw-semibold"><i class="bi bi-calendar-plus"></i> Update schedule / venue</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Additional note: master page footer / contextual hint -->
  <div class="text-muted mt-4 small d-flex justify-content-between">
    <span><i class="bi bi-layout-three-columns"></i> College master layout · EduConnect v1.0</span>
    <span><i class="bi bi-chat"></i> Live mentorship active · {{ $activeConversations }} open chat(s)</span>
  </div>
</x-college.layout>
