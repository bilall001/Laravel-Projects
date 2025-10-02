@extends('layouts.app')

@section('title', 'Edit Expense')

@push('styles')
<style>
    .form-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.06);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .header-card {
        background: linear-gradient(90deg, #4f46e5, #6366f1);
        color: #fff;
        padding: 1.5rem 2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .header-card h2 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
    }
    .alert-custom {
        background: #fff8e6;
        border: 1px solid #ffeb99;
        padding: 1.25rem;
        border-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .alert-custom strong {
        color: #a16207;
    }
    .form-control {
    background-color: #fff;   /* Same white background */
    border: 1px solid #ced4da; /* Bootstrap default border */
    border-radius: 0.375rem;  /* Same rounded corners */
    color: #212529;           /* Text color */
    height: calc(2.25rem + 2px); /* Match height */
}
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Header card --}}
  <div class="header-card">
        <h2>Edit Expense</h2>
        <a href="{{ route('expenses.index') }}" class="btn btn-light">Back</a>
    </div>

    {{-- Show warning if no budget --}}
    @if(!$budget)
        <div class="alert-custom">
            <div>
                <strong>No budget found for {{ now()->format('F Y') }}.</strong> Please create one first.
            </div>
            <a href="{{ route('budgets.create') }}" class="btn btn-warning">Create Budget</a>
        </div>
    @endif


    {{-- Form card --}}
    <div class="form-card">
        <form action="{{ route('expenses.update', $expense->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $expense->title) }}" required>
                @error('title') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Amount</label>
                <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $expense->amount) }}" required>
                @error('amount') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

           @if($budget)
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        Budget ({{ \Carbon\Carbon::parse($budget->month)->format('F Y') }})
                    </label>
                    <input type="text" class="form-control" value="{{ number_format($budget->amount, 2) }}" disabled>
                    <input type="hidden" name="budget_id" value="{{ $budget->id }}">
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label">Expense Date</label>
                <input type="date" name="expense_date" class="form-control" value="{{ old('expense_date', $expense->expense_date) }}" required>
                @error('expense_date') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $expense->description) }}</textarea>
                @error('description') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Expense</button>
        </form>
    </div>
</div>
@endsection
