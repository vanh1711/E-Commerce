<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = Product::whereIn('id', array_keys($cart))->get()->map(function ($product) use ($cart) {
            $product->quantity = $cart[$product->id] ?? 1;
            return $product;
        });

        return view('cart.index', compact('items'));
    }

    public function add(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'nullable|integer|min:1']);

        $cart = $request->session()->get('cart', []);
        $productId = $request->post('product_id');
        $quantity = max(1, (int) $request->post('quantity', 1));
        $cart[$productId] = ($cart[$productId] ?? 0) + $quantity;
        $request->session()->put('cart', $cart);

        return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng', 'count' => array_sum($cart)]);
    }

    public function update(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id', 'quantity' => 'required|integer|min:0']);

        $cart = $request->session()->get('cart', []);
        $productId = $request->post('product_id');
        $quantity = (int) $request->post('quantity');

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        $request->session()->put('cart', $cart);

        return response()->json(['success' => true, 'count' => array_sum($cart), 'quantity' => $quantity]);
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);

        $cart = $request->session()->get('cart', []);
        $productId = $request->post('product_id');
        unset($cart[$productId]);
        $request->session()->put('cart', $cart);

        return response()->json(['success' => true, 'count' => array_sum($cart), 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng']);
    }
}
