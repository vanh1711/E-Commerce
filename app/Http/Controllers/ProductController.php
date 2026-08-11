<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();
        $brands = Brand::where('status', 'active')->orderBy('name')->get();

        $query = Product::with(['category', 'brand'])->where('status', true);

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        if ($request->filled('category')) {
            $categoryValue = $request->category;
            $query->whereHas('category', function ($q) use ($categoryValue) {
                if (is_numeric($categoryValue)) {
                    $q->where('id', $categoryValue);
                } else {
                    $q->where('slug', $categoryValue);
                }
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('model', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->latest()->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brand::all();
        $selectedCategory = request()->get('category_id');
        return view('products.create', compact('categories', 'brands', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'nullable|integer|min:0',
            'badge' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'specs' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if (is_string($request->input('specs'))) {
            $decoded = json_decode($request->input('specs'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['specs'] = $decoded;
            }
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? true;

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(Brand $brand, Product $product)
    {
        if ($product->brand_id !== $brand->id) {
            abort(404);
        }

        $product->load(['category', 'brand']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brand::all();
        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'model' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'stock' => 'nullable|integer|min:0',
            'badge' => 'nullable|string|max:50',
            'ram' => 'nullable|string|max:50',
            'storage' => 'nullable|string|max:50',
            'specs' => 'nullable',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if (is_string($request->input('specs'))) {
            $decoded = json_decode($request->input('specs'), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['specs'] = $decoded;
            }
        }

        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['status'] = $validated['status'] ?? true;

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
