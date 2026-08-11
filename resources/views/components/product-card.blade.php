<div class="product-card card overflow-hidden border-0 shadow-sm h-100">
    <div class="position-relative overflow-hidden bg-white product-image-wrap">
        <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/480x480?text=Phone' }}" class="card-img-top product-image" alt="{{ $product->name }}">
        @if($product->badge)
            <span class="badge bg-danger position-absolute top-0 start-0 m-3">{{ $product->badge }}</span>
        @endif
    </div>
    <div class="card-body d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="card-title mb-1">{{ $product->name }}</h6>
            <span class="text-warning"><i class="fa-solid fa-star"></i> {{ number_format($product->rating, 1) }}</span>
        </div>
        <p class="text-muted small mb-2">{{ $product->brand->name ?? $product->brand }}</p>
        <div class="d-flex gap-2 flex-wrap mb-3">
            <span class="badge bg-light text-dark">{{ $product->ram }}</span>
            <span class="badge bg-light text-dark">{{ $product->storage }}</span>
        </div>
        <div class="mb-3">
            @if($product->sale_price)
                <div class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }} ₫</div>
                <div class="fs-5 fw-bold text-danger">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</div>
            @else
                <div class="fs-5 fw-bold text-dark">{{ number_format($product->price, 0, ',', '.') }} ₫</div>
            @endif
        </div>
        <div class="mt-auto d-flex gap-2 flex-wrap">
            <button class="btn btn-outline-primary flex-fill add-to-cart-btn" data-id="{{ $product->id }}">Thêm vào giỏ</button>
            <a href="{{ route('products.show', ['brand' => $product->brand_slug, 'product' => $product->slug]) }}" class="btn btn-primary flex-fill">Mua ngay</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-secondary w-100">Sửa</a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="w-100">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-100" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</button>
                    </form>
                @endif
            @endauth
        </div>
    </div>
</div>
