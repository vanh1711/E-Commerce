@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">
        <div class="col-lg-12 mb-3">

            <div class="float-left">
                <h2>Categories</h2>
            </div>

            <div class="float-right">
                <a
                    class="btn btn-success"
                    href="{{ route('categories.create') }}"
                >
                    Create New Category
                </a>
            </div>

        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            {{ $message }}
        </div>
    @endif

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th width="280px">Action</th>
            </tr>
        </thead>

        <tbody>

        @foreach ($categories as $category)

            <tr>

                <td>{{ $loop->iteration }}</td>

                <td>{{ $category->name }}</td>

                <td>

                    <a
                        class="btn btn-info"
                        href="{{ route('categories.show', $category->id) }}"
                    >
                        Show
                    </a>

                    <a
                        class="btn btn-primary"
                        href="{{ route('categories.edit', $category->id) }}"
                    >
                        Edit
                    </a>

                    <form
                        action="{{ route('categories.destroy', $category->id) }}"
                        method="POST"
                        style="display:inline"
                    >

                        @csrf

                        @method('DELETE')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Delete
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>

@endsection