@extends('admin.layouts.main')

@section('content')
    @if (Auth::user()->role === 'business develope')
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
                        @if ($s->is_paid)
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
                            <span
                                class="{{ $att->status === 'present' ? 'text-success' : ($att->status === 'leave' ? 'text-warning' : 'text-danger') }}">
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
    <div class="container-fluid">
        <!-- Page-Title -->

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
                                <p class="text-dark mb-1 font-weight-semibold">📊 Total Projects</p>
                                <h3 class="my-2">{{ $totalProjects }}</h3>
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
                                <p class="text-dark mb-1 font-weight-semibold">🚧 Current Projects</p>
                                <h3 class="my-2">{{ $currentProjects }}</h3>
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
                                <p class="text-dark mb-1 font-weight-semibold">✅ Completed Projects</p>
                                <h3 class="my-2">{{ $completedProjects }}</h3>
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
        {{-- end second card row --}}
        <div class="card shadow">
            <div class="card-header bg-primary text-white">📂 My Projects</div>
            <div class="card-body table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Assigned To</th>
                            <th>Total Price</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project->title }}</td>
                                <td>{{ $project->client->user->name ?? 'N/A' }}</td>
                                <td>
                                    @if ($project->type === 'individual')
                                        👨‍💻 {{ $project->user->name ?? 'N/A' }}
                                    @else
                                        👥 {{ $project->team->name ?? 'N/A' }}
                                    @endif
                                </td>
                                <td>${{ number_format($project->price, 2) }}</td>
                                <td>${{ number_format($project->paid_price, 2) }}</td>
                                <td>${{ number_format($project->remaining_price, 2) }}</td>
                                <td>{{ $project->start_date }}</td>
                                <td>{{ $project->end_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No projects assigned yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            {{-- Attendance Progress --}}
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
                                style="width: {{ $attendancePercentage }}%;" aria-valuenow="{{ $attendancePercentage }}"
                                aria-valuemin="0" aria-valuemax="100">
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
    </div><!-- container -->
@endsection
