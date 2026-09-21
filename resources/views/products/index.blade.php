@extends('layouts.admin')

@section('title', 'Quản Lý Sản Phẩm - PhoneStore')
@section('page_title', 'Sản Phẩm')
@section('page_heading', 'Quản Lý Kho Hàng & Sản Phẩm')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-[#0c1322] p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 border border-blue-200 dark:border-blue-900 text-blue-700 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2">
                <span>📱</span> Kho Hàng PhoneStore
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Danh Sách Sản Phẩm</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Giám sát tồn kho, cấu hình biến thể RAM/ROM, giá bán và hình ảnh sản phẩm.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-2xl shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5 flex-shrink-0 cursor-pointer">
                <span>+</span> Thêm Sản Phẩm Mới
            </a>
        </div>
    </div>

    <!-- Thanh Tìm Kiếm & Bộ Lọc Nâng Cao -->
    <div class="bg-white dark:bg-[#0c1322] p-5 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">

            {{-- Ô input tìm kiếm không phân biệt hoa thường --}}
            <div class="sm:col-span-6 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Tìm kiếm theo tên, thương hiệu, model (vd: iPhone 16, Samsung...)" 
                       class="w-full pl-10 pr-10 py-2.5 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 transition outline-none">
                <span class="absolute left-3.5 top-3 text-slate-400 dark:text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                @if(request('search'))
                    <a href="{{ route('products.index', array_merge(request()->except('search'), [])) }}" 
                       class="absolute right-3 top-2.5 w-5 h-5 rounded-full bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 flex items-center justify-center text-xs font-bold" title="Xóa từ khóa">
                        ✕
                    </a>
                @endif
            </div>

            {{-- Lọc Danh mục --}}
            <div class="sm:col-span-3">
                <select name="category" onchange="this.form.submit()" 
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-blue-500 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Sắp xếp --}}
            <div class="sm:col-span-2">
                <select name="sort" onchange="this.form.submit()" 
                        class="w-full py-2.5 px-3 bg-slate-50 dark:bg-slate-800/80 focus:bg-white dark:focus:bg-slate-800 border border-slate-200 dark:border-slate-700 focus:border-blue-500 rounded-2xl text-xs font-bold text-slate-700 dark:text-slate-200 outline-none transition cursor-pointer">
                    <option value="">Mới nhất</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến Cao</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến Thấp</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A - Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên: Z - A</option>
                </select>
            </div>

            {{-- Nút Tìm Kiếm --}}
            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-2xl transition shadow-md shadow-blue-500/20 flex items-center justify-center cursor-pointer">
                    Tìm
                </button>
            </div>
        </form>

        {{-- Thanh thông tin kết quả tìm kiếm --}}
        @if(request('search') || request('category') || request('sort'))
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400">
                    <span>Kết quả lọc:</span>
                    @if(request('search'))
                        <span class="px-2.5 py-0.5 bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 font-bold rounded-full border border-blue-200 dark:border-blue-800">
                            "{{ request('search') }}"
                        </span>
                    @endif
                    <span class="font-bold text-slate-900 dark:text-white">({{ $products->total() }} sản phẩm)</span>
                </div>
                <a href="{{ route('products.index') }}" class="text-rose-500 dark:text-rose-400 hover:text-rose-700 font-bold flex items-center gap-1 hover:underline">
                    <span>✕</span> Đặt lại bộ lọc
                </a>
            </div>
        @endif
    </div>

    <!-- Table Sản Phẩm Chuẩn Admin Flagship -->
    <div class="bg-white dark:bg-[#0c1322] rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8 transition-colors duration-300">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                <thead>
                    <tr class="text-left text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="pb-4">#ID</th>
                        <th class="pb-4">Hình ảnh</th>
                        <th class="pb-4">Tên sản phẩm</th>
                        <th class="pb-4">Hãng</th>
                        <th class="pb-4">Danh mục</th>
                        <th class="pb-4">Giá bán</th>
                        <th class="pb-4 text-center">Tồn kho</th>
                        <th class="pb-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition duration-150">
                            <td class="py-4 font-bold text-slate-400">#{{ $product->id }}</td>
                            
                            <!-- Ảnh sản phẩm -->
                            <td class="py-4">
                                <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-2xl p-1.5 border border-slate-100 dark:border-slate-700 flex items-center justify-center overflow-hidden flex-shrink-0">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=150&q=80' }}" 
                                         class="max-h-full object-contain hover:scale-110 transition duration-300" alt="">
                                </div>
                            </td>

                            <!-- Tên SP -->
                            <td class="py-4 max-w-xs">
                                <a href="{{ route('products.edit', $product->id) }}" class="font-extrabold text-slate-900 dark:text-white text-xs block hover:text-blue-600 dark:hover:text-blue-400 transition">
                                    {{ $product->name }}
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    @if($product->model)
                                        <span class="text-[10px] text-slate-400 font-mono">{{ $product->model }}</span>
                                    @endif
                                    @if($product->variants && $product->variants->count() > 0)
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400">
                                            {{ $product->variants->count() }} biến thể
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Hãng -->
                            <td class="py-4 font-semibold text-slate-700 dark:text-slate-300">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-bold border border-slate-200 dark:border-slate-700">
                                    {{ $product->brand ?? 'Chính Hãng' }}
                                </span>
                            </td>

                            <!-- Danh mục -->
                            <td class="py-4 font-bold text-blue-600 dark:text-blue-400">
                                {{ $product->category->name ?? '---' }}
                            </td>

                            <!-- Giá bán -->
                            <td class="py-4 text-xs font-black text-rose-600 dark:text-rose-400">
                                {{ number_format($product->price) }} đ
                            </td>

                            <!-- Tồn kho -->
                            <td class="py-4 text-center">
                                @if(($product->stock ?? 0) <= 0)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                        Hết hàng
                                    </span>
                                @elseif(($product->stock ?? 0) <= 5)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                        Còn {{ $product->stock }} máy ⚠️
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                        Còn {{ $product->stock }} máy
                                    </span>
                                @endif
                            </td>

                            <!-- Thao tác Admin -->
                            <td class="py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('products.show', $product->id) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold rounded-xl transition inline-flex items-center gap-1">
                                    <span>👁️</span> Xem
                                </a>

                                <a href="{{ route('products.edit', $product->id) }}" class="px-3 py-1.5 bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-xs font-bold rounded-xl transition inline-flex items-center gap-1">
                                    <span>✏️</span> Sửa
                                </a>

                                <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm [{{ $product->name }}]?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-xs font-bold rounded-xl transition cursor-pointer inline-flex items-center gap-1">
                                        <span>🗑️</span> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Không tìm thấy sản phẩm nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Phân trang -->
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection