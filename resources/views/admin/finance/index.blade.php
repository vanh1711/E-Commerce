@extends('layouts.admin')

@section('title', 'Thống Kê Tài Chính - Admin PhoneStore')
@section('page_title', 'Tài chính')
@section('page_heading', 'Báo Cáo & Thống Kê Tài Chính')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Navigation Tabs -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white dark:bg-[#0c1322] p-4 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.finance.index') }}" 
               class="px-4 py-2 rounded-2xl bg-blue-600 text-white font-black text-xs shadow-md shadow-blue-600/25 flex items-center gap-2">
                <span>📈</span>
                <span>Tổng Quan Tài Chính</span>
            </a>
            <a href="{{ route('admin.finance.transactions') }}" 
               class="px-4 py-2 rounded-2xl bg-slate-100 dark:bg-slate-800/80 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition flex items-center gap-2">
                <span>💳</span>
                <span>Danh Sách Giao Dịch</span>
            </a>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" 
                    class="px-3.5 py-2 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5 cursor-pointer">
                <span>📥</span>
                <span>Xuất Báo Cáo</span>
            </button>
        </div>
    </div>

    <!-- Bộ Lọc Nâng Cao (Finance Filter Form) -->
    <div class="bg-white dark:bg-[#0c1322] p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors">
        <form id="financeFilterForm" action="{{ route('admin.finance.index') }}" method="GET" class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider flex items-center gap-2">
                    <span>🔍</span> Bộ Lọc Thống Kê Tài Chính
                </h3>
                @if(request()->anyFilled(['search', 'date_from', 'date_to', 'min_amount', 'max_amount', 'gateway', 'payment_status']))
                    <a href="{{ route('admin.finance.index') }}" class="text-xs text-rose-500 hover:underline font-bold">
                        Xóa tất cả bộ lọc ✕
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                
                <!-- Tìm kiếm tổng hợp -->
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
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Phương thức thanh toán</label>
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

                <!-- Số tiền từ -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Số tiền tối thiểu (đ)</label>
                    <input type="number" name="min_amount" value="{{ request('min_amount') }}" placeholder="0" min="0" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500">
                </div>

                <!-- Số tiền đến -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-1">Số tiền tối đa (đ)</label>
                    <input type="number" name="max_amount" value="{{ request('max_amount') }}" placeholder="Ví dụ: 50.000.000" min="0" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-blue-500">
                </div>

                <!-- Nút thao tác áp dụng -->
                <div class="flex items-end gap-2">
                    <button type="submit" 
                            class="flex-1 py-2 px-4 bg-blue-600 hover:bg-blue-500 text-white font-black text-xs rounded-xl shadow-md shadow-blue-600/25 transition cursor-pointer">
                        Áp dụng lọc
                    </button>
                    <a href="{{ route('admin.finance.index') }}" title="Làm mới"
                       class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition flex items-center justify-center">
                        🔄
                    </a>
                </div>

            </div>
        </form>
    </div>

    <!-- KPI Summary Metric Cards (Chỉ số tài chính chủ chốt) -->
    @php
        $totalOrdersCount = $summary->order_count ?? 0;
        $totalGrossAmount = $summary->total_amount ?? 0;
        $paidAmount = $statusTotals->get('paid')->total_amount ?? 0;
        $pendingAmount = $statusTotals->get('pending')->total_amount ?? 0;
        $refundAmount = ($statusTotals->get('refund_pending')->total_amount ?? 0) + ($statusTotals->get('refunded')->total_amount ?? 0);
        $paidOrdersCount = $statusTotals->get('paid')->order_count ?? 0;
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Tổng Doanh Thu Lọc Được -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-5 rounded-3xl shadow-xl shadow-blue-600/20 relative overflow-hidden">
            <div class="relative z-10 space-y-1">
                <span class="text-[11px] font-black uppercase text-blue-100 tracking-wider">Tổng Doanh Thu</span>
                <h3 class="text-2xl font-black">{{ number_format($totalGrossAmount, 0, ',', '.') }} <span class="text-sm font-normal">đ</span></h3>
                <p class="text-[11px] text-blue-200 pt-1 font-medium">{{ $totalOrdersCount }} đơn hàng trong kỳ lọc</p>
            </div>
            <div class="absolute -right-3 -bottom-3 text-7xl opacity-10 pointer-events-none">💰</div>
        </div>

        <!-- Card 2: Doanh Thu Thực Thu (Đã Thanh Toán) -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden transition-colors">
            <div class="relative z-10 space-y-1">
                <span class="text-[11px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">Thực Thu (Đã Thanh Toán)</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($paidAmount, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">đ</span></h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 pt-1 font-medium">{{ $paidOrdersCount }} đơn thanh toán thành công</p>
            </div>
            <div class="absolute -right-3 -bottom-3 text-7xl opacity-5 pointer-events-none">✅</div>
        </div>

        <!-- Card 3: Đang Chờ Thanh Toán (COD / Pending) -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden transition-colors">
            <div class="relative z-10 space-y-1">
                <span class="text-[11px] font-black uppercase text-amber-600 dark:text-amber-400 tracking-wider">Chờ Thu Tiền (COD / Pending)</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($pendingAmount, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">đ</span></h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 pt-1 font-medium">{{ $statusTotals->get('pending')->order_count ?? 0 }} đơn đang chờ xử lý thu</p>
            </div>
            <div class="absolute -right-3 -bottom-3 text-7xl opacity-5 pointer-events-none">⏳</div>
        </div>

        <!-- Card 4: Hoàn Tiền / Hủy Đơn -->
        <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden transition-colors">
            <div class="relative z-10 space-y-1">
                <span class="text-[11px] font-black uppercase text-purple-600 dark:text-purple-400 tracking-wider">Hoàn Tiền & Khiếu Nại</span>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ number_format($refundAmount, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">đ</span></h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 pt-1 font-medium">Bao gồm chờ hoàn và đã hoàn tiền</p>
            </div>
            <div class="absolute -right-3 -bottom-3 text-7xl opacity-5 pointer-events-none">🔄</div>
        </div>

    </div>

    <!-- 2 Bảng Thống Kê Phân Tích Chi Tiết -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
        
        <!-- BẢNG 1: THỐNG KÊ THEO TRẠNG THÁI THANH TOÁN -->
        <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📊</span> Thống Kê Theo Trạng Thái Thanh Toán
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Phân bổ giá trị và số lượng đơn hàng theo từng trạng thái</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <thead>
                        <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Trạng Thái</th>
                            <th class="py-3.5 px-4 text-center">Số Đơn</th>
                            <th class="py-3.5 px-4 text-right">Tổng Tiền</th>
                            <th class="py-3.5 px-5 text-right">Tỷ Trọng</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @foreach($statuses as $statusKey => $statusLabel)
                            @php
                                $item = $statusTotals->get($statusKey);
                                $count = $item ? $item->order_count : 0;
                                $amount = $item ? $item->total_amount : 0;
                                $pct = ($totalGrossAmount > 0) ? round(($amount / $totalGrossAmount) * 100, 1) : 0;

                                $badgeClass = match($statusKey) {
                                    'paid'           => 'bg-emerald-500 text-white',
                                    'pending'        => 'bg-amber-500 text-white',
                                    'initiated'      => 'bg-blue-600 text-white',
                                    'failed'         => 'bg-rose-600 text-white',
                                    'refund_pending' => 'bg-purple-600 text-white',
                                    'refunded'       => 'bg-purple-700 text-white',
                                    'cancelled'      => 'bg-slate-600 text-white',
                                    default          => 'bg-slate-500 text-white',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-5">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $count }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white">
                                    {{ number_format($amount, 0, ',', '.') }} đ
                                </td>
                                <td class="py-3.5 px-5 text-right font-mono text-slate-500">
                                    {{ $pct }}%
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/80 dark:bg-slate-800/60 font-black text-slate-900 dark:text-white border-t border-slate-200 dark:border-slate-700">
                        <tr>
                            <td class="py-3.5 px-5">TỔNG CỘNG</td>
                            <td class="py-3.5 px-4 text-center">{{ $totalOrdersCount }}</td>
                            <td class="py-3.5 px-4 text-right">{{ number_format($totalGrossAmount, 0, ',', '.') }} đ</td>
                            <td class="py-3.5 px-5 text-right">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- BẢNG 2: THỐNG KÊ THEO PHƯƠNG THỨC THANH TOÁN (GATEWAY) -->
        <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors">
            <div class="p-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>💳</span> Thống Kê Theo Cổng Thanh Toán (Gateway)
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">So sánh hiệu suất giữa COD, MoMo và các phương thức khác</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    <thead>
                        <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-5">Phương Thức</th>
                            <th class="py-3.5 px-4 text-center">Số Đơn</th>
                            <th class="py-3.5 px-4 text-right">Tổng Tiền Đơn</th>
                            <th class="py-3.5 px-5 text-right">Đã Thu (Paid)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium">
                        @foreach($methods as $methodKey => $methodLabel)
                            @php
                                $item = $methodTotals->get($methodKey);
                                $count = $item ? $item->order_count : 0;
                                $amount = $item ? $item->total_amount : 0;
                                $paid = $item ? $item->paid_amount : 0;

                                $icon = match($methodKey) {
                                    'cod'           => '💵',
                                    'momo'          => '📱',
                                    'bank_transfer' => '🏦',
                                    'card'          => '💳',
                                    default         => '❓',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/30 transition">
                                <td class="py-3.5 px-5 font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                    <span>{{ $icon }}</span>
                                    <span>{{ $methodLabel }}</span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-slate-700 dark:text-slate-300">
                                    {{ $count }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-black text-slate-900 dark:text-white">
                                    {{ number_format($amount, 0, ',', '.') }} đ
                                </td>
                                <td class="py-3.5 px-5 text-right font-black text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($paid, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-slate-50/80 dark:bg-slate-800/60 font-black text-slate-900 dark:text-white border-t border-slate-200 dark:border-slate-700">
                        <tr>
                            <td class="py-3.5 px-5">TỔNG CỘNG</td>
                            <td class="py-3.5 px-4 text-center">{{ $totalOrdersCount }}</td>
                            <td class="py-3.5 px-4 text-right">{{ number_format($totalGrossAmount, 0, ',', '.') }} đ</td>
                            <td class="py-3.5 px-5 text-right text-emerald-600 dark:text-emerald-400">{{ number_format($paidAmount, 0, ',', '.') }} đ</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
