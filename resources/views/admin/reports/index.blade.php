@extends('layouts.admin')

@section('title', 'Báo Cáo Doanh Thu & Chỉ Số - Admin PhoneStore')
@section('page_title', 'Báo Cáo')
@section('page_heading', 'Báo Cáo & Thống Kê Doanh Thu')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>📈</span> Quản Trị Tài Chính & Kinh Doanh
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Báo Cáo Doanh Thu</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng.</p>
        </div>

        <!-- Navigation Tabs: Bảng số liệu vs Biểu đồ -->
        <div class="inline-flex p-1.5 bg-slate-100 dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700/80 self-start md:self-auto">
            <a href="{{ route('admin.reports.index') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 bg-blue-600 text-white shadow-md shadow-blue-600/30">
                📊 Bảng Số Liệu
            </a>
            <a href="{{ route('admin.reports.charts') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400">
                📈 Biểu Đồ Trực Quan
            </a>
        </div>
    </div>

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Tổng đơn hàng -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-950/60 border border-blue-100 dark:border-blue-900 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl flex-shrink-0">
                📦
            </div>
            <div>
                <span class="text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Tổng Số Đơn Hàng</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalOrders) }}</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Bao gồm toàn bộ đơn đã tạo</span>
            </div>
        </div>

        <!-- Tổng khách hàng -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors duration-300">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 dark:bg-purple-950/60 border border-purple-100 dark:border-purple-900 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl flex-shrink-0">
                👥
            </div>
            <div>
                <span class="text-[11px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-wider">Tổng Số Khách Hàng</span>
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white mt-0.5">{{ number_format($totalCustomers) }}</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Tài khoản thành viên mua sắm</span>
            </div>
        </div>

        <!-- Tổng doanh thu -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center gap-4 transition-colors duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-100 dark:border-emerald-900 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-2xl flex-shrink-0">
                💰
            </div>
            <div>
                <span class="text-[11px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-wider">Tổng Doanh Thu Thực Thu</span>
                <h3 class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h3>
                <span class="text-[11px] text-slate-400 dark:text-slate-500">Đã bao gồm cước vận chuyển</span>
            </div>
        </div>
    </div>

    <!-- Bảng 1: Doanh Thu Theo Danh Mục -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <span>📁</span> Doanh Thu Theo Danh Mục Sản Phẩm
                </h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</p>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 self-start sm:self-auto">
                {{ count($categoryRevenue) }} danh mục có phát sinh
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Danh Mục</th>
                        <th class="py-3.5 px-6 text-right">Số Lượng Đã Bán</th>
                        <th class="py-3.5 px-6 text-right">Doanh Thu Thu Được</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($categoryRevenue as $revenue)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            {{ $revenue->category_name ?? ('Danh mục #' . $revenue->category_id) }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-700 dark:text-slate-300">
                            {{ number_format($revenue->total_qty) }} món
                        </td>
                        <td class="py-4 px-6 text-right font-black text-emerald-600 dark:text-emerald-400">
                            {{ number_format($revenue->total_revenue, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-slate-400 dark:text-slate-500">Chưa có dữ liệu doanh thu danh mục.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Các Bảng: Theo Ngày / Tháng / Năm -->
    @foreach([
        ['Doanh Thu Theo Ngày', 'Ngày Tạo Đơn', 'date', $revenueByDate, 'd/m/Y', '📅'],
        ['Doanh Thu Theo Tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y', '📆'],
        ['Doanh Thu Theo Năm', 'Năm', 'year', $revenueByYear, null, '📈'],
    ] as [$title, $label, $field, $rows, $format, $icon])
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>{{ $icon }}</span> {{ $title }}
            </h3>
            <span class="text-xs text-slate-400 dark:text-slate-500 font-bold">Tổng hợp chu kỳ</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ $label }}</th>
                        <th class="py-3.5 px-6 text-right">Số Đơn Đã Thu Tiền</th>
                        <th class="py-3.5 px-6 text-right">Doanh Thu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($rows as $revenue)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white font-mono">
                            {{ $format ? \Carbon\Carbon::parse($revenue->{$field} . ($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}
                        </td>
                        <td class="py-4 px-6 text-right font-black text-slate-700 dark:text-slate-300">
                            {{ number_format($revenue->order_count) }} đơn
                        </td>
                        <td class="py-4 px-6 text-right font-black text-emerald-600 dark:text-emerald-400">
                            {{ number_format($revenue->total_revenue, 0, ',', '.') }} đ
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-slate-400 dark:text-slate-500">Chưa có doanh thu trong chu kỳ này.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

</div>
@endsection
