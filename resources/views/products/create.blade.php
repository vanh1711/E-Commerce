@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4">Create Product</h1>
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

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="category_id" class="form-select">
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @if(old('category_id', $selectedCategory) == $c->id) selected @endif>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Brand</label>
                <input type="text" name="brand" class="form-control" value="{{ old('brand') }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Model</label>
                <input type="text" name="model" class="form-control" value="{{ old('model') }}">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Specs (JSON or leave empty)</label>
            <textarea name="specs" class="form-control">{{ old('specs') }}</textarea>
            <div class="form-text">Example: {"ram":"4GB","color":"black"}</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Image</label>
            <input type="file" name="image" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection
