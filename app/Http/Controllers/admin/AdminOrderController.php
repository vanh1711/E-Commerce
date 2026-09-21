<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Services\GHNService;
use App\Services\GHNOrderService;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AdminOrderController extends Controller
{
    const STORE_LAT = 21.028511;
    const STORE_LNG = 105.854444;

    public const TABS = [
        'all'        => ['label' => 'TẤT CẢ', 'color' => 'blue', 'statuses' => []],
        'pending'    => ['label' => 'CHỜ XỬ LÝ', 'color' => 'slate', 'statuses' => ['pending', 'not_shipped', 'processing', 'preparing']],
        'ready'      => ['label' => 'CHỜ LẤY HÀNG', 'color' => 'cyan', 'statuses' => ['ready_to_pick']],
        'picking'    => ['label' => 'ĐANG LẤY HÀNG', 'color' => 'sky', 'statuses' => ['picking']],
        'delivering' => ['label' => 'ĐANG GIAO', 'color' => 'amber', 'statuses' => ['delivering', 'picked', 'storing', 'transporting', 'sorting', 'shipping']],
        'delivered'  => ['label' => 'THÀNH CÔNG', 'color' => 'emerald', 'statuses' => ['delivered', 'completed']],
        'return'     => ['label' => 'HOÀN HÀNG', 'color' => 'orange', 'statuses' => ['return', 'returning', 'returned', 'return_transporting', 'return_sorting']],
        'cancelled'  => ['label' => 'ĐÃ HỦY', 'color' => 'rose', 'statuses' => ['cancelled']],
    ];

    /**
     * Hiển thị danh sách đơn hàng & Lọc đa tiêu chí chuẩn Lab 08
     */
    public function index(Request $request)
    {
        $paymentLabels = [
            'pending'        => 'Chờ thanh toán',
            'initiated'      => 'Đang chờ MoMo',
            'paid'           => 'Đã thanh toán',
            'failed'         => 'Thanh toán thất bại',
            'cancelled'      => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền',
            'refunded'       => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending'             => 'Chờ tạo vận đơn',
            'not_shipped'         => 'Chưa giao hàng',
            'processing'          => 'Đang đóng gói',
            'preparing'           => 'Đang đóng gói xuất kho',
            'ready_to_pick'       => 'Chờ lấy hàng',
            'picking'             => 'Đang lấy hàng',
            'picked'              => 'Đã lấy hàng',
            'storing'             => 'Đang lưu kho',
            'transporting'        => 'Đang trung chuyển',
            'sorting'             => 'Đang phân loại',
            'delivering'          => 'Đang giao hàng',
            'shipping'            => 'Đang giao hàng',
            'delivered'           => 'Giao hàng thành công',
            'return'              => 'Chờ hoàn hàng',
            'returning'           => 'Đang hoàn hàng',
            'returned'            => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting'      => 'Đang phân loại hoàn',
            'cancelled'           => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search'          => ['nullable', 'string', 'max:100'],
            'payment_status'  => ['nullable', 'string'],
            'shipping_status' => ['nullable', 'string'],
            'payment_method'  => ['nullable', 'string'],
            'tab'             => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from'       => ['nullable', 'date_format:Y-m-d'],
            'date_to'         => ['nullable', 'date_format:Y-m-d'],
            'per_page'        => ['nullable', 'integer', Rule::in([25, 50, 100])],
            'sort'            => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page'            => ['nullable', 'integer', 'min:1'],
        ]);

        $query = Order::with(['items.product', 'user', 'latestTransaction']);

        // 1. Tìm kiếm tổng hợp (Mã đơn, Tên khách, SĐT, Mã GHN, Tên sản phẩm, ID số)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', '%' . $search . '%')
                  ->orWhere('customer_name', 'like', '%' . $search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $search . '%')
                  ->orWhere('customer_email', 'like', '%' . $search . '%')
                  ->orWhere('ghn_order_code', 'like', '%' . $search . '%')
                  ->orWhereHas('items', function ($iq) use ($search) {
                      $iq->where('product_name', 'like', '%' . $search . '%');
                  });

                if (preg_match('/^(?:#|DH|VP)?0*(\d+)$/i', $search, $matches)) {
                    $q->orWhere('id', (int) $matches[1]);
                }
            });
        }

        // 2. Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // 3. Lọc theo phương thức thanh toán / Gateway
        if ($request->filled('payment_method') && $request->payment_method !== 'all') {
            $query->where('payment_method', $request->payment_method);
        }

        // 4. Lọc theo khoảng thời gian tạo đơn
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        // 5. Tính số lượng đơn theo từng Tab trạng thái vận chuyển
        $baseQueryForCounts = clone $query;
        $shippingCounts = $baseQueryForCounts->select('shipping_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')
            ->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all'
                ? $shippingCounts->sum()
                : collect($tab['statuses'])->sum(fn ($status) => $shippingCounts->get($status, 0));
            return $tab;
        });

        // 6. Áp dụng Tab lọc trạng thái
        $activeTab = $request->input('tab', 'all');
        if ($activeTab !== 'all' && isset(self::TABS[$activeTab])) {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status') && $request->shipping_status !== 'all') {
            $query->where('shipping_status', $request->shipping_status);
        }

        // 7. Sắp xếp thứ tự hiển thị
        [$sortColumn, $sortDirection] = match ($request->input('sort', 'newest')) {
            'oldest'      => ['created_at', 'asc'],
            'amount_desc' => ['total_amount', 'desc'],
            'amount_asc'  => ['total_amount', 'asc'],
            default       => ['created_at', 'desc'],
        };

        $perPage = (int) $request->input('per_page', 25);
        $orders = $query->orderBy($sortColumn, $sortDirection)
            ->orderBy('id', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // 8. Thống kê nhanh toàn sàn
        $stats = [
            'total'     => Order::count(),
            'pending'   => Order::whereIn('shipping_status', ['pending', 'preparing', 'not_shipped'])->count(),
            'shipping'  => Order::whereIn('shipping_status', ['shipping', 'delivering'])->count(),
            'delivered' => Order::where('shipping_status', 'delivered')->count(),
            'revenue'   => Order::where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('admin.orders.index', compact(
            'orders',
            'filters',
            'tabs',
            'activeTab',
            'paymentLabels',
            'shippingLabels',
            'stats'
        ));
    }

    /**
     * Chi tiết đơn hàng & Bản đồ GPS
     */
    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'paymentTransactions' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        $storeLat = self::STORE_LAT;
        $storeLng = self::STORE_LNG;

        return view('admin.orders.show', compact('order', 'storeLat', 'storeLng'));
    }

    /**
     * Cập nhật điều phối giao hàng & Hủy đơn hàng
     */
    public function updateShipping(Request $request, $id, GHNService $ghn)
    {
        $order = Order::with('items')->findOrFail($id);

        $request->validate([
            'shipping_status' => 'required|in:pending,preparing,shipping,delivered,cancelled',
            'shipper_name'    => 'nullable|string|max:255',
            'shipper_phone'   => 'nullable|string|max:20',
            'payment_status'  => 'nullable|in:pending,paid,failed,refunded',
        ]);

        $oldStatus = $order->shipping_status;
        $newStatus = $request->shipping_status;

        // Nếu chuyển sang trạng thái HỦY đơn
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            // Hủy vận đơn bên GHN nếu có
            if (!empty($order->ghn_order_code)) {
                try {
                    $ghn->cancelOrder([$order->ghn_order_code]);
                } catch (\Exception $e) {}
            }

            // Hoàn lại số lượng tồn kho
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }
        }

        $order->shipping_status = $newStatus;

        if ($request->filled('shipper_name')) {
            $order->shipper_name = $request->shipper_name;
        }
        if ($request->filled('shipper_phone')) {
            $order->shipper_phone = $request->shipper_phone;
        }
        if ($request->filled('payment_status')) {
            $order->payment_status = $request->payment_status;
        }

        $order->save();

        return redirect()->back()->with('success', "Đã cập nhật trạng thái đơn hàng [{$order->order_code}] thành công!");
    }

    /**
     * Cập nhật trạng thái hàng loạt cho các đơn hàng đã chọn
     */
    public function bulkUpdate(Request $request, GHNService $ghn)
    {
        $request->validate([
            'order_ids'       => 'required|array|min:1',
            'order_ids.*'     => 'integer|exists:orders,id',
            'shipping_status' => 'nullable|in:pending,ready_to_pick,delivered,cancelled',
            'payment_status'  => 'nullable|in:pending,paid,failed',
        ]);

        $orderIds = $request->input('order_ids', []);
        $newShippingStatus = $request->input('shipping_status');
        $newPaymentStatus = $request->input('payment_status');

        if (!$newShippingStatus && !$newPaymentStatus) {
            return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một trạng thái (Giao hàng hoặc Thanh toán) để cập nhật.');
        }

        $orders = Order::with('items')->whereIn('id', $orderIds)->get();
        $updatedCount = 0;

        DB::transaction(function () use ($orders, $newShippingStatus, $newPaymentStatus, $ghn, &$updatedCount) {
            foreach ($orders as $order) {
                $needsSave = false;

                // 1. Cập nhật trạng thái thanh toán nếu được chọn
                if ($newPaymentStatus && $order->payment_status !== $newPaymentStatus) {
                    $order->payment_status = $newPaymentStatus;
                    $needsSave = true;
                }

                // 2. Cập nhật trạng thái vận chuyển nếu được chọn
                if ($newShippingStatus && $order->shipping_status !== $newShippingStatus) {
                    $oldStatus = $order->shipping_status;

                    // Nếu chuyển sang trạng thái HỦY đơn
                    if ($newShippingStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                        // Hủy GHN nếu có
                        if (!empty($order->ghn_order_code)) {
                            try {
                                $ghn->cancelOrder([$order->ghn_order_code]);
                            } catch (\Exception $e) {}
                        }
                        // Hoàn lại số lượng tồn kho
                        foreach ($order->items as $item) {
                            if ($item->product_id) {
                                Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                            }
                        }
                    }

                    $order->shipping_status = $newShippingStatus;
                    $needsSave = true;
                }

                if ($needsSave) {
                    $order->save();
                    $updatedCount++;
                }
            }
        });

        return redirect()->back()->with('success', "Đã chuyển đổi trạng thái thành công cho {$updatedCount} đơn hàng được chọn!");
    }

    /**
     * Đồng bộ trạng thái đơn hàng trực tiếp từ GHN Express API
     */
    public function syncGHN($id, GHNService $ghn)
    {
        $order = Order::findOrFail($id);

        if (empty($order->ghn_order_code)) {
            return redirect()->back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHN Express để đồng bộ.');
        }

        $result = $ghn->syncOrderStatus($order);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * In phiếu vận đơn A5 chuẩn GHN Express
     */
    public function printGHN($id, GHNService $ghn)
    {
        $order = Order::findOrFail($id);

        if (empty($order->ghn_order_code)) {
            return redirect()->back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHN Express để in.');
        }

        $printUrl = $ghn->getPrintOrderUrl($order->ghn_order_code);

        if ($printUrl) {
            return redirect()->away($printUrl);
        }

        return redirect()->back()->with('error', 'Không thể tạo liên kết in vận đơn từ máy chủ GHN. Vui lòng thử lại sau.');
    }

    /**
     * Tạo vận đơn GHN Express thủ công từ trang quản trị
     */
    public function createGHN($id, GHNOrderService $ghnOrders)
    {
        $order = Order::with('items.product')->findOrFail($id);

        if (!empty($order->ghn_order_code)) {
            return redirect()->back()->with('info', "Đơn hàng này đã có mã vận đơn GHN Express: {$order->ghn_order_code}");
        }

        $isPaid = ($order->payment_status === 'paid');
        $result = $ghnOrders->create($order, $isPaid);

        if (!empty($result['data']['order_code'])) {
            $order->update([
                'ghn_order_code'  => $result['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);

            return redirect()->back()->with('success', "Tạo vận đơn GHN Express thành công! Mã: {$result['data']['order_code']}");
        }

        return redirect()->back()->with('error', 'Không thể tạo vận đơn GHN: ' . ($result['message'] ?? 'Lỗi không xác định'));
    }
}
