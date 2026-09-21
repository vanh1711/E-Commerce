@extends('layouts.admin')

@section('title', 'Biểu Đồ Báo Cáo Doanh Thu - Admin PhoneStore')
@section('page_title', 'Biểu Đồ')
@section('page_heading', 'Phân Tích Doanh Thu Trực Quan')

@section('content')
<div class="space-y-6">

    <!-- Header & Navigation Pills -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>📊</span> Biểu Đồ Thống Kê
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Biểu Đồ Phân Tích Doanh Thu</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng. Số liệu theo danh mục không gồm phí vận chuyển.</p>
        </div>

        <!-- Navigation Tabs: Bảng số liệu vs Biểu đồ -->
        <div class="inline-flex p-1.5 bg-slate-100 dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700/80 self-start md:self-auto">
            <a href="{{ route('admin.reports.index') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400">
                📊 Bảng Số Liệu
            </a>
            <a href="{{ route('admin.reports.charts') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-black transition-all duration-200 bg-blue-600 text-white shadow-md shadow-blue-600/30">
                📈 Biểu Đồ Trực Quan
            </a>
        </div>
    </div>

    <!-- Error container if Chart.js fails -->
    <div id="report-chart-error" class="hidden p-4 rounded-3xl bg-amber-50 dark:bg-amber-950/60 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs font-bold">
        ⚠️ Không thể tải thư viện biểu đồ. Bạn có thể chuyển sang xem số liệu tại <a href="{{ route('admin.reports.index') }}" class="underline font-black">Bảng số liệu</a>.
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- 1. Doanh thu theo danh mục -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between transition-colors duration-300">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📁</span> Doanh Thu Theo Danh Mục
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Doanh số từng nhóm sản phẩm</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-500/10 text-blue-500">Bar Chart</span>
            </div>
            <div class="h-80 relative w-full">
                <canvas id="categoryRevenueChart"></canvas>
            </div>
        </div>

        <!-- 2. Doanh thu 30 ngày gần nhất -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between transition-colors duration-300">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📅</span> Doanh Thu Theo Ngày (30 Ngày Gần Nhất)
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Xu hướng biến động từng ngày</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-500">Area Line Chart</span>
            </div>
            <div class="h-80 relative w-full">
                <canvas id="revenueByDateChart"></canvas>
            </div>
        </div>

        <!-- 3. Doanh thu 12 tháng -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between transition-colors duration-300">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📆</span> Doanh Thu Theo Tháng (12 Tháng)
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Hiệu suất kinh doanh theo tháng</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-purple-500/10 text-purple-500">Bar Chart</span>
            </div>
            <div class="h-80 relative w-full">
                <canvas id="revenueByMonthChart"></canvas>
            </div>
        </div>

        <!-- 4. Doanh thu theo năm -->
        <div class="bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between transition-colors duration-300">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>📈</span> Doanh Thu Theo Năm
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Tăng trưởng dài hạn</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-500">Bar Chart</span>
            </div>
            <div class="h-80 relative w-full">
                <canvas id="revenueByYearChart"></canvas>
            </div>
        </div>

        <!-- 5. Tỷ trọng Phương Thức Thanh Toán -->
        <div class="lg:col-span-2 bg-white dark:bg-[#0c1322] p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>💳</span> Doanh Thu Theo Phương Thức Thanh Toán
                    </h3>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Tỷ trọng tiền vào qua Ví MoMo vs Tiền mặt COD</p>
                </div>
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-pink-500/10 text-pink-500">Pie / Doughnut Chart</span>
            </div>
            <div class="h-80 relative w-full flex items-center justify-center">
                <canvas id="revenueByPaymentMethodChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Data Container -->
<div id="report-chart-data" hidden data-chart-data="{{ json_encode([
    'catLabels'            => $catLabels ?? [],
    'catRevenue'           => $catRevenue ?? [],
    'revDateLabels'        => $revDateLabels ?? [],
    'revDateData'          => $revDateData ?? [],
    'revMonthLabels'       => $revMonthLabels ?? [],
    'revMonthData'         => $revMonthData ?? [],
    'revYearLabels'        => $revYearLabels ?? [],
    'revYearData'          => $revYearData ?? [],
    'paymentMethodLabels'  => $paymentMethodLabels ?? [],
    'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
]) }}"></div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.addEventListener('DOMContentLoaded', () => {
    if (typeof Chart === 'undefined') {
        const errEl = document.getElementById('report-chart-error');
        if (errEl) errEl.classList.remove('hidden');
        return;
    }

    const dataEl = document.getElementById('report-chart-data');
    if (!dataEl) return;

    const reportData = JSON.parse(dataEl.dataset.chartData);

    const isDark = document.documentElement.classList.contains('dark');
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)';
    const textColor = isDark ? '#94a3b8' : '#64748b';

    Chart.defaults.color = textColor;
    Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
    Chart.defaults.font.size = 11;

    // Helper tạo Bar / Line chart
    const createChart = (elId, type, labels, data, label, colorHex) => {
        const ctx = document.getElementById(elId);
        if (!ctx) return;

        const isLine = type === 'line';

        new Chart(ctx, {
            type: type,
            data: {
                labels: labels,
                datasets: [{
                    label: label,
                    data: data,
                    backgroundColor: isLine ? `${colorHex}22` : `${colorHex}cc`,
                    borderColor: colorHex,
                    borderWidth: 2,
                    fill: isLine,
                    tension: 0.35,
                    pointBackgroundColor: colorHex,
                    pointRadius: isLine ? 3 : 0,
                    borderRadius: isLine ? 0 : 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#0f172a' : '#1e293b',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' VNĐ';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000).toFixed(1) + 'M';
                                if (value >= 1000) return (value / 1000).toFixed(0) + 'k';
                                return value;
                            }
                        }
                    }
                }
            }
        });
    };

    // 1. Danh mục
    createChart('categoryRevenueChart', 'bar', reportData.catLabels, reportData.catRevenue.map(Number), 'Doanh thu (VNĐ)', '#3b82f6');

    // 2. 30 ngày
    createChart('revenueByDateChart', 'line', reportData.revDateLabels, reportData.revDateData.map(Number), 'Doanh thu (VNĐ)', '#10b981');

    // 3. 12 tháng
    createChart('revenueByMonthChart', 'bar', reportData.revMonthLabels, reportData.revMonthData.map(Number), 'Doanh thu (VNĐ)', '#a855f7');

    // 4. Năm
    createChart('revenueByYearChart', 'bar', reportData.revYearLabels, reportData.revYearData.map(Number), 'Doanh thu (VNĐ)', '#6366f1');

    // 5. Pie chart Phương thức thanh toán
    const pieCtx = document.getElementById('revenueByPaymentMethodChart');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: reportData.paymentMethodLabels,
                datasets: [{
                    data: reportData.paymentMethodRevenue.map(Number),
                    backgroundColor: ['#ec4899', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 14,
                            padding: 20,
                            font: { size: 12, weight: '700' }
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#0f172a' : '#1e293b',
                        titleColor: '#fff',
                        bodyColor: '#e2e8f0',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.raw) + ' VNĐ';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>
@endsection
