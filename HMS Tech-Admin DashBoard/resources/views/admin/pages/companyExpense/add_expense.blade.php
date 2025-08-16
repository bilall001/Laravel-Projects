@extends('admin.layouts.main')
@section('title')
    Expense - HMS Tech & Solutions
@endsection
@section('content')
    <div class="container-fluid">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="page-title">💰 Manage Expenses</h4>
            <button class="btn btn-primary" id="createExpenseBtn">
                <i class="bi bi-plus-circle"></i> Add Expense
            </button>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Expense Table --}}
        <div class="card">
            <div class="card-header bg-dark text-white">Expense List</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Currency</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Receipt</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            <tr>
                                <td>{{ $expense->title }}</td>
                                <td>${{ number_format($expense->amount, 2) }}</td>
                                <td>{{ $expense->currency }}</td>
                                <td>{{ $expense->category ?? '-' }}</td>
                                <td>{{ $expense->date }}</td>
                                <td>
                                    @if ($expense->receipt_file)
                                        <a href="{{ asset('storage/' . $expense->receipt_file) }}" target="_blank">View
                                            File</a>
                                    @else
                                        <span class="text-muted">No file</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 justify-content-center">
                                        <button class="btn btn-sm btn-light view-expense-btn"
                                            data-expense='@json($expense)' title="View">
                                            <i class="fas fa-eye text-primary"></i>
                                        </button>

                                        <button class="btn btn-sm btn-light edit-expense-btn"
                                            data-expense='@json($expense)' title="Edit">
                                            <i class="fas fa-edit text-info"></i>
                                        </button>

                                        <form action="{{ route('companyExpense.destroy', $expense->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Are you sure?');">
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
                                <td colspan="7" class="text-center text-muted py-4">No expenses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add/Edit Expense Modal --}}
        <div class="modal fade" id="expenseModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" enctype="multipart/form-data" id="expenseForm">
                    @csrf
                    <input type="hidden" name="_method" id="expenseFormMethod" value="POST">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="expenseModalTitle">Add Expense</h5>
                            <a href="{{ route('companyExpense.index') }}" class="close">&times;</a>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Title</label>
                                <input type="text" name="title" id="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" class="form-control" required>
                                    <option value="PKR">PKR - Pakistani Rupee</option>
                                    <option value="USD">USD - US Dollar</option>
                                    <option value="EUR">EUR - Euro</option>
                                    <option value="GBP">GBP - British Pound</option>
                                    <option value="AED">AED - UAE Dirham</option>
                                    <option value="CAD">CAD - Canadian Dollar</option>
                                    <option value="AUD">AUD - Australian Dollar</option>
                                    <option value="INR">INR - Indian Rupee</option>
                                    <option value="CNY">CNY - Chinese Yuan</option>
                                    <option value="JPY">JPY - Japanese Yen</option>
                                    <option value="SAR">SAR - Saudi Riyal</option>
                                    <option value="TRY">TRY - Turkish Lira</option>
                                    <option value="ZAR">ZAR - South African Rand</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label>Category</label>
                                <input type="text" name="category" id="category" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label>Date</label>
                                <input type="date" name="date" id="date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Receipt File (optional)</label>
                                <input type="file" name="receipt_file" id="receipt_file" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save Expense</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- View Expense Modal --}}
        <div class="modal fade" id="viewExpenseModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content p-3">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">Expense Details</h5>
                        <a href="{{ route('companyExpense.index') }}" class="close">&times;</a>
                    </div>
                    <div class="modal-body" id="viewExpenseContent"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const expenseModal = new bootstrap.Modal(document.getElementById('expenseModal'));
            const viewModal = new bootstrap.Modal(document.getElementById('viewExpenseModal'));
            const form = document.getElementById('expenseForm');
            const formMethod = document.getElementById('expenseFormMethod');

            document.getElementById('createExpenseBtn').addEventListener('click', () => {
                form.reset();
                formMethod.value = 'POST';
                document.getElementById('expenseModalTitle').innerText = 'Add Expense';
                form.action = "{{ route('companyExpense.store') }}";
                expenseModal.show();
            });

            document.querySelectorAll('.edit-expense-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const expense = JSON.parse(btn.dataset.expense);
                    form.reset();
                    formMethod.value = 'PUT';
                    document.getElementById('expenseModalTitle').innerText = 'Edit Expense';
                    form.action = `/companyExpense/${expense.id}`;

                    document.getElementById('title').value = expense.title;
                    document.getElementById('amount').value = expense.amount;
                    document.getElementById('currency').value = expense.currency || 'PKR';
                    document.getElementById('category').value = expense.category || '';
                    document.getElementById('date').value = expense.date;
                    document.getElementById('description').value = expense.description || '';

                    expenseModal.show();
                });
            });

            document.querySelectorAll('.view-expense-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const expense = JSON.parse(btn.dataset.expense);
                    const fileLink = expense.receipt_file ?
                        `<p><strong>Receipt:</strong> <a href="/storage/${expense.receipt_file}" target="_blank">View File</a></p>` :
                        `<p><strong>Receipt:</strong> No file</p>`;
                    const html = `
                <p><strong>Title:</strong> ${expense.title}</p>
                <p><strong>Amount:</strong> $${expense.amount} ${expense.currency || 'PKR'}</p>
                <p><strong>Category:</strong> ${expense.category || '-'}</p>
                <p><strong>Date:</strong> ${expense.date}</p>
                <p><strong>Description:</strong><br>${expense.description || '-'}</p>
                ${fileLink}
            `;
                    document.getElementById('viewExpenseContent').innerHTML = html;
                    viewModal.show();
                });
            });
        });
    </script>
@endsection
