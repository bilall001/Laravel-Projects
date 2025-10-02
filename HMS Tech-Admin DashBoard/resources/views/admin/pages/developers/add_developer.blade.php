@extends('admin.layouts.main')
@section('title')
    Developers - HMS Tech & Solutions
@endsection
@section('content')
    <div class="container-fluid mt-3">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">💻 Manage Developers</h4>
            @if (auth()->user()->role === 'admin' ||
                    auth()->user()->role === 'business developer' ||
                    auth()->user()->role === 'team manager')
                <button class="btn btn-primary" id="createDeveloperBtn">
                    <i class="bi bi-plus-circle"></i> Add Developer
                </button>
            @endif
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Developer Table --}}
        <div class="card">
            <div class="card-header bg-dark text-white">Developer List</div>
            <div class="card-body table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Name</th>
                            <th>Skills</th>
                            <th>Experience</th>
                            <th>Work Type</th>
                            <th>Salary</th>
                            @if (auth()->user()->role === 'admin' ||
                                    auth()->user()->role === 'business developer' ||
                                    auth()->user()->role === 'team manager')
                                <th class="text-center">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($developers as $developer)
                            <tr>
                                <td>{{ $developer->user->name ?? '-' }}</td>
                                <td>{{ $developer->skill ?? '-' }}</td>
                                <td>{{ $developer->experience ?? '-' }}</td>
                                <td>
                                    @if ($developer->part_time)
                                        <span class="badge bg-info">Part Time</span>
                                    @endif
                                    @if ($developer->full_time)
                                        <span class="badge bg-success">Full Time</span>
                                    @endif
                                    @if ($developer->internship)
                                        <span class="badge bg-warning text-dark">Internship</span>
                                    @endif
                                    @if ($developer->job)
                                        <span class="badge bg-primary">Job</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($developer->salary_type === 'salary_based')
                                        <span class="badge bg-success">Salary Based</span><br>
                                        ₨{{ number_format($developer->salary, 2) }}
                                    @elseif ($developer->salary_type === 'project_based')
                                        <span class="badge bg-info">Project Based</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{-- <button class="btn btn-sm btn-outline-info view-dev-btn" data-dev='@json($developer)'>
                                    <i class="fas fa-eye"></i>
                                </button> --}}
                                    @if (auth()->user()->role === 'admin' ||
                                            auth()->user()->role === 'business developer' ||
                                            auth()->user()->role === 'team manager')
                                        <button class="btn btn-sm btn-outline-primary edit-dev-btn"
                                            data-dev='@json($developer)'>
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('developers.destroy', $developer->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i
                                                    class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No developers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add/Edit Developer Modal --}}
        <div class="modal fade" id="developerModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="POST" id="developerForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="devModalTitle">Add Developer</h5>
                            <button type="button" id="closeModalBtn" class="btn btn-sm" aria-label="Close"
                                data-bs-dismiss="modal">
                                <i class="fas fa-times text-dark fs-5"></i>
                            </button>
                        </div>
                        <div class="modal-body row g-3">
                            <div class="col-md-6">
                                <label for="addUserSelect" class="form-label">Select Developer User</label>
                                <select name="add_user_id" class="form-control" id="addUserSelect" required>
                                    <option value="">-- Select Developer --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Skills</label>
                                <input type="text" name="skill" id="skill" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Experience</label>
                                <input type="text" name="experience" id="experience" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Work Type</label>
                                <div class="d-flex flex-wrap gap-4">
                                    <div>
                                        <label class="form-label d-block">Time</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="time_type" id="part_time"
                                                value="part_time">
                                            <label class="form-check-label" for="part_time">Part Time</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="time_type" id="full_time"
                                                value="full_time">
                                            <label class="form-check-label" for="full_time">Full Time</label>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label d-block">Type</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="job_type" id="internship"
                                                value="internship">
                                            <label class="form-check-label" for="internship">Internship</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="job_type"
                                                id="job" value="job">
                                            <label class="form-check-label" for="job">Job</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Salary Type</label>
                                <select name="salary_type" id="salaryType" class="form-control">
                                    <option value="">-- Select Type --</option>
                                    <option value="salary_based">Salary Based</option>
                                    <option value="project_based">Project Based</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="salaryWrapper" style="display:none;">
                                <label>Salary</label>
                                <input type="number" name="salary" id="salary" step="0.01"
                                    class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label>Profile Image</label>
                                <input type="file" name="profile_image" class="form-control">
                                <div id="profileImagePreview" class="mt-2"></div>
                            </div>

                            <div class="col-md-6">
                                <label>CNIC Front</label>
                                <input type="file" name="cnic_front" class="form-control">
                                <div id="cnicFrontPreview" class="mt-2"></div>
                            </div>

                            <div class="col-md-6">
                                <label>CNIC Back</label>
                                <input type="file" name="cnic_back" class="form-control">
                                <div id="cnicBackPreview" class="mt-2"></div>
                            </div>

                            <div class="col-md-6">
                                <label>Contract File</label>
                                <input type="file" name="contract_file" class="form-control">
                                <div id="contractFilePreview" class="mt-2"></div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Developer</button>
                            <button type="button" id="cancelModalBtn" class="btn btn-secondary">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- View Developer Modal --}}
        <div class="modal fade" id="viewDeveloperModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content p-3">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Developer Details</h5>
                        <button type="button" id="closeModalBtn" class="btn btn-sm" aria-label="Close"
                            data-bs-dismiss="modal">
                            <i class="fas fa-times text-dark fs-5"></i>
                        </button>
                    </div>
                    <div class="modal-body row g-3" id="viewDeveloperContent"></div>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const devModalEl = document.getElementById('developerModal');
            const devModal = new bootstrap.Modal(devModalEl);
            const viewModalEl = document.getElementById('viewDeveloperModal');
            const viewModal = new bootstrap.Modal(viewModalEl);

            const form = document.getElementById('developerForm');
            const formMethod = document.getElementById('formMethod');
            const salaryWrapper = document.getElementById('salaryWrapper');
            const salaryType = document.getElementById('salaryType');

            salaryType.addEventListener('change', () => {
                salaryWrapper.style.display = salaryType.value === 'salary_based' ? 'block' : 'none';
            });

            // Create Developer
            document.getElementById('createDeveloperBtn').addEventListener('click', () => {
                form.reset();
                formMethod.value = 'POST';
                document.getElementById('devModalTitle').innerText = 'Add Developer';
                salaryWrapper.style.display = 'none';
                form.action = "{{ route('developers.store') }}";
                devModal.show();
            });

            // Edit Developer
            document.querySelectorAll('.edit-dev-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const dev = JSON.parse(btn.dataset.dev);
                    form.reset();
                    formMethod.value = 'PUT';
                    document.getElementById('devModalTitle').innerText = 'Edit Developer';
                    form.action = `/developers/${dev.id}`;

                    document.getElementById('addUserSelect').value = dev.user_id || (dev.user ? dev
                        .user.id : '');
                    document.getElementById('skill').value = dev.skill ?? '';
                    document.getElementById('experience').value = dev.experience ?? '';
                    salaryType.value = dev.salary_type || '';
                    salaryWrapper.style.display = salaryType.value === 'salary_based' ? 'block' :
                        'none';
                    document.getElementById('salary').value = dev.salary ?? '';

                    document.getElementById('part_time').checked = !!dev.part_time;
                    document.getElementById('full_time').checked = !!dev.full_time;
                    document.getElementById('internship').checked = !!dev.internship;
                    document.getElementById('job').checked = !!dev.job;

                    devModal.show();
                });
            });

            /// View Developer
            document.querySelectorAll('.view-dev-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const dev = JSON.parse(btn.dataset.dev);
                    let workTypes = [];
                    if (dev.part_time) workTypes.push('Part Time');
                    if (dev.full_time) workTypes.push('Full Time');
                    if (dev.internship) workTypes.push('Internship');
                    if (dev.job) workTypes.push('Job');

                    const html = `
            <div class="col-md-6">
                <label class="form-label fw-bold">Name</label>
                <input type="text" class="form-control" value="${dev.user?.name || '-'}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Skills</label>
                <input type="text" class="form-control" value="${dev.skill || '-'}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Experience</label>
                <input type="text" class="form-control" value="${dev.experience || '-'}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Work Types</label>
                <input type="text" class="form-control" value="${workTypes.join(', ') || '-'}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Salary</label>
                <input type="text" class="form-control" value="${dev.salary ? '₨' + parseFloat(dev.salary).toFixed(2) : '-'}" readonly>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Profile Image</label>
                ${dev.profile_image ? `<img src="/${dev.profile_image}" class="img-fluid rounded" alt="Profile Image">` : '-'}
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">CNIC Front</label>
                ${dev.cnic_front ? `<img src="/${dev.cnic_front}" class="img-fluid rounded" alt="CNIC Front">` : '-'}
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">CNIC Back</label>
                ${dev.cnic_back ? `<img src="/${dev.cnic_back}" class="img-fluid rounded" alt="CNIC Back">` : '-'}
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Contract</label>
                ${dev.contract_file ? `<a href="/${dev.contract_file}" target="_blank">View Contract</a>` : '-'}
            </div>
        `;

                    document.getElementById('viewDeveloperContent').innerHTML = html;
                    viewModal.show();
                });
            });


            // ✅ Close Add/Edit modal (❌ & Cancel)
            document.querySelectorAll('#developerModal #closeModalBtn, #developerModal #cancelModalBtn')
                .forEach(btn => btn.addEventListener('click', () => devModal.hide()));

            // ✅ Close View modal (❌ & Cancel)
            document.querySelectorAll('#viewDeveloperModal #closeModalBtn, #viewDeveloperModal #cancelModalBtn')
                .forEach(btn => btn.addEventListener('click', () => viewModal.hide()));
        });


        // ✅ Close View modal (❌ & Cancel)
        document.querySelectorAll('#viewDeveloperModal #closeModalBtn, #viewDeveloperModal #cancelModalBtn')
            .forEach(btn => btn.addEventListener('click', () => viewModal.hide()));
    </script>
@endsection
