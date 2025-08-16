@extends('admin.layouts.main')
@section('title')
Team Manager - HMS Tech  & Solutions
@endsection
@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">👥 Manage Team Managers</h4>
            <button class="btn btn-primary" id="createTeamManagerBtn">
                <i class="bi bi-person-plus"></i> Add Team Manager
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Team Managers Table --}}
        <div class="card">
            <div class="card-header text-white" style="background-color: rgb(2, 2, 100)">Team Manager List</div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Team</th>
                            <th>Phone</th>
                            <th>Experience</th>
                            <th>Skill</th>
                            <th>Profile Image</th>
                            <th>Contract File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamManagers as $manager)
                            <tr>
                                <td>{{ $manager->user->name }}</td>
                                <td>{{ $manager->user->email }}</td>
                                <td>
                                    @forelse($manager->teams as $team)
                                        <span class="badge bg-primary">{{ $team->name }}</span>
                                    @empty
                                        <span class="text-muted">N/A</span>
                                    @endforelse
                                </td>
                                <td>{{ $manager->phone }}</td>
                                <td>{{ $manager->experience }}</td>
                                <td>{{ $manager->skill1 ?? 'N/A' }}</td>
                                <td>
                                    @if ($manager->profile_image)
                                        <img src="{{ asset('storage/' . $manager->profile_image) }}" alt="Profile Image"
                                            style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($manager->contract_file)
                                        <a href="{{ asset('storage/' . $manager->contract_file) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">View Contract</a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        {{-- Edit --}}
                                        {{-- <button class="btn btn-sm btn-light edit-team-manager-btn"
                                            data-id="{{ $manager->id }}" data-user_id="{{ $manager->user_id }}"
                                            data-team_id="{{ $manager->team_id }}" data-phone="{{ $manager->phone }}"
                                            data-experience="{{ $manager->experience }}"
                                            data-skill="{{ $manager->skill }}" title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button> --}}
                                        <button class="btn btn-sm btn-light edit-team-manager-btn"
                                            data-id="{{ $manager->id }}" data-user_id="{{ $manager->user_id }}"
                                            data-teams='@json($manager->teams->pluck('id'))' data-phone="{{ $manager->phone }}"
                                            data-experience="{{ $manager->experience }}"
                                            data-skill1="{{ $manager->skill1 }}" title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('team_managers.destroy', $manager->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this team manager?')">
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
                                <td colspan="9" class="text-center text-muted py-4">No team managers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal: Create/Edit Team Manager --}}
    <div class="modal fade" id="teamManagerModal" tabindex="-1" aria-labelledby="teamManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="teamManagerForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Team Manager</h5>
                        <button type="button" class="btn btn-sm" data-bs-dismiss="modal">
                            <i class="fas fa-times text-dark fs-5"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Team Manager (User)</label>
                            <select id="userSelect" name="user_id" class="form-control" required>
                                <option value="">-- Select Team Manager --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" data-email="{{ $user->email }}">
                                        {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Choose Teams *</label>
                            <div class="input-group">
                                <select class="form-select" id="teamSelect">
                                    <option value="">-- Select Team --</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" data-name="{{ $team->name }}">
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="addTeamBtn">Add</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Selected Teams</label>
                            <ul id="selectedTeamsList" class="list-group"></ul>
                        </div>

                        {{-- Hidden input to store team IDs --}}
                        <input type="hidden" name="team_ids[]" id="teamIdsInput">

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" id="managerEmail" class="form-control" readonly>
                        </div>

                        <div class="mb-3">
                            <label>Phone</label>
                            <input type="text" name="phone" id="managerPhone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label>Experience</label>
                            <input type="text" name="experience" id="managerExperience" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Skill</label>
                            <input type="text" name="skill1" id="managerSkill" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label>Profile Image</label>
                            <input type="file" name="profile_image" class="form-control" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label>Contract File</label>
                            <input type="file" name="contract_file" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Team Manager</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- JS for Modal and Auto-fill --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const teamManagerForm = document.getElementById('teamManagerForm');
            const teamManagerModal = new bootstrap.Modal(document.getElementById('teamManagerModal'));
            const modalTitle = document.querySelector('#teamManagerModal .modal-title');
            const formMethod = document.getElementById('formMethod');

            // Create
            document.getElementById('createTeamManagerBtn').addEventListener('click', function() {
                teamManagerForm.reset();
                formMethod.value = 'POST';
                teamManagerForm.action = "{{ route('team_managers.store') }}";
                modalTitle.textContent = 'Add Team Manager';
                document.getElementById('managerEmail').value = '';
                teamManagerModal.show();
            });

            // Edit
            document.querySelectorAll('.edit-team-manager-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    teamManagerForm.action = "{{ url('admin/team_managers') }}/" + id;
                    formMethod.value = 'PUT';

                    document.getElementById('userSelect').value = this.dataset.user_id || '';
                    document.getElementById('teamSelect').value = this.dataset.team_id || '';

                    // Trigger change to fill email field
                    const select = document.getElementById('userSelect');
                    const option = select.options[select.selectedIndex];
                    document.getElementById('managerEmail').value = option ? option.getAttribute(
                        'data-email') : '';

                    document.getElementById('managerPhone').value = this.dataset.phone || '';
                    document.getElementById('managerExperience').value = this.dataset.experience ||
                        '';
                    document.getElementById('managerSkill').value = this.dataset.skill || '';

                    modalTitle.textContent = 'Edit Team Manager';
                    teamManagerModal.show();
                });
            });

            // Auto-fill email on user select change
            document.getElementById('userSelect').addEventListener('change', function() {
                const email = this.selectedOptions[0]?.getAttribute('data-email') || '';
                document.getElementById('managerEmail').value = email;
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let teamSelect = document.getElementById('teamSelect');
            let selectedTeamsList = document.getElementById('selectedTeamsList');
            let teamIds = [];

            document.getElementById('addTeamBtn').addEventListener('click', function() {
                let teamId = teamSelect.value;
                let teamName = teamSelect.options[teamSelect.selectedIndex].dataset.name;

                if (teamId && !teamIds.includes(teamId)) {
                    teamIds.push(teamId);

                    let li = document.createElement('li');
                    li.className = 'list-group-item d-flex justify-content-between align-items-center';
                    li.innerHTML = `
                ${teamName}
                <button type="button" class="btn btn-sm btn-danger remove-btn" data-id="${teamId}">Remove</button>
                <input type="hidden" name="team_ids[]" value="${teamId}">
            `;

                    selectedTeamsList.appendChild(li);
                }
            });

            selectedTeamsList.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-btn')) {
                    let id = e.target.dataset.id;
                    teamIds = teamIds.filter(t => t !== id);
                    e.target.closest('li').remove();
                }
            });
        });
    </script>

@endsection
