@extends('admin.layouts.main')
@section('title')
Client - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">👥 Manage Clients</h4>
        <button class="btn btn-primary" id="createClientBtn">
            <i class="bi bi-person-plus"></i> Add Client
        </button>
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

    {{-- Clients Table --}}
    <div class="card">
        <div class="card-header text-white" style="background-color: rgb(2, 2, 100)">Client List</div>
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>{{ $client->user->name }}</td>
                            <td>{{ $client->user->email }}</td>
                            <td>{{ $client->phone }}</td>
                            <td>{{ ucfirst($client->gender) }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-1">
                                    {{-- View --}}
                                    <button 
                                      class="btn btn-sm btn-light view-client-btn" 
                                      data-name="{{ $client->user->name }}"
                                      data-email="{{ $client->user->email }}"
                                      data-phone="{{ $client->phone }}"
                                      data-gender="{{ ucfirst($client->gender) }}"
                                      title="View"
                                    >
                                      <i class="fas fa-eye text-primary"></i>
                                    </button>

                                    {{-- Edit --}}
                                    <button 
                                      class="btn btn-sm btn-light edit-client-btn"
                                      data-id="{{ $client->id }}"
                                      data-user_id="{{ $client->user_id }}"
                                      data-phone="{{ $client->phone }}"
                                      data-gender="{{ $client->gender }}"
                                      title="Edit"
                                    >
                                      <i class="fas fa-edit text-info"></i>
                                    </button>

                                    {{-- Delete --}}
                                    <form 
                                      action="{{ route('clients.destroy', $client->id) }}" 
                                      method="POST" 
                                      class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this client?')"
                                    >
                                      @csrf 
                                      @method('DELETE')
                                      <button 
                                        type="submit" 
                                        class="btn btn-sm btn-light" 
                                        title="Delete"
                                      >
                                        <i class="fas fa-trash text-danger"></i>
                                      </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Create/Edit Client --}}
<div class="modal fade" id="clientModal" tabindex="-1" aria-labelledby="clientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="clientForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Client</h5>
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal">
                        <i class="fas fa-times text-dark fs-5"></i>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label>Client</label>
                      <select id="clientSelect" name="user_id" class="form-control" required>
    <option value="">-- Select Client --</option>
    @foreach($users as $user)
        <option value="{{ $user->id }}" data-email="{{ $user->email }}">{{ $user->name }}</option>
    @endforeach
</select>

                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" id="clientEmail" class="form-control" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Phone</label>
                        <input type="text" name="phone" id="clientPhone" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Gender</label>
                        <select name="gender" id="clientGender" class="form-select" required>
                            <option value="">-- Select Gender --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Client</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal: View Client --}}
<div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Client Info</h5>
                <button type="button" class="btn btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times text-dark fs-5"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Name:</strong> <span id="viewName"></span></div>
                <div class="mb-2"><strong>Email:</strong> <span id="viewEmail"></span></div>
                <div class="mb-2"><strong>Phone:</strong> <span id="viewPhone"></span></div>
                <div class="mb-2"><strong>Gender:</strong> <span id="viewGender"></span></div>
            </div>
        </div>
    </div>
</div>

{{-- JS for Modal and Auto-fill --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const clientForm = document.getElementById('clientForm');
    const clientModal = new bootstrap.Modal(document.getElementById('clientModal'));
    const modalTitle = document.querySelector('#clientModal .modal-title');
    const formMethod = document.getElementById('formMethod');

    // Create
    document.getElementById('createClientBtn').addEventListener('click', function () {
        clientForm.reset();
        formMethod.value = 'POST';
        clientForm.action = "{{ route('clients.store') }}";
        modalTitle.textContent = 'Add Client';
        document.getElementById('clientEmail').value = '';
        clientModal.show();
    });

    // Edit
    document.querySelectorAll('.edit-client-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            clientForm.action = "{{ url('admin/clients') }}/" + id;
            formMethod.value = 'PUT';

            document.getElementById('clientSelect').value = this.dataset.user_id || '';

            // Trigger change to fill email field
            const select = document.getElementById('clientSelect');
            const option = select.options[select.selectedIndex];
            document.getElementById('clientEmail').value = option ? option.getAttribute('data-email') : '';

            document.getElementById('clientPhone').value = this.dataset.phone || '';
            document.getElementById('clientGender').value = this.dataset.gender || '';

            modalTitle.textContent = 'Edit Client';
            clientModal.show();
        });
    });

    // View
    document.querySelectorAll('.view-client-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('viewName').innerText = this.dataset.name;
            document.getElementById('viewEmail').innerText = this.dataset.email;
            document.getElementById('viewPhone').innerText = this.dataset.phone;
            document.getElementById('viewGender').innerText = this.dataset.gender;
            new bootstrap.Modal(document.getElementById('viewClientModal')).show();
        });
    });

    // Auto-fill email on client select change
    document.getElementById('clientSelect').addEventListener('change', function () {
        const email = this.selectedOptions[0]?.getAttribute('data-email') || '';
        document.getElementById('clientEmail').value = email;
    });
});
</script>
@endsection
