@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 space-y-8">

    <!-- Card Thông Báo Đặt Hàng Thành Công -->
    <div class="bg-white dark:bg-slate-900 p-8 sm:p-10 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm text-center space-y-4 transition-colors duration-300">
        <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center text-4xl mx-auto shadow-inner animate-bounce">
            🎉
        </div>

        <div>
            <span class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-full border border-transparent dark:border-emerald-800">
                Đặt Hàng Thành Công
            </span>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-2">
                Cảm Ơn Bạn Đã Mua Hàng Tại PhoneStore!
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                Mã đơn hàng của bạn là: <strong class="font-mono text-blue-600 dark:text-blue-400 text-sm font-black">{{ $order->order_code }}</strong>
            </p>
        </div>

        <!-- ================= NẾU CHỌN CHUYỂN KHOẢN VIETQR NAPAS 24/7 ================= -->
        @if($order->payment_method === 'bank_transfer')
        <div class="mt-8 p-6 bg-gradient-to-b from-blue-50/80 dark:from-slate-800/80 to-white dark:to-slate-900 rounded-3xl border-2 border-blue-200 dark:border-slate-700 text-left space-y-5 shadow-lg transition-colors duration-300">
            
            <div class="flex items-center justify-between border-b border-blue-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📲</span>
                    <h3 class="font-black text-sm text-blue-950 dark:text-white">Quét Mã VietQR Napas 24/7 Để Thanh Toán</h3>
                </div>
                <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2.5 py-0.5 rounded-full border border-rose-200 dark:border-rose-800 animate-pulse">
                    ● Chờ thanh toán
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <!-- Cột Trái: Ảnh Mã QR Tạo Tự Động -->
                <div class="md:col-span-5 flex flex-col items-center justify-center bg-white dark:bg-slate-800 p-4 rounded-2xl border border-blue-100 dark:border-slate-700 shadow-md text-center">
                    <img src="{{ $vietQrUrl }}" alt="VietQR Napas 24/7 PhoneStore" 
                         class="max-w-[200px] w-full rounded-xl shadow-sm hover:scale-105 transition-transform duration-300">
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-bold mt-2 flex items-center gap-1 justify-center">
                        <span>⚡</span> Quét bằng App của bất kỳ ngân hàng nào
                    </p>
                </div>

                <!-- Cột Phải: Thông tin tài khoản chuyển khoản -->
                <div class="md:col-span-7 space-y-3 text-xs">
                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 space-y-1">
                        <span class="text-slate-400 dark:text-slate-500 font-medium">Chủ tài khoản:</span>
                        <p class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-wide">{{ $accountName }}</p>
                    </div>

                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 space-y-1">
                        <span class="text-slate-400 dark:text-slate-500 font-medium">Số tài khoản (Napas 24/7):</span>
                        <div class="flex items-center justify-between">
                            <p class="font-mono font-black text-base text-blue-600 dark:text-blue-400">{{ $accountNo }}</p>
                            <button type="button" onclick="navigator.clipboard.writeText('{{ $accountNo }}'); alert('Đã sao chép số tài khoản!')" 
                                    class="px-2 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-blue-50 text-[10px] font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 rounded-lg transition cursor-pointer">
                                Copy STK
                            </button>
                        </div>
                    </div>

                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 space-y-1">
                        <span class="text-slate-400 dark:text-slate-500 font-medium">Số tiền chuyển khoản:</span>
                        <p class="font-black text-base text-rose-600 dark:text-rose-400">{{ number_format($order->total_amount) }} đ</p>
                    </div>

                    <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 space-y-1">
                        <span class="text-slate-400 dark:text-slate-500 font-medium">Nội dung chuyển khoản (Bắt buộc):</span>
                        <div class="flex items-center justify-between">
                            <p class="font-mono font-black text-xs text-slate-900 dark:text-slate-100 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded border border-amber-200 dark:border-amber-800">
                                PS {{ $order->order_code }}
                            </p>
                            <button type="button" onclick="navigator.clipboard.writeText('PS {{ $order->order_code }}'); alert('Đã sao chép nội dung!')" 
                                    class="px-2 py-1 bg-slate-100 dark:bg-slate-700 hover:bg-blue-50 text-[10px] font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 rounded-lg transition cursor-pointer">
                                Copy cú pháp
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                <span>🛡️</span> 
                <span>Hệ thống sẽ tự động cập nhật trạng thái đơn hàng ngay sau khi nhận được tiền.</span>
            </div>
        </div>
        @endif

        <!-- Thông tin đơn hàng tóm tắt & Vận đơn GHN Express -->
        <div class="mt-6 p-6 bg-slate-50 dark:bg-slate-800/80 rounded-3xl border border-slate-100 dark:border-slate-800 text-left space-y-4 text-xs transition-colors duration-300">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 dark:border-slate-700 pb-3">
                <h3 class="font-black text-slate-900 dark:text-white text-sm flex items-center gap-2">
                    <span>📋</span> Thông Tin Nhận Hàng & Vận Đơn
                </h3>
                @if($order->ghn_order_code)
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800 text-orange-700 dark:text-orange-400 font-extrabold text-[11px]">
                    <span>⚡ GHN Express:</span>
                    <span class="font-mono font-black text-xs text-orange-600 dark:text-orange-300">{{ $order->ghn_order_code }}</span>
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-slate-600 dark:text-slate-400">
                <p>👤 Người nhận: <strong class="text-slate-800 dark:text-slate-200">{{ $order->customer_name }}</strong></p>
                <p>📞 Điện thoại: <strong class="text-slate-800 dark:text-slate-200">{{ $order->customer_phone }}</strong></p>
                <p class="sm:col-span-2">📍 Địa chỉ: <strong class="text-slate-800 dark:text-slate-200">{{ $order->shipping_address }}</strong></p>
                @if($order->province_name || $order->district_name || $order->ward_name)
                <p class="sm:col-span-2 text-slate-500 dark:text-slate-400">
                    🏛️ Khu vực: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ implode(' - ', array_filter([$order->ward_name, $order->district_name, $order->province_name])) }}</span>
                </p>
                @endif
                <p>🚚 Đơn vị vận chuyển: <strong class="text-orange-600 dark:text-orange-400 font-black">Giao Hàng Nhanh (GHN Express)</strong></p>
                <p>💰 Phí vận chuyển: <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $order->shipping_fee == 0 ? 'Miễn phí (0đ)' : number_format($order->shipping_fee) . ' đ' }}</strong></p>
                <p>💳 Hình thức: <strong class="text-slate-800 dark:text-slate-200">{{ $order->payment_method_text }}</strong></p>
                <p>💵 Tổng thanh toán: <strong class="text-rose-600 dark:text-rose-400 text-sm font-black">{{ number_format($order->total_amount) }} đ</strong></p>
            </div>
        </div>

        <!-- Nút hành động -->
        <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
            <a href="{{ route('orders.show', $order->id) }}" 
               class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-xs uppercase tracking-wider rounded-full shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 transition hover:scale-105 flex items-center gap-2 cursor-pointer">
                <span>📍 Theo Dõi Lộ Trình Ship Hàng</span>
            </a>

            <a href="{{ route('orders.index') }}" 
               class="px-8 py-3.5 bg-blue-50 dark:bg-slate-800 hover:bg-blue-100 dark:hover:bg-slate-700 text-blue-700 dark:text-blue-300 font-black text-xs uppercase tracking-wider rounded-full border border-blue-200 dark:border-slate-700 transition flex items-center gap-2 cursor-pointer">
                <span>📦 Danh Sách Đơn Hàng</span>
            </a>

            <a href="{{ route('home') }}" 
               class="px-8 py-3.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-full border border-transparent dark:border-slate-700 transition cursor-pointer">
                Tiếp Tục Mua Sắm
            </a>
        </div>

    </div>

</div>
@endsection
