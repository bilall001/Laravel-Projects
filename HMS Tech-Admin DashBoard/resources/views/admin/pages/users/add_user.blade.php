@extends('admin.layouts.main')
@section('title')
All Users - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">👥 Manage Users</h4>
        @if(auth()->user()->role === 'admin')
        <button class="btn btn-primary" id="createUserBtn">
            <i class="bi bi-person-plus"></i> Add User
        </button>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Users Table --}}
    <div class="card">
        <div class="card-header text-white" style="background-color: rgb(2, 2, 100)">User List</div>
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    {{-- View --}}
                                    <button 
                                        class="btn btn-sm btn-light view-user-btn" 
                                        data-name="{{ $user->name }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-role="{{ ucfirst($user->role) }}"
                                        title="View"
                                    >
                                        <i class="fas fa-eye text-primary"></i>
                                    </button>

                                    {{-- Edit + Delete --}}
                                    @if(auth()->user()->role === 'admin')
                                        <button 
                                            class="btn btn-sm btn-light edit-user-btn"
                                            data-id="{{ $user->id }}"
                                            data-url="{{ route('add-users.update', $user->id) }}"
                                            data-name="{{ $user->name }}"
                                            data-username="{{ $user->username }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->role }}"
                                            title="Edit"
                                        >
                                            <i class="fas fa-edit text-info"></i>
                                        </button>

                                        <form 
                                            action="{{ route('add-users.destroy', $user->id) }}" 
                                            method="POST" 
                                            class="d-inline" 
                                            onsubmit="return confirm('Are you sure?')"
                                        >
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Create/Edit User --}}
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="userForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="user_id" id="userId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add User</h5>
                    <button type="button" id="closeModalBtn" class="btn btn-sm" aria-label="Close">
                        <i class="fas fa-times text-dark fs-5"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label>Name</label>
                        <input type="text" name="name" id="userName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="userUsername" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" id="userEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>New Password (optional)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="userRole" class="form-select" required>
                            <option value="">-- Select Role --</option>
                            @foreach(['admin', 'developer', 'client', 'team manager', 'business developer', 'partner'] as $role)
                                <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save User</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: View User --}}
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">User Info</h5>
                    <button type="button" id="closeModalBtn" class="btn btn-sm" aria-label="Close">
                        <i class="fas fa-times text-dark fs-5"></i>
                    </button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Name:</strong> <span id="viewName"></span></div>
                <div class="mb-2"><strong>Username:</strong> <span id="viewUsername"></span></div>
                <div class="mb-2"><strong>Email:</strong> <span id="viewEmail"></span></div>
                <div class="mb-2"><strong>Role:</strong> <span id="viewRole"></span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- JS --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById('userForm');
    const userModalEl = document.getElementById('userModal');
    const userModal = new bootstrap.Modal(userModalEl);
    const modalTitle = document.querySelector('#userModal .modal-title');
    const formMethodInput = document.getElementById('formMethod');

    // Reset form when modal is closed
    userModalEl.addEventListener('hidden.bs.modal', function () {
        userForm.reset();
        formMethodInput.value = 'POST';
        userForm.action = "{{ route('add-users.store') }}";
    });

    // Create button
    document.getElementById('createUserBtn')?.addEventListener('click', function () {
        userForm.reset();
        formMethodInput.value = 'POST';
        userForm.action = "{{ route('add-users.store') }}";
        document.getElementById('userId').value = '';
        modalTitle.textContent = 'Add User';
        userModal.show();
    });

    // Edit user
    document.querySelectorAll('.edit-user-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            const url = this.dataset.url;
            userForm.action = url;
            formMethodInput.value = 'PUT';

            document.getElementById('userId').value = id;
            document.getElementById('userName').value = this.dataset.name;
            document.getElementById('userUsername').value = this.dataset.username;
            document.getElementById('userEmail').value = this.dataset.email;
            document.getElementById('userRole').value = this.dataset.role;

            modalTitle.textContent = 'Edit User';
            userModal.show();
        });
    });

    // View user
    document.querySelectorAll('.view-user-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('viewName').innerText = this.dataset.name;
            document.getElementById('viewUsername').innerText = this.dataset.username;
            document.getElementById('viewEmail').innerText = this.dataset.email;
            document.getElementById('viewRole').innerText = this.dataset.role;

            new bootstrap.Modal(document.getElementById('viewUserModal')).show();
        });
    });

    // Get CSRF
    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : document.querySelector('input[name="_token"]').value;
    }

    // AJAX Submit
    userForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const formData = new FormData(userForm);
        const url = userForm.action;
        const csrf = getCsrfToken();

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData,
                credentials: 'same-origin'
            });

            if (response.ok) {
                userModal.hide();
                setTimeout(() => location.reload(), 200);
                return;
            }

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const data = await response.json();
                const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || JSON.stringify(data));
                alert(errors);
            } else {
                const text = await response.text();
                console.error('Server error:', text);
                alert('Server error: ' + (text.slice(0, 300) || response.statusText));
            }
        } catch (err) {
            console.error("AJAX error:", err);
            alert("Something went wrong.");
        }
    });

    // Close modal button
    document.getElementById('closeModalBtn').addEventListener('click', function() {
        userModal.hide();
    });
});
</script>
@endsection
