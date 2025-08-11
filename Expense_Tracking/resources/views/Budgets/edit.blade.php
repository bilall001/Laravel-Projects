@extends('layouts.app')

@section('title', 'Edit Budget')

@push('styles')
    {{-- Reuse the same styles as create --}}
@endpush

@section('content')
<div class="container py-5">
    <div class="budget-card">
        <h2 class="mb-4 fw-bold text-center">Edit Budget</h2>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('budgets.update', $budget->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Amount --}}
            <div class="mb-4">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" name="amount" step="0.01" class="form-control" 
                       value="{{ old('amount', $budget->amount) }}" required>
            </div>

            {{-- Month --}}
            <div class="mb-4">
                <label for="month" class="form-label">Month</label>
                <input type="month" name="month" class="form-control" 
                       value="{{ old('month', \Carbon\Carbon::parse($budget->month)->format('Y-m')) }}" required>
            </div>

            <div class="text-center">
                <button type="submit" class="btn-submit">Update Budget</button>
            </div>
        </form>
    </div>
</div>
@endsection
