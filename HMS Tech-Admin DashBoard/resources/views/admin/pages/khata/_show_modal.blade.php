<div class="card shadow-sm border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">{{ $account->party_label }}</h5>
            <div class="text-muted small">
                Type: <span class="badge badge-secondary">{{ str_replace('_', ' ', $account->party_type) }}</span>
                @if (in_array($account->party_type, ['manual', 'other']))
                    @if ($account->phone)
                        · {{ $account->phone }}
                    @endif
                    @if ($account->email)
                        · {{ $account->email }}
                    @endif
                @endif
            </div>
        </div>
        <div class="text-right">
            <div class="small text-muted">Current Balance</div>
            <div class="h4 mb-1 {{ $account->current_balance >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($account->current_balance, 2) }} {{ $account->currency }}
            </div>
            <span class="badge {{ $account->status === 'active' ? 'badge-success' : 'badge-secondary' }}">
                {{ $account->status }}
            </span>
        </div>
    </div>

    <div class="card-body p-3">
        @if ($account->notes)
            <div class="alert alert-light border small mb-3">
                <strong>Notes:</strong> {{ $account->notes }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 100px;">Date</th>
                        <th style="width: 110px;">Type</th>
                        <th>Description</th>
                        <th class="text-right" style="width: 110px;">Debit</th>
                        <th class="text-right" style="width: 110px;">Credit</th>
                        <th class="text-right" style="width: 130px;">Running</th>
                        <th style="width: 100px;">Method</th>
                        <th style="width: 120px;">Proof</th>
                        <th style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $e)
                        <tr>
                            <td>{{ \Illuminate\Support\Carbon::parse($e->entry_date)->format('Y-m-d') }}</td>
                            <td><span class="badge badge-light">{{ $e->ref_type }}</span></td>
                            <td class="small">{{ $e->description }}</td>
                            <td class="text-right text-success">{{ number_format($e->debit, 2) }}</td>
                            <td class="text-right text-danger">{{ number_format($e->credit, 2) }}</td>
                            <td class="text-right {{ $e->running_balance >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($e->running_balance, 2) }}
                            </td>
                            <td class="small">{{ $e->payment_method }}</td>
                            <td class="small">
                                @if ($e->online_proof_path)
                                    @php $path = asset($e->online_proof_path); @endphp
                                    @if (preg_match('/\.(jpe?g|png|webp)$/i', $e->online_proof_path))
                                        <a href="{{ $path }}" target="_blank"><img src="{{ $path }}"
                                                class="img-thumbnail" style="max-height:42px"></a>
                                    @else
                                        <a href="{{ $path }}" target="_blank">View file</a>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary js-edit-entry"
                                        data-entry-id="{{ $e->id }}"
                                        data-entry_date="{{ \Illuminate\Support\Carbon::parse($e->entry_date)->format('Y-m-d') }}"
                                        data-ref_type="{{ $e->ref_type }}" data-description="{{ $e->description }}"
                                        data-debit="{{ $e->debit }}" data-credit="{{ $e->credit }}"
                                        data-payment_method="{{ $e->payment_method }}"
                                        data-online_reference="{{ $e->online_reference }}"
                                        data-online_proof_path="{{ $e->online_proof_path }}">Edit</button>

                                    <button type="button" class="btn btn-outline-danger js-delete-entry"
                                        data-entry-id="{{ $e->id }}">Delete</button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No entries yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
