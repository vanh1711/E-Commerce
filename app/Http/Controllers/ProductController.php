<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->get();

        $query = Product::with('category', 'variants');

        // Tìm kiếm từ khóa: Không phân biệt hoa thường (tên, hãng, model, mô tả, danh mục)
        if ($request->filled('search')) {
            $searchTerm = trim($request->get('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('brand', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('model', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function ($catQuery) use ($searchTerm) {
                      $catQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        // Lọc theo danh mục
        $selected = null;
        if ($request->filled('category') && $cid = $request->get('category')) {
            $query->where('category_id', $cid);
            $selected = $cid;
        }

        // Sắp xếp
        if ($request->filled('sort')) {
            switch ($request->get('sort')) {
                case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();

        return view('products.index', compact('products', 'categories', 'selected'));
    }

    /**
     * API Tìm kiếm nhanh thời gian thực (Live Preview / Autocomplete như Samsung Store)
     */
    public function searchApi(Request $request)
    {
        $keyword = trim($request->get('q', ''));
        if (empty($keyword)) {
            return response()->json([
                'success' => true,
                'products' => [],
                'suggestions' => []
            ]);
        }

        // Tìm 6 sản phẩm khớp nhất
        $products = Product::with('category')
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('brand', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('model', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('description', 'LIKE', '%' . $keyword . '%')
                  ->orWhereHas('category', function ($catQuery) use ($keyword) {
                      $catQuery->where('name', 'LIKE', '%' . $keyword . '%');
                  });
            })
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand ?? 'Chính Hãng',
                    'price' => number_format($product->price) . ' VND',
                    'raw_price' => $product->price,
                    'image' => $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=300&q=80',
                    'url' => route('products.show', $product->id),
                    'category' => $product->category->name ?? 'Điện thoại'
                ];
            });


        // Gợi ý từ khóa liên quan
        $suggestions = Product::where('name', 'LIKE', '%' . $keyword . '%')
            ->distinct()
            ->take(4)
            ->pluck('name');

        return response()->json([
            'success' => true,
            'keyword' => $keyword,
            'products' => $products,
            'suggestions' => $suggestions,
            'total' => count($products),
            'view_all_url' => route('products.index', ['search' => $keyword])
        ]);
    }

    /**
     * Trang So Sánh Thông Số Kỹ Thuật Trực Quan (Interactive Specs Comparison)
     */
    public function compare(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        // Mặc định nếu không có IDs thì lấy 2-3 sản phẩm đầu tiên làm mẫu so sánh
        if (empty($ids)) {
            $comparedProducts = Product::with('category')->latest()->take(2)->get();
        } else {
            $comparedProducts = Product::with('category')->whereIn('id', array_slice($ids, 0, 3))->get();
        }

        $allProducts = Product::select('id', 'name', 'brand', 'price', 'image')->get();

        return view('products.compare', compact('comparedProducts', 'allProducts'));
    }

    /**
     * API lấy thông số so sánh dạng JSON
     */
    public function compareApi(Request $request)
    {
        $ids = $request->input('ids', []);
        if (is_string($ids)) {
            $ids = array_filter(explode(',', $ids));
        }

        $products = Product::whereIn('id', array_slice($ids, 0, 3))->get()->map(function($p) {
            $specs = is_array($p->specs) ? $p->specs : [];
            return [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand ?? 'PhoneStore',
                'price' => number_format($p->price) . ' đ',
                'raw_price' => (float)$p->price,
                'image' => $p->image ? asset('storage/' . $p->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=400&q=80',
                'url' => route('products.show', $p->id),
                'specs' => [
                    'Màn hình' => $specs['screen'] ?? ($p->brand === 'Apple' ? '6.7" Super Retina XDR OLED 120Hz' : '6.8" Dynamic AMOLED 2X 120Hz'),
                    'Chip xử lý (CPU)' => $specs['cpu'] ?? ($p->brand === 'Apple' ? 'Apple A18 Pro / A17 Pro (3nm)' : 'Snapdragon 8 Gen 3 for Galaxy'),
                    'Bộ nhớ RAM' => $specs['ram'] ?? '8GB / 12GB LPDDR5X',
                    'Dung lượng ROM' => $specs['storage'] ?? '256GB / 512GB / 1TB',
                    'Cụm Camera sau' => $specs['camera'] ?? ($p->brand === 'Apple' ? '48MP Chính + 12MP Tele 5x + 48MP Siêu rộng' : '200MP Chính + 50MP Tele 5x + 12MP Siêu rộng'),
                    'Camera trước' => $specs['front_camera'] ?? '12MP TrueDepth / AF',
                    'Dung lượng Pin' => $specs['battery'] ?? ($p->brand === 'Apple' ? '4,441 mAh (Sạc nhanh 30W)' : '5,000 mAh (Sạc siêu tốc 45W)'),
                    'Chất liệu viền máy' => $specs['material'] ?? 'Titanium Hàng Không Vũ Trụ',
                    'Hệ điều hành' => $specs['os'] ?? ($p->brand === 'Apple' ? 'iOS 18 (Tích hợp Apple Intelligence)' : 'Android 14 (One UI 6.1 / Galaxy AI)'),
                    'Kháng nước & Bụi' => $specs['waterproof'] ?? 'IP68 (Độ sâu 6 mét trong 30 phút)'
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'products' => $products
        ]);
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
            'specs' => 'nullable|array',
            'tags' => 'nullable|array',
            'image' => 'nullable|image|max:3072',
            'gallery_camera' => 'nullable|image|max:3072',
            'gallery_side' => 'nullable|image|max:3072',
            'gallery_back' => 'nullable|image|max:3072',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data['stock'] = $data['stock'] ?? 10;
        $data['tags'] = $request->input('tags', []);

        // Xử lý specs chi tiết
        $specsInput = $request->input('specs', []);
        if (is_array($specsInput)) {
            $data['specs'] = array_filter($specsInput, fn($v) => !is_null($v) && trim($v) !== '');
        }

        // Xử lý upload ảnh chính (Chính diện)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        // Xử lý upload bộ sưu tập ảnh phụ đa góc nhìn (Gallery)
        $gallery = [];
        if ($request->hasFile('gallery_camera')) {
            $gallery['camera'] = $request->file('gallery_camera')->store('products/gallery', 'public');
        }
        if ($request->hasFile('gallery_side')) {
            $gallery['side'] = $request->file('gallery_side')->store('products/gallery', 'public');
        }
        if ($request->hasFile('gallery_back')) {
            $gallery['back'] = $request->file('gallery_back')->store('products/gallery', 'public');
        }
        $data['gallery'] = $gallery;

        $product = Product::create($data);

        // Lưu danh sách biến thể sản phẩm (nếu có)
        $this->syncVariants($product, $request);

        return redirect()->route('products.index')->with('success', 'Đã thêm sản phẩm mới thành công kèm đầy đủ thông số, bộ ảnh và biến thể tồn kho.');
    }

    public function show($id)
    {
        $product = Product::with('category', 'variants')->findOrFail($id);
        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->take(4)
                                  ->get();
        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function edit(Product $product)
    {
        $product->load('variants');
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
            'specs' => 'nullable|array',
            'tags' => 'nullable|array',
            'image' => 'nullable|image|max:3072',
            'gallery_camera' => 'nullable|image|max:3072',
            'gallery_side' => 'nullable|image|max:3072',
            'gallery_back' => 'nullable|image|max:3072',
            'stock' => 'nullable|integer|min:0',
        ]);

        $data['stock'] = $data['stock'] ?? 10;
        $data['tags'] = $request->input('tags', []);

        // Xử lý specs chi tiết
        $specsInput = $request->input('specs', []);
        if (is_array($specsInput)) {
            $data['specs'] = array_filter($specsInput, fn($v) => !is_null($v) && trim($v) !== '');
        }

        // Xử lý upload thay thế ảnh chính
        if ($request->hasFile('image')) {
            if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Xử lý upload thay thế từng ảnh phụ trong Gallery
        $gallery = is_array($product->gallery) ? $product->gallery : [];

        if ($request->hasFile('gallery_camera')) {
            if (!empty($gallery['camera']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gallery['camera'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery['camera']);
            }
            $gallery['camera'] = $request->file('gallery_camera')->store('products/gallery', 'public');
        }

        if ($request->hasFile('gallery_side')) {
            if (!empty($gallery['side']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gallery['side'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery['side']);
            }
            $gallery['side'] = $request->file('gallery_side')->store('products/gallery', 'public');
        }

        if ($request->hasFile('gallery_back')) {
            if (!empty($gallery['back']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($gallery['back'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($gallery['back']);
            }
            $gallery['back'] = $request->file('gallery_back')->store('products/gallery', 'public');
        }

        $data['gallery'] = $gallery;

        $product->update($data);

        // Đồng bộ danh sách biến thể sản phẩm
        $this->syncVariants($product, $request);

        return redirect()->route('products.index')->with('success', 'Đã cập nhật thông tin sản phẩm, bộ sưu tập ảnh và biến thể tồn kho thành công.');
    }

    public function destroy(Product $product)
    {
        // Xóa ảnh chính
        if ($product->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($product->image)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
        }

        // Xóa các ảnh trong gallery
        if (is_array($product->gallery)) {
            foreach ($product->gallery as $imgPath) {
                if ($imgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imgPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($imgPath);
                }
            }
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Đã xóa sản phẩm thành công.');
    }

    /**
     * Đồng bộ danh sách biến thể sản phẩm từ request
     * Xử lý: Thêm mới, cập nhật existing, xóa những variant không còn trong request
     */
    private function syncVariants(Product $product, Request $request): void
    {
        $variantsData = $request->input('variants', []);

        if (empty($variantsData)) {
            return;
        }

        $existingIds = $product->variants()->pluck('id')->toArray();
        $submittedIds = [];

        foreach ($variantsData as $vData) {
            // Bỏ qua dòng trống (không có tên bản và không có giá)
            if (empty($vData['version_name']) && empty($vData['price'])) {
                continue;
            }

            $variantFields = [
                'sku'            => $vData['sku'] ?? null,
                'version_name'   => $vData['version_name'] ?? null,
                'color'          => $vData['color'] ?? null,
                'color_code'     => $vData['color_code'] ?? null,
                'storage'        => $vData['storage'] ?? null,
                'ram'            => $vData['ram'] ?? null,
                'price'          => (float) ($vData['price'] ?? $product->price ?? 0),
                'original_price' => !empty($vData['original_price']) ? (float) $vData['original_price'] : null,
                'stock'          => (int) ($vData['stock'] ?? 0),
                'weight'         => (int) ($vData['weight'] ?? 200),
            ];

            if (!empty($vData['id'])) {
                // Cập nhật variant đã tồn tại
                $variant = ProductVariant::find($vData['id']);
                if ($variant && $variant->product_id === $product->id) {
                    $variant->update($variantFields);
                    $submittedIds[] = $variant->id;
                }
            } else {
                // Tạo variant mới
                $variant = $product->variants()->create($variantFields);
                $submittedIds[] = $variant->id;
            }
        }

        // Xóa các variant không còn trong danh sách submitted
        $toDelete = array_diff($existingIds, $submittedIds);
        if (!empty($toDelete)) {
            ProductVariant::whereIn('id', $toDelete)->delete();
        }
    }
}
