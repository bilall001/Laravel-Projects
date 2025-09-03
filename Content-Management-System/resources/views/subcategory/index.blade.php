@extends('layouts.main')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white d-flex justify-content-between align-items-center" style="background-color: #001B2F">
            <h4 class="mb-0">Subcategories</h4>
            <a href="{{ route('subcategories.create') }}" class="btn btn-light btn-sm">Add subcategory</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subcategories as $subcategory)
                        <tr>
                            <td>{{ $subcategory->id }}</td>
                            <td>{{ $subcategory->category->name }}</td>
                            <td>{{ $subcategory->name }}</td>
                            <td>{{ $subcategory->slug }}</td>
                            <td>{{ $subcategory->description }}</td>
                            <td>
                                <a href="{{ route('subcategories.edit', $subcategory->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('subcategories.destroy', $subcategory->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this subcategory?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $subcategories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
