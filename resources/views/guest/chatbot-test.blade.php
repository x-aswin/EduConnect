<x-guest.layout title="Chatbot Test - EduConnect" :hideButtons="true">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 p-lg-5 bg-white">
                    <div class="text-center mb-4">
                        <i class="bi bi-robot fs-1 text-primary mb-3"></i>
                        <h4 class="fw-bold mb-1">Chatbot Test Playground</h4>
                        <p class="text-muted small">Try asking about courses, locations, and dates</p>
                    </div>

                    {{-- Quick suggestion chips --}}
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <button onclick="quickAsk('linux courses near angamaly')" class="btn btn-sm btn-outline-primary rounded-pill">🐧 Linux near Angamaly</button>
                        <button onclick="quickAsk('python courses this month')" class="btn btn-sm btn-outline-primary rounded-pill">🐍 Python this month</button>
                        <button onclick="quickAsk('free courses')" class="btn btn-sm btn-outline-primary rounded-pill">💰 Free courses</button>
                        <button onclick="quickAsk('upcoming courses')" class="btn btn-sm btn-outline-primary rounded-pill">📅 Upcoming</button>
                        <button onclick="quickAsk('firm courses in kochi')" class="btn btn-sm btn-outline-primary rounded-pill">🏢 Firm in Kochi</button>
                        <button onclick="quickAsk('help')" class="btn btn-sm btn-outline-primary rounded-pill">❓ Help</button>
                    </div>

                    {{-- Chat area --}}
                    <div class="bg-light rounded-4 p-3 mb-3" id="chatArea" style="height: 350px; overflow-y: auto;">
                        <div class="text-center text-muted py-5" id="chatPlaceholder">
                            <i class="bi bi-chat-dots fs-1 d-block mb-2"></i>
                            Click a suggestion above or type below
                        </div>
                    </div>

                    {{-- Input --}}
                    <form id="chatForm" class="d-flex gap-2" onsubmit="return false;">
                        <input type="text" id="chatInput" class="form-control rounded-pill" 
                               placeholder="e.g., linux courses near angamaly" autocomplete="off">
                        <button type="button" onclick="sendMessage()" class="btn btn-primary rounded-circle" 
                                style="width:44px;height:44px;padding:0;">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const chatArea = document.getElementById('chatArea');
        const chatInput = document.getElementById('chatInput');
        const chatPlaceholder = document.getElementById('chatPlaceholder');

        // Hide placeholder on first message
        function hidePlaceholder() {
            if (chatPlaceholder) chatPlaceholder.style.display = 'none';
        }

        // Quick suggestion click
        function quickAsk(text) {
            chatInput.value = text;
            sendMessage();
        }

        // Add a message bubble
        function addBubble(sender, text) {
            hidePlaceholder();
            const div = document.createElement('div');
            div.className = `mb-3 ${sender === 'user' ? 'text-end' : ''}`;
            div.innerHTML = `
                <div class="d-inline-block p-2 px-3 rounded-4 ${sender === 'user' ? 'bg-primary text-white' : 'bg-white border'} small"
                     style="max-width:85%; white-space:pre-wrap;">
                    ${text}
                </div>
            `;
            chatArea.appendChild(div);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        // Add typing indicator
        function showTyping() {
            hidePlaceholder();
            const div = document.createElement('div');
            div.className = 'mb-3';
            div.id = 'typingIndicator';
            div.innerHTML = '<small class="text-muted"><i>Typing...</i></small>';
            chatArea.appendChild(div);
            chatArea.scrollTop = chatArea.scrollHeight;
        }

        function hideTyping() {
            const el = document.getElementById('typingIndicator');
            if (el) el.remove();
        }

        // Send message
        async function sendMessage() {
            const text = chatInput.value.trim();
            if (!text) return;

            addBubble('user', text);
            chatInput.value = '';
            showTyping();

            try {
                const res = await fetch('{{ route("chatbot.message") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({ message: text })
                });
                const data = await res.json();
                hideTyping();

                let reply = data.message;
                if (data.type === 'courses' && data.courses) {
                    reply += '\n\n' + data.courses.map(c => 
                        `📘 <strong>${c.title}</strong><br>   <small>${c.college} | ${c.venue} | ${c.price}</small>`
                    ).join('<br><br>');
                }

                addBubble('bot', reply);
            } catch (err) {
                hideTyping();
                addBubble('bot', 'Sorry, something went wrong.');
            }
        }

        // Enter key to send
        document.getElementById('chatInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                sendMessage();
            }
        });
    </script>
    @endpush
</x-guest.layout>