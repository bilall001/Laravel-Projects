@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #001B2F">
            <h4 class="mb-0">Add New Subcategory</h4>
            <a href="{{ route('subcategories.index') }}" class="btn btn-light btn-sm">Back</a>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('subcategories.store') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="category_id">Parent Category</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="name">Subcategory Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Enter subcategory name" value="{{ old('name') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="slug">Slug</label>
                    <input type="text" id="slug" name="slug" class="form-control" placeholder="Enter subcategory slug" value="{{ old('slug') }}">
                </div>

                <div class="form-group mb-3">
                    <label for="description">Description (Optional)</label>
                    <input type="text" id="description" name="description" class="form-control" placeholder="Enter subcategory description" value="{{ old('description') }}">
                </div>

                <button type="submit" class="btn btn-success">Save Subcategory</button>
            </form>
        </div>
    </div>
</div>
@endsection
