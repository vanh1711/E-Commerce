<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Services\GHNOrderService;
use App\Services\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MomoController extends Controller
{
    /**
     * Bắt đầu phiên thanh toán MoMo cho đơn hàng mới tạo
     */
    public function start(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()?->is_admin) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Thanh toán lại cho đơn hàng chưa thanh toán hoặc thanh toán thất bại
     */
    public function payAgain(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()?->is_admin) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        if ($order->payment_status === 'paid') {
            return redirect()->route('orders.show', $order->id)->with('info', 'Đơn hàng này đã được thanh toán thành công trước đó.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Xử lý khi khách hàng hoàn tất hoặc hủy thanh toán và quay về website
     */
    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo Callback Received:', [
            'payload'       => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if (!$momo->isValidSuccessfulResponse($request->all())) {
            Log::warning('MoMo Callback Rejected / Failed:', [
                'result_code'     => $request->input('resultCode'),
                'order_id'        => $request->input('orderId'),
                'signature_valid' => $momo->isValidResponse($request->all()),
            ]);

            if ($momo->isValidResponse($request->all())) {
                $this->markFailed($request->all(), $momo);
            }

            return redirect()->route('orders.index')->with('error', 'Giao dịch qua ví MoMo không thành công hoặc đã bị hủy.');
        }

        $result = $this->completePayment($request->all(), $ghnOrders, $momo);

        $message = in_array($result, ['created', 'already_created'], true)
            ? 'Thanh toán MoMo thành công! Vận đơn GHN Express đã được tự động tạo.'
            : 'Thanh toán MoMo thành công! Đơn hàng đang được chuẩn bị đóng gói.';

        return redirect()->route('orders.index')->with('success', $message);
    }

    /**
     * Nhận IPN Webhook trực tiếp từ máy chủ MoMo chạy ngầm
     */
    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        Log::info('MoMo IPN Received:', [
            'payload'       => $request->except('signature'),
            'has_signature' => $request->has('signature'),
        ]);

        if ($momo->isValidSuccessfulResponse($request->all())) {
            $this->completePayment($request->all(), $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($request->all())) {
            $this->markFailed($request->all(), $momo);
        }

        return response()->json(['message' => 'Received']);
    }

    /**
     * Tạo bản ghi giao dịch mới (PaymentTransaction) cho mỗi lần thử thanh toán
     */
    private function newTransaction(Order $order): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'momo',
            'amount'   => $order->total_amount ?? $order->total_price,
            'status'   => 'pending',
        ]);
    }

    /**
     * Gọi MomoService để lấy payUrl và chuyển hướng người dùng
     */
    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        $result = $momo->createPayment($order, $transaction);

        if (isset($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        $errorMsg = $result['message'] ?? 'Không thể kết nối tới cổng thanh toán MoMo.';
        return redirect()->route('orders.index')->with('error', $errorMsg);
    }

    /**
     * Xác nhận thanh toán thành công và tự động tạo vận đơn GHN Express
     */
    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                return 'invalid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (!$order) {
                return 'invalid';
            }

            if ($order->payment_status === 'paid' && $order->ghn_order_code) {
                return 'already_created';
            }

            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                $momo->markFailed($transaction, $payload);
                return 'invalid';
            }

            $order->update([
                'payment_status'  => 'paid',
                'payment_method'  => 'momo',
                'shipping_status' => 'preparing',
            ]);

            $momo->markPaid($transaction, $payload);

            return ['create', $order->id];
        });

        if (!is_array($result)) {
            return (string) $result;
        }

        $order = Order::with('items.product')->find($result[1]);
        if (!$order) {
            return 'failed';
        }

        // Tự động đẩy vận đơn sang GHN Express với trạng thái đã thanh toán (isPaid = true => COD = 0)
        if (!empty($order->to_district_id) && !empty($order->to_ward_code)) {
            try {
                $response = $ghnOrders->create($order, true);
                if (isset($response['code']) && $response['code'] === 200 && !empty($response['data']['order_code'])) {
                    $order->update([
                        'ghn_order_code'  => $response['data']['order_code'],
                        'shipping_status' => 'ready_to_pick',
                    ]);
                    return 'created';
                }

                Log::error('GHN order creation failed after MoMo payment:', [
                    'order_id' => $order->id,
                    'response' => $response,
                ]);
            } catch (\Exception $e) {
                Log::error('GHN Exception after MoMo payment: ' . $e->getMessage());
            }
        }

        return 'paid_without_ghn';
    }

    /**
     * Ghi nhận giao dịch thanh toán thất bại
     */
    private function markFailed(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->first();

        if ($transaction && $transaction->status !== 'paid') {
            $momo->markFailed($transaction, $payload);
        }
    }
}
