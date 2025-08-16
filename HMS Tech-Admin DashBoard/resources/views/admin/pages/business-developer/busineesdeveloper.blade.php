@extends('admin.layouts.main')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page-title">💼 Business Developers</h4>
        <button class="btn btn-primary" id="createBizDevBtn">
            <i class="bi bi-plus-circle"></i> Add Business Developer
        </button>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Developers Table --}}
    <div class="card">
        <div class="card-header bg-dark text-white">Business Developer List</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Percentage</th>
                        <th>Image</th>
                        <th>CNIC</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($developers as $dev)
                        <tr>
                            <td>{{ $dev->addUser->name ?? '-' }}</td>
                            <td>{{ $dev->phone ?? '-' }}</td>
                            <td>{{ ucfirst($dev->gender ?? '-') }}</td>
                            <td>{{ $dev->percentage ?? '-' }}%</td>
                            <td>
                                @if($dev->image)
                                    <img src="{{ Storage::url($dev->image) }}" alt="image" class="img-thumbnail" width="50">
                                @endif
                            </td>
                            <td>
                                @if($dev->cnic_front && $dev->cnic_back)
                                    <img src="{{ Storage::url($dev->cnic_front) }}" alt="CNIC Front" width="50">
                                    <img src="{{ Storage::url($dev->cnic_back) }}" alt="CNIC Back" width="50">
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-info view-bizdev-btn" data-dev='@json($dev)'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary edit-bizdev-btn" data-dev='@json($dev)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="{{ route('business-developers.destroy', $dev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No business developers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add/Edit Modal --}}
    <div class="modal fade" id="bizDevModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="bizDevForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="bizDevFormMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="bizDevModalTitle">Add Business Developer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body row g-3">
                        <div class="col-md-6">
                            <label>Select BusinessDeveloper</label>
                            <select name="add_user_id" class="form-select" required>
                                <option value="">-- Select BusinessDeveloper --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Phone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label>Gender</label>
                            <select name="gender" class="form-select">
                                <option value="">-- Select Gender --</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label>Percentage</label>
                            <input type="number" name="percentage" class="form-control" min="0" max="100">
                        </div>

                        <div class="col-md-4">
                            <label>Profile Image</label>
                            <input type="file" name="image" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>CNIC Front</label>
                            <input type="file" name="cnic_front" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>CNIC Back</label>
                            <input type="file" name="cnic_back" class="form-control">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save Business Developer</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- View Modal --}}
    <div class="modal fade" id="viewBizDevModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content p-3">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Business Developer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewBizDevContent"></div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bizDevModal = new bootstrap.Modal(document.getElementById('bizDevModal'));
    const viewBizModal = new bootstrap.Modal(document.getElementById('viewBizDevModal'));
    const form = document.getElementById('bizDevForm');
    const formMethod = document.getElementById('bizDevFormMethod');

    document.getElementById('createBizDevBtn').addEventListener('click', () => {
        form.reset();
        formMethod.value = 'POST';
        document.getElementById('bizDevModalTitle').innerText = 'Add Business Developer';
        form.action = "{{ route('business-developers.store') }}";
        bizDevModal.show();
    });

    document.querySelectorAll('.edit-bizdev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const dev = JSON.parse(btn.dataset.dev);
            form.reset();
            formMethod.value = 'PUT';
            document.getElementById('bizDevModalTitle').innerText = 'Edit Business Developer';
            form.action = `/admin/business-developers/${dev.id}`;
            form.elements['add_user_id'].value = dev.add_user_id || '';
            form.elements['phone'].value = dev.phone || '';
            form.elements['gender'].value = dev.gender || '';
            form.elements['percentage'].value = dev.percentage || '';
            bizDevModal.show();
        });
    });

    document.querySelectorAll('.view-bizdev-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const dev = JSON.parse(btn.dataset.dev);
            const html = `
                <p><strong>Name:</strong> ${dev.add_user?.name || '-'}</p>
                <p><strong>Phone:</strong> ${dev.phone || '-'}</p>
                <p><strong>Gender:</strong> ${dev.gender || '-'}</p>
                <p><strong>Percentage:</strong> ${dev.percentage || '-'}%</p>
                <p><strong>Image:</strong> ${dev.image ? `<img src="/storage/${dev.image}" width="100">` : '-'}</p>
                <p><strong>CNIC Front:</strong> ${dev.cnic_front ? `<img src="/storage/${dev.cnic_front}" width="100">` : '-'}</p>
                <p><strong>CNIC Back:</strong> ${dev.cnic_back ? `<img src="/storage/${dev.cnic_back}" width="100">` : '-'}</p>
            `;
            document.getElementById('viewBizDevContent').innerHTML = html;
            viewBizModal.show();
        });
    });
});
</script>
@endsection
