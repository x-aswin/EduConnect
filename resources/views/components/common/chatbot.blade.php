{{-- Toggle Button with Pulse Animation --}}
<button id="chat-toggle-btn" aria-label="Open AI Assistant" title="EduConnect AI">
    <svg id="chat-icon-open" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        <circle cx="9" cy="10" r="1" fill="currentColor"/>
        <circle cx="12" cy="10" r="1" fill="currentColor"/>
        <circle cx="15" cy="10" r="1" fill="currentColor"/>
    </svg>
    <svg id="chat-icon-close" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
        <line x1="18" y1="6" x2="6" y2="18"/>
        <line x1="6" y1="6" x2="18" y2="18"/>
    </svg>
    <span id="chat-pulse" class="chat-pulse-ring"></span>
</button>

{{-- Chat Container --}}
<div id="chatbot-container" class="chatbot-hidden">
    {{-- Header --}}
    <div class="chatbot-header">
        <div class="chatbot-header-left">
            <div class="chatbot-avatar">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4z"/>
                    <path d="M6 10v1a6 6 0 0 0 12 0v-1"/>
                    <path d="M12 18v4"/>
                    <path d="M8 22h8"/>
                </svg>
            </div>
            <div>
                <h6 class="chatbot-title">EduConnect AI</h6>
                <span class="chatbot-subtitle">
                    <span class="status-dot"></span>
                    Always online
                </span>
            </div>
        </div>
        <div class="chatbot-header-actions">
            <button type="button" id="chat-clear-btn" class="chatbot-header-btn" title="Clear chat">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </button>
            <button type="button" id="chat-close-btn" class="chatbot-header-btn" title="Close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Messages Area --}}
    <div class="chatbot-messages" id="chat-messages">
        {{-- Welcome message --}}
        <div class="msg-row msg-bot">
            <div class="msg-bubble msg-bubble-bot">
                <p>👋 Hi there! I'm <strong>EduConnect AI</strong>. I can help you with:</p>
                <ul>
                    <li>🔍 Finding courses & categories</li>
                    <li>🏛️ College & mentor info</li>
                    <li>📋 Your enrollments & certificates</li>
                    <li>❓ Any question about the platform</li>
                </ul>
                <p>Try asking me something below! 👇</p>
            </div>
        </div>
    </div>

    {{-- Quick Suggest Chips --}}
    <div class="chatbot-chips" id="chat-chips">
        <button class="chip" data-text="Show me available courses">📚 Browse Courses</button>
        <button class="chip" data-text="What categories do you have?">📂 Categories</button>
        <button class="chip" data-text="Show my enrollments">📋 My Enrollments</button>
        <button class="chip" data-text="Tell me about EduConnect">ℹ️ About</button>
    </div>

    {{-- Input Area --}}
    <div class="chatbot-footer">
        <form id="chatbot-form" class="chatbot-input-row chat-form">
            <input type="text" id="chat-input" placeholder="Ask me anything..." required autocomplete="off" maxlength="2000">
            <button type="submit" id="chat-send-btn" title="Send message">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </form>
        <div class="chatbot-powered">Powered by Gemini AI</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════
     Scoped Styles
     ═══════════════════════════════════════════════════════════════════ --}}
<style>
    /* ── CSS Variables ─────────────────────────────────────────────── */
    :root {
        --cb-primary: #6366f1;
        --cb-primary-light: #818cf8;
        --cb-primary-dark: #4f46e5;
        --cb-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a78bfa 100%);
        --cb-glass: rgba(255, 255, 255, 0.08);
        --cb-glass-border: rgba(255, 255, 255, 0.15);
        --cb-bg: #0f0f23;
        --cb-bg-secondary: #1a1a2e;
        --cb-text: #e2e8f0;
        --cb-text-muted: #94a3b8;
        --cb-bot-bubble: rgba(255, 255, 255, 0.06);
        --cb-user-bubble: linear-gradient(135deg, #6366f1, #8b5cf6);
        --cb-radius: 16px;
        --cb-shadow: 0 25px 60px rgba(0, 0, 0, 0.5);
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
        background: var(--cb-gradient);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 32px rgba(99, 102, 241, 0.4);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    #chat-toggle-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 12px 40px rgba(99, 102, 241, 0.6);
    }
    #chat-toggle-btn:active { transform: scale(0.95); }

    .chat-pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 2px solid var(--cb-primary-light);
        animation: chatPulse 2s ease-out infinite;
        pointer-events: none;
    }
    @keyframes chatPulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.6); opacity: 0; }
    }

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
        border-radius: var(--cb-radius);
        background: var(--cb-bg);
        border: 1px solid var(--cb-glass-border);
        box-shadow: var(--cb-shadow);
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
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
        background: var(--cb-gradient);
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
    }
    .chatbot-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chatbot-avatar {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .chatbot-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: white;
        letter-spacing: 0.3px;
    }
    .chatbot-subtitle {
        font-size: 11px;
        color: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #34d399;
        display: inline-block;
        box-shadow: 0 0 6px rgba(52, 211, 153, 0.6);
    }
    .chatbot-header-actions { display: flex; gap: 6px; }
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
        background: rgba(255,255,255,0.1);
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
    .msg-bubble ul, .msg-bubble ol {
        margin: 4px 0;
        padding-left: 18px;
    }
    .msg-bubble li { margin-bottom: 3px; }
    .msg-bubble code {
        background: rgba(255,255,255,0.1);
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 12.5px;
    }
    .msg-bubble h3, .msg-bubble h4, .msg-bubble h5, .msg-bubble h6 {
        margin: 8px 0 4px 0;
        font-size: 14px;
        font-weight: 700;
    }
    .msg-bubble strong { font-weight: 700; }
    .msg-bubble-bot {
        background: var(--cb-bot-bubble);
        color: var(--cb-text);
        border: 1px solid rgba(255,255,255,0.06);
        border-bottom-left-radius: 4px;
    }
    .msg-bubble-user {
        background: var(--cb-user-bubble);
        color: white;
        border-bottom-right-radius: 4px;
    }

    /* ── Buttons inside messages ────────────────────────────────────── */
    .msg-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }
    .msg-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid rgba(99, 102, 241, 0.4);
        background: rgba(99, 102, 241, 0.1);
        color: var(--cb-primary-light);
    }
    .msg-btn:hover {
        background: rgba(99, 102, 241, 0.25);
        border-color: var(--cb-primary-light);
        transform: translateY(-1px);
        color: white;
        text-decoration: none;
    }
    .msg-btn-suggest {
        border-color: rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: var(--cb-text-muted);
    }
    .msg-btn-suggest:hover {
        background: rgba(255,255,255,0.1);
        color: var(--cb-text);
        border-color: rgba(255,255,255,0.2);
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
        background: var(--cb-text-muted);
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
        border-radius: 20px;
        font-size: 12px;
        background: var(--cb-bg-secondary);
        color: var(--cb-text-muted);
        border: 1px solid rgba(255,255,255,0.08);
        cursor: pointer;
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .chip:hover {
        background: rgba(99, 102, 241, 0.15);
        color: var(--cb-primary-light);
        border-color: rgba(99, 102, 241, 0.3);
    }

    /* ── Footer / Input ────────────────────────────────────────────── */
    .chatbot-footer {
        padding: 12px 16px;
        border-top: 1px solid rgba(255,255,255,0.06);
        background: var(--cb-bg-secondary);
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
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.1);
        background: rgba(255,255,255,0.05);
        color: var(--cb-text);
        font-size: 13.5px;
        outline: none;
        transition: border-color 0.2s;
    }
    .chatbot-input-row input::placeholder { color: var(--cb-text-muted); }
    .chatbot-input-row input:focus {
        border-color: var(--cb-primary);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
    }
    #chat-send-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: none;
        background: var(--cb-gradient);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    #chat-send-btn:hover { box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4); transform: scale(1.05); }
    #chat-send-btn:disabled { opacity: 0.4; cursor: not-allowed; transform: none; box-shadow: none; }

    .chatbot-powered {
        text-align: center;
        font-size: 10px;
        color: rgba(255,255,255,0.2);
        margin-top: 8px;
        letter-spacing: 0.5px;
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