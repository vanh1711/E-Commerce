<?php

namespace App\Services;

use App\Models\Order;

class GHNOrderService
{
    public function __construct(private GHNService $ghn)
    {
    }

    /**
     * Tạo vận đơn GHN từ Model Order
     */
    public function create(Order $order, bool $isPaid = false): array
    {
        $items = [];
        $weight = 0;

        foreach ($order->items as $item) {
            $itemWeight = (int) ($item->product->weight ?? 200);
            $weight += $itemWeight * (int) $item->quantity;

            $items[] = [
                'name'     => $item->product_name ?? ($item->product->name ?? 'Điện thoại Smartphone'),
                'quantity' => (int) $item->quantity,
                'price'    => (int) $item->price,
                'weight'   => $itemWeight,
            ];
        }

        $customerName  = $order->customer_name ?? $order->name ?? 'Khách Hàng';
        $customerPhone = $order->customer_phone ?? $order->phone ?? '0987654321';
        $totalPrice    = (int) ($order->total_amount ?? $order->total_price ?? 0);

        // Xác định đơn hàng đã được thanh toán online (MoMo, Thẻ, chuyển khoản, v.v.) hay chưa
        $isOrderPaid = $isPaid 
            || ($order->payment_status === 'paid') 
            || (in_array(strtolower($order->payment_method ?? ''), ['momo', 'card', 'bank_transfer']) && $order->payment_status === 'paid');

        // GHN payment_type_id:
        // 1: Người gửi / Shop trả cước vận chuyển (Bên gửi trả phí).
        // 2: Người nhận / Khách hàng trả cước vận chuyển khi nhận hàng (Bên nhận trả phí).
        // Khi khách đã thanh toán online (MoMo) -> Shop đã thu tiền hàng + tiền ship -> payment_type_id = 1 (Bên gửi trả phí) và cod_amount = 0 (Thu hộ 0đ, Tổng thu 0đ).
        // Khi khách thanh toán COD -> Shop quản lý tổng hóa đơn -> payment_type_id = 1 (Bên gửi trả phí) và cod_amount = $totalPrice (Thu hộ đúng tổng tiền đơn hàng).
        $paymentTypeId = 1;
        $codAmount = $isOrderPaid ? 0 : $totalPrice;

        return $this->ghn->createOrder([
            'payment_type_id' => $paymentTypeId,
            'note'            => 'Đơn hàng #' . ($order->order_code ?? $order->id),
            'required_note'   => 'KHONGCHOXEMHANG', // Hoặc 'CHOXEMHANGKHONGTHU'
            'to_name'         => $customerName,
            'to_phone'        => $customerPhone,
            'to_address'      => $order->shipping_address,
            'to_ward_code'    => (string) $order->to_ward_code,
            'to_district_id'  => (int) $order->to_district_id,
            'cod_amount'      => $codAmount,
            'weight'          => $weight > 0 ? $weight : 300,
            'length'          => 15,
            'width'           => 15,
            'height'          => 10,
            'service_type_id' => 2, // Chuẩn E-Commerce
            'items'           => $items,
        ]);
    }
}
