{{-- resources/views/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Category')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .category-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .category-title {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #334155;
        }

        .form-label {
            font-weight: 500;
            color: #475569;
        }

        .form-control {
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            padding: 0.75rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-submit {
            background: #6366f1;
            color: white;
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
            border: none;
        }

        .btn-submit:hover {
            background: #4f46e5;
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('content')
<div class="container py-5">
    <div class="category-card">
        <h2 class="category-title">Create New Category</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       class="form-control" 
                       placeholder="Enter category name"
                       value="{{ old('name') }}"
                       required>
            </div>

            {{-- Submit Button --}}
            <div class="text-center">
                <button type="submit" class="btn-submit">
                    Save Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
