@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">

    <!-- Header Breadcrumb & Tiêu đề -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[11px] font-black uppercase tracking-wider mb-2 border border-blue-100 dark:border-blue-900">
                <span>⚖️</span> Đối Chiếu Flagship
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                So Sánh Thông Số Kỹ Thuật
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Đặt 2-3 chiếc điện thoại cạnh nhau để xem sự khác biệt về camera, hiệu năng chip, màn hình và thời lượng pin.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <!-- Toggle Chỉ hiện điểm khác biệt -->
            <label class="relative inline-flex items-center cursor-pointer bg-slate-100 dark:bg-slate-800 px-4 py-2.5 rounded-2xl border border-slate-200/80 dark:border-slate-700">
                <input type="checkbox" id="diffToggle" onchange="toggleDifferencesOnly()" class="sr-only peer">
                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[18px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-xs font-black text-slate-700 dark:text-slate-300 select-none">Chỉ hiện điểm khác biệt</span>
            </label>

            <a href="{{ route('home') }}#all-products" class="px-5 py-2.5 bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 text-white text-xs font-black rounded-2xl transition shadow-md">
                + Thêm Máy Khác
            </a>
        </div>
    </div>

    <!-- Bảng So Sánh Thông Số Trực Quan 2-3 Cột -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden p-4 sm:p-8">
        
        <!-- Khung Header Sản Phẩm Cần So Sánh -->
        <div class="grid grid-cols-12 gap-4 pb-8 border-b border-slate-200 dark:border-slate-800 items-end">
            <!-- Cột 1: Nhãn tiêu chí -->
            <div class="col-span-12 sm:col-span-3 font-black text-slate-400 dark:text-slate-500 text-xs uppercase tracking-wider pb-2">
                Thiết Bị So Sánh ({{ $comparedProducts->count() }}/3)
            </div>

            <!-- Các Cột Sản Phẩm Đang So Sánh -->
            @php $colSpan = count($comparedProducts) === 2 ? 'sm:col-span-4' : 'sm:col-span-3'; @endphp
            @foreach($comparedProducts as $product)
            <div class="col-span-12 {{ $colSpan }} relative group bg-slate-50 dark:bg-slate-800/50 p-4 rounded-3xl border border-slate-200/80 dark:border-slate-700 text-center space-y-3">
                
                <!-- Nút xóa sản phẩm khỏi so sánh -->
                <button type="button" onclick="removeCompareItem({{ $product->id }})" 
                        class="absolute top-3 right-3 w-7 h-7 rounded-full bg-white dark:bg-slate-700 text-slate-400 hover:text-rose-600 flex items-center justify-center text-xs shadow-sm transition">
                    ✕
                </button>

                <div class="h-32 sm:h-40 flex items-center justify-center p-2">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?auto=format&fit=crop&w=400&q=80' }}" 
                         alt="{{ $product->name }}" 
                         class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300 drop-shadow-md">
                </div>

                <div>
                    <span class="text-[10px] font-black uppercase text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-md">
                        {{ $product->brand ?? 'PhoneStore' }}
                    </span>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm mt-1 line-clamp-1">
                        {{ $product->name }}
                    </h3>
                    <p class="text-rose-600 dark:text-rose-400 font-black text-base mt-0.5">
                        {{ number_format($product->price) }} đ
                    </p>
                </div>

                <div class="pt-2 flex gap-2">
                    <a href="{{ route('products.show', $product->id) }}" 
                       class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md transition">
                        Xem Chi Tiết
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Thẻ Thêm Sản Phẩm Thứ 3 nếu còn chỗ -->
            @if($comparedProducts->count() < 3)
            <div class="col-span-12 {{ $colSpan }} flex flex-col items-center justify-center p-6 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-3xl min-h-[220px] text-center space-y-3">
                <span class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center text-xl font-bold">+</span>
                <div>
                    <h4 class="text-xs font-black text-slate-700 dark:text-slate-300">Thêm Máy Để So Sánh</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Chọn tối đa 3 chiếc điện thoại cùng lúc</p>
                </div>
                <select onchange="addCompareFromSelect(this.value)" class="px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none cursor-pointer max-w-full">
                    <option value="">-- Chọn điện thoại thêm --</option>
                    @foreach($allProducts as $p)
                        @if(!$comparedProducts->contains('id', $p->id))
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ number_format($p->price) }}đ)</option>
                        @endif
                    @endforeach
                </select>
            </div>
            @endif

        </div>

        <!-- DANH SÁCH BẢNG THÔNG SỐ SO SÁNH ĐỐI CHIẾU (SPECS MATRIX) -->
        @php
            $specRows = [
                'Màn hình hiển thị' => [
                    'icon' => '📱',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['screen'] ?? ($p->brand === 'Apple' ? '6.7" Super Retina XDR OLED 120Hz ProMotion' : '6.8" Dynamic AMOLED 2X 120Hz LTPO');
                    })
                ],
                'Bộ vi xử lý (Chipset)' => [
                    'icon' => '⚡',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['cpu'] ?? ($p->brand === 'Apple' ? 'Apple A18 Pro / A17 Pro (3nm đỉnh cao)' : 'Snapdragon 8 Gen 3 for Galaxy (4nm)');
                    })
                ],
                'Bộ nhớ RAM' => [
                    'icon' => '🧠',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['ram'] ?? '8GB / 12GB LPDDR5X';
                    })
                ],
                'Bộ nhớ trong (ROM)' => [
                    'icon' => '💾',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['storage'] ?? '256GB / 512GB / 1TB';
                    })
                ],
                'Cụm Camera sau' => [
                    'icon' => '📸',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['camera'] ?? ($p->brand === 'Apple' ? '48MP Fusion + 12MP Tele 5x + 48MP Ultra Wide' : '200MP Cảm biến lớn + 50MP Zoom 5x + 12MP Siêu rộng');
                    })
                ],
                'Camera trước (Selfie)' => [
                    'icon' => '🤳',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['front_camera'] ?? '12MP TrueDepth Autofocus';
                    })
                ],
                'Dung lượng Pin & Sạc' => [
                    'icon' => '🔋',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['battery'] ?? ($p->brand === 'Apple' ? '4,441 mAh (Sạc MagSafe 25W & Type-C)' : '5,000 mAh (Sạc siêu tốc 45W)');
                    })
                ],
                'Chất liệu hoàn thiện' => [
                    'icon' => '💎',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['material'] ?? ($p->brand === 'Apple' ? 'Khung Titanium cấp độ 5 + Mặt kính Ceramic Shield' : 'Khung Titanium Armor + Kính Gorilla Glass Armor');
                    })
                ],
                'Hệ điều hành & AI' => [
                    'icon' => '🤖',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['os'] ?? ($p->brand === 'Apple' ? 'iOS 18 (Apple Intelligence AI độc quyền)' : 'Android 14 (Galaxy AI thông minh)');
                    })
                ],
                'Chuẩn kháng nước' => [
                    'icon' => '💧',
                    'values' => $comparedProducts->map(function($p) {
                        return $p->specs['waterproof'] ?? 'IP68 (Ngâm sâu 6m trong 30 phút)';
                    })
                ]
            ];
        @endphp

        <div class="divide-y divide-slate-100 dark:divide-slate-800" id="specsMatrix">
            @foreach($specRows as $label => $specData)
                @php
                    $vals = $specData['values']->values()->all();
                    $isDifferent = count(array_unique($vals)) > 1;
                @endphp
                <div class="spec-row grid grid-cols-12 gap-4 py-5 items-center hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition {{ $isDifferent ? 'is-different' : 'is-same' }}">
                    <!-- Cột Tiêu đề thông số -->
                    <div class="col-span-12 sm:col-span-3 flex items-center gap-2">
                        <span class="text-base">{{ $specData['icon'] }}</span>
                        <div>
                            <h4 class="text-xs font-black text-slate-800 dark:text-slate-200">{{ $label }}</h4>
                            @if($isDifferent)
                                <span class="text-[9px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-widest inline-block">Khác biệt</span>
                            @endif
                        </div>
                    </div>

                    <!-- Cột Giá Trị Của Từng Máy -->
                    @foreach($vals as $idx => $val)
                    <div class="col-span-12 {{ $colSpan }} px-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <div class="p-3 rounded-2xl {{ $isDifferent ? 'bg-blue-50/50 dark:bg-blue-950/30 border-l-4 border-blue-600 dark:border-blue-400 font-bold text-slate-900 dark:text-white' : 'bg-slate-50 dark:bg-slate-800/40' }}">
                            {{ $val }}
                        </div>
                    </div>
                    @endforeach
                </div>
            @endforeach
        </div>

    </div>

</div>

@push('scripts')
<script>
    // Bật / Tắt chế độ "Chỉ hiện điểm khác biệt"
    function toggleDifferencesOnly() {
        const isChecked = document.getElementById('diffToggle').checked;
        const sameRows = document.querySelectorAll('.spec-row.is-same');
        sameRows.forEach(row => {
            if (isChecked) {
                row.classList.add('hidden');
            } else {
                row.classList.remove('hidden');
            }
        });
    }

    // Xóa một máy khỏi danh sách so sánh
    function removeCompareItem(removeId) {
        const currentIds = {!! json_encode($comparedProducts->pluck('id')) !!};
        const newIds = currentIds.filter(id => id !== removeId);
        if (newIds.length === 0) {
            window.location.href = "{{ route('home') }}#all-products";
        } else {
            window.location.href = "{{ route('products.compare') }}?ids=" + newIds.join(',');
        }
    }

    // Thêm máy mới vào so sánh
    function addCompareFromSelect(newId) {
        if (!newId) return;
        const currentIds = {!! json_encode($comparedProducts->pluck('id')) !!};
        if (!currentIds.includes(parseInt(newId))) {
            currentIds.push(parseInt(newId));
        }
        window.location.href = "{{ route('products.compare') }}?ids=" + currentIds.join(',');
    }
</script>
@endpush
@endsection
