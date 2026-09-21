@extends('layouts.admin')

@section('title', 'Chi Tiết Đơn Hàng #' . $order->order_code . ' - PhoneStore Admin')
@section('page_title', 'Chi Tiết Đơn Hàng')
@section('page_heading', 'Đơn Hàng #' . $order->order_code)

@push('head')
<!-- Leaflet Map cho Admin điều phối ship hàng -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #adminOrderMap { height: 380px; width: 100%; border-radius: 1.5rem; z-index: 10; }
</style>
@endpush

@section('content')
@php
    $storeLat = $storeLat ?? 21.028511;
    $storeLng = $storeLng ?? 105.854444;
@endphp
<div class="max-w-6xl mx-auto py-4 space-y-8">

    <!-- Breadcrumb & Top -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 dark:text-slate-500 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Admin</a>
                <span>/</span>
                <a href="{{ route('admin.orders.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Đơn hàng</a>
                <span>/</span>
                <span class="text-blue-600 dark:text-blue-400 font-mono">{{ $order->order_code }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Chi Tiết Đơn Hàng & Điều Phối Giao Hàng
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($order->shipping_status !== 'cancelled')
            <form action="{{ route('admin.orders.update-shipping', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn HỦY đơn hàng [#{{ $order->order_code }}] không?\n\n• Vận đơn GHN Express sẽ tự động bị hủy\n• Toàn bộ sản phẩm sẽ được cộng hoàn lại kho');">
                @csrf
                @method('PATCH')
                <input type="hidden" name="shipping_status" value="cancelled">
                <button type="submit" class="px-5 py-2.5 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-bold rounded-full border border-rose-200 dark:border-rose-800 transition cursor-pointer flex items-center gap-1.5">
                    <span>✕</span> Hủy Đơn & Vận Đơn GHN
                </button>
            </form>
            @endif

            <a href="{{ route('admin.orders.index') }}" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold rounded-full border border-transparent dark:border-slate-700 transition">
                Danh Sách Đơn
            </a>
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

    @if($order->shipping_status === 'cancelled')
        <div class="p-4 rounded-3xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 flex items-center gap-3.5 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <span class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/80 text-rose-600 dark:text-rose-300 flex items-center justify-center text-xl flex-shrink-0">🚫</span>
            <div>
                <h4 class="font-black text-sm text-rose-900 dark:text-white">Đơn Hàng Này Đã Bị Hủy</h4>
                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5">Mã vận đơn GHN Express đã được thu hồi và số lượng sản phẩm đã hoàn kho đầy đủ.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- CỘT TRÁI: BẢN ĐỒ GPS VẬN CHUYỂN & FORM ĐIỀU PHỐI (7 CỘT) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Bản đồ GPS Đo Khoảng Cách -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <span>🗺️</span> Định Vị GPS & Tuyến Đường Giao Hàng
                    </h3>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-900">
                        Khoảng cách: {{ $order->distance_km }} km
                    </span>
                </div>

                <div class="relative overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700">
                    <div id="adminOrderMap"></div>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700 text-xs space-y-1.5 text-slate-700 dark:text-slate-300">
                    <p>🏢 <strong>Store xuất hàng:</strong> PhoneStore Flagship (21.0285, 105.8544)</p>
                    <p>📍 <strong>Địa chỉ nhà khách:</strong> {{ $order->shipping_address }}</p>
                    <p>🚗 <strong>Tọa độ GPS khách:</strong> ({{ $order->latitude }}, {{ $order->longitude }})</p>
                </div>
            </div>

            <!-- Form Điều Phối Tài Xế & Cập Nhật Trạng Thái -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
                <h3 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>🛵</span> Điều Phối Tài Xế & Cập Nhật Giao Hàng
                </h3>

                <form action="{{ route('admin.orders.update-shipping', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tên Shipper phụ trách</label>
                            <input type="text" name="shipper_name" value="{{ old('shipper_name', $order->shipper_name) }}"
                                   placeholder="Ví dụ: Nguyễn Văn Giao - Đội 1"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-blue-500 transition">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Số điện thoại Shipper</label>
                            <input type="tel" name="shipper_phone" value="{{ old('shipper_phone', $order->shipper_phone) }}"
                                   placeholder="0912 345 678"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-blue-500 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trạng thái vận chuyển <span class="text-rose-500">*</span></label>
                            <select name="shipping_status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-blue-500 transition cursor-pointer">
                                <option value="pending" {{ $order->shipping_status === 'pending' ? 'selected' : '' }}>Chờ duyệt đơn</option>
                                <option value="preparing" {{ $order->shipping_status === 'preparing' ? 'selected' : '' }}>Đang đóng gói xuất kho</option>
                                <option value="shipping" {{ $order->shipping_status === 'shipping' ? 'selected' : '' }}>Đang giao hàng (Tài xế đang chạy)</option>
                                <option value="delivered" {{ $order->shipping_status === 'delivered' ? 'selected' : '' }}>Đã giao thành công</option>
                                <option value="cancelled" {{ $order->shipping_status === 'cancelled' ? 'selected' : '' }}>Hủy đơn hàng</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trạng thái thanh toán <span class="text-rose-500">*</span></label>
                            <select name="payment_status" class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-slate-800 dark:text-slate-200 focus:bg-white dark:focus:bg-slate-800 focus:outline-none focus:border-blue-500 transition cursor-pointer">
                                <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Chưa thanh toán</option>
                                <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Đã thu tiền / Thanh toán thành công</option>
                                <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Thanh toán thất bại</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-blue-500/25 transition cursor-pointer">
                        Lưu Thay Đổi & Cập Nhật Điều Phối
                    </button>
                </form>
            </div>

            <!-- Khối Điều Khiển & Đồng Bộ GHN Express -->
            @if($order->ghn_order_code)
            <div class="bg-gradient-to-r from-orange-50/80 via-amber-50/60 to-white dark:from-slate-900 dark:via-slate-900 dark:to-slate-800 p-6 rounded-3xl border-2 border-orange-200 dark:border-orange-900/60 shadow-sm space-y-4 text-xs transition-colors duration-300">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-orange-100 dark:border-slate-800 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-orange-500 text-white flex items-center justify-center font-black text-xs shadow-md shadow-orange-500/30">⚡</span>
                        <div>
                            <h4 class="font-black text-slate-900 dark:text-white text-sm">Hệ Thống Vận Chuyển GHN Express</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Mã vận đơn: <strong class="font-mono text-orange-600 dark:text-orange-400">{{ $order->ghn_order_code }}</strong></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Nút Đồng Bộ Trạng Thái GHN -->
                        <form action="{{ route('admin.orders.sync-ghn', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 bg-white dark:bg-slate-800 hover:bg-orange-500 hover:text-white text-orange-600 dark:text-orange-300 text-xs font-bold rounded-xl border border-orange-200 dark:border-orange-800 transition flex items-center gap-1.5 shadow-sm cursor-pointer" title="Lấy trạng thái thực tế mới nhất từ GHN">
                                <span>🔄</span> Đồng Bộ Từ GHN
                            </button>
                        </form>

                        <!-- Nút In Vận Đơn A5 -->
                        <a href="{{ route('admin.orders.print-ghn', $order->id) }}" target="_blank" class="px-3.5 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold rounded-xl shadow-md shadow-orange-500/25 transition flex items-center gap-1.5 cursor-pointer">
                            <span>🖨️</span> In Vận Đơn (A5)
                        </a>

                        <!-- Nút Xem trên Portal GHN -->
                        <a href="https://5sao.ghn.dev/order?search={{ $order->ghn_order_code }}" target="_blank" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition flex items-center gap-1" title="Mở trang quản lý trên GHN">
                            <span>🌐</span> GHN Portal ↗
                        </a>
                    </div>
                </div>

                <div class="p-3.5 bg-white/80 dark:bg-slate-800/80 rounded-2xl border border-orange-100 dark:border-slate-700/80 space-y-1 text-slate-600 dark:text-slate-300">
                    <p class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                        <span>ℹ️</span> <span>Cơ chế đồng bộ trạng thái GHN:</span>
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500 dark:text-slate-400 pl-1">
                        <li>Khi tạo đơn, GHN tiếp nhận ở trạng thái <strong>"Chờ lấy hàng / Chờ bàn giao"</strong> với tùy chọn <strong>Bên gửi trả phí</strong>.</li>
                        <li>Đơn thanh toán MoMo/Online: COD = 0đ (không thu thêm tiền khách).</li>
                        <li>Đơn thanh toán COD: COD = {{ number_format($order->total_amount) }}đ (thu đúng tổng giá trị đơn).</li>
                        <li>Trạng thái trên GHN chỉ chuyển sang <strong>"Đang giao hàng"</strong> khi bưu tá GHN đến lấy hàng và quét mã vạch trên app tài xế.</li>
                        <li>Bấm nút <strong>"Đồng Bộ Từ GHN"</strong> bất cứ lúc nào để hệ thống tự động kiểm tra và đồng bộ trạng thái thực tế từ GHN về website.</li>
                    </ul>
                </div>
            </div>
            @elseif($order->shipping_status !== 'cancelled')
            <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 flex items-center justify-center font-black text-xs">📦</span>
                        <div>
                            <h4 class="font-black text-slate-900 dark:text-white text-sm">Vận Đơn GHN Express Chưa Tạo</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400">Đơn hàng này chưa được đồng bộ sang cổng GHN Express.</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.orders.create-ghn', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white font-black text-xs rounded-xl shadow-md shadow-orange-500/25 transition cursor-pointer flex items-center gap-1.5">
                            <span>⚡</span> Đẩy Vận Đơn Sang GHN
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>


        <!-- CỘT PHẢI: CHI TIẾT KHÁCH HÀNG & MÓN HÀNG (5 CỘT) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Khách hàng & Vận đơn GHN -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 text-xs transition-colors duration-300">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="font-black text-slate-900 dark:text-white text-sm">👤 Thông Tin Khách Hàng</h3>
                    @if($order->ghn_order_code)
                        <span class="px-2.5 py-0.5 rounded-full bg-orange-50 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 font-extrabold text-[10px] border border-orange-200 dark:border-orange-800">
                            ⚡ GHN: {{ $order->ghn_order_code }}
                        </span>
                    @endif
                </div>

                <div class="space-y-2">
                    <p class="text-slate-700 dark:text-slate-300">Họ tên: <strong class="text-slate-900 dark:text-white">{{ $order->customer_name }}</strong></p>
                    <p class="text-slate-700 dark:text-slate-300">Số điện thoại: <a href="tel:{{ $order->customer_phone }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">{{ $order->customer_phone }}</a></p>
                    <p class="text-slate-700 dark:text-slate-300">Email: <span class="text-slate-600 dark:text-slate-400">{{ $order->customer_email ?? '---' }}</span></p>
                    <p class="text-slate-700 dark:text-slate-300">Địa chỉ cụ thể: <span class="text-slate-800 dark:text-slate-200 font-semibold">{{ $order->shipping_address }}</span></p>
                    @if($order->province_name || $order->district_name || $order->ward_name)
                    <div class="p-3 bg-slate-50 dark:bg-slate-800/80 rounded-xl border border-slate-100 dark:border-slate-700 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Phân cấp hành chính GHN:</span>
                        <p class="font-bold text-slate-800 dark:text-slate-200">
                            {{ $order->ward_name ?? '---' }}, {{ $order->district_name ?? '---' }}, {{ $order->province_name ?? '---' }}
                        </p>
                        <p class="text-[10px] text-slate-400">District ID: <code>{{ $order->to_district_id ?? '---' }}</code> | Ward Code: <code>{{ $order->to_ward_code ?? '---' }}</code></p>
                    </div>
                    @endif
                </div>

                @if($order->notes)
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-950/60 rounded-xl border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-300">
                        <strong>Ghi chú:</strong> {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Sản phẩm & Chi phí -->
            <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 text-xs transition-colors duration-300">
                <h3 class="font-black text-slate-900 dark:text-white text-sm border-b border-slate-100 dark:border-slate-800 pb-3">📦 Danh Sách Món Hàng</h3>
                
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($order->items as $item)
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-slate-900 dark:text-white truncate">{{ $item->product_name }}</p>
                            @if($item->variant_label)
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-950/60 px-1.5 py-0.5 rounded">{{ $item->variant_label }}</span>
                            @endif
                            <span class="text-slate-400 dark:text-slate-500 block mt-0.5">{{ number_format($item->price) }} đ x {{ $item->quantity }}</span>
                        </div>
                        <span class="font-black text-slate-900 dark:text-white">{{ number_format($item->price * $item->quantity) }} đ</span>
                    </div>
                    @endforeach
                </div>

                <div class="border-t border-dashed border-slate-200 dark:border-slate-800 pt-3 space-y-2 text-slate-600 dark:text-slate-400">
                    <div class="flex justify-between">
                        <span>Tiền hàng:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($order->subtotal) }} đ</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Phí ship ({{ $order->distance_km }} km):</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $order->shipping_fee == 0 ? 'Free Ship (0đ)' : number_format($order->shipping_fee) . ' đ' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Hình thức thanh toán:</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->payment_method_text }}</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-sm font-black text-slate-900 dark:text-white">Tổng thanh toán:</span>
                        <span class="text-xl font-black text-rose-600 dark:text-rose-400">{{ number_format($order->total_amount) }} đ</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const STORE_LAT = {{ $storeLat }};
    const STORE_LNG = {{ $storeLng }};
    const USER_LAT = {{ $order->latitude ?? $storeLat }};
    const USER_LNG = {{ $order->longitude ?? $storeLng }};

    function initAdminMap() {
        const map = L.map('adminOrderMap').setView([STORE_LAT, STORE_LNG], 12);

        L.tileLayer('https://mt{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}&hl=vi', {
            maxZoom: 20,
            subdomains: ['0', '1', '2', '3'],
            attribution: 'Google Maps'
        }).addTo(map);

        setTimeout(() => {
            map.invalidateSize();
        }, 300);


        // Marker Store
        const storeIcon = L.divIcon({
            html: '<div style="background:linear-gradient(135deg, #2563eb, #1d4ed8); color:white; border-radius:50%; width:38px; height:38px; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(0,0,0,0.3); border:2px solid white;">🏢</div>',
            className: '',
            iconSize: [38, 38],
            iconAnchor: [19, 19]
        });
        L.marker([STORE_LAT, STORE_LNG], { icon: storeIcon }).addTo(map).bindPopup('<strong>Điểm xuất kho PhoneStore</strong>');

        // Marker Nhà Khách
        const userIcon = L.divIcon({
            html: '<div style="background:linear-gradient(135deg, #e11d48, #be123c); color:white; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(225,29,72,0.5); border:2px solid white;">📍</div>',
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
        L.marker([USER_LAT, USER_LNG], { icon: userIcon }).addTo(map).bindPopup('<strong>Khách: {{ $order->customer_name }}</strong><br>{{ $order->shipping_address }}').openPopup();

        const bounds = L.latLngBounds([[STORE_LAT, STORE_LNG], [USER_LAT, USER_LNG]]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }


    document.addEventListener('DOMContentLoaded', initAdminMap);
</script>
@endpush
