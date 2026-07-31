
<button id="chat-toggle-btn" aria-label="Open AI Assistant" title="EduConnect AI Assistant">
    <i class="bi bi-robot fs-4"></i>
</button>

{{-- Chat Container --}}
<div id="chatbot-container" class="chatbot-hidden">
    {{-- Header --}}
    <div class="chatbot-header">
        <div class="d-flex align-items-center gap-3">
            <div class="chatbot-avatar">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div>
                <h6 class="chatbot-title">EduConnect AI</h6>
                <span class="chatbot-subtitle">
                    <span class="status-dot"></span>
                    @auth
                        {{ ucfirst(Auth::user()->role) }} Assistant
                    @else
                        EduConnect Guide
                    @endauth
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" id="chat-clear-btn" class="chatbot-header-btn" title="Clear chat">
                <i class="bi bi-trash"></i>
            </button>
            <button type="button" id="chat-close-btn" class="chatbot-header-btn" title="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div class="chatbot-messages" id="chat-messages">
        <div class="msg-row msg-bot">
            <div class="msg-bubble msg-bubble-bot">
                <p>👋 Hi there! I'm <strong>EduConnect AI</strong>.</p>
                <p>I can help you find courses, check enrollments, and answer questions about the platform.</p>
                <p class="mb-0">Try asking me something! 👇</p>
            </div>
        </div>
    </div>

    {{-- Quick Suggest Chips --}}
    <div class="chatbot-chips" id="chat-chips">
        @auth
            @if(Auth::user()->role === 'student')
                <button class="chip" data-text="What are my last enrollment details?"><i class="bi bi-journal-check me-1"></i> My Last Enrollment</button>
                <button class="chip" data-text="Show new courses provided by colleges in Angamaly"><i class="bi bi-geo-alt me-1"></i> Courses in Angamaly</button>
                <button class="chip" data-text="Check my certificate status"><i class="bi bi-award me-1"></i> My Certificates</button>
                <button class="chip" data-text="How do I live chat with my course mentor?"><i class="bi bi-chat-dots me-1"></i> Mentor Chat</button>
            @elseif(Auth::user()->role === 'firm')
                <button class="chip" data-text="Show my organization group bookings"><i class="bi bi-building me-1"></i> Group Bookings</button>
                <button class="chip" data-text="What courses in Angamaly are available for firm bookings?"><i class="bi bi-geo-alt me-1"></i> Angamaly Courses</button>
                <button class="chip" data-text="How can I manage firm groups and cohorts?"><i class="bi bi-people me-1"></i> Manage Groups</button>
                <button class="chip" data-text="Where can I download member certificates?"><i class="bi bi-award me-1"></i> Member Certificates</button>
            @elseif(Auth::user()->role === 'college')
                <button class="chip" data-text="List all of our active offline courses"><i class="bi bi-book me-1"></i> Our Active Courses</button>
                <button class="chip" data-text="Show pending student and firm enrollments awaiting review"><i class="bi bi-hourglass-split me-1"></i> Pending Enrollments</button>
                <button class="chip" data-text="How do I assign mentors to course offerings?"><i class="bi bi-person-badge me-1"></i> Course Mentors</button>
                <button class="chip" data-text="Tell me about certificate issuance with QR codes"><i class="bi bi-award me-1"></i> Issue Certificates</button>
            @elseif(Auth::user()->role === 'mentor')
                <button class="chip" data-text="List all of my assigned offline courses"><i class="bi bi-book me-1"></i> Assigned Courses</button>
                <button class="chip" data-text="Are there any pending student live chat requests?"><i class="bi bi-chat-left-quote me-1"></i> Chat Requests</button>
                <button class="chip" data-text="Show my student participation reports summary"><i class="bi bi-graph-up me-1"></i> Mentorship Summary</button>
            @elseif(Auth::user()->role === 'admin')
                <button class="chip" data-text="Show overall platform statistics"><i class="bi bi-bar-chart-fill me-1"></i> Platform Stats</button>
                <button class="chip" data-text="Show pending college approvals"><i class="bi bi-bank me-1"></i> College Approvals</button>
                <button class="chip" data-text="Show pending firm approvals"><i class="bi bi-building me-1"></i> Firm Approvals</button>
            @endif
        @else
            <button class="chip" data-text="Show active offline courses"><i class="bi bi-book me-1"></i> Browse Courses</button>
            <button class="chip" data-text="What new courses are provided by colleges in Angamaly?"><i class="bi bi-geo-alt me-1"></i> Angamaly Courses</button>
            <button class="chip" data-text="How does the dual enrollment model work?"><i class="bi bi-info-circle me-1"></i> How it Works</button>
            <button class="chip" data-text="How do I register an account to enroll?"><i class="bi bi-person-plus me-1"></i> How to Enroll</button>
        @endauth
    </div>

    {{-- Input Area --}}
    <div class="chatbot-footer">
        <form id="chatbot-form" class="chatbot-input-row chat-form">
            <input type="text" id="chat-input" placeholder="Ask me anything..." required autocomplete="off" maxlength="2000">
            <button type="submit" id="chat-send-btn" title="Send message">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
        <div class="chatbot-powered">EduConnect AI Assistant</div>
    </div>
</div>

{{-- Scoped Styles --}}
<style>
    :root {
        --cb-navy: #0f172a;
        --cb-gold: #c9a84c;
        --cb-gold-light: #f5e6c8;
        --cb-primary: #2563eb;
        --cb-primary-light: #3b82f6;
        --cb-white: #ffffff;
        --cb-slate: #64748b;
        --cb-bg: #f8fafc;
        --cb-border: #e2e8f0;
    }

    /* ── Toggle Button ─────────────────────────────────────────────── */
    #chat-toggle-btn {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 10000;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: var(--cb-primary);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #chat-toggle-btn:hover {
        transform: scale(1.08);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.5);
    }
    #chat-toggle-btn:active { transform: scale(0.95); }

    /* ── Container ─────────────────────────────────────────────────── */
    #chatbot-container {
        position: fixed;
        bottom: 96px;
        right: 24px;
        width: 400px;
        max-height: 600px;
        height: 80vh;
        z-index: 10000;
        display: flex;
        flex-direction: column;
        border-radius: 1.5rem;
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.15);
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: bottom right;
    }
    #chatbot-container.chatbot-hidden {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
        pointer-events: none;
        visibility: hidden;
    }
    #chatbot-container.chatbot-visible {
        opacity: 1;
        transform: scale(1) translateY(0);
        pointer-events: all;
        visibility: visible;
    }

    /* ── Header ────────────────────────────────────────────────────── */
    .chatbot-header {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .chatbot-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
    }
    .chatbot-title {
        margin: 0;
        font-family: 'Space Grotesk', sans-serif;
        font-size: 15px;
        font-weight: 700;
        color: white;
    }
    .chatbot-subtitle {
        font-size: 11px;
        color: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #34d399;
        display: inline-block;
        box-shadow: 0 0 6px rgba(52, 211, 153, 0.5);
    }
    .chatbot-header-btn {
        background: rgba(255,255,255,0.15);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s;
    }
    .chatbot-header-btn:hover { background: rgba(255,255,255,0.25); }

    /* ── Messages ──────────────────────────────────────────────────── */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: var(--cb-bg);
        scroll-behavior: smooth;
    }
    .chatbot-messages::-webkit-scrollbar { width: 4px; }
    .chatbot-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .msg-row {
        display: flex;
        max-width: 88%;
        animation: msgSlideIn 0.3s ease-out;
    }
    .msg-row.msg-bot { align-self: flex-start; }
    .msg-row.msg-user { align-self: flex-end; }

    @keyframes msgSlideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .msg-bubble {
        padding: 12px 16px;
        border-radius: 14px;
        font-size: 13.5px;
        line-height: 1.6;
        word-break: break-word;
    }
    .msg-bubble p { margin: 0 0 6px 0; }
    .msg-bubble p:last-child { margin-bottom: 0; }
    .msg-bubble ul, .msg-bubble ol { margin: 4px 0; padding-left: 18px; }
    .msg-bubble li { margin-bottom: 3px; }
    .msg-bubble strong { font-weight: 700; }

    .msg-bubble-bot {
        background: var(--cb-white);
        color: var(--cb-navy);
        border: 1px solid var(--cb-border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border-bottom-left-radius: 4px;
    }
    .msg-bubble-user {
        background: var(--cb-primary);
        color: white;
        border-bottom-right-radius: 4px;
    }

    /* ── Typing indicator ──────────────────────────────────────────── */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 14px 18px;
    }
    .typing-dot {
        width: 7px;
        height: 7px;
        background: var(--cb-slate);
        border-radius: 50%;
        animation: typingBounce 1.4s ease-in-out infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typingBounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-6px); opacity: 1; }
    }

    /* ── Quick Chips ───────────────────────────────────────────────── */
    .chatbot-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        padding: 0 16px 10px 16px;
        flex-shrink: 0;
        background: var(--cb-bg);
    }
    .chip {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        background: var(--cb-white);
        color: var(--cb-navy);
        border: 1px solid var(--cb-border);
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .chip:hover {
        background: #eef2ff;
        color: var(--cb-primary);
        border-color: var(--cb-primary-light);
    }

    /* ── Footer / Input ────────────────────────────────────────────── */
    .chatbot-footer {
        padding: 12px 16px;
        border-top: 1px solid var(--cb-border);
        background: var(--cb-white);
        flex-shrink: 0;
    }
    .chatbot-input-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    .chatbot-input-row input {
        flex: 1;
        padding: 10px 14px;
        border-radius: 50px;
        border: 1px solid var(--cb-border);
        background: var(--cb-bg);
        color: var(--cb-navy);
        font-size: 13.5px;
        outline: none;
        transition: border-color 0.2s;
        font-family: 'Inter', system-ui, sans-serif;
    }
    .chatbot-input-row input::placeholder { color: var(--cb-slate); }
    .chatbot-input-row input:focus {
        border-color: var(--cb-primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    #chat-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: none;
        background: var(--cb-primary);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    #chat-send-btn:hover {
        background: #1d4ed8;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
    #chat-send-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
        box-shadow: none;
    }

    .chatbot-powered {
        text-align: center;
        font-size: 10px;
        color: var(--cb-slate);
        margin-top: 8px;
        letter-spacing: 0.3px;
    }

    /* ── Action Buttons in Bot Messages ────────────────────────────── */
    .msg-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px dashed rgba(226, 232, 240, 0.8);
    }
    .msg-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        padding: 9px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        cursor: pointer;
    }
    /* Primary action link buttons (e.g. redirect links) */
    .msg-btn:not(.msg-btn-suggest) {
        background: linear-gradient(135deg, var(--cb-primary) 0%, #1d4ed8 100%);
        color: white;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
    }
    .msg-btn:not(.msg-btn-suggest):hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1.5px);
        box-shadow: 0 6px 14px rgba(37, 99, 235, 0.3);
        color: white;
    }
    /* Suggestion / query chips style buttons */
    .msg-btn-suggest {
        background: #f1f5f9;
        color: var(--cb-navy);
        border: 1px solid var(--cb-border);
    }
    .msg-btn-suggest:hover {
        background: #e2e8f0;
        color: var(--cb-primary);
        border-color: var(--cb-primary-light);
        transform: translateY(-1.5px);
    }
    .msg-btn:active {
        transform: translateY(0);
    }

    /* ── Responsive ────────────────────────────────────────────────── */
    @media (max-width: 480px) {
        #chatbot-container {
            width: calc(100vw - 16px);
            height: calc(100vh - 100px);
            max-height: none;
            right: 8px;
            bottom: 80px;
            border-radius: 14px;
        }
        #chat-toggle-btn { bottom: 16px; right: 16px; width: 54px; height: 54px; }
    }
</style>
<script src="{{ asset('js/chatbot.js') }}" defer></script>