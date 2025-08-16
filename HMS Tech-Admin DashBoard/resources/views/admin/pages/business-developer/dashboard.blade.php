@extends('admin.layouts.main')

@section('content')

@if(Auth::user()->role === 'business developer')
<div class="container-fluid py-4">

    {{-- 🔹 Welcome --}}
    <h3 class="fw-bold mb-4 text-primary">
        <i class="bi bi-briefcase-fill me-2"></i> Business Developer Dashboard
    </h3>

    {{-- 🔹 Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Projects</h6>
                    <h4 class="fw-bold">{{ $projects->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Commission</h6>
                    <h4 class="fw-bold text-success">
                        ${{ number_format($projects->sum('commission_amount'), 2) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Payments Received</h6>
                    {{-- <h4 class="fw-bold text-primary">
                        ${{ number_format($salary->where('is_paid', true)->sum('amount'), 2) }}
                    </h4> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Your Projects --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-kanban me-2"></i> Your Projects & Commission
        </div>
        <div class="card-body">
            @forelse($projects as $project)
                <div class="p-3 mb-3 rounded border bg-light">
                    <h5 class="fw-bold mb-1">{{ $project->title }}</h5>
                    <p class="mb-1 text-muted">{{ $project->description }}</p>
                    <small class="d-block">
                        <strong>Project Price:</strong> ${{ number_format($project->price, 2) }}
                    </small>
                    <small class="d-block">
                        <strong>Your Commission %:</strong> {{ $project->commission_percentage }}%
                    </small>
                    <small class="d-block text-success">
                        <strong>Commission Amount:</strong> ${{ number_format($project->commission_amount, 2) }}
                    </small>
                    <small class="d-block">
                        <strong>Status:</strong> {{ ucfirst($project->status) }}
                    </small>
                </div>
            @empty
                <p class="text-muted">No projects assigned to you yet.</p>
            @endforelse
        </div>
    </div>

    {{-- 🔹 Salary / Payments --}}
    {{-- <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-cash-coin me-2"></i> Salary / Payments from Admin
        </div>
        <div class="card-body">
            @forelse($salary as $s)
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <div>
                        <strong class="text-primary">
                            {{ \Carbon\Carbon::parse($s->salary_date)->format('M d, Y') }}
                        </strong>
                        <span class="mx-2">|</span>
                        <span class="fw-bold text-success">
                            ${{ number_format($s->amount, 2) }}
                        </span>
                        <span class="mx-2">|</span>
                        <span class="text-muted">{{ ucfirst($s->payment_method) }}</span>
                    </div>
                    <div>
                        @if($s->is_paid)
                            <span class="badge bg-success px-3 py-2">Paid</span>
                        @else
                            <span class="badge bg-danger px-3 py-2">Unpaid</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">No salary records available.</p>
            @endforelse
        </div>
    </div> --}}

    {{-- 🔹 Attendance --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark fw-semibold">
            <i class="bi bi-calendar-check me-2"></i> Attendance
        </div>
        <div class="card-body">
            @forelse($attendance as $att)
                <div class="border-bottom py-2">
                    {{ $att->date }} -
                    <span class="{{ $att->status === 'present' ? 'text-success' : ($att->status === 'leave' ? 'text-warning' : 'text-danger') }}">
                        {{ ucfirst($att->status) }}
                    </span>
                </div>
            @empty
                <p class="text-muted">No attendance records found.</p>
            @endforelse
        </div>
    </div>

</div>
@endif

@endsection
