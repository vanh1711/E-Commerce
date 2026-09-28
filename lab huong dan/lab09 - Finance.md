# **XỬ LÝ, BÁO CÁO GIAO DỊCH THANH TOÁN – FINANCE** 

## **<u>Mục tiêu</u>** 

Xây dựng hai trang: thống kê tài chính và danh sách giao dịch; hỗ trợ tìm kiếm, lọc, phân trang và cập nhật thanh toán COD. Finance sử dụng hai bảng chính: 

- orders: mã đơn, người nhận, số điện thoại, tổng tiền, trạng thái đơn và ngày tạo. 

- payment_transactions: order_id, gateway, amount, status, paid_at; lưu các lần thanh toán của đơn hàng. 

KIỂM TRA các khai báo đã đầy đủ chưa, nếu chưa bổ sung: 

Trong app/Models/Order.php, khai báo quan hệ: 

```
publicfunctionpaymentTransactions()
{
return$this->hasMany(PaymentTransaction::class);
}
```

Trong app/Models/PaymentTransaction.php, khai báo quan hệ ngược: 

```
publicfunctionorder(): BelongsTo
{
return$this->belongsTo(Order::class);
}
```

## **<u>Thực hiện xử lý:</u>** 

1. Tạo Controller: 

php artisan make:controller Admin/FinanceController 

## 2. Cập nhật Controller vừa tạo: 

Tổ chức xử lý thành năm phương thức: 

- ordersQuery(): nối đơn hàng với một giao dịch đại diện, tránh đếm trùng đơn. Ưu tiên giao dịch đã thu/chờ hoàn/đã hoàn tiền, sau đó 

- chọn ID lớn nhất trong nhóm ưu tiên. 

   - filteredOrders(): kiểm tra đầu vào; lọc theo mã đơn, tên, điện thoại, khoảng ngày, số tiền, phương thức và trạng thái. 

   - index(): tính chỉ số và trả về view admin.finance.index. 

   - transactions(): lấy danh sách, sắp xếp và trả về view admin.finance.transactions. 

- updateStatus(): kiểm tra và lưu trạng thái thanh toán COD. 

|`<?p`|`hp`|
|---|---|
|`nam`|`espace App\Http\Controllers\Admin; `|
|`use `<br>`use `<br>`use `<br>`use `<br>`use `<br>`use `<br>`use `|`App\Http\Controllers\Controller; `<br>`App\Models\Order; `<br>`Carbon\Carbon; `<br>`Illuminate\Http\Request; `<br>`Illuminate\Support\Facades\DB; `<br>`Illuminate\Validation\Rule; `<br>`Illuminate\Validation\ValidationException; `|
|`cla`<br>`{`<br>|`ss FinanceController extends Controller`<br> `private const STATUSES =[`|



```
'pending' => 'Chờ thanh toán', 'initiated' => 'Đang chờ MoMo',
'paid' => 'Đã thanh toán', 'failed' => 'Thanh toán thất bại',
'cancelled' => 'Đã hủy', 'refund_pending' => 'Chờ hoàn tiền', 'refunded' => 'Đã hoàn tiền',
    ];
privateconst COD_TRANSITIONS = [
'pending' => ['pending', 'paid', 'failed'],
'failed' => ['failed', 'pending', 'paid'],
'paid' => ['paid', 'refund_pending'],
'refund_pending' => ['refund_pending', 'refunded'],
'refunded' => ['refunded'], 'cancelled' => ['cancelled'],
    ];
// Ưu tiên giao dịch đã thu/hoàn tiền; lần thử thanh toán mới không che mất tiền đã thu.
privateconst PAYMENT_PRIORITY = "CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END";
privatefunctionordersQuery()
    {
$paymentId = DB::table('payment_transactions')->select('id')
```

```
            ->whereColumn('order_id', 'orders.id')->orderByRaw(self::PAYMENT_PRIORITY)->orderByDesc('id')->limit(1);
$orders = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
$join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*', 'payment.id as payment_id', 'payment.paid_at')
```

- <mark>`->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")`</mark> 

- <mark>`->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");`</mark> 

```
returnDB::query()->fromSub($orders, 'finance_orders');
    }
privatefunctionfilteredOrders(Request$request): array
{
```

```
$filters = $request->validate([
'search' => ['nullable', 'string', 'max:100'],
'date_from' => ['nullable', 'date_format:Y-m-d'],
'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] :
[])],
'min_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
'max_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99', ...($request->filled('min_amount') ?
['gte:min_amount'] : [])],
'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
'payment_status' => ['nullable', Rule::in(array_keys(self::STATUSES))],
'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_asc', 'amount_desc'])],
'page' => ['nullable', 'integer', 'min:1'],
        ], [
'date_to.after_or_equal' => 'Ngày kết thúc phải từ ngày bắt đầu trở đi.',
'max_amount.gte' => 'Số tiền tối đa phải lớn hơn hoặc bằng số tiền tối thiểu.',
'*.date_format' => 'Ngày lọc không hợp lệ (định dạng năm-tháng-ngày).',
'*.numeric' => 'Số tiền phải là một giá trị số.',
'*.min' => 'Giá trị bộ lọc nhỏ hơn mức cho phép.',
'*.in' => 'Giá trị bộ lọc không hợp lệ.',
        ]);
$query = $this->ordersQuery()->where('created_at', '<=', now());
if ($request->filled('search')) {
$search = trim($filters['search']);
$query->where(function ($query) use ($search) {
$query->where('name', 'like', '%'.$search.'%')->orWhere('phone', 'like', '%'.$search.'%');
if (ctype_digit(ltrim($search, '#'))) {
$query->orWhere('id', ltrim($search, '#'));
                }
            });
        }
foreach (['gateway', 'payment_status'] as $field) {
if ($request->filled($field)) {
$query->where($field,$filters[$field]);
```

```
            }
        }
if ($request->filled('date_from')) {
$query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
if ($request->filled('date_to')) {
$query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }
foreach (['min_amount' => '>=', 'max_amount' => '<='] as $field => $operator) {
if ($request->filled($field)) {
$query->where('total_price', $operator, $filters[$field]);
            }
        }
return [$query, $filters];
    }
publicfunctionindex(Request$request)
    {
        [$query, $filters] = $this->filteredOrders($request);
```

```
// Thống kê toàn bộ kết quả lọc; mỗi đơn chỉ tính một lần.
```

```
$summary = (clone$query)->selectRaw('COUNT(*) as order_count, COALESCE(SUM(total_price), 0) as total_amount')->first();
$statusTotals = (clone$query)->select('payment_status')
```

- <mark>`->selectRaw('COUNT(*) as order_count, SUM(total_price) as total_amount')`</mark> 

- <mark>`->groupBy('payment_status')->get()->keyBy('payment_status'); $methodTotals = (clone $query)->select('gateway')`</mark> 

- <mark>`->selectRaw('COUNT(*) as order_count, SUM(total_price) as total_amount')`</mark> 

- <mark>`->selectRaw("SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as paid_amount")`</mark> 

```
            ->groupBy('gateway')->get()->keyBy('gateway');
```

```
returnview('admin.finance.index', [
'filters' => $filters, 'summary' => $summary,
'statusTotals' => $statusTotals,'methodTotals' => $methodTotals,
```

```
'statuses' => self::STATUSES,
'methods' => ['cod' => 'COD', 'momo' => 'MoMo', 'unknown' => 'Chưa xác định'],
        ]);
    }
publicfunctiontransactions(Request$request)
    {
        [$query, $filters] = $this->filteredOrders($request);
        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
'oldest'=> ['created_at', 'asc'], 'amount_asc'=> ['total_price', 'asc'],
'amount_desc'=> ['total_price', 'desc'], default=> ['created_at', 'desc'],
        };
$orders = $query->orderBy($column, $direction)->orderBy('id', $direction)->paginate(15)->withQueryString();
returnview('admin.finance.transactions', [
'orders' => $orders, 'filters' => $filters,
'statuses' => self::STATUSES, 'codTransitions' => self::COD_TRANSITIONS,
'methods' => ['cod' => 'COD', 'momo' => 'MoMo', 'unknown' => 'Chưa xác định'],
        ]);
    }
publicfunctionupdateStatus(Request$request, Order$order)
    {
$data = $request->validate([
'payment_status' => ['required', Rule::in(array_keys(self::COD_TRANSITIONS))],
'current_payment_status' => ['required', 'string'],
'current_order_status' => ['required', 'string'],
'current_payment_id' => ['required', 'integer', 'min:0'],
```

```
        ], ['payment_status.in' => 'Trạng thái COD không hợp lệ.', '*.required' => 'Thiếu thông tin trạng thái. Vui lòng tải lại
trang.']);
```

```
DB::transaction(function () use ($order, $data, $request) {
```

```
$order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
```

```
$payment = $order->paymentTransactions()->orderByRaw(self::PAYMENT_PRIORITY)->orderByDesc('id')->lockForUpdate()-
>first();
```

```
$isCod = $payment ? $payment->gateway === 'cod' : in_array($order->status, ['cod_ordered', 'cod_paid'], true);
if (!$isCod) {
```

```
throwValidationException::withMessages(['payment_status' => 'Chỉ được cập nhật thủ công cho đơn COD.']);
            }
```

```
$currentStatus = $payment?->status ?? ($order->status === 'cod_paid' ? 'paid' : 'pending');
if ($currentStatus !== $data['current_payment_status'] || $order->status !== $data['current_order_status'] || (int)
($payment?->id ?? 0) !== (int) $data['current_payment_id']) {
```

```
throwValidationException::withMessages(['payment_status' => 'Đơn hàng vừa thay đổi. Vui lòng tải lại trang trước
khi cập nhật.']);
```

```
            }
$newStatus = $data['payment_status'];
```

```
if (!in_array($newStatus, self::COD_TRANSITIONS[$currentStatus] ?? [], true)) {
```

```
throwValidationException::withMessages(['payment_status' => 'Không thể chuyển sang trạng thái thanh toán
này.']);
            }
if ($newStatus === $currentStatus) {
return;
            }
```

```
if (in_array($newStatus, ['pending', 'paid'], true) && ($order->status === 'cancelled' || in_array($order-
>shipping_status, ['cancelled', 'return', 'returned'], true))) {
```

```
throwValidationException::withMessages(['payment_status' => 'Không thể xác nhận thu tiền cho đơn đã hủy hoặc
hoàn hàng.']);
            }
$attributes = [
'status' => $newStatus,
```

```
'message' => 'Quản trị viên #'.$request->user()->id.' cập nhật: '.self::STATUSES[$newStatus],
```

```
'paid_at' => $newStatus === 'paid' ? ($payment?->paid_at ?? now()) : $payment?->paid_at,
            ];
```

```
if ($payment) {
$payment->update($attributes);
}else{
```

```
$order->paymentTransactions()->create(array_merge($attributes, ['gateway' => 'cod', 'amount' => $order-
>total_price]));
            }
if ($newStatus === 'paid') {
$order->update(['status' => 'cod_paid']);
            } elseif (in_array($newStatus, ['pending', 'failed'], true)) {
$order->update(['status' => 'cod_ordered']);
            }
        });
returnback()->with('success', 'Đã lưu trạng thái thanh toán đơn COD #'.$order->id.'.');
    }
}
```



## 3. Khai báo Route và quyền admin 

Trong routes/web.php, thêm import và các route dưới đây vào nhóm admin hiện có: 

```
use App\Http\Controllers\Admin\FinanceController;
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
Route::get('/finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
Route::patch('/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');
});
```

## 4. Xây dựng hai trang giao diện 

Trang 1: hiển thị chỉ số thống kê, bảng biểu báo cáo, xuất dữ liệu 



<!-- Start of picture text -->
> siorxun gh Shop Adin Osi<br>(6) Thong ké tai chinh<br>‘Shop Admin<br>39 Dashboard<br>© sinphim Wa don, tan node 58 én theal adimmiyyvy 0 aammiyyry o<br>BR Nawsi ding Keng gi han Kéng oii nan Teta v Titel -<br>oor Andinav6 oc REEede<br>|. Théng<br>ké ta chin<br>3 Giao dich tanhton<br>J Bio cio oa oa<br>0 Gon bao gm om hy oan o¢on aon<br>han<br>tdn thta Ba hiy Cha hoa tn Ba hodn én<br>oa od od oa<br>Théng ké theo phurongthre<br>Phurong thie sb aon Téa oii i thank tosn<br>cop ° os 06<br>MoMo ° oa 06<br>Churn xe din 0 oa<br><!-- End of picture text -->

Trang 2: Hiển thị danh sách chi tiết các giá trị thanh toán và cho phép cập nhật các giá trị các đơn hàng COD 



<!-- Start of picture text -->
} sor acum Giao dich tha ton r<br>Giao dich thanh toan<br>‘shop Admin<br>88 Dashboard<br>© Sin phim Ma don, tén node 66 din tool éaimmiyy 0D daimmiyyyy o<br>BR Nowe ding enéng6 nan Kvdng git han rat ca v ttea .<br>—<br>Sonning Mei nnd EEE [ee |<br>IL Thng<br>ké ti chon<br>BREMNER<br>C50<on phi nop 06 oc. 6 tdn bao adm phi van chuyén; nay lc nga tao don<br>Bao cio Danh sch glo dich (0 6am)<br><!-- End of picture text -->

