@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-container min-vh-100 p-4" style="font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e0f7fa, #ffffff); animation: bgFade 15s ease-in-out infinite alternate;">
        <div class="row mb-5 text-center">
            <div class="col-12">
                <h1 class="fw-bold text-primary">Welcome Back, {{ Auth::user()->name }}</h1>
                <p class="text-muted">Here’s a quick summary of your expenses.</p>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card text-bg-primary shadow-lg h-100">
                    <div class="card-body">
                        <h5 class="card-title">💰 Total Expenses</h5>
                        <p class="card-text fs-4">Rs. {{ number_format($totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-bg-success shadow-lg h-100">
                    <div class="card-body">
                        <h5 class="card-title">📊 Budget Limit</h5>
                        <p class="card-text fs-4">Rs. {{ number_format($budgetLimit, 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-bg-warning shadow-lg h-100">
                    <div class="card-body">
                        <h5 class="card-title">🧾 Remaining Budget</h5>
                        <p class="card-text fs-4">Rs. {{ number_format($budgetLimit - $totalExpenses, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Filters -->
        {{-- <div class="row my-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">Expense Category Distribution</div>
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">Monthly Expenses</div>
                    <div class="card-body">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Filters -->
        <div class="row mb-4 mt-4" >
            <div class="col-md-4">
                <select class="form-select">
                    <option selected disabled>Filter by Category</option>
                    {{-- @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-4">
                <input type="month" class="form-control" placeholder="Filter by Month">
            </div>
            <div class="col-md-4">
                <button class="btn btn-dark w-100">Apply Filters</button>
            </div>
        </div>

        <!-- Recent Expenses Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>🕒 Recent Expenses</span>
                <a href="#" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentExpenses as $index => $expense)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $expense->title }}</td>
                                <td>Rs. {{ number_format($expense->amount, 2) }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $expense->category->name ?? 'Uncategorized' }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No recent expenses recorded.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

        @keyframes bgFade {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .dashboard-container {
            background-size: 200% 200%;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- <script>
        const pieChart = new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode($pieChartLabels) !!},
                datasets: [{
                    label: 'Expenses',
                    data: {!! json_encode($pieChartData) !!},
                    backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'],
                }]
            }
        });

        const barChart = new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($barChartLabels) !!},
                datasets: [{
                    label: 'Monthly Expenses',
                    data: {!! json_encode($barChartData) !!},
                    backgroundColor: '#17a2b8',
                    borderRadius: 5
                }]
            }
        });
    </script> --}}
@endsection
