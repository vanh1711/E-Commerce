<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress;

class CheckoutController extends Controller
{
    // Tọa độ Store PhoneStore Flagship (Hà Nội)
    const STORE_LAT = 21.028511;
    const STORE_LNG = 105.854444;

    // Hiển thị trang thanh toán
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        // Lọc các item được chọn thanh toán nếu có truyền selected_ids
        $selectedIds = $request->input('selected_ids');
        if ($selectedIds) {
            $idsArray = is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds);
            $checkoutCart = array_filter($cart, fn($key) => in_array($key, $idsArray), ARRAY_FILTER_USE_KEY);
        } else {
            $checkoutCart = $cart;
        }

        if (empty($checkoutCart)) {
            $checkoutCart = $cart;
        }

        $subtotal = collect($checkoutCart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $user = Auth::user();

        // Cấu hình ngân hàng VietQR theo đúng hình ảnh cung cấp
        $bankConfig = [
            'bank_id'      => 'MB', // MBBank hoặc chuyển liên ngân hàng Napas
            'account_no'   => '12317112005',
            'account_name' => 'DOAN VIET ANH',
        ];

        // Load danh sách sổ địa chỉ đã lưu của khách hàng (nếu đã đăng nhập)
        $savedAddresses = collect();
        if (Auth::check()) {
            $savedAddresses = UserAddress::where('user_id', Auth::id())
                ->orderByDesc('is_default')
                ->orderByDesc('updated_at')
                ->get();
        }

        return view('checkout.index', compact('checkoutCart', 'subtotal', 'user', 'bankConfig', 'savedAddresses'));
    }

    // API tính phí ship theo GPS (Haversine formula)
    public function calculateShipping(Request $request)
    {
        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');

        if (!$lat || !$lng) {
            return response()->json([
                'success' => false,
                'message' => 'Tọa độ GPS không hợp lệ.'
            ], 422);
        }

        $distance = $this->calculateHaversineDistance(self::STORE_LAT, self::STORE_LNG, $lat, $lng);
        
        // Dưới 10km: Free ship (0đ). Hơn 10km: Mỗi km tính 10.000đ
        $shippingFee = 0;
        if ($distance > 10) {
            $extraKm = ceil($distance - 10);
            $shippingFee = $extraKm * 10000;
        }

        return response()->json([
            'success'      => true,
            'distance_km'  => round($distance, 2),
            'shipping_fee' => $shippingFee,
            'store_lat'    => self::STORE_LAT,
            'store_lng'    => self::STORE_LNG,
            'fee_formatted'=> number_format($shippingFee) . ' đ',
            'is_free'      => ($distance <= 10),
        ]);
    }

    // API tìm kiếm địa chỉ (Geocoding backend proxy an toàn 100% không bị CORS)
    public function geocodeSearch(Request $request)
    {

        $query = trim($request->input('q', ''));
        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập từ khóa tìm kiếm.']);
        }

        try {
            $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($query . ', Việt Nam') . "&limit=5&addressdetails=1";
            
            $res = \Illuminate\Support\Facades\Http::timeout(3)
                ->withHeaders([
                    'User-Agent' => 'PhoneStoreApp/1.0 (contact: support@phonestore.vn)'
                ])
                ->get($url);

            if ($res->successful()) {
                $data = $res->json();
                if (!empty($data) && is_array($data)) {
                    return response()->json([
                        'success' => true,
                        'results' => array_map(fn($item) => [
                            'display_name' => $item['display_name'],
                            'lat'          => (float) $item['lat'],
                            'lng'          => (float) $item['lon'],
                        ], $data)
                    ]);
                }
            }
        } catch (\Exception $e) {}

        // Fallback tọa độ một số địa danh phổ biến nếu máy chủ bận
        $popularLocations = [
            'hoài đức' => ['lat' => 20.9984, 'lng' => 105.7042, 'name' => 'Huyện Hoài Đức, Hà Nội, Việt Nam'],
            'thanh oai' => ['lat' => 20.8714, 'lng' => 105.7766, 'name' => 'Huyện Thanh Oai, Hà Nội, Việt Nam'],
            'kim bài' => ['lat' => 20.8714, 'lng' => 105.7766, 'name' => 'Thị trấn Kim Bài, Thanh Oai, Hà Nội, Việt Nam'],
            'cầu giấy' => ['lat' => 21.0333, 'lng' => 105.7833, 'name' => 'Quận Cầu Giấy, Hà Nội, Việt Nam'],
            'đống đa' => ['lat' => 21.0189, 'lng' => 105.8288, 'name' => 'Quận Đống Đa, Hà Nội, Việt Nam'],
            'hà đông' => ['lat' => 20.9723, 'lng' => 105.7772, 'name' => 'Quận Hà Đông, Hà Nội, Việt Nam'],
            'nam từ liêm' => ['lat' => 21.0183, 'lng' => 105.7642, 'name' => 'Quận Nam Từ Liêm, Hà Nội, Việt Nam'],
            'bắc từ liêm' => ['lat' => 21.0664, 'lng' => 105.7538, 'name' => 'Quận Bắc Từ Liêm, Hà Nội, Việt Nam'],
            'hoàng mai' => ['lat' => 20.9733, 'lng' => 105.8500, 'name' => 'Quận Hoàng Mai, Hà Nội, Việt Nam'],
            'long biên' => ['lat' => 21.0362, 'lng' => 105.8929, 'name' => 'Quận Long Biên, Hà Nội, Việt Nam'],
            'vĩnh phúc' => ['lat' => 21.3609, 'lng' => 105.5474, 'name' => 'Tỉnh Vĩnh Phúc, Việt Nam'],
            'bắc ninh' => ['lat' => 21.1861, 'lng' => 106.0763, 'name' => 'Tỉnh Bắc Ninh, Việt Nam'],
            'hải phòng' => ['lat' => 20.8449, 'lng' => 106.6881, 'name' => 'Thành phố Hải Phòng, Việt Nam'],
            'đà nẵng' => ['lat' => 16.0544, 'lng' => 108.2022, 'name' => 'Thành phố Đà Nẵng, Việt Nam'],
            'hồ chí minh' => ['lat' => 10.8231, 'lng' => 106.6297, 'name' => 'Thành phố Hồ Chí Minh, Việt Nam'],
        ];

        $lowerQuery = mb_strtolower($query, 'UTF-8');
        foreach ($popularLocations as $key => $loc) {
            if (str_contains($lowerQuery, $key) || str_contains($key, $lowerQuery)) {
                return response()->json([
                    'success' => true,
                    'results' => [[
                        'display_name' => $loc['name'],
                        'lat'          => $loc['lat'],
                        'lng'          => $loc['lng'],
                    ]]
                ]);
            }
        }

        return response()->json(['success' => false, 'message' => "Không tìm thấy địa điểm \"{$query}\". Vui lòng kiểm tra lại hoặc click trực tiếp lên bản đồ!"]);
    }

    public function process(Request $request, \App\Services\GHNService $ghn, \App\Services\GHNOrderService $ghnOrderService)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:20',
            'customer_email'   => 'nullable|email|max:255',
            'shipping_address' => 'required|string|max:500',
            'to_province_id'   => 'nullable|integer',
            'to_district_id'   => 'nullable|integer',
            'to_ward_code'     => 'nullable|string',
            'province_name'    => 'nullable|string',
            'district_name'    => 'nullable|string',
            'ward_name'        => 'nullable|string',
            'payment_method'   => 'required|in:cod,momo,bank_transfer',
            'shipping_fee'     => 'nullable|numeric|min:0',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'selected_ids'     => 'nullable|string',
        ]);

        $fullCart = session()->get('cart', []);
        if (empty($fullCart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đã hết hạn hoặc đang trống.');
        }

        // Lọc các item được chọn thanh toán nếu có truyền selected_ids (Fix lỗi #9)
        $selectedIds = $request->input('selected_ids');
        if ($selectedIds) {
            $idsArray = is_array($selectedIds) ? $selectedIds : explode(',', $selectedIds);
            $cart = array_filter($fullCart, fn($key) => in_array($key, $idsArray), ARRAY_FILTER_USE_KEY);
        } else {
            $cart = $fullCart;
        }

        if (empty($cart)) {
            $cart = $fullCart;
        }

        // Đồng bộ và tính lại giá và khối lượng từ Database máy chủ (Chống giả mạo giá client)
        $subtotal = 0;
        $weight   = 0;
        foreach ($cart as $key => &$cItem) {
            $itemPrice = (float) $cItem['price'];
            $itemWeight = (int) ($cItem['weight'] ?? 200);

            if (!empty($cItem['variant_id'])) {
                $v = ProductVariant::find($cItem['variant_id']);
                if ($v) {
                    $itemPrice = (float) $v->price;
                    $itemWeight = (int) $v->weight;
                }
            } elseif (!empty($cItem['id'])) {
                $p = Product::find($cItem['id']);
                if ($p) {
                    $itemPrice = (float) $p->price;
                }
            }

            $cItem['price'] = $itemPrice;
            $cItem['weight'] = $itemWeight;
            $subtotal += $itemPrice * (int) $cItem['quantity'];
            $weight   += $itemWeight * (int) $cItem['quantity'];
        }
        unset($cItem);

        // Tính phí ship chuẩn xác trên máy chủ (Chống giả mạo phí ship 0đ từ client - Fix lỗi #3)
        $shippingFee = 0;
        if ($request->filled('to_district_id') && $request->filled('to_ward_code')) {
            $feeRes = $ghn->calculateFee(array_merge([
                'from_district_id' => (int) config('services.ghn.from_district_id', 1450),
                'to_district_id'   => (int) $request->to_district_id,
                'to_ward_code'     => (string) $request->to_ward_code,
            ], $ghn->packageParameters($weight)));

            if (!empty($feeRes['data']['total'])) {
                $shippingFee = (float) $feeRes['data']['total'];
            }
        } elseif ($request->filled('latitude') && $request->filled('longitude')) {
            $lat = (float) $request->input('latitude');
            $lng = (float) $request->input('longitude');
            $distance = $this->calculateHaversineDistance(self::STORE_LAT, self::STORE_LNG, $lat, $lng);
            if ($distance > 10) {
                $shippingFee = ceil($distance - 10) * 10000;
            }
        } else {
            $shippingFee = 30000; // Cước cơ bản mặc định nếu không có dữ liệu GPS / GHN
        }

        $totalAmount = $subtotal + $shippingFee;

        // Sinh mã đơn hàng ngẫu nhiên duy nhất
        $orderCode = 'VP-' . strtoupper(dechex(time())) . rand(100, 999);

        // Chuẩn hóa địa chỉ đầy đủ
        $fullAddress = trim($request->shipping_address);
        if ($request->filled('ward_name') && !str_contains($fullAddress, $request->ward_name)) {
            $fullAddress .= ', ' . $request->ward_name . ', ' . $request->district_name . ', ' . $request->province_name;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_code'       => $orderCode,
                'user_id'          => Auth::id(),
                'customer_name'    => $request->customer_name,
                'customer_phone'   => $request->customer_phone,
                'customer_email'   => $request->customer_email ?? (Auth::user()?->email),
                'shipping_address' => $fullAddress,
                'to_province_id'   => $request->to_province_id,
                'to_district_id'   => $request->to_district_id,
                'to_ward_code'     => $request->to_ward_code,
                'province_name'    => $request->province_name,
                'district_name'    => $request->district_name,
                'ward_name'        => $request->ward_name,
                'latitude'         => $request->input('latitude', self::STORE_LAT),
                'longitude'        => $request->input('longitude', self::STORE_LNG),
                'distance_km'      => 0,
                'shipping_fee'     => $shippingFee,
                'ghn_total_fee'    => (int) $shippingFee,
                'subtotal'         => $subtotal,
                'total_amount'     => $totalAmount,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'pending',
                'shipping_status'  => 'pending',
                'notes'            => $request->notes,
            ]);

            // Trừ tồn kho an toàn bằng lockForUpdate() (Fix lỗi #8)
            foreach ($cart as $item) {
                $qty = (int) $item['quantity'];

                if (!empty($item['variant_id'])) {
                    $variant = ProductVariant::where('id', $item['variant_id'])->lockForUpdate()->first();
                    if (!$variant || $variant->stock < $qty) {
                        $stockLeft = $variant ? $variant->stock : 0;
                        throw new \Exception("Sản phẩm \"{$item['name']}\" ({$item['variant_label']}) chỉ còn {$stockLeft} máy trong kho.");
                    }
                    $variant->decrement('stock', $qty);
                } elseif (!empty($item['id'])) {
                    $product = Product::where('id', $item['id'])->lockForUpdate()->first();
                    if (!$product || $product->stock < $qty) {
                        $stockLeft = $product ? $product->stock : 0;
                        throw new \Exception("Sản phẩm \"{$item['name']}\" chỉ còn {$stockLeft} máy trong kho.");
                    }
                    $product->decrement('stock', $qty);
                }

                OrderItem::create([
                    'order_id'      => $order->id,
                    'product_id'    => $item['id'] ?? null,
                    'variant_id'    => $item['variant_id'] ?? null,
                    'product_name'  => $item['name'],
                    'price'         => $item['price'],
                    'quantity'      => $qty,
                    'variant_label' => $item['variant_label'] ?? '',
                    'image'         => $item['image'] ?? null,
                ]);
            }

            // Lưu địa chỉ vào sổ địa chỉ nếu khách hàng tích chọn (Fix lỗi #5: Gom nhóm điều kiện orWhere)
            if ($request->has('save_address') && Auth::check()) {
                UserAddress::updateOrCreate(
                    [
                        'user_id'        => Auth::id(),
                        'to_district_id' => $request->to_district_id,
                        'to_ward_code'   => $request->to_ward_code,
                        'address_line'   => trim($request->shipping_address),
                    ],
                    [
                        'recipient_name' => $request->customer_name,
                        'phone'          => $request->customer_phone,
                        'to_province_id' => $request->to_province_id,
                        'province_name'  => $request->province_name,
                        'district_name'  => $request->district_name,
                        'ward_name'      => $request->ward_name,
                        'is_default'     => $request->has('set_default_address'),
                    ]
                );

                if ($request->has('set_default_address')) {
                    UserAddress::where('user_id', Auth::id())
                        ->where(function ($q) use ($request) {
                            $q->where('to_ward_code', '!=', $request->to_ward_code)
                              ->orWhere('address_line', '!=', trim($request->shipping_address));
                        })
                        ->update(['is_default' => false]);
                }
            }

            // PHÂN LUỒNG THANH TOÁN (PAYMENT_TRANSACTION & GATEWAYS)
            if ($request->payment_method === 'momo') {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'gateway'  => 'momo',
                    'amount'   => $totalAmount,
                    'status'   => 'pending',
                ]);

                DB::commit();

                // Chỉ xóa các item vừa đặt khỏi giỏ (Fix lỗi #9)
                foreach (array_keys($cart) as $checkedKey) {
                    unset($fullCart[$checkedKey]);
                }
                session()->put('cart', $fullCart);
                session(['last_placed_order_id' => $order->id]);

                return redirect()->route('orders.momo.start', $order->id);
            }

            // Tạo bản ghi giao dịch cho COD / Chuyển khoản
            PaymentTransaction::create([
                'order_id' => $order->id,
                'gateway'  => $request->payment_method,
                'amount'   => $totalAmount,
                'status'   => 'pending',
                'message'  => match ($request->payment_method) {
                    'cod'           => 'Thanh toán tiền mặt khi nhận hàng (COD)',
                    'bank_transfer' => 'Chuyển khoản VietQR Napas 24/7',
                    default         => 'Đặt hàng thành công',
                },
            ]);

            // Đối với COD: Tự động tạo vận đơn GHN Express ngay lập tức (Chưa thanh toán: isPaid = false)
            if ($order->to_district_id && $order->to_ward_code) {
                $ghnResult = $ghnOrderService->create($order, false);
                if (!empty($ghnResult['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code'  => $ghnResult['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                    ]);
                }
            }

            DB::commit();

            // Chỉ xóa các item vừa đặt khỏi giỏ (Fix lỗi #9)
            foreach (array_keys($cart) as $checkedKey) {
                unset($fullCart[$checkedKey]);
            }
            session()->put('cart', $fullCart);
            session(['last_placed_order_id' => $order->id]);

            return redirect()->route('checkout.success', $order->id)->with('success', 'Đặt hàng thành công! Đơn hàng đã được liên kết với hệ thống GHN Express.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage())->withInput();
        }
    }

    // Trang hiển thị thông báo đặt hàng thành công & Mã VietQR (Fix lỗi #4: Bảo vệ kiểm tra quyền truy cập)
    public function success($id)
    {
        $order = Order::with('items')->findOrFail($id);

        // Kiểm tra quyền: Chỉ chủ đơn, Admin hoặc phiên đặt hàng vừa thực hiện mới được xem thông tin
        $isOwner  = Auth::check() && $order->user_id === Auth::id();
        $isAdmin  = Auth::check() && Auth::user()->isAdmin();
        $isRecent = session('last_placed_order_id') == $id;

        if (!$isOwner && !$isAdmin && !$isRecent) {
            abort(403, 'Bạn không có quyền truy cập thông tin đơn hàng này.');
        }

        // Sinh link VietQR chuẩn Napas 24/7
        $bankId = 'MB';
        $accountNo = '12317112005';
        $accountName = 'DOAN VIET ANH';
        $amount = (int) $order->total_amount;
        $addInfo = 'PS ' . $order->order_code;

        $vietQrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo=" . urlencode($addInfo) . "&accountName=" . urlencode($accountName);

        return view('checkout.success', compact('order', 'vietQrUrl', 'accountNo', 'accountName'));
    }

    // Hàm tính khoảng cách giữa 2 điểm GPS theo công thức Haversine
    private function calculateHaversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Bán kính trái đất (km)

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
