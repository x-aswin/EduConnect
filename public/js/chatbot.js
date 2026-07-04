document.addEventListener('DOMContentLoaded', function () {
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const chatCloseBtn = document.getElementById('chat-close-btn');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    chatToggleBtn.addEventListener('click', () => {
        chatbotContainer.classList.toggle('d-none');
        chatInput.focus();
    });

    chatCloseBtn.addEventListener('click', () => {
        chatbotContainer.classList.add('d-none');
    });

    chatbotForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const messageText = chatInput.value.trim();
        if (!messageText) return;

        appendMessage(messageText, 'user');
        chatInput.value = '';

        // Add a clean, embedded "Thinking..." status bubble inside the widget only
        const thinkingId = appendMessage('<span class="spinner-border spinner-border-sm me-2"></span>EduConnect is typing...', 'bot');

        // Fetch CSRF token for security validation
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
            || '';

        // Connect live to your newly registered backend endpoint
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
                throw new Error(payload?.reply || payload?.message || `Request failed with status ${response.status}`);
            }

            return payload;
        })
        .then(data => {
            removeMessage(thinkingId);
            if (data?.status === 'success') {
                appendMessage(data.reply, 'bot');
                return;
            }

            appendMessage(data?.reply?.text || data?.reply || "I ran into a hitch parsing that message. Let's try again.", 'bot');
        })
        .catch(error => {
            removeMessage(thinkingId);
            appendMessage(error?.message || "I am unable to reach the server right now. Please check your connectivity.", 'bot');
            console.error('Chat error:', error);
        });
    });

    function appendMessage(data, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `d-flex mb-3 ${sender === 'user' ? 'justify-content-end' : ''}`;
    
    const id = 'msg-' + Date.now() + Math.random().toString(36).substr(2, 4);
    messageDiv.id = id;

    const bubbleClass = sender === 'user' 
        ? 'bg-primary text-white' 
        : 'bg-white text-dark shadow-sm border';

    // Handle plain text vs. parsed JSON rich layouts
    let textContent = '';
    let buttonsHtml = '';

    if (typeof data === 'object' && data !== null) {
        textContent = data.text || '';
        
        // If the backend sent interactive buttons, render them cleanly
        if (data.buttons && Array.isArray(data.buttons)) {
            buttonsHtml = `<div class="d-flex flex-wrap gap-2 mt-2 pt-2 border-top">`;
            data.buttons.forEach(btn => {
                if (btn.action === 'redirect') {
                    buttonsHtml += `
                        <a href="${btn.url}" class="btn btn-outline-primary btn-sm rounded-pill py-1 px-3 style="font-size: 0.75rem;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> ${btn.label}
                        </a>`;
                } else if (btn.action === 'chat_suggest') {
                    buttonsHtml += `
                        <button type="button" class="btn btn-light border btn-sm rounded-pill py-1 px-3 chat-suggest-btn" data-text="${btn.text}" style="font-size: 0.75rem;">
                            ${btn.label}
                        </button>`;
                }
            });
            buttonsHtml += `</div>`;
        }
    } else {
        textContent = data; // Fallback for raw text strings
    }

    messageDiv.innerHTML = `
        <div class="${bubbleClass} p-3 rounded-3" style="max-width: 80%; font-size: 0.875rem; word-break: break-word;">
            <p class="mb-0">${textContent}</p>
            ${buttonsHtml}
        </div>
    `;

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Attach click events immediately to the newly generated quick-suggest buttons
    messageDiv.querySelectorAll('.chat-suggest-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const suggestText = this.getAttribute('data-text');
            chatInput.value = suggestText;
            chatbotForm.requestSubmit(); // Programmatically fires the send loop
        });
    });

    return id;
}

    function removeMessage(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }
});