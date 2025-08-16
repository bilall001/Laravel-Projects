@extends('admin.layouts.main')
@section('title')
Tasks - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">Manage Tasks</h4>
        <button class="btn btn-primary" data-toggle="modal" data-target="#assignTaskModal" id="createTaskBtn">Assign Task</button>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tasks Table --}}
    <div class="card">
        <div class="card-header">All Tasks</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project Title</th>
                        <th>File</th>
                        <th>Type</th>
                        <th>Team</th>
                        <th>User</th>
                        <th>Client</th>
                        <th>Description</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $t)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $t->project->title ?? 'N/A' }}</td>
                            <td>
                                @if($t->project && $t->project->file)
                                    <a href="{{ asset('storage/' . $t->project->file) }}" target="_blank">View</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ ucfirst($t->project->type ?? '-') }}</td>
                            <td>{{ $t->team->name ?? '-' }}</td>
                            <td>{{ $t->user->name ?? '-' }}</td>
                            <td>{{ $t->project->client->user->name ?? '-' }}</td>
                            <td>{{ $t->description }}</td>
                            <td>{{ $t->end_date }}</td>
                            <td>
                                <button
                                    class="btn btn-sm btn-warning edit-task-btn"
                                    data-id="{{ $t->id }}"
                                    data-project-title="{{ $t->project->title ?? '' }}"
                                    data-project-id="{{ $t->project->id ?? '' }}"
                                    data-project-file="{{ $t->project->file ?? '' }}"
                                    data-project-type="{{ $t->project->type ?? '' }}"
                                    data-team-name="{{ $t->team->name ?? '' }}"
                                    data-user-name="{{ $t->user->name ?? '' }}"
                                    data-client-name="{{ $t->project->client->name ?? '' }}"
                                    data-end-date="{{ \Carbon\Carbon::parse($t->end_date)->format('Y-m-d') }}"
                                    data-description="{{ $t->description }}"
                                >
                                    Edit
                                </button>

                                <form action="{{ route('admin.tasks.destroy', $t->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete task?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Assign/Edit Task -->
<div class="modal fade" id="assignTaskModal" tabindex="-1" role="dialog" aria-labelledby="assignTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" id="taskForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign/Edit Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Project Title</label>
                            <select id="projectTitleSelect" class="form-control" required>
                                <option value="">-- Select Title --</option>
                                @foreach($uniqueTitles as $title)
                                    <option value="{{ $title }}">{{ $title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Client</label>
                            <input type="text" id="assignedClient" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Project File</label>
                            <input type="text" id="projectFile" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Project Type</label>
                            <input type="text" id="projectType" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Team (if team project)</label>
                            <input type="text" id="assignedTeam" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>User (if individual project)</label>
                            <input type="text" id="assignedUser" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>End Date</label>
                            <input type="date" name="end_date" id="taskEndDate" class="form-control" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Description</label>
                            <input type="text" name="description" id="taskDescription" class="form-control" required>
                        </div>
                        <input type="hidden" name="project_id" id="projectId">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Task</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- JS for filling modal --}}
<script>
    // Load project info on title select
    document.getElementById('projectTitleSelect')?.addEventListener('change', function () {
        const title = this.value;
        if (!title) return;

        fetch(`/admin/tasks/project-info/${encodeURIComponent(title)}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('projectId').value = data.id || '';
                document.getElementById('projectFile').value = data.file || '';
                document.getElementById('projectType').value = data.type || '';
                document.getElementById('assignedTeam').value = data.team ? data.team.name : '';
                document.getElementById('assignedUser').value = data.user ? data.user.name : '';
                document.getElementById('assignedClient').value = data.client && data.client.user ? data.client.user.name : 'N/A';
            })
            .catch(err => alert("Could not fetch project info"));
    });

    // Open modal for create
    document.getElementById('createTaskBtn')?.addEventListener('click', function () {
        const form = document.getElementById('taskForm');
        form.reset();
        form.action = "{{ route('admin.tasks.store') }}";
        document.getElementById('formMethod').value = 'POST';
    });

    // Open modal for edit
    document.querySelectorAll('.edit-task-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('taskForm');
            const id = this.dataset.id;

            form.action = `/admin/tasks/${id}`;
            document.getElementById('formMethod').value = 'PUT';

            document.getElementById('projectTitleSelect').value = this.dataset.projectTitle;
            document.getElementById('projectId').value = this.dataset.projectId;
            document.getElementById('projectFile').value = this.dataset.projectFile;
            document.getElementById('projectType').value = this.dataset.projectType;
            document.getElementById('assignedTeam').value = this.dataset.teamName;
            document.getElementById('assignedUser').value = this.dataset.userName;
            document.getElementById('assignedClient').value = this.dataset.clientName;
            document.getElementById('taskEndDate').value = this.dataset.endDate;
            document.getElementById('taskDescription').value = this.dataset.description;

            $('#assignTaskModal').modal('show');
        });
    });
</script>
@endsection
