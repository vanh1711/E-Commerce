<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\GHNOrderService;
use App\Services\GHNService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // Danh sách lịch sử đơn hàng của người dùng hiện tại
    public function index(Request $request)
    {
        // Nếu là Admin thì chuyển hướng ngay sang trang Quản lý đơn hàng của Admin
        if (Auth::check() && (Auth::user()->is_admin == 1 || Auth::user()->role === 'admin' || Auth::user()->email === 'admin@gmail.com' || Auth::user()->email === 'admin@example.com')) {
            return redirect()->route('admin.orders.index');
        }

        $userId = Auth::id();
        $query = Order::where('user_id', $userId)
                      ->with(['items.product', 'items.variant', 'paymentTransactions', 'latestTransaction'])
                      ->latest();

        // Lọc theo trạng thái vận chuyển
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'pending') {
                $query->whereIn('shipping_status', ['pending', 'preparing']);
            } elseif ($request->status === 'delivered') {
                $query->whereIn('shipping_status', ['delivered', 'completed']);
            } else {
                $query->where('shipping_status', $request->status);
            }
        }

        // Tìm kiếm theo mã đơn hoặc sản phẩm
        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('order_code', 'LIKE', "%{$s}%")
                  ->orWhere('ghn_order_code', 'LIKE', "%{$s}%")
                  ->orWhereHas('items', function ($iq) use ($s) {
                      $iq->where('product_name', 'LIKE', "%{$s}%");
                  });
            });
        }

        $orders = $query->paginate(10)->withQueryString();

        // Thống kê nhanh theo các trạng thái
        $counts = [
            'all'         => Order::where('user_id', $userId)->count(),
            'pending'     => Order::where('user_id', $userId)->whereIn('shipping_status', ['pending', 'preparing'])->count(),
            'shipping'    => Order::where('user_id', $userId)->where('shipping_status', 'shipping')->count(),
            'delivered'   => Order::where('user_id', $userId)->where('shipping_status', 'delivered')->count(),
            'cancelled'   => Order::where('user_id', $userId)->where('shipping_status', 'cancelled')->count(),
            'total_spent' => Order::where('user_id', $userId)->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('orders.index', compact('orders', 'counts'));
    }

    // Chi tiết đơn hàng và bản đồ GPS theo dõi lộ trình giao hàng
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'paymentTransactions', 'latestTransaction'])->findOrFail($id);

        // Bảo mật: Khách hàng chỉ xem được đơn của mình (hoặc Admin)
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403, 'Bạn không có quyền xem đơn hàng này.');
        }

        $storeLat = 21.028511;
        $storeLng = 105.854444;

        return view('orders.show', compact('order', 'storeLat', 'storeLng'));
    }

    // ==========================================
    // XỬ LÝ ĐẶT HÀNG TRỰC TIẾP (PROCESS PAYMENT THEO LAB06)
    // ==========================================
    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders)
    {
        $request->validate([
            'name'           => 'required|string|max:100',
            'phone'          => ['required', 'regex:/^0\d{9}$/'],
            'address'        => 'required|string|max:255',
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
            'payment_method' => 'required|in:cod,momo,bank_transfer,card',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng trống.');
        }

        // 1. Tính tổng tiền hàng và tổng khối lượng sản phẩm
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $totalWeight = collect($cart)->sum(
            fn($item) => (int) ($item['weight'] ?? 200) * (int) $item['quantity']
        );

        // 2. Tính lại phí ship chuẩn xác từ GHN trên server
        $feeResponse = $ghn->calculateFee(array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
        ], $ghn->packageParameters($totalWeight)));

        $shippingFee = (isset($feeResponse['code']) && $feeResponse['code'] == 200)
            ? (int) $feeResponse['data']['total']
            : (int) $request->input('shipping_fee', 0);

        // Tổng thanh toán = Tiền hàng + Phí ship
        $finalTotal = $subtotal + $shippingFee;

        // Mã đơn hàng
        $orderCode = 'VP-' . strtoupper(dechex(time())) . rand(100, 999);

        // 3. Tạo đơn hàng và chi tiết đơn hàng trong Database
        $order = DB::transaction(function () use ($request, $shippingFee, $finalTotal, $subtotal, $orderCode, $cart) {
            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => Auth::id(),
                'customer_name'    => $request->name,
                'customer_phone'   => $request->phone,
                'customer_email'   => Auth::user()?->email,
                'shipping_address' => $request->address,
                'to_province_id'   => $request->to_province_id,
                'to_district_id'   => (int) $request->to_district_id,
                'to_ward_code'     => (string) $request->to_ward_code,
                'province_name'    => $request->province_name,
                'district_name'    => $request->district_name,
                'ward_name'        => $request->ward_name,
                'subtotal'         => $subtotal,
                'total_amount'     => $finalTotal,
                'shipping_fee'     => $shippingFee,
                'ghn_total_fee'    => $shippingFee,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending',
                'shipping_status'  => 'pending',
                'notes'            => $request->notes,
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'] ?? null,
                    'variant_id'    => $item['variant_id'] ?? null,
                    'product_name'  => $item['name'],
                    'price'         => $item['price'],
                    'quantity'      => $item['quantity'],
                    'variant_label' => $item['variant_label'] ?? '',
                    'image'         => $item['image'] ?? null,
                ]);

                // Trừ tồn kho
                $qty = (int) $item['quantity'];
                if (!empty($item['variant_id'])) {
                    ProductVariant::where('id', $item['variant_id'])->decrement('stock', $qty);
                } elseif (!empty($item['id'])) {
                    Product::where('id', $item['id'])->decrement('stock', $qty);
                }
            }

            return $order;
        });

        // Xóa session giỏ hàng
        session()->forget('cart');

        // 4. Phân luồng thanh toán
        if ($request->payment_method === 'momo') {
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway'  => 'momo',
                'amount'   => $order->total_amount,
                'status'   => 'pending',
            ]);

            return redirect()->route('orders.momo.start', $order);
        }

        PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'cod',
            'amount'   => $order->total_amount,
            'status'   => 'pending',
            'message'  => 'Thanh toán khi nhận hàng',
        ]);

        // --- NHÁNH COD: TẠO VẬN ĐƠN GHN NGAY LẬP TỨC ---
        $order->load('items.product');
        $ghnOrderResponse = $ghnOrders->create($order, false);

        if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
            $order->update([
                'ghn_order_code'  => $ghnOrderResponse['data']['order_code'],
                'shipping_status' => 'ready_to_pick',
            ]);

            return redirect()->route('orders.index')
                ->with('success', 'Đặt hàng thành công! Mã vận đơn GHN Express: ' . $ghnOrderResponse['data']['order_code']);
        }

        Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
        return redirect()->route('orders.index')
            ->with('warning', 'Đặt hàng thành công nhưng chưa thể tạo vận đơn GHN tự động.');
    }

    // Hủy đơn hàng của khách hàng & Hủy vận đơn GHN Express nếu có
    public function cancel(Request $request, $id, \App\Services\GHNService $ghn)
    {
        $order = Order::with('items')->findOrFail($id);

        // Kiểm tra quyền sở hữu đơn hàng
        if ($order->user_id !== Auth::id() && !Auth::user()?->is_admin) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        // Chỉ cho phép hủy khi đơn chưa giao đi (pending hoặc preparing)
        if (!in_array($order->shipping_status, ['pending', 'preparing'])) {
            return redirect()->back()->with('error', 'Không thể hủy đơn hàng vì đơn đã được bàn giao cho Shipper vận chuyển hoặc đã hoàn tất.');
        }

        // 1. Gọi API hủy vận đơn trên hệ thống GHN Express
        if (!empty($order->ghn_order_code)) {
            try {
                $ghn->cancelOrder([$order->ghn_order_code]);
            } catch (\Exception $e) {
                // Log lỗi nhưng vẫn tiếp tục hủy đơn trên hệ thống
            }
        }

        // 2. Hoàn lại số lượng tồn kho cho biến thể/sản phẩm trong đơn
        foreach ($order->items as $item) {
            if ($item->variant_id) {
                \App\Models\ProductVariant::where('id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            } elseif ($item->product_id) {
                \App\Models\Product::where('id', $item->product_id)
                    ->increment('stock', $item->quantity);
            }
        }

        // 3. Cập nhật trạng thái đơn hàng
        $order->update([
            'shipping_status' => 'cancelled',
        ]);

        return redirect()->back()->with('success', "Đã hủy đơn hàng [#{$order->order_code}] và hủy vận đơn GHN Express thành công!");
    }

    // Khách hàng đồng bộ realtime trạng thái vận đơn trực tiếp từ GHN
    public function syncGHN($id, \App\Services\GHNService $ghn)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && !Auth::user()?->is_admin) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        if (empty($order->ghn_order_code)) {
            return redirect()->back()->with('error', 'Đơn hàng này chưa có mã vận đơn GHN Express.');
        }

        $result = $ghn->syncOrderStatus($order);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
