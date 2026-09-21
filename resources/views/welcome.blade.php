<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VanhPhone - Trải Nghiệm Công Nghệ Flagship Đỉnh Cao</title>
    
    <!-- Favicon VanhPhone -->
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
                            bg: '#07090e',
                            surface: '#0d121f',
                            card: '#131b2e',
                            border: '#1e293b',
                            text: '#f8fafc',
                            muted: '#94a3b8'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Script chạy tức thì đồng bộ Dark Mode -->
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
    <!-- Swiper.js CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <style>
        :root {
            --app-bg: #f8fafc;
            --app-surface: #ffffff;
            --app-card: #ffffff;
            --app-text: #0f172a;
            --app-muted: #64748b;
            --app-border: #e2e8f0;
            --app-glass-bg: rgba(255, 255, 255, 0.88);
            --app-glass-border: rgba(226, 232, 240, 0.8);
        }

        html.dark {
            --app-bg: #07090e;
            --app-surface: #0d121f;
            --app-card: #131b2e;
            --app-text: #f8fafc;
            --app-muted: #94a3b8;
            --app-border: #1e293b;
            --app-glass-bg: rgba(13, 18, 31, 0.88);
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

        .text-glow {
            text-shadow: 0 0 35px rgba(37, 99, 235, 0.5);
        }

        /* Triệt tiêu hoàn toàn lỗi Chrome Autofill nhảy nền sáng màu trong Dark Mode */
        html.dark input,
        html.dark textarea,
        html.dark select {
            color-scheme: dark !important;
        }

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
        
        /* 3D Tilt & Parallax Physics */
        .perspective-container {
            perspective: 1000px;
        }
        .tilt-card {
            transform-style: preserve-3d;
            transition: transform 0.15s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease-out;
            will-change: transform;
        }
        .parallax-hero-img {
            will-change: transform;
            transition: transform 0.1s linear;
        }
        .parallax-glow {
            will-change: transform;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        /* Scroll Reveal Transitions */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .reveal-on-scroll.revealed {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .tilt-card, .parallax-hero-img, .parallax-glow, .reveal-on-scroll {
                transition: none !important;
                transform: none !important;
                opacity: 1 !important;
            }
        }
    </style>
</head>
<body class="bg-[var(--app-bg)] text-[var(--app-text)] antialiased selection:bg-blue-600 selection:text-white transition-colors duration-300">


    <!-- 1. STICKY ULTRA GLASSMORPHISM NAVBAR -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-nav border-b border-slate-200/80 dark:border-slate-800 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group flex-shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 flex items-center justify-center text-white text-base font-black shadow-md shadow-blue-500/30 group-hover:scale-105 transition-transform">
                    V
                </div>
                <span class="text-xl font-black tracking-tighter text-slate-950 dark:text-white uppercase">VANH<span class="text-blue-600 dark:text-blue-400">PHONE</span></span>
            </a>

            <!-- Category Menu (FPT Shop Style) & Search Bar & Quick Links -->
            <div class="flex items-center space-x-4 flex-1 max-w-2xl mx-4">
                <!-- Nút Danh mục -->
                <div class="relative group flex-shrink-0">
                    <button class="flex items-center gap-2 bg-gradient-to-r from-rose-600 to-red-600 text-white px-4 py-2 rounded-xl font-black text-xs hover:from-rose-500 hover:to-red-500 shadow-md shadow-rose-600/20 transition hover:scale-105">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        Danh mục
                    </button>

                    <!-- Mega Menu Dropdown -->
                    <div class="absolute top-full left-0 mt-2 w-[800px] bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-slate-800 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 flex overflow-hidden">
                        <!-- Menu Trái (Các hãng từ Admin) -->
                        <div class="w-1/4 bg-gray-50 dark:bg-slate-800/60 border-r border-gray-100 dark:border-slate-800 py-2 rounded-l-2xl max-h-[400px] overflow-y-auto">
                            @foreach($categories as $cat)
                                <a href="#all-products" onclick="filterCategory('cat-{{ $cat->id }}', document.querySelector('#btn-cat-{{ $cat->id }}') || this)" class="flex items-center justify-between px-4 py-3 hover:bg-white dark:hover:bg-slate-800 hover:text-red-600 dark:hover:text-red-400 font-medium text-gray-700 dark:text-slate-300 transition group/item">
                                    <span class="flex items-center gap-2">
                                        <span class="w-4 h-4 bg-gray-200 dark:bg-slate-700 rounded-full flex items-center justify-center text-[10px]">📱</span>
                                        {{ $cat->name }}
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 group-hover/item:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            @endforeach
                        </div>
                        <!-- Nội dung Menu Phải (Mở rộng theo danh mục) -->
                        <div class="w-3/4 p-6 bg-white dark:bg-slate-900">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-100 dark:border-slate-800 pb-2">Danh mục nổi bật</h4>
                            <div class="grid grid-cols-3 gap-6">
                                @foreach($categories->take(6) as $cat)
                                    <div>
                                        <h5 class="font-bold text-gray-800 dark:text-slate-200 mb-2">{{ $cat->name }}</h5>
                                        <ul class="space-y-2 text-sm text-gray-600 dark:text-slate-400">
                                            <li>
                                                <a href="#all-products" onclick="filterCategory('cat-{{ $cat->id }}', document.querySelector('#btn-cat-{{ $cat->id }}') || this)" class="hover:text-red-600 dark:hover:text-red-400 transition">
                                                    Xem tất cả {{ $cat->name }} ({{ $cat->products_count }})
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thanh tìm kiếm Header Trang Chủ (Instant Live Search Dropdown chuẩn Samsung) -->
                <div class="flex-1 relative" id="searchDropdownContainer">
                    <div class="relative">
                        <input type="text" id="navbarSearchInput" 
                               oninput="performSamsungLiveSearch(this.value)"
                               onfocus="showSearchDropdown()"
                               placeholder="Tìm kiếm iPhone 16, Galaxy S24, Xiaomi..." 
                               class="w-full pl-9 pr-8 py-2 bg-gray-100/90 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-transparent focus:border-blue-500 dark:focus:border-blue-400 focus:ring-4 focus:ring-blue-500/10 rounded-full text-xs font-semibold text-gray-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 transition outline-none">
                        <span class="absolute left-3 top-2.5 text-gray-400 dark:text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <button type="button" onclick="closeAndClearLiveSearch()" id="clearNavSearchBtn" 
                                class="hidden absolute right-2.5 top-2 w-4 h-4 rounded-full bg-gray-200 dark:bg-slate-700 hover:bg-gray-300 dark:hover:bg-slate-600 text-gray-500 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold">
                            ✕
                        </button>
                    </div>


                    <!-- DROPDOWN GỢI Ý & SẢN PHẨM LIÊN QUAN (CHUẨN SAMSUNG EXPERIENCE STORE) -->
                    <div id="liveSearchResultsBox" 
                         style="display: none; width: min(860px, 92vw); left: 50%; transform: translateX(-50%);"
                         class="absolute top-full mt-3 bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 p-6 z-[9999]">
                        
                        <!-- Header bên trong Dropdown -->
                        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 dark:border-slate-800 text-xs">
                            <span class="font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-[11px]" id="searchHeaderLabel">
                                Gợi ý tìm kiếm nhanh
                            </span>
                            <button type="button" onclick="closeLiveSearchDropdown()" aria-label="Đóng kết quả tìm kiếm" class="text-slate-400 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 font-bold flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                <span>Đóng (Esc)</span> ✕
                            </button>
                        </div>

                        <!-- Trạng thái Loading Skeleton -->
                        <div id="searchLoadingState" class="hidden py-8 text-center text-xs text-slate-400">
                            <div class="inline-block w-5 h-5 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                            <p class="font-medium">Đang tìm kiếm sản phẩm phù hợp...</p>
                        </div>

                        <!-- Khung kết quả hiển thị 2 cột (Từ khóa đề xuất & Sản phẩm liên quan) -->
                        <div id="searchContentGrid" class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            
                            <!-- Cột Trái: TÌM KIẾM ĐƯỢC ĐỀ XUẤT (4 cột) -->
                            <div class="md:col-span-4 border-b md:border-b-0 md:border-r border-slate-100 dark:border-slate-800 pr-0 md:pr-4">
                                <h4 class="text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider mb-3">TỪ KHÓA ĐỀ XUẤT</h4>
                                <div class="space-y-2" id="searchKeywordSuggestions">
                                    <button type="button" onclick="quickFillSearch('iPhone 16 Pro Max')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                        <span class="text-blue-600 dark:text-blue-400">🔍</span> <span>iPhone 16 Pro Max</span>
                                    </button>
                                    <button type="button" onclick="quickFillSearch('Galaxy S24 Ultra')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                        <span class="text-blue-600 dark:text-blue-400">🔍</span> <span>Galaxy S24 Ultra</span>
                                    </button>
                                    <button type="button" onclick="quickFillSearch('Xiaomi')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                        <span class="text-blue-600 dark:text-blue-400">🔍</span> <span>Xiaomi Flagship</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Cột Phải: SẢN PHẨM LIÊN QUAN (8 cột - Lưới 4 card ảnh như ảnh mẫu Samsung) -->
                            <div class="md:col-span-8">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-[11px] font-bold uppercase text-slate-400 dark:text-slate-500 tracking-wider">SẢN PHẨM LIÊN QUAN</h4>
                                    <a href="{{ route('products.index') }}" id="viewAllSearchLink" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                        Xem tất cả
                                    </a>
                                </div>

                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5" id="searchProductCardsGrid">
                                    <!-- Render động bằng JavaScript -->
                                </div>

                                <!-- Khi không có kết quả -->
                                <div id="searchEmptyState" class="hidden py-8 text-center text-xs text-slate-400">
                                    <p class="text-3xl mb-2">🔍</p>
                                    <p class="font-bold text-slate-700 dark:text-slate-200">Không tìm thấy sản phẩm nào phù hợp</p>
                                    <p class="text-slate-400 mt-1">Vui lòng thử lại với từ khóa khác (ví dụ: iPhone, Samsung...)</p>
                                </div>
                            </div>

                        </div>


                    </div>
                </div>

                <nav class="hidden lg:flex items-center space-x-5 text-xs font-bold tracking-wide flex-shrink-0">
                    <a href="{{ route('products.index') }}" class="text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Sản Phẩm</a>
                    <a href="#all-products" class="text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 transition">Điện Thoại</a>
                </nav>
            </div>



            <!-- Auth Buttons & Dropdown Menu -->
            <div class="flex items-center gap-3">
                {{-- Nút Chuyển Đổi Dark / Light Mode Chuẩn Apple --}}
                <button type="button" onclick="togglePhoneStoreTheme()" 
                        class="theme-toggle-btn relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 hover:bg-amber-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-amber-400 hover:text-amber-500 transition shadow-sm cursor-pointer" 
                        title="Chuyển chế độ Sáng / Tối (Dark Mode)" aria-label="Đổi giao diện Sáng / Tối">
                    <span class="dark:hidden text-base">🌙</span>
                    <span class="hidden dark:inline-block text-base">☀️</span>
                </button>

                @auth
                    {{-- Icon giỏ hàng --}}
                    <a href="{{ route('cart.index') }}" aria-label="Xem giỏ hàng" class="relative flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 transition text-slate-700 dark:text-slate-200" title="Giỏ hàng">

                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        @php $cartCount = count(session()->get('cart', [])); @endphp
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-rose-600 text-white text-[11px] font-black rounded-full flex items-center justify-center px-1">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    <!-- Dropdown Container -->
                    <div class="relative" id="userMenuDropdown">
                        <button onclick="toggleUserMenu()" type="button" aria-label="Menu tài khoản cá nhân"
                                class="flex items-center gap-2 px-3.5 py-2 text-xs font-bold bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 rounded-full shadow-sm transition duration-200 cursor-pointer">
                            <span class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[11px] font-black">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                            <span>{{ auth()->user()->name }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Menu Xổ Xuống (Dropdown Content) -->
                        <div id="userMenuContent" class="hidden absolute right-0 mt-2 w-52 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-100 dark:border-slate-800 py-2 z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100 dark:border-slate-800">
                                <p class="text-[11px] text-slate-400 font-medium">Đang đăng nhập:</p>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-1 px-2.5 py-0.5 text-[11px] font-black uppercase tracking-wider rounded-full {{ (auth()->user()->is_admin == 1 || auth()->user()->role === 'admin' || auth()->user()->email === 'admin@gmail.com') ? 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900' : 'bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-900' }}">
                                    {{ (auth()->user()->is_admin == 1 || auth()->user()->role === 'admin' || auth()->user()->email === 'admin@gmail.com') ? '⚡ Quản Trị Viên' : '👤 Khách Hàng' }}
                                </span>
                            </div>

                            <!-- Nếu là Admin thì hiện nút sang Dashboard & Quản Lý Đơn -->
                            @if(auth()->user()->is_admin == 1 || auth()->user()->role === 'admin' || auth()->user()->email === 'admin@gmail.com')
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                    <span>📊</span> Bảng Quản Trị
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                    <span>🚚</span> Quản Lý Đơn & Ship
                                </a>
                            @else
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-slate-800 transition">
                                    <span>📦</span> Lịch Sử Đơn Hàng
                                </a>
                            @endif

                            <!-- Nút Đăng Xuất Hoạt Động 100% -->
                            <form action="{{ route('logout') }}" method="POST" class="mt-1 border-t border-slate-50 dark:border-slate-800">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-slate-800 transition cursor-pointer">
                                    <span>🚪</span> Đăng Xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 transition">Đăng Nhập</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white rounded-full shadow-md shadow-blue-500/20 transition">
                        Đăng Ký
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- 2. HERO SHOWCASE 3D BANNER (FLAGSHIP GALAXY & IPHONE EXPERIENCE) -->
    <section class="pt-20 pb-12 bg-gradient-to-b from-[#06080e] via-[#090d16] to-[#0f172a] text-white overflow-hidden relative">

        <!-- Background Cyber Ambient Glow & Mesh -->
        <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[400px] bg-indigo-600/20 rounded-full blur-[160px] pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-20 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">
                    
                    <!-- Slide 1: Galaxy Flagship S24 Ultra -->
                    <div class="swiper-slide py-12 lg:py-16">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                            
                            <!-- Cột Trái: Thông điệp & CTA -->
                            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/15 border border-blue-400/30 text-blue-400 text-xs font-black uppercase tracking-widest backdrop-blur-xl shadow-inner">
                                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                                    ✨ Kỷ Nguyên Trí Tuệ Nhân Tạo Mới
                                </div>
                                
                                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-[1.05] text-white">
                                    Galaxy S24 Ultra & iPhone 16 Pro
                                </h1>
                                
                                <p class="text-slate-300 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                                    Khung viền Titanium chuẩn hàng không, công nghệ nhiếp ảnh zoom quang 100x và hiệu năng bùng nổ từ chip 3nm thế hệ mới.
                                </p>


                                <!-- Badge điểm nhấn -->
                                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 text-xs font-bold text-slate-300">
                                    <span class="px-3.5 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md flex items-center gap-1.5">
                                        ⚡ Trả góp 0% - 0đ phụ phí
                                    </span>
                                    <span class="px-3.5 py-1.5 rounded-xl bg-white/5 border border-white/10 backdrop-blur-md flex items-center gap-1.5">
                                        🛡️ Bảo hành 1 Đổi 1 trong 30 ngày
                                    </span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                                    <a href="#all-products" class="group relative px-9 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-sm rounded-full shadow-2xl shadow-blue-500/40 hover:shadow-blue-500/60 hover:scale-105 transition-all duration-300 flex items-center gap-2 overflow-hidden">
                                        <span>Khám Phá Giá Ưu Đãi</span>
                                    </a>
                                    <a href="#bento" class="px-8 py-4 bg-white/10 hover:bg-white/15 border border-white/20 text-white font-bold text-sm rounded-full backdrop-blur-md hover:-translate-y-0.5 transition-all duration-300">
                                        Xem Chi Tiết Công Nghệ
                                    </a>
                                </div>
                            </div>

                            <!-- Cột Phải: Ảnh Thiết Bị Nổi Khối 3D Siêu Đẹp (Parallax Glow) -->
                            <div class="lg:col-span-5 flex justify-center relative">
                                <div class="relative w-full max-w-[420px] aspect-square flex items-center justify-center">
                                    <!-- Vòng tròn hào quang động có Parallax -->
                                    <div class="parallax-glow w-80 h-80 bg-gradient-to-tr from-blue-600/40 via-indigo-600/30 to-purple-600/20 rounded-full blur-3xl absolute pointer-events-none"></div>
                                    <div class="absolute inset-0 rounded-full border border-blue-500/20 animate-spin" style="animation-duration: 20s;"></div>
                                    
                                    <img src="https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?auto=format&fit=crop&w=800&q=80" 
                                         class="parallax-hero-img relative z-10 max-h-[380px] sm:max-h-[440px] object-contain drop-shadow-[0_20px_50px_rgba(37,99,235,0.45)] hover:scale-105 transition-transform duration-700" 
                                         alt="Galaxy Flagship 3D">
                                </div>
                            </div>


                        </div>
                    </div>

                    <!-- Slide 2: Titanium iPhone 16 -->
                    <div class="swiper-slide py-12 lg:py-16">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-400 text-xs font-black uppercase tracking-widest backdrop-blur-xl">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                    🚀 Đỉnh Cao Chế Tác Titanium
                                </div>
                                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight leading-[1.05] text-white">
                                    Kiệt Tác Thiết Kế. Sức Mạnh Tuyệt Đối.
                                </h1>

                                <p class="text-slate-300 text-base sm:text-lg max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                                    Nút điều khiển Camera Control hoàn toàn mới, viền màn hình mỏng nhất lịch sử và thời lượng pin dẫn đầu phân khúc.
                                </p>
                                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
                                    <a href="#all-products" class="px-9 py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black text-sm rounded-full shadow-2xl shadow-emerald-500/40 hover:scale-105 transition-all duration-300">
                                        Mua Ngay & Nhận Quà 2 Triệu
                                    </a>
                                </div>
                            </div>
                            <div class="lg:col-span-5 flex justify-center relative">
                                <div class="w-80 h-80 bg-gradient-to-tr from-emerald-600/30 to-teal-500/20 rounded-full blur-3xl absolute pointer-events-none"></div>
                                <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=800&q=80" 
                                     class="relative z-10 max-h-[380px] sm:max-h-[440px] object-contain drop-shadow-[0_20px_50px_rgba(16,185,129,0.35)] hover:scale-105 transition-transform duration-700" 
                                     alt="Titanium Flagship Phone">
                            </div>
                        </div>
                    </div>

                </div>
                <!-- Swiper Controls -->
                <div class="swiper-pagination !bottom-2"></div>
            </div>

            <!-- THANH CAM KẾT CHUẨN FLAGSHIP (QUICK PROMISE BAR) -->
            <div class="mt-8 pt-8 border-t border-slate-800/80 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/[0.08] backdrop-blur-md">
                    <span class="text-2xl">⚡</span>
                    <div>
                        <h4 class="text-xs font-extrabold text-white">Giao Nhanh 2 Giờ</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Miễn phí toàn quốc</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/[0.08] backdrop-blur-md">
                    <span class="text-2xl">🛡️</span>
                    <div>
                        <h4 class="text-xs font-extrabold text-white">Bảo Hành 1 Đổi 1</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Trong 30 ngày đầu</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/[0.08] backdrop-blur-md">
                    <span class="text-2xl">🔄</span>
                    <div>
                        <h4 class="text-xs font-extrabold text-white">Thu Cũ Giá Cao</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Trợ giá đến 5 triệu</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-3.5 rounded-2xl bg-white/[0.04] border border-white/[0.08] backdrop-blur-md">
                    <span class="text-2xl">💳</span>
                    <div>
                        <h4 class="text-xs font-extrabold text-white">Trả Góp 0%</h4>
                        <p class="text-[11px] text-slate-400 font-medium">Kỳ hạn đến 12 tháng</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 3. DANH MỤC & BỘ LỌC THÔNG MINH DẠNG TRỰC QUAN (NEEDS-BASED SMART FILTER) -->
    <section id="all-products" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-transparent">
        
        <div class="text-center max-w-3xl mx-auto mb-10">
            <span class="text-xs font-black uppercase tracking-widest text-blue-600 bg-blue-50 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-100 dark:border-blue-800 px-4 py-1.5 rounded-full inline-block mb-3">
                Bộ Sưu Tập Smartphone
            </span>
            <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
                Khám Phá Dòng Sản Phẩm Mới Nhất
            </h2>
            <p class="text-slate-500 dark:text-slate-400 mt-3 text-sm sm:text-base font-medium">
                Chọn nhu cầu sử dụng thực tế hoặc thương hiệu để AI PhoneStore gợi ý chiếc điện thoại hoàn hảo nhất
            </p>
        </div>


        <!-- BỘ LỌC TRỰC QUAN THEO NHU CẦU SỬ DỤNG (INTERACTIVE NEEDS FILTER) -->
        <div class="mb-10 p-4 sm:p-6 bg-white/90 dark:bg-slate-900/80 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm backdrop-blur-md">
            <div class="flex items-center justify-between gap-3 mb-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="text-lg">🎯</span>
                    <span class="text-xs font-black uppercase tracking-wider text-slate-700 dark:text-slate-200">Bạn Cần Điện Thoại Để Làm Gì?</span>
                    <span class="hidden sm:inline-block px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 text-[10px] font-black border border-indigo-100 dark:border-indigo-800">Gợi ý thông minh</span>
                </div>
                <button type="button" onclick="clearNeedsFilter()" id="btnClearNeeds" class="hidden text-[11px] font-bold text-rose-500 hover:text-rose-600 flex items-center gap-1 transition">
                    <span>✕</span> Bỏ lọc nhu cầu
                </button>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <!-- Nhu cầu 1: Chơi Game Khủng -->
                <button type="button" onclick="toggleNeedFilter('gaming', this)"
                        class="need-filter-btn group p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:border-indigo-500 text-left transition-all duration-300 hover:shadow-lg hover:shadow-indigo-500/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🎮</span>
                        <span class="need-badge hidden px-2 py-0.5 rounded-md bg-indigo-600 text-white text-[9px] font-black">Đang chọn</span>
                    </div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">Chơi Game Khủng</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Chip mạnh, RAM &ge; 8GB, tản nhiệt tốt, tần số quét cao</p>
                </button>

                <!-- Nhu cầu 2: Chụp Ảnh Đỉnh Cao -->
                <button type="button" onclick="toggleNeedFilter('camera', this)"
                        class="need-filter-btn group p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:border-rose-500 text-left transition-all duration-300 hover:shadow-lg hover:shadow-rose-500/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl group-hover:scale-110 transition-transform">📸</span>
                        <span class="need-badge hidden px-2 py-0.5 rounded-md bg-rose-600 text-white text-[9px] font-black">Đang chọn</span>
                    </div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-rose-600 dark:group-hover:text-rose-400 transition">Chụp Ảnh Đỉnh Cao</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Cụm camera zoom xa, cảm biến lớn, chống rung OIS</p>
                </button>

                <!-- Nhu cầu 3: Pin Trâu Cả Ngày -->
                <button type="button" onclick="toggleNeedFilter('battery', this)"
                        class="need-filter-btn group p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:border-emerald-500 text-left transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🔋</span>
                        <span class="need-badge hidden px-2 py-0.5 rounded-md bg-emerald-600 text-white text-[9px] font-black">Đang chọn</span>
                    </div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition">Pin Trâu Cả Ngày</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Dung lượng lớn &ge; 5000mAh, sạc siêu tốc 45W-120W</p>
                </button>

                <!-- Nhu cầu 4: Gọn Nhẹ & Sang Trọng -->
                <button type="button" onclick="toggleNeedFilter('compact', this)"
                        class="need-filter-btn group p-3.5 sm:p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 hover:border-amber-500 text-left transition-all duration-300 hover:shadow-lg hover:shadow-amber-500/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🪶</span>
                        <span class="need-badge hidden px-2 py-0.5 rounded-md bg-amber-600 text-white text-[9px] font-black">Đang chọn</span>
                    </div>
                    <h4 class="text-xs font-black text-slate-900 dark:text-white group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">Gọn Nhẹ & Sang Trọng</h4>
                    <p class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Viền titan mỏng nhẹ, cầm vừa tay, đút túi dễ dàng</p>
                </button>
            </div>
        </div>

        <!-- Filter Tab Buttons Flagship Style (Theo Danh Mục Thương Hiệu) -->
        <div class="flex items-center justify-center flex-wrap gap-2.5 mb-14">
            <button id="btn-cat-all" onclick="filterCategory('all', this)" 
                    class="category-btn px-7 py-3 rounded-full text-xs font-black uppercase tracking-wider transition-all duration-300 bg-slate-900 text-white dark:bg-blue-600 shadow-xl shadow-slate-900/20 hover:scale-105">
                🔥 Tất Cả ({{ $products->count() }})
            </button>

            @foreach($categories as $cat)
                <button id="btn-cat-{{ $cat->id }}" onclick="filterCategory('cat-{{ $cat->id }}', this)" 
                        class="category-btn px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider transition-all duration-300 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-600 dark:hover:text-blue-400 border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-blue-200">
                    {{ $cat->name }} ({{ $cat->products_count }})
                </button>
            @endforeach
        </div>




        <!-- 4. LƯỚI SẢN PHẨM CARD BOX SANG TRỌNG (FLAGSHIP GRADIENT & MODERN SLATE PALETTE) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-7 perspective-container" id="productGrid">
            @forelse($products as $product)
                @php 
                    $needsClasses = implode(' ', array_map(fn($t) => 'need-' . $t, $product->needs_tags)); 
                @endphp
                <div class="product-card tilt-card reveal-on-scroll cat-{{ $product->category_id }} {{ $needsClasses }} relative bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-2xl hover:shadow-blue-500/10 hover:border-blue-300 dark:hover:border-blue-500 transition-all duration-300 flex flex-col group p-5 overflow-hidden"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-price="{{ number_format($product->price) }} đ"
                     data-image="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=400&q=80' }}"
                     data-brand="{{ $product->brand ?? 'PhoneStore' }}">
                    
                    <!-- Badges Top Floating -->
                    <div class="absolute top-3.5 left-3.5 z-10 flex flex-col gap-1.5 items-start">
                        <span class="inline-flex items-center gap-1 bg-gradient-to-r from-rose-600 to-red-600 text-white text-[11px] font-black px-2.5 py-1 rounded-full shadow-sm">
                            🔥 Giảm {{ rand(10, 30) }}%
                        </span>
                        @if(in_array('gaming', $product->needs_tags))
                            <span class="inline-flex items-center gap-1 bg-indigo-600/95 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">
                                🎮 Chiến Game
                            </span>
                        @elseif(in_array('camera', $product->needs_tags))
                            <span class="inline-flex items-center gap-1 bg-rose-600/95 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">
                                📸 Camera Đỉnh
                            </span>
                        @endif
                    </div>
                    
                    <div class="absolute top-3.5 right-3.5 z-10 flex items-center gap-1.5">
                        <!-- Nút Thêm vào So Sánh Specs Nhanh -->
                        <button type="button" onclick="toggleCompareProduct({{ $product->id }}, '{{ addslashes($product->name) }}', '{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=400&q=80' }}', '{{ number_format($product->price) }} đ')" 
                                class="compare-btn-{{ $product->id }} w-8 h-8 rounded-full bg-white/90 dark:bg-slate-800 backdrop-blur-md border border-slate-200/80 dark:border-slate-700 flex items-center justify-center text-slate-500 dark:text-slate-300 hover:text-blue-600 hover:scale-110 shadow-sm transition cursor-pointer" 
                                title="Thêm vào bảng so sánh">
                            ⚖️
                        </button>
                        <button class="w-8 h-8 rounded-full bg-white/90 dark:bg-slate-800 backdrop-blur-md border border-slate-200/80 dark:border-slate-700 flex items-center justify-center text-slate-400 hover:text-rose-500 hover:scale-110 shadow-sm transition" aria-label="Yêu thích">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </button>
                    </div>

                    <!-- Product Image Container -->
                    <a href="{{ route('products.show', $product->id) }}" class="block relative pt-7 pb-3 h-[210px] flex justify-center items-center">
                        <div class="w-full h-full rounded-2xl bg-slate-50 dark:bg-slate-800/60 p-4 flex items-center justify-center border border-slate-100 dark:border-slate-800 shadow-inner group-hover:bg-white dark:group-hover:bg-slate-800 transition-colors duration-300">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="max-h-[160px] max-w-full object-contain group-hover:scale-110 transition-transform duration-300 drop-shadow-md" alt="{{ $product->name }}">
                            @else
                                <img src="https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=400&q=80" class="max-h-[160px] max-w-full object-contain group-hover:scale-110 transition-transform duration-300 drop-shadow-md" alt="{{ $product->name }}">
                            @endif
                        </div>
                    </a>

                    <!-- Info -->
                    <div class="flex-1 flex flex-col mt-3">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <span class="text-[11px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-md bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900">
                                {{ $product->brand ?? 'Chính Hãng' }}
                            </span>
                            <div class="flex items-center gap-1 text-xs font-bold text-slate-500 dark:text-slate-400">
                                <span class="text-amber-400">★</span> 4.9
                            </div>
                        </div>

                        <a href="{{ route('products.show', $product->id) }}">
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm leading-snug line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                {{ $product->name }}
                            </h3>
                        </a>

                        <!-- Prices -->
                        <div class="mt-2.5 flex items-baseline gap-2 flex-wrap">
                            <span class="text-rose-600 dark:text-rose-400 font-black text-lg tracking-tight">{{ number_format($product->price) }}đ</span>
                            <span class="text-slate-400 text-xs line-through font-medium">{{ number_format($product->price * 1.2) }}đ</span>
                        </div>

                        <!-- Specs (Ram/Rom/Screen) -->
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            <span class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-2 py-0.5 rounded-lg shadow-2xs">6.7" OLED</span>
                            <span class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-2 py-0.5 rounded-lg shadow-2xs">{{ $product->specs['ram'] ?? '8GB RAM' }}</span>
                            <span class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold px-2 py-0.5 rounded-lg shadow-2xs">{{ $product->specs['storage'] ?? '256GB' }}</span>
                        </div>

                        <!-- Promotion Boxes Phối Màu Tinh Tế -->
                        <div class="mt-3.5 space-y-1.5 text-xs font-semibold">
                            <div class="bg-blue-50/80 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/60 text-blue-900 dark:text-blue-300 px-2.5 py-1.5 rounded-xl flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                <span class="truncate">Smember giảm thêm 150.000đ</span>
                            </div>
                            <div class="bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 text-indigo-900 dark:text-indigo-300 px-2.5 py-1.5 rounded-xl flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                                <span class="truncate">S-Student trợ giá đến 300.000đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="mt-4 pt-3 flex items-center justify-between border-t border-slate-200/80 dark:border-slate-800">
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 px-2.5 py-0.5 rounded-full">
                            ⚡ Giao 2h
                        </span>
                        
                        <a href="{{ route('products.show', $product->id) }}" class="inline-flex items-center gap-1 text-xs font-black text-blue-600 dark:text-blue-400 hover:text-blue-700 group-hover:underline transition">
                            <span>Chi tiết</span>
                        </a>
                    </div>

                </div>

            @empty
                <div class="col-span-full py-16 text-center text-slate-400">
                    <p class="text-lg font-medium">Chưa có sản phẩm nào được cập nhật.</p>
                </div>
            @endforelse
        </div>



        <!-- 4.1. NÚT XEM THÊM SẢN PHẨM (FLAGSHIP LOAD MORE BUTTON) -->
        <div id="loadMoreSection" class="mt-12 text-center">
            <div class="inline-flex flex-col items-center gap-3">
                <button type="button" id="loadMoreBtn" onclick="loadMoreProducts()" 
                        class="group inline-flex items-center gap-2.5 px-8 py-3.5 bg-white dark:bg-slate-900 hover:bg-slate-900 dark:hover:bg-blue-600 text-slate-800 dark:text-slate-200 hover:text-white rounded-full font-extrabold text-xs uppercase tracking-wider border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
                    <span id="loadMoreBtnText">Xem thêm sản phẩm</span>
                    <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-800 group-hover:bg-slate-800 dark:group-hover:bg-blue-700 text-slate-600 dark:text-slate-300 group-hover:text-white text-xs flex items-center justify-center transition">
                        ↓
                    </span>
                </button>
                
                <p id="productCounterText" class="text-xs font-semibold text-slate-400">
                    Đang hiển thị <span id="currentVisibleCount" class="font-bold text-slate-700 dark:text-slate-200">8</span> / <span id="totalProductCount" class="font-bold text-slate-700 dark:text-slate-200">{{ $products->count() }}</span> sản phẩm
                </p>
            </div>
        </div>

    </section>


    <!-- 5. BENTO GRID: TÍNH NĂNG CÔNG NGHỆ (Storytelling Section) -->
    <section id="bento" class="py-24 bg-white dark:bg-[#07090e] border-t border-slate-200/80 dark:border-slate-800 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-black tracking-widest text-blue-600 dark:text-blue-400 uppercase bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900 px-3.5 py-1.5 rounded-full">
                    Đặc Quyền Flagship
                </span>
                <h2 class="text-3xl sm:text-5xl font-black tracking-tight text-slate-900 dark:text-white mt-4">
                    Tại Sao Chọn Mua Tại PhoneStore?
                </h2>
                <p class="text-slate-500 dark:text-slate-400 text-sm mt-3 font-medium">Hệ thống dịch vụ chuẩn Flagship mang lại trải nghiệm an tâm trọn vẹn.</p>
            </div>

            <!-- Bento Grid 3 Cột -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-7">
                <!-- Card 1 (Lớn) -->
                <div class="md:col-span-2 bg-[#07090e] dark:bg-[#0d121f] text-white rounded-3xl p-8 sm:p-14 relative overflow-hidden flex flex-col justify-between min-h-[360px] border border-slate-800 shadow-2xl group">
                    <div class="relative z-10 max-w-md">
                        <span class="text-xs font-black text-blue-400 uppercase tracking-widest bg-blue-500/10 border border-blue-500/30 px-3 py-1 rounded-full">
                            Quyền Năng AI
                        </span>
                        <h3 class="text-2xl sm:text-4xl font-black tracking-tight mt-4 leading-snug">
                            Dịch Trực Tiếp & Khoanh Tròn Để Tìm Kiếm
                        </h3>
                        <p class="text-slate-400 text-sm mt-3 font-medium leading-relaxed">
                            Trải nghiệm các tính năng trí tuệ nhân tạo thế hệ mới nhất có sẵn trên tất cả dòng máy flagship hàng đầu.
                        </p>
                    </div>
                    <div class="absolute -bottom-10 -right-10 w-80 h-80 bg-gradient-to-tl from-blue-600/40 via-indigo-600/20 to-transparent rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700 pointer-events-none"></div>
                </div>

                <!-- Card 2 -->
                <div class="bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/60 rounded-3xl p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-emerald-200 dark:hover:border-emerald-500/40 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                            🛡️
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Bảo Hành 1 Đổi 1</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-2.5 font-medium leading-relaxed">
                            Cam kết máy chính hãng 100% nguyên seal. Bảo hành 12 tháng tại các trung tâm ủy quyền toàn quốc.
                        </p>
                    </div>
                    <span class="text-xs font-black text-emerald-600 dark:text-emerald-400 mt-6 inline-block">
                        <span>Yên tâm tuyệt đối</span>
                    </span>
                </div>

                <!-- Card 3 -->
                <div class="bg-gradient-to-b from-white to-slate-50 dark:from-slate-900 dark:to-slate-800/60 rounded-3xl p-8 border border-slate-200/90 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-amber-200 dark:hover:border-amber-500/40 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-950/60 border border-amber-100 dark:border-amber-900 text-amber-600 dark:text-amber-400 flex items-center justify-center text-3xl mb-6 group-hover:scale-110 transition-transform">
                            ⚡
                        </div>
                        <h3 class="text-xl font-extrabold text-slate-900 dark:text-white">Giao Siêu Tốc 2H</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-2.5 font-medium leading-relaxed">
                            Giao hàng miễn phí toàn quốc. Nhận hàng kiểm tra thanh toán ngay tại nhà không rủi ro.
                        </p>
                    </div>
                    <span class="text-xs font-black text-amber-600 dark:text-amber-400 mt-6 inline-block">
                        <span>Miễn phí vận chuyển</span>
                    </span>
                </div>

                <!-- Card 4 -->
                <div class="md:col-span-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-indigo-700 text-white rounded-3xl p-8 sm:p-12 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl shadow-blue-500/20 border border-blue-400/20">
                    <div class="space-y-2 text-center sm:text-left">
                        <h3 class="text-2xl sm:text-3xl font-black tracking-tight">Thu Cũ Đổi Mới Trợ Giá Đến 30%</h3>
                        <p class="text-blue-100 text-sm font-medium">Lên đời điện thoại sang chảnh chưa bao giờ dễ dàng và tiết kiệm đến thế.</p>
                    </div>
                    <a href="#all-products" class="px-9 py-4 bg-white text-slate-950 hover:bg-blue-50 hover:text-blue-600 font-black text-xs uppercase tracking-wider rounded-full transition-all duration-300 whitespace-nowrap shadow-xl hover:shadow-2xl hover:-translate-y-0.5">
                        Định Giá Ngay
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- 6. MINIMALIST FOOTER -->
    <footer class="bg-black text-gray-400 py-12 border-t border-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <span class="text-sm font-extrabold tracking-tighter text-white uppercase">VANH<span class="text-blue-500">PHONE</span></span>
            <p class="text-xs text-gray-500">© 2026 VanhPhone Inc. All rights reserved. Thiết kế trải nghiệm phong cách Flagship.</p>
            <div class="flex gap-6 text-xs font-medium">
                <a href="#" class="hover:text-white transition">Chính sách bảo hành</a>
                <a href="#" class="hover:text-white transition">Giao hàng</a>
                <a href="#" class="hover:text-white transition">Liên hệ</a>
            </div>
        </div>
    </footer>

    <!-- Swiper.js Script CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- JAVASCRIPT TƯƠNG TÁC (TABS, CAROUSEL, USER MENU) -->
    <script>
        // 1. Khởi tạo Hero Banner Slider
        const swiper = new Swiper('.heroSwiper', {
            loop: true,
            autoplay: {
                delay: 4500,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });

        // Hàm chuẩn hóa chuỗi tiếng Việt để tìm kiếm không phân biệt hoa thường và dấu
        function normalizeStr(str) {
            return (str || '')
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .replace(/đ/g, 'd')
                .trim();
        }

        // ===============================================
        // SAMSUNG STYLE LIVE SEARCH DROPDOWN ENGINE
        // ===============================================
        let searchDebounceTimer = null;


        function showSearchDropdown() {
            const dropdown = document.getElementById('liveSearchResultsBox');
            if (dropdown) {
                dropdown.style.display = 'block';
            }
            const inputVal = (document.getElementById('navbarSearchInput')?.value || '').trim();
            if (inputVal.length > 0) {
                performSamsungLiveSearch(inputVal);
            } else {
                loadDefaultPopularSuggestions();
            }
        }


        function closeLiveSearchDropdown() {
            const dropdown = document.getElementById('liveSearchResultsBox');
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        }

        function closeAndClearLiveSearch() {
            const navInput = document.getElementById('navbarSearchInput');
            if (navInput) navInput.value = '';
            const clearBtn = document.getElementById('clearNavSearchBtn');
            if (clearBtn) clearBtn.classList.add('hidden');
            closeLiveSearchDropdown();
        }

        function quickFillSearch(keyword) {
            const navInput = document.getElementById('navbarSearchInput');
            if (navInput) {
                navInput.value = keyword;
                navInput.focus();
                performSamsungLiveSearch(keyword);
            }
        }

        async function performSamsungLiveSearch(query) {
            const clearBtn = document.getElementById('clearNavSearchBtn');
            const dropdown = document.getElementById('liveSearchResultsBox');
            const loading = document.getElementById('searchLoadingState');
            const contentGrid = document.getElementById('searchContentGrid');
            const emptyState = document.getElementById('searchEmptyState');
            const cardsGrid = document.getElementById('searchProductCardsGrid');
            const suggestionsBox = document.getElementById('searchKeywordSuggestions');
            const viewAllLink = document.getElementById('viewAllSearchLink');
            const headerLabel = document.getElementById('searchHeaderLabel');

            if (dropdown) dropdown.style.display = 'block';

            const trimmed = query.trim();
            if (clearBtn) {
                if (trimmed.length > 0) clearBtn.classList.remove('hidden');
                else clearBtn.classList.add('hidden');
            }

            if (trimmed.length === 0) {
                loadDefaultPopularSuggestions();
                return;
            }

            if (headerLabel) headerLabel.innerText = `Kết quả gợi ý cho "${query}"`;
            if (viewAllLink) viewAllLink.href = `/products?search=${encodeURIComponent(trimmed)}`;

            // Reset ngay lập tức nội dung cũ và bật loading
            if (cardsGrid) cardsGrid.innerHTML = '';
            if (loading) loading.classList.remove('hidden');
            if (emptyState) emptyState.classList.add('hidden');

            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/products/search?q=${encodeURIComponent(trimmed)}`);
                    const data = await response.json();

                    if (loading) loading.classList.add('hidden');

                    if (data.products && data.products.length > 0) {
                        if (emptyState) emptyState.classList.add('hidden');
                        if (cardsGrid) {
                            cardsGrid.classList.remove('hidden');
                            cardsGrid.innerHTML = data.products.map(p => `
                                <a href="${p.url}" class="group block bg-slate-50/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 rounded-2xl p-3 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-500/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                                    <div class="h-28 w-full bg-white dark:bg-slate-900 rounded-xl p-2 flex items-center justify-center overflow-hidden border border-slate-50 dark:border-slate-800 mb-2">
                                        <img src="${p.image}" alt="${p.name}" loading="lazy" class="max-h-full max-w-full object-contain group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-slate-900 dark:text-slate-100 text-xs leading-tight line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                            ${p.name}
                                        </h5>
                                        <span class="text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 block mt-0.5">${p.brand}</span>
                                        <p class="text-rose-600 dark:text-rose-400 font-black text-xs mt-1.5">
                                            ${p.price}
                                        </p>
                                    </div>
                                </a>
                            `).join('');

                        }

                        // Render Từ khóa gợi ý thông minh
                        if (suggestionsBox) {
                            const suggestionKeywords = (data.suggestions && data.suggestions.length > 0)
                                ? data.suggestions
                                : [trimmed, trimmed + ' Pro', trimmed + ' Ultra', trimmed + ' Chính Hãng'];
                            
                            suggestionsBox.innerHTML = suggestionKeywords.map(k => `
                                <button type="button" onclick="quickFillSearch('${k}')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">🔍</span> <span class="truncate">${k}</span>
                                </button>
                            `).join('');
                        }

                    } else {
                        if (cardsGrid) {
                            cardsGrid.innerHTML = '';
                            cardsGrid.classList.add('hidden');
                        }
                        if (emptyState) emptyState.classList.remove('hidden');

                        if (suggestionsBox) {
                            suggestionsBox.innerHTML = `
                                <button type="button" onclick="quickFillSearch('iPhone')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">🔍</span> <span>iPhone</span>
                                </button>
                                <button type="button" onclick="quickFillSearch('Samsung')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">🔍</span> <span>Samsung Galaxy</span>
                                </button>
                                <button type="button" onclick="quickFillSearch('Xiaomi')" class="w-full text-left text-xs font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-50 dark:hover:bg-slate-800 p-2 rounded-xl transition flex items-center gap-2">
                                    <span class="text-blue-500 dark:text-blue-400">🔍</span> <span>Xiaomi</span>
                                </button>
                            `;
                        }
                    }

                } catch (err) {
                    if (loading) loading.classList.add('hidden');
                }
            }, 60);
        }



        // Tải các sản phẩm gợi ý phổ biến ban đầu
        function loadDefaultPopularSuggestions() {
            const emptyState = document.getElementById('searchEmptyState');
            const cardsGrid = document.getElementById('searchProductCardsGrid');
            const headerLabel = document.getElementById('searchHeaderLabel');
            const viewAllLink = document.getElementById('viewAllSearchLink');

            if (headerLabel) headerLabel.innerText = "Gợi ý dòng máy thịnh hành";
            if (viewAllLink) viewAllLink.href = "{{ route('products.index') }}";
            if (emptyState) emptyState.classList.add('hidden');
            if (cardsGrid) {
                cardsGrid.classList.remove('hidden');
                // Lấy 4 sản phẩm đầu tiên có sẵn trên DOM
                const existingCards = Array.from(document.querySelectorAll('.product-card')).slice(0, 4);
                if (existingCards.length > 0) {
                    cardsGrid.innerHTML = existingCards.map(card => {
                        const link = card.querySelector('a')?.href || '#';
                        const img = card.querySelector('img')?.src || '';
                        const title = card.querySelector('h3')?.innerText || 'Flagship Phone';
                        const price = card.querySelector('.text-rose-600')?.innerText || card.querySelector('.text-\\[\\#d70018\\]')?.innerText || 'Liên hệ';
                        return `
                            <a href="${link}" class="group block bg-slate-50/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-800 rounded-2xl p-3 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-500/50 hover:shadow-lg transition-all duration-300 flex flex-col justify-between">
                                <div class="h-28 w-full bg-white dark:bg-slate-900 rounded-xl p-2 flex items-center justify-center overflow-hidden border border-slate-50 dark:border-slate-800 mb-2">
                                    <img src="${img}" alt="${title}" class="max-h-full max-w-full object-contain group-hover:scale-110 transition duration-300">
                                </div>
                                <div>
                                    <h5 class="font-bold text-slate-800 dark:text-slate-100 text-[11px] leading-tight line-clamp-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                        ${title}
                                    </h5>
                                    <p class="text-rose-600 dark:text-rose-400 font-extrabold text-xs mt-1.5">
                                        ${price}
                                    </p>
                                </div>
                            </a>

                        `;
                    }).join('');
                }
            }
        }

        // Tự động đóng dropdown khi click ra ngoài
        document.addEventListener('click', function(e) {
            const container = document.getElementById('searchDropdownContainer');
            if (container && !container.contains(e.target)) {
                closeLiveSearchDropdown();
            }
        });



        // 2. Chức năng Phân Trang & Bộ Lọc Nhu Cầu Thông Minh (Smart Needs Filter)
        const PRODUCTS_PER_PAGE = 8;
        let currentDisplayLimit = PRODUCTS_PER_PAGE;
        let currentActiveCategory = 'all';
        let currentActiveNeed = null; // 'gaming' | 'camera' | 'battery' | 'compact'

        function updateProductVisibility() {
            const allCards = Array.from(document.querySelectorAll('.product-card'));
            const matchingCards = allCards.filter(card => {
                const matchCat = (currentActiveCategory === 'all' || card.classList.contains(currentActiveCategory));
                const matchNeed = (!currentActiveNeed || card.classList.contains('need-' + currentActiveNeed));
                return matchCat && matchNeed;
            });

            let visibleCount = 0;
            allCards.forEach(card => {
                const matchCat = (currentActiveCategory === 'all' || card.classList.contains(currentActiveCategory));
                const matchNeed = (!currentActiveNeed || card.classList.contains('need-' + currentActiveNeed));
                const matches = matchCat && matchNeed;

                if (matches) {
                    const matchIndex = matchingCards.indexOf(card);
                    if (matchIndex < currentDisplayLimit) {
                        card.style.display = 'flex';
                        card.style.opacity = '1';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                } else {
                    card.style.display = 'none';
                }
            });

            // Cập nhật bộ đếm
            const countEl = document.getElementById('currentVisibleCount');
            const totalEl = document.getElementById('totalProductCount');
            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadMoreBtnText = document.getElementById('loadMoreBtnText');
            const loadMoreSection = document.getElementById('loadMoreSection');

            if (countEl) countEl.innerText = visibleCount;
            if (totalEl) totalEl.innerText = matchingCards.length;

            if (loadMoreSection) {
                if (matchingCards.length <= PRODUCTS_PER_PAGE) {
                    loadMoreSection.style.display = 'none';
                } else {
                    loadMoreSection.style.display = 'block';
                    if (visibleCount >= matchingCards.length) {
                        loadMoreBtn.disabled = true;
                        loadMoreBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        if (loadMoreBtnText) loadMoreBtnText.innerText = 'Đã hiển thị toàn bộ sản phẩm';
                    } else {
                        loadMoreBtn.disabled = false;
                        loadMoreBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        const remaining = matchingCards.length - visibleCount;
                        if (loadMoreBtnText) loadMoreBtnText.innerText = `Xem thêm ${Math.min(PRODUCTS_PER_PAGE, remaining)} sản phẩm (còn ${remaining} máy)`;
                    }
                }
            }
        }

        // Bật / Tắt Bộ lọc theo Nhu cầu sử dụng
        function toggleNeedFilter(needType, btn) {
            const clearBtn = document.getElementById('btnClearNeeds');
            if (currentActiveNeed === needType) {
                // Click lại thì tắt lọc
                currentActiveNeed = null;
                if (clearBtn) clearBtn.classList.add('hidden');
            } else {
                currentActiveNeed = needType;
                if (clearBtn) clearBtn.classList.remove('hidden');
            }

            // Cập nhật UI các nút nhu cầu
            document.querySelectorAll('.need-filter-btn').forEach(b => {
                b.classList.remove('border-blue-600', 'ring-2', 'ring-blue-500/20', 'bg-blue-50/50', 'dark:bg-blue-950/40');
                const badge = b.querySelector('.need-badge');
                if (badge) badge.classList.add('hidden');
            });

            if (currentActiveNeed && btn) {
                btn.classList.add('border-blue-600', 'ring-2', 'ring-blue-500/20', 'bg-blue-50/50', 'dark:bg-blue-950/40');
                const badge = btn.querySelector('.need-badge');
                if (badge) badge.classList.remove('hidden');
            }

            currentDisplayLimit = PRODUCTS_PER_PAGE;
            updateProductVisibility();
        }

        // Xóa bộ lọc nhu cầu
        function clearNeedsFilter() {
            currentActiveNeed = null;
            const clearBtn = document.getElementById('btnClearNeeds');
            if (clearBtn) clearBtn.classList.add('hidden');

            document.querySelectorAll('.need-filter-btn').forEach(b => {
                b.classList.remove('border-blue-600', 'ring-2', 'ring-blue-500/20', 'bg-blue-50/50', 'dark:bg-blue-950/40');
                const badge = b.querySelector('.need-badge');
                if (badge) badge.classList.add('hidden');
            });

            updateProductVisibility();
        }

        function loadMoreProducts() {
            currentDisplayLimit += PRODUCTS_PER_PAGE;
            updateProductVisibility();
        }

        // Chức năng Lọc Tab Danh Mục Mượt Mà
        function filterCategory(catClass, btn) {
            currentActiveCategory = catClass;
            currentDisplayLimit = PRODUCTS_PER_PAGE; // Reset về 8 sản phẩm đầu tiên khi đổi tab

            // Nếu không có btn được truyền vào hoặc btn không hợp lệ, fallback về nút "Tất Cả"
            if (!btn || !btn.classList) {
                btn = document.getElementById('btn-cat-all');
            }

            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('bg-slate-900', 'dark:bg-blue-600', 'text-white', 'shadow-xl');
                b.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-200');
            });
            if (btn) {
                btn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-700', 'dark:text-slate-200');
                btn.classList.add('bg-slate-900', 'dark:bg-blue-600', 'text-white', 'shadow-xl');
            }

            updateProductVisibility();
        }


        // Khởi chạy phân trang khi tải trang xong
        document.addEventListener('DOMContentLoaded', function() {
            updateProductVisibility();
        });



        // 3. Đóng / Mở Menu Tài Khoản & Đăng Xuất
        function toggleUserMenu() {
            const menu = document.getElementById('userMenuContent');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // Tự động đóng menu khi click ra ngoài
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('userMenuDropdown');
            const menu = document.getElementById('userMenuContent');
            if (dropdown && menu && !dropdown.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });

        // 4. LẮNG NGHE PHÍM TẮT HỆ THỐNG (Power User Shortcuts: '/' để tìm kiếm, 'Esc' để đóng popup)
        document.addEventListener('keydown', function(e) {
            // Khi nhấn phím Escape
            if (e.key === 'Escape') {
                closeLiveSearchDropdown();
                const chatPopup = document.getElementById('chat-popup');
                const chatToggle = document.getElementById('chat-toggle');
                if (chatPopup && chatPopup.style.display !== 'none') {
                    chatPopup.style.display = 'none';
                    if (chatToggle) chatToggle.style.display = 'flex';
                }
            }

            // Khi nhấn phím '/' khi không gõ trong input/textarea
            if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const navSearch = document.getElementById('navbarSearchInput');
                if (navSearch) {
                    navSearch.focus();
                    showSearchDropdown();
                }
            }
        });

        // ==========================================
        // 5. PARALLAX SCROLLING & 3D TILT ENGINE
        // ==========================================
        
        // A. Parallax Hero Background & Device Depth
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroImgs = document.querySelectorAll('.parallax-hero-img');
            const heroGlows = document.querySelectorAll('.parallax-glow');
            
            // Di chuyển ảnh điện thoại và quầng sáng với 2 vận tốc khác nhau (Chiều sâu Parallax)
            heroImgs.forEach(img => {
                if (scrolled < 700) {
                    img.style.transform = `translateY(${scrolled * 0.14}px) scale(${1 - scrolled * 0.0002})`;
                }
            });
            heroGlows.forEach(glow => {
                if (scrolled < 700) {
                    glow.style.transform = `translateY(${scrolled * 0.28}px) scale(${1 + scrolled * 0.0005})`;
                }
            });
        }, { passive: true });

        // B. 3D Tilt Physics cho Thẻ Sản Phẩm (Interactive Micro-Interaction)
        function init3DTiltCards() {
            const cards = document.querySelectorAll('.tilt-card');
            cards.forEach(card => {
                card.addEventListener('mousemove', function(e) {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left; // Tọa độ X trong card
                    const y = e.clientY - rect.top;  // Tọa độ Y trong card
                    
                    const centerX = rect.width / 2;
                    const centerY = rect.height / 2;
                    
                    const rotateX = ((y - centerY) / centerY) * -7; // Tối đa nghiêng 7 độ
                    const rotateY = ((x - centerX) / centerX) * 7;
                    
                    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px) scale(1.02)`;
                });

                card.addEventListener('mouseleave', function() {
                    card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) translateY(0px) scale(1)';
                });
            });
        }

        // C. Scroll Reveal Animation liên tục (Intersection Observer lặp lại khi cuộn)
        function initScrollReveal() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                    } else {
                        // Khi cuộn ra ngoài màn hình -> reset để khi cuộn lại sẽ chạy tiếp animation
                        entry.target.classList.remove('revealed');
                    }
                });
            }, { 
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px' // Kích hoạt sớm hơn một chút khi cuộn tới
            });

            document.querySelectorAll('.reveal-on-scroll').forEach(el => {
                observer.observe(el);
            });
        }


        // Khởi động các tương tác
        document.addEventListener('DOMContentLoaded', function() {
            init3DTiltCards();
            initScrollReveal();
            renderCompareDock();
        });

        // ========================================================
        // 6. FLOATING COMPARISON BAR (DOCK SO SÁNH THÔNG SỐ TRỰC QUAN)
        // ========================================================
        let compareList = JSON.parse(localStorage.getItem('phonestore_compare_list') || '[]');

        function toggleCompareProduct(id, name, image, price) {
            const index = compareList.findIndex(item => item.id === id);
            if (index > -1) {
                // Đã có -> Bỏ chọn
                compareList.splice(index, 1);
            } else {
                if (compareList.length >= 3) {
                    alert('Bạn chỉ có thể so sánh tối đa 3 chiếc điện thoại cùng lúc!');
                    return;
                }
                compareList.push({ id, name, image, price });
            }

            localStorage.setItem('phonestore_compare_list', JSON.stringify(compareList));
            renderCompareDock();
        }

        function removeCompareItemFromDock(id) {
            compareList = compareList.filter(item => item.id !== id);
            localStorage.setItem('phonestore_compare_list', JSON.stringify(compareList));
            renderCompareDock();
        }

        function clearAllCompare() {
            compareList = [];
            localStorage.setItem('phonestore_compare_list', JSON.stringify(compareList));
            renderCompareDock();
        }

        function renderCompareDock() {
            const dock = document.getElementById('floatingCompareDock');
            const itemsContainer = document.getElementById('compareDockItems');
            const countText = document.getElementById('compareCountText');
            const btnGo = document.getElementById('btnGoCompare');

            if (!dock) return;

            // Highlight các nút ⚖️ trên card sản phẩm
            document.querySelectorAll('[class*="compare-btn-"]').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
            });
            compareList.forEach(item => {
                const btn = document.querySelector(`.compare-btn-${item.id}`);
                if (btn) {
                    btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                }
            });

            if (compareList.length === 0) {
                dock.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
                return;
            }

            dock.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            if (countText) countText.innerText = `${compareList.length}/3 máy`;

            if (itemsContainer) {
                itemsContainer.innerHTML = compareList.map(item => `
                    <div class="relative flex items-center gap-2 bg-slate-50 dark:bg-slate-800/80 p-2 rounded-2xl border border-slate-200 dark:border-slate-700 min-w-[140px] max-w-[180px]">
                        <button type="button" onclick="removeCompareItemFromDock(${item.id})" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-slate-900 text-white flex items-center justify-center text-[9px] hover:bg-rose-600 transition">✕</button>
                        <img src="${item.image}" alt="${item.name}" class="w-8 h-8 object-contain">
                        <div class="flex-1 min-w-0">
                            <p class="text-[10px] font-black text-slate-800 dark:text-slate-200 truncate">${item.name}</p>
                            <p class="text-[9px] font-bold text-rose-600">${item.price}</p>
                        </div>
                    </div>
                `).join('');
            }

            if (btnGo) {
                btnGo.onclick = function() {
                    const ids = compareList.map(i => i.id).join(',');
                    window.location.href = `{{ route('products.compare') }}?ids=${ids}`;
                };
            }
        }

        // Toggle Dark / Light Mode chuẩn Apple
        function togglePhoneStoreTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('phonestore_theme', isDark ? 'dark' : 'light');
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: { isDark } }));
        }
    </script>

    <!-- FLOATING DOCK SO SÁNH SẢN PHẨM -->
    <div id="floatingCompareDock" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[9999] bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border border-slate-200/90 dark:border-slate-800 shadow-2xl rounded-3xl p-3.5 flex items-center gap-4 transition-all duration-300 translate-y-32 opacity-0 pointer-events-none max-w-2xl w-[94vw] sm:w-auto">
        <div class="flex items-center gap-2 pl-2">
            <span class="text-xl">⚖️</span>
            <div>
                <h4 class="text-xs font-black text-slate-900 dark:text-white">Bảng So Sánh</h4>
                <p class="text-[10px] font-bold text-blue-600 dark:text-blue-400" id="compareCountText">0/3 máy</p>
            </div>
        </div>

        <!-- Danh sách thumbnail máy đang chọn -->
        <div class="flex items-center gap-2 overflow-x-auto" id="compareDockItems"></div>

        <div class="flex items-center gap-2 border-l border-slate-200 dark:border-slate-800 pl-3">
            <button type="button" id="btnGoCompare" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs rounded-2xl shadow-lg shadow-blue-500/25 hover:scale-105 transition">
                So Sánh Ngay
            </button>
            <button type="button" onclick="clearAllCompare()" class="p-2 text-slate-400 hover:text-rose-600 text-xs font-bold" title="Xóa toàn bộ">
                ✕
            </button>
        </div>
    </div>

    <!-- Lab 07 Livechat Khách Hàng -->
    @include('partials.user-chat')
</body>
</html>