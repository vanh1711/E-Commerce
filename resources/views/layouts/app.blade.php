<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'PhoneStore') }} - Trải Nghiệm Công Nghệ Flagship</title>
    
    <!-- Favicon PhoneStore -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.svg') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}?v=2">
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
                        dark: {
                            bg: '#0a0d14',
                            surface: '#111726',
                            card: '#161f33',
                            border: '#1e293b',
                            text: '#f8fafc',
                            muted: '#94a3b8'
                        }
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                        'glass-dark': '0 8px 32px 0 rgba(0, 0, 0, 0.45)',
                        'card-hover': '0 20px 35px -10px rgba(0, 0, 0, 0.08)',
                        'glow-blue': '0 0 25px -5px rgba(59, 130, 246, 0.5)',
                    }
                }
            }
        }
    </script>
    <!-- Script chạy ngay lập tức để đồng bộ Dark/Light Mode không bị chớp trắng -->
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
        :root {
            --app-bg: #f8fafc;
            --app-surface: #ffffff;
            --app-card: #ffffff;
            --app-text: #0f172a;
            --app-muted: #64748b;
            --app-border: #e2e8f0;
            --app-glass-bg: rgba(255, 255, 255, 0.85);
            --app-glass-border: rgba(226, 232, 240, 0.8);
        }

        html.dark {
            --app-bg: #07090e;
            --app-surface: #0d121f;
            --app-card: #131b2e;
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            --app-border: #1e293b;
            --app-glass-bg: rgba(13, 18, 31, 0.85);
            --app-glass-border: rgba(30, 41, 59, 0.8);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--app-bg);
            color: var(--app-text);
            transition: background-color 0.3s cubic-bezier(0.16, 1, 0.3, 1), color 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .glass-nav {
            background: var(--app-glass-bg);
            border-color: var(--app-glass-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transition: background 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .badge-pulse {
            animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.9; }
        }

        /* Triệt tiêu hoàn toàn lỗi Chrome Autofill nhảy nền sáng màu trong Dark Mode */
        html.dark input:-webkit-autofill,
        html.dark input:-webkit-autofill:hover, 
        html.dark input:-webkit-autofill:focus, 
        html.dark input:-webkit-autofill:active,
        html.dark textarea:-webkit-autofill,
        html.dark textarea:-webkit-autofill:hover,
        html.dark textarea:-webkit-autofill:focus,
        html.dark select:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 1000px #1e293b inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            caret-color: #f8fafc !important;
            border-color: #334155 !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        html:not(.dark) input:-webkit-autofill,
        html:not(.dark) input:-webkit-autofill:hover, 
        html:not(.dark) input:-webkit-autofill:focus, 
        html:not(.dark) input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #f8fafc inset !important;
            -webkit-text-fill-color: #0f172a !important;
            caret-color: #0f172a !important;
            transition: background-color 5000s ease-in-out 0s;
        }
    </style>
    @stack('head')
</head>
<body class="bg-[var(--app-bg)] text-[var(--app-text)] antialiased selection:bg-blue-600 selection:text-white flex flex-col min-h-screen">


    <!-- 1. STICKY GLASS NAVBAR -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-nav border-b border-slate-200/70 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 text-white flex items-center justify-center font-black text-base shadow-md shadow-blue-500/25 group-hover:scale-105 transition-transform duration-300">
                    P
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white uppercase">
                    PHONE<span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">STORE</span>
                </span>
            </a>

            <!-- Search Bar Quick Access & Samsung Style Live Dropdown (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-md mx-4 relative" id="appSearchContainer">
                <div class="w-full relative">
                    <input type="text" id="appNavbarSearchInput" 
                           oninput="performAppSamsungSearch(this.value)"
                           onfocus="showAppSearchDropdown()"
                           placeholder="Tìm kiếm iPhone 16, Galaxy S24, Xiaomi..." 
                           class="w-full pl-10 pr-9 py-2 bg-slate-100/80 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-800 focus:bg-white dark:focus:bg-slate-800 border border-transparent focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 rounded-full text-xs font-semibold text-slate-800 dark:text-slate-100 transition-all duration-200 placeholder-slate-400 dark:placeholder-slate-500 outline-none">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 dark:text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <button type="button" onclick="closeAppSearchDropdown()" id="appClearSearchBtn" 
                            class="hidden absolute right-3 top-2.5 w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-500 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold">
                        ✕
                    </button>
                </div>

                <!-- POPUP PREVIEW DROPDOWN (SAMSUNG STYLE) -->
                <div id="appSearchResultsBox" 
                     class="hidden absolute top-full -left-20 -right-20 md:-left-32 md:-right-32 mt-2 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 p-6 z-50 animate-in fade-in slide-in-from-top-2 duration-200">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800 text-xs">
                        <span class="font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider text-[10px]" id="appSearchHeaderLabel">
                            Gợi ý tìm kiếm nhanh
                        </span>
                        <button type="button" onclick="closeAppSearchDropdown()" class="text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold flex items-center gap-1">
                            <span>Đóng</span> ✕
                        </button>
                    </div>

                    <div id="appSearchLoading" class="hidden py-6 text-center text-xs text-slate-400">
                        <div class="inline-block w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                        <p>Đang tìm sản phẩm...</p>
                    </div>

                    <div id="appSearchGrid" class="grid grid-cols-1 md:grid-cols-12 gap-5">
                        <div class="md:col-span-4 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800 pr-0 md:pr-3">
                            <h4 class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider mb-2.5">TỪ KHÓA ĐỀ XUẤT</h4>
                            <div class="space-y-1.5" id="appSearchKeywords">
                                <button type="button" onclick="quickFillAppSearch('iPhone 16 Pro Max')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-1.5 rounded-xl transition flex items-center gap-1.5">
                                    <span>🔍</span> <span class="truncate">iPhone 16 Pro Max</span>
                                </button>
                                <button type="button" onclick="quickFillAppSearch('Galaxy S24 Ultra')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-1.5 rounded-xl transition flex items-center gap-1.5">
                                    <span>🔍</span> <span class="truncate">Galaxy S24 Ultra</span>
                                </button>
                            </div>
                        </div>

                        <div class="md:col-span-8">
                            <div class="flex justify-between items-center mb-2.5">
                                <h4 class="text-[10px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">SẢN PHẨM LIÊN QUAN</h4>
                                <a href="{{ route('home') }}#all-products" id="appViewAllLink" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    Xem tất cả
                                </a>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="appSearchCards">
                                <!-- Render JS -->
                            </div>

                            <div id="appSearchEmpty" class="hidden py-6 text-center text-xs text-slate-400">
                                <p class="text-2xl mb-1">🔍</p>
                                <p class="font-bold text-slate-700 dark:text-slate-200">Không tìm thấy sản phẩm</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Navigation Links & User Actions -->
            <nav class="flex items-center space-x-2 sm:space-x-4 text-xs font-bold">
                
                <a href="{{ route('home') }}" class="hidden sm:inline-flex px-3.5 py-2 rounded-full {{ request()->routeIs('home') ? 'bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800' }} transition">
                    Trang Chủ
                </a>



                @if(Auth::check() && (Auth::user()->is_admin == 1 || Auth::user()->role === 'admin' || Auth::user()->email === 'admin@gmail.com' || Auth::user()->email === 'admin@example.com'))
                    <a href="{{ route('dashboard') }}" class="hidden lg:inline-flex items-center gap-1 px-3.5 py-2 rounded-full bg-slate-900 dark:bg-blue-600 text-white hover:bg-blue-600 dark:hover:bg-blue-700 shadow-sm transition">
                        <span>📊</span> Admin Dashboard
                    </a>
                @endif

                {{-- Nút Chuyển Đổi Dark / Light Mode Chuẩn Apple --}}
                <button type="button" onclick="togglePhoneStoreTheme()" 
                        class="theme-toggle-btn relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-100/80 dark:bg-slate-800 text-slate-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-700 hover:text-amber-500 transition shadow-sm cursor-pointer" 
                        title="Chuyển chế độ Sáng / Tối (Dark Mode)" aria-label="Đổi giao diện Sáng / Tối">
                    <span class="dark:hidden text-base">🌙</span>
                    <span class="hidden dark:inline-block text-base">☀️</span>
                </button>

                {{-- Cart Button --}}
                <a href="{{ route('cart.index') }}" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-100/80 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition text-slate-700 dark:text-slate-200 shadow-sm" title="Giỏ hàng">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @php $cartCount = count(session()->get('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-r from-red-500 to-rose-600 text-white text-[10px] font-black rounded-full flex items-center justify-center shadow-md badge-pulse">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- Auth State --}}
                @auth
                    <div class="relative" id="appUserMenuDropdown">
                        <button onclick="toggleAppUserMenu()" type="button" 
                                class="flex items-center gap-2 pl-2 pr-3 py-1.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200/80 dark:border-slate-700 rounded-full shadow-sm transition">
                            <span class="w-7 h-7 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center text-[11px] font-black uppercase shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span class="hidden md:inline-block font-bold text-slate-800 dark:text-slate-200 max-w-[100px] truncate">{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div id="appUserMenuContent" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150">
                            <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-[10px] uppercase font-bold text-slate-400">Tài khoản hiện tại</p>
                                <p class="text-xs font-extrabold text-slate-900 dark:text-white truncate mt-0.5">{{ auth()->user()->name }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-2 px-2.5 py-0.5 text-[10px] font-bold rounded-full {{ (auth()->user()->is_admin == 1 || auth()->user()->role === 'admin') ? 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900' : 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900' }}">
                                    {{ (auth()->user()->is_admin == 1 || auth()->user()->role === 'admin') ? '⚡ Quản Trị Viên' : '👤 Khách Hàng Thân Thiết' }}
                                </span>
                            </div>

                            @if(auth()->user()->is_admin == 1 || auth()->user()->role === 'admin' || auth()->user()->email === 'admin@gmail.com' || auth()->user()->email === 'admin@example.com')
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                    <span>📊</span> Bảng Quản Trị (Admin)
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    <span>🚚</span> Quản Lý Đơn & Ship Hàng
                                </a>
                                <a href="{{ route('products.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    <span>📱</span> Quản Lý Sản Phẩm
                                </a>
                                <a href="{{ route('categories.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    <span>📁</span> Quản Lý Danh Mục
                                </a>
                            @else
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                    <span>📦</span> Lịch Sử Đơn Hàng
                                </a>
                            @endif

                            <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-slate-100 dark:border-slate-800">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-slate-800 transition">
                                    <span>🚪</span> Đăng Xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Đăng Nhập</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-full shadow-md shadow-blue-500/20 transition">
                        Đăng Ký
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- 2. MAIN CONTENT -->
    <main class="flex-grow pt-24 pb-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <!-- Toast Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-sm backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs font-black">✓</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-sm backdrop-blur-sm">
                <div class="flex items-center gap-3">
                    <span class="w-7 h-7 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs font-black">!</span>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- 3. PREMIUM MODERN FOOTER -->
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
                <div class="space-y-3">
                    <span class="text-xl font-black text-white uppercase tracking-tight">PHONE<span class="text-blue-500">STORE</span></span>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Hệ thống bán lẻ thiết bị công nghệ chính hãng hàng đầu. Cam kết chất lượng, bảo hành 1 đổi 1 và dịch vụ tận tâm.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Sản Phẩm</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">iPhone 16 Series</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Samsung Galaxy S24</a></li>
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Phụ kiện công nghệ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Chính Sách</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:text-white transition">Chính sách bảo hành 12 tháng</a></li>
                        <li><a href="#" class="hover:text-white transition">Giao hàng hỏa tốc 2 giờ</a></li>
                        <li><a href="#" class="hover:text-white transition">Trả góp 0% lãi suất</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Tổng Đài Hỗ Trợ</h4>
                    <p class="text-lg font-black text-white">1800 6868 <span class="text-xs font-normal text-emerald-400">(Miễn phí)</span></p>
                    <p class="text-xs text-slate-500 mt-1">Phục vụ từ 8h00 - 21h30 hàng ngày</p>
                </div>
            </div>
            <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>© 2026 PhoneStore Co. All rights reserved. Designed for Excellence.</p>
                <div class="flex gap-4">
                    <a href="{{ route('home') }}" class="hover:text-slate-300 transition">Trang Chủ</a>
                    <a href="{{ route('home') }}#all-products" class="hover:text-slate-300 transition">Điện Thoại</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Dropdown JS & Samsung Style Live Search Engine -->
    <script>
        function toggleAppUserMenu() {
            const menu = document.getElementById('appUserMenuContent');
            if (menu) menu.classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('appUserMenuDropdown');
            const menu = document.getElementById('appUserMenuContent');
            if (dropdown && menu && !dropdown.contains(e.target)) {
                menu.classList.add('hidden');
            }

            const searchContainer = document.getElementById('appSearchContainer');
            if (searchContainer && !searchContainer.contains(e.target)) {
                closeAppSearchDropdown();
            }
        });

        // App Live Search
        let appSearchTimer = null;
        function showAppSearchDropdown() {
            const box = document.getElementById('appSearchResultsBox');
            if (box) box.classList.remove('hidden');
            const val = document.getElementById('appNavbarSearchInput')?.value || '';
            performAppSamsungSearch(val);
        }

        function closeAppSearchDropdown() {
            const box = document.getElementById('appSearchResultsBox');
            if (box) box.classList.add('hidden');
        }

        function quickFillAppSearch(keyword) {
            const input = document.getElementById('appNavbarSearchInput');
            if (input) {
                input.value = keyword;
                input.focus();
                performAppSamsungSearch(keyword);
            }
        }

        function performAppSamsungSearch(query) {
            const trimmed = query.trim();
            const clearBtn = document.getElementById('appClearSearchBtn');
            const loading = document.getElementById('appSearchLoading');
            const cards = document.getElementById('appSearchCards');
            const empty = document.getElementById('appSearchEmpty');
            const viewAll = document.getElementById('appViewAllLink');
            const header = document.getElementById('appSearchHeaderLabel');

            if (clearBtn) {
                if (trimmed.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            if (viewAll) viewAll.href = `/?search=${encodeURIComponent(trimmed)}`;
            if (header) header.innerText = trimmed.length > 0 ? `Kết quả cho "${trimmed}"` : 'Gợi ý tìm kiếm';

            if (trimmed.length === 0) {
                if (cards) cards.innerHTML = '';
                if (empty) empty.classList.add('hidden');
                return;
            }

            if (loading) loading.classList.remove('hidden');

            clearTimeout(appSearchTimer);
            appSearchTimer = setTimeout(async () => {
                try {
                    const res = await fetch(`/api/products/search?q=${encodeURIComponent(trimmed)}`);
                    const data = await res.json();
                    if (loading) loading.classList.add('hidden');

                    if (data.products && data.products.length > 0) {
                        if (empty) empty.classList.add('hidden');
                        if (cards) {
                            cards.classList.remove('hidden');
                            cards.innerHTML = data.products.slice(0, 3).map(p => `
                                <a href="${p.url}" class="group block bg-slate-50 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 rounded-2xl p-2.5 border border-slate-100 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-500 hover:shadow-md transition">
                                    <div class="h-20 w-full bg-white dark:bg-slate-900 rounded-xl p-1.5 flex items-center justify-center overflow-hidden mb-1.5">
                                        <img src="${p.image}" alt="${p.name}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition">
                                    </div>
                                    <h5 class="font-bold text-slate-800 dark:text-slate-200 text-[11px] leading-tight line-clamp-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        ${p.name}
                                    </h5>
                                    <p class="text-rose-600 dark:text-rose-400 font-extrabold text-[11px] mt-1">
                                        ${p.price}
                                    </p>
                                </a>
                            `).join('');
                        }
                    } else {
                        if (cards) cards.innerHTML = '';
                        if (empty) empty.classList.remove('hidden');
                    }
                } catch (e) {
                    if (loading) loading.classList.add('hidden');
                }
            }, 250);
        }

        // Toggle Dark / Light Mode chuẩn Apple
        function togglePhoneStoreTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('phonestore_theme', isDark ? 'dark' : 'light');
            
            // Dispatch event để các component khác nếu cần phản hồi
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark } }));
        }
    </script>

    <!-- Lab 07 Livechat Khách Hàng -->
    @include('partials.user-chat')

    @stack('scripts')
</body>
</html>