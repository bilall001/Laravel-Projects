@extends('layouts.app')

@section('title', 'Create Budget')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8fafc;
    }

    .budget-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        max-width: 600px;
        margin: 0 auto;
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
        border: none;
    }

    .btn-submit:hover {
        background: #4f46e5;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="budget-card">
        <h2 class="mb-4 fw-bold text-center">Create Budget</h2>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('budgets.store') }}" method="POST">
            @csrf

            {{-- Amount --}}
            <div class="mb-4">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control" required>
            </div>

            {{-- Month --}}
            <div class="mb-4">
                <label for="month" class="form-label">Month</label>
                <input type="month" name="month" class="form-control" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn-submit">Save Budget</button>
            </div>
        </form>
    </div>
</div>
@endsection
