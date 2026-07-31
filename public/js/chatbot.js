/**
 * ═══════════════════════════════════════════════════════════════════
 * EduConnect AI Chatbot — Frontend Controller
 * Handles: markdown rendering, buttons, chips, history, animations
 * ═══════════════════════════════════════════════════════════════════
 */
document.addEventListener('DOMContentLoaded', function () {
    // ── DOM References ────────────────────────────────────────────────
    const chatToggleBtn  = document.getElementById('chat-toggle-btn');
    const chatCloseBtn   = document.getElementById('chat-close-btn');
    const chatClearBtn   = document.getElementById('chat-clear-btn');
    const chatContainer  = document.getElementById('chatbot-container');
    const chatForm       = document.getElementById('chatbot-form');
    const chatInput      = document.getElementById('chat-input');
    const chatMessages   = document.getElementById('chat-messages');
    const chatChips      = document.getElementById('chat-chips');
    const chatSendBtn    = document.getElementById('chat-send-btn');
    const chatPulse      = document.getElementById('chat-pulse');
    const iconOpen       = document.getElementById('chat-icon-open');
    const iconClose      = document.getElementById('chat-icon-close');

    let isOpen = false;
    let isWaiting = false;

    // ── Toggle Open/Close ─────────────────────────────────────────────
    chatToggleBtn.addEventListener('click', () => {
        isOpen = !isOpen;
        if (isOpen) {
            chatContainer.classList.remove('chatbot-hidden');
            chatContainer.classList.add('chatbot-visible');
            if (iconOpen) iconOpen.style.display = 'none';
            if (iconClose) iconClose.style.display = 'block';
            if (chatPulse) chatPulse.style.display = 'none';
            chatInput.focus();
        } else {
            closeChat();
        }
    });

    chatCloseBtn.addEventListener('click', closeChat);

    function closeChat() {
        isOpen = false;
        chatContainer.classList.remove('chatbot-visible');
        chatContainer.classList.add('chatbot-hidden');
        if (iconOpen) iconOpen.style.display = 'block';
        if (iconClose) iconClose.style.display = 'none';
    }

    // ── Clear Chat ────────────────────────────────────────────────────
    chatClearBtn.addEventListener('click', () => {
        // Keep the welcome message (first child), remove everything else
        while (chatMessages.children.length > 1) {
            chatMessages.removeChild(chatMessages.lastChild);
        }
        // Show chips again
        chatChips.style.display = 'flex';

        // Clear server-side history
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/chatbot/clear', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        }).catch(() => {});
    });

    // ── Quick Suggest Chips ───────────────────────────────────────────
    chatChips.querySelectorAll('.chip').forEach(chip => {
        chip.addEventListener('click', function () {
            const text = this.getAttribute('data-text');
            chatInput.value = text;
            chatForm.requestSubmit();
        });
    });

    // ── Form Submit ───────────────────────────────────────────────────
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const messageText = chatInput.value.trim();
        if (!messageText || isWaiting) return;

        // Hide chips after first message
        chatChips.style.display = 'none';

        // Append user message
        appendMessage(messageText, 'user');
        chatInput.value = '';

        // Show typing indicator
        const typingId = showTypingIndicator();
        setWaiting(true);

        // Send to backend
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch('/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ message: messageText })
        })
        .then(async response => {
            const payload = await response.json().catch(() => null);
            if (!response.ok) {
                throw new Error(payload?.reply?.text || payload?.message || `Request failed (${response.status})`);
            }
            return payload;
        })
        .then(data => {
            removeElement(typingId);
            setWaiting(false);

            if (data?.status === 'success' && data?.reply) {
                appendMessage(data.reply, 'bot');
            } else {
                appendMessage({ text: data?.reply?.text || 'Something went wrong. Please try again.', buttons: [] }, 'bot');
            }
        })
        .catch(error => {
            removeElement(typingId);
            setWaiting(false);
            appendMessage({ text: error?.message || 'Unable to reach the server. Check your connection.', buttons: [] }, 'bot');
        });
    });

    // ══════════════════════════════════════════════════════════════════
    // MESSAGE RENDERING
    // ══════════════════════════════════════════════════════════════════

    function appendMessage(data, sender) {
        const row = document.createElement('div');
        const id = 'msg-' + Date.now() + Math.random().toString(36).substr(2, 4);
        row.id = id;
        row.className = `msg-row msg-${sender}`;

        let htmlContent = '';
        let buttonsHtml = '';

        if (sender === 'user') {
            // Plain text — escape HTML for XSS safety
            htmlContent = escapeHtml(typeof data === 'string' ? data : data.text || '');
        } else {
            // Bot message — parse markdown
            const text = typeof data === 'object' ? (data.text || '') : data;
            htmlContent = renderMarkdown(text);

            // Render buttons if present
            const buttons = typeof data === 'object' ? (data.buttons || []) : [];
            if (buttons.length > 0) {
                buttonsHtml = '<div class="msg-buttons">';
                buttons.forEach(btn => {
                    if (btn.action === 'redirect' && btn.url) {
                        buttonsHtml += `<a href="${escapeHtml(btn.url)}" class="msg-btn">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            ${escapeHtml(btn.label)}
                        </a>`;
                    } else if (btn.action === 'chat_suggest' && btn.text) {
                        buttonsHtml += `<button type="button" class="msg-btn msg-btn-suggest chat-suggest-btn" data-text="${escapeHtml(btn.text)}">
                            💬 ${escapeHtml(btn.label)}
                        </button>`;
                    }
                });
                buttonsHtml += '</div>';
            }
        }

        const bubbleClass = sender === 'user' ? 'msg-bubble-user' : 'msg-bubble-bot';
        row.innerHTML = `<div class="msg-bubble ${bubbleClass}">${htmlContent}${buttonsHtml}</div>`;

        chatMessages.appendChild(row);
        scrollToBottom();

        // Bind chat_suggest button events
        row.querySelectorAll('.chat-suggest-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                chatInput.value = this.getAttribute('data-text');
                chatForm.requestSubmit();
            });
        });

        return id;
    }

    // ── Typing Indicator ──────────────────────────────────────────────
    function showTypingIndicator() {
        const row = document.createElement('div');
        const id = 'typing-' + Date.now();
        row.id = id;
        row.className = 'msg-row msg-bot';
        row.innerHTML = `
            <div class="msg-bubble msg-bubble-bot typing-indicator">
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
                <span class="typing-dot"></span>
            </div>
        `;
        chatMessages.appendChild(row);
        scrollToBottom();
        return id;
    }

    // ══════════════════════════════════════════════════════════════════
    // LIGHTWEIGHT MARKDOWN RENDERER
    // ══════════════════════════════════════════════════════════════════

    function renderMarkdown(text) {
        if (!text) return '';

        // Escape HTML first to prevent XSS, then apply markdown
        let html = escapeHtml(text);

        // Headings: ### Heading → <h6>
        html = html.replace(/^#{1,4}\s+(.+)$/gm, '<h6>$1</h6>');

        // Bold: **text** or __text__
        html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        html = html.replace(/__(.+?)__/g, '<strong>$1</strong>');

        // Italic: *text* or _text_
        html = html.replace(/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/g, '<em>$1</em>');
        html = html.replace(/(?<!_)_(?!_)(.+?)(?<!_)_(?!_)/g, '<em>$1</em>');

        // Inline code: `code`
        html = html.replace(/`([^`]+)`/g, '<code>$1</code>');

        // Unordered lists: lines starting with - or *
        html = html.replace(/^(?:\s*)[-*]\s+(.+)$/gm, '<li>$1</li>');
        html = html.replace(/((?:<li>.*<\/li>\s*)+)/g, '<ul>$1</ul>');

        // Ordered lists: lines starting with 1. 2. etc.
        html = html.replace(/^\d+\.\s+(.+)$/gm, '<li>$1</li>');
        // Wrap consecutive <li> not already wrapped
        html = html.replace(/(?<!<\/ul>)((?:<li>.*<\/li>\s*)+)(?!<\/ul>)/g, function(match) {
            // Only wrap if not already inside <ul>
            if (match.indexOf('<ul>') === -1) {
                return '<ol>' + match + '</ol>';
            }
            return match;
        });

        // Line breaks: double newline → paragraph break, single newline → <br>
        html = html.replace(/\n\n/g, '</p><p>');
        html = html.replace(/\n/g, '<br>');

        // Wrap in paragraph tags if not already wrapped in block elements
        if (!html.startsWith('<h') && !html.startsWith('<ul') && !html.startsWith('<ol') && !html.startsWith('<p')) {
            html = '<p>' + html + '</p>';
        }

        // Clean up empty paragraphs
        html = html.replace(/<p>\s*<\/p>/g, '');
        html = html.replace(/<p>\s*(<(?:ul|ol|h\d))/g, '$1');
        html = html.replace(/(<\/(?:ul|ol|h\d)>)\s*<\/p>/g, '$1');

        return html;
    }

    // ══════════════════════════════════════════════════════════════════
    // UTILITIES
    // ══════════════════════════════════════════════════════════════════

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function removeElement(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        requestAnimationFrame(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    }

    function setWaiting(state) {
        isWaiting = state;
        chatSendBtn.disabled = state;
        chatInput.disabled = state;
        if (!state) chatInput.focus();
    }
});