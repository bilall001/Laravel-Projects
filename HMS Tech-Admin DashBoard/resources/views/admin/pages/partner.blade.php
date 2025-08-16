@extends('admin.layouts.main')

@section('title')
    Partners- HMS Tech & Solutions
@endsection

@section('content')
    <div class="container-fluid">
        <h4 class="page-title mb-4">Partner Management</h4>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <button class="btn btn-success mb-3" id="openCreateModal">
            <i class="fas fa-plus"></i> Add Partner
        </button>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Investments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($partners as $partner)
                        <tr>
                            <td>
                                @if ($partner->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($partner->image))
                                    <img src="{{ asset('storage/' . $partner->image) }}" width="50" height="50"
                                        alt="Partner Image">
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $partner->user?->name ?? 'N/A' }}</td>
                            <td>{{ $partner->user?->email ?? 'N/A' }}</td>
                            <td>
                                @foreach ($partner->investments as $inv)
                                    <div class="border p-2 mb-2">
                                        <strong>Amount:</strong> ${{ $inv->contribution }}<br>
                                        <strong>Date:</strong> {{ $inv->contribution_date }}<br>
                                        <strong>Method:</strong> {{ $inv->payment_method }}<br>
                                        @if (
                                            $inv->payment_method === 'Account' &&
                                                $inv->payment_receipt &&
                                                Storage::disk('public')->exists($inv->payment_receipt))
                                            <a href="{{ asset('storage/' . $inv->payment_receipt) }}" target="_blank">View
                                                Receipt</a><br>
                                        @elseif($inv->payment_method === 'Account')
                                            <span class="text-muted">Receipt not available</span><br>
                                        @endif
                                        <strong>Status:</strong> {{ $inv->is_received ? 'Received' : 'Pending' }}
                                    </div>
                                @endforeach
                            </td>
                            <td>

                                <div class="d-flex align-items-center gap-1">
                                    <button type="button" class="btn btn-sm btn-warning"
                                        onclick="openEditModal({{ $partner->load('investments')->toJson() }})">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light" title="Delete">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="partnerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <form method="POST" id="partnerForm" action="{{ route('admin.partners.store') }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Partner</h5>
                        <a href="{{ route('admin.partners.index') }}" class="close">&times;</a>
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
                            <label>Partner Name</label>
                            <select name="user_id" id="partnerSelect" class="form-control" required>
                                <option value="">Select Partner</option>
                                @foreach ($partnerUsers as $partner)
                                    <option value="{{ $partner->id }}" data-email="{{ $partner->email }}">
                                        {{ $partner->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Partner Email</label>
                            <input type="email" name="email" id="partnerEmail" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label>Partner Image</label>
                            <input type="file" name="image" class="form-control-file">
                            <img id="image-preview" style="max-width: 100px; margin-top: 10px;" hidden>
                        </div>

                        <hr>
                        <h5>Investments</h5>
                        <div id="investmentFields">
                            <div class="investment-group border p-3 mb-3 row">
                                <div class="form-group col-md-2">
                                    <label>Amount</label>
                                    <input type="number" name="investments[0][contribution]" class="form-control" required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Date</label>
                                    <input type="date" name="investments[0][contribution_date]" class="form-control"
                                        required>
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Method</label>
                                    <select name="investments[0][payment_method]" class="form-control payment-method"
                                        data-index="0" required>
                                        <option value="Cash">Cash</option>
                                        <option value="Account">Account</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-3 receipt-group" id="receipt-group-0" style="display: none;">
                                    <label>Receipt</label>
                                    <input type="file" name="investments[0][payment_receipt]" class="form-control-file">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Status</label><br>
                                    <input type="checkbox" name="investments[0][is_received]" class="form-check-input"
                                        value="1">
                                    <label class="form-check-label">Received</label>
                                </div>
                                <div class="form-group col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-investment">×</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-secondary mt-2" id="addInvestment">+ Add
                            Investment</button>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Partner</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let investmentIndex = 1;

        document.getElementById('addInvestment').addEventListener('click', function() {
            const wrapper = document.getElementById('investmentFields');
            const html = `
        <div class="investment-group border p-3 mb-3 row">
            <div class="form-group col-md-2">
                <label>Amount</label>
                <input type="number" name="investments[${investmentIndex}][contribution]" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <label>Date</label>
                <input type="date" name="investments[${investmentIndex}][contribution_date]" class="form-control" required>
            </div>
            <div class="form-group col-md-2">
                <label>Method</label>
                <select name="investments[${investmentIndex}][payment_method]" class="form-control payment-method" data-index="${investmentIndex}" required>
                    <option value="Cash">Cash</option>
                    <option value="Account">Account</option>
                </select>
            </div>
            <div class="form-group col-md-3 receipt-group" id="receipt-group-${investmentIndex}" style="display: none;">
                <label>Receipt</label>
                <input type="file" name="investments[${investmentIndex}][payment_receipt]" class="form-control-file">
            </div>
            <div class="form-group col-md-2">
                <label>Status</label><br>
                <input type="checkbox" name="investments[${investmentIndex}][is_received]" class="form-check-input" value="1">
                <label class="form-check-label">Received</label>
            </div>
            <div class="form-group col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm remove-investment">×</button>
            </div>
        </div>`;
            wrapper.insertAdjacentHTML('beforeend', html);
            investmentIndex++;
        });

        document.getElementById('investmentFields').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-investment')) {
                e.target.closest('.investment-group').remove();
            }
        });

        // document.getElementById('openCreateModal').addEventListener('click', function() {
        //     $('#partnerModal').modal('show');
        // });

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('payment-method')) {
                const index = e.target.dataset.index;
                const receiptGroup = document.getElementById(`receipt-group-${index}`);
                receiptGroup.style.display = e.target.value === 'Account' ? 'block' : 'none';
            }
        });

        document.querySelector('input[name="image"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.hidden = false;
                };
                reader.readAsDataURL(file);
            } else {
                preview.hidden = true;
            }
        });
    </script>
    <script>
        // Open CREATE modal
        document.getElementById('openCreateModal').addEventListener('click', function() {
            const form = document.getElementById('partnerForm');
            form.action = "{{ route('admin.partners.store') }}";
            document.getElementById('formMethod').value = "POST";
            form.reset();
            $('#partnerModal').modal('show');
        });

        // Open EDIT modal
        function openEditModal(partner) {
            const form = document.getElementById('partnerForm');
            form.action = `/admin/partners/${partner.id}`;
            document.getElementById('formMethod').value = "PUT";

            // Fill fields
            document.getElementById('partnerSelect').value = partner.user_id;
            document.getElementById('partnerEmail').value = partner.user?.email ?? '';
            // document.querySelector('input[name="password"]').value = '';
            // document.querySelector('input[name="password_confirmation"]').value = '';

            if (partner.image_url) {
                document.getElementById('image-preview').src = partner.image_url;
                document.getElementById('image-preview').hidden = false;
            }

            // Investments (clear & repopulate)
            const wrapper = document.getElementById('investmentFields');
            wrapper.innerHTML = '';
            partner.investments.forEach((inv, idx) => {
                const html = `
        <div class="investment-group border p-3 mb-3 row">
            <div class="form-group col-md-2">
                <label>Amount</label>
                <input type="number" name="investments[${idx}][contribution]" class="form-control" value="${inv.contribution}" required>
            </div>
            <div class="form-group col-md-2">
                <label>Date</label>
                <input type="date" name="investments[${idx}][contribution_date]" class="form-control" value="${inv.contribution_date}" required>
            </div>
            <div class="form-group col-md-2">
                <label>Method</label>
                <select name="investments[${idx}][payment_method]" class="form-control payment-method" data-index="${idx}" required>
                    <option value="Cash" ${inv.payment_method === 'Cash' ? 'selected' : ''}>Cash</option>
                    <option value="Account" ${inv.payment_method === 'Account' ? 'selected' : ''}>Account</option>
                </select>
            </div>
            <div class="form-group col-md-3 receipt-group" id="receipt-group-${idx}" style="${inv.payment_method === 'Account' ? '' : 'display:none;'}">
                <label>Receipt</label>
                <input type="file" name="investments[${idx}][payment_receipt]" class="form-control-file">
            </div>
            <div class="form-group col-md-2">
                <label>Status</label><br>
                <input type="checkbox" name="investments[${idx}][is_received]" class="form-check-input" value="1" ${inv.is_received ? 'checked' : ''}>
                <label class="form-check-label">Received</label>
            </div>
        </div>`;
                wrapper.insertAdjacentHTML('beforeend', html);
            });

            $('#partnerModal').modal('show');
        }
    </script>
    <script>
        document.getElementById('partnerSelect').addEventListener('change', function() {
            let selectedOption = this.options[this.selectedIndex];
            let email = selectedOption.getAttribute('data-email');
            document.getElementById('partnerEmail').value = email ? email : '';
        });
    </script>
@endsection
