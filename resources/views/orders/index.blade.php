@extends('layouts.app')

@section('content')
@php
    $counts = $counts ?? ['all' => 0, 'pending' => 0, 'shipping' => 0, 'delivered' => 0, 'cancelled' => 0, 'total_spent' => 0];
@endphp
<div class="max-w-6xl mx-auto py-4 space-y-6">

    <!-- Header & Thống kê đơn hàng -->
    <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                    <span>🛍️</span> Quản Lý Mua Hàng
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Lịch Sử Đơn Hàng</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Theo dõi lộ trình giao hàng GHN Express, kiểm tra trạng thái và quản lý đơn hàng của bạn.</p>
            </div>

            <a href="{{ route('home') }}" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-black uppercase tracking-wider rounded-full shadow-lg shadow-blue-500/25 transition hover:scale-105 inline-flex items-center gap-2">
                <span>⚡</span> Mua Sắm Thêm
            </a>
        </div>

        <!-- 4 Thẻ Thống Kê Nhanh -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-100 dark:border-slate-700/80 space-y-1">
                <span class="text-[11px] font-bold text-slate-400 uppercase">Tổng đơn đã đặt</span>
                <p class="text-xl font-black text-slate-900 dark:text-white">{{ $counts['all'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/50 space-y-1">
                <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400 uppercase">Đang xử lý</span>
                <p class="text-xl font-black text-amber-700 dark:text-amber-300">{{ $counts['pending'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-blue-50/70 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/50 space-y-1">
                <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400 uppercase">Đang giao hàng</span>
                <p class="text-xl font-black text-blue-700 dark:text-blue-300">{{ $counts['shipping'] }}</p>
            </div>
            <div class="p-4 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/50 space-y-1">
                <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 uppercase">Giao thành công</span>
                <p class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ $counts['delivered'] }}</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 text-xs font-bold flex items-center gap-2 shadow-sm">
            <span>✓</span> <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 text-xs font-bold flex items-center gap-2 shadow-sm">
            <span>⚠️</span> <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Thanh Tìm Kiếm & Tabs Trạng Thái -->
    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
        
        <!-- Search bar -->
        <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div class="relative flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="🔍 Tìm theo mã đơn, mã GHN (VD: GHN..., PS-...) hoặc tên sản phẩm..."
                       class="w-full pl-9 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                <span class="absolute left-3 top-2.5 text-slate-400">📦</span>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold text-xs rounded-2xl transition cursor-pointer">
                Tìm Kiếm
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('orders.index') }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300 font-bold text-xs rounded-2xl transition flex items-center">
                    Đặt lại
                </a>
            @endif
        </form>

        <!-- Status Tabs -->
        @php
            $currentStatus = request('status', 'all');
            $tabs = [
                'all'       => ['label' => 'Tất Cả', 'count' => $counts['all']],
                'pending'   => ['label' => 'Chờ Xử Lý', 'count' => $counts['pending']],
                'shipping'  => ['label' => 'Đang Vận Chuyển', 'count' => $counts['shipping']],
                'delivered' => ['label' => 'Đã Giao Hàng', 'count' => $counts['delivered']],
                'cancelled' => ['label' => 'Đã Hủy', 'count' => $counts['cancelled']],
            ];
        @endphp

        <div class="flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none text-xs font-bold border-t border-slate-100 dark:border-slate-800 pt-3">
            @foreach($tabs as $key => $tab)
                @php
                    $isActive = ($currentStatus === $key);
                    $url = route('orders.index', array_merge(request()->query(), ['status' => $key]));
                @endphp
                <a href="{{ $url }}" 
                   class="px-4 py-2 rounded-full whitespace-nowrap transition flex items-center gap-1.5 {{ $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-500/25 font-black' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $isActive ? 'bg-white/20 text-white' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Danh Sách Đơn Hàng -->
    <div class="space-y-4">
        @forelse($orders as $order)
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-card-hover transition duration-300 space-y-4">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="font-mono font-black text-sm text-blue-600 dark:text-blue-400">#{{ $order->order_code }}</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">• {{ $order->created_at->format('d/m/Y H:i') }}</span>
                    
                    @if($order->ghn_order_code)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-orange-50 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800 text-orange-700 dark:text-orange-400 text-[10px] font-black">
                        <span>⚡ GHN:</span> <strong class="font-mono">{{ $order->ghn_order_code }}</strong>
                    </span>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->payment_method === 'momo' ? 'bg-pink-50 dark:bg-pink-950/60 text-pink-700 dark:text-pink-300 border-pink-200 dark:border-pink-800' : 'bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700' }}">
                        {{ $order->payment_method === 'momo' ? 'Ví MoMo' : ($order->payment_method === 'cod' ? 'Tiền mặt COD' : 'VietQR') }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->payment_status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' }}">
                        {{ $order->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $order->shipping_status_badge }}">
                        {{ $order->shipping_status_text }}
                    </span>
                </div>
            </div>

            <!-- Items -->
            <div class="space-y-3">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between text-xs text-slate-700 dark:text-slate-300">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 flex items-center justify-center p-1 flex-shrink-0">
                            @if($item->image)
                                <img src="{{ asset('storage/' . $item->image) }}" class="max-h-full object-contain" alt="">
                            @else
                                <span class="text-xl">📱</span>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-slate-900 dark:text-white truncate text-xs">{{ $item->product_name }}</h4>
                            <div class="flex items-center gap-2 mt-0.5 text-[11px]">
                                @if($item->variant_label)
                                    <span class="text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.5 rounded text-[10px]">
                                        {{ $item->variant_label }}
                                    </span>
                                @endif
                                <span class="text-slate-400 dark:text-slate-500">Số lượng: <strong>x{{ $item->quantity }}</strong></span>
                            </div>
                        </div>
                    </div>
                    <span class="font-black text-slate-900 dark:text-white ml-2 flex-shrink-0">{{ number_format($item->price * $item->quantity) }} đ</span>
                </div>
                @endforeach
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3.5 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div class="text-slate-500 dark:text-slate-400 space-y-0.5">
                    <p>📍 Nhận hàng tại: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $order->shipping_address }}</span></p>
                    <p>🚚 Phí vận chuyển: <strong class="text-emerald-600 dark:text-emerald-400">{{ $order->shipping_fee == 0 ? 'Miễn phí (0đ)' : number_format($order->shipping_fee).' đ' }}</strong> (GHN Express)</p>
                </div>

                <div class="flex flex-wrap items-center justify-between sm:justify-end gap-2.5 pt-2 sm:pt-0">
                    <div class="text-left sm:text-right mr-2">
                        <span class="text-[11px] text-slate-400 block font-bold">Tổng thanh toán:</span>
                        <span class="text-base font-black text-rose-600 dark:text-rose-400">{{ number_format($order->total_amount) }} đ</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        @if($order->payment_method === 'momo' && $order->payment_status !== 'paid' && $order->shipping_status !== 'cancelled')
                        <a href="{{ route('orders.momo.pay', $order->id) }}" 
                           class="px-3.5 py-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-black rounded-xl shadow-md shadow-pink-500/25 transition cursor-pointer flex items-center gap-1.5">
                            <span class="text-sm">💳</span> Thanh Toán MoMo
                        </a>
                        @endif

                        @if(in_array($order->shipping_status, ['pending', 'preparing']))
                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng [#{{ $order->order_code }}] không?\n\n• Hệ thống sẽ tự động hủy vận đơn trên GHN Express\n• Hoàn lại số lượng tồn kho sản phẩm');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-3.5 py-2 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-xl border border-rose-200 dark:border-rose-800 transition cursor-pointer">
                                ✕ Hủy Đơn
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('orders.show', $order->id) }}" 
                           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/20 transition cursor-pointer flex items-center gap-1.5">
                            <span>📍</span> Chi Tiết & Lộ Trình
                        </a>
                    </div>
                </div>
            </div>

        </div>
        @empty
        <div class="bg-white dark:bg-slate-900 p-12 rounded-3xl border border-slate-200/80 dark:border-slate-800 text-center space-y-3">
            <span class="text-4xl">📦</span>
            <h3 class="text-base font-black text-slate-900 dark:text-white">Không tìm thấy đơn hàng nào</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Không có đơn hàng nào phù hợp với bộ lọc hiện tại của bạn.</p>
            <a href="{{ route('home') }}" class="inline-block px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-full shadow transition mt-2">
                Mua Sắm Ngay
            </a>
        </div>
        @endforelse

        <div class="pt-4">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
