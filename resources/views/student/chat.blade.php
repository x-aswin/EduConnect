{{-- resources/views/student/chat.blade.php --}}
<x-student.layout title="Mentorship - EduConnect" active="chat">
    @php
        $activeTab = $activeTab ?? 'live-chat';
    @endphp
    @push('styles')
    <style>
        /* Floating tab bar */
        .floating-tab-bar {
            position: static;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            display: flex;
            justify-content: center;
            margin: 1rem auto 2rem;
            width: min(300px, calc(100% - 2rem));
            max-width: 300px;
        }
        .floating-tab-bar .btn-tab {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            background: transparent;
            color: #64748b;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
            text-decoration: none;
        }
        .floating-tab-bar .btn-tab:hover,
        .floating-tab-bar .btn-tab:focus,
        .floating-tab-bar .btn-tab:active {
            text-decoration: none;
            color: inherit;
            outline: none;
        }
        .floating-tab-bar .btn-tab.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        }
        /* Request cards */
        .request-card {
            background: white;
            border-radius: 1.2rem;
            box-shadow: 0 6px 16px rgba(0,0,0,0.04);
            padding: 1.25rem;
            transition: 0.2s;
        }
        .request-card:hover {
            box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        }
    </style>
    @endpush

    <div class="container py-4">
        <!-- Floating Tab Bar -->
        <div class="floating-tab-bar" id="chatTabs">
            <a href="{{ route('student.chat.show', ['q' => 'requests']) }}" class="btn-tab {{ $activeTab === 'requests' ? 'active' : '' }}" id="tab-requests">
                <i class="bi bi-list-check me-1"></i> Requests
            </a>
            <a href="{{ route('student.chat.show', array_filter(['chat' => $selectedChat?->id, 'q' => 'live-chat'])) }}" class="btn-tab {{ $activeTab === 'live-chat' ? 'active' : '' }}" id="tab-live-chat">
                <i class="bi bi-chat-dots me-1"></i> Live Chat
            </a>
        </div>

        {{-- ======================== REQUESTS PANEL ======================== --}}
        <div id="panel-requests" class="tab-panel" style="display: {{ $activeTab === 'requests' ? 'block' : 'none' }};">
            <h2 class="fw-bold mb-4"><i class="bi bi-hourglass-split text-warning me-2"></i>My Mentorship Requests</h2>
            @if($requests->isEmpty())
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="bi bi-inbox fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">No Requests</h5>
                    <p class="text-secondary">You haven't requested mentorship for any course yet.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($requests as $request)
                        <div class="col-md-6">
                            <div class="request-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $request->course->title ?? 'Course' }}</h6>
                                        <small class="text-muted">
                                            Mentor: {{ $request->mentor->name ?? 'N/A' }}
                                        </small>
                                    </div>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning">Pending</span>
                                    @elseif($request->status === 'accepted')
                                        <span class="badge bg-success-subtle text-success border border-success">Accepted</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger">Declined</span>
                                    @endif
                                </div>
                                <p class="small text-muted mb-2">Requested {{ $request->created_at->diffForHumans() }}</p>

                                <div class="d-flex gap-2">
                                    @if($request->status === 'pending')
                                        <form action="{{ route('student.chat.cancel', $request) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100 py-2 fw-semibold" onclick="return confirm('Cancel this mentorship request?')">
                                                <i class="bi bi-trash me-1"></i> Cancel
                                            </button>
                                        </form>
                                    @elseif($request->status === 'accepted')
                                        <a href="{{ route('student.chat.show', ['chat' => $request, 'q' => 'live-chat']) }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">
                                            <i class="bi bi-chat-square-text-fill me-1"></i> Open Chat
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-light rounded-pill w-100 py-2 fw-semibold" disabled>
                                            Request Declined
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ======================== LIVE CHAT PANEL ======================== --}}
        <div id="panel-live-chat" class="tab-panel" style="display: {{ $activeTab === 'live-chat' ? 'block' : 'none' }};">
            @if($chats->isEmpty())
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">No Active Chats</h5>
                    <p class="text-secondary">Once a mentor accepts your request, your chat will appear here.</p>
                </div>
            @else
               <x-common.chat
                :chats="$chats"
                :selected-chat="$selectedChat"
                role="student"
                :courses="$courses"
            />
            @endif
        </div>
    </div>
</x-student.layout>