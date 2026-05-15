{{-- resources/views/components/common/chat-layout.blade.php --}}
@props(['chats', 'selectedChat' => null, 'role', 'courses' => []])

@php
    $isMentor = ($role === 'mentor');
@endphp

@push('styles')
<style>
    .chat-shell {
        display: flex;
        height: 80vh;
        background: white;
        border-radius: 1.5rem;
        box-shadow: 0 12px 28px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .chat-sidebar {
        width: 340px;
        border-right: 1px solid rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        background: #f9fafb;
        flex-shrink: 0;
    }
    .chat-sidebar-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        background: white;
    }
    .chat-sidebar-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-contact {
        display: flex;
        align-items: center;
        padding: 0.85rem 1.25rem;
        transition: background 0.15s;
        text-decoration: none;
        color: inherit;
        border-left: 3px solid transparent;
    }
    .chat-contact:hover {
        background: #eef2ff;
    }
    .chat-contact.active-chat {
        background: #e0e7ff;
        border-left-color: #2563eb;
    }
    .chat-contact .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
    }
    .chat-contact .placeholder-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
    }
    .chat-contact .contact-info {
        flex: 1;
        margin-left: 0.75rem;
        overflow: hidden;
    }
    .chat-contact .contact-name {
        font-weight: 500;
        font-size: 0.95rem;
        line-height: 1.3;
    }
    .chat-contact .contact-course {
        font-size: 0.8rem;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .chat-contact.unread .contact-name {
        font-weight: 700;
    }
    .chat-contact.unread .contact-course {
        color: #1e293b;
        font-weight: 500;
    }
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
    }
    .chat-main-placeholder {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
    .message-area {
        flex: 1;
        overflow-y: auto;
        padding: 1.5rem;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .message-bubble {
        max-width: 75%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        word-wrap: break-word;
        position: relative;
    }
    .message-sent {
        background: #2563eb;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 0.25rem;
    }
    .message-received {
        background: white;
        color: #1e293b;
        align-self: flex-start;
        border-bottom-left-radius: 0.25rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    }
    .message-meta {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 0.25rem;
    }
    .chat-input {
        background: white;
        padding: 1rem 1.5rem;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    .filter-select {
        border-radius: 50px;
        border: none;
        background: #f1f5f9;
        font-weight: 500;
        padding: 0.5rem 1rem;
    }
    @media (max-width: 768px) {
        .chat-shell {
            flex-direction: column;
            height: auto;
        }
        .chat-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid rgba(0,0,0,0.06);
        }
    }
</style>
@endpush

<div class="container py-4">
    <div class="chat-shell">
        <!-- LEFT SIDEBAR -->
        <div class="chat-sidebar">
            <div class="chat-sidebar-header">
                @if($isMentor)
                    <form method="GET" action="{{ url()->current() }}" class="d-flex gap-2">
                        <select name="course_id" class="form-select filter-select" onchange="this.form.submit()">
                            <option value="">All Courses</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->title }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @else
                    <h6 class="fw-bold mb-0">Accepted Mentors</h6>
                @endif
            </div>
            <div class="chat-sidebar-list">
                @forelse($chats as $chat)
                    @php
                        // Determine the other participant
                        if ($role === 'student') {
                            $otherUser = $chat->mentor;
                            $otherProfile = $otherUser->mentor->photo ?? null;
                            $otherName = $otherUser->name ?? 'Mentor';
                            $courseTitle = $chat->course->title ?? 'Course';
                            $collegeName = $chat->course->college->institution_name ?? null;
                        } else {
                            $otherUser = $chat->student;
                            $otherProfile = $otherUser->student->photo ?? null;
                            $otherName = $otherUser->name ?? 'Student';
                            $courseTitle = $chat->course->title ?? 'Course';
                            $collegeName = null; // mentor doesn't see college
                        }
                        // Unread count: messages sent by other user and not read
                        $unreadCount = $chat->messages()
                                        ->where('sender_id', '!=', Auth::id())
                                        ->where('is_read', false)
                                        ->count();
                        $activeClass = (isset($selectedChat) && $selectedChat->id === $chat->id) ? 'active-chat' : '';
                        $unreadClass = $unreadCount > 0 ? 'unread' : '';
                    @endphp
                    <a href="{{ $role === 'student' ? route('student.chat.show', $chat) : route('mentor.chat.show', $chat) }}" class="chat-contact {{ $activeClass }} {{ $unreadClass }}">
                        @if($otherProfile)
                            <img src="{{ asset('storage/' . $otherProfile) }}" class="avatar" alt="{{ $otherName }}">
                        @else
                            <div class="placeholder-avatar">{{ strtoupper(substr($otherName, 0, 1)) }}</div>
                        @endif
                        <div class="contact-info">
                            <div class="contact-name">{{ $otherName }}</div>
                            <div class="contact-course">
                                {{ $courseTitle }}
                                @if($collegeName) · {{ $collegeName }} @endif
                            </div>
                        </div>
                        @if($unreadCount > 0)
                            <span class="badge bg-primary rounded-pill ms-1">{{ $unreadCount }}</span>
                        @endif
                    </a>
                @empty
                    <div class="p-3 text-muted text-center">No accepted chats yet</div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="chat-main">
            @if($selectedChat)
                @php
                    $messages = $selectedChat->messages()->orderBy('created_at')->get();
                    // Other participant details
                    if ($role === 'student') {
                        $otherName = $selectedChat->mentor->name ?? 'Mentor';
                        $otherPhoto = $selectedChat->mentor->mentor->photo ?? null;
                        $courseTitle = $selectedChat->course->title ?? 'Course';
                        $collegeName = $selectedChat->course->college->institution_name ?? null;
                    } else {
                        $otherName = $selectedChat->student->name ?? 'Student';
                        $otherPhoto = $selectedChat->student->student->photo ?? null;
                        $courseTitle = $selectedChat->course->title ?? 'Course';
                        $collegeName = null;
                    }
                @endphp
                <!-- Chat header -->
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        @if($otherPhoto)
                            <img src="{{ asset('storage/' . $otherPhoto) }}" class="rounded-circle" width="44" height="44" style="object-fit: cover;">
                        @else
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                                <i class="bi bi-person text-primary"></i>
                            </div>
                        @endif
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-0">{{ $otherName }}</h6>
                        <small class="text-muted">
                            {{ $courseTitle }}
                            @if($collegeName) · {{ $collegeName }} @endif
                        </small>
                    </div>
                    <a href="{{ $role === 'student' ? route('student.chat.requests') : route('mentor.chat.requests') }}" class="btn btn-light rounded-pill ms-auto">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>

                <!-- Messages -->
                <div class="message-area" id="chatMessages">
                    @forelse($messages as $msg)
                        <div class="message-bubble {{ $msg->sender_id === Auth::id() ? 'message-sent' : 'message-received' }}">
                            <p class="mb-0">{{ $msg->message }}</p>
                            <div class="message-meta">
                                {{ $msg->created_at->format('h:i A') }}
                                @if($msg->sender_id === Auth::id() && $msg->is_read)
                                    <i class="bi bi-check2-all ms-1"></i>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">No messages yet</div>
                    @endforelse
                </div>

                <!-- Input -->
                <div class="chat-input">
                    <form action="{{ $role === 'student' ? route('student.chat.send', $selectedChat) : route('mentor.chat.send', $selectedChat) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="message" class="form-control rounded-pill" placeholder="Type a message..." required autofocus>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            @else
                <div class="chat-main-placeholder">
                    <div class="text-center">
                        <i class="bi bi-chat-dots fs-1"></i>
                        <p class="mt-2">Select a chat to start messaging</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const msgDiv = document.getElementById('chatMessages');
        if(msgDiv) msgDiv.scrollTop = msgDiv.scrollHeight;
    });
</script>