@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">{{ $product->name }}</h1>
        <div>
            @if(auth()->check() && (auth()->user()->is_admin ?? false))
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $product) }}">Edit</a>
                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</button>
                </form>
            @endif
            <a class="btn btn-secondary" href="{{ route('products.index') }}">Back to list</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if($product->image)
                <div class="mb-3"><img src="{{ asset('storage/' . $product->image) }}" alt="" style="max-width:300px;max-height:300px"></div>
            @endif
            <h5 class="card-title">{{ $product->brand }} - {{ $product->model }}</h5>
            <p class="text-muted">Category: {{ optional($product->category)->name }}</p>
            <p><strong>Price:</strong> {{ $product->price ? number_format($product->price, 0, ',', '.') . ' ₫' : 'Contact' }}</p>
            <hr>
            <p>{{ $product->description }}</p>
            <h6>Specs</h6>
            <pre>{{ json_encode($product->specs, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
@endsection
