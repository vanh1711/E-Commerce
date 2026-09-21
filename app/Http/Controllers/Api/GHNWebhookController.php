<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Services\GHNService;

class GHNWebhookController extends Controller
{
    /**
     * Nhận Webhook callback từ GHN Express khi có thay đổi trạng thái vận đơn
     */
    public function handle(Request $request, GHNService $ghn)
    {
        Log::info('GHN Webhook received', $request->all());

        $orderCode = $request->input('OrderCode') ?? $request->input('order_code');

        if (empty($orderCode)) {
            return response()->json(['code' => 400, 'message' => 'Missing OrderCode'], 400);
        }

        $order = Order::where('ghn_order_code', $orderCode)->first();

        if (!$order) {
            return response()->json(['code' => 404, 'message' => 'Order not found for GHN code: ' . $orderCode], 404);
        }

        $result = $ghn->syncOrderStatus($order);

        return response()->json([
            'code'    => 200,
            'message' => 'Webhook processed successfully',
            'data'    => $result,
        ]);
    }
}
