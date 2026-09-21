@extends('layouts.admin')

@section('title', 'Chỉnh Sửa Sản Phẩm: ' . $product->name . ' - PhoneStore Admin')
@section('page_title', 'Sản Phẩm')
@section('page_heading', 'Chỉnh Sửa Sản Phẩm')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 pb-12">

    <!-- Header & Nút Quay Lại -->
    <div class="flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2 border border-blue-100 dark:border-blue-900">
                <span>⚙️</span> Chỉnh Sửa Sản Phẩm
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Chỉnh Sửa Sản Phẩm</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Cập nhật thông tin chi tiết, nhu cầu gợi ý thông minh và bộ ảnh đa góc nhìn của <strong class="text-slate-800 dark:text-slate-200">{{ $product->name }}</strong>.</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-xl transition flex items-center gap-1.5">
            Quay Lại
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-700 dark:text-rose-300 rounded-2xl text-xs space-y-1">
            <p class="font-bold">⚠️ Vui lòng kiểm tra lại các lỗi sau:</p>
            <ul class="list-disc list-inside space-y-0.5 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        @php
            $currentTags = old('tags', $product->tags ?? $product->needs_tags ?? []);
            $specs = is_array($product->specs) ? $product->specs : [];
            $gallery = is_array($product->gallery) ? $product->gallery : [];
        @endphp

        <!-- 1. THÔNG TIN CƠ BẢN SẢN PHẨM -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="w-7 h-7 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xs font-black">1</span>
                <h2 class="text-base font-black text-slate-900 dark:text-white">Thông Tin Cơ Bản</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tên sản phẩm <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}" placeholder="Ví dụ: Galaxy S24 Ultra Titanium Gray"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Hãng / Thương hiệu <span class="text-rose-500">*</span></label>
                    <input type="text" name="brand" required value="{{ old('brand', $product->brand) }}" placeholder="Apple, Samsung, Xiaomi..."
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Danh mục hệ thống <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Giá niêm yết (VNĐ) <span class="text-rose-500">*</span></label>
                    <input type="number" name="price" required value="{{ old('price', (int)$product->price) }}" placeholder="Ví dụ: 29990000"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Số lượng trong kho</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 10) }}"
                           class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mô tả đặc điểm nổi bật</label>
                <textarea name="description" rows="3" placeholder="Mô tả các tính năng vượt trội của sản phẩm..."
                          class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl text-xs font-medium text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition">{{ old('description', $product->description) }}</textarea>
            </div>
        </div>


        <!-- 2. BỘ LỌC THÔNG MINH THEO NHU CẦU SỬ DỤNG (SMART NEEDS TAGS) -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xs font-black">2</span>
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white">Gợi Ý Nhu Cầu Sử Dụng (Smart Filter Tags)</h2>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Chọn 1 hoặc nhiều nhu cầu để AI/Trang chủ gợi ý sản phẩm phù hợp</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Tag 1: Gaming -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 hover:border-indigo-500 dark:hover:border-indigo-400 cursor-pointer transition has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50/40 dark:has-[:checked]:bg-indigo-950/40">
                    <input type="checkbox" name="tags[]" value="gaming" {{ in_array('gaming', $currentTags) ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">🎮</span>
                        <span class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center text-[10px] text-transparent check-indicator">✓</span>
                    </div>
                    <span class="text-xs font-black text-slate-900 dark:text-white">Chơi Game Khủng</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Chip mạnh, RAM &ge; 8GB, tản nhiệt tốt, 120Hz</span>
                </label>

                <!-- Tag 2: Camera -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 hover:border-rose-500 dark:hover:border-rose-400 cursor-pointer transition has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/40 dark:has-[:checked]:bg-rose-950/40">
                    <input type="checkbox" name="tags[]" value="camera" {{ in_array('camera', $currentTags) ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">📸</span>
                        <span class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center text-[10px] text-transparent check-indicator">✓</span>
                    </div>
                    <span class="text-xs font-black text-slate-900 dark:text-white">Chụp Ảnh Đỉnh Cao</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Cảm biến lớn, Zoom xa quang học, OIS</span>
                </label>

                <!-- Tag 3: Battery -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 hover:border-emerald-500 dark:hover:border-emerald-400 cursor-pointer transition has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40 dark:has-[:checked]:bg-emerald-950/40">
                    <input type="checkbox" name="tags[]" value="battery" {{ in_array('battery', $currentTags) ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">🔋</span>
                        <span class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center text-[10px] text-transparent check-indicator">✓</span>
                    </div>
                    <span class="text-xs font-black text-slate-900 dark:text-white">Pin Trâu Cả Ngày</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Pin &ge; 4500mAh, sạc nhanh công suất lớn</span>
                </label>

                <!-- Tag 4: Compact -->
                <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 hover:border-amber-500 dark:hover:border-amber-400 cursor-pointer transition has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/40 dark:has-[:checked]:bg-amber-950/40">
                    <input type="checkbox" name="tags[]" value="compact" {{ in_array('compact', $currentTags) ? 'checked' : '' }} class="sr-only">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-2xl">🪶</span>
                        <span class="w-5 h-5 rounded-full border border-slate-300 dark:border-slate-600 flex items-center justify-center text-[10px] text-transparent check-indicator">✓</span>
                    </div>
                    <span class="text-xs font-black text-slate-900 dark:text-white">Gọn Nhẹ & Sang Trọng</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-400 mt-1">Khung titan mỏng nhẹ, cầm vừa vặn tay</span>
                </label>
            </div>
        </div>


        <!-- 3. BỘ SƯU TẬP ẢNH ĐA GÓC NHÌN (MULTI-ANGLE PRODUCT GALLERY) -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="w-7 h-7 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-black">3</span>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Bộ Sưu Tập Ảnh Đa Góc Nhìn (Gallery)</h2>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Bạn có thể tải ảnh mới lên để thay đổi từng góc nhìn chi tiết của sản phẩm</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- 1. Ảnh Chính Diện (Ảnh Đại Diện) -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 text-center space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-blue-600 dark:text-blue-400">1. Ảnh Chính Diện</span>
                        <span class="text-[9px] text-slate-400">Ảnh hiện tại</span>
                    </div>
                    <div class="h-32 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-800 p-2" id="preview_box_main">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="max-h-full max-w-full object-contain drop-shadow" alt="Chính diện">
                        @else
                            <span class="text-2xl text-slate-300">📱</span>
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Đổi ảnh chính</span>
                        <input type="file" name="image" accept="image/*" onchange="previewImage(this, 'preview_box_main')"
                               class="w-full text-[11px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </label>
                </div>

                <!-- 2. Ảnh Cụm Camera Macro -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 text-center space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">2. Cụm Camera</span>
                        <span class="text-[9px] text-slate-400">{{ !empty($gallery['camera']) ? 'Đã có ảnh' : 'Chưa có' }}</span>
                    </div>
                    <div class="h-32 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-800 p-2" id="preview_box_camera">
                        @if(!empty($gallery['camera']))
                            <img src="{{ asset('storage/' . $gallery['camera']) }}" class="max-h-full max-w-full object-contain drop-shadow" alt="Camera">
                        @else
                            <span class="text-2xl text-slate-300">📸</span>
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Đổi ảnh camera</span>
                        <input type="file" name="gallery_camera" accept="image/*" onchange="previewImage(this, 'preview_box_camera')"
                               class="w-full text-[11px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer">
                    </label>
                </div>

                <!-- 3. Ảnh Cạnh Viền Titan -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 text-center space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">3. Cạnh Viền Khung Máy</span>
                        <span class="text-[9px] text-slate-400">{{ !empty($gallery['side']) ? 'Đã có ảnh' : 'Chưa có' }}</span>
                    </div>
                    <div class="h-32 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-800 p-2" id="preview_box_side">
                        @if(!empty($gallery['side']))
                            <img src="{{ asset('storage/' . $gallery['side']) }}" class="max-h-full max-w-full object-contain drop-shadow" alt="Cạnh viền">
                        @else
                            <span class="text-2xl text-slate-300">💎</span>
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Đổi ảnh cạnh viền</span>
                        <input type="file" name="gallery_side" accept="image/*" onchange="previewImage(this, 'preview_box_side')"
                               class="w-full text-[11px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer">
                    </label>
                </div>

                <!-- 4. Ảnh Nghiêng 45° / Mặt Lưng -->
                <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 text-center space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-black text-slate-800 dark:text-slate-200">4. Nghiêng 45° / Lưng</span>
                        <span class="text-[9px] text-slate-400">{{ !empty($gallery['back']) ? 'Đã có ảnh' : 'Chưa có' }}</span>
                    </div>
                    <div class="h-32 rounded-xl bg-white dark:bg-slate-900 flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-800 p-2" id="preview_box_back">
                        @if(!empty($gallery['back']))
                            <img src="{{ asset('storage/' . $gallery['back']) }}" class="max-h-full max-w-full object-contain drop-shadow" alt="Nghiêng 45°">
                        @else
                            <span class="text-2xl text-slate-300">📐</span>
                        @endif
                    </div>
                    <label class="block">
                        <span class="sr-only">Đổi ảnh nghiêng</span>
                        <input type="file" name="gallery_back" accept="image/*" onchange="previewImage(this, 'preview_box_back')"
                               class="w-full text-[11px] text-slate-500 dark:text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[10px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-600 cursor-pointer">
                    </label>
                </div>
            </div>
        </div>


        <!-- 4. BẢNG THÔNG SỐ KỸ THUẬT CHI TIẾT (SPECS MATRIX) -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800">
                <span class="w-7 h-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xs font-black">4</span>
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white">Thông Số Kỹ Thuật Chi Tiết (Specs Matrix)</h2>
                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Thông số hiển thị ở trang Chi tiết và bảng So Sánh đối chiếu</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📱 Màn hình hiển thị</label>
                    <input type="text" name="specs[screen]" value="{{ old('specs.screen', $specs['screen'] ?? '') }}"
                           placeholder="6.7 inch OLED 120Hz"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">⚡ Bộ vi xử lý (CPU)</label>
                    <input type="text" name="specs[cpu]" value="{{ old('specs.cpu', $specs['cpu'] ?? '') }}"
                           placeholder="Apple A18 Pro / Snapdragon 8 Gen 3"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🧠 Bộ nhớ RAM</label>
                    <input type="text" name="specs[ram]" value="{{ old('specs.ram', $specs['ram'] ?? '') }}"
                           placeholder="8GB / 12GB RAM"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">💾 Bộ nhớ trong (ROM)</label>
                    <input type="text" name="specs[storage]" value="{{ old('specs.storage', $specs['storage'] ?? '') }}"
                           placeholder="256GB / 512GB"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">📸 Cụm Camera sau</label>
                    <input type="text" name="specs[camera]" value="{{ old('specs.camera', $specs['camera'] ?? '') }}"
                           placeholder="48MP + 12MP + 12MP"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🤳 Camera trước (Selfie)</label>
                    <input type="text" name="specs[front_camera]" value="{{ old('specs.front_camera', $specs['front_camera'] ?? '') }}"
                           placeholder="12MP TrueDepth"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🔋 Pin & Công nghệ sạc</label>
                    <input type="text" name="specs[battery]" value="{{ old('specs.battery', $specs['battery'] ?? '') }}"
                           placeholder="5000 mAh, Sạc 45W"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">🤖 Hệ điều hành & AI</label>
                    <input type="text" name="specs[os]" value="{{ old('specs.os', $specs['os'] ?? '') }}"
                           placeholder="iOS 18 / Android 14"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-1">💧 Chuẩn kháng nước</label>
                    <input type="text" name="specs[waterproof]" value="{{ old('specs.waterproof', $specs['waterproof'] ?? '') }}"
                           placeholder="IP68 kháng nước"
                           class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- 5. QUẢN LÝ BIẾN THỂ & TỒN KHO TỪNG PHIÊN BẢN (PRODUCT VARIANTS) -->
        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5 transition-colors duration-300">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-xs font-black">5</span>
                    <div>
                        <h2 class="text-base font-black text-slate-900 dark:text-white">Quản Lý Phiên Bản & Tồn Kho Riêng</h2>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Chỉnh sửa hoặc thêm biến thể mới (Pro, Pro Max...) với giá và tồn kho riêng biệt</p>
                    </div>
                </div>
                <button type="button" onclick="addVariantRow()"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-black rounded-xl transition cursor-pointer flex items-center gap-1.5">
                    <span>＋</span> Thêm Phiên Bản
                </button>
            </div>

            <div id="variantsContainer" class="space-y-4">
                <!-- Biến thể hiện có sẽ được load bởi JS bên dưới -->
            </div>

            <p class="text-[11px] text-slate-400 dark:text-slate-500 italic">
                💡 Nếu xóa tất cả biến thể, hệ thống sẽ sử dụng giá và tồn kho ở mục "Thông Tin Cơ Bản".
            </p>
        </div>

        <!-- NÚT LƯU FORM -->
        <div class="flex items-center justify-end gap-3 pt-4">
            <a href="{{ route('products.index') }}" class="px-6 py-3 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold rounded-2xl transition">
                Hủy
            </a>
            <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-black uppercase tracking-wider rounded-2xl shadow-lg shadow-blue-500/25 transition cursor-pointer">
                ✓ Lưu Cập Nhật Sản Phẩm
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
    // Hàm xem trước ảnh upload
    function previewImage(input, boxId) {
        const box = document.getElementById(boxId);
        if (!box) return;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                box.innerHTML = `<img src="${e.target.result}" class="max-h-full max-w-full object-contain drop-shadow" alt="Preview">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle indicator UI cho checkbox tag
    document.querySelectorAll('input[name="tags[]"]').forEach(chk => {
        chk.addEventListener('change', function() {
            const ind = this.parentElement.querySelector('.check-indicator');
            if (ind) {
                if (this.checked) {
                    ind.classList.remove('text-transparent');
                    ind.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                } else {
                    ind.classList.add('text-transparent');
                    ind.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                }
            }
        });
        // Initial state
        if (chk.checked) {
            const ind = chk.parentElement.querySelector('.check-indicator');
            if (ind) {
                ind.classList.remove('text-transparent');
                ind.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            }
        }
    });

    // ========================================
    // QUẢN LÝ BIẾN THỂ SẢN PHẨM (DYNAMIC VARIANT ROWS)
    // ========================================
    let variantIndex = 0;

    function addVariantRow(data = {}) {
        const container = document.getElementById('variantsContainer');
        const idx = variantIndex++;

        const row = document.createElement('div');
        row.className = 'variant-row p-5 bg-slate-50/80 dark:bg-slate-800/50 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3 relative';
        row.innerHTML = `
            <div class="flex items-center justify-between mb-1">
                <span class="text-xs font-black text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                    📦 Phiên bản #${idx + 1} ${data.id ? '<span class="text-[9px] text-blue-500 font-mono">ID:' + data.id + '</span>' : '<span class="text-[9px] text-emerald-500">(Mới)</span>'}
                </span>
                <button type="button" onclick="this.closest('.variant-row').remove()"
                        class="text-xs font-bold text-rose-500 hover:text-rose-700 dark:hover:text-rose-400 cursor-pointer px-2 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition">
                    ✕ Xóa
                </button>
            </div>

            ${data.id ? `<input type="hidden" name="variants[${idx}][id]" value="${data.id}">` : ''}

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Tên phiên bản *</label>
                    <input type="text" name="variants[${idx}][version_name]" value="${data.version_name || ''}"
                           placeholder="Tiêu Chuẩn, Pro, Pro Max..."
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Mã SKU</label>
                    <input type="text" name="variants[${idx}][sku]" value="${data.sku || ''}"
                           placeholder="IP16-PRO-256-BLK"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Màu sắc</label>
                    <input type="text" name="variants[${idx}][color]" value="${data.color || ''}"
                           placeholder="Titan Sa Mạc"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Mã màu HEX</label>
                    <div class="flex items-center gap-2">
                        <input type="color" name="variants[${idx}][color_code]" value="${data.color_code || '#000000'}"
                               class="w-8 h-8 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer p-0"
                               onchange="this.nextElementSibling.value = this.value">
                        <input type="text" value="${data.color_code || '#000000'}" readonly
                               class="flex-1 px-2 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-mono text-slate-600 dark:text-slate-400">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Dung lượng</label>
                    <input type="text" name="variants[${idx}][storage]" value="${data.storage || ''}"
                           placeholder="128GB, 256GB..."
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">RAM</label>
                    <input type="text" name="variants[${idx}][ram]" value="${data.ram || ''}"
                           placeholder="8GB, 12GB..."
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Giá bán (VNĐ) *</label>
                    <input type="number" name="variants[${idx}][price]" value="${data.price || ''}"
                           placeholder="34990000"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-bold text-rose-600 dark:text-rose-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Tồn kho riêng *</label>
                    <input type="number" name="variants[${idx}][stock]" value="${data.stock ?? 10}"
                           placeholder="10"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-black text-emerald-600 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">Cân nặng (g)</label>
                    <input type="number" name="variants[${idx}][weight]" value="${data.weight || 200}"
                           placeholder="200"
                           class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-medium text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>
            </div>
        `;

        container.appendChild(row);
    }

    // Load biến thể hiện có từ server (nếu đang edit)
    document.addEventListener('DOMContentLoaded', function() {
        const existingVariants = @json($product->variants ?? []);
        existingVariants.forEach(v => addVariantRow(v));
    });
</script>
@endpush