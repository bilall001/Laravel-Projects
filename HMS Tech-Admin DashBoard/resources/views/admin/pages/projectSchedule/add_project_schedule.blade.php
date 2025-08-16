@extends('admin.layouts.main')
@section('title')
Project Schedule - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container mt-4">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">📅 Project Schedules</h4>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                ➕ Add Schedule
            </button>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->title }}</td>
                                <td>{{ $schedule->date }}</td>
                                <td>
                                    <span class="badge {{ $schedule->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
<td>
  <div class="d-flex align-items-center gap-1 justify-content-center">
    <button 
      class="btn btn-sm btn-light view-schedule-btn" 
      data-bs-toggle="modal" 
      data-bs-target="#viewModal" 
      data-title="{{ $schedule->title }}" 
      data-date="{{ $schedule->date }}" 
      data-status="{{ ucfirst($schedule->status) }}" 
      title="View"
    >
      <i class="fas fa-eye text-primary"></i>
    </button>

    <button 
      class="btn btn-sm btn-light edit-schedule-btn" 
      data-id="{{ $schedule->id }}" 
      data-title="{{ $schedule->title }}" 
      data-date="{{ $schedule->date }}" 
      data-status="{{ $schedule->status }}" 
      data-bs-toggle="modal" 
      data-bs-target="#editModal" 
      title="Edit"
    >
      <i class="fas fa-edit text-info"></i>
    </button>

    <form 
      action="{{ route('projectSchedule.destroy', $schedule->id) }}" 
      method="POST" 
      class="d-inline" 
      onsubmit="return confirm('Are you sure you want to delete this schedule?')"
    >
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-light" title="Delete">
        <i class="fas fa-trash text-danger"></i>
      </button>
    </form>
  </div>
</td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('projectSchedule.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">➕ Add Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Add Schedule</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">✏️ Edit Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" id="editTitle" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" id="editDate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" id="editStatus" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning text-white">Update Schedule</button>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">📄 Schedule Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Title:</strong> <span id="viewTitle"></span></p>
                <p><strong>Date:</strong> <span id="viewDate"></span></p>
                <p><strong>Status:</strong> <span id="viewStatus"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Edit modal - fetch data when shown
    document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        this.querySelector('#editTitle').value = button.getAttribute('data-title');
        this.querySelector('#editDate').value = button.getAttribute('data-date');
        this.querySelector('#editStatus').value = button.getAttribute('data-status');
        this.querySelector('#editForm').action = `/projectSchedule/${id}`;
    });

    // View modal - fetch data when shown
    document.getElementById('viewModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        this.querySelector('#viewTitle').innerText = button.getAttribute('data-title');
        this.querySelector('#viewDate').innerText = button.getAttribute('data-date');
        this.querySelector('#viewStatus').innerText = button.getAttribute('data-status');
    });
</script>
@endsection
