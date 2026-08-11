@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Edit Product</h1>
        <a class="btn btn-secondary" href="{{ route('products.index') }}">Back to list</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @if($product->category_id == $c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand', $product->brand) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control" value="{{ old('model', $product->model) }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $product->price) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $product->description) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Specs (JSON or leave empty)</label>
            <textarea name="specs" class="form-control">{{ old('specs', json_encode($product->specs)) }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            @if($product->image)
                <div class="mb-2"><img src="{{ asset('storage/' . $product->image) }}" alt="" style="max-width:150px;max-height:150px"></div>
            @endif
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@endsection
