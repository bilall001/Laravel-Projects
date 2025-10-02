@extends('admin.layouts.main')

@section('title')
Client DashBoard - HMS Tech & Solutions
@endsection

@section('content')
@if(Auth::user()->role === 'client')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <div class="row">
                    <div class="col">
                        <h4 class="page-title">Analytics</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                    <div class="col-auto align-self-center">
                        <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                            <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                            <span class="" id="Select_date">Jan 11</span>
                            <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
    <div class="col-md-6 col-lg-4">
        <div class="card report-card">
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col">
                        <p class="text-dark mb-1 font-weight-semibold">💰 Total Amount</p>
                        <h3 class="my-2">${{ number_format($totalAmountSpent, 2) }}</h3>
                        <p class="mb-0 text-truncate text-muted">
                            <span class="text-success"><i class="mdi mdi-trending-up"></i></span> Total project price
                        </p>
                    </div>
                    <div class="col-auto align-self-center">
                        <div class="report-main-icon bg-light-alt">
                            <i data-feather="dollar-sign" class="align-self-center text-muted icon-md"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <div class="col-md-6 col-lg-4">
        <div class="card report-card">
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col">
                        <p class="text-dark mb-1 font-weight-semibold">💵 Paid Amount</p>
                        <h3 class="my-2">${{ number_format($amountspent, 2) }}</h3>
                        <p class="mb-0 text-truncate text-muted">
                            <span class="text-success"><i class="mdi mdi-trending-up"></i></span> Total paid
                        </p>
                    </div>
                    <div class="col-auto align-self-center">
                        <div class="report-main-icon bg-light-alt">
                            <i data-feather="credit-card" class="align-self-center text-muted icon-md"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 

    <div class="col-md-6 col-lg-4">
        <div class="card report-card">
            <div class="card-body">
                <div class="row d-flex justify-content-center">
                    <div class="col">
                        <p class="text-dark mb-1 font-weight-semibold">🔖 Remaining Amount</p>
                        <h3 class="my-2">${{ number_format($remainingAmount, 2) }}</h3>
                        <p class="mb-0 text-truncate text-muted">
                            <span class="text-danger"><i class="mdi mdi-trending-down"></i></span> Amount left
                        </p>
                    </div>
                    <div class="col-auto align-self-center">
                        <div class="report-main-icon bg-light-alt">
                            <i data-feather="pie-chart" class="align-self-center text-muted icon-md"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Stats Cards -->
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col">
                            <p class="text-dark mb-1 font-weight-semibold">📊 Total Projects</p>
                            <h3 class="my-2">{{ $totalProjects }}</h3>
                             <p class="mb-0 text-truncate text-muted">
                            <span class="text-success"><i class="mdi mdi-trending-up"></i></span> Total Projects
                        </p>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="report-main-icon bg-light-alt">
                                <i data-feather="users" class="align-self-center text-muted icon-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col">
                            <p class="text-dark mb-1 font-weight-semibold">🚧 Current Projects</p>
                            <h3 class="my-2">{{ $currentProjects }}</h3>
                             <p class="mb-0 text-truncate text-muted">
                            <span class="text-success"><i class="mdi mdi-trending-up"></i></span> Current Projects
                        </p>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="report-main-icon bg-light-alt">
                                <i data-feather="clock" class="align-self-center text-muted icon-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card report-card">
                <div class="card-body">
                    <div class="row d-flex justify-content-center">
                        <div class="col">
                            <p class="text-dark mb-1 font-weight-semibold">✅ Completed Projects</p>
                            <h3 class="my-2">{{ $completedProjects }}</h3>
                             <p class="mb-0 text-truncate text-muted">
                            <span class="text-success"><i class="mdi mdi-trending-up"></i></span> Completed Projects
                        </p>
                        </div>
                        <div class="col-auto align-self-center">
                            <div class="report-main-icon bg-light-alt">
                                <i data-feather="activity" class="align-self-center text-muted icon-md"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 🔹 My Projects --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="bi bi-kanban me-2"></i> My Projects
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>Deadline</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Remaining</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        @php
                            $latestSchedule = $project->schedules->sortByDesc('id')->first();
                            $projectStatus = $latestSchedule ? $latestSchedule->status : '-';
                        @endphp
                        <tr>
                            <td>{{ $project->title }}</td>
                            <td>{{ Str::limit($project->description, 40) ?? '-' }}</td>
                            <td>
                                <span class="badge 
                                    {{ $projectStatus === 'completed'
                                        ? 'bg-success'
                                        : ($projectStatus === 'inprogress'
                                            ? 'bg-warning'
                                            : 'bg-secondary') }}">
                                    {{ ucfirst($projectStatus) }}
                                </span>
                            </td>
                            <td>{{ $project->start_date ?? '-' }}</td>
                            <td>{{ $project->developer_end_date ?? '-' }}</td>
                            <td>${{ $project->price ?? '-' }}</td>
                            <td>${{ $project->paid_price ?? '-' }}</td>
                            <td>${{ $project->remaining_price ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No projects assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endif
@endsection
