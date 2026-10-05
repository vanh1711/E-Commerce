<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Hiển thị giỏ hàng
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('cart.index', compact('cart', 'total'));
    }

    // Thêm sản phẩm vào giỏ
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        // Xác định biến thể và kiểm tra tồn kho chính xác từ Database
        $variant = null;
        if ($variantId) {
            $variant = \App\Models\ProductVariant::find($variantId);
            if (!$variant || $variant->product_id != $id) {
                return redirect()->back()->with('error', 'Phiên bản sản phẩm không hợp lệ.');
            }
        }

        $availableStock = $variant ? (int) $variant->stock : (int) $product->stock;
        if ($availableStock <= 0) {
            return redirect()->back()->with('error', "Sản phẩm \"{$product->name}\" hiện đã hết hàng trong kho.");
        }

        // Cart key = product_id hoặc product_id-variant_id (để phân biệt cùng SP khác biến thể)
        $cartKey = $variant ? "{$id}-{$variant->id}" : (string) $id;

        $currentInCart = isset($cart[$cartKey]) ? (int) $cart[$cartKey]['quantity'] : 0;
        if (($currentInCart + $quantity) > $availableStock) {
            $allowedAdd = max(0, $availableStock - $currentInCart);
            if ($allowedAdd <= 0) {
                return redirect()->back()->with('error', "Số lượng trong giỏ đã đạt mức tồn kho tối đa ({$availableStock} sản phẩm).");
            }
            $quantity = $allowedAdd;
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Giá TUYỆT ĐỐI lấy từ Database máy chủ, không bao giờ tin giá client gửi lên
            $price = $variant ? (float) $variant->price : (float) $product->price;
            $weight = $variant ? (int) $variant->weight : (int) ($product->weight ?? 200);
            $variantLabel = $variant ? $variant->label : '';

            $product->load('category');

            $cart[$cartKey] = [
                'id'            => $product->id,
                'variant_id'    => $variant ? (int) $variant->id : null,
                'name'          => $product->name,
                'price'         => $price,
                'image'         => $variant && $variant->image ? $variant->image : $product->image,
                'quantity'      => $quantity,
                'weight'        => $weight,
                'variant_label' => $variantLabel,
                'category'      => $product->category->name ?? 'Điện thoại',
            ];
        }

        session()->put('cart', $cart);

        // Nếu bấm "MUA NGAY" -> Chuyển thẳng sang trang thanh toán /checkout
        if ($request->input('action') === 'buy_now' || $request->has('buy_now')) {
            return redirect()->route('checkout.index', ['selected_ids' => $cartKey]);
        }

        return redirect()->back()->with('success', "Đã thêm \"{$product->name}\" vào giỏ hàng!");
    }


    // Cập nhật số lượng
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $qty = (int) $request->input('quantity', 1);
            if ($qty <= 0) {
                unset($cart[$id]);
                session()->put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
            }

            // Kiểm tra tồn kho thực tế từ Database
            $item = $cart[$id];
            $availableStock = 0;
            if (!empty($item['variant_id'])) {
                $variant = \App\Models\ProductVariant::find($item['variant_id']);
                $availableStock = $variant ? (int) $variant->stock : 0;
            } elseif (!empty($item['id'])) {
                $product = Product::find($item['id']);
                $availableStock = $product ? (int) $product->stock : 0;
            }

            if ($qty > $availableStock) {
                $cart[$id]['quantity'] = max(1, $availableStock);
                session()->put('cart', $cart);
                return redirect()->route('cart.index')->with('warning', "Sản phẩm \"{$item['name']}\" chỉ còn {$availableStock} sản phẩm trong kho.");
            }

            $cart[$id]['quantity'] = $qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật số lượng giỏ hàng.');
    }

    // Xóa một sản phẩm
    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    // Xóa toàn bộ giỏ
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }
}
