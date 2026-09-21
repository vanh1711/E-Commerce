@extends('layouts.app')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">

    {{-- Breadcrumb & Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 dark:text-slate-500 mb-1">
                <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Trang chủ</a>
                <span>/</span>
                <span class="text-blue-600 dark:text-blue-400">Giỏ hàng</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                <span>🛒</span> Giỏ Hàng Mua Sắm
                @if(count($cart) > 0)
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 font-bold border border-blue-200 dark:border-blue-900">
                        {{ count($cart) }} sản phẩm
                    </span>
                @endif
            </h1>
        </div>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 bg-blue-50/80 dark:bg-blue-950/40 px-4 py-2.5 rounded-full transition hover:bg-blue-100 dark:hover:bg-blue-900/60 border border-transparent dark:border-blue-900/50">
            <span>Tiếp tục chọn thêm sản phẩm</span>
        </a>
    </div>

    @if(count($cart) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Danh sách sản phẩm (Cột trái: 8 cột) --}}
        <div class="lg:col-span-8 space-y-4">
            
            {{-- Toolbar chọn tất cả --}}
            <div class="bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-between transition-colors duration-300">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" checked 
                           class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 dark:bg-slate-800 cursor-pointer">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Chọn tất cả ({{ count($cart) }} sản phẩm)</span>
                </label>

                {{-- Nút xóa toàn bộ --}}
                <form action="{{ route('cart.clear') }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?')"
                            class="text-xs text-rose-500 hover:text-rose-700 font-bold flex items-center gap-1 hover:underline cursor-pointer">
                        <span>🗑️</span> Xóa tất cả
                    </button>
                </form>
            </div>

            {{-- Item Cards --}}
            @foreach($cart as $id => $item)
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-4 sm:p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center hover:shadow-card-hover transition-all duration-300">
                
                {{-- Left: Checkbox + Image --}}
                <div class="flex items-center gap-3 flex-shrink-0">
                    <input type="checkbox" 
                           class="cart-item-checkbox w-5 h-5 rounded text-blue-600 focus:ring-blue-500 border-slate-300 dark:border-slate-700 dark:bg-slate-800 cursor-pointer" 
                           data-id="{{ $id }}"
                           data-name="{{ $item['name'] }}"
                           data-quantity="{{ $item['quantity'] }}"
                           data-total="{{ $item['price'] * $item['quantity'] }}"
                           onchange="updateSummary()"
                           checked>

                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden border border-slate-100 dark:border-slate-700 p-2">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}" 
                                 alt="{{ $item['name'] }}" 
                                 class="w-full h-full object-contain hover:scale-110 transition duration-300">
                        @else
                            <span class="text-3xl">📱</span>
                        @endif
                    </div>
                </div>

                {{-- Center: Thông tin chi tiết --}}
                <div class="flex-1 min-w-0 space-y-1">
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm leading-snug hover:text-blue-600 dark:hover:text-blue-400 transition truncate">
                        {{ $item['name'] }}
                    </h3>

                    <div class="flex flex-wrap gap-1.5 pt-0.5">
                        @if(!empty($item['category']))
                            <span class="inline-flex items-center gap-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-slate-200 dark:border-slate-700">
                                📁 {{ $item['category'] }}
                            </span>
                        @endif
                        @if(!empty($item['variant_label']))
                            <span class="inline-flex items-center gap-1 bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 text-[10px] font-bold px-2.5 py-0.5 rounded-full border border-blue-100 dark:border-blue-900">
                                ⚙️ {{ $item['variant_label'] }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-baseline gap-2 pt-1">
                        <span class="text-rose-600 dark:text-rose-400 font-black text-sm">
                            {{ number_format($item['price']) }} đ
                        </span>
                        <span class="text-xs text-slate-400 dark:text-slate-500">/ chiếc</span>
                    </div>
                </div>

                {{-- Right: Quantity & Actions --}}
                <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                    
                    {{-- Quantity Selector --}}
                    <form action="{{ route('cart.update', $id) }}" method="POST" class="flex items-center" id="form-{{ $id }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden shadow-inner">
                            <button type="button" 
                                    onclick="changeQty('{{ $id }}', -1)"
                                    class="w-8 h-8 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs transition active:scale-90 cursor-pointer">−</button>
                            <input type="number" name="quantity" 
                                   value="{{ $item['quantity'] }}" 
                                   min="1" max="99"
                                   class="w-10 h-8 text-center text-xs font-black bg-transparent border-0 focus:outline-none focus:ring-0 p-0 text-slate-900 dark:text-white"
                                   id="qty-{{ $id }}" readonly>
                            <button type="button" 
                                    onclick="changeQty('{{ $id }}', 1)"
                                    class="w-8 h-8 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-black text-xs transition active:scale-90 cursor-pointer">+</button>
                        </div>
                    </form>

                    {{-- Thành tiền riêng của dòng --}}
                    <div class="text-right hidden md:block min-w-[100px]">
                        <span class="text-xs font-black text-slate-900 dark:text-white block">
                            {{ number_format($item['price'] * $item['quantity']) }} đ
                        </span>
                    </div>

                    {{-- Nút xóa --}}
                    <form action="{{ route('cart.remove', $id) }}" method="POST" class="flex-shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')"
                                title="Xóa khỏi giỏ"
                                class="w-8 h-8 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-rose-50 dark:hover:bg-rose-950/60 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 transition text-xs font-black cursor-pointer">
                            ✕
                        </button>
                    </form>
                </div>

            </div>
            @endforeach

            <!-- Bảo đảm dịch vụ -->
            <div class="p-4 rounded-2xl bg-blue-50/60 dark:bg-blue-950/40 border border-blue-100 dark:border-blue-900/50 flex items-center gap-3 text-xs text-blue-900 dark:text-blue-300">
                <span class="text-xl">🎁</span>
                <div>
                    <span class="font-bold">Ưu đãi độc quyền:</span> Miễn phí giao hàng hỏa tốc toàn quốc & Giảm thêm 5% khi thanh toán qua mã QR PhoneStore.
                </div>
            </div>
        </div>

        {{-- Tóm tắt đơn hàng (Cột phải: 4 cột Sticky) --}}
        <div class="lg:col-span-4 sticky top-24 space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 space-y-5 transition-colors duration-300">
                <h2 class="font-extrabold text-slate-900 dark:text-white text-base border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <span>📋</span> Tóm Tắt Đơn Hàng
                </h2>

                <div class="space-y-2.5 text-xs" id="summary-items-list">
                    {{-- Render JS --}}
                </div>

                <div class="border-t border-dashed border-slate-200 dark:border-slate-800 pt-4 space-y-2">
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-500 dark:text-slate-400">Phí giao hàng:</span>
                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded-full border border-transparent dark:border-emerald-800">Miễn phí 100%</span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold text-slate-500 dark:text-slate-400">Bảo hiểm vận chuyển:</span>
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Miễn phí</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-3 border-t border-slate-100 dark:border-slate-800">
                        <span class="text-sm font-black text-slate-900 dark:text-white">Tổng thanh toán:</span>
                        <span class="text-2xl font-black text-transparent bg-clip-text bg-gradient-to-r from-red-600 to-rose-600 dark:from-rose-400 dark:to-rose-500" id="summary-total-price">0 đ</span>
                    </div>
                </div>

                {{-- Nút thanh toán --}}
                <button type="button"
                        id="checkout-btn"
                        onclick="processCheckout()"
                        class="w-full py-4 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-rose-600/25 transition duration-200 active:scale-[0.99] flex items-center justify-center gap-2 cursor-pointer">
                    <span>Tiến Hành Đặt Hàng</span>
                </button>

                <div class="pt-2 text-center">
                    <p class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center justify-center gap-1">
                        <span>🔒</span> Bảo mật thanh toán SSL 256-bit chuẩn PCI-DSS
                    </p>
                </div>
            </div>
        </div>

    </div>

    @else
    {{-- Giỏ hàng rỗng (Empty State Flagship) --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-12 text-center max-w-xl mx-auto space-y-5 my-8 transition-colors duration-300">
        <div class="w-24 h-24 mx-auto bg-blue-50 dark:bg-blue-950/60 rounded-full flex items-center justify-center text-4xl shadow-inner animate-bounce">
            🛍️
        </div>
        <div class="space-y-2">
            <h2 class="text-xl font-black text-slate-900 dark:text-white">Giỏ hàng của bạn đang trống!</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                Hãy khám phá hàng trăm dòng điện thoại cao cấp với mức giá ưu đãi nhất tại PhoneStore ngay hôm nay.
            </p>
        </div>
        <a href="{{ route('home') }}"
           class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs rounded-full shadow-lg shadow-blue-500/25 transition transform hover:-translate-y-0.5 cursor-pointer">
            <span>⚡ Khám Phá Sản Phẩm Ngay</span>
        </a>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
    function changeQty(id, delta) {
        const input = document.getElementById('qty-' + id);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > 99) val = 99;
        input.value = val;
        document.getElementById('form-' + id).submit();
    }

    function toggleSelectAll(masterCheckbox) {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
        updateSummary();
    }

    function updateSummary() {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox');
        const selectAll = document.getElementById('selectAllCheckbox');
        const summaryList = document.getElementById('summary-items-list');
        const totalPriceEl = document.getElementById('summary-total-price');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        let total = 0;
        let htmlContent = '';
        let checkedCount = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkedCount++;
                const name = checkbox.dataset.name;
                const qty = checkbox.dataset.quantity;
                const itemTotal = parseFloat(checkbox.dataset.total);
                
                total += itemTotal;
                
                htmlContent += `
                    <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                        <span class="truncate max-w-[170px] font-medium">${name} <span class="text-slate-400 dark:text-slate-500 font-normal">x${qty}</span></span>
                        <span class="font-bold text-slate-800 dark:text-slate-100">${new Intl.NumberFormat('vi-VN').format(itemTotal)} đ</span>
                    </div>
                `;
            }
        });

        if (selectAll) {
            selectAll.checked = (checkedCount === checkboxes.length && checkboxes.length > 0);
        }

        if (checkedCount === 0) {
            htmlContent = `<p class="text-xs text-slate-400 italic text-center py-2">Vui lòng chọn sản phẩm để thanh toán</p>`;
            if (checkoutBtn) {
                checkoutBtn.disabled = true;
                checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        } else {
            if (checkoutBtn) {
                checkoutBtn.disabled = false;
                checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }

        if (summaryList) summaryList.innerHTML = htmlContent;
        if (totalPriceEl) totalPriceEl.innerText = new Intl.NumberFormat('vi-VN').format(total) + ' đ';
    }

    function processCheckout() {
        const checkboxes = document.querySelectorAll('.cart-item-checkbox:checked');
        if (checkboxes.length === 0) {
            alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
            return;
        }

        const selectedIds = Array.from(checkboxes).map(cb => cb.dataset.id).join(',');
        window.location.href = `{{ route('checkout.index') }}?selected_ids=${selectedIds}`;
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateSummary();
    });
</script>
@endpush


