@extends('admin.layouts.main')
@section('title')
    Projects - HMS Tech & Solutions
@endsection
@section('custom_css')
    <style>
        .quill-editor .ql-editor img {
            max-width: 100%;
            height: auto;
            max-height: 380px;
            display: block;
            margin: .5rem auto;
            border-radius: 6px;
        }

        .ql-render img {
            max-width: 100%;
            height: auto;
            max-height: 480px;
            display: block;
            margin: .5rem auto;
            border-radius: 6px;
        }

        .quill-editor {
            max-height: 60vh;
            overflow: auto;
        }

        .sticky-header th {
            position: sticky;
            top: 0;
            z-index: 2;
            /* background-color: #1D2C48 !important;  */
            /* color: #fff !important; */
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-md-6">
                <h4 class="page-title">Projects</h4>
            </div>
            @if (auth()->user()->role === 'admin' ||
                    auth()->user()->role === 'business developer' ||
                    auth()->user()->role === 'team manager')
                <div class="col-md-6 text-md-right">
                    <a href="{{ route('admin.projects.index', ['add' => true]) }}" class="btn btn-primary">Add Project</a>
                </div>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header text-white" style="background-color: #1D2C48">All Projects</div>
            <div class="card-body">
                <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="table-primary sticky-header">
                            <tr>
                                <th>Title</th>
                                <th>File</th>
                                <th>Teams</th>
                                <th>Developers</th>
                                <th>Roles</th>
                                <th>Total Price</th>
                                <th>Paid Price</th>
                                <th>Remaining Price</th>
                                <th>Duration</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Developer End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($projects as $project)
                                <tr>
                                    <td>{{ $project->title }}</td>
                                    <td>
                                        @if ($project->file)
                                            <a href="{{ asset('storage/' . $project->file) }}" target="_blank">View</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        @forelse ($project->teams as $team)
                                            <span class="badge bg-info text-dark">{{ $team->name }}</span>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse ($project->developers as $dev)
                                            <span class="badge bg-secondary">{{ $dev->user->name ?? $dev->id }}</span>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td>
                                        @foreach ($project->memberRoles as $mr)
                                            <span class="badge bg-info">
                                                {{ $mr->developer?->user?->name }} → {{ $mr->role?->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>${{ number_format($project->price, 2) }}</td>
                                    <td>${{ number_format($project->paid_price, 2) }}</td>
                                    <td>
                                        @if ($project->remaining_price >= 0)
                                            ${{ number_format($project->remaining_price, 2) }}
                                        @else
                                            <span class="text-danger">Overpaid</span>
                                        @endif
                                    </td>
                                    <td>{{ $project->duration }}</td>
                                    <td>{{ $project->start_date }}</td>
                                    <td>{{ $project->end_date }}</td>
                                    <td>{{ $project->developer_end_date }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <a href="{{ route('admin.projects.roles.index', $project->id) }}"
                                                class="btn btn-sm btn-light" title="Manage Roles">
                                                <i class="fas fa-user-tag"></i>
                                            </a>
                                            <a href="{{ route('admin.projects.index', ['show_id' => $project->id]) }}"
                                                class="btn btn-sm btn-light" title="View">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                            <a href="{{ route('admin.projects.index', ['edit_id' => $project->id]) }}"
                                                class="btn btn-sm btn-light" title="Edit">
                                                <i class="fas fa-edit text-info"></i>
                                            </a>
                                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST"
                                                style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button onclick="return confirm('Are you sure?')"
                                                    class="btn btn-sm btn-light" title="Delete">
                                                    <i class="fas fa-trash text-danger"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    @if ($showModal)
        <div class="modal fade show d-block" id="projectModal" tabindex="-1" role="dialog"
            style="background-color: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-lg">
                <form id="projectForm" method="POST"
                    action="{{ $editProject ? route('admin.projects.update', $editProject) : route('admin.projects.store') }}"
                    enctype="multipart/form-data">
                    @csrf
                    @if ($editProject)
                        @method('PUT')
                    @endif
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">{{ $editProject ? 'Edit Project' : 'Add Project' }}</h5>
                            <button type="button" class="btn-close" id="closeProjectModalBtn" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" style="max-height: 75vh; overflow-y: auto;">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Title + Price --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Title</label>
                                    <input name="title" class="form-control"
                                        value="{{ old('title', $editProject->title ?? '') }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Total Price</label>
                                    <input name="price" type="number" step="0.01" class="form-control"
                                        value="{{ old('price', $editProject->price ?? '') }}">
                                </div>
                            </div>

                            {{-- Paid + Remaining --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Paid Price</label>
                                    <input name="paid_price" type="number" step="0.01" class="form-control"
                                        value="{{ old('paid_price', $editProject->paid_price ?? 0) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Remaining Price</label><br>
                                    <strong id="remaining-display">$0.00</strong>
                                    <small class="text-muted d-block">Automatically calculated</small>
                                </div>
                            </div>

                            {{-- Client --}}
                            <div class="form-group col-md-6">
                                <label>Client</label>
                                <select name="client_id" class="form-control">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}"
                                            {{ old('client_id', $editProject->client_id ?? '') == $client->id ? 'selected' : '' }}>
                                            {{ $client->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Duration + File --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Duration</label>
                                    <input name="duration" class="form-control"
                                        value="{{ old('duration', $editProject->duration ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>File</label>
                                    <input type="file" name="file" class="form-control">
                                    @if ($editProject && $editProject->file)
                                        <small>Current File: <a href="{{ asset('storage/' . $editProject->file) }}"
                                                target="_blank">View</a></small>
                                    @endif
                                </div>
                            </div>

                            {{-- Dates --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="{{ old('start_date', $editProject->start_date ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control"
                                        value="{{ old('end_date', $editProject->end_date ?? '') }}">
                                </div>
                            </div>

                            {{-- Developer End Date + Type --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Developer End Date</label>
                                    <input type="date" name="developer_end_date" class="form-control"
                                        value="{{ old('developer_end_date', $editProject->developer_end_date ?? '') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option value="team"
                                            {{ old('type', $editProject->type ?? '') == 'team' ? 'selected' : '' }}>Team
                                        </option>
                                        <option value="individual"
                                            {{ old('type', $editProject->type ?? '') == 'individual' ? 'selected' : '' }}>
                                            Individual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-6" id="team-group">
                                <label>Teams</label>
                                <select name="teams[]" class="form-control select2" multiple>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}"
                                            @if (isset($editProject) && $editProject->teams->pluck('id')->contains($team->id)) selected @endif>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6" id="user-group">
                                <label>Developers</label>
                                <select name="developers[]" class="form-control select2" multiple>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            @if (isset($editProject) && $editProject->developers->pluck('id')->contains($user->id)) selected @endif>
                                            {{ $user->user->name ?? $user->id }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- RICH TEXT: Project Brief / Description --}}
                            <div class="form-group">
                                <label>Project Brief / Description</label>
                                <div id="quill-project" class="quill-editor border rounded" style="min-height:180px;">
                                </div>
                                <input type="hidden" name="body_html" id="project_body_html">
                                <input type="hidden" name="body_json" id="project_body_json">
                                @if (!$editProject)
                                    <small class="text-muted d-block mt-1">
                                        Tip: Save the project first, then re-open Edit to upload and insert images.
                                    </small>
                                @endif
                            </div>

                            {{-- Image upload (enabled only for EDIT) --}}
                            <div class="form-group">
                                <label>Insert Image</label>
                                <input type="file" class="form-control-file" accept="image/*"
                                    onchange="uploadProjectImageAndInsert(event)" {{ $editProject ? '' : 'disabled' }}>
                                @if ($editProject)
                                    <small class="text-muted">Images will upload and insert at the cursor.</small>
                                @endif
                            </div>
                            @if (isset($editProject))
                                <div id="project-asset-list" class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach ($editProject->images as $img)
                                        <div class="position-relative me-2 mb-2">
                                            <img src="{{ $img->url }}" alt=""
                                                style="max-width:120px;max-height:90px;border-radius:6px;">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute"
                                                style="top:2px; right:2px"
                                                onclick="deleteProjectImage({{ $img->id }}, this)">×</button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            {{-- Project Get By --}}
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Project Get By</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="project_get_by"
                                            id="getByAdmin" value="admin"
                                            {{ old('project_get_by', empty($editProject->business_developer_id) ? 'admin' : '') == 'admin' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="getByAdmin">Admin</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="project_get_by"
                                            id="getByBD" value="business_developer"
                                            {{ old('project_get_by', !empty($editProject->business_developer_id) ? 'business_developer' : '') == 'business_developer' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="getByBD">Business Developer</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3" id="businessDeveloperSelect" style="display: none;">
                                <label for="business_developer_id" class="form-label">Business Developer</label>
                                <select name="business_developer_id" id="business_developer_id" class="form-control">
                                    <option value="">Select Business Developer</option>
                                    @foreach ($businessDevelopers as $bd)
                                        <option value="{{ $bd->id }}"
                                            {{ isset($editProject) && $editProject->business_developer_id == $bd->id ? 'selected' : '' }}>
                                            {{ $bd->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"
                                class="btn btn-success">{{ $editProject ? 'Update' : 'Create' }}</button>
                            <button type="button" class="btn btn-secondary" id="cancelProjectModalBtn">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Show Modal --}}
    @if ($showProject)
        <div class="modal fade show d-block" id="projectShowModal" tabindex="-1" role="dialog"
            style="background-color: rgba(0,0,0,0.6); z-index: 1050;">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <div class="modal-header bg-primary text-white rounded-top">
                        <h5 class="modal-title fw-bold">📂 Project Details</h5>
                        <button type="button" class="btn-close btn-close-white" id="closeShowProjectModalBtn">x</button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>📌 Title:</strong> {{ $showProject->title }}</p>
                                @if ($showProject->type === 'individual' && $showProject->developers->count())
                                    <p><strong>👨‍💻 Developers:</strong>
                                        @foreach ($showProject->developers as $dev)
                                            <span class="badge bg-secondary">{{ $dev->user->name ?? $dev->id }}</span>
                                        @endforeach
                                    </p>
                                @elseif ($showProject->type === 'team' && $showProject->teams->count())
                                    <p><strong>👥 Teams:</strong>
                                        @foreach ($showProject->teams as $team)
                                            <span class="badge bg-info text-dark">{{ $team->name }}</span>
                                        @endforeach
                                    </p>
                                @else
                                    <p><strong>👥/👨‍💻 Assigned To:</strong> N/A</p>
                                @endif
                                <p><strong>⏳ Duration:</strong> {{ $showProject->duration ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>💲 Total Price:</strong> ${{ number_format($showProject->price, 2) }}</p>
                                <p><strong>✅ Paid:</strong> ${{ number_format($showProject->paid_price, 2) }}</p>
                                <p><strong>💰 Remaining:</strong>
                                    @if ($showProject->remaining_price >= 0)
                                        ${{ number_format($showProject->remaining_price, 2) }}
                                    @else
                                        <span class="badge bg-danger">Overpaid</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p><strong>📅 Start Date:</strong> {{ $showProject->start_date }}</p>
                                <p><strong>📅 End Date:</strong> {{ $showProject->end_date }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>🛠 Developer End Date:</strong> {{ $showProject->developer_end_date }}</p>
                                @if ($showProject->business_developer_id)
                                    <p><strong>📥 Project Get By:</strong> {{ $showProject->businessDeveloper->name }}</p>
                                @else
                                    <p><strong>📥 Project Get By:</strong> Admin</p>
                                @endif
                            </div>
                        </div>
                        @if ($showProject->file)
                            <p><strong>📎 File:</strong>
                                <a href="{{ asset('storage/' . $showProject->file) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">View File</a>
                            </p>
                        @endif
                        {{-- RICH TEXT (read-only) --}}
                        @if ($showProject->body_html)
                            <div class="mb-3">
                                <strong>Description</strong>
                                <div class="ql-render border rounded p-3" style="min-height:120px;">
                                    {!! $showProject->body_html !!}
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer bg-light rounded-bottom">
                        <button type="button" class="btn btn-secondary px-4"
                            id="cancelShowProjectModalBtn">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@push('custom_js')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select options",
                allowClear: true,
                width: '100%'
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Business Developer select
            const adminRadio = document.getElementById('getByAdmin');
            const bdRadio = document.getElementById('getByBD');
            const bdSelectDiv = document.getElementById('businessDeveloperSelect');

            function toggleBDSelect() {
                if (bdRadio && bdRadio.checked) {
                    bdSelectDiv.style.display = 'block';
                } else {
                    bdSelectDiv.style.display = 'none';
                    const bdSelect = document.getElementById('business_developer_id');
                    if (bdSelect) bdSelect.value = '';
                }
            }
            if (adminRadio && bdRadio) {
                adminRadio.addEventListener('change', toggleBDSelect);
                bdRadio.addEventListener('change', toggleBDSelect);
                toggleBDSelect();
            }

            // Close modal buttons
            ['closeProjectModalBtn', 'cancelProjectModalBtn', 'closeShowProjectModalBtn',
                'cancelShowProjectModalBtn'
            ]
            .forEach(id => {
                const btn = document.getElementById(id);
                if (btn) {
                    btn.addEventListener('click', function() {
                        window.location.href = "{{ route('admin.projects.index') }}";
                    });
                }
            });

            // Remaining calculation
            function calculateRemaining() {
                const total = parseFloat(document.querySelector('[name="price"]')?.value) || 0;
                const paid = parseFloat(document.querySelector('[name="paid_price"]')?.value) || 0;
                const remaining = Math.max(total - paid, 0);
                if (document.getElementById('remaining-display')) {
                    document.getElementById('remaining-display').innerText = '$' + remaining.toFixed(2);
                }
            }
            document.querySelector('[name="price"]')?.addEventListener('input', calculateRemaining);
            document.querySelector('[name="paid_price"]')?.addEventListener('input', calculateRemaining);

            // Toggle teams/developers based on type
            function toggleFields() {
                const type = document.getElementById('type')?.value;
                if (document.getElementById('team-group')) {
                    document.getElementById('team-group').style.display = (type === 'team') ? 'block' : 'none';
                }
                if (document.getElementById('user-group')) {
                    document.getElementById('user-group').style.display = (type === 'individual') ? 'block' :
                        'none';
                }
            }
            toggleFields();
            document.getElementById('type')?.addEventListener('change', toggleFields);
            // Upload & insert (Edit only)
            // --------- Quill init (single modal) ----------
            let quillProject = null;
            const qEl = document.getElementById('quill-project');
            if (qEl && typeof Quill !== 'undefined') {
                quillProject = new Quill('#quill-project', {
                    theme: 'snow'
                });

                // Seed content when editing
                @if (!empty($editProject))
                    try {
                        const delta = {!! $editProject->body_json ? $editProject->body_json : 'null' !!};
                        if (delta) quillProject.setContents(delta);
                        else quillProject.root.innerHTML = `{!! addslashes($editProject->body_html ?? '') !!}`;
                    } catch (e) {
                        quillProject.root.innerHTML = `{!! addslashes($editProject->body_html ?? '') !!}`;
                    }
                @endif

                // Sync on submit
                const form = document.getElementById('projectForm');
                if (form) {
                    form.addEventListener('submit', function() {
                        document.getElementById('project_body_html').value = quillProject.root.innerHTML;
                        document.getElementById('project_body_json').value = JSON.stringify(quillProject
                            .getContents());
                    });
                }
            }

            // --------- Upload & insert image (Edit only) ----------
            window.uploadProjectImageAndInsert = function(evt) {
                const file = evt.target.files[0];
                if (!file) return;

                @if (empty($editProject))
                    alert('Please save the project first, then re-open Edit to insert images.');
                    evt.target.value = '';
                    return;
                @endif

                if (!quillProject) {
                    alert('Editor not ready yet.');
                    return;
                }

                const form = new FormData();
                form.append('file', file);
                form.append('project_id', {{ $editProject->id ?? 'null' }});
                form.append('_token', '{{ csrf_token() }}');

                fetch(`{{ route('projects.assets.upload') }}`, {
                        method: 'POST',
                        body: form
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.url) {
                            const range = quillProject.getSelection(true);
                            quillProject.insertEmbed(range ? range.index : 0, 'image', data.url, 'user');
                            evt.target.value = '';
                        } else {
                            alert('Upload failed');
                        }
                    })
                    .catch(() => alert('Upload error'));
            };
            window.deleteProjectImage = function(assetId, btnEl) {
                if (!confirm('Delete this image?')) return;
                fetch(`{{ route('projects.assets.destroy', ':id') }}`.replace(':id', assetId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(res => {
                    if (res.ok) {
                        // remove the thumb from DOM
                        const card = btnEl.closest('.position-relative');
                        if (card) card.remove();
                    } else {
                        alert('Failed to delete image.');
                    }
                }).catch(() => alert('Delete error.'));
            };
        });
    </script>
@endpush
