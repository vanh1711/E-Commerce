@extends('layouts.app')

@section('content')
<div class="space-y-10 max-w-7xl mx-auto">
    
    <!-- Breadcrumb điều hướng -->
    <nav class="flex text-xs font-bold text-slate-400 gap-2 items-center">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Trang Chủ</a>
        <span>/</span>
        <a href="{{ route('home') }}#all-products" class="hover:text-blue-600">Điện Thoại</a>
        <span>/</span>
        <span class="text-blue-600 font-extrabold truncate max-w-xs">{{ $product->name }}</span>
    </nav>

    <!-- KHỐI CHÍNH: ẢNH & BỘ CHỌN CẤU HÌNH (DYNAMICS OPTION & PRICING) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 bg-white dark:bg-slate-900 p-6 sm:p-10 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-300">
        
        <!-- CỘT TRÁI: HÌNH ẢNH SẢN PHẨM & ZOOM KÍNH LÚP ĐA GÓC NHÌN (5 CỘT) -->
        <div class="lg:col-span-5 flex flex-col justify-between space-y-5">
            <!-- Khung Ảnh Chính Tích Hợp Kính Lúp Zoom Khi Rê Chuột -->
            <div class="relative bg-slate-50/80 dark:bg-slate-800/50 rounded-3xl p-6 flex items-center justify-center min-h-[380px] sm:min-h-[440px] border border-slate-100 dark:border-slate-800 overflow-hidden group cursor-crosshair select-none"
                 id="zoomContainer"
                 onmousemove="zoomProductImage(event)"
                 onmouseleave="resetZoomImage()">
                
                @php
                    $gallery = is_array($product->gallery) ? $product->gallery : [];
                    $mainImg = $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=600&q=80';
                    $cameraImg = !empty($gallery['camera']) ? asset('storage/' . $gallery['camera']) : $mainImg;
                    $sideImg = !empty($gallery['side']) ? asset('storage/' . $gallery['side']) : $mainImg;
                    $backImg = !empty($gallery['back']) ? asset('storage/' . $gallery['back']) : $mainImg;
                @endphp

                <!-- Ảnh Sản Phẩm Chính -->
                <img id="mainProductImg" 
                     src="{{ $mainImg }}" 
                     class="max-h-[340px] object-contain transition-transform duration-150 drop-shadow-2xl will-change-transform pointer-events-none" 
                     alt="{{ $product->name }}">

                <!-- Badge góc nhìn & zoom -->
                <div class="absolute top-4 left-4 flex flex-col gap-1.5 z-20">
                    <span class="bg-slate-900/90 dark:bg-blue-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-sm backdrop-blur-sm">
                        🔍 Rê chuột để Zoom chi tiết
                    </span>
                    <span id="currentAngleLabel" class="bg-white/90 dark:bg-slate-900/90 text-slate-700 dark:text-slate-300 text-[10px] font-bold px-2.5 py-0.5 rounded-md shadow-xs border border-slate-200/80 dark:border-slate-700">
                        Góc: Chính diện
                    </span>
                </div>

                <!-- Kính Lúp Magnifier Lens nổi bật -->
                <div id="zoomLens" class="hidden absolute pointer-events-none rounded-full border-2 border-blue-500 bg-blue-500/10 backdrop-blur-xs shadow-2xl z-10 w-32 h-32 -translate-x-1/2 -translate-y-1/2"></div>
            </div>

            <!-- Thumbnail 4 Góc Nhìn (Multi-Angle Gallery: Chính diện, Cụm Camera, Cạnh viền Titan, Nghiêng 45°/Lưng) -->
            <div class="grid grid-cols-4 gap-2.5">
                <button type="button" onclick="switchProductAngle('{{ $mainImg }}', 'Góc: Chính diện', this)" 
                        class="angle-thumb-btn active p-2 rounded-2xl border-2 border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 text-center transition hover:border-blue-500 cursor-pointer">
                    <img src="{{ $mainImg }}" class="h-12 w-full object-contain mx-auto" alt="Chính diện">
                    <span class="text-[9px] font-black block mt-1 text-slate-700 dark:text-slate-300">Chính diện</span>
                </button>

                <button type="button" onclick="switchProductAngle('{{ $cameraImg }}', 'Góc: Cụm Camera Macro', this)" 
                        class="angle-thumb-btn p-2 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-center transition hover:border-blue-500 cursor-pointer">
                    <img src="{{ $cameraImg }}" class="h-12 w-full object-contain mx-auto" alt="Camera">
                    <span class="text-[9px] font-bold block mt-1 text-slate-500 dark:text-slate-400">Cụm Camera</span>
                </button>

                <button type="button" onclick="switchProductAngle('{{ $sideImg }}', 'Góc: Cạnh viền Khung máy', this)" 
                        class="angle-thumb-btn p-2 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-center transition hover:border-blue-500 cursor-pointer">
                    <img src="{{ $sideImg }}" class="h-12 w-full object-contain mx-auto" alt="Cạnh viền">
                    <span class="text-[9px] font-bold block mt-1 text-slate-500 dark:text-slate-400">Cạnh Viền</span>
                </button>

                <button type="button" onclick="switchProductAngle('{{ $backImg }}', 'Góc: Nghiêng 45 độ / Lưng', this)" 
                        class="angle-thumb-btn p-2 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-center transition hover:border-blue-500 cursor-pointer">
                    <img src="{{ $backImg }}" class="h-12 w-full object-contain mx-auto" alt="Nghiêng 45°">
                    <span class="text-[9px] font-bold block mt-1 text-slate-500 dark:text-slate-400">Nghiêng / Lưng</span>
                </button>
            </div>

            <!-- Cam kết mua hàng -->
            <div class="grid grid-cols-3 gap-2.5 text-center pt-2">
                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-lg">🛡️</span>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 mt-1">Bảo hành 12T</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">1 đổi 1 30 ngày</p>
                </div>
                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-lg">⚡</span>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 mt-1">Giao Nhanh 2H</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Miễn phí toàn quốc</p>
                </div>
                <div class="p-3 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                    <span class="text-lg">🔄</span>
                    <p class="text-xs font-black text-slate-800 dark:text-slate-200 mt-1">Thu Cũ Giá Cao</p>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500">Trợ giá đến 5 triệu</p>
                </div>
            </div>
        </div>


        <!-- CỘT PHẢI: CHỌN CẤU HÌNH, MÀU, BỘ NHỚ & TÍNH TIỀN (7 CỘT) -->
        <div class="lg:col-span-7 space-y-6">
            
            <div>
                <span class="text-[11px] font-black uppercase tracking-wider text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-3 py-1 rounded-full border border-blue-100 dark:border-blue-900">
                    {{ $product->brand ?? 'Flagship Smartphone' }}
                </span>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mt-2.5">
                    {{ $product->name }}
                </h1>
                <div class="flex items-center gap-3 mt-2 text-xs text-slate-500 dark:text-slate-400">
                    <span>Mã SP: <strong class="text-slate-700 dark:text-slate-300 font-mono">{{ $product->slug ?? $product->id }}</strong></span>
                    <span>•</span>
                    <span id="stockStatus">Tình trạng: <strong class="text-emerald-600 dark:text-emerald-400 font-black">● Còn {{ $product->total_stock }} máy</strong></span>
                    <span>•</span>
                    <a href="{{ route('products.compare') }}?ids={{ $product->id }}" class="text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1">
                        <span>⚖️</span> So sánh máy này
                    </a>
                </div>
            </div>

            <!-- GIÁ TIỀN TỰ ĐỘNG THAY ĐỔI THEO OPTION -->
            <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50/30 dark:from-slate-800/80 dark:to-slate-800/40 rounded-3xl border border-slate-200/80 dark:border-slate-800 flex items-baseline justify-between">
                <div>
                    <span class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider block">Giá bán ưu đãi:</span>
                    <div class="flex items-baseline gap-3 mt-1.5">
                        <span id="displayPrice" class="text-3xl sm:text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-600 dark:from-red-400 dark:to-rose-400 tracking-tight">
                            {{ number_format($product->min_price) }} đ
                        </span>
                        <span id="oldPrice" class="text-sm font-semibold text-slate-400 dark:text-slate-500 line-through">
                            {{ number_format($product->min_price * 1.15) }} đ
                        </span>
                    </div>
                </div>
                <span id="discountBadge" class="px-3 py-1 bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-400 text-xs font-black rounded-xl border border-transparent dark:border-rose-900">
                    Tiết kiệm 15%
                </span>
            </div>

            @php
                $variants = $product->variants;
                $hasVariants = $variants->count() > 0;
                // Nhóm các biến thể theo thuộc tính
                $versionNames = $hasVariants ? $variants->pluck('version_name')->filter()->unique()->values() : collect();
                $storages = $hasVariants ? $variants->pluck('storage')->filter()->unique()->values() : collect();
                $colors = $hasVariants ? $variants->whereNotNull('color')->unique('color')->values() : collect();
            @endphp

            @if($hasVariants)
            <!-- 1. CHỌN PHIÊN BẢN DÒNG MÁY -->
            @if($versionNames->count() > 0)
            <div class="space-y-2.5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    1. Chọn Phiên Bản Dòng Máy:
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach($versionNames as $idx => $vName)
                    <button type="button" onclick="selectVersionName('{{ $vName }}', this)"
                            class="variant-btn p-3.5 rounded-2xl border-2 {{ $idx === 0 ? 'border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700' }} text-left transition duration-200 cursor-pointer">
                        <span class="text-xs font-black block">{{ $vName }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 2. CHỌN DUNG LƯỢNG BỘ NHỚ -->
            @if($storages->count() > 0)
            <div class="space-y-2.5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    2. Chọn Dung Lượng Bộ Nhớ:
                </label>
                <div class="grid grid-cols-4 gap-2.5" id="storageOptionsContainer">
                    @foreach($storages as $idx => $stor)
                    <button type="button" onclick="selectStorage('{{ $stor }}', this)"
                            class="storage-btn p-3 rounded-2xl border-2 {{ $idx === 0 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700' }} text-center transition duration-200 cursor-pointer">
                        <span class="text-xs font-black block">{{ $stor }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- 3. CHỌN MÀU SẮC -->
            @if($colors->count() > 0)
            <div class="space-y-2.5">
                <label class="block text-xs font-black uppercase tracking-wider text-slate-400 dark:text-slate-500">
                    3. Chọn Màu Sắc:
                </label>
                <div class="grid grid-cols-3 gap-2.5" id="colorOptionsContainer">
                    @foreach($colors as $idx => $cv)
                    <button type="button" onclick="selectColor('{{ $cv->color }}', this)"
                            class="color-btn p-2.5 rounded-2xl border-2 {{ $idx === 0 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300' : 'border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700' }} flex items-center justify-center gap-2 transition duration-200 cursor-pointer">
                        <span class="w-3.5 h-3.5 rounded-full shadow-sm" style="background-color: {{ $cv->color_code ?? '#888' }}"></span>
                        <span class="text-xs font-bold">{{ $cv->color }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            @else
            <!-- SẢN PHẨM KHÔNG CÓ BIẾN THỂ - Hiển thị đơn giản -->
            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                <p class="text-xs text-slate-500 dark:text-slate-400">Sản phẩm này chỉ có một phiên bản duy nhất.</p>
            </div>
            @endif

            <!-- 4. KHỐI FORM MUA HÀNG & THÊM VÀO GIỎ -->
            <div class="pt-4 space-y-3">
                @auth
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" id="addToCartForm" class="space-y-3">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="variant_id" id="hiddenVariantId" value="">
                        <input type="hidden" name="selected_price" id="hiddenSelectedPrice" value="{{ $product->min_price }}">
                        <input type="hidden" name="variant_label" id="hiddenVariantLabel" value="">

                        <button type="submit" name="action" value="buy_now" id="buyNowBtn"
                                class="w-full py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-rose-600/25 transition duration-200 active:scale-[0.99] flex flex-col items-center justify-center cursor-pointer">
                            <span>⚡ MUA NGAY VỚI GIÁ NÀY</span>
                            <span class="text-[10px] font-medium opacity-90">Chuyển ngay tới trang thanh toán & đặt ship GPS</span>
                        </button>

                        <div class="grid grid-cols-2 gap-3">
                            <button type="submit" name="action" value="add_to_cart"
                                    class="py-3 bg-blue-50 dark:bg-blue-950/60 hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-blue-200 dark:border-blue-800 text-blue-700 dark:text-blue-400 font-bold text-xs rounded-2xl transition flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>🛒</span> Thêm Vào Giỏ
                            </button>
                            <a href="{{ route('cart.index') }}"
                               class="py-3 bg-slate-900 dark:bg-slate-800 hover:bg-slate-800 dark:hover:bg-slate-700 text-white font-bold text-xs rounded-2xl border border-transparent dark:border-slate-700 transition flex items-center justify-center gap-1.5">
                                <span>💳</span> Xem Giỏ Hàng
                            </a>
                        </div>
                    </form>
                @else
                    <a href="{{ route('login') }}" 
                       class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition duration-200 flex items-center justify-center gap-2">
                        <span>Đăng Nhập Để Mua Hàng</span>
                    </a>
                @endauth
            </div>

        </div>
    </div>

    <!-- KHỐI MÔ TẢ CHI TIẾT & THÔNG SỐ KỸ THUẬT (SPECS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Mô tả (7 cột) -->
        <div class="lg:col-span-7 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
            <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>📖</span> Đặc Điểm Nổi Bật
            </h2>
            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed space-y-3">
                <p>{{ $product->description ?? 'Mẫu flagship đỉnh cao được trang bị vi xử lý thế hệ mới, màn hình siêu sáng 120Hz và cụm camera chuyên nghiệp cho trải nghiệm hình ảnh tuyệt mỹ.' }}</p>
                <div class="p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50">
                    <h4 class="font-black text-[11px] text-blue-900 dark:text-blue-300 uppercase">Khuyến Mãi Đặc Biệt Đi Kèm:</h4>
                    <ul class="text-xs text-blue-800 dark:text-blue-400 mt-2 space-y-1 list-disc list-inside">
                        <li>Tặng củ sạc nhanh Flagship 45W trị giá 690.000 đ</li>
                        <li>Gói bảo hành rơi vỡ 12 tháng VIP Care</li>
                        <li>Trợ giá lên đời 2.000.000 đ khi tham gia Thu Cũ Đổi Mới</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Thông số kỹ thuật (5 cột) -->
        <div class="lg:col-span-5 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4 transition-colors duration-300">
            <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                <span>⚙️</span> Thông Số Kỹ Thuật
            </h2>
            @php
                $specs = is_array($product->specs) ? $product->specs : [];
            @endphp
            <div class="space-y-2.5 text-xs divide-y divide-slate-100 dark:divide-slate-800">
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Màn hình:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['screen'] ?? ($product->brand === 'Apple' ? '6.7" OLED 120Hz ProMotion' : '6.8" Dynamic AMOLED 2X') }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Vi xử lý (CPU):</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['cpu'] ?? ($product->brand === 'Apple' ? 'Apple A18 Pro (3nm)' : 'Snapdragon 8 Gen 3') }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">RAM:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['ram'] ?? '8GB / 12GB LPDDR5X' }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Bộ nhớ (ROM):</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['storage'] ?? '256GB / 512GB / 1TB' }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Camera sau:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['camera'] ?? '48MP + 12MP + 12MP' }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Camera trước:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['front_camera'] ?? '12MP TrueDepth AI' }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Pin & Sạc:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['battery'] ?? '4,422 mAh, Sạc nhanh 45W' }}</span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Hệ điều hành:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['os'] ?? ($product->brand === 'Apple' ? 'iOS 18 (Apple Intelligence)' : 'Android 14 / One UI 6.1') }}</span>
                </div>
                @if(!empty($specs['waterproof']))
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Kháng nước:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['waterproof'] }}</span>
                </div>
                @endif
                @if(!empty($specs['material']))
                <div class="flex justify-between pt-2">
                    <span class="text-slate-400 dark:text-slate-500">Chất liệu:</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-right">{{ $specs['material'] }}</span>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // ========================================
    // DỮ LIỆU BIẾN THỂ TỪ DATABASE
    // ========================================
    const allVariants = @json($product->variants ?? []);
    const basePrice = {{ $product->price }};
    const hasVariants = allVariants.length > 0;

    let selectedVersion = '';
    let selectedStorage = '';
    let selectedColor = '';
    let matchedVariant = null;

    // Khởi tạo mặc định
    if (hasVariants) {
        const firstV = allVariants[0];
        selectedVersion = firstV.version_name || '';
        selectedStorage = firstV.storage || '';
        selectedColor = firstV.color || '';
        matchedVariant = firstV;
        updateUI();
    }

    function findMatchingVariant() {
        // Tìm variant khớp chính xác nhất với các lựa chọn hiện tại
        let candidates = allVariants;

        if (selectedVersion) {
            const filtered = candidates.filter(v => v.version_name === selectedVersion);
            if (filtered.length > 0) candidates = filtered;
        }
        if (selectedStorage) {
            const filtered = candidates.filter(v => v.storage === selectedStorage);
            if (filtered.length > 0) candidates = filtered;
        }
        if (selectedColor) {
            const filtered = candidates.filter(v => v.color === selectedColor);
            if (filtered.length > 0) candidates = filtered;
        }

        return candidates[0] || allVariants[0] || null;
    }

    function updateUI() {
        matchedVariant = findMatchingVariant();

        if (!matchedVariant) return;

        const price = parseFloat(matchedVariant.price);
        const originalPrice = matchedVariant.original_price ? parseFloat(matchedVariant.original_price) : price * 1.15;
        const stock = parseInt(matchedVariant.stock);
        const variantId = matchedVariant.id;

        // Cập nhật giá hiển thị
        document.getElementById('displayPrice').innerText = new Intl.NumberFormat('vi-VN').format(price) + ' đ';
        document.getElementById('oldPrice').innerText = new Intl.NumberFormat('vi-VN').format(originalPrice) + ' đ';

        // Cập nhật phần trăm giảm giá
        if (originalPrice > price) {
            const discount = Math.round((1 - price / originalPrice) * 100);
            document.getElementById('discountBadge').innerText = 'Tiết kiệm ' + discount + '%';
        }

        // Cập nhật tồn kho
        const stockEl = document.getElementById('stockStatus');
        if (stock > 0) {
            stockEl.innerHTML = 'Tình trạng: <strong class="text-emerald-600 dark:text-emerald-400 font-black">● Còn ' + stock + ' máy</strong>';
        } else {
            stockEl.innerHTML = 'Tình trạng: <strong class="text-rose-600 dark:text-rose-400 font-black">✕ Hết hàng</strong>';
        }

        // Cập nhật hidden inputs
        const hiddenPrice = document.getElementById('hiddenSelectedPrice');
        const hiddenVariantId = document.getElementById('hiddenVariantId');
        const hiddenLabel = document.getElementById('hiddenVariantLabel');

        if (hiddenPrice) hiddenPrice.value = price;
        if (hiddenVariantId) hiddenVariantId.value = variantId;
        if (hiddenLabel) {
            hiddenLabel.value = [matchedVariant.version_name, matchedVariant.storage, matchedVariant.color]
                .filter(Boolean).join(' / ');
        }

        // Disable/Enable nút mua nếu hết hàng
        const buyBtn = document.getElementById('buyNowBtn');
        if (buyBtn) {
            if (stock <= 0) {
                buyBtn.disabled = true;
                buyBtn.classList.add('opacity-50', 'cursor-not-allowed');
                buyBtn.querySelector('span').innerText = '✕ HẾT HÀNG - PHIÊN BẢN NÀY';
            } else {
                buyBtn.disabled = false;
                buyBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                buyBtn.querySelector('span').innerText = '⚡ MUA NGAY VỚI GIÁ NÀY';
            }
        }
    }

    function selectVersionName(name, btn) {
        document.querySelectorAll('.variant-btn').forEach(b => {
            b.className = 'variant-btn p-3.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700 text-left transition duration-200 cursor-pointer';
        });
        btn.className = 'variant-btn p-3.5 rounded-2xl border-2 border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-left transition duration-200 cursor-pointer';
        selectedVersion = name;
        updateUI();
    }

    function selectStorage(size, btn) {
        document.querySelectorAll('.storage-btn').forEach(b => {
            b.className = 'storage-btn p-3 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700 text-center transition duration-200 cursor-pointer';
        });
        btn.className = 'storage-btn p-3 rounded-2xl border-2 border-blue-600 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 text-center transition duration-200 cursor-pointer';
        selectedStorage = size;
        updateUI();
    }

    function selectColor(colorName, btn) {
        document.querySelectorAll('.color-btn').forEach(b => {
            b.className = 'color-btn p-2.5 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 hover:border-slate-300 dark:hover:border-slate-700 flex items-center justify-center gap-2 transition duration-200 cursor-pointer';
        });
        btn.className = 'color-btn p-2.5 rounded-2xl border-2 border-blue-600 bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 flex items-center justify-center gap-2 transition duration-200 cursor-pointer';
        selectedColor = colorName;
        updateUI();
    }

    // ========================================================
    // ZOOMABLE PRODUCT GALLERY & KÍNH LÚP MAGNIFIER LENS
    // ========================================================
    function zoomProductImage(e) {
        const container = document.getElementById('zoomContainer');
        const img = document.getElementById('mainProductImg');
        const lens = document.getElementById('zoomLens');

        if (!container || !img) return;

        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;

        img.style.transformOrigin = `${xPercent}% ${yPercent}%`;
        img.style.transform = 'scale(2.3)';

        if (lens) {
            lens.classList.remove('hidden');
            lens.style.left = `${x}px`;
            lens.style.top = `${y}px`;
        }
    }

    function resetZoomImage() {
        const img = document.getElementById('mainProductImg');
        const lens = document.getElementById('zoomLens');

        if (img) {
            img.style.transformOrigin = 'center center';
            img.style.transform = 'scale(1)';
        }
        if (lens) {
            lens.classList.add('hidden');
        }
    }

    function switchProductAngle(src, label, btn) {
        const img = document.getElementById('mainProductImg');
        const labelEl = document.getElementById('currentAngleLabel');

        if (img) {
            img.style.opacity = '0.3';
            setTimeout(() => {
                img.src = src;
                img.style.opacity = '1';
            }, 120);
        }

        if (labelEl) labelEl.innerText = label;

        document.querySelectorAll('.angle-thumb-btn').forEach(b => {
            b.className = 'angle-thumb-btn p-2 rounded-2xl border-2 border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 text-center transition hover:border-blue-500';
            const span = b.querySelector('span');
            if (span) span.className = 'text-[9px] font-bold block mt-1 text-slate-500 dark:text-slate-400';
        });

        if (btn) {
            btn.className = 'angle-thumb-btn active p-2 rounded-2xl border-2 border-blue-600 bg-blue-50/50 dark:bg-blue-950/40 text-center transition hover:border-blue-500';
            const span = btn.querySelector('span');
            if (span) span.className = 'text-[9px] font-black block mt-1 text-slate-700 dark:text-slate-300';
        }
    }
</script>
@endpush