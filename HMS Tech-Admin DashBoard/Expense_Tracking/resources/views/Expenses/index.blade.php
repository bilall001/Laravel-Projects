@extends('layouts.app')

@section('title', 'Expenses')

@push('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
        }

        .header-card,
        .expense-card {
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

        .table th,
        .table td {
            padding: 0.75rem;
            white-space: nowrap;
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
            <h2 class="page-title">Expenses</h2>
            <a href="{{ route('expenses.create') }}" class="btn btn-primary">Add New Expense</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="expense-card table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Expense Title</th>
                        <th>Expense Amount</th>
                        <th>Expense Category</th>
                        <th>Month Budget</th>
                        <th>Budget Month</th>
                        <th>Expense Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->id }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>Rs.{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->category->name ?? 'N/A' }}</td>
                            <td>Rs.{{ $expense->budget->amount ?? 'N/A' }}</td>
                            <td>
                                {{ $expense->budget ? \Carbon\Carbon::parse($expense->budget->month)->format('F Y') : 'N/A' }}
                            </td>

                            <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('F d, Y') }}</td>
                            <td>
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST"
                                    style="display:inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this expense?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No expenses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
