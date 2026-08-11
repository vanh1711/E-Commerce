@extends('layout')

@section('content')
<section class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Giỏ hàng</h1>
            <p class="text-muted">Kiểm tra lại sản phẩm trước khi thanh toán.</p>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="card shadow-sm p-5 text-center">
            <h3 class="mb-3">Giỏ hàng trống</h3>
            <p class="text-muted">Thêm điện thoại vào giỏ để tiếp tục.</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="list-group">
                    @foreach($items as $item)
                        <div class="list-group-item rounded-4 shadow-sm mb-3 p-4">
                            <div class="row align-items-center g-3">
                                <div class="col-3 col-md-2">
                                    <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://via.placeholder.com/120x120?text=Phone' }}" class="img-fluid rounded-4" alt="{{ $item->name }}">
                                </div>
                                <div class="col-5 col-md-6">
                                    <h5 class="mb-1">{{ $item->name }}</h5>
                                    <p class="text-muted small mb-1">{{ $item->brand->name ?? $item->brand }} · {{ $item->ram }} / {{ $item->storage }}</p>
                                    <p class="text-danger fw-bold mb-0">{{ number_format($item->sale_price ?: $item->price, 0, ',', '.') }} ₫</p>
                                </div>
                                <div class="col-4 col-md-4 text-end">
                                    <div class="d-flex justify-content-end gap-2 mb-2 align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary cart-action-btn" data-action="decrease" data-id="{{ $item->id }}" data-price="{{ $item->sale_price ?: $item->price }}">-</button>
                                        <input id="quantity-{{ $item->id }}" type="text" readonly class="form-control form-control-sm text-center" value="{{ $item->quantity }}" style="width:60px;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary cart-action-btn" data-action="increase" data-id="{{ $item->id }}" data-price="{{ $item->sale_price ?: $item->price }}">+</button>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-danger btn-sm cart-remove-btn" data-id="{{ $item->id }}">Xóa</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card rounded-4 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">Tổng đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span>{{ number_format($items->sum(fn($item) => ($item->sale_price ?: $item->price) * $item->quantity), 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span>Phí vận chuyển</span>
                        <span>Miễn phí</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Thanh toán</span>
                        <span>{{ number_format($items->sum(fn($item) => ($item->sale_price ?: $item->price) * $item->quantity), 0, ',', '.') }} ₫</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Tiến hành thanh toán</a>
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
