@extends('layouts.admin')

@section('title', 'Quản Lý Đơn Hàng - Admin PhoneStore')
@section('page_title', 'Đơn hàng')
@section('page_heading', 'Đơn hàng')

@section('content')
<div class="space-y-4">

    <!-- Top Bar: Title & Filter Controls -->
    <div class="bg-white dark:bg-[#0c1322] p-4 sm:p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form id="orderFilterForm" action="{{ route('admin.orders.index') }}" method="GET" class="space-y-4">
            <input type="hidden" name="tab" value="{{ $activeTab }}">

            <!-- Row 1: Title & Main Search Bar -->
            <div class="flex flex-wrap items-center justify-between gap-3">
                
                <!-- Left: Title & Quick Pagination info -->
                <div class="flex items-center gap-3">
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Đơn hàng</h1>
                    
                    @if($orders->total() > 0)
                        <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-[11px] font-bold text-slate-600 dark:text-slate-300">
                            @if($orders->onFirstPage())
                                <span class="text-slate-400">&lt;</span>
                            @else
                                <a href="{{ $orders->previousPageUrl() }}" class="text-blue-600 dark:text-blue-400 font-bold">&lt;</a>
                            @endif
                            
                            <span>{{ $orders->firstItem() }}–{{ $orders->lastItem() }}/{{ $orders->total() }}</span>

                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}" class="text-blue-600 dark:text-blue-400 font-bold">&gt;</a>
                            @else
                                <span class="text-slate-400">&gt;</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right: Per-page, Payment Filter, Search, Reload, Export -->
                <div class="flex flex-wrap items-center gap-2.5 flex-1 justify-end min-w-0">
                    
                    <!-- Hiển thị: 25 | 50 | 100 -->
                    <div class="flex items-center gap-1 text-xs">
                        <span class="text-slate-400 dark:text-slate-500 font-medium hidden sm:inline">Hiển thị:</span>
                        <select name="per_page" onchange="document.getElementById('orderFilterForm').submit()"
                                class="py-2 px-2.5 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer">
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>

                    <!-- Lọc Trạng thái Thanh toán -->
                    <select name="payment_status" onchange="document.getElementById('orderFilterForm').submit()"
                            class="py-2 px-3 bg-slate-50 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer max-w-[160px]">
                        <option value="">Tất cả thanh toán</option>
                        @foreach($paymentLabels as $key => $label)
                            <option value="{{ $key }}" {{ request('payment_status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <!-- Ô tìm kiếm tổng hợp -->
                    <div class="relative min-w-[220px] sm:min-w-[280px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Mã đơn, khách hàng, SĐT, sản phẩm..." 
                               class="w-full pl-8 pr-8 py-2 bg-slate-50 dark:bg-slate-800/90 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-blue-500 transition">
                        <span class="absolute left-2.5 top-2 text-slate-400 text-xs">🔍</span>
                        @if(request('search'))
                            <a href="{{ route('admin.orders.index', ['tab' => $activeTab]) }}" class="absolute right-2.5 top-2 text-slate-400 hover:text-slate-600 text-xs font-bold">✕</a>
                        @endif
                    </div>

                    <!-- Nút Làm Mới (Reload) -->
                    <a href="{{ route('admin.orders.index', ['tab' => $activeTab]) }}" title="Làm mới bộ lọc"
                       class="w-9 h-9 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm transition">
                        🔄
                    </a>

                    <!-- Nút Xuất Trang Này (Export / Print) -->
                    <button type="button" onclick="window.print()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs rounded-2xl shadow-md shadow-emerald-600/20 transition cursor-pointer">
                        <span>📥</span>
                        <span>Xuất trang này</span>
                    </button>

                </div>

            </div>

            <!-- Bộ Lọc Nâng Cao (Collapsible Accordion) -->
            <div class="pt-2 border-t border-slate-100 dark:border-slate-800">
                <details class="group text-xs" {{ request()->anyFilled(['date_from', 'date_to', 'payment_method', 'sort']) ? 'open' : '' }}>
                    <summary class="cursor-pointer font-bold text-slate-500 dark:text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 list-none flex items-center gap-1.5 select-none">
                        <span class="group-open:rotate-90 transition-transform duration-200">▶</span>
                        <span>Bộ lọc nâng cao</span>
                    </summary>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mt-3 pt-2">
                        <!-- Từ ngày -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Từ ngày</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-blue-500">
                        </div>

                        <!-- Đến ngày -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Đến ngày</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-blue-500">
                        </div>

                        <!-- Cổng / Phương thức thanh toán -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Hình thức thanh toán</label>
                            <select name="payment_method" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-blue-500">
                                <option value="">Tất cả phương thức</option>
                                <option value="cod" {{ request('payment_method') === 'cod' ? 'selected' : '' }}>Tiền mặt (COD)</option>
                                <option value="momo" {{ request('payment_method') === 'momo' ? 'selected' : '' }}>Ví MoMo</option>
                                <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                                <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Thẻ quốc tế</option>
                            </select>
                        </div>

                        <!-- Sắp xếp -->
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Sắp xếp theo</label>
                            <div class="flex items-center gap-2">
                                <select name="sort" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-700 dark:text-slate-200 outline-none focus:border-blue-500">
                                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Mới nhất trước</option>
                                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất trước</option>
                                    <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>Giá trị: Cao → Thấp</option>
                                    <option value="amount_asc" {{ request('sort') === 'amount_asc' ? 'selected' : '' }}>Giá trị: Thấp → Cao</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl transition cursor-pointer">
                                    Áp dụng
                                </button>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </form>

        <!-- Status Tabs Bar (Horizontal Scrollable Pills) -->
        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2 overflow-x-auto pb-1 text-xs">
            @foreach($tabs as $tabKey => $tab)
                @php
                    $isActive = ($activeTab === $tabKey);
                    $color = $tab['color'];
                @endphp
                <a href="{{ route('admin.orders.index', array_merge(request()->except('tab', 'page'), ['tab' => $tabKey])) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full font-black text-[11px] whitespace-nowrap transition-all duration-200 {{ $isActive ? 'bg-blue-600 text-white shadow-md shadow-blue-600/20' : 'bg-slate-100 dark:bg-slate-800/80 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                    <span>{{ $tab['label'] }}</span>
                    <span class="px-1.5 py-0.2 rounded-md font-mono text-[10px] {{ $isActive ? 'bg-white/25 text-white font-bold' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                        {{ $tab['count'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-300">
        
        <!-- Info Subheader -->
        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 font-medium">
            <span>{{ $orders->total() }} đơn hàng trong danh sách</span>
            <span class="flex items-center gap-1 text-[11px]">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Trạng thái vận chuyển được cập nhật từ GHN
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="bg-slate-50/60 dark:bg-slate-800/40 text-left text-[11px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input type="checkbox" id="selectAllOrders" title="Chọn / Bỏ chọn tất cả đơn hàng trang này"
                                   class="rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-0 cursor-pointer w-4 h-4">
                        </th>
                        <th class="py-3.5 px-4">Mã Đơn Hàng</th>
                        <th class="py-3.5 px-4">Ngày Tạo Đơn</th>
                        <th class="py-3.5 px-4">Sản Phẩm</th>
                        <th class="py-3.5 px-4">Tổng Tiền</th>
                        <th class="py-3.5 px-4">COD Cần Thu</th>
                        <th class="py-3.5 px-4">Tên Khách Hàng</th>
                        <th class="py-3.5 px-4">Mã Vận Đơn</th>
                        <th class="py-3.5 px-4">Trạng Thái Giao Hàng</th>
                        <th class="py-3.5 px-4">Đơn Vị VC</th>
                        <th class="py-3.5 px-4 text-right">Thao Tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($orders as $order)
                    @php
                        // First item summary
                        $firstItem = $order->items->first();
                        $itemCount = $order->items->count();
                        $productSummary = $firstItem ? ($firstItem->product_name . ' × ' . $firstItem->quantity) : '—';
                        if ($itemCount > 1) {
                            $productSummary .= ' (+' . ($itemCount - 1) . ' khác)';
                        }

                        // Payment badge color
                        $payBadgeClass = match($order->payment_status) {
                            'paid'           => 'bg-emerald-500 text-white',
                            'pending'        => 'bg-amber-500 text-white',
                            'initiated'      => 'bg-amber-600 text-white',
                            'failed'         => 'bg-rose-600 text-white',
                            'refund_pending' => 'bg-purple-600 text-white',
                            'refunded'       => 'bg-purple-700 text-white',
                            default          => 'bg-slate-600 text-white',
                        };

                        // COD amount
                        $codAmount = ($order->payment_status === 'paid') ? 0 : $order->total_amount;

                        // Shipping dot color
                        $shipDotClass = match($order->shipping_status) {
                            'delivered', 'completed' => 'bg-emerald-500',
                            'cancelled'              => 'bg-rose-500',
                            'ready_to_pick', 'ready' => 'bg-cyan-500',
                            'picking'                => 'bg-sky-500',
                            'shipping', 'delivering' => 'bg-amber-500',
                            'return', 'returned'     => 'bg-orange-500',
                            default                  => 'bg-slate-400',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition">
                        
                        <!-- Checkbox chọn đơn -->
                        <td class="py-4 px-4 text-center">
                            <input type="checkbox" name="selected_orders[]" value="{{ $order->id }}" 
                                   class="order-checkbox rounded border-slate-300 dark:border-slate-700 text-blue-600 focus:ring-0 cursor-pointer w-4 h-4">
                        </td>

                        <!-- Mã đơn hàng & Badge thanh toán -->
                        <td class="py-4 px-4">
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               class="font-mono font-bold text-blue-600 dark:text-blue-400 hover:underline block text-xs">
                                {{ $order->order_code }}
                            </a>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $payBadgeClass }}">
                                {{ $paymentLabels[$order->payment_status] ?? $order->payment_status }}
                            </span>
                        </td>

                        <!-- Ngày tạo đơn -->
                        <td class="py-4 px-4">
                            <p class="font-bold text-slate-800 dark:text-slate-200">{{ $order->created_at ? $order->created_at->format('d/m/Y') : '—' }}</p>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $order->created_at ? $order->created_at->format('H:i') : '' }}</span>
                        </td>

                        <!-- Sản phẩm -->
                        <td class="py-4 px-4 max-w-[200px]">
                            <p class="font-medium text-slate-700 dark:text-slate-300 truncate" title="{{ $firstItem->product_name ?? '' }}">
                                {{ $productSummary }}
                            </p>
                        </td>

                        <!-- Tổng tiền -->
                        <td class="py-4 px-4 font-black text-slate-900 dark:text-white">
                            {{ number_format($order->total_amount, 0, ',', '.') }}
                            <span class="text-[10px] text-slate-400 font-normal">đ</span>
                        </td>

                        <!-- COD cần thu -->
                        <td class="py-4 px-4 font-bold {{ $codAmount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400 dark:text-slate-500' }}">
                            {{ number_format($codAmount, 0, ',', '.') }}
                            <span class="text-[10px] font-normal">đ</span>
                        </td>

                        <!-- Tên khách hàng -->
                        <td class="py-4 px-4">
                            <p class="font-bold text-slate-900 dark:text-white">{{ $order->customer_name }}</p>
                            <span class="text-[10px] text-slate-400 dark:text-slate-500 font-mono">{{ $order->customer_phone }}</span>
                        </td>

                        <!-- Mã vận đơn -->
                        <td class="py-4 px-4">
                            @if(!empty($order->ghn_order_code))
                                <a href="https://tracking.ghn.vn/?order_code={{ $order->ghn_order_code }}" target="_blank"
                                   class="font-mono font-black text-blue-600 dark:text-blue-400 hover:underline">
                                    {{ $order->ghn_order_code }}
                                </a>
                            @else
                                <span class="text-slate-400 dark:text-slate-500 text-[11px]">Chưa có vận đơn</span>
                            @endif
                        </td>

                        <!-- Trạng thái giao hàng -->
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 text-slate-700 dark:text-slate-300 font-bold">
                                <span class="w-2 h-2 rounded-full {{ $shipDotClass }}"></span>
                                {{ $shippingLabels[$order->shipping_status] ?? $order->shipping_status }}
                            </span>
                        </td>

                        <!-- Đơn vị VC -->
                        <td class="py-4 px-4 font-black text-slate-500">
                            {{ !empty($order->ghn_order_code) ? 'GHN' : '—' }}
                        </td>

                        <!-- Thao tác -->
                        <td class="py-4 px-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('admin.orders.show', $order->id) }}" title="Xem chi tiết & lộ trình"
                                   class="p-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-blue-50 dark:hover:bg-blue-950/60 text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    👁️
                                </a>

                                @if(!empty($order->ghn_order_code))
                                    <a href="{{ route('admin.orders.print-ghn', $order->id) }}" target="_blank" title="In phiếu vận đơn A5 GHN"
                                       class="p-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 text-emerald-600 dark:text-emerald-400 transition">
                                        🖨️
                                    </a>
                                    <form action="{{ route('admin.orders.sync-ghn', $order->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" title="Đồng bộ trạng thái từ GHN Express"
                                                class="p-1.5 rounded-xl bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 text-blue-600 dark:text-blue-400 transition cursor-pointer">
                                            🔄
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-12 text-center text-slate-400 dark:text-slate-500">
                            Không có đơn hàng nào phù hợp với bộ lọc hiện tại.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Bottom Pagination -->
        @if($orders->total() > 0)
        <div class="p-4 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span class="text-slate-500 dark:text-slate-400">
                Hiển thị {{ $orders->firstItem() }}–{{ $orders->lastItem() }} trong {{ $orders->total() }} đơn hàng
            </span>

            <div>
                {{ $orders->links() }}
            </div>
        </div>
        @endif

    </div>

    <!-- ========================================================================= -->
    <!-- FLOATING BULK ACTION TOOLBAR (Thanh Chuyển Đổi Trạng Thái Hàng Loạt)       -->
    <!-- ========================================================================= -->
    <div id="bulkActionBar" 
         class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-slate-900/95 dark:bg-[#080d19]/95 backdrop-blur-xl border border-blue-500/40 shadow-2xl shadow-blue-900/40 rounded-3xl px-5 py-3.5 transition-all duration-300 transform translate-y-32 opacity-0 pointer-events-none flex flex-wrap items-center gap-3 sm:gap-5 text-white max-w-[95vw]">
        
        <!-- Số lượng đã chọn & Nút hủy chọn -->
        <div class="flex items-center gap-2.5">
            <span class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span>
            <span id="selectedCountText" class="font-black text-xs sm:text-sm tracking-tight text-white whitespace-nowrap">Đã chọn 0 đơn</span>
            <button type="button" id="btnDeselectAll" 
                    class="text-[11px] font-bold text-slate-400 hover:text-white underline ml-1 cursor-pointer transition">
                Bỏ chọn
            </button>
        </div>

        <div class="h-6 w-[1px] bg-slate-700/80 hidden md:block"></div>

        <!-- Form thực hiện cập nhật hàng loạt -->
        <form id="bulkActionForm" action="{{ route('admin.orders.bulk-update') }}" method="POST" class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            @csrf
            <div id="bulkSelectedInputs"></div>

            <!-- Chuyển đổi trạng thái giao hàng -->
            <div class="flex items-center gap-1.5">
                <span class="text-slate-400 text-[11px] font-bold hidden sm:inline">Vận chuyển:</span>
                <select name="shipping_status" id="bulkShippingStatus" 
                        class="py-1.5 px-3 bg-slate-800/90 border border-slate-700 hover:border-blue-500 rounded-xl text-xs font-bold text-slate-100 outline-none focus:border-blue-500 cursor-pointer transition">
                    <option value="">-- Đổi trạng thái GH --</option>
                    <option value="pending">⏳ Chờ xử lý / Chờ tạo đơn</option>
                    <option value="ready_to_pick">📦 Chờ vận chuyển / Lấy hàng</option>
                    <option value="delivering" disabled class="text-slate-500 bg-slate-900" title="Shipper GHN quét mã tự cập nhật">
                        🚚 Đang vận chuyển (Shipper tự quét)
                    </option>
                    <option value="delivered">✅ Giao thành công</option>
                    <option value="cancelled">❌ Hủy đơn (Hoàn lại kho)</option>
                </select>
            </div>

            <!-- Chuyển đổi trạng thái thanh toán -->
            <div class="flex items-center gap-1.5">
                <span class="text-slate-400 text-[11px] font-bold hidden sm:inline">Thanh toán:</span>
                <select name="payment_status" id="bulkPaymentStatus" 
                        class="py-1.5 px-3 bg-slate-800/90 border border-slate-700 hover:border-blue-500 rounded-xl text-xs font-bold text-slate-100 outline-none focus:border-blue-500 cursor-pointer transition">
                    <option value="">-- Đổi thanh toán --</option>
                    <option value="pending">⏳ Chờ thanh toán</option>
                    <option value="paid">💳 Đã thanh toán</option>
                    <option value="failed">⚠️ Thanh toán thất bại</option>
                </select>
            </div>

            <!-- Nút Áp Dụng -->
            <button type="submit" id="btnSubmitBulk" 
                    class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-600/30 transition transform hover:scale-105 active:scale-95 cursor-pointer flex items-center gap-1.5 whitespace-nowrap">
                <span>⚡</span>
                <span>Áp dụng thay đổi</span>
            </button>
        </form>
    </div>

</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllOrders');
    const rowCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkBar = document.getElementById('bulkActionBar');
    const selectedCountText = document.getElementById('selectedCountText');
    const btnDeselectAll = document.getElementById('btnDeselectAll');
    const bulkForm = document.getElementById('bulkActionForm');
    const bulkSelectedInputs = document.getElementById('bulkSelectedInputs');

    function updateBulkBar() {
        const checkedBoxes = Array.from(document.querySelectorAll('.order-checkbox:checked'));
        const count = checkedBoxes.length;
        const total = rowCheckboxes.length;

        if (count > 0) {
            selectedCountText.textContent = `Đã chọn ${count} đơn`;
            bulkBar.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            bulkBar.classList.add('translate-y-0', 'opacity-100', 'pointer-events-auto');
        } else {
            bulkBar.classList.add('translate-y-32', 'opacity-0', 'pointer-events-none');
            bulkBar.classList.remove('translate-y-0', 'opacity-100', 'pointer-events-auto');
        }

        if (selectAll) {
            if (count === 0) {
                selectAll.checked = false;
                selectAll.indeterminate = false;
            } else if (count === total && total > 0) {
                selectAll.checked = true;
                selectAll.indeterminate = false;
            } else {
                selectAll.checked = false;
                selectAll.indeterminate = true;
            }
        }
    }

    // Toggle tất cả checkbox khi tick ô header
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => {
                cb.checked = selectAll.checked;
            });
            updateBulkBar();
        });
    }

    // Lắng nghe sự kiện tick trên từng hàng
    rowCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });

    // Bỏ chọn tất cả
    if (btnDeselectAll) {
        btnDeselectAll.addEventListener('click', function () {
            if (selectAll) selectAll.checked = false;
            rowCheckboxes.forEach(cb => {
                cb.checked = false;
            });
            updateBulkBar();
        });
    }

    // Submit form chuyển đổi trạng thái hàng loạt
    if (bulkForm) {
        bulkForm.addEventListener('submit', function (e) {
            const shipStatus = document.getElementById('bulkShippingStatus').value;
            const payStatus = document.getElementById('bulkPaymentStatus').value;

            if (!shipStatus && !payStatus) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một trạng thái cần chuyển đổi (Vận chuyển hoặc Thanh toán).');
                return;
            }

            const checkedBoxes = Array.from(document.querySelectorAll('.order-checkbox:checked'));
            if (checkedBoxes.length === 0) {
                e.preventDefault();
                alert('Vui lòng tích chọn ít nhất 1 đơn hàng để thao tác.');
                return;
            }

            let confirmMsg = `Bạn có chắc chắn muốn chuyển đổi trạng thái cho ${checkedBoxes.length} đơn hàng đã chọn?`;
            if (shipStatus === 'cancelled') {
                confirmMsg += "\n\n⚠️ Lưu ý: Khi chuyển sang ĐÃ HỦY, số lượng tồn kho của các sản phẩm trong đơn sẽ tự động được hoàn lại.";
            }

            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return;
            }

            // Gắn danh sách order_ids đã chọn vào form
            bulkSelectedInputs.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'order_ids[]';
                hiddenInput.value = cb.value;
                bulkSelectedInputs.appendChild(hiddenInput);
            });
        });
    }
});
</script>
@endpush
@endsection
