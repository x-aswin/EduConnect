<x-college.layout active="dashboard">
  <!-- Page header with greeting & quick stats -->
  <div class="page-header flex-wrap">
    <div>
      <h1 class="h3 fw-bold mb-1">Welcome back, Cochin University College 👋</h1>
      <p class="welcome-tag mb-0">
        <i class="bi bi-calendar-week me-1"></i> 
        Manage your offline courses, mentors, and enrollments — all in one place.
      </p>
    </div>
    <div class="mt-3 mt-sm-0">
      <a href="#" class="btn btn-outline-primary rounded-pill px-4 me-2">
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
          <h3 class="mb-0 fw-bold">12</h3>
          <small class="text-success"><i class="bi bi-arrow-up"></i> 2 new this month</small>
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
          <h3 class="mb-0 fw-bold">8</h3>
          <small class="text-muted">+1 pending approval</small>
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
          <h3 class="mb-0 fw-bold">143</h3>
          <small>18 pending review</small>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card-placeholder p-3 d-flex align-items-center">
        <div class="rounded-3 bg-info bg-opacity-10 p-3 me-3">
          <i class="bi bi-cash-stack fs-3 text-info"></i>
        </div>
        <div>
          <span class="text-secondary text-uppercase small fw-semibold">Revenue (MTD)</span>
          <h3 class="mb-0 fw-bold">₹ 2.4L</h3>
          <small>↑ 12% vs last month</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Placeholder: College dashboard content (example of what appears below navbar) -->
  <div class="card-placeholder">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <h5 class="fw-semibold mb-0"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i>Your Offline Courses</h5>
      <a href="#" class="text-decoration-none small fw-semibold">View all <i class="bi bi-chevron-right"></i></a>
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
          <tr>
            <td><i class="bi bi-code-slash me-2"></i>Full Stack Web Dev (Offline)</td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">Student</span></td>
            <td>Dr. Meera Nair</td>
            <td>18/30</td>
            <td>CS Lab 204</td>
            <td><span class="badge bg-success">Active</span></td>
            <td><a href="#" class="btn-sm btn-outline-secondary border-0"><i class="bi bi-three-dots"></i></a></td>
          </tr>
          <tr>
            <td><i class="bi bi-shield-shaded me-2"></i>Ethical Hacking (Corporate Batch)</td>
            <td><span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Firm</span></td>
            <td>Prof. R. Menon</td>
            <td>12/20</td>
            <td>Seminar Hall</td>
            <td><span class="badge bg-success">Active</span></td>
            <td><a href="#" class="btn-sm btn-outline-secondary border-0"><i class="bi bi-three-dots"></i></a></td>
          </tr>
          <tr>
            <td><i class="bi bi-bar-chart me-2"></i>Data Analytics with Python</td>
            <td><span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">Student</span></td>
            <td>Asst. Prof. K. Varma</td>
            <td>28/30</td>
            <td>IT Lab 1</td>
            <td><span class="badge bg-warning text-dark">Waitlist</span></td>
            <td><a href="#" class="btn-sm btn-outline-secondary border-0"><i class="bi bi-three-dots"></i></a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="mt-3 border-top pt-3 d-flex justify-content-between">
      <div><i class="bi bi-chat-dots me-1"></i> <strong>Mentorship chat</strong> — 4 active conversations</div>
      <a href="#" class="btn btn-sm btn-outline-primary rounded-pill px-4">Manage Mentors</a>
    </div>
  </div>

  <!-- Second row: enrollment requests & upcoming schedule (college specific) -->
  <div class="row g-4">
    <div class="col-md-7">
      <div class="card-placeholder h-100">
        <h5 class="fw-semibold mb-3"><i class="bi bi-inbox-fill me-2 text-primary"></i>Pending Enrollment Requests</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
            <div>
              <span class="fw-semibold">Rahul S. (Student)</span> <br>
              <small class="text-secondary">Course: Full Stack Web Dev · Applied 2h ago</small>
            </div>
            <div>
              <button class="btn btn-sm btn-success rounded-pill px-3 me-1"><i class="bi bi-check-lg"></i> Approve</button>
              <button class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="bi bi-x-lg"></i> Reject</button>
            </div>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
            <div>
              <span class="fw-semibold">TechVantage Solutions (Firm)</span> <br>
              <small class="text-secondary">Bulk enrollment · 8 participants · Ethical Hacking</small>
            </div>
            <div>
              <span class="badge bg-info text-dark px-3 py-2 me-2">Venue: Firm Office</span>
              <button class="btn btn-sm btn-outline-primary rounded-pill">Review</button>
            </div>
          </li>
          <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
            <div>
              <span class="fw-semibold">Anjali Menon (Student)</span> <br>
              <small class="text-secondary">Data Analytics · waitlist clearance</small>
            </div>
            <div>
              <button class="btn btn-sm btn-success rounded-pill px-3 me-1">Approve</button>
              <button class="btn btn-sm btn-outline-danger rounded-pill px-3">Reject</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
    <div class="col-md-5">
      <div class="card-placeholder h-100">
        <h5 class="fw-semibold mb-3"><i class="bi bi-calendar2-week me-2 text-primary"></i>Upcoming Offline Sessions</h5>
        <div class="d-flex mb-3 border-bottom pb-3">
          <div class="me-3 text-center bg-light rounded-3 px-3 py-2">
            <span class="d-block fw-bold">15</span>
            <small>APR</small>
          </div>
          <div>
            <strong>Full Stack · Lab 204</strong><br>
            <small>10:00 – 12:30 · Mentor: Dr. Meera</small>
          </div>
        </div>
        <div class="d-flex mb-3 border-bottom pb-3">
          <div class="me-3 text-center bg-light rounded-3 px-3 py-2">
            <span class="d-block fw-bold">17</span>
            <small>APR</small>
          </div>
          <div>
            <strong>Ethical Hacking (Firm batch)</strong><br>
            <small>14:00 – 16:00 · Venue: Seminar Hall</small>
          </div>
        </div>
        <div class="d-flex">
          <div class="me-3 text-center bg-light rounded-3 px-3 py-2">
            <span class="d-block fw-bold">19</span>
            <small>APR</small>
          </div>
          <div>
            <strong>Data Analytics (Python)</strong><br>
            <small>09:30 – 12:00 · IT Lab 1</small>
          </div>
        </div>
        <div class="mt-4">
          <a href="#" class="text-decoration-none fw-semibold"><i class="bi bi-calendar-plus"></i> Update schedule / venue</a>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Additional note: master page footer / contextual hint -->
  <div class="text-muted mt-4 small d-flex justify-content-between">
    <span><i class="bi bi-layout-three-columns"></i> College master layout · EduConnect v1.0</span>
    <span><i class="bi bi-chat"></i> Live mentorship active · <a href="#">Open chat</a></span>
  </div>
</x-college.layout>
