@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        <div class="col-lg-12 mb-3">

            <div class="float-left">
                <h2>Show Category</h2>
            </div>

            <div class="float-right">

                <a
                    class="btn btn-primary"
                    href="{{ route('categories.index') }}"
                >
                    Back
                </a>

            </div>

        </div>

    </div>

    <div class="row">


        <div class="col-12 mb-3 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0">{{ $category->name }}</h3>
                <p class="text-muted mb-0">Products in this category</p>
            </div>
            <div>
                <a href="{{ route('products.create', ['category_id' => $category->id]) }}" class="btn btn-success">Create Product</a>
            </div>
        </div>

        <div class="col-12">
            <div class="row g-3">
                @forelse($products as $p)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            @if($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top" alt="{{ $p->name }}">
                            @else
                                <img src="https://via.placeholder.com/600x400?text=No+Image" class="card-img-top" alt="No image">
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><a href="{{ route('products.show', ['brand' => $p->brand_slug, 'product' => $p->slug]) }}">{{ $p->name }}</a></h5>
                                <p class="text-muted mb-2">{{ $p->brand }}</p>
                                <p class="text-muted small">{{ \Illuminate\Support\Str::limit($p->description, 120) }}</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-danger">{{ $p->price ? number_format($p->price, 0, ',', '.') . ' ₫' : 'Contact' }}</div>
                                    <div>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('products.show', ['brand' => $p->brand_slug, 'product' => $p->slug]) }}">View</a>
                                        @if(auth()->check() && (auth()->user()->is_admin ?? false))
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $p) }}">Edit</a>
                                            <form action="{{ route('products.destroy', $p) }}" method="POST" style="display:inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete product?')">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">No products in this category.</div>
                @endforelse
            </div>

            <div class="mt-3">{{ $products->links() }}</div>
        </div>

    </div>

</div>

@endsection