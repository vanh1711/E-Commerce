@extends('layouts.admin')

@section('title', 'Danh Sách Giao Dịch Thanh Toán - Admin PhoneStore')
@section('page_title', 'Giao dịch thanh toán')
@section('page_heading', 'Giao Dịch Thanh Toán & Cập Nhật COD')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Navigation Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-[#0c1322] p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.finance.index') }}" 
               class="px-4 py-2 rounded-2xl bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-2">
                <span>📈</span>
                <span>Tổng Quan Tài Chính</span>
            </a>
            <a href="{{ route('admin.finance.transactions') }}" 
               class="px-4 py-2 rounded-2xl bg-blue-600 text-white font-black text-xs shadow-md shadow-blue-600/25 flex items-center gap-2">
                <span>💳</span>
                <span>Danh Sách Giao Dịch</span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" 
                    class="px-3.5 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 cursor-pointer">
                <span>📥</span>
                <span>Xuất Danh Sách</span>
            </button>
        </div>
    </div>

    <!-- Bộ Lọc Nâng Cao (Transactions Filter Form) -->
    <div class="bg-white dark:bg-[#0c1322] p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors">
        <form id="transFilterForm" action="{{ route('admin.finance.transactions') }}" method="GET" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider flex items-center gap-2">
                    <span>🔍</span> Bộ Lọc Giao Dịch
                </h3>
                @if(request()->anyFilled(['search', 'date_from', 'date_to', 'min_amount', 'max_amount', 'gateway', 'payment_status', 'sort']))
                    <a href="{{ route('admin.finance.transactions') }}" class="text-xs text-rose-500 hover:underline font-bold">
                        Xóa tất cả bộ lọc ✕
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                <!-- Tìm kiếm -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Tìm kiếm</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Mã đơn, tên, SĐT..." 
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-blue-500">
                        <span class="absolute left-2.5 top-2.5 text-slate-400 text-xs">🔍</span>
                    </div>
                </div>

                <!-- Từ ngày -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500">
                </div>

                <!-- Đến ngày -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500">
                </div>

                <!-- Cổng thanh toán (Gateway) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Phương thức</label>
                    <select name="gateway" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">Tất cả phương thức</option>
                        @foreach($methods as $gKey => $gLabel)
                            <option value="{{ $gKey }}" {{ request('gateway') === $gKey ? 'selected' : '' }}>{{ $gLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Trạng thái thanh toán -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Trạng thái thanh toán</label>
                    <select name="payment_status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500 cursor-pointer">
                        <option value="">Tất cả trạng thái</option>
                        @foreach($statuses as $stKey => $stLabel)
                            <option value="{{ $stKey }}" {{ request('payment_status') === $stKey ? 'selected' : '' }}>{{ $stLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Sắp xếp -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Sắp xếp theo</label>
                    <select name="sort" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500 cursor-pointer">
                        <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Mới nhất trước</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất trước</option>
                        <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Giá trị: Cao → Thấp</option>
                        <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Giá trị: Thấp → Cao</option>
                    </select>
                </div>

                <!-- Số tiền từ -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Số tiền tối thiểu (đ)</label>
                    <input type="number" name="min_amount" value="{{ request('min_amount') }}" placeholder="0" min="0" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500">
                </div>

                <!-- Nút thao tác áp dụng -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs rounded-xl shadow-md shadow-blue-600/25 transition cursor-pointer">
                        Lọc kết quả
                    </button>
                    <a href="{{ route('admin.finance.transactions') }}" title="Làm mới bộ lọc"
                       class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition flex items-center justify-center">
                        🔄
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- Bảng Danh Sách Giao Dịch Chi Tiết -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
        
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>💳</span> Danh Sách Giao Dịch Thanh Toán
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Hiển thị {{ $orders->total() }} giao dịch khớp với bộ lọc</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-12 text-center">ID</th>
                        <th class="py-3.5 px-4">Mã Đơn Hàng</th>
                        <th class="py-3.5 px-4">Khách Hàng</th>
                        <th class="py-3.5 px-4">Phương Thức</th>
                        <th class="py-3.5 px-4 text-right">Tổng Tiền</th>
                        <th class="py-3.5 px-4">Trạng Thái Thanh Toán</th>
                        <th class="py-3.5 px-4">Thời Gian</th>
                        <th class="py-3.5 px-4 text-center">Cập Nhật COD</th>
                        <th class="py-3.5 px-4 text-right">Chi Tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                    @forelse($orders as $order)
                        @php
                            $isCod = ($order->gateway === 'cod');
                            $badgeClass = match($order->payment_status) {
                                'paid'           => 'bg-emerald-500 text-white',
                                'pending'        => 'bg-amber-500 text-white',
                                'initiated'      => 'bg-blue-600 text-white',
                                'failed'         => 'bg-rose-600 text-white',
                                'refund_pending' => 'bg-purple-600 text-white',
                                'refunded'       => 'bg-purple-700 text-white',
                                'cancelled'      => 'bg-slate-600 text-white',
                                default          => 'bg-slate-500 text-white',
                            };

                            $allowedTransitions = $codTransitions[$order->payment_status] ?? [];
                        @endphp
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                            
                            <!-- ID -->
                            <td class="py-4 px-4 text-center font-mono text-slate-400 font-bold">
                                #{{ $order->id }}
                            </td>

                            <!-- Mã Đơn Hàng -->
                            <td class="py-4 px-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}" 
                                   class="font-mono font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $order->order_code ?? ('DH' . $order->id) }}
                                </a>
                                @if(!empty($order->ghn_order_code))
                                    <span class="block text-[10px] text-slate-400 font-mono">GHN: {{ $order->ghn_order_code }}</span>
                                @endif
                            </td>

                            <!-- Khách Hàng -->
                            <td class="py-4 px-4">
                                <p class="font-bold text-slate-900 dark:text-white">{{ $order->customer_name ?? $order->name }}</p>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $order->customer_phone ?? $order->phone }}</span>
                            </td>

                            <!-- Phương Thức (Gateway) -->
                            <td class="py-4 px-4">
                                @if($order->gateway === 'momo')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-pink-100 dark:bg-pink-950/80 text-pink-700 dark:text-pink-300 font-bold text-[10px]">
                                        <span>📱</span> MoMo
                                    </span>
                                @elseif($order->gateway === 'cod')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 font-bold text-[10px]">
                                        <span>💵</span> COD
                                    </span>
                                @elseif($order->gateway === 'bank_transfer')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 dark:bg-blue-950/80 text-blue-700 dark:text-blue-300 font-bold text-[10px]">
                                        <span>🏦</span> VietQR
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-bold text-[10px]">
                                        {{ $methods[$order->gateway] ?? $order->gateway }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tổng Tiền -->
                            <td class="py-4 px-4 text-right font-black text-slate-900 dark:text-white">
                                {{ number_format($order->total_price ?? $order->total_amount, 0, ',', '.') }}
                                <span class="text-[10px] font-normal text-slate-400">đ</span>
                            </td>

                            <!-- Trạng Thái Thanh Toán -->
                            <td class="py-4 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $statuses[$order->payment_status] ?? $order->payment_status }}
                                </span>
                            </td>

                            <!-- Thời Gian -->
                            <td class="py-4 px-4 text-[11px]">
                                <p class="text-slate-700 dark:text-slate-300 font-semibold">{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}</p>
                                @if($order->paid_at)
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-mono">Đã thu: {{ \Carbon\Carbon::parse($order->paid_at)->format('d/m H:i') }}</span>
                                @endif
                            </td>

                            <!-- Cập Nhật COD Thủ Công -->
                            <td class="py-4 px-4 text-center">
                                @if($isCod && count($allowedTransitions) > 0)
                                    <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="inline-flex items-center gap-1.5"
                                          onsubmit="return confirm('Bạn có chắc muốn chuyển đổi trạng thái thanh toán đơn COD #{{ $order->order_code ?? $order->id }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="current_payment_status" value="{{ $order->payment_status }}">
                                        <input type="hidden" name="current_order_status" value="{{ $order->payment_status }}">
                                        <input type="hidden" name="current_payment_id" value="{{ (int) ($order->payment_id ?? 0) }}">

                                        <select name="payment_status" 
                                                class="py-1 px-2 text-[11px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-blue-500 cursor-pointer">
                                            @foreach($allowedTransitions as $transKey)
                                                <option value="{{ $transKey }}" {{ $transKey === $order->payment_status ? 'selected' : '' }}>
                                                    {{ $statuses[$transKey] ?? $transKey }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <button type="submit" title="Lưu thay đổi" 
                                                class="p-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-white font-black text-xs transition cursor-pointer">
                                            💾
                                        </button>
                                    </form>
                                @else
                                    <span class="text-[10px] text-slate-400 font-medium">Tự động / Khóa</span>
                                @endif
                            </td>

                            <!-- Chi Tiết -->
                            <td class="py-4 px-4 text-right">
                                <a href="{{ route('admin.orders.show', $order->id) }}" title="Xem chi tiết đơn hàng"
                                   class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition inline-block">
                                    👁️
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                Không có giao dịch thanh toán nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân Trang (Bottom Pagination) -->
        @if($orders->total() > 0)
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span class="text-slate-500 dark:text-slate-400">
                Hiển thị {{ $orders->firstItem() }}–{{ $orders->lastItem() }} trong tổng số {{ $orders->total() }} giao dịch
            </span>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif

    </div>

</div>
@endsection
