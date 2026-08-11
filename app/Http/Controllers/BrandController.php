<?php

namespace App\Http\Controllers;

use App\Models\Brand;

class BrandController extends Controller
{
    public function show(Brand $brand)
    {
        $products = $brand->products()->with('brand')->orderByDesc('featured')->paginate(12);
        return view('brands.show', compact('brand', 'products'));
    }
}
