@extends('admin.layouts.main')
@section('title')
    Project Schedule - HMS Tech & Solutions
@endsection

@section('content')
    <div class="container mt-4">

        {{-- Header --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-primary fw-bold">📅 Project Schedules</h4>
                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'business developer' || auth()->user()->role === 'team manager')
                <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle"></i> Add Schedule
                </button>
                @endif
            </div>
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Schedules Table --}}
        <div class="card">
            <div class="card-header text-white" style="background-color: #1D2C48">All Project Schedules</div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Project</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schedules as $schedule)
                            <tr>
                                <td>{{ $schedule->project->title ?? '-' }}</td>
                                <td>{{ $schedule->date }}</td>
                                <td>
                                    <span
                                        class="badge {{ $schedule->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($schedule->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        {{-- View --}}
                                        <button class="btn btn-sm btn-light view-schedule-btn" data-bs-toggle="modal"
                                            data-bs-target="#viewModal" data-title="{{ $schedule->project->title }}"
                                            data-date="{{ $schedule->date }}" data-status="{{ ucfirst($schedule->status) }}"
                                            title="View">
                                            <i class="fas fa-eye text-primary"></i>
                                        </button>

                                        {{-- Edit --}}
                                        <button class="btn btn-sm btn-light edit-schedule-btn" data-id="{{ $schedule->id }}"
                                            data-title="{{ $schedule->project->title ?? '' }}"
                                            data-date="{{ $schedule->date }}" data-status="{{ $schedule->status }}"
                                            data-project="{{ $schedule->project_id }}" data-bs-toggle="modal"
                                            data-bs-target="#editModal" title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('projectSchedule.destroy', $schedule->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this schedule?')">
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
                                <td colspan="5" class="text-center text-muted">No schedules found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('projectSchedule.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">➕ Add Schedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label">Project</label>
                        <select name="project_id" class="form-control" required>
                            <option value="">-- Select Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option> {{-- use title if name is empty --}}
                            @endforeach
                        </select>


                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="inProgress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Add Schedule</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Modal --}}
    {{-- <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
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
                        <label class="form-label">Project</label>
                        <select name="project_id" id="editProject" class="form-control" required>
                            <option value="">-- Select Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" id="editDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="inProgress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning text-white">Update Schedule</button>
                </div>
            </form>
        </div>
    </div> --}}

    {{-- Edit Modal --}}
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
                        <label class="form-label">Project</label>
                        <select name="project_id" id="editProject" class="form-control" required>
                            <option value="">-- Select Project --</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" id="editDate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="editStatus" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="inProgress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning text-white">Update Schedule</button>
                </div>
            </form>
        </div>
    </div>

    {{-- View Modal --}}
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

    {{-- JS for Modals --}}
    <script>

        document.getElementById('editModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const projectId = button.getAttribute('data-project');

            this.querySelector('#editProject').value = projectId;
            this.querySelector('#editDate').value = button.getAttribute('data-date');
            this.querySelector('#editStatus').value = button.getAttribute('data-status');

            // Set correct action with ID
            this.querySelector('#editForm').action = "/projectSchedule/" + id;
        });
        // View modal fill
        document.getElementById('viewModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            this.querySelector('#viewTitle').innerText = button.getAttribute('data-title');
            this.querySelector('#viewDate').innerText = button.getAttribute('data-date');
            this.querySelector('#viewStatus').innerText = button.getAttribute('data-status');
        });
    </script>
@endsection
