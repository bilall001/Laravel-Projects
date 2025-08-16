@extends('admin.layouts.main')
@section('title')
Attendance - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container my-5">
  <h1 class="h3 mb-4 text-primary">
    📅 Attendance Management
  </h1>

  {{-- ✅ Flash Message --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ✅ Filters --}}
  <div class="card border-0 shadow-sm mb-4 p-3">
    <form method="GET" class="row gx-3 gy-2 align-items-end">
      <div class="col">
        <label class="form-label fw-bold mb-1">Select Role</label>
        <select name="role" class="form-select shadow-sm w-100">
          <option value="">All Roles</option>
          @foreach($roles as $role)
            <option value="{{ $role }}" {{ $selectedRole === $role ? 'selected' : '' }}>
              {{ ucfirst($role) }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col">
        <label class="form-label fw-bold mb-1">Select Date</label>
        <input type="date" name="date" class="form-control shadow-sm w-100" value="{{ $date }}">
        <small class="text-muted d-block">Yesterday: {{ \Carbon\Carbon::yesterday()->toDateString() }}</small>
      </div>
      <div class="col">
        <label class="form-label fw-bold mb-1 invisible">Filter</label>
        <button type="submit" class="btn btn-primary shadow-sm w-100">
          <i class="bi bi-funnel-fill"></i> Filter
        </button>
      </div>
    </form>
  </div>

  {{-- ✅ Summary --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <h6 class="fw-bold mb-1 text-muted">Total Users</h6>
          <h3 class="fw-bold text-dark">{{ $totalUsers }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <h6 class="fw-bold mb-1 text-muted">Presents</h6>
          <h3 class="fw-bold text-success">{{ $totalPresents }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <h6 class="fw-bold mb-1 text-muted">Absents</h6>
          <h3 class="fw-bold text-danger">{{ $totalAbsents }}</h3>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <h6 class="fw-bold mb-1 text-muted">Leaves</h6>
          <h3 class="fw-bold text-warning">{{ $totalLeaves }}</h3>
        </div>
      </div>
    </div>
  </div>

  {{-- ✅ Attendance Table --}}
  <div class="table-responsive">
    <table class="table table-striped table-hover align-middle shadow-sm">
      <thead class="bg-secondary text-white">
        <tr>
          <th>Name</th>
          <th>Role</th>
          <th>Check In</th>
          <th>Check Out</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
          @php $attendance = $attendances[$user->id] ?? null; @endphp
          <tr>
            <td>{{ $user->name }}</td>
            <td>{{ ucfirst($user->role) }}</td>
            <td>
              @if($attendance?->is_leave)
                <span class="badge bg-danger">Leave</span>
              @else
                {{ $attendance?->check_in ?? '-' }}
              @endif
            </td>
            <td>
              @if($attendance?->is_leave)
                <span class="badge bg-danger">Leave</span>
              @else
                {{ $attendance?->check_out ?? '-' }}
              @endif
            </td>
            <td>
              @if(!$attendance)
                {{-- ✅ Mark Leave --}}
                <form method="POST" action="{{ route('attendances.markLeave') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <input type="hidden" name="date" value="{{ $date }}">
                  <button type="submit" class="btn btn-sm btn-danger">
                    <i class="bi bi-calendar-x"></i> Leave
                  </button>
                </form>

                {{-- ✅ Mark Absent --}}
                <form method="POST" action="{{ route('attendances.markAbsent') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <input type="hidden" name="date" value="{{ $date }}">
                  <button type="submit" class="btn btn-sm btn-dark">
                    <i class="bi bi-x-circle"></i> Absent
                  </button>
                </form>

                {{-- ✅ Check In --}}
                <form method="POST" action="{{ route('attendances.store') }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <input type="hidden" name="date" value="{{ $date }}">
                  <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-box-arrow-in-right"></i> Check In
                  </button>
                </form>

              @elseif($attendance->is_leave)
                <span class="badge bg-danger">On Leave</span>

              @elseif($attendance->is_absent)
                <span class="badge bg-dark">Absent</span>

              @elseif(!$attendance->check_in)
                <form method="POST" action="{{ route('attendances.store') }}">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <input type="hidden" name="date" value="{{ $date }}">
                  <button type="submit" class="btn btn-sm btn-success">
                    <i class="bi bi-box-arrow-in-right"></i> Check In
                  </button>
                </form>

              @elseif(!$attendance->check_out)
                <form method="POST" action="{{ route('attendances.store') }}">
                  @csrf
                  <input type="hidden" name="user_id" value="{{ $user->id }}">
                  <input type="hidden" name="date" value="{{ $date }}">
                  <button type="submit" class="btn btn-sm btn-warning">
                    <i class="bi bi-box-arrow-left"></i> Check Out
                  </button>
                </form>
              @else
                ✅ Done
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
