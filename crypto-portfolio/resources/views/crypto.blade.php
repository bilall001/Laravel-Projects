@extends('layouts.main')

@section('content')
<div class="row g-4">
    <!-- Stats Cards -->
    <div class="col-md-3">
        <div class="card p-3 text-center shadow-lg border-0 bg-gradient-primary text-white rounded-4">
            <h6 class="fw-bold">Total Coins</h6>
            <h3>{{ $cryptos->count() }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center shadow-lg border-0 bg-gradient-success text-white rounded-4">
            <h6 class="fw-bold">Highest Price</h6>
            <h3>${{ number_format($cryptos->max('prices.first.price_usd') ?? 0, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center shadow-lg border-0 bg-gradient-danger text-white rounded-4">
            <h6 class="fw-bold">Lowest Price</h6>
            <h3>${{ number_format($cryptos->min('prices.first.price_usd') ?? 0, 2) }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 text-center shadow-lg border-0 bg-gradient-dark text-white rounded-4">
            <h6 class="fw-bold">Last Updated</h6>
            <h3>{{ now()->format('h:i A') }}</h3>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card p-4 shadow-lg rounded-4 border-0">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up"></i> Bitcoin Price Trend</h5>
            <canvas id="priceChart" height="100"></canvas>
        </div>
    </div>
</div>

<!-- Crypto Table -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card p-4 shadow-lg rounded-4 border-0">
            <h5 class="fw-bold"><i class="bi bi-currency-bitcoin"></i> Cryptocurrencies</h5>
            <div class="table-responsive mt-3">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Symbol</th>
                            <th>Latest Price (USD)</th>
                            <th>24h Change</th>
                            <th>Market Cap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cryptos as $crypto)
                            <tr>
                                <td class="fw-bold">{{ $crypto->name }}</td>
                                <td>{{ strtoupper($crypto->symbol) }}</td>
                             <td>
    @if($crypto->prices && $crypto->prices->first() && $crypto->prices->first()->price_usd)
        <span class="badge bg-light text-dark">
            ${{ number_format($crypto->prices->first()->price_usd, 2) }}
        </span>
    @else
        <span class="badge bg-secondary">N/A</span>
    @endif
</td>

                             <td>
    @php
        // Check if prices exists and get change_24h, default to 0 if not set
        $change = $crypto->prices && $crypto->prices->first() ? $crypto->prices->first()->change_24h ?? 0 : 0;
    @endphp
    <span class="fw-bold {{ $change >= 0 ? 'text-success' : 'text-danger' }}">
        {{ number_format($change, 2) }}%
        {!! $change >= 0 ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>' !!}
    </span>
</td>

<td>
    @if($crypto->prices && $crypto->prices->first() && $crypto->prices->first()->market_cap)
        ${{ number_format($crypto->prices->first()->market_cap, 0) }}
    @else
        <span class="badge bg-secondary">N/A</span>
    @endif
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('priceChart').getContext('2d');
    const priceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($btcLabels ?? []) !!},
            datasets: [{
                label: 'BTC Price (USD)',
                data: {!! json_encode($btcPrices ?? []) !!},
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.2)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 3,
                pointBackgroundColor: '#0d6efd'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { labels: { color: '#212529', font: { weight: 'bold' } } }
            },
            scales: {
                x: { ticks: { color: '#495057', font: { weight: 'bold' } } },
                y: { ticks: { color: '#495057', font: { weight: 'bold' } } }
            }
        }
    });
</script>
@endsection
