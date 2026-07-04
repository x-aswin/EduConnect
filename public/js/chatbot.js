document.addEventListener('DOMContentLoaded', function () {
    const chatToggleBtn = document.getElementById('chat-toggle-btn');
    const chatCloseBtn = document.getElementById('chat-close-btn');
    const chatbotContainer = document.getElementById('chatbot-container');
    const chatbotForm = document.getElementById('chatbot-form');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');

    // 1. Toggle Chat Window Visibility
    chatToggleBtn.addEventListener('click', () => {
        chatbotContainer.classList.toggle('d-none');
        chatInput.focus();
    });

    chatCloseBtn.addEventListener('click', () => {
        chatbotContainer.classList.add('d-none');
    });

    // 2. Handle Message Submission
    chatbotForm.addEventListener('submit', function (e) {
        e.preventDefault();
        
        const messageText = chatInput.value.trim();
        if (!messageText) return;

        // Append user's message bubble to UI
        appendMessage(messageText, 'user');
        chatInput.value = '';

        // Show a temporary "Thinking..." bubble
        const thinkingId = appendMessage('Thinking...', 'bot', true);

        // Placeholder for the upcoming backend Fetch request
        setTimeout(() => {
            removeThinkingMessage(thinkingId);
            appendMessage("I'm ready to connect to the Gemini API! Let's set up our Laravel route next.", 'bot');
        }, 1000);
    });

    // Helper to add message bubbles dynamically
    function appendMessage(text, sender, isThinking = false) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `d-flex mb-3 ${sender === 'user' ? 'justify-content-end' : ''}`;
        
        const id = 'msg-' + Date.now();
        if (isThinking) messageDiv.id = id;

        const bubbleClass = sender === 'user' 
            ? 'bg-primary text-white' 
            : 'bg-white text-dark shadow-sm';

        messageDiv.innerHTML = `
            <div class="${bubbleClass} p-3 rounded-3" style="max-width: 75%; font-size: 0.875rem;">
                <p class="mb-0">${text}</p>
            </div>
        `;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight; // Auto scroll to bottom
        return id;
    }

    function removeThinkingMessage(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }
});