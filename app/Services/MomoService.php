<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MomoService
{
    /**
     * Tạo yêu cầu thanh toán MoMo Sandbox (API v2 / Gateway)
     */
    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $endpoint    = config('services.momo.endpoint', 'https://test-payment.momo.vn/v2/gateway/api/create');
        $partnerCode = config('services.momo.partner_code', env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'));
        $accessKey   = config('services.momo.access_key', env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'));
        $secretKey   = config('services.momo.secret_key', env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'));

        $orderInfo   = 'Thanh toan don hang #' . ($order->order_code ?? $order->id);
        $amount      = (string) ((int) ($order->total_amount ?? $order->total_price));
        $orderId     = $order->id . '_' . $transaction->id . '_' . time();
        $redirectUrl = config('services.momo.redirect_url') ?: route('user.payment.momo.callback');
        $ipnUrl      = config('services.momo.ipn_url') ?: route('payment.momo.ipn');
        $extraData   = (string) $order->id;
        $requestId   = (string) time();
        $requestType = config('services.momo.request_type', 'payWithATM');

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

        $signature = hash_hmac('sha256', $rawHash, $secretKey);

        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => config('app.name', 'Fruit Shop'),
            'storeId'     => 'MomoStore',
            'requestId'   => $requestId,
            'amount'      => $amount,
            'orderId'     => $orderId,
            'orderInfo'   => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl'      => $ipnUrl,
            'lang'        => 'vi',
            'extraData'   => $extraData,
            'requestType' => $requestType,
            'signature'   => $signature,
        ];

        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload'  => $data,
            'status'           => 'initiated',
        ]);

        try {
            $response = Http::withOptions([
                'verify'  => filter_var(config('services.momo.verify_ssl', false), FILTER_VALIDATE_BOOLEAN),
                'timeout' => 15,
            ])->post($endpoint, $data);

            $result = $response->json() ?? [];
        } catch (\Exception $e) {
            Log::error('MoMo API Connection Error: ' . $e->getMessage());
            $result = [
                'resultCode' => 99,
                'message'    => 'Lỗi kết nối tới MoMo: ' . $e->getMessage(),
            ];
        }

        $transaction->update([
            'response_payload' => $result,
            'result_code'      => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
            'message'          => $result['message'] ?? null,
            'status'           => isset($result['payUrl']) ? 'initiated' : 'failed',
        ]);

        return $result;
    }

    /**
     * Kiểm tra mã kết quả thanh toán thành công
     */
    public function isSuccessful(array $payload): bool
    {
        return (string) ($payload['resultCode'] ?? '') === '0';
    }

    /**
     * Cập nhật trạng thái giao dịch thanh toán thành công
     */
    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId'] ?? null,
            'result_code'      => (int) ($payload['resultCode'] ?? 0),
            'message'          => $payload['message'] ?? 'Thanh toán thành công',
            'response_payload' => $payload,
            'status'           => 'paid',
            'paid_at'          => Carbon::now(),
        ]);
    }

    /**
     * Cập nhật trạng thái giao dịch thanh toán thất bại
     */
    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId'] ?? null,
            'result_code'      => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message'          => $payload['message'] ?? 'Thanh toán thất bại',
            'response_payload' => $payload,
            'status'           => 'failed',
        ]);
    }

    /**
     * Kiểm tra chữ ký và trạng thái thành công
     */
    public function isValidSuccessfulResponse(array $payload): bool
    {
        return $this->isValidResponse($payload) && $this->isSuccessful($payload);
    }

    /**
     * Kiểm tra tính hợp lệ chữ ký số (HMAC-SHA256) từ MoMo
     */
    public function isValidResponse(array $payload): bool
    {
        if (!isset($payload['signature'])) {
            return false;
        }

        $accessKey = config('services.momo.access_key', env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'));
        $secretKey = config('services.momo.secret_key', env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa'));

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

        return hash_equals(
            hash_hmac('sha256', $rawHash, $secretKey),
            (string) $payload['signature']
        );
    }

    /**
     * Lấy ID đơn hàng từ extraData
     */
    public function orderId(array $payload): ?int
    {
        $orderId = $payload['extraData'] ?? null;
        return is_numeric($orderId) ? (int) $orderId : null;
    }
}
