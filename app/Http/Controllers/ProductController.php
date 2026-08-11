<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();

        $query = Product::with('category')->orderBy('created_at', 'desc');

        $selected = null;
        if (request()->has('category') && $cid = request()->get('category')) {
            $query->where('category_id', $cid);
            $selected = $cid;
        }

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'categories', 'selected'));
    }

    public function create()
    {
        $categories = Category::all();
        $selectedCategory = request()->get('category_id');
        return view('products.create', compact('categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'specs' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);

        // Accept specs as JSON string or array inputs
        $specs = $request->input('specs');
        if (is_string($specs)) {
            $decoded = json_decode($specs, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['specs'] = $decoded;
            }
        } elseif (is_array($specs)) {
            $data['specs'] = $specs;
        }

        // handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created.');
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'specs' => 'nullable',
            'image' => 'nullable|image|max:2048',
        ]);
        $specs = $request->input('specs');
        if (is_string($specs)) {
            $decoded = json_decode($specs, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $data['specs'] = $decoded;
            }
        } elseif (is_array($specs)) {
            $data['specs'] = $specs;
        }

        // handle image upload (replace old)
        if ($request->hasFile('image')) {
            // delete old if exists
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        // delete image if exists
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
