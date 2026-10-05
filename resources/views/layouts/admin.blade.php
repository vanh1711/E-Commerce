<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - Trung Tâm Quản Trị Hệ Thống</title>
    
    <!-- Favicon PhoneStore Admin -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}?v=2">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        },
                        admin: {
                            sidebar: '#0b1329',
                            sidebardark: '#070b18',
                            hover: '#182343',
                            active: '#2563eb',
                        }
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                        'admin-card': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'admin-card-dark': '0 4px 20px -2px rgba(0, 0, 0, 0.3)',
                    }
                }
            }
        }
    </script>

    <!-- Đồng bộ tức thì Dark/Light Theme -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('phonestore_theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Tùy chỉnh scrollbar thanh lịch cho Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* Pulse badge */
        .badge-live-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
        }
    </style>
    @stack('head')
</head>
<body class="bg-slate-50 dark:bg-[#070b14] text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white">

    <!-- OVERLAY CHO MOBILE SIDEBAR -->
    <div id="adminSidebarOverlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-40 lg:hidden hidden transition-opacity duration-300"></div>

    <div class="flex flex-1 min-h-screen">

        <!-- ====================================================================== -->
        <!-- 1. ADMIN SIDEBAR (Thanh Điều Hướng Bên Trái Chuyên Nghiệp)             -->
        <!-- ====================================================================== -->
        <aside id="adminSidebar" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-[#0c1322] dark:bg-[#060a14] text-slate-300 border-r border-slate-800/80 flex flex-col justify-between transition-transform duration-300 -translate-x-full lg:translate-x-0">
            
            <!-- Phần Trên: Brand & Menu Links -->
            <div class="flex flex-col flex-1 overflow-y-auto sidebar-scroll">
                
                <!-- Logo Quản Trị Hệ Thống -->
                <div class="h-20 px-6 flex items-center justify-between border-b border-slate-800/80">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 text-white flex items-center justify-center font-black text-lg shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform duration-300">
                            📱
                        </div>
                        <div>
                            <span class="text-base font-black tracking-tight text-white block uppercase leading-none">
                                PHONE<span class="text-blue-400">STORE</span>
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mt-1">Admin Portal</span>
                        </div>
                    </a>

                    <!-- Nút Đóng Trên Mobile -->
                    <button onclick="toggleAdminSidebar()" class="lg:hidden w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold">
                        ✕
                    </button>
                </div>

                <!-- Trạng Thái Admin Badge -->
                <div class="px-6 py-4">
                    <div class="px-3.5 py-2 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 badge-live-pulse"></span>
                            <span class="text-xs font-bold text-slate-200">Admin Control Online</span>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 border border-blue-500/30 font-bold">v2.5</span>
                    </div>
                </div>

                <!-- Navigation List -->
                <nav class="px-4 space-y-6 pb-6 text-xs font-semibold">
                    
                    <!-- Nhóm 1: CORE (Tổng quan) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Tổng Quan Điều Hành</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('dashboard') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <span class="text-base">📊</span>
                                <span>Bảng Điều Khiển (Dashboard)</span>
                            </a>
                        </div>
                    </div>

                    <!-- Nhóm 2: CATALOG (Quản lý hàng hóa) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Hàng Hóa & Danh Mục</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('products.index') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('products.index') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">📱</span>
                                    <span>Sản Phẩm</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('products.index') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }} font-bold">
                                    {{ \App\Models\Product::count() }}
                                </span>
                            </a>

                            <a href="{{ route('categories.index') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">📁</span>
                                    <span>Danh Mục</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('categories.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }} font-bold">
                                    {{ \App\Models\Category::count() }}
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Nhóm 3: USERS (Người dùng & Chăm sóc) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Tài Khoản & Thành Viên</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.users.index') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">👥</span>
                                    <span>Người Dùng</span>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }} font-bold">
                                    {{ \App\Models\User::count() }}
                                </span>
                            </a>

                            <a href="{{ route('admin.chat.index') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.chat.*') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">💬</span>
                                    <span>Tin Nhắn Khách</span>
                                </div>
                                @php
                                    $adminIdsList = \App\Models\User::where('is_admin', 1)
                                        ->orWhere('role', 'admin')
                                        ->orWhere('email', 'admin@gmail.com')
                                        ->orWhere('email', 'admin@example.com')
                                        ->pluck('id')
                                        ->toArray();
                                    $unreadChatCount = \App\Models\Message::whereIn('receiver_id', !empty($adminIdsList) ? $adminIdsList : [Auth::id()])->where('is_read', false)->count();
                                @endphp
                                @if($unreadChatCount > 0)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-rose-500 text-white font-black animate-pulse">
                                        {{ $unreadChatCount }} mới
                                    </span>
                                @else
                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('admin.chat.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }} font-bold">
                                        Chat
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Nhóm 4: OPERATIONS (Vận hành & Đơn hàng) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Vận Hành & Đơn Hàng</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.orders.index') }}" 
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">📦</span>
                                    <span>Đơn Hàng</span>
                                </div>
                                @php
                                    $pendingOrderCount = \App\Models\Order::whereIn('shipping_status', ['pending', 'not_shipped', 'processing', 'preparing'])->count();
                                @endphp
                                @if($pendingOrderCount > 0)
                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-black">
                                        {{ $pendingOrderCount }} mới
                                    </span>
                                @else
                                    <span class="text-[10px] px-2 py-0.5 rounded-full {{ request()->routeIs('admin.orders.*') ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }} font-bold">
                                        {{ \App\Models\Order::count() }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- Nhóm 5: REPORTS & FINANCIAL (Báo cáo & Tài chính) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Thống Kê & Tài Chính</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('admin.finance.index') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.finance.index') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <span class="text-base">📈</span>
                                <span>Thống Kê Tài Chính</span>
                            </a>

                            <a href="{{ route('admin.finance.transactions') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.finance.transactions') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <span class="text-base">💳</span>
                                <span>Giao Dịch Thanh Toán</span>
                            </a>

                            <a href="{{ route('admin.reports.charts') }}" 
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-200 {{ request()->routeIs('admin.reports.charts') ? 'bg-blue-600 text-white font-bold shadow-lg shadow-blue-600/30' : 'text-slate-300 hover:bg-slate-800/80 hover:text-white' }}">
                                <span class="text-base">📉</span>
                                <span>Biểu Đồ Doanh Thu</span>
                            </a>
                        </div>
                    </div>

                    <!-- Nhóm 6: EXTERNAL (Xem Cửa Hàng Bán Lẻ) -->
                    <div>
                        <span class="px-3 text-[10px] font-black uppercase text-slate-400 tracking-wider">Cửa Hàng Bán Lẻ</span>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('home') }}" target="_blank"
                               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-slate-300 hover:bg-slate-800/80 hover:text-cyan-400 transition-all duration-200 group">
                                <div class="flex items-center gap-3">
                                    <span class="text-base">🌐</span>
                                    <span>Xem Web Bán Lẻ</span>
                                </div>
                                <span class="text-xs text-slate-400 group-hover:text-cyan-400 transition">↗</span>
                            </a>
                        </div>
                    </div>

                </nav>

            </div>

            <!-- Phần Dưới: Admin User Info & Logout Button -->
            <div class="p-4 border-t border-slate-800/80 bg-[#090e1a] dark:bg-[#050810]">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-black text-sm uppercase shadow-md flex-shrink-0">
                            {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-bold text-xs text-white truncate">{{ Auth::user()?->name ?? 'Administrator' }}</p>
                            <span class="text-[10px] font-semibold text-blue-400 block truncate">Quản Trị Viên</span>
                        </div>
                    </div>

                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" title="Đăng xuất khỏi Admin" 
                                class="w-9 h-9 rounded-xl bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

        </aside>

        <!-- ====================================================================== -->
        <!-- 2. MAIN CONTENT WRAPPER (Bên Phải Sidebar)                              -->
        <!-- ====================================================================== -->
        <div class="flex-1 flex flex-col min-w-0 lg:pl-72">
            
            <!-- ADMIN TOPBAR (Thanh Công Cụ Trên Cùng) -->
            <header class="sticky top-0 z-30 h-20 bg-white/90 dark:bg-[#090e1a]/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-slate-800/80 px-4 sm:px-8 flex items-center justify-between gap-4 transition-colors duration-300">
                
                <!-- Nút Mở Sidebar (Mobile) + Breadcrumbs & Tiêu đề ngữ cảnh -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <button onclick="toggleAdminSidebar()" class="lg:hidden p-2 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <div>
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-400 dark:text-slate-500">
                            <span>Admin Portal</span>
                            <span>/</span>
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">@yield('page_title', 'Dashboard')</span>
                        </div>
                        <h2 class="text-base sm:text-lg font-black text-slate-900 dark:text-white tracking-tight leading-none mt-0.5">
                            @yield('page_heading', 'Bảng Điều Khiển')
                        </h2>
                    </div>
                </div>

                <!-- Bên Phải Topbar: Trạng thái API + Dark/Light Mode + Profile Menu -->
                <div class="flex items-center gap-2 sm:gap-3">
                    
                    <!-- Chips trạng thái tích hợp -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                        <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> GHN Express
                        </span>
                        <span class="text-slate-300 dark:text-slate-600">|</span>
                        <span class="flex items-center gap-1 text-pink-600 dark:text-pink-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-pink-500"></span> MoMo Sandbox
                        </span>
                    </div>

                    <!-- Nút Xem Web Bán Lẻ Nhanh -->
                    <a href="{{ route('home') }}" target="_blank" 
                       class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-2xl bg-slate-100 dark:bg-slate-800/80 hover:bg-blue-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 text-xs font-bold transition border border-slate-200 dark:border-slate-700/80 shadow-sm"
                       title="Mở website bán lẻ trong tab mới">
                        <span>🌐</span>
                        <span>Xem Web Bán Lẻ</span>
                        <span class="text-[10px] text-slate-400">↗</span>
                    </a>

                    <!-- Nút Dark / Light Mode -->
                    <button type="button" onclick="toggleAdminTheme()" 
                            class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-700 hover:text-amber-500 transition flex items-center justify-center shadow-sm cursor-pointer"
                            title="Đổi giao diện Sáng / Tối">
                        <span class="dark:hidden text-base">🌙</span>
                        <span class="hidden dark:inline-block text-base">☀️</span>
                    </button>

                    <!-- User Profile Dropdown Button -->
                    <div class="relative" id="adminHeaderDropdownContainer">
                        <button onclick="toggleAdminHeaderMenu()" type="button" 
                                class="flex items-center gap-2 p-1.5 sm:px-3 sm:py-1.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-2xl transition cursor-pointer">
                            <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xs font-black uppercase shadow-sm">
                                {{ strtoupper(substr(Auth::user()?->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="hidden sm:block text-left pr-1">
                                <p class="text-xs font-black text-slate-900 dark:text-white leading-none max-w-[120px] truncate">{{ Auth::user()?->name ?? 'Admin' }}</p>
                                <span class="text-[9px] font-bold text-slate-400 leading-none">Quản trị viên</span>
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <!-- Menu Dropdown -->
                        <div id="adminHeaderMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 py-2 z-50 animate-in fade-in slide-in-from-top-2">
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-xs font-bold text-slate-900 dark:text-white">{{ Auth::user()?->name ?? 'Admin' }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email ?? 'admin@example.com' }}</p>
                            </div>
                            <div class="p-1 space-y-0.5 text-xs font-semibold">
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>📊</span> Dashboard
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>📦</span> Quản lý Đơn hàng & GHN
                                </a>
                                <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>📱</span> Quản lý Sản phẩm
                                </a>
                                <a href="{{ route('admin.chat.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>💬</span> Tin Nhắn Khách Hàng
                                </a>
                                <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>📁</span> Quản lý Danh mục
                                </a>
                                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-blue-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    <span class="flex items-center gap-2"><span>🌐</span> Xem Web Bán Lẻ</span>
                                    <span class="text-[10px] text-slate-400">↗</span>
                                </a>
                            </div>
                            <div class="p-1 pt-1.5 border-t border-slate-100 dark:border-slate-800">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/50 text-xs font-bold transition cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Đăng Xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>

            </header>

            <!-- FLASH ALERTS / THÔNG BÁO HỆ THỐNG -->
            <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto space-y-6">
                
                @if(session('success'))
                    <div class="p-4 rounded-3xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-xs font-bold flex items-center gap-3 shadow-sm animate-in fade-in slide-in-from-top-2">
                        <span class="w-7 h-7 rounded-2xl bg-emerald-100 dark:bg-emerald-900/80 text-emerald-600 dark:text-emerald-300 flex items-center justify-center text-sm font-black flex-shrink-0">✓</span>
                        <span class="flex-1">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-3xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs font-bold flex items-center gap-3 shadow-sm animate-in fade-in slide-in-from-top-2">
                        <span class="w-7 h-7 rounded-2xl bg-rose-100 dark:bg-rose-900/80 text-rose-600 dark:text-rose-300 flex items-center justify-center text-sm font-black flex-shrink-0">⚠️</span>
                        <span class="flex-1">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- NỘI DUNG CHÍNH CỦA TRANG ADMIN -->
                @yield('content')

            </main>

            <!-- FOOTER ADMIN BẢN QUYỀN -->
            <footer class="py-6 px-8 border-t border-slate-200/80 dark:border-slate-800/80 text-center text-xs text-slate-400 dark:text-slate-500 transition-colors duration-300">
                <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
                    <p>PhoneStore Control Center &copy; {{ date('Y') }} - Hệ thống quản lý bán hàng Flagship.</p>
                    <div class="flex items-center gap-4 text-[11px] font-semibold">
                        <span>Hệ thống: Trực tuyến</span>
                    </div>
                </div>
            </footer>

        </div>

    </div>

    <!-- JS ĐIỀU KHIỂN GIAO DIỆN ADMIN -->
    <script>
        // Bật/Tắt Sidebar trên Mobile
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        // Bật/Tắt Dropdown User Header
        function toggleAdminHeaderMenu() {
            const menu = document.getElementById('adminHeaderMenu');
            menu.classList.toggle('hidden');
        }

        // Đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const container = document.getElementById('adminHeaderDropdownContainer');
            const menu = document.getElementById('adminHeaderMenu');
            if (container && menu && !container.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // Bật/Tắt Dark/Light Theme cho Admin
        function toggleAdminTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('phonestore_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('phonestore_theme', 'dark');
            }
        }
    </script>

    <!-- ========================================================================= -->
    <!-- LAB 07: KHUNG CHAT QUẢN TRỊ VIÊN (ADMIN LIVECHAT CENTER)                  -->
    <!-- ========================================================================= -->
    <div id="admin-chat-box" class="fixed bottom-6 right-6 z-50 font-sans">
        
        <!-- Nút Mở Khung Chat Admin (id="chat-toggle") -->
        <button id="chat-toggle" type="button" 
                class="btn btn-dark shadow-2xl flex items-center gap-2.5 px-5 py-3.5 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white font-extrabold text-xs rounded-full border border-slate-700/80 dark:border-blue-500/50 shadow-xl shadow-slate-950/40 hover:scale-105 transition-all duration-300 cursor-pointer group">
            <span class="text-base group-hover:scale-110 transition-transform">💬</span>
            <span class="tracking-wide">Chat Khách Hàng</span>
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 badge-live-pulse"></span>
        </button>

        <!-- Popup Chat Quản Trị Đa Kênh (id="chat-popup") -->
        <div id="chat-popup" class="card shadow-2xl bg-white dark:bg-[#0c1322] border border-slate-200 dark:border-slate-800 rounded-3xl overflow-hidden w-[720px] max-w-[calc(100vw-32px)] h-[540px] max-h-[calc(100vh-100px)] flex flex-col transition-all duration-300 transform origin-bottom-right" 
             style="display: none; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);">
            
            <!-- 1. Header Khung Chat -->
            <div class="card-header bg-slate-900 dark:bg-[#070b14] px-5 py-4 text-white flex justify-between items-center border-b border-slate-800 flex-shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-2xl bg-blue-600/30 border border-blue-500/30 flex items-center justify-center text-lg font-black text-blue-400">
                        ⚡
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <strong class="font-extrabold text-sm text-white">Hỗ Trợ Trực Tuyến</strong>
                            <span class="text-[9px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded-full">Admin Center</span>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-0.5">Kênh tương tác trực tiếp 2 chiều với khách hàng</p>
                    </div>
                </div>

                <button id="chat-close" type="button" 
                        class="btn btn-sm btn-light w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center text-xs font-bold transition cursor-pointer" 
                        title="Đóng cửa sổ chat">
                    ✕
                </button>
            </div>

            <!-- 2. Nội dung 2 Cột (Trái: Danh sách Khách / Phải: Khung chat) -->
            <div class="flex-1 flex min-h-0 overflow-hidden">
                
                <!-- Cột Trái: Danh Sách Khách Hàng (id="user-list") -->
                <div class="w-60 sm:w-64 border-r border-slate-200 dark:border-slate-800 bg-slate-50/60 dark:bg-[#090e1a] flex flex-col flex-shrink-0">
                    <div class="p-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                        <span>Hội thoại gần đây</span>
                        <span class="text-[10px] text-blue-500 font-mono">Realtime</span>
                    </div>

                    <div id="user-list" class="flex-1 overflow-y-auto p-2 space-y-1">
                        <div class="p-4 text-center text-muted text-slate-400 dark:text-slate-500">
                            <small>Đang tải danh sách...</small>
                        </div>
                    </div>
                </div>

                <!-- Cột Phải: Lịch Sử Tin Nhắn & Ô Nhập Trả Lời -->
                <div class="flex-1 flex flex-col bg-white dark:bg-[#0c1322]">
                    
                    <!-- Khung Tin Nhắn (id="chat-messages") -->
                    <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 text-xs bg-slate-50/40 dark:bg-[#070b14]/60">
                        <div class="text-center mt-16 text-muted text-slate-400 dark:text-slate-500 space-y-2">
                            <div class="text-3xl">👥</div>
                            <p class="font-bold text-slate-700 dark:text-slate-300">Chọn một khách hàng để xem tin nhắn</p>
                            <p class="text-[11px]">Danh sách khách hàng đã nhắn tin hiển thị ở cột bên trái.</p>
                        </div>
                    </div>

                    <!-- Ô Nhập Liệu Trả Lời (card-footer) -->
                    <div class="card-footer bg-white dark:bg-[#0c1322] p-3 border-t border-slate-200 dark:border-slate-800 flex-shrink-0">
                        <div class="input-group flex items-center gap-2">
                            <input type="text" id="chat-input" 
                                   class="form-control form-control-sm flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:bg-white dark:focus:bg-slate-800 transition" 
                                   placeholder="Nhập câu trả lời..." 
                                   autocomplete="off">
                            <div class="input-group-append">
                                <button id="send-btn" type="button" 
                                        class="btn btn-success btn-sm px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-2xl shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center gap-1.5">
                                    <span>Gửi</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <style>
        /* Tùy chỉnh danh sách User & Tin nhắn Admin */
        .user-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        html.dark .user-item {
            color: #94a3b8;
        }
        .user-item:hover {
            background-color: rgba(37, 99, 235, 0.08);
            color: #2563eb;
        }
        html.dark .user-item:hover {
            background-color: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
        }
        .user-item.active {
            background-color: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .msg-row {
            margin-bottom: 0.5rem;
            padding: 0.6rem 0.85rem;
            border-radius: 1rem;
            word-break: break-word;
            line-height: 1.5;
            animation: chatFadeIn 0.2s ease-out;
        }
        .msg-row.admin-sent {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(29, 78, 216, 0.15));
            border-left: 3px solid #2563eb;
        }
        .msg-row.user-sent {
            background: #f1f5f9;
            border-left: 3px solid #64748b;
        }
        html.dark .msg-row.user-sent {
            background: #1e293b;
            border-left-color: #475569;
        }
    </style>

    <!-- LOGIC XỬ LÝ ADMIN LIVECHAT (LAB 07) -->
    <script>
        let currentUserId = null;
        const chatPopup = document.getElementById("chat-popup");
        const chatMessages = document.getElementById("chat-messages");
        const chatInput = document.getElementById("chat-input");
        const sendBtn = document.getElementById("send-btn");

        // Mở/Đóng popup
        const toggleBtn = document.getElementById("chat-toggle");
        if (toggleBtn) {
            toggleBtn.onclick = () => {
                chatPopup.style.display = "flex";
                loadUsers();
            };
        }
        const closeBtn = document.getElementById("chat-close");
        if (closeBtn) {
            closeBtn.onclick = () => {
                chatPopup.style.display = "none";
            };
        }

        // 1. Load danh sách User đã từng nhắn tin
        function loadUsers() {
            fetch("{{ route('admin.chat.users') }}", {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
                }
            })
            .then(res => res.json())
            .then(users => {
                let html = "";
                if (!users || users.length === 0) {
                    html = '<div class="p-4 text-center text-muted text-slate-400 dark:text-slate-500 text-xs">Chưa có hội thoại nào</div>';
                } else {
                    users.forEach(user => {
                        let activeClass = (currentUserId == user.id) ? 'active' : '';
                        let initial = user.name ? user.name.charAt(0).toUpperCase() : 'U';
                        let unreadBadge = (user.unread_count > 0) ? `<span class="px-1.5 py-0.5 rounded-full bg-rose-500 text-white font-black text-[9px] flex-shrink-0">${user.unread_count}</span>` : '';
                        let snippet = user.latest_message ? escapeAdminHtml(user.latest_message.content) : '';
                        html += `
                            <div class="user-item ${activeClass}" onclick="selectUser(${user.id}, this)">
                                <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-[10px] font-black flex-shrink-0">
                                    ${initial}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="truncate text-xs font-bold text-slate-800 dark:text-slate-200">${escapeAdminHtml(user.name)}</span>
                                        ${unreadBadge}
                                    </div>
                                    ${snippet ? `<p class="truncate text-[10px] text-slate-400 font-normal mt-0.5">${snippet}</p>` : ''}
                                </div>
                            </div>
                        `;
                    });
                }
                document.getElementById("user-list").innerHTML = html;
            })
            .catch(err => console.error("Lỗi tải danh sách khách hàng chat:", err));
        }

        // 2. Chọn User để chat
        function selectUser(userId, element) {
            currentUserId = userId;
            // Highlight user được chọn
            document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
            if (element) element.classList.add('active');
            loadMessages();
            setTimeout(() => { if (chatInput) chatInput.focus(); }, 150);
        }

        // 3. Load tin nhắn của User đang được chọn
        function loadMessages() {
            if (!currentUserId) return;
            fetch(`/admin/chat/messages/${currentUserId}`, {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
                }
            })
            .then(res => res.json())
            .then(data => {
                const messages = Array.isArray(data) ? data : (data.messages || []);
                let html = "";
                if (!messages || messages.length === 0) {
                    html = `
                        <div class="text-center mt-16 text-muted text-slate-400 dark:text-slate-500 space-y-2">
                            <p class="font-bold text-slate-700 dark:text-slate-300">Chưa có tin nhắn nào trong hội thoại này</p>
                            <p class="text-[11px]">Nhập câu trả lời bên dưới để bắt đầu gửi tin nhắn tới khách.</p>
                        </div>
                    `;
                } else {
                    const currentAdminId = "{{ Auth::id() }}";
                    messages.forEach(msg => {
                        let isMe = (msg.sender_id == currentAdminId || msg.sender_id != currentUserId);
                        let senderName = (msg.sender_id == currentAdminId) 
                            ? "Bạn (Admin)" 
                            : ((msg.sender_id != currentUserId) 
                                ? ("BQT (" + (msg.sender ? msg.sender.name : 'Admin') + ")") 
                                : (msg.sender ? msg.sender.name : 'Khách hàng'));
                        let color = isMe ? "#2563eb" : "currentColor";
                        let rowClass = isMe ? "admin-sent" : "user-sent";
                        let timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';

                        html += `
                            <div class="msg-row ${rowClass}">
                                <div class="flex items-center justify-between text-[10px] font-extrabold mb-1" style="color: ${color}">
                                    <span>${senderName}</span>
                                    <span class="text-slate-400 font-normal">${timeStr}</span>
                                </div>
                                <div class="text-slate-800 dark:text-slate-100 font-medium whitespace-pre-line">
                                    ${escapeAdminHtml(msg.content)}
                                </div>
                            </div>
                        `;
                    });
                }
                chatMessages.innerHTML = html;
                chatMessages.scrollTop = chatMessages.scrollHeight; // Cuộn xuống cuối
            })
            .catch(err => console.error("Lỗi tải tin nhắn:", err));
        }

        // 4. Gửi tin nhắn cho User
        function sendMessage() {
            let message = chatInput.value.trim();
            if (!message || !currentUserId) return;

            chatInput.disabled = true;
            if (sendBtn) sendBtn.disabled = true;

            fetch("{{ route('admin.chat.send') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ""
                },
                body: JSON.stringify({
                    message: message,
                    user_id: currentUserId
                })
            })
            .then(res => {
                if (!res.ok) throw new Error("Gửi tin phản hồi thất bại");
                return res.json();
            })
            .then(data => {
                chatInput.value = "";
                chatInput.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                chatInput.focus();
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin phản hồi:", err);
                chatInput.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
            });
        }

        if (sendBtn) sendBtn.onclick = sendMessage;
        if (chatInput) {
            chatInput.onkeypress = (e) => { 
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage(); 
                }
            };
        }

        // 5. Polling (Tự động cập nhật mỗi 3 giây)
        setInterval(() => {
            if (chatPopup && chatPopup.style.display !== "none") {
                if (currentUserId) {
                    loadMessages();
                }
                loadUsers(); // Cập nhật danh sách nếu có người mới nhắn
            }
        }, 3000);

        function escapeAdminHtml(text) {
            const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
            return (text || '').replace(/[&<>"']/g, m => map[m]);
        }
    </script>
    @stack('scripts')
</body>
</html>
