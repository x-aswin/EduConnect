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
      <h1 class="fw-bold mt-2 mb-1">Hello, Aswin 👋</h1>
      <p class="text-secondary mb-0">Continue your offline learning journey. New courses waiting for you.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="#" class="btn btn-primary rounded-pill px-4 py-2 shadow-sm">
        <i class="bi bi-search me-1"></i> Explore Courses
      </a>
      <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2 ms-2">
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
          <h4 class="fw-bold mb-0">3</h4>
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
          <h4 class="fw-bold mb-0">4</h4>
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
          <h4 class="fw-bold mb-0">2</h4>
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
          <h4 class="fw-bold mb-0">3</h4>
          <small>From mentors</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Recommended Courses Section -->
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="section-title"><i class="bi bi-lightning-charge-fill text-warning me-2"></i>Recommended for You</h2>
    <a href="#" class="text-decoration-none fw-semibold">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div class="row g-4 mb-5">
    <!-- Course Card 1 -->
    <div class="col-md-4">
      <div class="course-card p-0">
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
          <i class="bi bi-code-slash fs-1 text-primary"></i>
        </div>
        <div class="p-3">
          <span class="badge bg-success bg-opacity-10 text-success mb-2">Student Only</span>
          <h5 class="fw-bold mt-1">Full Stack Web Dev</h5>
          <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> CS Lab 204 · <i class="bi bi-calendar3 ms-2"></i> Starts Apr 20</p>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="fw-bold text-primary">Free</span>
            <span class="small"><i class="bi bi-person"></i> 12/30 seats</span>
          </div>
          <div class="progress mt-2">
            <div class="progress-bar bg-primary" style="width: 40%"></div>
          </div>
          <button class="btn btn-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
            <i class="bi bi-box-arrow-in-right"></i> Enroll Now
          </button>
        </div>
      </div>
    </div>
    <!-- Course Card 2 -->
    <div class="col-md-4">
      <div class="course-card p-0">
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
          <i class="bi bi-shield-shaded fs-1 text-success"></i>
        </div>
        <div class="p-3">
          <span class="badge bg-warning bg-opacity-10 text-warning mb-2">Firm Available</span>
          <h5 class="fw-bold mt-1">Ethical Hacking</h5>
          <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> Seminar Hall · <i class="bi bi-calendar3 ms-2"></i> Apr 22</p>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="fw-bold text-primary">₹1,200</span>
            <span class="small"><i class="bi bi-person"></i> 18/25 seats</span>
          </div>
          <div class="progress mt-2">
            <div class="progress-bar bg-warning" style="width: 72%"></div>
          </div>
          <button class="btn btn-outline-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
            <i class="bi bi-info-circle"></i> Details & Enroll
          </button>
        </div>
      </div>
    </div>
    <!-- Course Card 3 -->
    <div class="col-md-4">
      <div class="course-card p-0">
        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px; background: linear-gradient(135deg, #fce7f3, #fbcfe8);">
          <i class="bi bi-graph-up-arrow fs-1 text-danger"></i>
        </div>
        <div class="p-3">
          <span class="badge bg-primary bg-opacity-10 text-primary mb-2">Student Only</span>
          <h5 class="fw-bold mt-1">Data Analytics</h5>
          <p class="small text-secondary"><i class="bi bi-geo-alt me-1"></i> IT Lab · <i class="bi bi-calendar3 ms-2"></i> Apr 25</p>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <span class="fw-bold text-primary">₹950</span>
            <span class="small"><i class="bi bi-person"></i> 28/30 seats</span>
          </div>
          <div class="progress mt-2">
            <div class="progress-bar bg-danger" style="width: 93%"></div>
          </div>
          <button class="btn btn-soft-primary rounded-pill w-100 mt-3 py-2 fw-semibold">
            <i class="bi bi-lightning"></i> Last seats – Join
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Upcoming Offline Sessions & Mentorship Chat (two columns) -->
  <div class="row g-4">
    <!-- Upcoming Sessions -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
        <h3 class="fw-bold mb-4"><i class="bi bi-calendar2-week text-primary me-2"></i>Your Upcoming Offline Sessions</h3>
        <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
          <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
            <span class="d-block fw-bold">20</span>
            <small class="text-muted">APR</small>
          </div>
          <div class="flex-grow-1">
            <strong>Full Stack Web Dev</strong><br>
            <small><i class="bi bi-geo-alt"></i> CS Lab 204 · 10:00 – 12:30</small>
          </div>
          <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">Mentor: Dr. Meera</span>
        </div>
        <div class="d-flex align-items-center bg-light rounded-4 p-3 mb-3">
          <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
            <span class="d-block fw-bold">22</span>
            <small class="text-muted">APR</small>
          </div>
          <div class="flex-grow-1">
            <strong>Ethical Hacking</strong><br>
            <small><i class="bi bi-geo-alt"></i> Seminar Hall · 14:00 – 16:00</small>
          </div>
          <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Mentor: Prof. R. Menon</span>
        </div>
        <div class="d-flex align-items-center bg-light rounded-4 p-3">
          <div class="text-center me-3 bg-white rounded-3 px-3 py-2 shadow-sm">
            <span class="d-block fw-bold">25</span>
            <small class="text-muted">APR</small>
          </div>
          <div class="flex-grow-1">
            <strong>Data Analytics</strong><br>
            <small><i class="bi bi-geo-alt"></i> IT Lab 1 · 09:30 – 12:00</small>
          </div>
          <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">Mentor: K. Varma</span>
        </div>
        <div class="mt-4 text-end">
          <a href="#" class="text-decoration-none fw-semibold"><i class="bi bi-calendar-plus"></i> Full schedule</a>
        </div>
      </div>
    </div>

    <!-- Mentorship Chat Preview -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100">
        <h3 class="fw-bold mb-4"><i class="bi bi-chat-dots text-success me-2"></i>Mentorship Chat</h3>
        <div class="d-flex align-items-start mb-3">
          <div class="mentor-avatar me-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width:48px;height:48px;">
            <i class="bi bi-person-workspace fs-5 text-primary"></i>
          </div>
          <div class="flex-grow-1">
            <strong>Dr. Meera Nair</strong> <span class="badge bg-success ms-1" style="font-size:0.5rem;">online</span>
            <p class="small text-secondary mb-0">Please review the JavaScript assignment before tomorrow's session.</p>
            <small class="text-muted">10 min ago</small>
          </div>
          <span class="badge bg-danger rounded-pill">2</span>
        </div>
        <div class="d-flex align-items-start mb-3">
          <div class="mentor-avatar me-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10 rounded-circle" style="width:48px;height:48px;">
            <i class="bi bi-person-badge fs-5 text-warning"></i>
          </div>
          <div class="flex-grow-1">
            <strong>Prof. R. Menon</strong>
            <p class="small text-secondary mb-0">Ethical Hacking lab access card ready – collect from office.</p>
            <small class="text-muted">1 hour ago</small>
          </div>
        </div>
        <div class="d-flex align-items-start">
          <div class="mentor-avatar me-3 d-flex align-items-center justify-content-center bg-info bg-opacity-10 rounded-circle" style="width:48px;height:48px;">
            <i class="bi bi-person-video3 fs-5 text-info"></i>
          </div>
          <div class="flex-grow-1">
            <strong>Asst. Prof. K. Varma</strong>
            <p class="small text-secondary mb-0">Reminder: Python quiz on Monday – offline, IT Lab.</p>
            <small class="text-muted">Yesterday</small>
          </div>
        </div>
        <div class="mt-4">
          <a href="#" class="btn btn-outline-success rounded-pill w-100 py-2 fw-semibold">
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