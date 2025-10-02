@extends('admin.layouts.main')

@section('title', 'Monthly Profits - HMS Tech & Solutions')

@section('content')
<div class="container-fluid mt-4">
    <h3 class="mb-4">💰 Monthly Profits & Partner Shares</h3>

    {{-- Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Generate Profit Button --}}
    <div class="mb-3">
        <form method="POST" action="{{ route('admin.profits.generate') }}" class="form-inline">
            @csrf
            <label class="mr-2">Select Month:</label>
            <input type="month" name="month" class="form-control mr-2" required>
            <button type="submit" class="btn btn-primary">⚡ Generate Profit</button>
        </form>
    </div>

    @forelse($monthlyProfits as $monthProfit)
        <div class="card shadow-sm border-0 mt-4 mb-4">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <div>
                 <h5 class="mb-0 text-white">
    📅 {{ \Carbon\Carbon::parse($monthProfit->month)->format('F Y') }}
    @if($monthProfit->locked)
        <span class="badge badge-success ml-2">Locked ✅</span>
    @else
        <span class="badge badge-warning ml-2">Updating 🔄</span>
    @endif
</h5>
<small>
    Revenue: <strong class="text-success">PKR {{ number_format($monthProfit->total_revenue, 2) }}</strong> | 
    Expenses: <strong class="text-danger">PKR {{ number_format($monthProfit->total_expenses, 2) }}</strong> | 
    Net Profit: <strong class="text-info">PKR {{ number_format($monthProfit->net_profit, 2) }}</strong>
</small>
                    
                </div>
                <button class="btn btn-sm btn-outline-info text-white" type="button" data-toggle="collapse" data-target="#month-{{ $monthProfit->id }}">
                    View Partner Shares ⬇
                </button>
            </div>

            <div class="collapse" id="month-{{ $monthProfit->id }}">
                <div class="card-body table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Partner</th>
                                <th>Email</th>
                                <th>Percentage</th>
                                <th>Profit Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($monthProfit->partnerProfits as $pProfit)
                                <tr>
                                    <td>{{ $pProfit->partner->user->name ?? 'N/A' }}</td>
                                    <td>{{ $pProfit->partner->user->email ?? 'N/A' }}</td>
                                    <td>{{ $pProfit->percentage }}%</td>
                                    <td><strong>PKR {{ number_format($pProfit->profit_amount, 2) }}</strong></td>
                                    <td>
                                        @if($pProfit->is_received)
                                            <span class="badge badge-success">✅ Received</span>
                                        @elseif($pProfit->reinvested)
                                            <span class="badge badge-warning">🔄 Reinvested</span>
                                        @else
                                            <span class="badge badge-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <form method="POST" action="{{ route('admin.profits.received', $pProfit->id) }}">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-success">Mark Received</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.profits.reinvest', $pProfit->id) }}">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-warning">Reinvest</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">No partner distribution yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No profits calculated yet. Generate for the first month above.</div>
    @endforelse

    <div class="mt-3">
        {{ $monthlyProfits->links() }}
    </div>
</div>
@endsection
