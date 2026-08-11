@extends('layout')

@section('content')
<section class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3 mb-4">
        <div>
            <span class="badge bg-primary mb-3">{{ $brand->name }}</span>
            <h1 class="fw-bold">{{ $brand->name }} Collection</h1>
            <p class="text-muted">Các mẫu điện thoại nổi bật từ {{ $brand->name }} với thiết kế hiện đại và cấu hình mạnh mẽ.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <img src="{{ $brand->logo ?? 'https://via.placeholder.com/120x120?text='.urlencode($brand->name) }}" class="rounded-4" width="80" alt="{{ $brand->name }}">
        </div>
    </div>

    <div class="row g-4">
        @foreach($products as $product)
            <div class="col-12 col-md-6 col-xl-3">
                @include('components.product-card', ['product' => $product])
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</section>
@endsection
