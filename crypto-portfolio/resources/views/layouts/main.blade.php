<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #0d1117;
            color: #e6edf3;
        }
        .navbar {
            background-color: #161b22;
        }
        .card {
            background-color: #161b22;
            border: none;
            border-radius: 12px;
        }
        .table {
            color: #e6edf3;
        }
        .table thead {
            background-color: #21262d;
        }
        .price-up {
            color: #00c853;
        }
        .price-down {
            color: #ff1744;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">💰 Crypto Dashboard</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid mt-4">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
