@extends('admin.layouts.main')

@section('content')

@if(Auth::user()->role === 'developer')
<div class="container-fluid py-4">

    {{-- 🔹 Welcome --}}
    <h3 class="fw-bold mb-4 text-primary">
        <i class="bi bi-speedometer2 me-2"></i> Developer Dashboard
    </h3>

    {{-- 🔹 Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Assigned Projects</h6>
                    <h4 class="fw-bold">{{ $projects->count() }}</h4>
                </div>
            </div>
        </div>
        {{-- <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Completed Projects</h6>
                    <h4 class="fw-bold text-success">
                        {{ $projects->where('status', 'completed')->count() + $completedSchedules->count() }}
                    </h4>
                </div>
            </div>
        </div> --}}
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Teams</h6>
                    <h4 class="fw-bold text-info">{{ $teams->count() }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Pending Salary</h6>
                    <h4 class="fw-bold text-danger">
                        ${{ number_format($salary->where('is_paid', false)->sum('amount'), 2) }}
                    </h4>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 Assigned Projects --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-kanban me-2"></i> Your Projects
        </div>
       <div class="card-body">
    <h5 class="mb-3">Active Projects</h5>

    @forelse($projects as $project)
        <div class="p-3 mb-3 rounded border position-relative {{ $project->type === 'team' ? 'bg-light' : 'bg-white' }}">
            <h5 class="fw-bold mb-1">
                {{ $project->title }}
                @if($project->type === 'team')
                    <span class="badge bg-info">Team</span>
                @endif
            </h5>
            <p class="mb-1 text-muted">{{ $project->description }}</p>
            <small class="d-block mb-1">
                <strong>Developer Deadline:</strong> {{ $project->developer_end_date }}
            </small>
            <small class="d-block mb-2">
                <strong>Status:</strong> {{ ucfirst($project->status) }}
            </small>

            {{-- <form action="{{ route('developer.projects.updateStatus', $project->id) }}" method="POST" class="mt-2">
                @csrf
                <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="pending" {{ $project->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ $project->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
                <button class="btn btn-primary btn-sm">
                    Update
                </button>
            </form> --}}
        </div>
    @empty
        <p class="text-muted">No active projects assigned to you.</p>
    @endforelse
</div>

    </div>

    {{-- 🔹 Completed Projects from Schedule --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white fw-semibold">
            <i class="bi bi-check2-circle me-2"></i> Completed Projects from Schedule
        </div>
        {{-- <div class="card-body">
            @forelse($completedSchedules as $schedule)
                <div class="p-3 mb-3 rounded border bg-light">
                    <h5 class="fw-bold mb-1">{{ $schedule->name ?? 'Untitled Project' }}</h5>
                    <p class="mb-1 text-muted">{{ $schedule->description ?? 'No description available' }}</p>
                    <small class="d-block mb-1">
                        <strong>Completed On:</strong>
                        {{ \Carbon\Carbon::parse($schedule->completed_at ?? $schedule->updated_at)->format('M d, Y') }}
                    </small>
                </div>
            @empty
                <p class="text-muted">No completed schedules found.</p>
            @endforelse
        </div> --}}
    </div>

    {{-- 🔹 Your Teams --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="bi bi-people-fill me-2"></i> Your Teams
        </div>
        <div class="card-body">
            @forelse($teams as $team)
                <div class="mb-3">
                    <strong>{{ $team->name }}</strong>
                    <div class="mt-1">
                        @foreach($team->users as $user)
                            <span class="badge bg-info text-dark">{{ $user->name }}</span>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="text-muted">You are not part of any team.</p>
            @endforelse
        </div>
    </div>


    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white fw-semibold d-flex align-items-center">
            <i class="bi bi-cash-coin me-2 fs-5"></i> Salary / Payments
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
                        <span class="text-muted">
                            {{ ucfirst($s->payment_method) }}
                        </span>
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
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-wallet2 fs-3 d-block mb-2"></i>
                    No salary records found.
                </div>
            @endforelse
        </div>
    </div>



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
                <p class="text-muted">No attendance records available.</p>
            @endforelse
        </div>
    </div>

</div>
@endif

@endsection
