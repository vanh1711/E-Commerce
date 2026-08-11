@extends('layout')

@section('content')
<section class="container py-5">
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card rounded-4 shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-4">Thông tin giao hàng</h2>
                <form>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" placeholder="Nguyễn Văn A">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" placeholder="0912 345 678">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" placeholder="Số nhà, đường, quận/huyện">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tỉnh/Thành phố</label>
                            <input type="text" class="form-control" placeholder="Hà Nội">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phương thức thanh toán</label>
                            <select class="form-select">
                                <option>Thanh toán khi nhận hàng</option>
                                <option>Chuyển khoản ngân hàng</option>
                                <option>VNPay / Momo</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card rounded-4 shadow-sm p-4">
                <h2 class="fw-bold mb-4">Lưu ý đơn hàng</h2>
                <p class="text-muted">Kiểm tra kỹ sản phẩm và địa chỉ giao hàng trước khi xác nhận.</p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Giao hàng nhanh trong 24h</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Bảo hành chính hãng 12 tháng</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Hỗ trợ đổi trả trong 7 ngày</li>
                </ul>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card rounded-4 shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-4">Chi tiết đơn hàng</h2>
                @if($items->isEmpty())
                    <p class="text-muted">Giỏ hàng của bạn hiện đang trống.</p>
                @else
                    @foreach($items as $item)
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $item->image ? asset('storage/'.$item->image) : 'https://via.placeholder.com/80x80?text=Phone' }}" alt="{{ $item->name }}" class="rounded-4" width="80">
                            <div>
                                <div class="fw-semibold">{{ $item->name }}</div>
                                <div class="text-muted small">{{ $item->quantity }} x {{ number_format($item->sale_price ?: $item->price, 0, ',', '.') }} ₫</div>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <span>{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Phí vận chuyển</span>
                        <span>{{ $shipping ? number_format($shipping, 0, ',', '.') . ' ₫' : 'Miễn phí' }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 mb-4">
                        <span>Tổng</span>
                        <span>{{ number_format($total, 0, ',', '.') }} ₫</span>
                    </div>
                    <button class="btn btn-primary w-100">Xác nhận đơn hàng</button>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection