{{-- Controller must send: $teams, $users, $developers --}}
{{-- Example:
$teams = Team::with(['users', 'teamLead'])->get();
$users = User::all(); // For members
$developers = Developer::all(); // For team leads
--}}

@extends('admin.layouts.main')
@section('title')
    Teams - HMS Tech & Solutions
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h4 class="page-title mb-0 text-primary fw-bold">Teams Management</h4>
            </div>
            <div class="col-md-6 text-md-end">
                <button class="btn btn-success shadow-sm" id="addTeamBtn">
                    <i class="bi bi-plus-circle"></i> Add Team
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">All Teams</h5>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Team Name</th>
                            <th>Members</th>
                            <th>Team Lead</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teams as $index => $team)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $team->name }}</td>
                                <td>
                                    @foreach ($team->users as $user)
                                        <span class="badge bg-info text-dark">{{ $user->name }}</span>
                                    @endforeach
                                </td>
                                <td>{{ $team->teamLead?->user?->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        @php
                                            $teamPayload = [
                                                'id' => $team->id,
                                                'name' => $team->name,
                                                'team_lead_id' => $team->team_lead_id,
                                                'users' => $team->users
                                                    ->map(
                                                        fn($u) => [
                                                            'id' => $u->id,
                                                            'name' => $u->name,
                                                        ],
                                                    )
                                                    ->values()
                                                    ->toArray(),
                                            ];
                                        @endphp
                                        <button class="btn btn-sm btn-light editTeamBtn"
                                            data-team='@json($teamPayload)' title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>
                                        <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?')">
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
                                <td colspan="5" class="text-center text-muted">No teams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Add/Edit Team Modal --}}
    <div class="modal fade" id="teamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="teamForm" method="POST" action="{{ route('admin.teams.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="teamModalTitle">Add New Team</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Team Name *</label>
                            <input type="text" name="name" id="teamName" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Choose Developers (Members) *</label>
                            <div class="input-group">
                                <select class="form-select" id="userSelect">
                                    <option value="">-- Select Developer --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}">
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="addUserBtn">Add</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Selected Developers</label>
                            <ul id="selectedUsersList" class="list-group"></ul>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Team Lead (From Developers Table)</label>
                            <select name="team_lead_id" id="teamLeadSelect" class="form-select">
                                <option value="">-- Select Team Lead --</option>
                                @foreach ($developers as $dev)
                                    <option value="{{ $dev->id }}">{{ $dev->user->name ?? 'Unnamed Developer' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="formErrors" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Save
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const teamModal = new bootstrap.Modal(document.getElementById('teamModal'));
            const addTeamBtn = document.getElementById('addTeamBtn');
            const editTeamBtns = document.querySelectorAll('.editTeamBtn');
            const form = document.getElementById('teamForm');
            const formMethod = document.getElementById('formMethod');
            const teamModalTitle = document.getElementById('teamModalTitle');
            const teamNameInput = document.getElementById('teamName');
            const userSelect = document.getElementById('userSelect');
            const addUserBtn = document.getElementById('addUserBtn');
            const selectedUsersList = document.getElementById('selectedUsersList');
            const teamLeadSelect = document.getElementById('teamLeadSelect');
            const formErrors = document.getElementById('formErrors');

            function resetForm() {
                form.reset();
                formMethod.value = 'POST';
                form.action = "{{ route('admin.teams.store') }}";
                teamModalTitle.textContent = 'Add New Team';
                selectedUsersList.innerHTML = '';
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
            }

            function addUserToList(userId, userName) {
                if ([...selectedUsersList.querySelectorAll("input")].some(input => input.value == userId)) return;
                const li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center";
                li.innerHTML = `${userName}
            <input type="hidden" name="users[]" value="${userId}">
            <button type="button" class="btn btn-sm btn-outline-danger remove-user">Remove</button>`;
                selectedUsersList.appendChild(li);
            }

            addTeamBtn.addEventListener('click', () => {
                resetForm();
                teamModal.show();
            });

            addUserBtn.addEventListener('click', () => {
                const userId = userSelect.value;
                const userName = userSelect.options[userSelect.selectedIndex]?.dataset.name || '';
                if (!userId) return;
                addUserToList(userId, userName);
                userSelect.value = '';
            });

            selectedUsersList.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-user')) {
                    e.target.parentElement.remove();
                }
            });

            editTeamBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    resetForm();
                    const team = JSON.parse(btn.getAttribute('data-team'));
                    formMethod.value = 'PUT';
                    form.action = `/admin/teams/${team.id}`;
                    teamModalTitle.textContent = 'Edit Team';
                    teamNameInput.value = team.name;
                    if (team.users && team.users.length > 0) {
                        team.users.forEach(user => {
                            addUserToList(user.id, user.name);
                        });
                    }
                    teamLeadSelect.value = team.team_lead_id || '';
                    teamModal.show();
                });
            });

            form.addEventListener('submit', function(e) {
                formErrors.classList.add('d-none');
                formErrors.innerHTML = '';
                if (!teamNameInput.value.trim()) {
                    e.preventDefault();
                    formErrors.textContent = 'Team name is required.';
                    formErrors.classList.remove('d-none');
                    return false;
                }
                if (selectedUsersList.querySelectorAll('input').length === 0) {
                    e.preventDefault();
                    formErrors.textContent = 'Please select at least one user.';
                    formErrors.classList.remove('d-none');
                    return false;
                }
            });
        });
    </script>
@endsection
