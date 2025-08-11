@extends('layouts.app')

@section('title', 'Budgets')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f8fafc;
    }

    .budget-card, .header-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 2rem;
        margin-bottom: 1.5rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .table th {
        background-color: #f1f5f9;
        font-weight: 600;
        color: #475569;
    }

    .btn {
        border-radius: 12px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #6366f1;
        color: white;
    }

    .btn-primary:hover {
        background: #4f46e5;
    }

    .btn-warning {
        background: #facc15;
        color: #1e293b;
    }

    .btn-warning:hover {
        background: #eab308;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Header card --}}
    <div class="header-card d-flex justify-content-between align-items-center">
        <h2 class="page-title">Budgets</h2>
        <a href="{{ route('budgets.create') }}" class="btn btn-primary">Add New Budget</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="budget-card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($budgets as $budget)
                    <tr>
                        <td>{{ $budget->id }}</td>
                        <td>Rs.{{ number_format($budget->amount, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($budget->month)->format('F Y') }}</td>
                        <td>
                            <a href="{{ route('budgets.edit', $budget->id) }}" class="btn btn-warning">Edit</a>
                            <form action="{{ route('budgets.destroy', $budget->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this budget?')">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No budgets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
