@extends('layouts.app')

@push('head')
<!-- Leaflet CSS & JS cho Bản đồ GPS Theo Dõi Lộ Trình -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    #trackingMap { height: 360px; width: 100%; border-radius: 1.5rem; z-index: 10; }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto py-4 space-y-8">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs font-bold text-slate-400">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Trang chủ</a>
        <span>/</span>
        <a href="{{ route('orders.index') }}" class="hover:text-blue-600">Đơn hàng của tôi</a>
        <span>/</span>
        <span class="text-blue-600 font-extrabold">{{ $order->order_code }}</span>
    </div>

    <!-- Thông báo kết quả thao tác -->
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

    <!-- Banner cảnh báo nếu đơn hàng đã bị hủy -->
    @if($order->shipping_status === 'cancelled')
        <div class="p-4 rounded-3xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900 flex items-center gap-3.5 text-rose-800 dark:text-rose-300 text-xs shadow-sm">
            <span class="w-10 h-10 rounded-2xl bg-rose-100 dark:bg-rose-900/80 text-rose-600 dark:text-rose-300 flex items-center justify-center text-xl flex-shrink-0">🚫</span>
            <div>
                <h4 class="font-black text-sm text-rose-900 dark:text-white">Đơn Hàng Này Đã Bị Hủy</h4>
                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-0.5">Vận đơn tương ứng trên hệ thống GHN Express đã được thu hồi và số lượng sản phẩm đã được hoàn trả lại kho hàng.</p>
            </div>
        </div>
    @endif

    <!-- Header Đơn Hàng -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider">
                    <span>📦</span> Chi Tiết Đơn Hàng
                </span>
                @if($order->ghn_order_code)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 dark:bg-orange-950/60 border border-orange-200 dark:border-orange-800 text-orange-700 dark:text-orange-400 text-[11px] font-black">
                    <span>⚡ GHN Express:</span> <strong class="font-mono">{{ $order->ghn_order_code }}</strong>
                </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                Mã đơn: <span class="text-blue-600 dark:text-blue-400 font-mono">{{ $order->order_code }}</span>
            </h1>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Đặt ngày {{ $order->created_at->format('d/m/Y - H:i') }}</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($order->payment_method === 'momo' && $order->payment_status !== 'paid' && $order->shipping_status !== 'cancelled')
            <a href="{{ route('orders.momo.pay', $order->id) }}" class="px-4 py-2 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-black text-xs rounded-full shadow-lg shadow-pink-500/25 transition flex items-center gap-1.5 cursor-pointer">
                <span>💳</span> Thanh Toán MoMo Ngay
            </a>
            @endif

            <span class="px-3.5 py-1.5 rounded-full text-xs font-black border {{ $order->shipping_status_badge }}">
                {{ $order->shipping_status_text }}
            </span>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-black border {{ $order->payment_status === 'paid' ? 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' : 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-800' }}">
                {{ $order->payment_status === 'paid' ? '✓ Đã thanh toán' : '⏳ Chưa thanh toán' }}
            </span>

            @if($order->ghn_order_code)
            <form action="{{ route('orders.sync-ghn', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-full bg-orange-50 dark:bg-orange-950/60 hover:bg-orange-100 dark:hover:bg-orange-900/60 text-orange-700 dark:text-orange-400 text-xs font-bold border border-orange-200 dark:border-orange-800 transition flex items-center gap-1.5 shadow-sm cursor-pointer" title="Cập nhật trạng thái mới nhất từ GHN Express">
                    <span>🔄</span> Cập Nhật Vận Đơn
                </button>
            </form>
            @endif

            @if(in_array($order->shipping_status, ['pending', 'preparing']))
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?\n\n• Hệ thống sẽ tự động hủy vận đơn trên GHN Express\n• Hoàn lại số lượng tồn kho sản phẩm');">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-1.5 rounded-full bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 text-xs font-extrabold border border-rose-200 dark:border-rose-800 transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <span>✕</span> Hủy Đơn
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- TIẾN TRÌNH GIAO HÀNG (STEPPER CHUẨN FLAGSHIP) -->
    <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6 transition-colors duration-300">
        <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">Tiến Trình Vận Chuyển Đơn Hàng</h3>
        
        @php
            $steps = [
                'pending'       => ['icon' => '📝', 'label' => 'Đã đặt đơn', 'desc' => 'Chờ xác nhận & thanh toán'],
                'preparing'     => ['icon' => '📦', 'label' => 'Đang đóng gói', 'desc' => 'Đang xuất kho kiểm tra'],
                'ready_to_pick' => ['icon' => '🚚', 'label' => 'GHN chờ lấy', 'desc' => 'Đã kết nối vận đơn GHN'],
                'shipping'      => ['icon' => '🚀', 'label' => 'Đang giao hàng', 'desc' => 'Tài xế đang vận chuyển'],
                'delivered'     => ['icon' => '✨', 'label' => 'Giao thành công', 'desc' => 'Khách đã nhận hàng'],
            ];
            $statusKeys = array_keys($steps);
            $currentIdx = array_search($order->shipping_status, $statusKeys);
            if ($currentIdx === false) $currentIdx = 0;
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 relative">
            @foreach($steps as $key => $step)
                @php
                    $stepIdx = array_search($key, $statusKeys);
                    $isPassed = $stepIdx <= $currentIdx;
                    $isCurrent = $stepIdx === $currentIdx;
                @endphp
                <div class="flex items-center sm:flex-col sm:text-center gap-3 sm:gap-2 p-3 rounded-2xl {{ $isCurrent ? 'bg-blue-50/80 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800' : '' }}">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg shadow-sm {{ $isPassed ? 'bg-blue-600 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500' }}">
                        {{ $step['icon'] }}
                    </div>
                    <div>
                        <h4 class="text-xs font-black {{ $isPassed ? 'text-slate-900 dark:text-white' : 'text-slate-400 dark:text-slate-500' }}">{{ $step['label'] }}</h4>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">{{ $step['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        @if($order->shipper_name)
        <div class="p-4 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900 flex items-center justify-between text-xs text-indigo-900 dark:text-indigo-300">
            <div class="flex items-center gap-3">
                <span class="text-2xl">🛵</span>
                <div>
                    <p class="font-bold">Tài xế phụ trách giao hàng:</p>
                    <p class="text-sm font-black text-indigo-950 dark:text-white">{{ $order->shipper_name }} ({{ $order->shipper_phone ?? 'Chưa cập nhật SĐT' }})</p>
                </div>
            </div>
            @if($order->shipper_phone)
                <a href="tel:{{ $order->shipper_phone }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-full transition shadow-md">
                    Gọi Tài Xế 📞
                </a>
            @endif
        </div>
        @endif
    </div>

    <!-- BẢNG NHẬT KÝ GIAO DỊCH TÀI CHÍNH (PAYMENT TRANSACTIONS - LAB 06) -->
    @if($order->paymentTransactions->count() > 0)
    <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>💳</span> Nhật Ký Giao Dịch & Cổng Thanh Toán (Payment Log)
            </h3>
            <span class="text-xs font-bold text-slate-400">{{ $order->paymentTransactions->count() }} lần thử thanh toán</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 dark:bg-slate-800/80 text-slate-500 dark:text-slate-400 font-bold border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="py-2.5 px-4">Cổng TT</th>
                        <th class="py-2.5 px-4">Mã GD Cổng / TransID</th>
                        <th class="py-2.5 px-4">Số tiền</th>
                        <th class="py-2.5 px-4">Trạng thái</th>
                        <th class="py-2.5 px-4">Thông điệp phản hồi</th>
                        <th class="py-2.5 px-4">Thời gian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($order->paymentTransactions as $trans)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40">
                        <td class="py-3 px-4">
                            @if($trans->gateway === 'momo')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-pink-100 text-pink-700 dark:bg-pink-950/60 dark:text-pink-300">Ví MoMo</span>
                            @elseif($trans->gateway === 'cod')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">Tiền mặt COD</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">{{ strtoupper($trans->gateway) }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-mono text-[11px] text-slate-600 dark:text-slate-300">
                            <div>{{ $trans->transaction_id ?? $trans->gateway_order_id ?? '---' }}</div>
                            @if($trans->result_code !== null)
                                <span class="text-[10px] text-slate-400">Mã kết quả: {{ $trans->result_code }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 font-bold text-slate-900 dark:text-white">
                            {{ number_format($trans->amount) }} đ
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black border {{ $trans->status_badge }}">
                                {{ $trans->status_text }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-slate-500 dark:text-slate-400 text-[11px]">
                            {{ $trans->message ?? 'Chưa có thông điệp' }}
                        </td>
                        <td class="py-3 px-4 text-slate-400 text-[11px]">
                            {{ $trans->paid_at ? $trans->paid_at->format('d/m/Y H:i:s') : $trans->created_at->format('d/m/Y H:i:s') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- BẢN ĐỒ GPS THEO DÕI LỘ TRÌNH TỪ STORE ĐẾN NHÀ KHÁCH -->
    <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>🗺️</span> Bản Đồ Lộ Trình Giao Hàng GPS
            </h3>
            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-900">
                Khoảng cách: {{ $order->distance_km }} km
            </span>
        </div>

        <div class="relative overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-700">
            <div id="trackingMap"></div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600 dark:text-slate-400 pt-2">
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 block">🏢 ĐIỂM XUẤT PHÁT:</span>
                <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">PhoneStore Flagship Hà Nội</p>
                <p class="text-[11px] text-slate-400 mt-0.5">Đơn vị giao: <strong class="text-orange-600 dark:text-orange-400">GHN Express</strong></p>
            </div>
            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/80 rounded-2xl border border-slate-100 dark:border-slate-700">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 block">📍 ĐỊA CHỈ NHẬN HÀNG:</span>
                <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">{{ $order->shipping_address }}</p>
                @if($order->ward_name || $order->district_name || $order->province_name)
                    <p class="text-[11px] text-slate-400 mt-0.5">Khu vực: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ implode(', ', array_filter([$order->ward_name, $order->district_name, $order->province_name])) }}</span></p>
                @endif
            </div>
        </div>
    </div>

    <!-- DANH SÁCH MÓN HÀNG & TỔNG THANH TOÁN -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <div class="lg:col-span-8 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
            <h3 class="text-sm font-black text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                Sản Phẩm Trong Đơn Hàng ({{ $order->items->count() }})
            </h3>

            <div class="space-y-3 divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($order->items as $item)
                <div class="flex items-center gap-4 pt-3 first:pt-0">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-2xl p-1.5 border border-slate-100 dark:border-slate-700 flex items-center justify-center flex-shrink-0">
                        @if($item->image)
                            <img src="{{ asset('storage/' . $item->image) }}" class="max-h-full object-contain" alt="">
                        @else
                            <span class="text-2xl">📱</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-extrabold text-slate-900 dark:text-white text-xs truncate">{{ $item->product_name }}</h4>
                        @if($item->variant_label)
                            <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                {{ $item->variant_label }}
                            </span>
                        @endif
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                            {{ number_format($item->price) }} đ x <strong class="text-slate-800 dark:text-slate-200">{{ $item->quantity }}</strong>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="font-black text-slate-900 dark:text-white text-xs">
                            {{ number_format($item->price * $item->quantity) }} đ
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-4 bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 text-xs transition-colors duration-300">
            <h3 class="font-black text-slate-900 dark:text-white text-sm border-b border-slate-100 dark:border-slate-800 pb-3">Chi Phí Đơn Hàng</h3>
            
            <div class="space-y-2 text-slate-600 dark:text-slate-400">
                <div class="flex justify-between">
                    <span>Tiền hàng:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ number_format($order->subtotal) }} đ</span>
                </div>
                <div class="flex justify-between">
                    <span>Phí giao hàng (GHN Express):</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $order->shipping_fee == 0 ? 'Miễn phí (0đ)' : number_format($order->shipping_fee) . ' đ' }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Phương thức:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $order->payment_method_text }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Trạng thái TT:</span>
                    <span class="font-bold {{ $order->payment_status === 'paid' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                        {{ $order->payment_status_text }}
                    </span>
                </div>
                <div class="flex justify-between items-baseline pt-3 border-t border-slate-100 dark:border-slate-800">
                    <span class="text-sm font-black text-slate-900 dark:text-white">Tổng cộng:</span>
                    <span class="text-xl font-black text-rose-600 dark:text-rose-400">{{ number_format($order->total_amount) }} đ</span>
                </div>
            </div>

            @if($order->payment_method === 'momo' && $order->payment_status !== 'paid' && $order->shipping_status !== 'cancelled')
            <a href="{{ route('orders.momo.pay', $order->id) }}" class="w-full py-3 bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-700 hover:to-rose-700 text-white font-black text-xs rounded-2xl shadow-lg shadow-pink-500/25 transition text-center flex items-center justify-center gap-1.5 cursor-pointer">
                <span>💳</span> Thanh Toán Qua Ví MoMo
            </a>
            @endif

            <a href="{{ route('orders.index') }}" class="w-full py-3 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-extrabold text-xs rounded-2xl border border-blue-200 dark:border-blue-800 transition text-center flex items-center justify-center gap-1.5">
                <span>📦</span> Xem Danh Sách Đơn Hàng
            </a>

            <a href="{{ route('home') }}" class="w-full py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs rounded-2xl border border-transparent dark:border-slate-700 transition text-center block">
                Về Trang Chủ Mua Thêm
            </a>

            @if(in_array($order->shipping_status, ['pending', 'preparing']))
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?\n\n• Hệ thống sẽ tự động hủy vận đơn trên GHN Express\n• Hoàn lại số lượng tồn kho sản phẩm');">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full py-3 bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-extrabold text-xs rounded-2xl border border-rose-200 dark:border-rose-800 transition flex items-center justify-center gap-1.5 cursor-pointer">
                    <span>✕</span> Hủy Đơn Hàng Này
                </button>
            </form>
            @endif
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

    function initTrackingMap() {
        const map = L.map('trackingMap').setView([STORE_LAT, STORE_LNG], 12);

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
        L.marker([STORE_LAT, STORE_LNG], { icon: storeIcon }).addTo(map).bindPopup('<strong>Điểm xuất hàng PhoneStore</strong>');

        // Marker Nhà Khách
        const userIcon = L.divIcon({
            html: '<div style="background:linear-gradient(135deg, #e11d48, #be123c); color:white; border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-size:18px; box-shadow:0 4px 10px rgba(225,29,72,0.5); border:2px solid white;">📍</div>',
            className: '',
            iconSize: [40, 40],
            iconAnchor: [20, 40]
        });
        L.marker([USER_LAT, USER_LNG], { icon: userIcon }).addTo(map).bindPopup('<strong>Địa chỉ nhận hàng: {{ $order->customer_name }}</strong>').openPopup();

        const bounds = L.latLngBounds([[STORE_LAT, STORE_LNG], [USER_LAT, USER_LNG]]);
        map.fitBounds(bounds, { padding: [50, 50] });
    }


    document.addEventListener('DOMContentLoaded', initTrackingMap);
</script>
@endpush
