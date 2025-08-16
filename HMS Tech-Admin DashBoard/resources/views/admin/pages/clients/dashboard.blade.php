@extends('admin.layouts.main')
@section('title')
Client DashBoard- HMS Tech  & Solutions
@endsection
@section('content')

@if(Auth::user()->role === 'client')
<div class="container-fluid py-4">

    {{-- Stats Section --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Projects</h6>
                    <h4 class="fw-bold">{{ $totalProjects ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Current Projects</h6>
                    <h4 class="fw-bold text-primary">{{ $currentProjects ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Completed Projects</h6>
                    <h4 class="fw-bold text-success">{{ $completedProjects ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Amount</h6>
                    <h4 class="fw-bold text-success">
                         ${{ number_format($totalAmountSpent ?? 0, 2) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Advance Paid</h6>
                    <h4 class="fw-bold text-danger">
                        ${{ number_format($amountspent ?? 0, 2) }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-4 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Remaining Amount</h6>
                    <h4 class="fw-bold text-warning">
                        ${{ number_format($remainingAmount ?? 0, 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

  {{-- Latest Project --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-kanban me-2"></i> Latest Project Assigned
    </div>
    <div class="card-body">
        @if(!empty($latestProject))
            <div class="p-3 rounded border bg-light">
                <h5 class="fw-bold mb-1">{{ $latestProject->title }}</h5>
                {{-- Optional: If you want to add description later, uncomment below --}}
                {{-- <p class="mb-1 text-muted">{{ $latestProject->description ?? 'No description available' }}</p> --}}
                <small class="d-block mb-1">
                    <strong>Status:</strong> {{ ucfirst($latestProject->status ?? 'N/A') }}
                </small>
                <small class="d-block mb-2">
                    <strong>Price:</strong> ${{ number_format($latestProject->price ?? 0, 2) }}
                </small>
            </div>
        @else
            <p class="text-muted">No project assigned yet.</p>
        @endif
    </div>
</div>

{{-- Previous Projects --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white fw-semibold">
        <i class="bi bi-archive-fill me-2"></i> Previous Projects
    </div>
    <div class="card-body">
        @forelse($previousProjects ?? [] as $project)
            <div class="p-3 mb-3 rounded border position-relative {{ $project->status === 'completed' ? 'bg-success bg-opacity-10' : 'bg-white' }}">
                <h6 class="fw-bold mb-1">{{ $project->title }}</h6>
                {{-- Optional: Uncomment to show description if you add that field --}}
                {{-- <p class="mb-1 text-muted">{{ $project->description ?? 'No description available' }}</p> --}}
                <small class="d-block mb-1">
                    <strong>Status:</strong> {{ ucfirst($project->status ?? 'N/A') }}
                </small>
                <small class="d-block">
                    <strong>Price:</strong> ${{ number_format($project->price ?? 0, 2) }}
                </small>
            </div>
        @empty
            <p class="text-muted">No previous projects available.</p>
        @endforelse
    </div>
</div>

</div>
@endif

@endsection
