@extends("layouts.app")

@section("content")
<section class="content">
    <h1 class="mb-4">Dashboard</h1>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Tasks</h5>
                    <p class="card-text display-6">{{ $totalTasks }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <p class="card-text display-6">{{ $completedTasks }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <p class="card-text display-6">{{ $pendingTasks }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="card">
        <div class="card-header">
            📈 Task Status Overview
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Pie Chart -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0">
                        <div class="card-body" style="height: 300px;">
                            <h5 class="card-title text-center text-primary">Pie Chart</h5>
                            <canvas id="pieChart" width="100%" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow border-0">
                        <div class="card-body" style="height: 300px;">
                            <h5 class="card-title text-center text-success">Bar Chart</h5>
                            <canvas id="barChart" width="100%" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const labels = ['Pending', 'In Progress', 'Completed'];
        const dataValues = [
            {{ $statusCounts['pending'] ?? 0 }},
            {{ $statusCounts['in_progress'] ?? 0 }},
            {{ $statusCounts['completed'] ?? 0 }}
        ];

        const colors = [
            'rgba(255, 193, 7, 0.7)',    // Pending - Yellow
            'rgba(0, 123, 255, 0.7)',    // In Progress - Blue
            'rgba(40, 167, 69, 0.7)'     // Completed - Green
        ];

        // Pie Chart
        new Chart(document.getElementById('pieChart'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Bar Chart
        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Task Count',
                    data: dataValues,
                    backgroundColor: colors,
                    borderColor: '#000',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
</section>
@endsection
