@extends('layouts.admin')

@section('title', 'Admin Dashboard - Tổng Quan Hệ Thống')
@section('page_title', 'Dashboard')
@section('page_heading', 'Tổng Quan Điều Hành')

@section('content')
<div class="space-y-8">
    
    <!-- 1. EXECUTIVE WELCOME BANNER -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 dark:from-[#0b1426] dark:via-[#0f172a] dark:to-[#090e1a] text-white p-6 sm:p-8 rounded-3xl border border-blue-800/40 shadow-xl">
        
        <!-- Decorative Glow Shapes -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -top-10 w-48 h-48 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300 text-[11px] font-black uppercase tracking-wider">
                    <span>⚡</span> Bảng Điều Khiển Quản Trị Flagship
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Xin chào, {{ Auth::user()->name }} 👋
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Hệ thống đang hoạt động ổn định. Bạn có <span class="font-bold text-amber-400">{{ $pendingOrders }}</span> đơn hàng đang chờ xử lý và <span class="font-bold text-emerald-400">{{ $lowStockProducts->count() }}</span> cảnh báo tồn kho cần chú ý.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-2xl shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <span>🚚</span> Quản Lý Đơn & GHN
                </a>
                <a href="{{ route('products.create') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-2xl border border-white/20 transition flex items-center gap-2 backdrop-blur-md">
                    <span>+</span> Thêm Sản Phẩm
                </a>
                <a href="{{ route('categories.create') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-2xl border border-white/20 transition flex items-center gap-2 backdrop-blur-md">
                    <span>+</span> Thêm Danh Mục
                </a>
            </div>
        </div>
    </div>

    <!-- 2. 4 THẺ KPI CHỦ LỰC (METRICS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- KPI 1: Doanh Thu -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400">Doanh Thu Thu Được</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">
                        {{ number_format($totalRevenue) }} <span class="text-xs font-bold text-slate-400">đ</span>
                    </h3>
                    <div class="flex items-center gap-1.5 mt-3 text-[11px] font-bold text-emerald-600 dark:text-emerald-400">
                        <span>●</span> <span>Từ đơn MoMo & COD đã thu</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl font-bold shadow-inner group-hover:scale-110 transition duration-300">
                    💰
                </div>
            </div>
        </div>

        <!-- KPI 2: Tổng Đơn Hàng -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400">Tổng Số Đơn Hàng</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">
                        {{ $totalOrders }} <span class="text-xs font-bold text-slate-400">đơn</span>
                    </h3>
                    <div class="flex items-center gap-1.5 mt-3 text-[11px] font-bold text-amber-500 dark:text-amber-400">
                        <span>⚡</span> <span>{{ $pendingOrders }} đơn chờ duyệt ship</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl font-bold shadow-inner group-hover:scale-110 transition duration-300">
                    📦
                </div>
            </div>
        </div>

        <!-- KPI 3: Mẫu Máy & Tồn Kho -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-indigo-600 dark:text-indigo-400">Sản Phẩm Trong Kho</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">
                        {{ $productCount }} <span class="text-xs font-bold text-slate-400">mẫu ({{ number_format($totalStock) }} máy)</span>
                    </h3>
                    <div class="flex items-center gap-1.5 mt-3 text-[11px] font-bold text-indigo-600 dark:text-indigo-400">
                        <span>📁</span> <span>{{ $categoryCount }} danh mục thương hiệu</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl font-bold shadow-inner group-hover:scale-110 transition duration-300">
                    📱
                </div>
            </div>
        </div>

        <!-- KPI 4: Định Giá Kho -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start">
                <div>
                    <span class="text-[11px] font-black uppercase tracking-wider text-purple-600 dark:text-purple-400">Tài Khoản & Định Giá</span>
                    <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-2">
                        {{ $userCount }} <span class="text-xs font-bold text-slate-400">users</span>
                    </h3>
                    <div class="flex items-center gap-1.5 mt-3 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                        <span>Ước tính kho:</span> <span class="text-slate-700 dark:text-slate-200 font-black">{{ number_format($totalValue / 1000000, 1) }} Tr</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl font-bold shadow-inner group-hover:scale-110 transition duration-300">
                    👥
                </div>
            </div>
        </div>

    </div>

    <!-- 3. MAIN DASHBOARD CONTENT GRID (2 Cột) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- CỘT TRÁI (2/3): BẢNG ĐƠN HÀNG MỚI NHẤT -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📦</span> Đơn Hàng Mới Cần Xử Lý
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Danh sách các đơn đặt hàng mới nhất cần điều phối giao hàng</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                    <span>Xem tất cả {{ $totalOrders }} đơn</span> <span>→</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="pb-3">Mã đơn</th>
                            <th class="pb-3">Khách hàng</th>
                            <th class="pb-3">Tổng tiền</th>
                            <th class="pb-3">Thanh toán</th>
                            <th class="pb-3">Trạng thái GHN</th>
                            <th class="pb-3 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($latestOrders as $ord)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                                <td class="py-3.5">
                                    <span class="font-mono font-black text-blue-600 dark:text-blue-400 block">
                                        #{{ $ord->order_code }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">{{ $ord->created_at ? $ord->created_at->format('H:i d/m/Y') : '' }}</span>
                                </td>

                                <td class="py-3.5">
                                    <p class="font-bold text-slate-900 dark:text-white truncate max-w-[120px]">{{ $ord->customer_name }}</p>
                                    <span class="text-[10px] text-slate-400 block truncate max-w-[120px]">{{ $ord->customer_phone }}</span>
                                </td>

                                <td class="py-3.5 font-black text-slate-900 dark:text-white">
                                    {{ number_format($ord->total_amount) }} đ
                                </td>

                                <td class="py-3.5">
                                    @if(strtoupper($ord->payment_method ?? '') === 'MOMO')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-pink-50 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border border-pink-200 dark:border-pink-800">
                                            MoMo ({{ $ord->payment_status === 'paid' ? 'Đã TT' : 'Chưa TT' }})
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                                            COD Thu Hộ
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5">
                                    @if($ord->shipping_status === 'delivered')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                                            ✓ Đã giao
                                        </span>
                                    @elseif($ord->shipping_status === 'shipping')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400">
                                            🚚 Đang giao
                                        </span>
                                    @elseif($ord->shipping_status === 'cancelled')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400">
                                            ✕ Đã hủy
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400">
                                            ⏳ Chờ duyệt
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 text-right">
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="px-3 py-1 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/80 text-blue-700 dark:text-blue-300 font-bold rounded-xl transition text-[11px] inline-flex items-center gap-1">
                                        Chi tiết <span>→</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Chưa có đơn hàng nào trong hệ thống.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>

        <!-- CỘT PHẢI (1/3): CẢNH BÁO TỒN KHO & DANH MỤC -->
        <div class="space-y-6">
            
            <!-- Widget 1: Cảnh Báo Tồn Kho Thấp -->
            <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="text-rose-500">⚠️</span> Cảnh Báo Tồn Kho
                    </h3>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400">
                        {{ $lowStockProducts->count() }} mẫu thấp
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse($lowStockProducts as $lowProd)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-9 h-9 bg-white dark:bg-slate-700 rounded-xl p-1 border border-slate-200 dark:border-slate-600 flex items-center justify-center flex-shrink-0">
                                    <img src="{{ $lowProd->image ? asset('storage/' . $lowProd->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=60&q=80' }}" class="max-h-full object-contain" alt="">
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 dark:text-white text-xs truncate max-w-[130px]">{{ $lowProd->name }}</p>
                                    <span class="text-[10px] text-slate-400">{{ $lowProd->category->name ?? '---' }}</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black {{ $lowProd->stock <= 0 ? 'bg-rose-100 dark:bg-rose-900 text-rose-700 dark:text-rose-300' : 'bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200' }}">
                                    Còn: {{ $lowProd->stock }}
                                </span>
                                <a href="{{ route('products.edit', $lowProd->id) }}" class="block text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline mt-0.5">Nhập thêm</a>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 py-4 text-center font-semibold">
                            ✓ Tất cả sản phẩm đều có số lượng tồn kho an toàn.
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Widget 2: Phân Bổ Danh Mục Thương Hiệu -->
            <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📁</span> Danh Mục Hàng
                    </h3>
                    <a href="{{ route('categories.index') }}" class="text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Quản lý
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($latestCategories as $cat)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl hover:bg-blue-50/50 dark:hover:bg-slate-800 transition">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-bold">📁</span>
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white text-xs">{{ $cat->name }}</h4>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $cat->slug }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-black text-blue-600 dark:text-blue-400 px-2 py-0.5 rounded-full bg-white dark:bg-slate-700 shadow-sm">
                                {{ $cat->products_count }} mẫu
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Chưa có danh mục nào.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection