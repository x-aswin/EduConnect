<button id="chat-toggle-btn" class="btn btn-primary rounded-circle position-fixed" style="bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 1050; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
    <i class="bi bi-chat-dots-fill fs-4"></i>
</button>

<div id="chatbot-container" class="card position-fixed d-none" style="bottom: 90px; right: 20px; width: 380px; height: 500px; z-index: 1050; box-shadow: 0 5px 20px rgba(0,0,0,0.15);">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="bi bi-robot me-2"></i> EduConnect Assistant</h6>
        <button type="button" class="btn-close btn-close-white" id="chat-close-btn"></button>
    </div>

    <div class="card-body overflow-auto" id="chat-messages" style="height: 380px; background-color: #f8f9fa;">
        <div class="d-flex mb-3">
            <div class="bg-white p-3 rounded-3 shadow-sm max-width-75">
                <p class="small mb-0">Hello! I'm your EduConnect assistant. Ask me about available courses, locations, or your current profile status!</p>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white border-top">
        <form id="chatbot-form" class="d-flex gap-2">
            <input type="text" id="chat-input" class="form-control form-control-sm" placeholder="Type your question..." required autocomplete="off">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-send"></i>
            </button>
        </form>
    </div>
</div>

{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"> --}}
<script src="{{ asset('js/chatbot.js') }}" defer></script>