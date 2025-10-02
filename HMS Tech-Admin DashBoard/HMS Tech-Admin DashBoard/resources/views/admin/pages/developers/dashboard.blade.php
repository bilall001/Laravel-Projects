@extends('admin.layouts.main')

@section('content')
    @if (Auth::user()->role === 'developer')
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
                            </div><!--end col-->
                            <div class="col-auto align-self-center">
                                <a href="#" class="btn btn-sm btn-outline-primary" id="Dash_Date">
                                    <span class="ay-name" id="Day_Name">Today:</span>&nbsp;
                                    <span class="" id="Select_date">Jan 11</span>
                                    <i data-feather="calendar" class="align-self-center icon-xs ml-1"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-outline-primary">
                                    <i data-feather="download" class="align-self-center icon-xs"></i>
                                </a>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end page-title-box-->
                </div><!--end col-->
            </div><!--end row-->
            <!-- end page title end breadcrumb -->
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col">
                                    <p class="text-dark mb-1 font-weight-semibold">📊 Individual Projects Assigned</p>
                                    <h3 class="my-2">{{ $directProjectsCount }}</h3>
                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                                class="mdi mdi-trending-up"></i>8.5%</span> </p>
                                </div>
                                <div class="col-auto align-self-center">
                                    <div class="report-main-icon bg-light-alt">
                                        <i data-feather="users" class="align-self-center text-muted icon-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
                <div class="col-md-6 col-lg-4">
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col">
                                    <p class="text-dark mb-1 font-weight-semibold">🚧 Team Projects Assigned</p>
                                    <h3 class="my-2">{{ $teamProjectsCount }}</h3>
                                    <p class="mb-0 text-truncate text-muted"><span class="text-success"><i
                                                class="mdi mdi-trending-up"></i>1.5%</span> Weekly Avg.Sessions</p>
                                </div>
                                <div class="col-auto align-self-center">
                                    <div class="report-main-icon bg-light-alt">
                                        <i data-feather="clock" class="align-self-center text-muted icon-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
                <div class="col-md-6 col-lg-4">
                    <div class="card report-card">
                        <div class="card-body">
                            <div class="row d-flex justify-content-center">
                                <div class="col">
                                    <p class="text-dark mb-1 font-weight-semibold">✅Total Teams Part In</p>
                                    <h3 class="my-2">{{ $teamCount }}</h3>
                                    <p class="mb-0 text-truncate text-muted"><span class="text-danger"><i
                                                class="mdi mdi-trending-down"></i>35%</span> Bounce Rate Weekly</p>
                                </div>
                                <div class="col-auto align-self-center">
                                    <div class="report-main-icon bg-light-alt">
                                        <i data-feather="activity" class="align-self-center text-muted icon-md"></i>
                                    </div>
                                </div>
                            </div>
                        </div><!--end card-body-->
                    </div><!--end card-->
                </div> <!--end col-->
            </div><!--end row-->

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
                                <th>Type</th>
                                <th>Team</th>
                                <th>Assigned By</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allProjects as $project)
                                <tr>
                                    <td>{{ $project->title }}</td>
                                    <td>{{ Str::limit($project->description, 40) ?? '-' }}</td>
                                    <td>{{ ucfirst($project->type) }}</td>
                                    <td>{{ $project->team?->name ?? '-' }}</td>
                                    <td>{{ $project->businessDeveloper?->name ?? '-' }}</td>
                                    <td>{{ $project->start_date ?? '-' }}</td>
                                    <td>{{ $project->developer_end_date ?? '-' }}</td>
                                    <td>
                                        @php
                                            $latestSchedule = $project->schedules->sortByDesc('id')->first();
                                            $projectStatus = $latestSchedule ? $latestSchedule->status : '-';
                                        @endphp
                                        <span
                                            class="badge 
        {{ $projectStatus === 'completed'
            ? 'bg-success'
            : ($projectStatus === 'inprogress'
                ? 'bg-warning'
                : 'bg-secondary') }}">
                                            {{ ucfirst($projectStatus) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-info">{{ $project->assignment_type }}</span>
                                    </td>

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

            <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white fw-semibold">
        <i class="bi bi-people-fill me-2"></i> My Teams
    </div>
    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Team Name</th>
                    <th>Other Developers</th>
                    <th>Total Members</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teams as $team)
                    <tr>
                        <td>{{ $team->name }}</td>
                        <td>
                            @forelse($team->users as $user)
                                <span class="badge bg-info text-dark">{{ $user->name }}</span>
                            @empty
                                <span class="text-muted">No other developers</span>
                            @endforelse
                        </td>
                        <td>{{ $team->users->count() + 1 }}</td> {{-- +1 for current developer --}}
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">You are not part of any team.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

            <div class="row">

                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            📊 Attendance Progress ({{ now()->format('F Y') }})
                        </div>
                        <div class="card-body">
                            <p><strong>Total Days:</strong> {{ $totalDays }}</p>
                            <p><strong>Present:</strong> ✅ {{ $presentDays }}</p>
                            <p><strong>Absent:</strong> ❌ {{ $absentDays }}</p>
                            <p><strong>Leave:</strong> 🏖️ {{ $leaveDays }}</p>

                            <div class="progress mt-3" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                    style="width: {{ $attendancePercentage }}%;"
                                    aria-valuenow="{{ $attendancePercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $attendancePercentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Salary --}}
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">💰 Salary Info</div>
                        <div class="card-body table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Amount</th>
                                        <th>Date Paid</th>
                                        <th>Method</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salaries as $salary)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($salary->salary_date)->format('F Y') }}</td>
                                            <td>Rs. {{ number_format($salary->amount, 2) }}</td>
                                            <td>{{ \Carbon\Carbon::parse($salary->salary_date)->format('d M, Y') }}</td>
                                            <td>{{ ucfirst($salary->payment_method) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No Paid Salaries Found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
@endsection
