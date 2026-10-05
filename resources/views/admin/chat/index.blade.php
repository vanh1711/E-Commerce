@extends('layouts.admin')

@section('title', 'Trung Tâm Tin Nhắn Khách Hàng - Admin PhoneStore')
@section('page_title', 'Tin nhắn khách hàng')
@section('page_heading', 'Trung Tâm Tin Nhắn Khách Hàng')

@section('content')
<div class="space-y-4">

    <!-- Top Banner: Thống kê & Trạng thái -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-[#0c1322] p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition-colors">
            <div class="w-11 h-11 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-black">
                👥
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tổng khách hàng</p>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">{{ $totalCustomers }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0c1322] p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition-colors">
            <div class="w-11 h-11 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-black">
                💬
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Hệ thống hỗ trợ</p>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Trực Tuyến Realtime</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0c1322] p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-3.5 transition-colors">
            <div class="w-11 h-11 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl font-black">
                🔔
            </div>
            <div>
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tin nhắn chưa đọc</p>
                <h3 id="topUnreadBadge" class="text-xl font-black text-amber-600 dark:text-amber-400">{{ $unreadCount }}</h3>
            </div>
        </div>
    </div>

    <!-- Main Chat Container: 2 Cột Chuyên Nghiệp -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col md:flex-row h-[720px] max-h-[82vh] transition-colors">
        
        <!-- CỘT TRÁI (DANH SÁCH LIÊN HỆ KHÁCH HÀNG) -->
        <div class="w-full md:w-80 lg:w-96 border-r border-slate-200 dark:border-slate-800 flex flex-col bg-slate-50/50 dark:bg-[#090e1a]/80 flex-shrink-0">
            
            <!-- Header Cột Trái & Ô Tìm Kiếm -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 space-y-3">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>💬</span> Danh Sách Khách Hàng
                    </h2>
                    <span id="contactCountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                        Đang tải...
                    </span>
                </div>

                <!-- Ô tìm kiếm tức thì -->
                <div class="relative">
                    <input type="text" id="userSearchInput" 
                           placeholder="Tìm theo tên hoặc email khách..." 
                           class="w-full pl-9 pr-8 py-2 bg-white dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                    <span class="absolute left-3 top-2.5 text-slate-400 text-xs">🔍</span>
                    <button type="button" id="clearSearchBtn" class="hidden absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-bold">✕</button>
                </div>
            </div>

            <!-- Danh sách người dùng cuộn mượt -->
            <div id="userListContainer" class="flex-1 overflow-y-auto p-2 space-y-1 divide-y divide-slate-100/50 dark:divide-slate-800/50">
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 text-xs font-medium space-y-2">
                    <div class="animate-spin text-xl inline-block">⌛</div>
                    <p>Đang tải danh sách khách hàng...</p>
                </div>
            </div>

        </div>

        <!-- CỘT PHẢI (KHÔNG GIAN TRÒ CHUYỆN CHI TIẾT) -->
        <div class="flex-1 flex flex-col bg-white dark:bg-[#0c1322] min-w-0">
            
            <!-- Header Khung Chat -->
            <div id="chatHeaderActive" class="hidden px-5 py-3.5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-white dark:bg-[#090e1a]/90 backdrop-blur-sm">
                <div class="flex items-center gap-3 min-w-0">
                    <div id="activeAvatar" class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-sm flex items-center justify-center shadow-md flex-shrink-0 uppercase">
                        U
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <h3 id="activeCustomerName" class="text-sm font-black text-slate-900 dark:text-white truncate">Khách Hàng</h3>
                            <span class="px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 text-[10px] font-bold">Khách Hàng</span>
                        </div>
                        <p id="activeCustomerEmail" class="text-[11px] text-slate-400 dark:text-slate-500 truncate font-mono">user@example.com</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="refreshCurrentChat()" title="Làm mới tin nhắn"
                            class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition text-xs">
                        🔄
                    </button>
                </div>
            </div>

            <!-- Header Khung Chat Rỗng (Khi chưa chọn khách) -->
            <div id="chatHeaderEmpty" class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center gap-2 text-xs font-bold text-slate-400">
                <span>💬</span> <span>Chưa chọn cuộc hội thoại nào</span>
            </div>

            <!-- Khung Lịch Sử Tin Nhắn -->
            <div id="chatMessagesBox" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-3.5 bg-slate-50/40 dark:bg-[#060a12]/70 text-xs">
                
                <!-- Mặc định khi chưa chọn khách hàng -->
                <div id="noUserSelectedState" class="h-full flex flex-col items-center justify-center text-center p-8 text-slate-400 dark:text-slate-500 space-y-3">
                    <div class="w-16 h-16 rounded-3xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-3xl">
                        👈
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-800 dark:text-slate-200">Chọn một khách hàng để bắt đầu</h4>
                        <p class="text-[11px] max-w-sm mt-1">Bạn có thể chọn bất kỳ khách hàng nào ở cột bên trái để xem lịch sử hoặc chủ động gửi tin nhắn tư vấn trước.</p>
                    </div>
                </div>

            </div>

            <!-- Quick Template Responses -->
            <div id="quickRepliesBar" class="hidden px-4 py-2 border-t border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-[#070b14]/50 flex items-center gap-2 overflow-x-auto text-[11px]">
                <span class="text-slate-400 text-[10px] font-bold uppercase whitespace-nowrap">Gợi ý nhanh:</span>
                <button type="button" onclick="useQuickReply('Xin chào quý khách! Em là tư vấn viên của PhoneStore, em có thể hỗ trợ gì cho mình ạ?')"
                        class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-blue-600 whitespace-nowrap transition cursor-pointer">
                    👋 Chào khách mới
                </button>
                <button type="button" onclick="useQuickReply('Đơn hàng của quý khách đã được đóng gói và chuẩn bị giao cho đơn vị vận chuyển GHN Express ạ.')"
                        class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-blue-600 whitespace-nowrap transition cursor-pointer">
                    📦 Thông báo xuất kho
                </button>
                <button type="button" onclick="useQuickReply('Cảm ơn quý khách đã mua sắm tại PhoneStore! Nếu cần hỗ trợ thêm thông tin kỹ thuật, anh/chị cứ nhắn em nhé.')"
                        class="px-2.5 py-1 rounded-xl bg-white dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:text-blue-600 whitespace-nowrap transition cursor-pointer">
                    ❤️ Cảm ơn khách hàng
                </button>
            </div>

            <!-- Ô Nhập Liệu & Nút Gửi Tin Nhắn -->
            <div id="chatInputArea" class="hidden p-3 sm:p-4 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-[#090e1a]/95">
                <form id="adminChatSendForm" class="flex items-center gap-2">
                    <input type="text" id="adminMessageInput" 
                           placeholder="Nhập tin nhắn trả lời hoặc chủ động tư vấn khách hàng..." 
                           class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 transition"
                           autocomplete="off">
                    
                    <button type="submit" id="adminSendBtn"
                            class="px-5 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black text-xs rounded-2xl shadow-md shadow-blue-600/30 transition transform hover:scale-105 active:scale-95 cursor-pointer flex items-center gap-1.5 flex-shrink-0">
                        <span>Gửi</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let allUsers = [];
    let currentFilter = 'all';
    let currentSelectedUserId = {{ $initialUser ? $initialUser->id : 'null' }};
    let pollingInterval = null;

    const userListContainer = document.getElementById('userListContainer');
    const userSearchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const contactCountBadge = document.getElementById('contactCountBadge');
    
    const chatHeaderActive = document.getElementById('chatHeaderActive');
    const chatHeaderEmpty = document.getElementById('chatHeaderEmpty');
    const activeAvatar = document.getElementById('activeAvatar');
    const activeCustomerName = document.getElementById('activeCustomerName');
    const activeCustomerEmail = document.getElementById('activeCustomerEmail');
    
    const chatMessagesBox = document.getElementById('chatMessagesBox');
    const quickRepliesBar = document.getElementById('quickRepliesBar');
    const chatInputArea = document.getElementById('chatInputArea');
    const adminMessageInput = document.getElementById('adminMessageInput');
    const adminChatSendForm = document.getElementById('adminChatSendForm');
    const adminSendBtn = document.getElementById('adminSendBtn');

    // 1. Tải danh sách tất cả người dùng
    function fetchUsers(query = '') {
        const url = new URL("{{ route('admin.chat.users') }}", window.location.origin);
        if (query) url.searchParams.set('q', query);

        fetch(url, {
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
            }
        })
        .then(res => res.json())
        .then(data => {
            allUsers = data;
            renderUserList();
            
            // Nếu có user khởi tạo từ query params
            if (currentSelectedUserId && !document.querySelector(`.user-item[data-id="${currentSelectedUserId}"]`)) {
                // Tự động load tin nhắn cho user
                loadUserConversation(currentSelectedUserId);
            }
        })
        .catch(err => console.error("Lỗi tải danh sách khách hàng:", err));
    }

    // 2. Render danh sách tất cả khách hàng
    function renderUserList() {
        let filtered = allUsers;
        const searchVal = userSearchInput.value.trim().toLowerCase();

        if (searchVal) {
            filtered = filtered.filter(u => 
                (u.name && u.name.toLowerCase().includes(searchVal)) || 
                (u.email && u.email.toLowerCase().includes(searchVal))
            );
        }

        contactCountBadge.textContent = `${filtered.length} khách`;

        if (filtered.length === 0) {
            userListContainer.innerHTML = `
                <div class="p-8 text-center text-slate-400 dark:text-slate-500 text-xs">
                    <p class="font-bold">Không tìm thấy khách hàng nào</p>
                    <p class="text-[11px] mt-1">Thử tìm kiếm với từ khóa khác.</p>
                </div>
            `;
            return;
        }

        let html = '';
        filtered.forEach(u => {
            const isSelected = (currentSelectedUserId == u.id);
            const activeClass = isSelected ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'hover:bg-slate-100 dark:hover:bg-slate-800/80 text-slate-700 dark:text-slate-200';
            const subTextColor = isSelected ? 'text-blue-100' : 'text-slate-400 dark:text-slate-500';
            const avatarBg = isSelected ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300';
            
            let snippet = 'Chưa có tin nhắn nào';
            if (u.latest_message) {
                snippet = (u.latest_message.is_me ? 'Bạn: ' : '') + u.latest_message.content;
            }

            let unreadBadgeHtml = '';
            if (u.unread_count > 0) {
                unreadBadgeHtml = `<span class="px-2 py-0.5 rounded-full bg-rose-500 text-white font-black text-[10px]">${u.unread_count}</span>`;
            }

            let timeStr = u.latest_message ? u.latest_message.time_human : '';

            html += `
                <div class="user-item p-3 rounded-2xl cursor-pointer transition-all duration-150 flex items-center gap-3 ${activeClass}" 
                     data-id="${u.id}" onclick="selectCustomer(${u.id})">
                    <div class="w-10 h-10 rounded-2xl ${avatarBg} font-black text-sm flex items-center justify-center flex-shrink-0 uppercase">
                        ${u.avatar_text || 'U'}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-1 mb-0.5">
                            <h4 class="font-bold text-xs truncate">${escapeHtml(u.name)}</h4>
                            <span class="text-[10px] ${subTextColor} whitespace-nowrap font-mono">${timeStr}</span>
                        </div>
                        <div class="flex items-center justify-between gap-1">
                            <p class="text-[11px] ${subTextColor} truncate font-normal">${escapeHtml(snippet)}</p>
                            ${unreadBadgeHtml}
                        </div>
                    </div>
                </div>
            `;
        });

        userListContainer.innerHTML = html;
    }

    // 4. Chọn một khách hàng để chat
    window.selectCustomer = function(userId) {
        currentSelectedUserId = userId;
        renderUserList(); // Cập nhật highlight active
        loadUserConversation(userId);
    };

    // 5. Tải tin nhắn của khách hàng
    function loadUserConversation(userId) {
        if (!userId) return;

        chatHeaderEmpty.classList.add('hidden');
        chatHeaderActive.classList.remove('hidden');
        quickRepliesBar.classList.remove('hidden');
        chatInputArea.classList.remove('hidden');

        fetch(`/admin/chat/messages/${userId}`, {
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
            }
        })
        .then(res => res.json())
        .then(data => {
            const customer = data.customer;
            const messages = data.messages;

            if (customer) {
                activeCustomerName.textContent = customer.name;
                activeCustomerEmail.textContent = customer.email;
                activeAvatar.textContent = (customer.name ? customer.name.charAt(0) : 'U').toUpperCase();
            }

            renderMessages(messages, customer);
            setTimeout(() => { if (adminMessageInput) adminMessageInput.focus(); }, 100);
        })
        .catch(err => console.error("Lỗi tải tin nhắn cuộc hội thoại:", err));
    }

    // 6. Hiển thị danh sách tin nhắn
    function renderMessages(messages, customer) {
        const currentAdminId = "{{ Auth::id() }}";

        if (!messages || messages.length === 0) {
            chatMessagesBox.innerHTML = `
                <div class="h-full flex flex-col items-center justify-center text-center p-8 text-slate-400 dark:text-slate-500 space-y-3">
                    <div class="w-14 h-14 rounded-3xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-2xl">
                        ✨
                    </div>
                    <div>
                        <h4 class="font-black text-sm text-slate-800 dark:text-slate-200">Chưa có tin nhắn nào với ${escapeHtml(customer?.name || 'khách hàng')}</h4>
                        <p class="text-[11px] max-w-sm mt-1">Hãy gửi tin nhắn mở đầu hoặc dùng các mẫu gợi ý nhanh bên dưới để tư vấn cho khách.</p>
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = (msg.sender_id == currentAdminId || msg.sender_id != currentSelectedUserId);
            const timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
            const adminSenderLabel = (msg.sender_id == currentAdminId) ? '' : ((msg.sender ? msg.sender.name : 'Admin') + ' · ');

            if (isMe) {
                // Bong bóng tin nhắn Admin gửi (Bên Phải)
                html += `
                    <div class="flex flex-col items-end space-y-1">
                        <div class="max-w-[75%] sm:max-w-[65%] px-4 py-2.5 rounded-2xl rounded-br-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium shadow-md shadow-blue-600/15">
                            <p class="leading-relaxed whitespace-pre-line">${escapeHtml(msg.content)}</p>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono pr-1">${adminSenderLabel}${timeStr} · Đã gửi</span>
                    </div>
                `;
            } else {
                // Bong bóng tin nhắn Khách gửi (Bên Trái)
                html += `
                    <div class="flex items-start gap-2.5 max-w-[80%] sm:max-w-[70%]">
                        <div class="w-7 h-7 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs flex items-center justify-center flex-shrink-0 uppercase mt-0.5">
                            ${customer?.name ? customer.name.charAt(0).toUpperCase() : 'K'}
                        </div>
                        <div class="space-y-1">
                            <div class="px-4 py-2.5 rounded-2xl rounded-tl-sm bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 font-medium border border-slate-200/60 dark:border-slate-700/60 shadow-sm">
                                <p class="leading-relaxed whitespace-pre-line">${escapeHtml(msg.content)}</p>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono pl-1">${timeStr}</span>
                        </div>
                    </div>
                `;
            }
        });

        chatMessagesBox.innerHTML = html;
        chatMessagesBox.scrollTop = chatMessagesBox.scrollHeight;
    }

    // 7. Gửi tin nhắn từ Admin
    adminChatSendForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const content = adminMessageInput.value.trim();
        if (!content || !currentSelectedUserId) return;

        adminMessageInput.disabled = true;
        adminSendBtn.disabled = true;

        fetch("{{ route('admin.chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
            },
            body: JSON.stringify({
                user_id: currentSelectedUserId,
                message: content
            })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gửi tin nhắn thất bại");
            return res.json();
        })
        .then(data => {
            adminMessageInput.value = '';
            adminMessageInput.disabled = false;
            adminSendBtn.disabled = false;
            adminMessageInput.focus();
            
            // Reload tin nhắn và danh sách
            loadUserConversation(currentSelectedUserId);
            fetchUsers(userSearchInput.value.trim());
        })
        .catch(err => {
            console.error("Lỗi gửi tin nhắn:", err);
            adminMessageInput.disabled = false;
            adminSendBtn.disabled = false;
        });
    });

    // 8. Dùng tin nhắn mẫu nhanh
    window.useQuickReply = function(text) {
        if (!adminMessageInput) return;
        adminMessageInput.value = text;
        adminMessageInput.focus();
    };

    window.refreshCurrentChat = function() {
        if (currentSelectedUserId) {
            loadUserConversation(currentSelectedUserId);
        }
        fetchUsers(userSearchInput.value.trim());
    };

    // 9. Tìm kiếm realtime
    let searchTimeout = null;
    userSearchInput.addEventListener('input', function () {
        const val = this.value.trim();
        clearSearchBtn.classList.toggle('hidden', val.length === 0);

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetchUsers(val);
        }, 300);
    });

    clearSearchBtn.addEventListener('click', function () {
        userSearchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        fetchUsers();
    });

    // 10. Polling định kỳ tự động làm mới (mỗi 2.5 giây)
    pollingInterval = setInterval(() => {
        if (currentSelectedUserId) {
            loadUserConversation(currentSelectedUserId);
        }
        fetchUsers(userSearchInput.value.trim());
    }, 2500);

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }

    // Khởi tạo
    fetchUsers();
});
</script>
@endpush
@endsection
