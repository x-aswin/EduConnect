<x-student.layout title="Dashboard - EduConnect" active="dashboard">
  @push('styles')
  <style>
  /* ========================================
     DASHBOARD-SPECIFIC STYLES
     ======================================== */
  .welcome-card {
    background: white;
    border-radius: 2rem;
    padding: 2rem;
    border: none;
    box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    margin-bottom: 2rem;
  }

  .stat-card {
    background: white;
    border-radius: 1.5rem;
    padding: 1.4rem;
    border: none;
    box-shadow: 0 10px 20px -10px rgba(0,0,0,0.04);
    transition: 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 24px -8px rgba(0,0,0,0.08);
  }

  .course-card {
    border-radius: 1.4rem;
    border: none;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,0.04);
    transition: all 0.2s;
    background: white;
  }

  .course-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(0,0,0,0.08);
  }

  .badge-soft-success {
    background: #dcfce7;
    color: #166534;
    font-weight: 500;
    border-radius: 30px;
    padding: 0.35rem 1rem;
  }

  .section-title {
    font-weight: 700;
    font-size: 1.4rem;
  }

  .mentor-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid white;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
  }

  .progress {
    height: 8px;
    border-radius: 10px;
  }
  </style>
  @endpush

  <!-- Welcome Section -->
<div class="welcome-card d-flex flex-wrap align-items-center justify-content-between mb-4 mt-4">
    <div>
      <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2">
        <i class="bi bi-emoji-smile me-1"></i> Welcome back!
      </span>
      <h1 class="fw-bold mt-2 mb-1">Hello, {{ $studentName }} 👋</h1>
      <p class="text-secondary mb-0">Continue your offline learning journey. Your latest enrollments and chats are ready below.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="{{ route('student.explore.index') }}" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
        <i class="bi bi-search me-1"></i> Explore Courses
      </a>
      <a href="{{ route('student.my.enrollments') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 ms-2">
        <i class="bi bi-calendar-week"></i> My Schedule
      </a>
    </div>
  </div>

  <!-- Quick Stats Cards -->
  <div class="row g-4 mb-5">
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center">
        <div class="rounded-4 bg-primary bg-opacity-10 p-3 me-3">
          <i class="bi bi-bookmark-check-fill fs-4 text-primary"></i>
        </div>
        <div>
          <span class="text-secondary small">Enrolled</span>
          <h4 class="fw-bold mb-0">{{ $enrolledCount }}</h4>
          <small class="text-success">Active courses</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center">
        <div class="rounded-4 bg-warning bg-opacity-10 p-3 me-3">
          <i class="bi bi-calendar-check fs-4 text-warning"></i>
        </div>
        <div>
          <span class="text-secondary small">Upcoming</span>
          <h4 class="fw-bold mb-0">{{ $upcomingCount }}</h4>
          <small>Offline sessions</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center">
        <div class="rounded-4 bg-info bg-opacity-10 p-3 me-3">
          <i class="bi bi-people-fill fs-4 text-info"></i>
        </div>
        <div>
          <span class="text-secondary small">Mentors</span>
          <h4 class="fw-bold mb-0">{{ $mentorCount }}</h4>
          <small>Assigned</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card d-flex align-items-center">
        <div class="rounded-4 bg-success bg-opacity-10 p-3 me-3">
          <i class="bi bi-chat-left-text-fill fs-4 text-success"></i>
        </div>
        <div>
          <span class="text-secondary small">Unread chats</span>
          <h4 class="fw-bold mb-0">{{ $unreadChatsCount }}</h4>
          <small>From mentors</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Recommended Courses Section -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="section-title"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Recommended for You</h2>
    <a href="{{ route('student.explore.index') }}" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4 mb-5">
@forelse($recommendedCourses as $course)
<div class="col-md-4 mb-4"> {{-- Added margin bottom for cleaner grids --}}
  <div class="course-card p-0 h-100 d-flex flex-column justify-content-between">
    <div>
      {{-- CARD HEADER IMAGE BLOCK --}}
      <div class="position-relative overflow-hidden d-flex align-items-center justify-content-center" style="height: 140px; background: {{ $course['gradient'] }};">
        @if(!empty($course['image_url']))
          <img src="{{ $course['image_url'] }}" alt="{{ $course['title'] }}" class="w-100 h-100" style="object-fit: cover; object-position: center;">
        @else
          {{-- Smooth fallback to icon style --}}
          <div class="d-flex align-items-center justify-content-center h-100 w-100">
            <i class="bi {{ $course['icon'] }} fs-1 {{ $course['icon_color'] }}"></i>
          </div>
        @endif

        {{-- Absolute badges placed on top of image overlay --}}
        {{-- <span class="badge {{ $course['badge_class'] }} position-absolute top-0 start-0 m-3 shadow-sm rounded-pill" style="font-size: 11px;">
          {{ $course['badge_label'] }}
        </span> --}}
      </div>

      <div class="p-3">
        <h5 class="fw-bold mt-1 text-dark">{{ $course['title'] }}</h5>
        <p class="small text-secondary mb-2">
          <i class="bi bi-geo-alt me-1"></i> {{ $course['venue'] }} · 
          <i class="bi bi-calendar3 ms-2"></i> Starts {{ $course['start_date'] }}
        </p>
        
        <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 13px;">
          <span class="text-secondary"><i class="bi bi-person me-1"></i> {{ $course['seats_label'] }}</span>
          <span class="fw-bold text-primary">{{ $course['progress'] }}% Filled</span>
        </div>
        
        <div class="progress mt-1" style="height: 6px; border-radius: 10px;">
          <div class="progress-bar bg-primary rounded-pill transition-all" role="progressbar" style="width: {{ $course['progress'] }}%"></div>
        </div>
      </div>
    </div>

    <div class="p-3 pt-0">
      <div class="d-flex justify-content-between align-items-center pt-2 border-top border-light-subtle mb-3">
        <span class="small text-muted text-uppercase tracking-wider" style="font-size: 11px;">Tuition Fee</span>
        <span class="fw-bold text-primary fs-5">{{ $course['price_label'] }}</span>
      </div>
      <a href="{{ $course['details_url'] }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold shadow-sm d-flex align-items-center justify-content-center gap-1">
        <i class="bi bi-box-arrow-in-right"></i> View Details
      </a>
    </div>
  </div>
</div>
@empty
    <div class="col-12">
      <div class="alert alert-light border rounded-4 mb-0">
        No active recommendations yet. Check back after new courses are published.
      </div>
    </div>
    @endforelse
  </div>

  <!-- Upcoming Offline Sessions & Mentorship Chat (two columns) -->
  <div class="row g-4">
    <!-- Upcoming Sessions -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
        <h3 class="fw-bold mb-4"><i class="bi bi-calendar2-week text-primary me-2"></i>Your Upcoming Offline Sessions</h3>
        @forelse($upcomingSessions as $session)
        <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
          <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
            <span class="d-block fw-bold">{{ $session['day'] }}</span>
            <small class="text-muted">{{ $session['month'] }}</small>
          </div>
          <div class="flex-grow-1">
            <strong>{{ $session['title'] }}</strong><br>
            <small><i class="bi bi-geo-alt"></i> {{ $session['venue'] }} · {{ $session['time_slot'] }}</small>
          </div>
          <span class="badge {{ $session['badge_class'] }} px-3 py-2">Mentor: {{ $session['mentor'] }}</span>
        </div>
        @empty
        <div class="alert alert-light border rounded-4 mb-0">
          No confirmed sessions are scheduled yet.
        </div>
        @endforelse
        <div class="mt-4 text-end">
          <a href="{{ route('student.my.enrollments') }}" class="text-decoration-none fw-semibold"><i class="bi bi-calendar-plus"></i> Full schedule</a>
        </div>
      </div>
    </div>

    <!-- Mentorship Chat Preview -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h3 class="fw-bold mb-4"><i class="bi bi-chat-dots text-success me-2"></i>Mentorship Chat</h3>
        @forelse($recentChats as $chat)
        <div class="d-flex align-items-start mb-3">
          <div class="mentor-avatar me-3 d-flex align-items-center justify-content-center {{ $chat['avatar_class'] }} rounded-circle" style="width:48px;height:48px;">
            <i class="bi bi-person-workspace fs-5"></i>
          </div>
          <div class="flex-grow-1">
            <strong>{{ $chat['mentor_name'] }}</strong> <span class="badge {{ $chat['status_class'] }} ms-1" style="font-size:0.5rem;">{{ $chat['status_label'] }}</span>
            <p class="small text-secondary mb-0">{{ $chat['message'] }}</p>
            <small class="text-muted">{{ $chat['course_title'] }} · {{ $chat['time'] }}</small>
          </div>
          @if($chat['unread_count'] > 0)
          <span class="badge bg-danger rounded-pill">{{ $chat['unread_count'] }}</span>
          @endif
        </div>
        @empty
        <div class="alert alert-light border rounded-4 mb-0">
          No mentorship chats yet. Messages from mentors will appear here.
        </div>
        @endforelse
        <div class="mt-4">
          <a href="{{ route('student.chat.show') }}" class="btn btn-outline-success rounded-pill w-100 py-2 fw-semibold">
            <i class="bi bi-chat-dots"></i> Open Mentorship Chat
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer / extra info -->
  <div class="text-muted mt-5 mb-3 d-flex justify-content-between small">
    <span><i class="bi bi-mortarboard"></i> EduConnect · Offline learning, connected.</span>
    <span><a href="#" class="text-decoration-none">Help Center</a> · <a href="#" class="text-decoration-none">Privacy</a></span>
  </div>

</x-student.layout>