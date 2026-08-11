@extends('layout')

@section('content')
<section class="hero-banner position-relative overflow-hidden bg-primary text-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-slide active" id="heroSlide1">
                    <div class="hero-slide-content">
                        <span class="badge bg-white text-primary mb-3">Mới ra mắt</span>
                        <h1 class="display-5 fw-bold">iPhone 17 Pro Max</h1>
                        <p class="lead opacity-85">Siêu phẩm iPhone 2026 với chip A20, camera Pro và thiết kế hoàn toàn mới.</p>
                        <div class="d-flex gap-3 mt-4">
                            <a href="{{ route('products.show', ['brand' => 'apple', 'product' => 'iphone-17-pro-max']) }}" class="btn btn-light btn-lg">Mua ngay</a>
                            <a href="{{ route('products.show', ['brand' => 'apple', 'product' => 'iphone-17-pro-max']) }}" class="btn btn-outline-light btn-lg">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://via.placeholder.com/760x620?text=iPhone+17+Pro+Max" class="img-fluid rounded-5 shadow-lg" alt="iPhone 17 Pro Max">
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Danh mục hãng</h2>
        <p class="text-muted">Chọn thương hiệu điện thoại bạn yêu thích</p>
    </div>
    <div class="row g-4">
        @foreach([['Apple','apple','🍎'], ['Samsung','samsung','📱'], ['Xiaomi','xiaomi','🔥'], ['OPPO','oppo','💚'], ['vivo','vivo','💙'], ['realme','realme','⚡'], ['Pixel','pixel','🟦'], ['ASUS','asus','🎮']] as $brand)
            <div class="col-6 col-md-3">
                <a href="{{ route('brand.show', $brand[1]) }}" class="brand-category-card p-4 text-center text-decoration-none text-dark d-flex flex-column align-items-center justify-content-center h-100">
                    <div class="category-badge bg-light mb-3">{{ $brand[2] }}</div>
                    <h5 class="mb-1">{{ $brand[0] }}</h5>
                    <p class="text-muted small">Điện thoại {{ $brand[0] }} cao cấp</p>
                </a>
            </div>
        @endforeach
    </div>
</section>

<section class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold">Sản phẩm nổi bật</h2>
            <p class="text-muted mb-0">Bộ sưu tập điện thoại đang được săn đón.</p>
        </div>
        <a href="/products" class="text-decoration-none">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="row g-4">
        @foreach($featured as $product)
            <div class="col-12 col-md-6 col-xl-3">
                @include('components.product-card', ['product' => $product])
            </div>
        @endforeach
    </div>
</section>

<section class="container py-5 bg-white rounded-4 shadow-sm">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <h3 class="fw-bold">Chọn điện thoại tốt nhất với giá ưu đãi</h3>
            <p class="text-muted">Tận hưởng trả góp 0%, giao hàng nhanh, bảo hành chính hãng.</p>
            <div class="d-flex gap-3 flex-wrap">
                <span class="badge bg-primary">Trả góp 0%</span>
                <span class="badge bg-success">Bảo hành 12 tháng</span>
                <span class="badge bg-dark">Giao hàng nhanh</span>
            </div>
        </div>
        <div class="col-lg-6 text-center">
            <img src="https://via.placeholder.com/700x420?text=PhoneStore+Quality" class="img-fluid rounded-4 shadow" alt="PhoneStore" />
        </div>
    </div>
</section>
@endsection
