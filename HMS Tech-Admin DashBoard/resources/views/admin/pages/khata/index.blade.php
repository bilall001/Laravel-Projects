@extends('admin.layouts.main')

@section('title')
    Khata - HMS Tech & Solutions
@endsection
@section('custom_css')

@section('content')
    <div class="container mt-3 my-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">📒 Khata Accounts</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createAccountModal">
                <i class="bi bi-plus-circle"></i> Add New
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                {{ $errors->first() }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
             <div class="card-header bg-dark border-bottom">
        <h5 class="mb-0 text-white">Khata List</h5>
    </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width:28%">Party</th>
                                <th>Type</th>
                                <th class="text-right">Balance</th>
                                <th>Currency</th>
                                <th>Status</th>
                                <th style="width:220px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($accounts as $a)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold" style="text-transform: capitalize !important;">{{ $a->party_label }}</div>
                                        @if ($a->party_type === 'manual' || $a->party_type === 'other')
                                            <small class="text-muted">{{ $a->phone ?? '' }}
                                                {{ $a->email ? '· ' . $a->email : '' }}</small>
                                        @endif
                                    </td>
                                    <td><span
                                            class="badge badge-secondary">{{ str_replace('_', ' ', $a->party_type) }}</span>
                                    </td>
                                    <td class="text-right">
                                        <span class="{{ $a->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($a->current_balance, 2) }}
                                        </span>
                                    </td>
                                    <td>{{ $a->currency }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $a->status === 'active' ? 'badge-success' : 'badge-secondary' }}">{{ $a->status }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <!-- View -->
                                            <button class="btn btn-sm btn-outline-info" data-toggle="modal"
                                                data-target="#showAccountModal" data-show_id="{{ $a->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>

                                            <!-- Add Entry -->
                                            <button class="btn btn-sm btn-outline-success" data-toggle="modal"
                                                data-target="#addEntryModal" data-account_id="{{ $a->id }}"
                                                data-party="{{ $a->party_label }}">
                                                <i class="fas fa-plus"></i>
                                            </button>

                                            <!-- Edit -->
                                            <button class="btn btn-sm btn-outline-secondary" data-toggle="modal"
                                                data-target="#editAccountModal" data-id="{{ $a->id }}"
                                                data-party_type="{{ $a->party_type }}"
                                                data-party_label="{{ $a->party_label }}" data-name="{{ $a->name }}"
                                                data-phone="{{ $a->phone }}" data-email="{{ $a->email }}"
                                                data-cnic="{{ $a->cnic }}" data-address="{{ $a->address }}"
                                                data-status="{{ $a->status }}" data-notes="{{ $a->notes }}">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <!-- Delete -->
                                            <form action="{{ route('khata.accounts.destroy', $a->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this account?');" class="d-inline ml-1">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No accounts yet. Create your
                                        first Khata.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- SHOW MODAL (loads body via AJAX) --}}
    <div class="modal fade" id="showAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Khata Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" id="showAccountBody">
                    <div class="p-4 text-center text-muted">Loading…</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CREATE MODAL --}}
    <div class="modal fade" id="createAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" action="{{ route('khata.accounts.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Khata Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label class="form-label">Party Type</label>
                            <select class="custom-select" name="party_type" id="create_party_type" required>
                                <option value="clients">Client</option>
                                <option value="partners">Partner</option>
                                <option value="developers">Developer</option>
                                <option value="team_managers">Team Manager</option>
                                <option value="business_developers">Business Developer</option>
                                <option value="manual">Manual</option>
                                <option value="other">Other</option>
                            </select>
                            
                        </div>

                        <div class="form-group col-md-8" id="create_party_picker_wrap">
                            <label class="form-label">Select Party</label>
                            <select class="custom-select" name="party_id" id="create_party_picker"></select>
                            <small class="form-text text-muted">Choose from your existing records.</small>
                        </div>

                        <div class="col-12 d-none" id="create_manual_fields">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="name" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control" name="phone" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-label">CNIC</label>
                                    <input type="text" class="form-control" name="cnic" />
                                </div>
                                <div class="form-group col-12">
                                    <label class="form-label">Address</label>
                                    <input type="text" class="form-control" name="address" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label class="form-label">Opening Balance</label>
                            <input type="number" step="0.01" class="form-control" name="opening_balance"
                                value="0">
                            <small class="form-text text-muted">+ they owe you, − you owe them</small>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Currency</label>
                            <input type="text" class="form-control" name="currency" value="PKR" maxlength="3">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-label">Status</label>
                            <select class="custom-select" name="status">
                                <option value="active" selected>Active</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT ACCOUNT MODAL (ONE copy only) --}}
    <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="edit_form">
                @csrf @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Khata Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_id">

                    <div class="alert alert-info small">
                        Party type and linked party are fixed for now. Use <strong>archive + recreate</strong> to change
                        linkage.
                    </div>

                    <h6 class="text-muted mb-3" id="edit_party_heading"></h6>

                    <div id="edit_manual_fields">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label class="form-label">Name (for manual/other)</label>
                                <input type="text" class="form-control" name="name" id="edit_name">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email">
                            </div>
                            <div class="form-group col-md-6">
                                <label class="form-label">CNIC</label>
                                <input type="text" class="form-control" name="cnic" id="edit_cnic">
                            </div>
                            <div class="form-group col-12">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" id="edit_address">
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-md-4 px-0">
                        <label class="form-label">Status</label>
                        <select class="custom-select" name="status" id="edit_status">
                            <option value="active">Active</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div class="form-group col-12 px-0">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2" id="edit_notes"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    {{-- GLOBAL EDIT ENTRY MODAL (ONE copy only) --}}
    <div class="modal fade" id="editEntryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="edit_entry_form" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Date</label>
                            <input type="date" class="form-control" name="entry_date" id="edit_entry_date" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Type</label>
                            <select class="custom-select" name="ref_type" id="edit_ref_type" required>
                                <option value="invoice">Invoice / Charge</option>
                                <option value="payment">Payment</option>
                                <option value="expense">Expense</option>
                                <option value="salary">Salary</option>
                                <option value="investment">Investment</option>
                                <option value="adjustment">Adjustment</option>
                                <option value="opening">Opening</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Payment Method</label>
                            <select class="custom-select" name="payment_method" id="edit_payment_method" required>
                                <option value="none">None (no money)</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="edit_description" rows="2"
                                placeholder="Short note (optional)"></textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Amount direction</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="edit_amount_type_debit" name="amount_type_edit"
                                    class="custom-control-input" value="debit">
                                <label class="custom-control-label" for="edit_amount_type_debit">Debit (they owe you
                                    more)</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="edit_amount_type_credit" name="amount_type_edit"
                                    class="custom-control-input" value="credit">
                                <label class="custom-control-label" for="edit_amount_type_credit">Credit (you
                                    received/settled)</label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" step="0.01" class="form-control" id="edit_amount"
                                placeholder="0.00" required>
                            <input type="hidden" name="debit" id="edit_debit" value="0">
                            <input type="hidden" name="credit" id="edit_credit" value="0">
                        </div>

                        <div class="form-group col-md-6 d-none" id="edit_online_ref_wrap">
                            <label>Online Reference</label>
                            <input type="text" class="form-control" name="online_reference"
                                id="edit_online_reference" placeholder="Bank/Easypaisa/JazzCash Ref">
                        </div>

                        <div class="form-group col-md-6 d-none" id="edit_online_proof_wrap">
                            <label>Online Proof (file)</label>
                            <input type="file" class="form-control-file" name="online_proof_file"
                                accept="image/*,application/pdf">
                            <small class="form-text text-muted">JPG/PNG/PDF up to 4MB.</small>
                            <div class="small mt-1" id="current_proof_wrap" style="display:none;">
                                Current: <a href="#" target="_blank" id="current_proof_link">view</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="edit_entry_submit" type="submit">Update Entry</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hidden delete form (once) --}}
    <form id="deleteEntryForm" method="POST" style="display:none;">
        @csrf
        @method('DELETE')
    </form>

    {{-- ADD ENTRY MODAL --}}
    <div class="modal fade" id="addEntryModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <form class="modal-content" method="POST" id="add_entry_form" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Entry — <span id="add_entry_party">Party</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label>Date</label>
                            <input type="date" class="form-control" name="entry_date"
                                value="{{ now()->toDateString() }}" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Type</label>
                            <select class="custom-select" name="ref_type" required>
                                <option value="invoice">Invoice / Charge</option>
                                <option value="payment">Payment</option>
                                <option value="expense">Expense</option>
                                <option value="salary">Salary</option>
                                <option value="investment">Investment</option>
                                <option value="adjustment">Adjustment</option>
                                <option value="other" selected>Other</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Payment Method</label>
                            <select class="custom-select" name="payment_method" id="add_payment_method" required>
                                <option value="none" selected>None (no money)</option>
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                            </select>
                        </div>

                        <div class="form-group col-12">
                            <label>Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Short note (optional)"></textarea>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Amount direction</label><br>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="amount_type_debit" name="amount_type"
                                    class="custom-control-input" value="debit" checked>
                                <label class="custom-control-label" for="amount_type_debit">Debit (they owe you
                                    more)</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input type="radio" id="amount_type_credit" name="amount_type"
                                    class="custom-control-input" value="credit">
                                <label class="custom-control-label" for="amount_type_credit">Credit (you
                                    received/settled)</label>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Amount</label>
                            <input type="number" step="0.01" class="form-control" id="add_amount"
                                placeholder="0.00" required>
                            <input type="hidden" name="debit" id="add_debit" value="0">
                            <input type="hidden" name="credit" id="add_credit" value="0">
                        </div>

                        <div class="form-group col-md-6 d-none" id="online_ref_wrap">
                            <label>Online Reference</label>
                            <input type="text" class="form-control" name="online_reference"
                                placeholder="Bank/Easypaisa/JazzCash Ref">
                        </div>

                        <div class="form-group col-md-6 d-none" id="online_proof_wrap">
                            <label>Online Proof (file)</label>
                            <input type="file" class="form-control-file" name="online_proof_file"
                                accept="image/*,application/pdf">
                            <small class="form-text text-muted">JPG/PNG/PDF up to 4MB.</small>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="add_entry_submit">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('custom_js')
    <script>
        /* ------------------ helpers ------------------ */
        function openModalSafely(selectorToOpen) {
            var $open = $('.modal.show');
            if ($open.length) {
                $open.one('hidden.bs.modal', function() {
                        $(selectorToOpen).modal('show');
                    })
                    .modal('hide');
            } else {
                $(selectorToOpen).modal('show');
            }
        }

        function assetJoin(path) {
            if (!path) return '#';
            return path.startsWith('http') ? path : ("{{ asset('') }}" + path.replace(/^\/?/, ''));
        }

        /* ------------------ party select (create modal) ------------------ */
        const PARTY_OPTIONS = @json($partyOptions);

        function populatePartySelect(selectEl, type) {
            selectEl.innerHTML = '';
            const list = (PARTY_OPTIONS && PARTY_OPTIONS[type]) ? PARTY_OPTIONS[type] : [];
            const ph = document.createElement('option');
            ph.value = '';
            ph.textContent = list.length ? ('Select ' + type.replace('_', ' ')) : 'No records found';
            selectEl.appendChild(ph);
            list.forEach(it => {
                const o = document.createElement('option');
                o.value = it.id;
                o.textContent = it.label;
                selectEl.appendChild(o);
            });
        }

        function toggleCreateFields() {
            const type = $('#create_party_type').val();
            const $pickerWrap = $('#create_party_picker_wrap');
            const $picker = $('#create_party_picker');
            const $manual = $('#create_manual_fields');
            const $manualInputs = $manual.find('input, textarea, select');

            if (type === 'manual' || type === 'other') {
                $pickerWrap.addClass('d-none');
                $manual.removeClass('d-none');
                $manualInputs.prop('disabled', false);
                $picker.empty();
            } else {
                $manual.addClass('d-none');
                $manualInputs.prop('disabled', true);
                $pickerWrap.removeClass('d-none');
                populatePartySelect($picker[0], type);
            }
        }
        $('#createAccountModal').on('shown.bs.modal', toggleCreateFields);
        $('#create_party_type').on('change', toggleCreateFields);

        /* ------------------ SHOW modal (AJAX body, single handler) ------------------ */
        var lastShowAccountId = null;
        $('#showAccountModal').off('show.bs.modal').on('show.bs.modal', function(e) {
            lastShowAccountId = $(e.relatedTarget).data('show_id') || lastShowAccountId;
            $('#showAccountBody').html('<div class="p-4 text-center text-muted">Loading…</div>');
            var url = "{{ route('khata.accounts.modal', ['id' => '__ID__']) }}".replace('__ID__',
                lastShowAccountId);
            $('#showAccountBody').load(url);
        });

        /* ------------------ EDIT ACCOUNT modal (intercept click; do not stack) ------------------ */
        function fillEditAccount($btn) {
            const id = $btn.data('id');
            const partyType = $btn.data('party_type') || '';
            const partyLabel = $btn.data('party_label') || '';

            $('#edit_form').attr('action', "{{ url('/khata/accounts') }}/" + id);
            $('#edit_id').val(id);
            $('#edit_party_heading').text(
                partyLabel ? ('Editing: ' + partyLabel + ' [' + partyType.replace('_', ' ') + ']') :
                ('Editing [' + partyType.replace('_', ' ') + ']')
            );

            const isManual = (partyType === 'manual' || partyType === 'other');
            const $wrap = $('#edit_manual_fields');
            const $inputs = $wrap.find('input, textarea, select');

            if (isManual) {
                $wrap.removeClass('d-none');
                $inputs.prop('disabled', false);
                $('#edit_name').val($btn.data('name') || '');
                $('#edit_phone').val($btn.data('phone') || '');
                $('#edit_email').val($btn.data('email') || '');
                $('#edit_cnic').val($btn.data('cnic') || '');
                $('#edit_address').val($btn.data('address') || '');
            } else {
                $wrap.addClass('d-none');
                $inputs.prop('disabled', true);
                $('#edit_name,#edit_phone,#edit_email,#edit_cnic,#edit_address').val('');
            }
            $('#edit_status').val($btn.data('status') || 'active');
            $('#edit_notes').val($btn.data('notes') || '');
        }

        // Intercept ALL buttons that target the account edit modal
        $(document).off('click.openAccountEdit').on('click.openAccountEdit', '[data-target="#editAccountModal"]', function(
            e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // prevent Bootstrap data-api from also firing
            fillEditAccount($(this));
            openModalSafely('#editAccountModal');
        });

        /* ------------------ EDIT ENTRY modal (open from Show; do not stack) ------------------ */
        function fillEditEntryFromBtn($btn) {
            // Support both data-entry-id and data-entry_id
            var id = $btn.data('entryId') || $btn.data('entry_id');
            var date = $btn.data('entry_date') || '';
            var ref = $btn.data('ref_type') || 'other';
            var desc = $btn.attr('data-description') || '';
            var debit = parseFloat($btn.data('debit') || '0');
            var credit = parseFloat($btn.data('credit') || '0');
            var method = $btn.data('payment_method') || 'none';
            var onlineRef = $btn.data('online_reference') || '';
            var proofPath = $btn.data('online_proof_path') || '';

            $('#edit_entry_form').attr('action', "{{ url('/khata/entries') }}/" + id);
            $('#edit_entry_date').val(date);
            $('#edit_ref_type').val(ref);
            $('#edit_description').val(desc);
            $('#edit_payment_method').val(method).trigger('change');

            if (debit > 0) {
                $('#edit_amount_type_debit').prop('checked', true);
                $('#edit_amount').val(debit.toFixed(2));
                $('#edit_debit').val(debit.toFixed(2));
                $('#edit_credit').val('0');
            } else {
                $('#edit_amount_type_credit').prop('checked', true);
                $('#edit_amount').val(credit.toFixed(2));
                $('#edit_debit').val('0');
                $('#edit_credit').val(credit.toFixed(2));
            }
            $('#edit_online_reference').val(onlineRef);

            if (proofPath) {
                $('#current_proof_wrap').show();
                $('#current_proof_link').attr('href', assetJoin(proofPath));
            } else {
                $('#current_proof_wrap').hide();
                $('#current_proof_link').attr('href', '#');
            }
        }

        // EDIT from the Show modal table (delegated; works for AJAX content)
        $(document).off('click.editEntry').on('click.editEntry', '.js-edit-entry', function(e) {
            e.preventDefault();
            fillEditEntryFromBtn($(this));
            openModalSafely('#editEntryModal');
        });

        // Optional: after closing Edit Entry, reopen and refresh the Show modal
        $('#editEntryModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            if (lastShowAccountId) {
                var url = "{{ route('khata.accounts.modal', ['id' => '__ID__']) }}".replace('__ID__',
                    lastShowAccountId);
                $('#showAccountBody').load(url, function() {
                    $('#showAccountModal').modal('show');
                });
            }
        });

        // Payment method toggle in Edit Entry modal
        $('#edit_payment_method').off('change').on('change', function() {
            var v = $(this).val();
            if (v === 'online') {
                $('#edit_online_ref_wrap, #edit_online_proof_wrap').removeClass('d-none');
            } else {
                $('#edit_online_ref_wrap, #edit_online_proof_wrap').addClass('d-none');
            }
        });

        // Map amount to debit/credit before submitting Edit Entry form
        $('#edit_entry_submit').off('click').on('click', function(ev) {
            var amt = parseFloat($('#edit_amount').val() || '0');
            if (isNaN(amt) || amt <= 0) {
                ev.preventDefault();
                alert('Please enter an amount greater than 0.');
                return false;
            }
            if ($('#edit_amount_type_debit').is(':checked')) {
                $('#edit_debit').val(amt.toFixed(2));
                $('#edit_credit').val('0');
            } else {
                $('#edit_debit').val('0');
                $('#edit_credit').val(amt.toFixed(2));
            }
        });

        /* ------------------ DELETE ENTRY (delegated) ------------------ */
        $(document).off('click.deleteEntry').on('click.deleteEntry', '.js-delete-entry', function(e) {
            e.preventDefault();
            var id = $(this).data('entryId') || $(this).data('entry_id');
            if (!id) return;
            if (!confirm('Delete this entry? This cannot be undone.')) return;
            $('#deleteEntryForm').attr('action', "{{ url('/khata/entries') }}/" + id).trigger('submit');
        });

        /* ------------------ ADD ENTRY modal ------------------ */
        $('#addEntryModal').on('show.bs.modal', function(e) {
            var $btn = $(e.relatedTarget);
            var accountId = $btn.data('account_id');
            var party = $btn.data('party');
            $('#add_entry_party').text(party || 'Party');
            $('#add_entry_form').attr('action', "{{ url('/khata/accounts') }}/" + accountId + "/entries");
            $('#add_entry_form')[0].reset();
            $('#add_debit').val('0');
            $('#add_credit').val('0');
            $('#online_ref_wrap, #online_proof_wrap').addClass('d-none');
        });
        $('#add_payment_method').on('change', function() {
            var v = $(this).val();
            if (v === 'online') {
                $('#online_ref_wrap, #online_proof_wrap').removeClass('d-none');
            } else {
                $('#online_ref_wrap, #online_proof_wrap').addClass('d-none');
            }
        });
        $('#add_entry_submit').on('click', function(ev) {
            var amt = parseFloat($('#add_amount').val() || '0');
            if (isNaN(amt) || amt <= 0) {
                ev.preventDefault();
                alert('Please enter an amount greater than 0.');
                return false;
            }
            if ($('#amount_type_debit').is(':checked')) {
                $('#add_debit').val(amt.toFixed(2));
                $('#add_credit').val('0');
            } else {
                $('#add_debit').val('0');
                $('#add_credit').val(amt.toFixed(2));
            }
        });
    </script>
@endpush
