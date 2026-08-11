<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggest(Request $request)
    {
        $query = trim($request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::with('brand')
            ->where('name', 'like', "%{$query}%")
            ->orWhere('model', 'like', "%{$query}%")
            ->take(6)
            ->get(['id', 'name', 'slug', 'price', 'image', 'brand_id']);

        return response()->json($products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->sale_price ?: $product->price,
                'image' => $product->image,
                'brand' => $product->brand->name ?? null,
                'brandSlug' => $product->brand->slug ?? null,
            ];
        }));
    }
}
