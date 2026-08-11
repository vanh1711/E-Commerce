<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = Product::whereIn('id', array_keys($cart))->get()->map(function ($product) use ($cart) {
            $product->quantity = $cart[$product->id] ?? 1;
            return $product;
        });

        $subtotal = $items->sum(fn ($item) => ($item->sale_price ?: $item->price) * $item->quantity);
        $shipping = $subtotal > 0 ? 0 : 0;
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('items', 'subtotal', 'shipping', 'total'));
    }
}
