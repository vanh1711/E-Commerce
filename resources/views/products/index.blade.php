@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Products</h1>
        <div>
            @auth
                <a class="btn btn-success" href="{{ route('products.create') }}">Add Product</a>
                <a class="btn btn-outline-primary ms-2" href="{{ route('categories.create') }}">Create Category</a>
            @endauth
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <aside class="col-md-3">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <div class="list-group">
                        <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action @if(!$selected) active @endif">All ({{ \App\Models\Product::count() }})</a>
                        @foreach($categories as $cat)
                            <a href="{{ route('categories.show', $cat) }}" class="list-group-item list-group-item-action @if($selected == $cat->id) active @endif">{{ $cat->name }} ({{ $cat->products_count }})</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <section class="col-md-9">
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
                                <h5 class="card-title"><a href="{{ route('products.show', $p) }}">{{ $p->name }}</a></h5>
                                <p class="text-muted mb-2">{{ $p->brand }} • {{ optional($p->category)->name }}</p>
                                <p class="text-muted small">{{ \Illuminate\Support\Str::limit($p->description, 120) }}</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <div class="fw-bold text-danger">{{ $p->price ? number_format($p->price, 0, ',', '.') . ' ₫' : 'Contact' }}</div>
                                    <div>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('products.show', $p) }}">View</a>
                                        @if(auth()->check() && (auth()->user()->is_admin ?? false))
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('products.edit', $p) }}">Edit</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">No products found.</div>
                @endforelse
            </div>

            <div class="mt-3">{{ $products->links() }}</div>
        </section>
    </div>
@endsection
