<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::where('status', 'active')->orderBy('name')->get();
        $featured = Product::with('brand')->where('featured', true)->orderByDesc('updated_at')->take(12)->get();
        $top = Product::with('brand')->orderByDesc('rating')->take(8)->get();

        return view('home', compact('brands', 'featured', 'top'));
    }
}
