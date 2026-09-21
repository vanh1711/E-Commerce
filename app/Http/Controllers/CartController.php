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

        $quantity = max(1, (int) $request->input('quantity', 1));
        $variantId = $request->input('variant_id');

        // Cart key = product_id hoặc product_id-variant_id (để phân biệt cùng SP khác biến thể)
        $cartKey = $variantId ? "{$id}-{$variantId}" : (string) $id;

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Lấy giá, tồn kho, trọng lượng từ biến thể cụ thể nếu có
            $variant = null;
            $price = $product->price;
            $variantLabel = $request->input('variant_label', '');
            $weight = (int) ($product->weight ?? 200);

            if ($variantId) {
                $variant = \App\Models\ProductVariant::find($variantId);
                if ($variant && $variant->product_id == $id) {
                    $price = (float) $variant->price;
                    $weight = (int) $variant->weight;
                    $variantLabel = $variant->label; // accessor: "Pro Max / 512GB / Titan"
                }
            }

            // Cho phép override giá từ client (fallback)
            $selectedPrice = $request->input('selected_price');
            if (!$variantId && $selectedPrice && is_numeric($selectedPrice) && $selectedPrice > 0) {
                $price = (int) $selectedPrice;
            }

            $product->load('category');

            $cart[$cartKey] = [
                'id'            => $product->id,
                'variant_id'    => $variantId ? (int) $variantId : null,
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
            $cart[$id]['quantity'] = $qty;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Đã cập nhật giỏ hàng!');
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
