# **Đặt hàng – Orders** 

Để thêm chức năng đặt hàng vào ứng dụng Laravel của bạn, chúng ta sẽ thực hiện các bước sau: 

   1. Tạo migration cho bảng payment_transactions và các models 

   2. Tạo services và khai báo chỉ số Momo 

   3. Tạo controller OrderController và MomoController. 

   4. Thêm các route liên quan đến đơn hàng. 

   5. Tạo giao diện để cho phép người dùng đặt hàng. 

**1. Tạo Migration Cho Bảng Payment_transection và models tương ứng.** 

Bảng payment_transactions đóng vai trò là nhật ký ghi vết tài chính độc lập giữa hệ thống của bạn và các cổng thanh toán bên thứ ba (như VNPay, MoMo, Stripe, PayPal). 

Với Lab shipping đã nhồi nhét quá nhiều trạng thái vận chuyển vào đơn hàng, để tránh việc dồn quá nhiều thông tin vào 1 bảng order=> Bảng payment_transactions được sinh ra để giải quyết các mục đích cốt lõi sau: 

- Tách biệt dữ liệu đơn hàng và dòng tiền (1 - N): Một đơn hàng (order) có thể có nhiều lần thanh toán. Người dùng có thể thanh toán thất bại 2 lần qua MoMo trước khi đổi ý sang quẹt thẻ qua VNPay thành công. Bảng này lưu lại toàn bộ các lần thử đó thay vì ghi đè trạng thái lên đơn hàng. 

- Lưu vết đối soát và mã giao dịch từ Gateway: Lưu giữ các mã định danh quan trọng do cổng thanh toán trả về (gateway_transaction_id, bank_code, response_code). Đây là căn cứ duy nhất để bạn đối soát doanh thu hoặc kiểm tra lỗi khi người mua báo đã trừ tiền nhưng đơn chưa hoàn tất. 

- Lưu trữ Payload/Log gốc (Webhook & IPN): Khi cổng thanh toán bắn IPN/Webhook về Laravel, bạn cần lưu lại toàn bộ JSON payload thô (payload hoặc response_data). Nếu xảy ra tranh chấp hoặc mã lỗi không xác định, bạn có log để debug mà không phụ thuộc vào log file hệ thống. 

- Xử lý hoàn tiền (Refund) và thanh toán từng phần: Khi khách hủy đơn hoặc trả hàng một phần, giao dịch hoàn tiền sẽ được ghi nhận là một bản ghi transaction mới (thường mang type: refund hoặc số tiền âm) liên kết ngược lại với giao dịch gốc. 

1 

php artisan make:model payment_transactions -m 

## Cập nhật các migration 

```
publicfunctionup(): void
    {
```

```
Schema::create('payment_transactions', function (Blueprint$table) {
$table->id();
```

```
$table->foreignId('order_id')->constrained()->cascadeOnDelete();
```

```
$table->string('gateway');     #phân biệt các cổng thanh toán khác nhau (ví dụ: PayPal, Stripe,
VNPay, Momo, v.v.)
```

```
$table->string('gateway_order_id')->nullable()->index(); #lưu trữ ID đơn hàng từ cổng thanh toán (ví
dụ: ID giao dịch từ PayPal, Stripe, VNPay, Momo, v.v.)
```

```
$table->string('transaction_id')->nullable()->index();   #lưu trữ ID giao dịch từ cổng thanh toán (ví
dụ: ID giao dịch từ PayPal, Stripe, VNPay, Momo, v.v.)
```

- <mark>`$table->decimal('amount', 15, 2);  #lưu trữ số tiền thanh toán (ví dụ: số tiền thanh toán từ PayPal,`</mark> 

- <mark>`Stripe, VNPay, Momo, v.v.)`</mark> 

```
$table->string('status')->default('pending');  #trạng thái giao dịch thanh toán (ví dụ: pending,
completed, failed, canceled, v.v.)
```

```
$table->integer('result_code')->nullable();  #lưu trữ mã kết quả từ cổng thanh toán (ví dụ: mã lỗi từ
PayPal, Stripe, VNPay, Momo, v.v.)
```

- <mark>`$table->string('message')->nullable();  #lưu trữ thông điệp từ cổng thanh toán (ví dụ: thông báo lỗi`</mark> 

- <mark>`từ PayPal, Stripe, VNPay, Momo, v.v.)`</mark> 

```
$table->json('request_payload')->nullable();  #lưu trữ dữ liệu yêu cầu gửi đến cổng thanh toán
$table->json('response_payload')->nullable();  #lưu trữ dữ liệu phản hồi từ cổng thanh toán
$table->timestamp('paid_at')->nullable();  #lưu trữ thời gian thanh toán thành công (nếu có)
$table->timestamps();
```

```
$table->unique(['gateway', 'gateway_order_id']);#đảm bảo rằng mỗi cổng thanh toán chỉ có một giao
dịch duy nhất cho mỗi ID đơn hàng từ cổng thanh toán
```

```
$table->index(['order_id', 'status']); #tạo chỉ mục để tối ưu hóa truy vấn theo order_id và status,
giúp tìm kiếm các giao dịch thanh toán theo đơn hàng và trạng thái nhanh hơn
        });
```

```
    }
```

2 

Cập nhật các model: 

```
classPaymentTransactionextendsModel
{
protected$fillable = [
'order_id',
'gateway',
'gateway_order_id',
'transaction_id',
'amount',
'status',
'result_code',
'message',
'request_payload',
'response_payload',
'paid_at',
    ];
```

```
protectedfunctioncasts(): array#dùng để chỉ định cách các thuộc tính của mô hình được chuyển đổi sang các
kiểu dữ liệu khác nhau khi truy xuất hoặc lưu trữ trong cơ sở dữ liệu
    {
return [
'amount' => 'decimal:2',
'request_payload' => 'array',
'response_payload' => 'array',
'paid_at' => 'datetime',
        ];
    }
publicfunctionorder(): BelongsTo
    {
return$this->belongsTo(Order::class);
    }
}
```

3 

## **<u>2. Tạo services và khai báo chỉ số Momo</u>** 

Có thể tham khảo DOCUMENT của momo: 

## **https://developers.momo.vn/v2/#/** 

## **https://developers.momo.vn/v3/vi/docs/payment/onboarding/overall/** 

## Khai báo ENV. 

```
MOMO_ENDPOINT = https://test-payment.momo.vn/v2/gateway/api/create
MOMO_PARTNER_CODE = MOMOBKUN20180529
```

```
MOMO_ACCESS_KEY = klm05TvNBzhg7h7j
MOMO_SECRET_KEY = at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa
MOMO_VERIFY_SSL=false
```

******LƯU Ý** : Momo yêu cầu tạo tài khoản dev và production đều là tài khoản doanh nghiệp vậy nên các trường như: 

```
MOMO_ENDPOINT
```

```
MOMO_PARTNER_CODE
```

```
MOMO_ACCESS_KEY
```

```
MOMO_SECRET_KEY
```

Đều giữ nguyên các giá trị để test. 

Truy cập vào config/services.php 

```
'momo' => [
```

```
'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
'partner_code' => env('MOMO_PARTNER_CODE'),
```

```
'access_key' => env('MOMO_ACCESS_KEY'),
''''
secret_key=> env(MOMO_SECRET_KEY),
```

4 

```
'verify_ssl' => env('MOMO_VERIFY_SSL', true),
'redirect_url' => env('MOMO_REDIRECT_URL'),
'ipn_url' => env('MOMO_IPN_URL'),
    ],
```

Tạo **folder** Services riêng tại mục http/controllers/Services 

Xây dựng bộ Services của Momo bao gồm: 

- http/controllers/Services/ **MomoService.php** 

Đây là lớp **kết nối trực tiếp đến API MOMO** trong đó: **createPayment()** -> Tạo yêu cầu thanh toán MoMo **isSuccessful()** -> Kiểm tra MoMo có báo thanh toán thành công hay không. **markPaid()** -> Cập nhật giao dịch sau khi MoMo thanh toán thành công. **markFailed()** -> Cập nhật giao dịch thất bại hoặc bị hủy. **isValidSuccessfulResponse()** -> Kiểm tra callback MoMo thành công đầy đủ. **isValidResponse()** -> Kiểm tra chữ ký callback MoMo. 

**orderId()** -> Lấy ID đơn hàng nội bộ từ trường. 

```
namespaceApp\Services;
```

```
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
```

5 

```
classMomoService
{
publicfunctioncreatePayment(Order$order, PaymentTransaction$transaction): array
    {
$endpoint = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
''
$partnerCode = config('services.momo.partner_code', env('MOMO_PARTNER_CODE', ));
''
$accessKey = config('services.momo.access_key', env('MOMO_ACCESS_KEY', ));
''
$secretKey = config('services.momo.secret_key', env('MOMO_SECRET_KEY', ));
$orderInfo = 'Thanh toan don hang #' . $order->id;
$amount = (string) ((int) $order->total_price);
$orderId = $order->id . '_' . $transaction->id . '_' . time();
$redirectUrl = config('services.momo.redirect_url') ?: route('user.payment.momo.callback');
$ipnUrl = config('services.momo.ipn_url') ?: route('payment.momo.ipn');
$extraData = (string) $order->id;
$requestId = (string) time();
$requestType = 'payWithCC';
$rawHash = 'accessKey=' . $accessKey .
'&amount=' . $amount .
'&extraData=' . $extraData .
'&ipnUrl=' . $ipnUrl .
'&orderId=' . $orderId .
'&orderInfo=' . $orderInfo .
'&partnerCode=' . $partnerCode .
'&redirectUrl=' . $redirectUrl .
'&requestId=' . $requestId .
'&requestType=' . $requestType;
$data = [
'partnerCode' => $partnerCode,
'partnerName' => 'Fruit Shop',
'storeId' => 'MomoStore',
'requestId' => $requestId,
'amount' => $amount,
'orderId' => $orderId,
```

6 

```
'orderInfo' => $orderInfo,
'redirectUrl' => $redirectUrl,
'ipnUrl' => $ipnUrl,
'lang' => 'vi',
'extraData' => $extraData,
'requestType' => $requestType,
'signature' => hash_hmac('sha256', $rawHash, $secretKey),
        ];
```

```
$transaction->update([
'gateway_order_id' => $orderId,
'request_payload' => $data,
        ]);
$response = Http::withOptions([
'verify' => filter_var(config('services.momo.verify_ssl', true), FILTER_VALIDATE_BOOLEAN),
        ])->post($endpoint, $data);
$result = $response->json() ?? [];
```

```
$transaction->update([
'response_payload' => $result,
'result_code' => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
'message' => $result['message'] ?? null,
'status' => isset($result['payUrl']) ? 'initiated' : 'failed',
        ]);
return$result;
    }
publicfunctionisSuccessful(array$payload): bool
    {
return (string) ($payload['resultCode'] ?? '') === '0';
    }
```

```
publicfunctionmarkPaid(PaymentTransaction$transaction,array$payload): void
```

7 

```
    {
$transaction->update([
'transaction_id' => $payload['transId'] ?? null,
'result_code' => (int) ($payload['resultCode'] ?? 0),
'message' => $payload['message'] ?? null,
'response_payload' => $payload,
'status' => 'paid',
'paid_at' => Carbon::now(),
        ]);
    }
publicfunctionmarkFailed(PaymentTransaction$transaction, array$payload): void
    {
$transaction->update([
'transaction_id' => $payload['transId'] ?? null,
'result_code' => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
'message' => $payload['message'] ?? null,
'response_payload' => $payload,
'status' => 'failed',
        ]);
    }
publicfunctionisValidSuccessfulResponse(array$payload): bool
    {
return$this->isValidResponse($payload) && $this->isSuccessful($payload);
    }
```

```
publicfunctionisValidResponse(array$payload): bool
    {
if (!isset($payload['signature'])) {
returnfalse;
        }
''
$accessKey = config('services.momo.access_key', );
'''
$secretKey = config('services.momo.secret_key,);
```

8 

```
$rawHash = 'accessKey=' . $accessKey .
'&amount=' . ($payload['amount'] ?? '') .
'&extraData=' . ($payload['extraData'] ?? '') .
'&message=' . ($payload['message'] ?? '') .
'&orderId=' . ($payload['orderId'] ?? '') .
'&orderInfo=' . ($payload['orderInfo'] ?? '') .
'&orderType=' . ($payload['orderType'] ?? '') .
'&partnerCode=' . ($payload['partnerCode'] ?? '') .
'&payType=' . ($payload['payType'] ?? '') .
'&requestId=' . ($payload['requestId'] ?? '') .
'&responseTime=' . ($payload['responseTime'] ?? '') .
'&resultCode=' . ($payload['resultCode'] ?? '') .
'&transId=' . ($payload['transId'] ?? '');
returnhash_equals(
hash_hmac('sha256', $rawHash, $secretKey),
string) $payload['signature']
publicfunctionorderId(array$payload): ?int
$orderId = $payload['extraData'] ?? null;
```

```
returnis_numeric($orderId) ? (int) $orderId : null;
    }
}
```



## **<u>3. Tạo controller OrderController và MomoController.</u>** 

## **<u><mark>Cập nhật</mark></u>** <u><mark>OrderController:</mark></u> 

9 

```
// ==========================================
// 3. XỬ LÝ ĐẶT HÀNG (PROCESS PAYMENT)
```

```
// ==========================================
```

```
publicfunctionprocessPayment(Request$request, GHNService$ghn, GHNOrderService$ghnOrders)
    {
```

```
$request->validate([
'name'           => 'required|string|max:100',
'phone'          => ['required', 'regex:/^0\d{9}$/'],
'address'        => 'required|string|max:255',
'to_district_id' => 'required|integer',
'to_ward_code'   => 'required|string',
'payment_method' => 'required|in:cod,momo',
```

```
$cart = session('cart', []);
if (empty($cart)) {
```

```
returnredirect()->route('user.cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng
trống.');
        }
```

```
// 1. Tính tổng tiền hàng và tổng khối lượng sản phẩm
$subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
$totalWeight = collect($cart)->sum(
fn($item) => $ghn->productWeight() * (int) $item['quantity']
```

```
// 2. Tính lại phí ship chuẩn xác từ GHN trên server
$feeResponse = $ghn->calculateFee(array_merge([
'from_district_id' => (int) config('services.ghn.from_district_id'),
'to_district_id'   => (int) $request->to_district_id,
'to_ward_code'     => (string) $request->to_ward_code,
$ghn->packageParameters($totalWeight)));
```

```
$shippingFee = (isset($feeResponse['code']) && $feeResponse['code'] == 200)
```

10 

```
            ? (int) $feeResponse['data']['total']
            : 0;
```

```
// Tổng thanh toán = Tiền hàng + Phí ship
$finalTotal = $subtotal + $shippingFee;
```

```
// 3. Tạo đơn hàng và chi tiết đơn hàng trong Database
$order = DB::transaction(function () use ($request, $shippingFee, $finalTotal, $cart) {
$order = Order::create([
'user_id'         => Auth::id(),
'name'            => $request->name,
'address'         => $request->address,
'phone'           => $request->phone,
'total_price'     => $finalTotal,
'status'          => 'pending',
'to_district_id'  => (int) $request->to_district_id,
'to_ward_code'    => (string) $request->to_ward_code,
'ghn_total_fee'   => $shippingFee,
'shipping_status' => 'pending',
            ]);
```

```
foreach ($cart as $item) {
OrderItem::create([
'order_id'   => $order->id,
'product_id' => $item['id'],
'quantity'   => $item['quantity'],
'price'      => $item['price'],
                ]);
            }
return$order;
        });
// Xóa session giỏ hàng
session()->forget('cart');
```

11 

```
// 4. Phân luồng thanh toán
if ($request->payment_method === 'momo') {
PaymentTransaction::create([
'order_id' => $order->id,
'gateway' => 'momo',
'amount' => $order->total_price,
'status' => 'pending',
            ]);
returnredirect()->route('user.orders.momo.start', $order);
        }
PaymentTransaction::create([
'order_id' => $order->id,
'gateway' => 'cod',
'amount' => $order->total_price,
'status' => 'pending',
'message' => 'Thanh toán khi nhận hàng',
        ]);
// --- NHÁNH COD: TẠO VẬN ĐƠN GHN NGAY LẬP TỨC ---
$order->load('items.product');
$ghnOrderResponse = $ghnOrders->create($order);
```

```
if (($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) {
$order->update([
'status'          => 'cod_ordered',
'ghn_order_code'  => $ghnOrderResponse['data']['order_code'],
'shipping_status' => 'ready_to_pick',
            ]);
returnredirect()->route('user.orders.index')
                ->with('success', 'Đặt hàng thành công! Mã vận đơn GHN: ' .
$ghnOrderResponse['data']['order_code']);
```

12 

```
        }
```

```
Log::error('GHN COD Order Failed: ', $ghnOrderResponse ?? []);
$order->update(['status' => 'cod_ordered']);
returnredirect()->route('user.orders.index')
```

- <mark>`->with('warning', 'Đặt hàng thành công nhưng chưa thể tạo vận đơn GHN tự động.'); }`</mark> 

Tạo MomoController.php 

*******Lưu ý** vị trí thư mục (không hoàn toàn theo hướng dẫn) 

```
php artisan make:controller User/MomoController
```

Cập nhật nội dung MomoController 

Các hàm controller được tạo bao gồm: 

1. start() — bắt đầu thanh toán 

2. payAgain() — thanh toán lại 

3. newTransaction() — lưu một lần thử thanh toán 

4. redirectToMomo() — lấy đường dẫn thanh toán 

5. callback() — xử lý khi khách quay về website 

6. ipn() — nhận thông báo trực tiếp từ MoMo 

13 

## 7. completePayment() — xác nhận thanh toán và tạo vận đơn 

## 8. markFailed() — ghi nhận giao dịch thất bại 

************   flowchart 

A[Đơn hàng đã được tạo] --> B[start hoặc payAgain] --> C[Tạo bản ghi giao dịch] --> D[MomoService gọi API MoMo] --> E[Chuyển khách đến payUrl] --> F[Nhận kết quả qua callback hoặc IPN] --> G[Kiểm tra chữ ký và kết quả] --> H[Đối chiếu giao dịch và số tiền] --> I[Đánh dấu đã thanh toán] --> J[Tạo vận đơn GHN] 

``` 

```
<?php
namespaceApp\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
```

```
classMomoControllerextendsController
{
publicfunctionstart(Order$order, MomoService$momo)
    {
if ($order->user_id !== Auth::id()) {
abort(403);
        }
return$this->redirectToMomo($order,$this->newTransaction($order),$momo);
```

14 

```
    }
publicfunctionpayAgain(Order$order, MomoService$momo)
    {
if ($order->user_id !== Auth::id()) {
abort(403);
        }
return$this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }
```

```
publicfunctioncallback(Request$request, GHNOrderService$ghnOrders, MomoService$momo)
    {
```

```
Log::info('MoMo callback received', [
'payload' => $request->except('signature'),
'has_signature' => $request->has('signature'),
        ]);
if (!$momo->isValidSuccessfulResponse($request->all())) {
Log::warning('MoMo callback rejected', [
'result_code' => $request->input('resultCode'),
'order_id' => $request->input('orderId'),
'signature_valid' => $momo->isValidResponse($request->all()),
            ]);
if ($momo->isValidResponse($request->all())) {
$this->markFailed($request->all(), $momo);
            }
```

```
returnredirect()->route('user.orders.index')->with('error', 'Giao dịch MoMo thất bại.');
```

```
        }
```

```
$result = $this->completePayment($request->all(), $ghnOrders, $momo);
$message = in_array($result, ['created', 'already_created'], true)
            ? 'Thanh toán MoMo thành công! Vận đơn GHN đã được khởi tạo.'
```

```
'Thanh toán thành công! Đơn hàng đang chờ tạo vận đơn GHN.';
```

15 

```
returnredirect()->route('user.orders.index')->with('success', $message);
    }
```

```
publicfunctionipn(Request$request, GHNOrderService$ghnOrders, MomoService$momo)
    {
```

```
Log::info('MoMo IPN received', [
'payload' => $request->except('signature'),
'has_signature' => $request->has('signature'),
        ]);
if ($momo->isValidSuccessfulResponse($request->all())) {
$this->completePayment($request->all(), $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($request->all())) {
$this->markFailed($request->all(), $momo);
        }
```

```
returnresponse()->json(['message' => 'Received']);
    }
```

```
privatefunctionnewTransaction(Order$order): PaymentTransaction
    {
```

```
returnPaymentTransaction::create([
'order_id' => $order->id,
'gateway' => 'momo',
'amount' => $order->total_price,
'status' => 'pending',
        ]);
    }
```

```
privatefunctionredirectToMomo(Order$order, PaymentTransaction$transaction, MomoService$momo)
    {
```

```
$result = $momo->createPayment($order, $transaction);
```

```
returnisset($result['payUrl'])
```

16 

```
            ? redirect($result['payUrl'])
```

```
            : redirect()->route('user.orders.index')->with('error', 'Không thể kết nối tới MoMo.');
    }
```

```
privatefunctioncompletePayment(array$payload, GHNOrderService$ghnOrders, MomoService$momo): string
    {
```

```
$result = DB::transaction(function () use ($payload, $momo) {
```

```
$transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();
if (!$transaction) {
return'invalid';
            }
```

```
$order = Order::lockForUpdate()->find($transaction->order_id);
if (!$order) {
return'invalid';
            }
```

```
if ($order->ghn_order_code) {
return'already_created';
            }
if ($order->shipping_status === 'processing') {
return'processing';
            }
```

```
if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
$momo->markFailed($transaction, $payload);
return'invalid';
            }
```

```
$order->update(['status' => 'paid','shipping_status' => 'processing']);
```

17 

```
$momo->markPaid($transaction, $payload);
return ['create', $order->id];
        });
if (!is_array($result)) {
return (string) $result;
        }
$order = Order::with('items.product')->find($result[1]);
$response = $ghnOrders->create($order, true);
if (isset($response['code']) && $response['code'] === 200) {
$order->update([
'ghn_order_code' => $response['data']['order_code'],
'shipping_status' => 'ready_to_pick',
            ]);
return'created';
        }
Log::error('GHN order failed after MoMo payment', [
'order_id' => $order->id,
'response' => $response,
        ]);
$order->update(['shipping_status' => 'pending']);
return'failed';
    }
privatefunctionmarkFailed(array$payload, MomoService$momo): void
    {
$transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();
```

18 

```
if ($transaction && $transaction->status !== 'paid') {
$momo->markFailed($transaction, $payload);
        }
    }
}
```

## **<u>4. Thêm Các Route Liên Quan Đến Đơn Hàng</u>** 

```
use App\Http\Controllers\User\MomoController;
```

<mark>`|-------------------------------------------------------------------------|` 🚚</mark> <mark>`THIRD-PARTY WEBHOOKS & CALLBACKS (GHN, MOMO IPN) |-------------------------------------------------------------------------| NOTE: | - Không dùng middleware 'auth' vì bên thứ 3 (GHN, MoMo) gọi sang tự động. | - Đã được bypass CSRF trong bootstrap/app.php. |-------------------------------------------------------------------------*/`</mark> 

```
Route::post('/ghn/webhook', [GHNWebhookController::class, 'handle'])->name('ghn.webhook');
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');
```

```
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {
// Payment
Route::get('/payment', [OrderController::class, 'index'])->name('payment.index');
Route::post('/payment/process', [OrderController::class, 'processPayment'])->name('payment.process');
Route::get('/orders/{order}/pay/momo',[MomoController::class,'payAgain'])->name('orders.momo.pay');
```

19 

<mark>`Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');`</mark> } 

## **<u>5. Tạo giao diện để cho phép người dùng đặt hàng.</u>** 

Tùy biến giao diện tương ứng với thao tác 

20 

**Mô tả luồng** 

## - Từ trang card/index thực hiện nút thanh toán 

- => payment/index trang này cho phép người dùng điền thông tin cá nhân để vận chuyển cũng như xác nhận lại các sản phẩm trong giỏ hàng 

=> chọn thanh toán momo hoặc COD 

- => nếu COD đơn hàng được ghi nhận chờ thanh toán và lên đơn thành công (giỏ hàng xóa) 

=> nếu momo đơn hàng chuyển hướng đến trang momo test để thực hiện thanh toán, thao tác thanh toán ATM bình thường theo nhưng tài khoản thẻ có sẵn như sau: 

|**No**|**Tên**|**Số thẻ**|**Hạn ghi trên thẻ**|**OTP**|**Trường hợp**|
|---|---|---|---|---|---|
|1|NGUYEN VAN A|9704 0000 0000 0018|12/30|OTP|Thành công|
|2|NGUYEN VAN A|9704 0000 0000 0026|12/30|OTP|Thẻ khóa|
|3|NGUYEN VAN A|9704 0000 0000 0034|12/30|OTP|Không đủ tiền|
|4|NGUYEN VAN A|9704 0000 0000 0042|12/30|OTP|Hạn mức thẻ|



=> nếu thanh toán không thành công trang trở về lịch sử đơn và báo trạng thái cũng như hiển thị nút thanh toán lại (thanh toán lại không tính thành đơn hàng mới) 

- => thanh toán thành công đổi trạng thái đã thanh toán thành công và xóa giỏ hàng 

21 

- _— — auth : layouts ® admin.blade.php po) SEENESEETZ — # index.blade.php > categories 

lL * vente > products product_detail.blade.php @ welcome.blade.php 

- ~ larvidu2 ~ app Y Http “ Controllers > Admin ™ User * CartController.php CategoryController.php ® ChatController.php GHNController.php ® GHNWebhookController.php MomoController.php 

   - ® WelcomeController.php 

   - ® AuthController.php Controller.php 

   - > Middleware 

   - > Models 

