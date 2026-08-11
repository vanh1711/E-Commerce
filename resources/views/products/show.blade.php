@extends('layout')

@section('content')
<section class="container py-5">
    <div class="row gy-5">
        <div class="col-lg-6">
            <div class="card rounded-4 shadow-sm overflow-hidden mb-4">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/720x720?text=Phone' }}" class="img-fluid w-100" alt="{{ $product->name }}">
            </div>
            <div class="row g-3">
                <div class="col-4">
                    <div class="card rounded-4 border p-3 text-center">
                        <small class="text-muted">Camera</small>
                        <strong>{{ $product->specs['rear_camera'] ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card rounded-4 border p-3 text-center">
                        <small class="text-muted">Pin</small>
                        <strong>{{ $product->specs['battery'] ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card rounded-4 border p-3 text-center">
                        <small class="text-muted">Sạc nhanh</small>
                        <strong>{{ $product->specs['fast_charging'] ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="mb-4">
                <span class="badge bg-primary mb-3">{{ $product->badge ?? 'Nổi bật' }}</span>
                <h1 class="fw-bold">{{ $product->name }}</h1>
                <p class="text-muted">{{ $product->brand->name ?? $product->brand }} · {{ $product->model }}</p>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="text-warning fs-5"><i class="fa-solid fa-star"></i> {{ number_format($product->rating, 1) }}</span>
                    <span class="text-muted">({{ $product->stock }} sản phẩm có sẵn)</span>
                </div>
                <div class="mb-4">
                    @if($product->sale_price)
                        <div class="text-muted text-decoration-line-through fs-6">{{ number_format($product->price, 0, ',', '.') }} ₫</div>
                        <div class="fs-2 fw-bold text-danger">{{ number_format($product->sale_price, 0, ',', '.') }} ₫</div>
                    @else
                        <div class="fs-2 fw-bold text-dark">{{ number_format($product->price, 0, ',', '.') }} ₫</div>
                    @endif
                    <div class="text-success small">Trả góp từ {{ number_format(($product->sale_price ?: $product->price) / 12, 0, ',', '.') }} ₫/tháng</div>
                </div>
                <div class="row gy-3 mb-4">
                    <div class="col-sm-6">
                        <label class="form-label">Màu sắc</label>
                        <select class="form-select">
                            <option>Đen</option>
                            <option>Bạc</option>
                            <option>Xanh</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Dung lượng</label>
                        <select class="form-select">
                            <option>{{ $product->storage }}</option>
                            <option>512GB</option>
                            <option>1TB</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-3 flex-column flex-sm-row mb-4">
                    <button class="btn btn-primary btn-lg add-to-cart-btn" data-id="{{ $product->id }}">Thêm vào giỏ</button>
                    <button class="btn btn-outline-primary btn-lg">Mua ngay</button>
                    <button class="btn btn-outline-secondary btn-lg"><i class="fa-regular fa-heart me-2"></i>Yêu thích</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row gy-4 mt-5">
        <div class="col-lg-8">
            <div class="card rounded-4 shadow-sm p-4">
                <h3 class="fw-bold mb-3">Thông số kỹ thuật</h3>
                <div class="row g-3">
                    @foreach([
                        ['Màn hình', $product->specs['display'] ?? 'N/A'],
                        ['Kích thước', $product->specs['dimensions'] ?? 'N/A'],
                        ['Tần số quét', $product->specs['refresh_rate'] ?? 'N/A'],
                        ['Chip xử lý', $product->specs['chip'] ?? 'N/A'],
                        ['RAM', $product->ram ?? 'N/A'],
                        ['Bộ nhớ trong', $product->storage ?? 'N/A'],
                        ['Camera trước', $product->specs['front_camera'] ?? 'N/A'],
                        ['Camera sau', $product->specs['rear_camera'] ?? 'N/A'],
                        ['Pin', $product->specs['battery'] ?? 'N/A'],
                        ['Sạc nhanh', $product->specs['fast_charging'] ?? 'N/A'],
                        ['Hệ điều hành', $product->specs['os'] ?? 'N/A'],
                        ['Kết nối', $product->specs['connectivity'] ?? 'N/A'],
                        ['Chống nước', $product->specs['water_resistance'] ?? 'N/A'],
                    ] as $spec)
                        <div class="col-6">
                            <div class="border-bottom pb-3 mb-3">
                                <div class="text-muted small">{{ $spec[0] }}</div>
                                <div class="fw-semibold">{{ $spec[1] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card rounded-4 shadow-sm p-4">
                <h5 class="fw-bold mb-3">Sản phẩm liên quan</h5>
                <div class="list-group">
                    @foreach($product->brand->products()->where('id', '!=', $product->id)->take(3)->get() as $related)
                        <a href="{{ route('products.show', ['brand' => $related->brand_slug, 'product' => $related->slug]) }}" class="list-group-item list-group-item-action rounded-4 mb-3">
                            <div class="d-flex gap-3 align-items-center">
                                <img src="{{ $related->image ? asset('storage/'.$related->image) : 'https://via.placeholder.com/80x80?text=Phone' }}" class="rounded-4" width="80" alt="{{ $related->name }}">
                                <div>
                                    <div class="fw-semibold">{{ $related->name }}</div>
                                    <div class="text-muted small">{{ number_format($related->sale_price ?: $related->price, 0, ',', '.') }} ₫</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
