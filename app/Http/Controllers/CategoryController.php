<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Tạo danh mục mới thành công.');
    }

    public function show(Category $category)
    {
        $products = $category->products()->orderBy('created_at', 'desc')->paginate(12);
        return view('categories.show', compact('category', 'products'));
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                         ->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy(Category $category)
    {
        // Chống xóa dây chuyền toàn bộ sản phẩm trong danh mục (Fix lỗi #13)
        $productCount = $category->products()->count();
        if ($productCount > 0) {
            return redirect()->route('categories.index')
                ->with('error', "Không thể xóa danh mục [{$category->name}] vì đang có {$productCount} sản phẩm liên kết. Vui lòng chuyển hoặc xóa các sản phẩm đó trước.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', "Đã xóa danh mục [{$categoryName}] thành công.");
    }
}
