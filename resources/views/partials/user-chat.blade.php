@php
    $isAdmin = Auth::check() && (Auth::user()->is_admin || Auth::user()->role === 'admin' || Auth::user()->email === 'admin@gmail.com' || Auth::user()->email === 'admin@example.com');
@endphp

@if(!$isAdmin)
<!-- ========================================================================= -->
<!-- DUAL-MODE CUSTOMER SUPPORT: GEMINI AI & ADMIN LIVECHAT WIDGET             -->
<!-- ========================================================================= -->
<div id="customer-chat-widget" class="fixed bottom-6 right-6 z-50 flex flex-col items-end font-sans">
    
    <!-- Floating FAB Button -->
    <button id="chat-toggle" type="button" onclick="openCustomerChat()"
            class="flex items-center gap-2.5 px-5 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-extrabold text-xs rounded-full shadow-2xl shadow-blue-500/40 hover:scale-105 transition-all duration-300 border border-white/20 cursor-pointer group">
        <span class="text-base group-hover:scale-110 transition-transform">💬</span>
        <span class="tracking-wide">Hỗ Trợ Trực Tuyến</span>
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
    </button>

    <!-- Chat Popup Modal -->
    <div id="chat-popup" class="card shadow-2xl bg-white dark:bg-[#0c1322] border border-slate-200/90 dark:border-slate-800 rounded-3xl overflow-hidden w-[360px] sm:w-[410px] h-[540px] max-h-[calc(100vh-100px)] flex flex-col transition-all duration-300 transform origin-bottom-right" 
         style="display: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.35);">
        
        <!-- 1. Header: Switch Tabs (Trợ Lý AI vs Chat Admin) & Close Button -->
        <div class="bg-slate-900 dark:bg-[#060a14] p-3.5 text-white flex items-center justify-between gap-2 border-b border-slate-800 flex-shrink-0">
            
            <!-- Segmented Control Switcher -->
            <div class="flex items-center bg-slate-800/90 dark:bg-slate-800/60 p-1 rounded-2xl border border-slate-700/60 text-[11px] font-bold">
                <button type="button" id="tab-btn-ai" onclick="switchChatMode('ai')" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 bg-blue-600 text-white shadow-sm">
                    <span>🤖</span>
                    <span>Trợ Lý AI</span>
                </button>
                <button type="button" id="tab-btn-admin" onclick="switchChatMode('admin')" 
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 text-slate-300 hover:text-white">
                    <span>👨‍💼</span>
                    <span>Gặp Admin</span>
                </button>
            </div>

            <!-- Status Indicator & Close Button -->
            <div class="flex items-center gap-2">
                <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-800/60 px-2 py-0.5 rounded-full">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Online
                </span>
                <button type="button" onclick="closeCustomerChat()" 
                        class="w-7 h-7 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition cursor-pointer" 
                        title="Đóng cửa sổ chat">
                    ✕
                </button>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODE 1: TRỢ LÝ AI GEMINI TỰ ĐỘNG (AI ASSISTANT CHAT)                      -->
        <!-- ========================================================================= -->
        <div id="ai-chat-panel" class="flex-1 flex flex-col min-h-0">
            
            <!-- AI Subheader Notice with Fast Link to Admin -->
            <div class="px-4 py-2 bg-blue-50/80 dark:bg-blue-950/40 border-b border-blue-100 dark:border-blue-900/60 flex items-center justify-between text-[11px] font-semibold text-blue-700 dark:text-blue-300 flex-shrink-0">
                <span class="flex items-center gap-1.5">
                    <span>⚡</span> Trả lời tự động 24/7
                </span>
                <button type="button" onclick="switchChatMode('admin')" class="font-bold underline hover:text-blue-900 dark:hover:text-blue-100 flex items-center gap-0.5">
                    <span>Gặp nhân viên</span> <span>→</span>
                </button>
            </div>

            <!-- AI Message History Container -->
            <div id="ai-messages-list" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/90 dark:bg-slate-950/90 text-xs transition-colors duration-200"
                 style="scroll-behavior: smooth;">
                
                <!-- AI Initial Greeting Message -->
                <div class="flex items-start gap-2">
                    <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-sm">
                        🤖
                    </div>
                    <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 max-w-[85%] leading-relaxed">
                        <p class="font-bold text-blue-600 dark:text-blue-400 mb-1">Xin chào bạn! 👋</p>
                        <p>Tôi là <strong>Trợ lý AI PhoneStore</strong>. Tôi có thể tư vấn giá máy, cấu hình, so sánh các dòng iPhone / Samsung và chính sách bảo hành 24/7.</p>
                        <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-1.5">
                            <button type="button" onclick="sendAiQuickPrompt('Tư vấn iPhone bán chạy')" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950 text-slate-700 dark:text-slate-300 hover:text-blue-600 rounded-lg text-[10px] font-bold transition">
                                📱 Giá iPhone
                            </button>
                            <button type="button" onclick="sendAiQuickPrompt('Có những dòng Samsung nào?')" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950 text-slate-700 dark:text-slate-300 hover:text-blue-600 rounded-lg text-[10px] font-bold transition">
                                ✨ Samsung Galaxy
                            </button>
                            <button type="button" onclick="sendAiQuickPrompt('Chính sách bảo hành và ship hàng')" class="px-2 py-1 bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950 text-slate-700 dark:text-slate-300 hover:text-blue-600 rounded-lg text-[10px] font-bold transition">
                                🛡️ Bảo hành & Ship
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- AI Input Box -->
            <div class="p-3 bg-white dark:bg-[#0c1322] border-t border-slate-200 dark:border-slate-800 flex-shrink-0">
                <form id="ai-chat-form" onsubmit="handleSendAiMessage(event)" class="flex items-center gap-2">
                    <input type="text" id="ai-chat-input" 
                           class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-xs font-semibold text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 transition" 
                           placeholder="Hỏi AI về giá máy, tư vấn mua sắm..." 
                           autocomplete="off">
                    <button type="submit" id="ai-send-btn" 
                            class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs rounded-full shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1 disabled:opacity-50">
                        <span>Gửi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODE 2: CHAT TRỰC TIẾP VỚI QUẢN TRỊ VIÊN (ADMIN LIVECHAT)                -->
        <!-- ========================================================================= -->
        <div id="admin-chat-panel" class="flex-1 flex flex-col min-h-0" style="display: none;">
            
            <!-- Admin Subheader Notice -->
            <div class="px-4 py-2 bg-emerald-50/80 dark:bg-emerald-950/40 border-b border-emerald-100 dark:border-emerald-900/60 flex items-center justify-between text-[11px] font-semibold text-emerald-700 dark:text-emerald-300 flex-shrink-0">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Kết nối trực tiếp với Quản Trị Viên
                </span>
                <button type="button" onclick="switchChatMode('ai')" class="font-bold underline hover:text-emerald-900 dark:hover:text-emerald-100 flex items-center gap-0.5">
                    <span>Dùng Trợ lý AI</span> <span>→</span>
                </button>
            </div>

            @auth
                <!-- Khách đã đăng nhập: Lịch sử tin nhắn với Admin -->
                <div id="admin-messages-list" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/90 dark:bg-slate-950/90 text-xs transition-colors duration-200"
                     style="scroll-behavior: smooth;">
                    <div class="text-center py-8 text-slate-400 dark:text-slate-500">
                        <div class="inline-block w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                        <p class="text-[11px] font-semibold">Đang tải lịch sử hội thoại với Admin...</p>
                    </div>
                </div>

                <!-- Ô nhập gửi tin nhắn cho Admin -->
                <div class="p-3 bg-white dark:bg-[#0c1322] border-t border-slate-200 dark:border-slate-800 flex-shrink-0">
                    <form id="admin-chat-form" onsubmit="handleSendAdminMessage(event)" class="flex items-center gap-2">
                        <input type="text" id="admin-chat-input" 
                               class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-full text-xs font-semibold text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 transition" 
                               placeholder="Nhập tin nhắn gửi tới Admin..." 
                               autocomplete="off">
                        <button type="submit" id="admin-send-btn" 
                                class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs rounded-full shadow-md shadow-emerald-500/20 transition cursor-pointer flex items-center gap-1 disabled:opacity-50">
                            <span>Gửi</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            @else
                <!-- Khách chưa đăng nhập: Hiển thị giao diện yêu cầu đăng nhập ở giữa -->
                <div class="flex-1 p-6 flex flex-col items-center justify-center text-center bg-slate-50/90 dark:bg-slate-950/90 space-y-4">
                    <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-3xl shadow-sm animate-bounce">
                        🔒
                    </div>
                    
                    <div>
                        <h4 class="text-base font-black text-slate-900 dark:text-white">Yêu Cầu Đăng Nhập</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed max-w-[260px] mx-auto">
                            Vui lòng đăng nhập tài khoản để gửi tin nhắn trực tiếp và lưu lịch sử trao đổi với Quản trị viên.
                        </p>
                    </div>

                    <div class="w-full pt-2 space-y-2.5">
                        <a href="{{ route('login') }}" 
                           class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 hover:from-blue-700 hover:to-indigo-800 text-white font-black text-xs rounded-2xl shadow-xl shadow-blue-500/30 flex items-center justify-center gap-2 hover:scale-[1.02] transition-all duration-200 cursor-pointer">
                            <span>🔑</span>
                            <span>Đăng Nhập Để Chat Với Admin</span>
                        </a>

                        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                            Hoặc bạn có thể 
                            <button type="button" onclick="switchChatMode('ai')" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">
                                trò chuyện với Trợ lý AI
                            </button>
                        </p>
                    </div>
                </div>
            @endauth

        </div>

    </div>
</div>

<style>
    /* Thanh cuộn mềm */
    #ai-messages-list::-webkit-scrollbar,
    #admin-messages-list::-webkit-scrollbar {
        width: 4px;
    }
    #ai-messages-list::-webkit-scrollbar-track,
    #admin-messages-list::-webkit-scrollbar-track {
        background: transparent;
    }
    #ai-messages-list::-webkit-scrollbar-thumb,
    #admin-messages-list::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.3);
        border-radius: 9999px;
    }

    /* Hiệu ứng bong bóng chat */
    .chat-bubble-fade {
        animation: bubbleFadeIn 0.2s ease-out;
    }
    @keyframes bubbleFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
let currentChatMode = 'ai'; // 'ai' | 'admin'
let lastAdminMessagesHash = "";

function openCustomerChat() {
    const popup = document.getElementById('chat-popup');
    const toggle = document.getElementById('chat-toggle');
    if (popup) popup.style.display = 'flex';
    if (toggle) toggle.style.display = 'none';

    if (currentChatMode === 'ai') {
        const input = document.getElementById('ai-chat-input');
        if (input) setTimeout(() => input.focus(), 150);
    } else {
        @auth
            loadAdminMessages(true);
            const input = document.getElementById('admin-chat-input');
            if (input) setTimeout(() => input.focus(), 150);
        @endauth
    }
}

function closeCustomerChat() {
    const popup = document.getElementById('chat-popup');
    const toggle = document.getElementById('chat-toggle');
    if (popup) popup.style.display = 'none';
    if (toggle) toggle.style.display = 'flex';
}

function switchChatMode(mode) {
    currentChatMode = mode;
    const tabAi = document.getElementById('tab-btn-ai');
    const tabAdmin = document.getElementById('tab-btn-admin');
    const panelAi = document.getElementById('ai-chat-panel');
    const panelAdmin = document.getElementById('admin-chat-panel');

    if (mode === 'ai') {
        panelAi.style.display = 'flex';
        panelAdmin.style.display = 'none';
        
        tabAi.className = "flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 bg-blue-600 text-white shadow-sm";
        tabAdmin.className = "flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 text-slate-300 hover:text-white";
        
        const input = document.getElementById('ai-chat-input');
        if (input) setTimeout(() => input.focus(), 100);
    } else {
        panelAi.style.display = 'none';
        panelAdmin.style.display = 'flex';
        
        tabAdmin.className = "flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 bg-emerald-600 text-white shadow-sm";
        tabAi.className = "flex items-center gap-1.5 px-3 py-1.5 rounded-xl transition-all duration-200 text-slate-300 hover:text-white";
        
        @auth
            loadAdminMessages(true);
            const input = document.getElementById('admin-chat-input');
            if (input) setTimeout(() => input.focus(), 100);
        @endauth
    }
}

// -------------------------------------------------------------
// 1. LOGIC CHATBOT AI GEMINI
// -------------------------------------------------------------
function sendAiQuickPrompt(text) {
    const input = document.getElementById('ai-chat-input');
    if (input) {
        input.value = text;
        handleSendAiMessage(new Event('submit'));
    }
}

async function handleSendAiMessage(e) {
    if (e) e.preventDefault();
    const input = document.getElementById('ai-chat-input');
    const sendBtn = document.getElementById('ai-send-btn');
    const list = document.getElementById('ai-messages-list');
    if (!input || !list) return;

    const message = input.value.trim();
    if (!message) return;

    // 1. Render tin nhắn User
    appendAiMessageBubble('user', message);
    input.value = '';
    if (sendBtn) sendBtn.disabled = true;

    // 2. Typing Indicator
    const typingId = 'ai-typing-' + Date.now();
    const typingDiv = document.createElement('div');
    typingDiv.id = typingId;
    typingDiv.className = 'flex items-start gap-2 chat-bubble-fade';
    typingDiv.innerHTML = `
        <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs flex-shrink-0">🤖</div>
        <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl rounded-tl-none border border-slate-200 dark:border-slate-800 flex gap-1.5 items-center">
            <span class="w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full animate-pulse"></span>
            <span class="w-1.5 h-1.5 bg-blue-500 dark:bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s"></span>
            <span class="w-1.5 h-1.5 bg-blue-400 dark:bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.4s"></span>
        </div>
    `;
    list.appendChild(typingDiv);
    list.scrollTop = list.scrollHeight;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const response = await fetch("{{ route('chat') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        });

        const data = await response.json();
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();

        appendAiMessageBubble('bot', data.reply || 'Xin lỗi, tôi không thể xử lý câu hỏi này lúc này.');
    } catch (err) {
        const typingEl = document.getElementById(typingId);
        if (typingEl) typingEl.remove();
        appendAiMessageBubble('bot', 'Có lỗi kết nối. Bạn có thể nhấn nút **Gặp Admin** phía trên để được tư vấn viên hỗ trợ nhé!');
    } finally {
        if (sendBtn) sendBtn.disabled = false;
        list.scrollTop = list.scrollHeight;
    }
}

function appendAiMessageBubble(sender, text) {
    const list = document.getElementById('ai-messages-list');
    if (!list) return;

    const row = document.createElement('div');
    row.className = 'chat-bubble-fade ' + (sender === 'user' ? 'flex items-start justify-end gap-2' : 'flex items-start gap-2');

    let formatted = escapeChatHtml(text);
    // Parse bold **text**
    formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-blue-600 dark:text-blue-400">$1</strong>');
    formatted = formatted.replace(/\n/g, '<br>');

    if (sender === 'user') {
        row.innerHTML = `
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[85%] leading-relaxed text-xs break-words">
                ${formatted}
            </div>
            <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-black flex-shrink-0">
                You
            </div>
        `;
    } else {
        row.innerHTML = `
            <div class="w-7 h-7 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-sm">
                🤖
            </div>
            <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 max-w-[85%] leading-relaxed text-xs break-words">
                ${formatted}
            </div>
        `;
    }

    list.appendChild(row);
    list.scrollTop = list.scrollHeight;
}

// -------------------------------------------------------------
// 2. LOGIC LIVECHAT VỚI QUẢN TRỊ VIÊN (LAB 07)
// -------------------------------------------------------------
@auth
function loadAdminMessages(forceScroll = false) {
    const chatBox = document.getElementById('admin-messages-list');
    if (!chatBox) return;

    fetch("{{ route('user.chat.messages') }}", {
        headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
        }
    })
    .then(res => res.json())
    .then(messages => {
        const currentHash = JSON.stringify(messages);
        if (currentHash === lastAdminMessagesHash && !forceScroll) return;
        lastAdminMessagesHash = currentHash;

        let html = "";
        if (!messages || messages.length === 0) {
            html = `
                <div class="text-center py-10 px-4 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mx-auto">
                        👨‍💼
                    </div>
                    <h5 class="font-extrabold text-slate-800 dark:text-slate-200 text-xs">Kết nối trực tiếp với Quản trị viên</h5>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">
                        Nhập tin nhắn bên dưới để bắt đầu trao đổi trực tiếp với tư vấn viên PhoneStore.
                    </p>
                </div>
            `;
        } else {
            const currentUserId = "{{ Auth::id() }}";
            messages.forEach(msg => {
                const isMe = (msg.sender_id == currentUserId);
                const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                
                if (isMe) {
                    html += `
                        <div class="flex items-start justify-end gap-2 chat-bubble-fade">
                            <div class="text-right">
                                <span class="text-[9px] font-bold text-slate-400 block mb-0.5">${timeStr}</span>
                                <div class="bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm max-w-[85%] text-left leading-relaxed text-xs break-words inline-block">
                                    ${escapeChatHtml(msg.content)}
                                </div>
                            </div>
                            <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-black flex-shrink-0 mt-3">
                                You
                            </div>
                        </div>
                    `;
                } else {
                    html += `
                        <div class="flex items-start gap-2 chat-bubble-fade">
                            <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center text-xs flex-shrink-0 shadow-sm mt-3">
                                ⚡
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 block mb-0.5">Admin • ${timeStr}</span>
                                <div class="bg-white dark:bg-slate-900 p-3 rounded-2xl rounded-tl-none shadow-sm border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-100 max-w-[85%] leading-relaxed text-xs break-words inline-block">
                                    ${escapeChatHtml(msg.content)}
                                </div>
                            </div>
                        </div>
                    `;
                }
            });
        }

        chatBox.innerHTML = html;
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(err => console.error("Lỗi tải tin nhắn Admin Livechat:", err));
}

function handleSendAdminMessage(e) {
    if (e) e.preventDefault();
    const input = document.getElementById('admin-chat-input');
    const sendBtn = document.getElementById('admin-send-btn');
    if (!input || !sendBtn) return;

    let message = input.value.trim();
    if (message === "") return;

    input.disabled = true;
    sendBtn.disabled = true;

    fetch("{{ route('user.chat.send') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "",
            "Content-Type": "application/json",
            "Accept": "application/json"
        },
        body: JSON.stringify({ message: message })
    })
    .then(res => {
        if (!res.ok) throw new Error("Gửi tin nhắn thất bại");
        return res.json();
    })
    .then(data => {
        input.value = "";
        input.disabled = false;
        sendBtn.disabled = false;
        input.focus();
        loadAdminMessages(true);
    })
    .catch(err => {
        console.error("Lỗi gửi tin tới Admin:", err);
        input.disabled = false;
        sendBtn.disabled = false;
    });
}

// Auto polling cho Admin chat mỗi 3s
setInterval(() => {
    const popup = document.getElementById('chat-popup');
    if (popup && popup.style.display !== "none" && currentChatMode === 'admin') {
        loadAdminMessages(false);
    }
}, 3000);
@endauth

function escapeChatHtml(text) {
    const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
    return (text || '').replace(/[&<>"']/g, m => map[m]);
}
</script>
@endif
