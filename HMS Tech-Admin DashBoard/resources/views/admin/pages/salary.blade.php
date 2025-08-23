@extends('admin.layouts.main')
@section('title')
Salaries - HMS Tech & Solutions
@endsection

@section('content')
<div class="container-fluid">
    <h4 class="page-title mb-4">💰 Salary Management</h4>

    {{-- Filter --}}
    <form method="GET" class="form-inline mb-3">
        <input type="month" name="month" class="form-control me-2" value="{{ $month }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pay button --}}
    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#salaryModal">
        <i class="fas fa-plus"></i> Pay Salary
    </button>

    <div class="card">
        <div class="card-header text-white" style="background-color: #1D2C48">All Salaries</div>
        <div class="card-body table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Developer</th>
                        <th>Email</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Receipt</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaries as $salary)
                        <tr>
                            <td>{{ $salary->addUser->name ?? '-' }}</td>
                            <td>{{ $salary->addUser->email ?? '-' }}</td>
                            <td>{{ $salary->salary_date }}</td>
                            <td><span class="text-success fw-bold">${{ number_format($salary->amount, 2) }}</span></td>
                            <td>{{ $salary->payment_method }}</td>
                            <td>
                                @if($salary->payment_receipt)
                                    <a href="{{ asset('storage/' . $salary->payment_receipt) }}" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-file-alt"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $salary->is_paid ? 'success' : 'secondary' }}">
                                    {{ $salary->is_paid ? 'Paid' : 'Unpaid' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-light editSalaryBtn" 
                                        data-id="{{ $salary->id }}"
                                        data-user_id="{{ $salary->add_user_id }}"
                                        data-salary_date="{{ $salary->salary_date }}"
                                        data-amount="{{ $salary->amount }}"
                                        data-method="{{ $salary->payment_method }}"
                                        data-is_paid="{{ $salary->is_paid }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#salaryModal"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit text-info"></i>
                                    </button>

                                    <form action="{{ route('admin.salaries.destroy', $salary->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this salary?')">
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
                        <tr><td colspan="8" class="text-center text-muted">No salaries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Salary Modal --}}
<div class="modal fade" id="salaryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="salaryForm" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">💵 Pay / Edit Salary</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Developer</label>
<select name="add_user_id" id="user_id" class="form-control" required>
    <option value="">-- Select User --</option>
    @foreach($users as $user)
        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
    @endforeach
</select>


                </div>

                <div class="mb-3">
                    <label>Salary Date</label>
                    <input type="date" name="salary_date" id="salary_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Payment Method</label><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="payment_method" value="Cash" class="form-check-input" checked>
                        <label class="form-check-label">Cash</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="payment_method" value="Account" class="form-check-input">
                        <label class="form-check-label">Account</label>
                    </div>
                </div>

                <div class="mb-3 d-none" id="receiptInput">
                    <label>Payment Receipt</label>
                    <input type="file" name="payment_receipt" class="form-control">
                </div>

                <div class="form-check">
                    <input type="checkbox" name="is_paid" value="1" class="form-check-input" id="isPaid">
                    <label class="form-check-label" for="isPaid">Mark as Paid</label>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

{{-- Scripts --}}
<script>
    // Show/hide receipt input
    document.addEventListener('change', function(e) {
        if(e.target.name === "payment_method") {
            document.getElementById('receiptInput').classList.toggle('d-none', e.target.value !== 'Account');
        }
    });

    // Reset form on modal open (create mode)
    document.querySelector('[data-bs-target="#salaryModal"]').addEventListener('click', function () {
        const form = document.getElementById('salaryForm');
        form.reset();
        form.action = "{{ route('admin.salaries.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('receiptInput').classList.add('d-none');
    });

    // Edit modal logic
    document.querySelectorAll('.editSalaryBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('salaryForm');
            form.action = `/admin/salaries/${this.dataset.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('user_id').value = this.dataset.user_id;
            document.getElementById('salary_date').value = this.dataset.salary_date;
            document.getElementById('amount').value = this.dataset.amount;

            // Payment method
            document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                el.checked = (el.value === this.dataset.method);
            });

            // Receipt toggle
            document.getElementById('receiptInput').classList.toggle('d-none', this.dataset.method !== 'Account');

            // Paid status
            document.getElementById('isPaid').checked = this.dataset.is_paid == "1";
        });
    });
</script>
@endsection
