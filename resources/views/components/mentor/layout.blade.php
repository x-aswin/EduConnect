@props(['active' => null, 'title' => 'EduConnect'])
<x-base.layout :title="$title">
    @push('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #eef1fa 100%);
            padding-top: 80px;
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
            font-size: 1.6rem;
            background: linear-gradient(135deg, #1e3ce0, #4f6ef6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar-nav .nav-link {
            font-weight: 600;
            color: #1e293b;
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            transition: all 0.25s;
            margin: 0 0.1rem;
        }
        .navbar-nav .nav-link:hover {
            background: #eef2ff;
            color: #1d4ed8;
            transform: translateY(-1px);
        }
        .navbar-nav .nav-link.active {
            background: #1d4ed8;
            color: white !important;
            box-shadow: 0 4px 14px rgba(29, 78, 216, 0.3);
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-radius: 20px;
            padding: 0.5rem;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(12px);
        }
        .dropdown-item {
            border-radius: 12px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
            transition: 0.15s;
        }
        .dropdown-item:hover { background: #f1f5f9; }
        .avatar-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.2rem;
            overflow: hidden;
        }
        .avatar-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        @media (max-width: 768px) {
            .navbar-collapse {
                background: white;
                border-radius: 24px;
                padding: 1rem;
                margin-top: 10px;
            }
        }
    </style>
    @endpush

    <nav class="navbar navbar-expand-lg fixed-top py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('mentor.dashboard') }}">
                <i class="bi bi-mortarboard-fill fs-2 me-2" style="color: #2563eb;"></i>
                <span>EduConnect</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mentorNavbar"
                    style="background: #f1f5f9; border-radius: 12px; padding: 8px 12px;">
                <i class="bi bi-list fs-4"></i>
            </button>

            <div class="collapse navbar-collapse" id="mentorNavbar">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'dashboard' ? 'active' : '' }}" href="{{ route('mentor.dashboard') }}">
                            <i class="bi bi-speedometer2 me-1 d-inline-block d-lg-none d-xl-inline"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'courses' ? 'active' : '' }}" href="{{ route('mentor.mycourses') }}">
                            <i class="bi bi-journal-bookmark-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> My Courses
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'requests' ? 'active' : '' }}" href="{{ route('mentor.chat.requests') }}">
                            <i class="bi bi-chat-dots-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Mentorship Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $active === 'chats' ? 'active' : '' }}" href="{{ route('mentor.chat.show') }}">
                            <i class="bi bi-chat-square-text-fill me-1 d-inline-block d-lg-none d-xl-inline"></i> Live Chats
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center ms-lg-3">
                    @php
                        $currentUser = auth()->user();
                        $lastSeenAt = session('notifications.last_seen_at');
                        $lastSeen = $lastSeenAt ? \Illuminate\Support\Carbon::parse($lastSeenAt) : $currentUser?->created_at;

                        $newRequests = $currentUser?->chatsAsMentor()
                            ->where('status', 'pending')
                            ->where('updated_at', '>', $lastSeen)
                            ->count() ?? 0;

                        $newMessages = $currentUser
                            ? \App\Models\Message::query()
                                ->whereHas('chat', function ($query) use ($currentUser) {
                                    $query->where('mentor_id', $currentUser->id);
                                })
                                ->where('sender_id', '!=', $currentUser->id)
                                ->where('is_read', false)
                                ->where('created_at', '>', $lastSeen)
                                ->count()
                            : 0;

                        $notificationCount = $newRequests + $newMessages;
                        $notificationSummary = $notificationCount > 0
                            ? 'You have ' . $notificationCount . ' new update' . ($notificationCount === 1 ? '' : 's') . ' since your last visit.'
                            : 'No new updates since your last visit.';
                    @endphp

                    <div class="dropdown me-3 d-none d-md-block">
                        <button type="button" class="btn p-0 border-0 text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow: none; background: transparent;">
                            <i class="bi bi-bell fs-5"></i>
                            @if($notificationCount > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                    {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                                </span>
                            @endif
                        </button>

                        <div class="dropdown-menu dropdown-menu-end p-3" style="min-width: 320px;">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <div class="fw-semibold">Notifications</div>
                                    <div class="small text-secondary">{{ $notificationSummary }}</div>
                                </div>
                                <a href="{{ route('notifications.mark-seen') }}" class="btn btn-sm btn-soft-primary">Mark as seen</a>
                            </div>

                            @if($notificationCount > 0)
                                <hr class="my-3">
                                <ul class="list-unstyled mb-0">
                                    @if($newRequests > 0)
                                        <li class="d-flex align-items-start gap-2 py-1">
                                            <i class="bi bi-chat-dots-fill text-primary mt-1"></i>
                                            <span>{{ $newRequests }} mentorship request{{ $newRequests === 1 ? '' : 's' }}</span>
                                        </li>
                                    @endif
                                    @if($newMessages > 0)
                                        <li class="d-flex align-items-start gap-2 py-1">
                                            <i class="bi bi-chat-square-text-fill text-primary mt-1"></i>
                                            <span>{{ $newMessages }} unread chat message{{ $newMessages === 1 ? '' : 's' }}</span>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </div>

                    <!-- Mentor profile dropdown -->
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="avatar-circle me-2 d-none d-md-flex">
                                @if(auth()->user()->mentor?->photo)
                                    <img src="{{ asset('storage/' . auth()->user()->mentor->photo) }}" alt="Mentor Photo">
                                @else
                                    <span>{{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="d-none d-lg-block text-start lh-sm">
                                <span class="d-block fw-semibold" style="font-size: 0.95rem;">{{ auth()->user()->name ?? 'Mentor' }}</span>
                                <span class="d-block small text-secondary" style="font-size: 0.75rem;">
                                    {{ auth()->user()->mentor?->qualification ?? 'Mentor' }}
                                    @if(auth()->user()->mentor?->college)
                                        · {{ auth()->user()->mentor->college->institution_name }}
                                    @endif
                                </span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('mentor.reports.index') }}"><i class="bi bi-bar-chart-line"></i> Reports</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <x-toast />

    <main class="container py-2">
        {{ $slot }}
    </main>

    @push('scripts')
    <script>
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                document.querySelectorAll('.navbar-nav .nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    @endpush
</x-base.layout>