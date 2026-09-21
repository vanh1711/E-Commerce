@extends('layouts.app')

@push('head')
<!-- Leaflet CSS & JS cho Bản đồ GPS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #map { height: 320px; width: 100%; border-radius: 1.5rem; z-index: 10; }
    .leaflet-popup-content-wrapper { border-radius: 1rem; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 12px; }
    /* 3D Card Flip Simulation */
    .card-flipper { perspective: 1000px; }
    .card-inner { transition: transform 0.6s; transform-style: preserve-3d; }
    .card-flipper.flipped .card-inner { transform: rotateY(180deg); }
    .card-front, .card-back { backface-visibility: hidden; }
    .card-back { transform: rotateY(180deg); }

    /* Cố định màu nền tối cho form input khi autofill hoặc focus */
    html.dark input,
    html.dark textarea,
    html.dark select {
        color-scheme: dark !important;
    }
    html.dark .checkout-input {
        background-color: #1e293b !important;
        color: #f8fafc !important;
        border-color: #334155 !important;
    }
    html.dark .checkout-input:focus {
        background-color: #1e293b !important;
        color: #ffffff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25) !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-8 max-w-7xl mx-auto py-2">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('cart.index') }}" class="hover:text-blue-600">Giỏ hàng</a>
        <span>/</span>
        <span class="text-blue-600 font-extrabold">Thanh toán & Đặt ship GPS</span>
    </div>

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center gap-2">
            <span>⚠️</span> <span>{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        
        <!-- Tọa độ GPS ẩn gửi lên server -->
        <input type="hidden" name="latitude" id="input_latitude" value="21.028511">
        <input type="hidden" name="longitude" id="input_longitude" value="105.854444">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- ================= CỘT TRÁI: THÔNG TIN GIAO HÀNG & BẢN ĐỒ GPS (7 CỘT) ================= -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- 1. THÔNG TIN KHÁCH HÀNG -->
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs">1</span>
                            Thông Tin Người Nhận
                        </h2>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Bảo mật thông tin 100%</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ và tên người nhận <span class="text-rose-500">*</span></label>
                            <input type="text" name="customer_name" required value="{{ old('customer_name', $user->name ?? '') }}"
                                   placeholder="Nguyễn Văn A"
                                   class="checkout-input w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Số điện thoại liên hệ <span class="text-rose-500">*</span></label>
                            <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}"
                                   placeholder="0987 654 321"
                                   class="checkout-input w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Địa chỉ Email (Nhận hóa đơn điện tử)</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $user->email ?? '') }}"
                               placeholder="email@example.com"
                               class="checkout-input w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>
                </div>

                <!-- Hidden inputs cho GHN Express & Tọa độ -->
                <input type="hidden" name="province_name" id="province_name" value="{{ old('province_name') }}">
                <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name') }}">
                <input type="hidden" name="ward_name" id="ward_name" value="{{ old('ward_name') }}">
                <input type="hidden" name="shipping_fee" id="shipping_fee_input" value="{{ old('shipping_fee', 0) }}">

                @if($savedAddresses->count() > 0)
                <!-- SỔ ĐỊA CHỈ ĐÃ LƯU -->
                <div class="bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span>📍</span> Sổ Địa Chỉ Đã Lưu
                        </h3>
                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Chọn 1-click để điền nhanh</span>
                    </div>
                    <div class="grid grid-cols-1 gap-2.5 max-h-48 overflow-y-auto pr-1">
                        @foreach($savedAddresses as $addr)
                        <button type="button" onclick="applySavedAddress({{ $addr->toJson() }})"
                                class="saved-addr-btn w-full text-left p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-blue-500 dark:hover:border-blue-400 bg-slate-50/50 dark:bg-slate-800/60 transition cursor-pointer group">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">{{ $addr->recipient_name }}</span>
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $addr->phone }}</span>
                                        @if($addr->is_default)
                                        <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.5 rounded-md">Mặc định</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">{{ $addr->full_address }}</p>
                                </div>
                                <span class="text-xs text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition ml-2">→</span>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- 2. ĐỊA CHỈ NHẬN HÀNG & TÍNH CƯỚC GHN EXPRESS -->
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black">2</span>
                            <div>
                                <h2 class="text-base font-black text-slate-900 dark:text-white">Địa Chỉ Nhận Hàng & Vận Chuyển</h2>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">Tích hợp mạng lưới vận chuyển GHN Express toàn quốc</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 text-xs font-extrabold border border-orange-200/60 dark:border-orange-900/60">
                                <span>⚡</span> GHN Express
                            </span>
                        </div>
                    </div>

                    <!-- 3 Dropdown Tỉnh / Huyện / Xã theo chuẩn GHN API -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Chọn Tỉnh / Thành Phố -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Tỉnh / Thành Phố <span class="text-rose-500">*</span>
                            </label>
                            <select name="to_province_id" id="province_select" required
                                    class="checkout-input w-full px-3.5 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                                <option value="">-- Chọn Tỉnh / Thành Phố --</option>
                            </select>
                        </div>

                        <!-- Chọn Quận / Huyện -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Quận / Huyện <span class="text-rose-500">*</span>
                            </label>
                            <select name="to_district_id" id="district_select" required disabled
                                    class="checkout-input w-full px-3.5 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                                <option value="">-- Chọn Quận / Huyện --</option>
                            </select>
                        </div>

                        <!-- Chọn Phường / Xã -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Phường / Xã <span class="text-rose-500">*</span>
                            </label>
                            <select name="to_ward_code" id="ward_select" required disabled
                                    class="checkout-input w-full px-3.5 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                                <option value="">-- Chọn Phường / Xã --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Ô nhập địa chỉ chi tiết -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Số nhà, tên đường, ngõ / thôn <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="shipping_address" id="input_address" required value="{{ old('shipping_address') }}"
                               placeholder="Ví dụ: Số 18 ngõ 24 đường Cầu Giấy, Tòa nhà Indochina Plaza..."
                               class="checkout-input w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                    </div>

                    <!-- Thẻ Thông Tin Gói Vận Chuyển GHN Express -->
                    <div class="p-4 rounded-2xl bg-gradient-to-br from-orange-50/80 via-white to-orange-50/40 dark:from-slate-800/90 dark:via-slate-800/60 dark:to-orange-950/20 border border-orange-200/80 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-orange-500 text-white flex items-center justify-center text-xl shadow-md flex-shrink-0">
                                🚚
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-xs text-slate-900 dark:text-white">Giao Hàng Nhanh (GHN Express)</h4>
                                    <span class="text-[10px] font-black text-orange-600 dark:text-orange-400 bg-orange-100 dark:bg-orange-950/80 px-2 py-0.5 rounded-full">Tiêu chuẩn</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5" id="shippingStatusNote">
                                    Vui lòng chọn Tỉnh, Huyện, Xã để tính cước vận chuyển chính xác
                                </p>
                            </div>
                        </div>

                        <div class="text-right flex sm:flex-col items-center sm:items-end justify-between border-t sm:border-t-0 border-orange-100 dark:border-slate-700 pt-2 sm:pt-0">
                            <span class="text-[10px] uppercase font-bold text-slate-400 dark:text-slate-500">Cước vận chuyển:</span>
                            <span id="displayShippingFee" class="text-sm font-black text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-xl inline-block mt-0.5 transition-all">
                                0 đ
                            </span>
                        </div>
                    </div>

                    <!-- Ghi chú đơn hàng -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ghi chú giao hàng (Tùy chọn)</label>
                        <input type="text" name="notes" value="{{ old('notes') }}"
                               placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến 15 phút..."
                               class="checkout-input w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none transition">
                    </div>

                    @auth
                    <!-- Checkbox lưu địa chỉ vào sổ -->
                    <div class="flex items-center gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="save_address" value="1" checked
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500/30">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Lưu địa chỉ này vào sổ địa chỉ</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="set_default_address" value="1"
                                   class="w-4 h-4 rounded border-slate-300 dark:border-slate-600 text-blue-600 focus:ring-blue-500/30">
                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-400">Đặt làm mặc định</span>
                        </label>
                    </div>
                    @endauth

                    <!-- Accordion Bản Đồ Định Vị GPS (Mở rộng tùy chọn) -->
                    <div class="pt-1">
                        <details class="group rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 p-3.5 transition-all">
                            <summary class="flex items-center justify-between cursor-pointer list-none text-xs font-bold text-slate-700 dark:text-slate-300">
                                <span class="flex items-center gap-2">
                                    <span>🗺️</span> Xem Bản Đồ Vị Trí & Tuyến Đường Dự Kiến
                                </span>
                                <span class="text-blue-600 dark:text-blue-400 group-open:rotate-180 transition-transform duration-200">▼</span>
                            </summary>
                            
                            <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 space-y-3">
                                <div class="flex gap-2">
                                    <input type="text" id="mapSearchInput" 
                                           onkeydown="if(event.key === 'Enter'){ event.preventDefault(); searchLocationOnMap(); }"
                                           placeholder="🔍 Tìm vị trí nhanh trên bản đồ..."
                                           class="checkout-input flex-1 px-3.5 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400">
                                    <button type="button" onclick="searchLocationOnMap()" class="px-4 py-2 bg-slate-900 dark:bg-blue-600 text-white font-bold text-xs rounded-xl cursor-pointer">
                                        Tìm
                                    </button>
                                    <button type="button" onclick="locateUserGPS()" class="px-3 py-2 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-bold text-xs rounded-xl cursor-pointer" title="Lấy GPS">
                                        📍 GPS
                                    </button>
                                </div>
                                <div id="map" class="w-full h-[280px] rounded-2xl border border-slate-200 dark:border-slate-700"></div>
                            </div>
                        </details>
                    </div>

                </div>

                <!-- 3. PHƯƠNG THỨC THANH TOÁN ĐA KÊNH (COD, MOMO, VIETQR, THẺ) -->
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                        <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black">3</span>
                            Chọn Phương Thức Thanh Toán
                        </h2>
                        <span class="text-[10px] font-bold text-slate-400">Lab 06 - COD & MoMo</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- Option 1: COD -->
                        <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-blue-500 bg-white dark:bg-slate-800/80 cursor-pointer transition payment-tab active" onclick="switchPayment('cod')">
                            <input type="radio" name="payment_method" value="cod" checked class="hidden">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl mb-3 shadow-inner">
                                    💵
                                </div>
                                <h3 class="font-extrabold text-xs text-slate-900 dark:text-white">Tiền Mặt (COD)</h3>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Nhận hàng rồi thanh toán</p>
                            </div>
                            <span class="mt-3 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full inline-block w-max">Tự động GHN</span>
                        </label>

                        <!-- Option 2: MoMo Sandbox Gateway (Lab 06) -->
                        <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-pink-500 bg-white dark:bg-slate-800/80 cursor-pointer transition payment-tab" onclick="switchPayment('momo')">
                            <input type="radio" name="payment_method" value="momo" class="hidden">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center text-xl mb-3 shadow-inner">
                                    <svg class="w-6 h-6" viewBox="0 0 40 40" fill="none">
                                        <rect width="40" height="40" rx="8" fill="#A50064"/>
                                        <path d="M12 25V15H15L18 21L21 15H24V25H21.5V18.5L18.8 23.5H17.2L14.5 18.5V25H12Z" fill="white"/>
                                        <circle cx="28" cy="20" r="3" fill="white"/>
                                    </svg>
                                </div>
                                <h3 class="font-extrabold text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                    Ví MoMo
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-pink-100 text-pink-700 font-bold">ATM/QR</span>
                                </h3>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Cổng thanh toán MoMo Sandbox</p>
                            </div>
                            <span class="mt-3 text-[10px] font-bold text-pink-600 dark:text-pink-400 bg-pink-50 dark:bg-pink-950/60 px-2 py-0.5 rounded-full inline-block w-max">MoMo Test v2</span>
                        </label>

                        <!-- Option 3: VietQR Napas 24/7 -->
                        <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-blue-500 bg-white dark:bg-slate-800/80 cursor-pointer transition payment-tab" onclick="switchPayment('bank_transfer')">
                            <input type="radio" name="payment_method" value="bank_transfer" class="hidden">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl mb-3 shadow-inner">
                                    📲
                                </div>
                                <h3 class="font-extrabold text-xs text-slate-900 dark:text-white">Chuyển Khoản QR</h3>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">VietQR Napas 24/7 tức thì</p>
                            </div>
                            <span class="mt-3 text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-full inline-block w-max">Mã QR động</span>
                        </label>

                        <!-- Option 4: Thẻ Quốc Tế -->
                        <label class="relative flex flex-col justify-between p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 hover:border-purple-500 bg-white dark:bg-slate-800/80 cursor-pointer transition payment-tab" onclick="switchPayment('card')">
                            <input type="radio" name="payment_method" value="card" class="hidden">
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl mb-3 shadow-inner">
                                    💳
                                </div>
                                <h3 class="font-extrabold text-xs text-slate-900 dark:text-white">Thẻ Quốc Tế</h3>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">Visa, Mastercard, JCB</p>
                            </div>
                            <span class="mt-3 text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded-full inline-block w-max">3D Secure</span>
                        </label>
                    </div>

                    <!-- Panel Hướng Dẫn & Thông Tin Test MoMo Sandbox (Lab 06) -->
                    <div id="momo_panel" class="hidden p-5 rounded-2xl bg-gradient-to-br from-pink-50 to-rose-50/50 dark:from-pink-950/40 dark:to-slate-900 border border-pink-200 dark:border-pink-900/50 space-y-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#A50064] text-white flex items-center justify-center font-black text-sm flex-shrink-0 shadow-md">
                                MoMo
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white">Thanh toán qua Cổng MoMo Sandbox (Môi trường Thử nghiệm)</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400">Sau khi bấm <strong>"Xác Nhận Đặt Hàng"</strong>, bạn sẽ được chuyển hướng sang giao diện MoMo để quẹt thẻ / nhập mã OTP.</p>
                            </div>
                        </div>

                        <!-- Bảng tài khoản thẻ test Sandbox từ Lab06 -->
                        <div class="overflow-x-auto rounded-xl border border-pink-200/80 dark:border-pink-900/40 bg-white/80 dark:bg-slate-900/80">
                            <div class="p-2.5 bg-pink-100/60 dark:bg-pink-950/60 font-black text-[10px] text-pink-900 dark:text-pink-300 uppercase tracking-wider flex items-center justify-between">
                                <span>📋 Thẻ Test MoMo Sandbox (Lab 06)</span>
                                <span class="text-[9px] font-medium lowercase">Click số thẻ để copy</span>
                            </div>
                            <table class="w-full text-left text-[11px]">
                                <thead>
                                    <tr class="border-b border-pink-100 dark:border-pink-900/30 text-slate-500 dark:text-slate-400 font-bold">
                                        <th class="py-1.5 px-3">Tên chủ thẻ</th>
                                        <th class="py-1.5 px-3">Số thẻ ATM</th>
                                        <th class="py-1.5 px-3">Hạn dùng</th>
                                        <th class="py-1.5 px-3">OTP</th>
                                        <th class="py-1.5 px-3">Kết quả test</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-pink-50 dark:divide-pink-900/20 font-mono">
                                    <tr class="hover:bg-pink-50/50 dark:hover:bg-pink-950/30">
                                        <td class="py-1.5 px-3 font-sans font-bold text-slate-800 dark:text-slate-200">NGUYEN VAN A</td>
                                        <td class="py-1.5 px-3 text-blue-600 dark:text-blue-400 font-bold select-all cursor-pointer" onclick="navigator.clipboard.writeText('9704000000000018'); alert('Đã copy số thẻ!');">9704 0000 0000 0018</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">12/30</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">OTP bất kỳ</td>
                                        <td class="py-1.5 px-3 font-sans text-emerald-600 dark:text-emerald-400 font-bold">✓ Thành công</td>
                                    </tr>
                                    <tr class="hover:bg-pink-50/50 dark:hover:bg-pink-950/30">
                                        <td class="py-1.5 px-3 font-sans font-bold text-slate-800 dark:text-slate-200">NGUYEN VAN A</td>
                                        <td class="py-1.5 px-3 text-blue-600 dark:text-blue-400 font-bold select-all cursor-pointer" onclick="navigator.clipboard.writeText('9704000000000026'); alert('Đã copy số thẻ!');">9704 0000 0000 0026</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">12/30</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">OTP bất kỳ</td>
                                        <td class="py-1.5 px-3 font-sans text-rose-600 dark:text-rose-400 font-bold">✗ Thẻ bị khóa</td>
                                    </tr>
                                    <tr class="hover:bg-pink-50/50 dark:hover:bg-pink-950/30">
                                        <td class="py-1.5 px-3 font-sans font-bold text-slate-800 dark:text-slate-200">NGUYEN VAN A</td>
                                        <td class="py-1.5 px-3 text-blue-600 dark:text-blue-400 font-bold select-all cursor-pointer" onclick="navigator.clipboard.writeText('9704000000000034'); alert('Đã copy số thẻ!');">9704 0000 0000 0034</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">12/30</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">OTP bất kỳ</td>
                                        <td class="py-1.5 px-3 font-sans text-amber-600 dark:text-amber-400 font-bold">✗ Không đủ tiền</td>
                                    </tr>
                                    <tr class="hover:bg-pink-50/50 dark:hover:bg-pink-950/30">
                                        <td class="py-1.5 px-3 font-sans font-bold text-slate-800 dark:text-slate-200">NGUYEN VAN A</td>
                                        <td class="py-1.5 px-3 text-blue-600 dark:text-blue-400 font-bold select-all cursor-pointer" onclick="navigator.clipboard.writeText('9704000000000042'); alert('Đã copy số thẻ!');">9704 0000 0000 0042</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">12/30</td>
                                        <td class="py-1.5 px-3 text-slate-600 dark:text-slate-300">OTP bất kỳ</td>
                                        <td class="py-1.5 px-3 font-sans text-purple-600 dark:text-purple-400 font-bold">✗ Hạn mức thẻ</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Panel Xem Trước Chuyển Khoản VietQR Napas 24/7 -->
                    <div id="bank_transfer_panel" class="hidden p-5 rounded-2xl bg-blue-50/70 dark:bg-slate-800/80 border border-blue-100 dark:border-slate-700 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-white dark:bg-slate-900 rounded-xl shadow-sm p-1.5 flex items-center justify-center border border-slate-100 dark:border-slate-700">
                                <span class="font-black text-rose-600 text-xs">VietQR</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white">Tài khoản thụ hưởng PhoneStore:</h4>
                                <p class="text-xs text-blue-900 dark:text-blue-400 font-bold">Chủ TK: <strong class="text-blue-700 dark:text-blue-300 uppercase">{{ $bankConfig['account_name'] }}</strong></p>
                                <p class="text-xs text-slate-600 dark:text-slate-400">STK: <strong class="font-mono text-slate-900 dark:text-slate-200">{{ $bankConfig['account_no'] }}</strong> (Napas 24/7)</p>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 italic">
                            * Sau khi bấm "Xác Nhận Đặt Hàng", mã QR Napas 24/7 động kèm đúng số tiền và nội dung sẽ hiển thị để bạn quét trên App ngân hàng.
                        </p>
                    </div>

                    <!-- Panel Nhập Thẻ 3D Simulation -->
                    <div id="card_panel" class="hidden p-5 rounded-2xl bg-slate-900 text-white space-y-4 shadow-xl">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-black tracking-widest text-slate-400 uppercase">Thẻ Thanh Toán Trực Tuyến</span>
                            <span class="text-lg">💳 VISA / MASTERCARD</span>
                        </div>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase">Số thẻ tín dụng</label>
                                <input type="text" placeholder="4123 •••• •••• 9876" maxlength="19"
                                       class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs font-mono font-bold text-white focus:outline-none focus:border-blue-500">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Hạn dùng (MM/YY)</label>
                                    <input type="text" placeholder="12/28" maxlength="5"
                                           class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs font-mono font-bold text-white focus:outline-none focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase">Mã bảo mật CVV</label>
                                    <input type="password" placeholder="•••" maxlength="4"
                                           class="w-full px-3.5 py-2.5 bg-slate-800/90 border border-slate-700 rounded-xl text-xs font-mono font-bold text-white focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


            <!-- ================= CỘT PHẢI: TÓM TẮT ĐƠN HÀNG & NÚT HOÀN TẤT (5 CỘT) ================= -->
            <div class="lg:col-span-5 sticky top-24 space-y-4">
                
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-base border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center justify-between">
                        <span class="flex items-center gap-2"><span>🛍️</span> Sản Phẩm Đặt Mua</span>
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">{{ count($checkoutCart) }} món</span>
                    </h3>

                    <!-- Danh sách Item -->
                    <div class="space-y-3.5 max-h-72 overflow-y-auto pr-1">
                        @foreach($checkoutCart as $item)
                        <div class="flex items-center gap-3.5 p-2 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/60 transition">
                            <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-xl p-1 border border-slate-100 dark:border-slate-700 flex items-center justify-center flex-shrink-0">
                                @if(!empty($item['image']))
                                    <img src="{{ asset('storage/' . $item['image']) }}" class="max-h-full object-contain" alt="">
                                @else
                                    <span class="text-2xl">📱</span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-extrabold text-slate-900 dark:text-white text-xs truncate">{{ $item['name'] }}</h4>
                                @if(!empty($item['variant_label']))
                                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                        {{ $item['variant_label'] }}
                                    </span>
                                @endif
                                <div class="flex items-center justify-between mt-1 text-xs">
                                    <span class="text-slate-400 dark:text-slate-500">SL: <strong>x{{ $item['quantity'] }}</strong></span>
                                    <span class="font-black text-slate-900 dark:text-white">{{ number_format($item['price'] * $item['quantity']) }} đ</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Bảng tính chi phí -->
                    <div class="border-t border-dashed border-slate-200 dark:border-slate-800 pt-4 space-y-2.5 text-xs">
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>Tiền hàng (Tạm tính):</span>
                            <span class="font-bold text-slate-900 dark:text-white" id="subtotalDisplay">{{ number_format($subtotal) }} đ</span>
                        </div>
                        <div class="flex justify-between items-center text-slate-600 dark:text-slate-400">
                            <span>Phí giao hàng (Theo GPS):</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400" id="shippingFeeDisplay">0 đ (Miễn phí)</span>
                        </div>
                        <div class="flex justify-between items-baseline pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-sm font-black text-slate-900 dark:text-white">Tổng thanh toán:</span>
                            <span class="text-2xl font-black text-rose-600 dark:text-rose-400" id="finalTotalDisplay">
                                {{ number_format($subtotal) }} đ
                            </span>
                        </div>
                    </div>

                    <!-- Nút Xác Nhận Đặt Hàng -->
                    <button type="button" id="submitOrderBtn" onclick="handleCheckoutSubmit()"
                            class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-[1.01] transition-all duration-300 flex items-center justify-center gap-2 cursor-pointer">
                        <span>Xác Nhận & Đặt Hàng Ngay</span>
                    </button>

                    <div class="pt-2 text-center text-[11px] text-slate-400 dark:text-slate-500 space-y-1">
                        <p class="flex items-center justify-center gap-1">
                            <span>🛡️</span> Cam kết bảo mật thông tin & Kiểm tra hàng trước khi thanh toán
                        </p>
                    </div>

                </div>

            </div>

        </div>
    </form>

    <!-- ================= MODAL POPUP VIETQR & HÓA ĐƠN ĐIỆN TỬ ================= -->
    <div id="qrInvoiceModal" class="hidden fixed inset-0 z-[99999] bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 dark:border-slate-800 overflow-hidden animate-in fade-in zoom-in duration-200">
            
            <!-- Modal Header -->
            <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center text-base">📲</div>
                    <div>
                        <h3 class="font-black text-sm">Hóa Đơn & Quét Mã VietQR Napas 24/7</h3>
                        <p class="text-[11px] text-blue-100">Xác nhận thanh toán tự động trong 5 giây</p>
                    </div>
                </div>
                <button type="button" onclick="closeQrModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white font-bold transition cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Modal Body: Ảnh QR & Chi Tiết Hóa Đơn -->
            <div class="p-6 space-y-5 max-h-[80vh] overflow-y-auto">
                
                <!-- Khung Mã QR -->
                <div class="flex flex-col items-center justify-center p-4 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-200/80 dark:border-slate-700 text-center">
                    <img id="modalQrImage" src="" alt="Mã QR Napas 24/7" 
                         class="max-w-[210px] w-full rounded-2xl shadow-md border-2 border-white dark:border-slate-700 hover:scale-105 transition-transform duration-300">
                    <span class="inline-flex items-center gap-1 mt-2.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 text-[10px] font-black border border-emerald-200 dark:border-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span> Quét mã qua App mọi ngân hàng (MB, VCB, Tech, Vietin...)
                    </span>
                </div>

                <!-- Thông Tin Hóa Đơn Điện Tử -->
                <div class="p-4 bg-blue-50/50 dark:bg-slate-800/80 rounded-2xl border border-blue-100 dark:border-slate-700 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center pb-2 border-b border-blue-100/80 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">Chủ tài khoản:</span>
                        <span class="font-black text-slate-900 dark:text-white uppercase">DOAN VIET ANH</span>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-blue-100/80 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">Số tài khoản:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-blue-600 dark:text-blue-400 text-sm">12317112005</span>
                            <button type="button" onclick="navigator.clipboard.writeText('12317112005'); alert('Đã sao chép STK!')" class="px-2 py-0.5 bg-white dark:bg-slate-700 text-[10px] font-bold rounded shadow-xs text-slate-700 dark:text-slate-200 hover:text-blue-600 cursor-pointer">Copy</button>
                        </div>
                    </div>
                    <div class="flex justify-between items-center pb-2 border-b border-blue-100/80 dark:border-slate-700">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">Tổng tiền hóa đơn:</span>
                        <span class="font-black text-rose-600 dark:text-rose-400 text-base" id="modalTotalAmount">0 đ</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500 dark:text-slate-400 font-bold">Nội dung chuyển khoản:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-black text-slate-900 dark:text-slate-100 bg-amber-100 dark:bg-amber-950/60 px-2 py-0.5 rounded text-xs border border-transparent dark:border-amber-800" id="modalTransferInfo">PS ORDER</span>
                            <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('modalTransferInfo').innerText); alert('Đã sao chép nội dung!')" class="px-2 py-0.5 bg-white dark:bg-slate-700 text-[10px] font-bold rounded shadow-xs text-slate-700 dark:text-slate-200 hover:text-blue-600 cursor-pointer">Copy</button>
                        </div>
                    </div>
                </div>

                <!-- Nút Xác Nhận Đã Chuyển Tiền Hoàn Tất Đơn -->
                <div class="space-y-2">
                    <button type="button" onclick="confirmOrderSubmit()" 
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-emerald-500/25 transition cursor-pointer">
                        ✓ Tôi Đã Chuyển Tiền - Hoàn Tất Đặt Hàng
                    </button>
                    <button type="button" onclick="closeQrModal()" class="w-full py-2.5 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 transition cursor-pointer">
                        Quay lại chỉnh sửa thông tin
                    </button>
                </div>

            </div>

        </div>
    </div>

</div>
@endsection


@push('scripts')
<script>
    const STORE_LAT = 21.028511;
    const STORE_LNG = 105.854444;
    const SUBTOTAL = {{ (int) $subtotal }};

    const URL_PROVINCES = "{{ route('locations.provinces') }}";
    const URL_DISTRICTS_BASE = "{{ url('/locations/districts') }}";
    const URL_WARDS_BASE = "{{ url('/locations/wards') }}";
    const URL_CALCULATE_FEE = "{{ route('locations.fee') }}";

    let map, storeMarker, userMarker;
    let currentLat = STORE_LAT;
    let currentLng = STORE_LNG;
    let isFullscreen = false;
    let currentShippingFee = 0;

    // ================= SỔ ĐỊA CHỈ ĐÃ LƯU - FILL FORM 1-CLICK =================
    function applySavedAddress(addr) {
        // Fill thông tin người nhận
        const nameInput = document.querySelector('input[name="customer_name"]');
        const phoneInput = document.querySelector('input[name="customer_phone"]');
        const addressInput = document.getElementById('input_address');

        if (nameInput && addr.recipient_name) nameInput.value = addr.recipient_name;
        if (phoneInput && addr.phone) phoneInput.value = addr.phone;
        if (addressInput && addr.address_line) addressInput.value = addr.address_line;

        // Fill hidden values
        document.getElementById('province_name').value = addr.province_name || '';
        document.getElementById('district_name').value = addr.district_name || '';
        document.getElementById('ward_name').value = addr.ward_name || '';

        // Highlight nút đã chọn
        document.querySelectorAll('.saved-addr-btn').forEach(b => {
            b.classList.remove('border-blue-500', 'bg-blue-50/80', 'dark:bg-blue-950/30');
            b.classList.add('border-slate-200', 'dark:border-slate-700');
        });
        event.currentTarget.classList.remove('border-slate-200', 'dark:border-slate-700');
        event.currentTarget.classList.add('border-blue-500', 'bg-blue-50/80', 'dark:bg-blue-950/30');

        // Auto-select tỉnh/huyện/xã trong dropdowns nếu có data
        if (addr.to_province_id) {
            autoSelectGHNAddress(addr.to_province_id, addr.to_district_id, addr.to_ward_code);
        }
    }

    // Auto chọn tỉnh -> huyện -> xã theo GHN dropdown
    async function autoSelectGHNAddress(provinceId, districtId, wardCode) {
        const provinceSelect = document.getElementById('province_select');
        const districtSelect = document.getElementById('district_select');
        const wardSelect = document.getElementById('ward_select');

        // Chọn tỉnh
        provinceSelect.value = provinceId;
        provinceSelect.dispatchEvent(new Event('change'));

        // Đợi districts load xong rồi chọn huyện
        await new Promise(resolve => setTimeout(resolve, 800));
        if (districtId) {
            districtSelect.value = districtId;
            districtSelect.dispatchEvent(new Event('change'));
        }

        // Đợi wards load xong rồi chọn xã
        await new Promise(resolve => setTimeout(resolve, 800));
        if (wardCode) {
            wardSelect.value = wardCode;
            wardSelect.dispatchEvent(new Event('change'));
        }
    }

    // ================= 1. GHN EXPRESS 3 CẤP ĐỊA LÝ & TÍNH PHÍ =================
    async function initGHNLocations() {
        const provinceSelect = document.getElementById('province_select');
        const districtSelect = document.getElementById('district_select');
        const wardSelect = document.getElementById('ward_select');

        if (!provinceSelect) return;

        // Load danh sách Tỉnh/Thành từ GHN API
        try {
            provinceSelect.innerHTML = '<option value="">⏳ Đang nạp danh sách Tỉnh/Thành...</option>';
            const res = await fetch(URL_PROVINCES);
            const json = await res.json();
            const provinces = json.data || [];

            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành Phố --</option>';
            provinces.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.ProvinceID;
                opt.textContent = p.ProvinceName;
                provinceSelect.appendChild(opt);
            });
        } catch (err) {
            console.error('Lỗi nạp Tỉnh/Thành GHN:', err);
            provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh / Thành Phố --</option>';
        }

        // Sự kiện khi chọn Tỉnh/Thành
        provinceSelect.addEventListener('change', async function () {
            const provinceId = this.value;
            const provinceText = this.options[this.selectedIndex]?.text || '';
            document.getElementById('province_name').value = provinceId ? provinceText : '';

            // Reset Huyện & Xã
            districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            districtSelect.disabled = true;
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardSelect.disabled = true;
            document.getElementById('district_name').value = '';
            document.getElementById('ward_name').value = '';
            resetShippingFeeDisplay();

            if (!provinceId) return;

            try {
                districtSelect.innerHTML = '<option value="">⏳ Đang nạp Quận/Huyện...</option>';
                const res = await fetch(`${URL_DISTRICTS_BASE}/${provinceId}`);
                const json = await res.json();
                const districts = json.data || [];

                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
                districts.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.DistrictID;
                    opt.textContent = d.DistrictName;
                    districtSelect.appendChild(opt);
                });
                districtSelect.disabled = false;
            } catch (err) {
                console.error('Lỗi nạp Quận/Huyện:', err);
                districtSelect.innerHTML = '<option value="">-- Chọn Quận / Huyện --</option>';
            }
        });

        // Sự kiện khi chọn Quận/Huyện
        districtSelect.addEventListener('change', async function () {
            const districtId = this.value;
            const districtText = this.options[this.selectedIndex]?.text || '';
            document.getElementById('district_name').value = districtId ? districtText : '';

            // Reset Xã
            wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            wardSelect.disabled = true;
            document.getElementById('ward_name').value = '';
            resetShippingFeeDisplay();

            if (!districtId) return;

            try {
                wardSelect.innerHTML = '<option value="">⏳ Đang nạp Phường/Xã...</option>';
                const res = await fetch(`${URL_WARDS_BASE}/${districtId}`);
                const json = await res.json();
                const wards = json.data || [];

                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
                wards.forEach(w => {
                    const opt = document.createElement('option');
                    opt.value = w.WardCode;
                    opt.textContent = w.WardName;
                    wardSelect.appendChild(opt);
                });
                wardSelect.disabled = false;
            } catch (err) {
                console.error('Lỗi nạp Phường/Xã:', err);
                wardSelect.innerHTML = '<option value="">-- Chọn Phường / Xã --</option>';
            }
        });

        // Sự kiện khi chọn Phường/Xã -> Tính cước GHN Express tức thì
        wardSelect.addEventListener('change', async function () {
            const wardCode = this.value;
            const wardText = this.options[this.selectedIndex]?.text || '';
            document.getElementById('ward_name').value = wardCode ? wardText : '';

            if (!wardCode) {
                resetShippingFeeDisplay();
                return;
            }

            const districtId = districtSelect.value;
            await calculateGHNShippingFee(districtId, wardCode);
        });
    }

    // Tính cước vận chuyển GHN Express qua API backend
    async function calculateGHNShippingFee(districtId, wardCode) {
        const feeBadge = document.getElementById('displayShippingFee');
        const feeSummary = document.getElementById('shippingFeeDisplay');
        const totalSummary = document.getElementById('finalTotalDisplay');
        const feeInput = document.getElementById('shipping_fee_input');
        const statusNote = document.getElementById('shippingStatusNote');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (feeBadge) {
            feeBadge.className = 'text-xs font-black text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/60 px-3 py-1 rounded-xl inline-block mt-0.5 animate-pulse';
            feeBadge.innerText = '⚡ Đang tính cước GHN...';
        }
        if (statusNote) {
            statusNote.innerText = 'Đang đồng bộ cước phí với hệ thống GHN Express...';
        }

        try {
            const res = await fetch(URL_CALCULATE_FEE, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    to_district_id: parseInt(districtId),
                    to_ward_code: wardCode.toString()
                })
            });

            const json = await res.json();
            const fee = json.data?.total || json.total || json.fee || 22000;
            currentShippingFee = fee;

            if (feeInput) feeInput.value = fee;

            const feeFormatted = new Intl.NumberFormat('vi-VN').format(fee) + ' đ';
            const totalWithShip = SUBTOTAL + fee;
            const totalFormatted = new Intl.NumberFormat('vi-VN').format(totalWithShip) + ' đ';

            if (feeBadge) {
                feeBadge.className = 'text-sm font-black text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-3 py-1 rounded-xl inline-block mt-0.5 border border-emerald-200 dark:border-emerald-800';
                feeBadge.innerText = feeFormatted;
            }
            if (feeSummary) {
                feeSummary.className = 'font-black text-emerald-600 dark:text-emerald-400';
                feeSummary.innerText = '+ ' + feeFormatted;
            }
            if (totalSummary) {
                totalSummary.innerText = totalFormatted;
            }
            if (statusNote) {
                statusNote.innerText = '✓ Cước vận chuyển tiêu chuẩn GHN Express (Giao trong 1-3 ngày)';
            }

            // Đồng bộ tên địa chỉ vào bản đồ nếu người dùng muốn xem vị trí
            const pName = document.getElementById('province_name').value;
            const dName = document.getElementById('district_name').value;
            const wName = document.getElementById('ward_name').value;
            if (pName && dName) {
                searchLocationQuery(`${wName}, ${dName}, ${pName}`);
            }

        } catch (err) {
            console.error('Lỗi tính phí GHN:', err);
            if (feeBadge) {
                feeBadge.className = 'text-sm font-black text-slate-700 bg-slate-100 px-3 py-1 rounded-xl inline-block';
                feeBadge.innerText = '22.000 đ';
            }
            if (feeInput) feeInput.value = 22000;
        }
    }

    function resetShippingFeeDisplay() {
        currentShippingFee = 0;
        const feeBadge = document.getElementById('displayShippingFee');
        const feeSummary = document.getElementById('shippingFeeDisplay');
        const totalSummary = document.getElementById('finalTotalDisplay');
        const feeInput = document.getElementById('shipping_fee_input');
        const statusNote = document.getElementById('shippingStatusNote');

        if (feeInput) feeInput.value = 0;
        if (feeBadge) {
            feeBadge.className = 'text-sm font-black text-slate-700 dark:text-slate-200 bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-xl inline-block mt-0.5';
            feeBadge.innerText = '0 đ';
        }
        if (feeSummary) {
            feeSummary.className = 'font-black text-slate-500';
            feeSummary.innerText = '0 đ (Chưa chọn xã)';
        }
        if (totalSummary) {
            totalSummary.innerText = new Intl.NumberFormat('vi-VN').format(SUBTOTAL) + ' đ';
        }
        if (statusNote) {
            statusNote.innerText = 'Vui lòng chọn Tỉnh, Huyện, Xã để tính cước vận chuyển chính xác';
        }
    }

    // ================= 2. BẢN ĐỒ LEAFLET GPS & TÌM KIẾM TỌA ĐỘ =================
    function initMap() {
        const mapEl = document.getElementById('map');
        if (!mapEl) return;

        map = L.map('map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([STORE_LAT, STORE_LNG], 13);

        L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=vi', {
            maxZoom: 20,
            subdomains: ['0', '1', '2', '3'],
            attribution: 'Google Maps'
        }).addTo(map);

        const storeIcon = L.divIcon({
            html: '<div style="background:linear-gradient(135deg, #2563eb, #1d4ed8); color:white; border-radius:50%; width:36px; height:36px; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 6px 15px rgba(37,99,235,0.4); border:2px solid white;">🏢</div>',
            className: '',
            iconSize: [36, 36],
            iconAnchor: [18, 18]
        });
        storeMarker = L.marker([STORE_LAT, STORE_LNG], { icon: storeIcon }).addTo(map)
            .bindPopup('<strong>🏢 PhoneStore Flagship</strong><br><span style="font-size:11px; color:#64748b;">Kho xuất hàng trung tâm</span>');

        const userIcon = L.divIcon({
            html: '<div style="background:linear-gradient(135deg, #f97316, #ea580c); color:white; border-radius:50%; width:38px; height:38px; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 6px 20px rgba(249,115,22,0.5); border:2px solid white;">📍</div>',
            className: '',
            iconSize: [38, 38],
            iconAnchor: [19, 38]
        });

        const defaultUserLat = 21.035000;
        const defaultUserLng = 105.845000;
        userMarker = L.marker([defaultUserLat, defaultUserLng], { icon: userIcon, draggable: true }).addTo(map);
        userMarker.bindPopup('<strong>📍 Vị trí nhận hàng dự kiến</strong>');

        userMarker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            currentLat = pos.lat;
            currentLng = pos.lng;
            document.getElementById('input_latitude').value = Number(pos.lat).toFixed(7);
            document.getElementById('input_longitude').value = Number(pos.lng).toFixed(7);
        });

        map.on('click', function (e) {
            userMarker.setLatLng(e.latlng);
            currentLat = e.latlng.lat;
            currentLng = e.latlng.lng;
            document.getElementById('input_latitude').value = Number(e.latlng.lat).toFixed(7);
            document.getElementById('input_longitude').value = Number(e.latlng.lng).toFixed(7);
        });
    }

    async function searchLocationQuery(query) {
        if (!query || !map) return;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        try {
            const res = await fetch('{{ route("checkout.search-location") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ q: query })
            });
            const data = await res.json();
            if (data.success && data.results?.length > 0) {
                const first = data.results[0];
                userMarker.setLatLng([first.lat, first.lng]);
                map.setView([first.lat, first.lng], 14);
                document.getElementById('input_latitude').value = Number(first.lat).toFixed(7);
                document.getElementById('input_longitude').value = Number(first.lng).toFixed(7);
            }
        } catch (e) {}
    }

    async function searchLocationOnMap() {
        const query = document.getElementById('mapSearchInput').value.trim();
        if (!query) {
            alert('Vui lòng nhập địa điểm cần tìm!');
            return;
        }
        await searchLocationQuery(query);
    }

    function locateUserGPS() {
        if (!navigator.geolocation) {
            alert('Trình duyệt không hỗ trợ định vị GPS.');
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                if (userMarker) {
                    userMarker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 15);
                    document.getElementById('input_latitude').value = Number(lat).toFixed(7);
                    document.getElementById('input_longitude').value = Number(lng).toFixed(7);
                }
            },
            () => {
                alert('Vui lòng cấp quyền truy cập vị trí trong cài đặt trình duyệt!');
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    // ================= 3. PHƯƠNG THỨC THANH TOÁN & POPUP VIETQR =================
    function switchPayment(method) {
        document.querySelectorAll('.payment-tab').forEach(tab => {
            const radio = tab.querySelector('input[type="radio"]');
            if (radio && radio.value === method) {
                radio.checked = true;
                if (method === 'momo') {
                    tab.classList.add('border-pink-600', 'dark:border-pink-500', 'bg-pink-50/40', 'dark:bg-pink-950/40');
                } else if (method === 'card') {
                    tab.classList.add('border-purple-600', 'dark:border-purple-500', 'bg-purple-50/40', 'dark:bg-purple-950/40');
                } else if (method === 'bank_transfer') {
                    tab.classList.add('border-blue-600', 'dark:border-blue-500', 'bg-blue-50/40', 'dark:bg-blue-950/40');
                } else {
                    tab.classList.add('border-emerald-600', 'dark:border-emerald-500', 'bg-emerald-50/40', 'dark:bg-emerald-950/40');
                }
                tab.classList.remove('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800/80');
            } else if (radio) {
                tab.classList.remove(
                    'border-blue-600', 'dark:border-blue-500', 'bg-blue-50/40', 'dark:bg-blue-950/40',
                    'border-pink-600', 'dark:border-pink-500', 'bg-pink-50/40', 'dark:bg-pink-950/40',
                    'border-purple-600', 'dark:border-purple-500', 'bg-purple-50/40', 'dark:bg-purple-950/40',
                    'border-emerald-600', 'dark:border-emerald-500', 'bg-emerald-50/40', 'dark:bg-emerald-950/40'
                );
                tab.classList.add('border-slate-200', 'dark:border-slate-700', 'bg-white', 'dark:bg-slate-800/80');
            }
        });

        const momoPanel = document.getElementById('momo_panel');
        const bankPanel = document.getElementById('bank_transfer_panel');
        const cardPanel = document.getElementById('card_panel');

        if (momoPanel) momoPanel.classList.toggle('hidden', method !== 'momo');
        if (bankPanel) bankPanel.classList.toggle('hidden', method !== 'bank_transfer');
        if (cardPanel) cardPanel.classList.toggle('hidden', method !== 'card');
    }

    function handleCheckoutSubmit() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        // Bắt buộc chọn Tỉnh, Huyện, Xã
        const pSel = document.getElementById('province_select');
        const dSel = document.getElementById('district_select');
        const wSel = document.getElementById('ward_select');

        if (pSel && !pSel.value) {
            alert('Vui lòng chọn Tỉnh / Thành Phố nhận hàng!');
            pSel.focus();
            return;
        }
        if (dSel && !dSel.value) {
            alert('Vui lòng chọn Quận / Huyện nhận hàng!');
            dSel.focus();
            return;
        }
        if (wSel && !wSel.value) {
            alert('Vui lòng chọn Phường / Xã nhận hàng để tính cước GHN!');
            wSel.focus();
            return;
        }

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const selectedPayment = document.querySelector('input[name="payment_method"]:checked')?.value || 'cod';

        if (selectedPayment === 'bank_transfer') {
            showQrModal();
        } else {
            form.submit();
        }
    }

    function showQrModal() {
        const modal = document.getElementById('qrInvoiceModal');
        const modalQrImage = document.getElementById('modalQrImage');
        const modalTotalAmount = document.getElementById('modalTotalAmount');
        const modalTransferInfo = document.getElementById('modalTransferInfo');

        const totalWithShip = SUBTOTAL + currentShippingFee;
        const totalFormatted = new Intl.NumberFormat('vi-VN').format(totalWithShip) + ' đ';

        if (modalTotalAmount) modalTotalAmount.innerText = totalFormatted;

        const tempOrderCode = 'PS' + Math.floor(100000 + Math.random() * 900000);
        if (modalTransferInfo) modalTransferInfo.innerText = tempOrderCode;

        const bankId = 'MB';
        const accountNo = '12317112005';
        const accountName = 'DOAN VIET ANH';
        const vietQrUrl = `https://img.vietqr.io/image/${bankId}-${accountNo}-compact2.png?amount=${totalWithShip}&addInfo=${encodeURIComponent(tempOrderCode)}&accountName=${encodeURIComponent(accountName)}`;

        if (modalQrImage) modalQrImage.src = vietQrUrl;
        if (modal) modal.classList.remove('hidden');
    }

    function closeQrModal() {
        const modal = document.getElementById('qrInvoiceModal');
        if (modal) modal.classList.add('hidden');
    }

    function confirmOrderSubmit() {
        const form = document.getElementById('checkoutForm');
        if (form) form.submit();
    }

    // ================= KHỞI CHẠY KHI TRANG SẴN SÀNG =================
    document.addEventListener('DOMContentLoaded', () => {
        initGHNLocations();
        initMap();
        switchPayment('cod');
    });
</script>
@endpush

