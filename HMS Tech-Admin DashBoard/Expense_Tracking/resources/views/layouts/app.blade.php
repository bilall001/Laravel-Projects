<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Expense Tracker')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    @stack('styles')
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            z-index: 1050;
            height: 60px;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            background-color: #343a40;
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            padding-top: 20px;
            transition: all 0.3s ease-in-out;
        }

        .sidebar a {
            color: #ffffff;
            padding: 15px 25px;
            display: block;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        /* Main Content */
        .main-content {
            margin-left: 240px;
            margin-top: 60px;
            padding: 30px;
            min-height: 100vh;
            position: relative;
            background: linear-gradient(145deg, #f0f4f8, #ffffff);
            overflow: hidden;
        }

        /* Animated Background Dots */
        .animated-bg {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 0;
            background: radial-gradient(circle at 20% 20%, #cce5ff 2px, transparent 0),
                        radial-gradient(circle at 80% 80%, #dee2e6 2px, transparent 0);
            background-size: 120px 120px;
            opacity: 0.3;
            animation: moveBg 10s linear infinite;
        }

        @keyframes moveBg {
            from { background-position: 0 0, 0 0; }
            to { background-position: 120px 120px, 120px 120px; }
        }

        /* Responsive Fix */
        @media (max-width: 768px) {
            .sidebar {
                left: -240px;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.shifted {
                margin-left: 240px;
            }

            .sidebar-toggle {
                display: block;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                margin-right: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark fixed-top d-flex justify-content-between px-3 align-items-center">
    <div class="d-flex align-items-center">
        <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">☰</button>
        <a class="navbar-brand fw-bold ms-2" href="{{ route('dashboard') }}">💸 Expense Tracker</a>
    </div>
    <form method="POST" action="{{ route('logout') }}" class="mb-0">
        @csrf
        <button class="btn btn-outline-light btn-sm">Logout</button>
    </form>
</nav>

<!-- Sidebar -->
<div id="sidebar" class="sidebar">
    <a href="{{ route('dashboard') }}">🏠 Dashboard</a>
    <a href="{{ route('categories.index') }}">📂 Categories</a>
    <a href="{{ route('budgets.index') }}">📌 Budgets</a>
    <a href="{{ route('expenses.index') }}">🪙 Expenses</a>
    <a href="#">📊 Reports</a>
    <a href="#">⚙️ Settings</a>
</div>

<!-- Main Content -->
<div id="main" class="main-content">
    <div class="animated-bg"></div>
    <div class="position-relative z-1">
        @yield('content')
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Sidebar Toggle Script -->
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const main = document.getElementById('main');
        sidebar.classList.toggle('show');
        main.classList.toggle('shifted');
    }
</script>

</body>
</html>
