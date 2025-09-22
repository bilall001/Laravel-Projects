@extends('admin.layouts.main')
@section('title')
    Developers Payment - HMS Tech & Solutions
@endsection
@section('content')
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0 text-white">Developer Project Payments</h4>
                 @if (auth()->user()->role === 'admin' ||
                    auth()->user()->role === 'business developer' ||
                    auth()->user()->role === 'team manager')
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createModal">
                    <i class="fas fa-plus"></i> Add Payment
                </button>
                @endif
            </div>

            <div class="card-body table-responsive">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <table class="table table-hover mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Developer</th>
                            <th>Project</th>
                            <th>Payment Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Notes</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->developer->user->name ?? 'N/A' }}</td>
                                <td>{{ $payment->project->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-{{ $payment->payment_type === 'fixed' ? 'info' : 'warning' }}">
                                        {{ ucfirst($payment->payment_type) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($payment->payment_type === 'percentage')
                                        {{ $payment->amount }}%
                                    @else
                                        ₨{{ number_format($payment->amount, 2) }}
                                    @endif
                                </td>

                                <td>
                                    <span class="badge badge-{{ $payment->status === 'paid' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td>{{ $payment->notes ?? '-' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary edit-btn" data-id="{{ $payment->id }}"
                                        data-developer_id="{{ $payment->developer_id }}"
                                        data-project_id="{{ $payment->project_id }}"
                                        data-payment_type="{{ $payment->payment_type }}"
                                        data-amount="{{ $payment->amount }}" data-status="{{ $payment->status }}"
                                        data-notes="{{ $payment->notes }}" data-toggle="modal" data-target="#editModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{ route('developer_project_payments.destroy', $payment->id) }}"
                                        method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $payments->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Create Payment Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('developer_project_payments.store') }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="createModalLabel">Add Payment</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Developer</label>
                            <select name="developer_id" class="form-control" required>
                                <option value="">-- Select Developer --</option>
                                @foreach ($developers as $dev)
                                    <option value="{{ $dev->id }}">{{ $dev->user->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Project</label>
                            <select name="project_id" class="form-control" required>
                                <option value="">-- Select Project --</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select name="payment_type" class="form-control" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="amount" id="editAmount1" class="form-control"
                                required placeholder="Enter amount">
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="_modal" value="edit">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="editModalLabel">Edit Payment</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Developer</label>
                            <select name="developer_id" id="editDeveloper" class="form-control" required>
                                @foreach ($developers as $dev)
                                    <option value="{{ $dev->id }}">{{ $dev->user->name ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Project</label>
                            <select name="project_id" id="editProject" class="form-control" required>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Payment Type</label>
                            <select name="payment_type" id="editPaymentType" class="form-control" required>
                                <option value="fixed">Fixed</option>
                                <option value="percentage">Percentage</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label id="editAmountLabel">Amount (Rs)</label>
                            <input type="number" step="0.01" name="amount" id="editAmount" class="form-control"
                                required placeholder="Enter amount">
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" id="editStatus" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" id="editNotes" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-info">Update Payment</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom_js')
    <script>
        $('#editPaymentType').on('change', function() {
            if ($(this).val() === 'percentage') {
                $('#editAmountLabel').text('Amount (%)');
                $('#editAmount').attr('placeholder', 'Enter percentage');
            } else {
                $('#editAmountLabel').text('Amount (Rs)');
                $('#editAmount').attr('placeholder', 'Enter amount in Rs');
            }

        });
        $(document).on('click', '.edit-btn', function() {
            let id = $(this).data('id');
            let developer_id = $(this).data('developer_id');
            let project_id = $(this).data('project_id');
            let payment_type = $(this).data('payment_type');
            let amount = $(this).data('amount');
            let status = $(this).data('status');
            let notes = $(this).data('notes');

            // Debug in console
            console.log("Editing Payment:", {
                id,
                developer_id,
                project_id,
                payment_type,
                amount,
                status,
                notes
            });

            // Set form action + values
            $('#editForm').attr('action', '/developer_project_payments/' + id);
            $('#editDeveloper').val(developer_id);
            $('#editProject').val(project_id);
            $('#editPaymentType').val(payment_type).trigger('change');
            $('#editAmount').val(amount);
            $('#editStatus').val(status);
            $('#editNotes').val(notes);
        });
    </script>
@endpush
