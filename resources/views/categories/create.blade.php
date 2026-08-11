@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        <div class="col-lg-12 mb-3">

            <div class="float-left">
                <h2>Create New Category</h2>
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

    <form
        action="{{ route('categories.store') }}"
        method="POST"
    >

        @csrf

        <div class="form-group">

            <strong>Name:</strong>

            <input
                type="text"
                name="name"
                class="form-control"
                placeholder="Name"
            >

        </div>

        <button
            type="submit"
            class="btn btn-primary"
        >
            Submit
        </button>

    </form>

</div>

@endsection