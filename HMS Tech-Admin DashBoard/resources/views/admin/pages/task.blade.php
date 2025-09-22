@extends('admin.layouts.main')
@section('title', 'Tasks - HMS Tech & Solutions')

@section('custom_css')
    <style>
        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
        }
        /* Inside the Quill editor while editing */
  .quill-editor .ql-editor img {
    max-width: 100%;
    height: auto;
    max-height: 380px;        /* tweak to taste */
    display: block;
    margin: .5rem auto;       /* center + spacing */
    border-radius: 6px;
  }

  /* Read-only render (show modal / index previews) */
  .ql-render img,
  .task-body img {
    max-width: 100%;
    height: auto;
    max-height: 480px;        /* a bit taller for reading */
    display: block;
    margin: .5rem auto;
    border-radius: 6px;
  }

  /* Optional: prevent the editor container from stretching too tall */
  .quill-editor {
    max-height: 60vh;
    overflow: auto;
  }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="page-title">Tasks</h4>
            </div>
            <div class="col-md-6 text-md-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#createTaskModal">Add Task</button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card">
            <div class="card-header text-white" style="background-color:#1D2C48">All Tasks</div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Title</th>
                            <th>Project</th>
                            <th>Teams</th>
                            <th>Assignees</th>
                            <th>Roles</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Due</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr>
                                <td>{{ $task->title }}</td>
                                <td>{{ $task->project->title ?? '-' }}</td>
                                <td>
                                    @forelse ($task->teams as $t)
                                        <span class="badge badge-info">{{ $t->name }}</span>
                                    @empty -
                                    @endforelse
                                </td>
                                <td>
                                    @foreach ($task->assignees as $dev)
                                        @php $teamNames = $dev->teams->pluck('name')->implode(', '); @endphp
                                        <span class="badge badge-secondary">
                                            {{ $dev->user->name ?? 'Dev#' . $dev->id }}{{ $teamNames ? ' — ' . $teamNames : '' }}
                                        </span>
                                    @endforeach
                                </td>
                              <td>
    @forelse ($task->getRolesForAssignees() as $mr)
        <span class="badge bg-info">
            {{ $mr->developer?->user?->name }} → {{ $mr->role?->name }}
        </span>
    @empty
        <span class="text-muted">Unassigned</span>
    @endforelse
</td>
                                <td>
                                    <span
                                        class="badge
    @if ($task->status === 'pending') badge-light
    @elseif ($task->status === 'in_progress') badge-warning
    @elseif ($task->status === 'review') badge-info
    @elseif ($task->status === 'completed') badge-success @endif">
                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                    </span>

                                </td>
                                <td>
                                    <span
                                        class="badge
                @if ($task->priority === 'low') badge-secondary
                @elseif($task->priority === 'normal') badge-info
                @elseif($task->priority === 'high') badge-warning
                @else badge-danger @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td>{{ optional($task->due_date)->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-sm btn-light mr-1" data-toggle="modal"
                                            data-target="#showTaskModal-{{ $task->id }}" title="View">
                                            <i class="fas fa-eye text-primary"></i>
                                        </button>
                                        <button class="btn btn-sm btn-light mr-1" data-toggle="modal"
                                            data-target="#editTaskModal-{{ $task->id }}" title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>
                                        <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                            onsubmit="return confirm('Delete this task?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-light" title="Delete"><i
                                                    class="fas fa-trash text-danger"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Show Modal --}}
                            <div class="modal fade" id="showTaskModal-{{ $task->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Task Details</h5>
                                            <button type="button" class="close"
                                                data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body" style="max-height:75vh;overflow:auto;">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Title:</strong> {{ $task->title }}</p>
                                                    <p><strong>Project:</strong> {{ $task->project->title ?? '-' }}</p>
                                                    <p><strong>Status:</strong>
                                                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}</p>
                                                    <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
                                                    <p><strong>Due:</strong>
                                                        {{ optional($task->due_date)->format('Y-m-d') ?? '-' }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Teams:</strong>
                                                        @forelse($task->teams as $t)
                                                            <span class="badge badge-info">{{ $t->name }}</span>
                                                        @empty -
                                                        @endforelse
                                                    </p>
                                                    <p><strong>Assignees:</strong>
                                                        @forelse($task->assignees as $d)
                                                            <span
                                                                class="badge badge-secondary">{{ $d->user->name ?? 'Dev#' . $d->id }}</span>
                                                        @empty -
                                                        @endforelse
                                                    </p>
                                                    <p><strong>Created By:</strong> {{ $task->creator->name ?? '-' }}</p>
                                                </div>
                                               
                                            </div>
                                            <hr>
                                            
                                            <div>
                                                <strong>Description</strong>
                                                <div class="ql-render border rounded p-3" style="min-height:120px;">
                                                    {!! $task->body_html !!}</div>
                                            </div>
                                        </div>
                                        <div class="modal-footer"><button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Modal --}}
                            @php
                                // Server-side eligible developers (union) for this task's project
$eligible =
    $task->project->type === 'individual'
        ? $task->project->developers
        : $task->project->teams->flatMap->developers->unique('id')->values();
$selectedIds = $task->assignees->pluck('id')->map(fn($v) => (string) $v)->toArray();
                            @endphp

                            <div class="modal fade" id="editTaskModal-{{ $task->id }}" tabindex="-1" role="dialog"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <form method="POST" action="{{ route('tasks.update', $task) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Task</h5>
                                                <button type="button" class="close"
                                                    data-dismiss="modal"><span>&times;</span></button>
                                            </div>
                                            <div class="modal-body" style="max-height:75vh;overflow:auto;">

                                                <div class="form-row">
                                                    <div class="form-group col-md-6">
                                                        <label>Project</label>
                                                        <input type="text" class="form-control"
                                                            value="{{ $task->project->title ?? '-' }}" disabled>
                                                    </div>
                                                    <div class="form-group col-md-6">
                                                        <label>Title</label>
                                                        <input type="text" name="title" class="form-control"
                                                            value="{{ $task->title }}" required>
                                                    </div>
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-4">
                                                        <label>Status</label>
                                                        <select name="status" class="form-control">
                                                            @foreach (['pending', 'in_progress', 'review', 'completed'] as $s)
                                                                <option value="{{ $s }}"
                                                                    {{ $task->status === $s ? 'selected' : '' }}>
                                                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group col-md-4">
                                                        <label>Priority</label>
                                                        <select name="priority" class="form-control">
                                                            @foreach (['low', 'normal', 'high', 'urgent'] as $p)
                                                                <option value="{{ $p }}"
                                                                    {{ $task->priority === $p ? 'selected' : '' }}>
                                                                    {{ ucfirst($p) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group col-md-4">
                                                        <label>Due Date</label>
                                                        <input type="date" name="due_date" class="form-control"
                                                            value="{{ optional($task->due_date)->format('Y-m-d') }}">
                                                    </div>
                                                </div>

                                                {{-- Teams (labels) — ONLY for team projects --}}
                                                @if ($task->project->type === 'team')
                                                    <div class="form-group" id="edit-teams-group-{{ $task->id }}">
                                                        <label>Teams (labels)</label>
                                                        <select id="edit-teams-{{ $task->id }}" name="team_ids[]"
                                                            class="form-control select2" multiple>
                                                            @foreach ($task->project->teams as $t)
                                                                <option value="{{ $t->id }}"
                                                                    {{ $task->teams->pluck('id')->contains($t->id) ? 'selected' : '' }}>
                                                                    {{ $t->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-muted">Optional: tag this task to one or more
                                                            project teams.</small>
                                                    </div>

                                                    <div class="form-group"
                                                        id="edit-show-only-group-{{ $task->id }}">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                id="edit-show-only-members-{{ $task->id }}"
                                                                class="custom-control-input" checked>
                                                            <label class="custom-control-label"
                                                                for="edit-show-only-members-{{ $task->id }}">Show only
                                                                members of the selected team(s)</label>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Assignees (developers) --}}
                                                <div class="form-group" id="edit-developers-group-{{ $task->id }}">
                                                    <label>Assignees (developers)</label>
                                                    <select id="edit-developers-{{ $task->id }}"
                                                        name="developer_ids[]" class="form-control select2" multiple
                                                        data-selected='@json($selectedIds)'>
                                                        {{-- Server-side initial options so edit modal always shows selections even if JS fails --}}
                                                        @foreach ($eligible as $dev)
                                                            <option value="{{ $dev->id }}"
                                                                {{ in_array((string) $dev->id, $selectedIds, true) ? 'selected' : '' }}>
                                                                {{ $dev->user->name ?? 'Dev#' . $dev->id }}
                                                                @php $tn = $dev->teams->pluck('name')->implode(', '); @endphp
                                                                {{ $tn ? ' — ' . $tn : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Assign to all team members (only meaningful if teams exist) --}}
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" id="assignAll-{{ $task->id }}"
                                                            class="custom-control-input" name="assign_all_team_members"
                                                            value="1">
                                                        <label class="custom-control-label"
                                                            for="assignAll-{{ $task->id }}">Also assign to all members
                                                            of the selected team(s)</label>
                                                    </div>
                                                    <small class="text-muted">If checked, team members will be added to
                                                        assignees on save.</small>
                                                </div>

                                                {{-- Quill --}}
                                                <div class="form-group">
                                                    <label>Task Description</label>
                                                    <div id="quill-edit-{{ $task->id }}"
                                                        class="quill-editor border rounded" style="min-height:180px;">
                                                    </div>
                                                    <input type="hidden" name="body_html"
                                                        id="body_html_edit_{{ $task->id }}">
                                                    <input type="hidden" name="body_json"
                                                        id="body_json_edit_{{ $task->id }}">
                                                    <small class="text-muted d-block mt-1">Tip: Use the button below to
                                                        upload images into the editor.</small>
                                                </div>

                                                <div class="form-group">
                                                    <label>Insert Image</label>
                                                    <input type="file" class="form-control-file" accept="image/*"
                                                        onchange="uploadTaskImageAndInsert(event, {{ $task->id }})">
                                                    <small class="text-muted">Images are uploaded to the server and
                                                        inserted at the cursor.</small>
                                                </div>

                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-success"
                                                    onclick="syncQuillBeforeSubmit({{ $task->id }})">Save</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Cancel</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                $(function() {
                                    bindEditTeamsFilter({{ $task->id }}, {{ $task->project_id }});
                                });
                            </script>

                        @empty
                            <tr>
                                <td colspan="8">No tasks found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">{{ $tasks->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Task</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" style="max-height:75vh;overflow:auto;">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Project</label>
                                <select id="create-project" name="project_id" class="form-control select2" required>
                                    <option value="">Select Project</option>
                                    @foreach ($projects as $p)
                                        <option value="{{ $p->id }}">{{ $p->title }}
                                            ({{ ucfirst($p->type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    @foreach (['pending', 'in_progress', 'review', 'completed'] as $s)
                                        <option value="{{ $s }}">{{ ucfirst(str_replace('_', ' ', $s)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Priority</label>
                                <select name="priority" class="form-control">
                                    @foreach (['low', 'normal', 'high', 'urgent'] as $p)
                                        <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Due Date</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                        </div>

                        <div class="form-group" id="create-teams-group">
                            <label>Teams (labels)</label>
                            <select id="create-teams" name="team_ids[]" class="form-control select2" multiple></select>
                        </div>

                        <div class="form-group" id="create-show-only-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="create-show-only-members" class="custom-control-input"
                                    checked>
                                <label class="custom-control-label" for="create-show-only-members">Show only members of
                                    the selected team(s)</label>
                            </div>
                        </div>

                        <div class="form-group" id="create-developers-group">
                            <label>Assignees (developers)</label>
                            <select id="create-developers" name="developer_ids[]" class="form-control select2"
                                multiple></select>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="create-assign-all" class="custom-control-input"
                                    name="assign_all_team_members" value="1">
                                <label class="custom-control-label" for="create-assign-all">Also assign to all members of
                                    the selected team(s)</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Task Description</label>
                            <div id="quill-create" class="quill-editor border rounded" style="min-height:180px;"></div>
                            <input type="hidden" name="body_html" id="body_html_create">
                            <input type="hidden" name="body_json" id="body_json_create">
                            <small class="text-muted d-block mt-1">You can save the task first, then open Edit to upload
                                and insert images.</small>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" onclick="syncQuillCreate()">Create</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @php
        $projectPayload = $projects
            ->map(function ($proj) {
                $teams = $proj->teams->map(fn($t) => ['id' => $t->id, 'name' => $t->name])->values();

                $teamMembers = $proj->teams->mapWithKeys(function ($t) {
                    $members = $t->developers
                        ->map(function ($d) use ($t) {
                            return [
                                'id' => $d->id,
                                'name' => $d->user->name ?? 'Dev#' . $d->id,
                                'teams' => [$t->name],
                            ];
                        })
                        ->values();
                    return [$t->id => $members];
                });

                $developerTeams = [];
                foreach ($proj->teams as $t) {
                    foreach ($t->developers as $d) {
                        $developerTeams[$d->id] = $developerTeams[$d->id] ?? [];
                        if (!in_array($t->name, $developerTeams[$d->id])) {
                            $developerTeams[$d->id][] = $t->name;
                        }
                    }
                }

                $devs =
                    $proj->type === 'individual'
                        ? $proj->developers
                        : $proj->teams->flatMap->developers->unique('id')->values();

                $developers = $devs
                    ->map(function ($d) use ($developerTeams) {
                        return [
                            'id' => $d->id,
                            'name' => $d->user->name ?? 'Dev#' . $d->id,
                            'teams' => $developerTeams[$d->id] ?? [],
                        ];
                    })
                    ->values();

                return [
                    'id' => $proj->id,
                    'title' => $proj->title,
                    'type' => $proj->type,
                    'teams' => $teams,
                    'developers' => $developers,
                    'teamMembers' => $teamMembers,
                ];
            })
            ->values();
    @endphp
    <script>
        window.PROJECT_DATA = @json($projectPayload);
    </script>
@endsection

@push('custom_js')
    <script>
        // ---------- Select2 ----------
        $(function() {
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select options',
                allowClear: true
            });
        });

        // ---------- Helpers ----------
        function optionLabel(dev) {
            const teams = (dev.teams && dev.teams.length) ? ' — ' + dev.teams.join(', ') : '';
            return `${dev.name}${teams}`;
        }

        function fillOptions($select, items, keepSelection = false) {
            const prev = keepSelection ? ($select.val() || []).map(v => String(v)) : [];
            $select.empty();
            (items || []).forEach(d => {
                const opt = new Option(optionLabel(d), d.id, false, false);
                $select.append(opt);
            });
            if (keepSelection) {
                const ids = new Set((items || []).map(d => String(d.id)));
                const keep = prev.filter(v => ids.has(v));
                $select.val(keep).trigger('change');
            } else {
                $select.trigger('change');
            }
        }

        function projectById(pid) {
            pid = parseInt(pid || 0);
            return (window.PROJECT_DATA || []).find(x => x.id === pid);
        }

        // ---------- CREATE ----------
        const $projSel = $('#create-project');
        const $teamsSel = $('#create-teams');
        const $devsSel = $('#create-developers');
        const $showOnly = $('#create-show-only-members');

        function onProjectChange() {
            const p = projectById($projSel.val());
            if (!p) {
                fillOptions($teamsSel, []);
                fillOptions($devsSel, []);
                $('#create-teams-group,#create-show-only-group,#create-developers-group').hide();
                return;
            }
            $('#create-developers-group').show();
            if (p.type === 'team') {
                $('#create-teams-group,#create-show-only-group').show();
                fillOptions($teamsSel, p.teams || []);
                fillOptions($devsSel, p.developers || []);
            } else {
                $('#create-teams-group,#create-show-only-group').hide();
                fillOptions($teamsSel, []);
                fillOptions($devsSel, p.developers || []);
            }
        }

        function createRefreshDevelopers() {
            const p = projectById($projSel.val());
            if (!p) {
                fillOptions($devsSel, []);
                return;
            }
            const selectedTeamIds = ($teamsSel.val() || []).map(id => parseInt(id));
            const onlyMembers = $showOnly.is(':checked');

            if (!selectedTeamIds.length || !onlyMembers) {
                fillOptions($devsSel, p.developers || []);
                return;
            }
            const map = p.teamMembers || {};
            const mergedById = {};
            selectedTeamIds.forEach(tid => {
                (map[tid] || []).forEach(dev => {
                    if (!mergedById[dev.id]) mergedById[dev.id] = {
                        id: dev.id,
                        name: dev.name,
                        teams: []
                    };
                    (dev.teams || []).forEach(tn => {
                        if (!mergedById[dev.id].teams.includes(tn)) mergedById[dev.id].teams.push(
                            tn);
                    });
                });
            });
            const merged = Object.values(mergedById);
            fillOptions($devsSel, merged);
        }

        $projSel.on('change', onProjectChange);
        $teamsSel.on('change', createRefreshDevelopers);
        $showOnly.on('change', createRefreshDevelopers);

        $('#createTaskModal').on('shown.bs.modal', function() {
            $('#create-project').val('').trigger('change');
            fillOptions($('#create-teams'), []);
            fillOptions($('#create-developers'), []);
            $('#create-show-only-members').prop('checked', true);
            $('#create-teams-group,#create-show-only-group,#create-developers-group').hide();
        });

        // ---------- EDIT (per task) ----------
        window.bindEditTeamsFilter = function(taskId, projectId) {
            const $teams = $(`#edit-teams-${taskId}`);
            const $devs = $(`#edit-developers-${taskId}`);
            const $show = $(`#edit-show-only-members-${taskId}`);
            const p = projectById(projectId);
            if (!p) return;

            // Extra safety: show/hide
            if (p.type === 'team') {
                $(`#edit-teams-group-${taskId},#edit-show-only-group-${taskId}`).show();
            } else {
                $(`#edit-teams-group-${taskId},#edit-show-only-group-${taskId}`).hide();
            }
            $(`#edit-developers-group-${taskId}`).show();

            // Preserve any server-rendered selection
            const initialSelected = ($devs.data('selected') || []).map(String);

            function refresh() {
                const selectedTeamIds = ($teams.val() || []).map(id => parseInt(id));
                const onlyMembers = ($show.length ? $show.is(':checked') : false);

                if (p.type !== 'team' || !selectedTeamIds.length || !onlyMembers) {
                    // hydrate with all eligible and KEEP current selection
                    fillOptions($devs, p.developers || [], true);
                } else {
                    const map = p.teamMembers || {};
                    const mergedById = {};
                    selectedTeamIds.forEach(tid => {
                        (map[tid] || []).forEach(dev => {
                            if (!mergedById[dev.id]) mergedById[dev.id] = {
                                id: dev.id,
                                name: dev.name,
                                teams: []
                            };
                            (dev.teams || []).forEach(tn => {
                                if (!mergedById[dev.id].teams.includes(tn)) mergedById[dev.id]
                                    .teams.push(tn);
                            });
                        });
                    });
                    const merged = Object.values(mergedById);
                    fillOptions($devs, merged, true);
                }

                // Ensure initial selection is applied once (in case options were empty initially)
                if (initialSelected.length) {
                    const now = ($devs.val() || []).map(String);
                    if (!initialSelected.every(v => now.includes(v))) {
                        $devs.val(initialSelected).trigger('change');
                    }
                }
            }

            // Bind changes
            $teams.on('change', refresh);
            if ($show.length) $show.on('change', refresh);

            // Initial hydrate (after server-side options are already in place)
            refresh();
        };

        // ---------- Quill ----------
        const quillCreate = new Quill('#quill-create', {
            theme: 'snow'
        });

        function syncQuillCreate() {
            document.getElementById('body_html_create').value = quillCreate.root.innerHTML;
            document.getElementById('body_json_create').value = JSON.stringify(quillCreate.getContents());
        }
        window.syncQuillCreate = syncQuillCreate;

        const quillEditors = {};
        @foreach ($tasks as $task)
            (function() {
                const id = {{ $task->id }};
                const el = document.getElementById('quill-edit-' + id);
                if (!el) return;
                const q = new Quill(el, {
                    theme: 'snow'
                });
                try {
                    const delta = {!! $task->body_json ? $task->body_json : 'null' !!};
                    if (delta) q.setContents(delta);
                    else q.root.innerHTML = `{!! addslashes($task->body_html ?? '') !!}`;
                } catch (e) {
                    q.root.innerHTML = `{!! addslashes($task->body_html ?? '') !!}`;
                }
                quillEditors[id] = q;
            })();
        @endforeach

        window.syncQuillBeforeSubmit = function(taskId) {
            const q = quillEditors[taskId];
            if (!q) return;
            document.getElementById('body_html_edit_' + taskId).value = q.root.innerHTML;
            document.getElementById('body_json_edit_' + taskId).value = JSON.stringify(q.getContents());
        };

        // ---------- Image upload ----------
        window.uploadTaskImageAndInsert = function(evt, taskId) {
            const file = evt.target.files[0];
            if (!file) return;
            const form = new FormData();
            form.append('file', file);
            form.append('task_id', taskId);
            form.append('_token', '{{ csrf_token() }}');

            fetch(`{{ route('tasks.assets.upload') }}`, {
                    method: 'POST',
                    body: form
                })
                .then(r => r.json())
                .then(data => {
                    if (data && data.url) {
                        const q = quillEditors[taskId];
                        if (!q) return;
                        const range = q.getSelection(true);
                        q.insertEmbed(range ? range.index : 0, 'image', data.url, 'user');
                    } else {
                        alert('Upload failed');
                    }
                })
                .catch(() => alert('Upload error'));
        };
    </script>
@endpush
