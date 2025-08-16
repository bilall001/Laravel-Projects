@extends('admin.layouts.main')
@section('title')
Salaries - HMS Tech  & Solutions
@endsection
@section('content')
<div class="container-fluid">
    <h4 class="page-title mb-4">Salary Management</h4>

    {{-- Filter --}}
    <form method="GET" class="form-inline mb-3">
        <input type="month" name="month" class="form-control mr-2" value="{{ $month }}">
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Pay button --}}
    <button class="btn btn-success mb-3" id="openCreateModal">
        <i class="fas fa-plus"></i> Pay Salary
    </button>

    {{-- Salary Table --}}
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover">
          <thead class="thead-dark">
              <tr>
                  <th>Developer</th>
                  <th>Email</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Receipt</th>
                  <th>Status</th>
                  <th>Actions</th>
              </tr>
          </thead>
          <tbody>
              @forelse($salaries as $salary)
                  <tr>
                      <td>{{ $salary->developer->name ?? '-' }}</td>
                      <td>{{ $salary->developer->email ?? '-' }}</td>
                      <td>{{ $salary->salary_date }}</td>
                      <td><span class="text-success font-weight-bold">${{ number_format($salary->amount, 2) }}</span></td>
                      <td>{{ $salary->payment_method }}</td>
      <td>
    @if($salary->payment_receipt)
        <a href="{{ asset('storage/' . $salary->payment_receipt) }}"
           class="btn btn-sm btn-info"
           target="_blank"
           rel="noopener noreferrer">
            <i class="fas fa-file-alt"></i> View Receipt
        </a>
    @else
        <span class="text-muted">N/A</span>
    @endif
</td>
                      <td>
                          <span class="badge badge-{{ $salary->is_paid ? 'success' : 'secondary' }}">
                              {{ $salary->is_paid ? 'Paid' : 'Unpaid' }}
                          </span>
                      </td>
                      <td>
  <div class="d-flex align-items-center gap-1">
    <button 
      type="button" 
      class="btn btn-sm btn-light editSalaryBtn" 
      data-id="{{ $salary->id }}"
      data-developer_id="{{ $salary->developer_id }}"
      data-salary_date="{{ $salary->salary_date }}"
      data-amount="{{ $salary->amount }}"
      data-method="{{ $salary->payment_method }}"
      data-is_paid="{{ $salary->is_paid }}"
      title="Edit"
    >
      <i class="fas fa-edit text-info"></i>
    </button>

    <form 
      action="{{ route('admin.salaries.destroy', $salary->id) }}" 
      method="POST" 
      class="d-inline" 
      onsubmit="return confirm('Delete this salary?')"
    >
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

{{-- Modal --}}
<div class="modal fade" id="paySalaryModal" tabindex="-1" role="dialog" aria-labelledby="paySalaryModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="salaryForm" action="{{ route('admin.salaries.store') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="_method" id="formMethod" value="POST">
      <input type="hidden" name="salary_id" id="salaryId">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pay Salary</h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>

        <div class="modal-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label>Choose Developer</label>
                <select name="add_user_id" id="developer_id" class="form-control" required>
                    <option value="">-- Select Developer --</option>
                    @foreach($developers as $dev)
                        <option value="{{ $dev->id }}">{{ $dev->name }} ({{ $dev->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Salary Date</label>
                <input type="date" name="salary_date" id="salary_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Amount</label>
                <input type="number" name="amount" id="amount" class="form-control" required>
            </div>

            <div class="form-group">
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

            <div class="form-group d-none" id="receiptInput">
                <label>Payment Receipt</label>
                <input type="file" name="payment_receipt" class="form-control-file">
            </div>

            <div class="form-check">
                <input type="checkbox" name="is_paid" value="1" class="form-check-input" id="isPaid">
                <label class="form-check-label" for="isPaid">Mark as Paid</label>
            </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Script --}}
<script>
    // Toggle receipt input based on method
    document.querySelectorAll('input[name="payment_method"]').forEach(el => {
        el.addEventListener('change', function() {
            document.getElementById('receiptInput').classList.toggle('d-none', this.value !== 'Account');
        });
    });

    // Open create modal
    document.getElementById('openCreateModal').addEventListener('click', function () {
        const form = document.getElementById('salaryForm');
        form.reset();
        form.action = "{{ route('admin.salaries.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('salaryId').value = '';
        document.getElementById('receiptInput').classList.add('d-none');
        $('#paySalaryModal').modal('show');
    });

    // Open edit modal
    document.querySelectorAll('.editSalaryBtn').forEach(btn => {
        btn.addEventListener('click', function () {
            const form = document.getElementById('salaryForm');

            form.action = `/admin/salaries/${this.dataset.id}`;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('salaryId').value = this.dataset.id;
            document.getElementById('developer_id').value = this.dataset.developer_id;
            document.getElementById('salary_date').value = this.dataset.salary_date;
            document.getElementById('amount').value = this.dataset.amount;

            document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                el.checked = (el.value === this.dataset.method);
            });

            document.getElementById('receiptInput').classList.toggle('d-none', this.dataset.method !== 'Account');
            document.getElementById('isPaid').checked = this.dataset.is_paid == "1";

            $('#paySalaryModal').modal('show');
        });
    });
</script>
@endsection
