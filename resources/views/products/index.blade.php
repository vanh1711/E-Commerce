@extends('layout')

@section('content')
<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <h1 class="fw-bold">Tất cả điện thoại</h1>
            <p class="text-muted">Tìm, lọc và chọn điện thoại phù hợp với nhu cầu của bạn.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('products.create') }}" class="btn btn-success">Thêm sản phẩm</a>
                    <a href="{{ route('categories.create') }}" class="btn btn-outline-secondary">Thêm danh mục</a>
                @endif
            @endauth
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Trang chủ</a>
            <a href="{{ route('cart.index') }}" class="btn btn-primary">Giỏ hàng</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-3">
            <div class="card rounded-4 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3">Bộ lọc</h5>
                <form method="GET" action="{{ route('products.index') }}">
                    <div class="mb-3">
                        <label class="form-label">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tên sản phẩm hoặc model">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thương hiệu</label>
                        <select name="brand" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->slug }}" @selected(request('brand') === $brand->slug)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-select">
                            <option value="">Tất cả</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug ?? $category->id }}" @selected(request('category') === ($category->slug ?? $category->id))>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-100">Áp dụng</button>
                </form>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-12 col-md-6 col-xl-4">
                        @include('components.product-card', ['product' => $product])
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card rounded-4 shadow-sm p-5 text-center">
                            <h3 class="fw-bold">Không có kết quả</h3>
                            <p class="text-muted">Thử lại với từ khóa khác hoặc chọn thương hiệu khác.</p>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">{{ $products->links() }}</div>
        </div>
    </div>
</section>
@endsection
