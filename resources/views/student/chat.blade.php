{{-- resources/views/student/chat.blade.php --}}
<x-student.layout title="Mentorship - EduConnect" active="chat">
    @push('styles')
    <style>
        /* Floating tab bar */
        .floating-tab-bar {
            position: sticky;
            top: 80px; /* below the main navbar */
            z-index: 100;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 50px;
            padding: 0.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
            display: flex;
            justify-content: center;
            margin: 0 auto 2rem;
            max-width: 300px;
        }
        .floating-tab-bar .btn-tab {
            flex: 1;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            background: transparent;
            color: #64748b;
            padding: 0.6rem 1rem;
            transition: all 0.2s;
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
            <button class="btn-tab active" onclick="switchTab('requests')" id="tab-requests">
                <i class="bi bi-list-check me-1"></i> Requests
            </button>
            <button class="btn-tab" onclick="switchTab('live-chat')" id="tab-live-chat">
                <i class="bi bi-chat-dots me-1"></i> Live Chat
            </button>
        </div>

        {{-- ======================== REQUESTS PANEL ======================== --}}
        <div id="panel-requests" class="tab-panel">
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
                                        <a href="{{ route('student.chat.show', $request) }}" class="btn btn-primary rounded-pill w-100 py-2 fw-semibold">
                                            <i class="bi bi-chat-square-text-fill me-1"></i> Open Chat
                                        </a>
                                    @else
                                        <form action="{{ route('student.chat.re-request', $request) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-primary rounded-pill w-100 py-2 fw-semibold">
                                                <i class="bi bi-arrow-repeat me-1"></i> Re‑request
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ======================== LIVE CHAT PANEL ======================== --}}
        <div id="panel-live-chat" class="tab-panel" style="display: none;">
            @if($Chats->isEmpty())
                <div class="alert alert-light border rounded-4 text-center py-5">
                    <i class="bi bi-chat-dots fs-1 text-primary"></i>
                    <h5 class="fw-bold mt-2">No Active Chats</h5>
                    <p class="text-secondary">Once a mentor accepts your request, your chat will appear here.</p>
                </div>
            @else
               <x-common.chat
                :chats="$chats"
                :selected-chat="$selectedChat"
                role="mentor"
                :courses="$courses"
            />
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tab) {
            // Hide all panels
            document.querySelectorAll('.tab-panel').forEach(el => el.style.display = 'none');
            // Show selected
            document.getElementById('panel-' + tab).style.display = 'block';
            // Update active button
            document.querySelectorAll('#chatTabs .btn-tab').forEach(btn => btn.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }
    </script>
    @endpush
</x-student.layout>