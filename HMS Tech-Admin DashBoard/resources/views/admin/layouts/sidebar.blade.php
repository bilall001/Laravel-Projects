<body class="dark-sidenav">
    <!-- Left Sidenav -->
    <div class="left-sidenav">
        @php
            $user = Auth::user();

            // Determine the dashboard route based on user role
            switch ($user->role) {
                case 'admin':
                    $dashboardRoute = route('admin.dashboard');
                    break;
                case 'developer':
                    $dashboardRoute = route('developers.index');
                    break;
                case 'client':
                    $dashboardRoute = route('client.dashboard');
                    break;
                case 'team manager':
                    $dashboardRoute = route('teamManager.dashboard');
                    break;
                default:
                    $dashboardRoute = route('admin.dashboard'); // fallback or 404
                    break;
            }
        @endphp

        <div class="brand">
            <a href="{{ $dashboardRoute }}" class="logo">
                <span>
                    <img src="/assets/admin/images/HMS_TECH_LOGO1-02.png" alt="logo-small" style="width: 115px;">
                </span>
            </a>
        </div>

        <!-- LOGO -->
        {{-- <div class="brand">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <span>
                    <img src="/assets/admin/images/HMS_TECH_LOGO1-02.png" alt="logo-small" style="width: 115px;">
                </span>
            </a>
        </div> --}}
        <!-- end logo -->

        <div class="menu-content h-100" data-simplebar>
            <ul class="metismenu left-sidenav-menu">

                <!-- Welcome -->
                <li>
                    <span style="font-weight:bold; margin-left:10px; color: white;">
                        Welcome, {{ Auth::user()->name }}
                    </span>
                </li>


                @auth

                    {{-- =========================== ADMIN =========================== --}}
                    @if (auth()->user()->role === 'admin')
                        <!-- Dashboard -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}">
                                <i class="mdi mdi-view-dashboard-outline" style="font-size: 18px;"></i> Dashboard
                            </a>
                        </li>

                        <!-- Users -->
                        <li>
                            <a href="{{ route('add-users.index') }}">
                                <i class="mdi mdi-account-multiple-outline" style="font-size: 18px;"></i> Users
                            </a>
                        </li>

                        <!-- Developers -->
                        <li>
                            <a href="{{ route('developers.create') }}">
                                <i class="mdi mdi-code-braces" style="font-size: 18px;"></i> Developers
                            </a>
                        </li>

                        <!-- Clients -->
                        <li>
                            <a href="{{ route('clients.index') }}">
                                <i class="mdi mdi-account-tie-outline" style="font-size: 18px;"></i> Clients
                            </a>
                        </li>

                        <!-- Company Expense -->
                        <li>
                            <a href="{{ route('companyExpense.index') }}">
                                <i class="mdi mdi-cash-multiple" style="font-size: 18px;"></i> Company Expense
                            </a>
                        </li>

                        <!-- Attendance -->
                        <li>
                            <a href="{{ route('attendances.index') }}">
                                <i class="mdi mdi-calendar-check-outline" style="font-size: 18px;"></i> Attendance
                            </a>
                        </li>

                        <!-- Manage Teams -->
                        <li>
                            <a href="{{ route('admin.teams.index') }}">
                                <i class="mdi mdi-account-group-outline" style="font-size: 18px;"></i> Manage Teams
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('team_managers.index') }}">
                                <i class="mdi mdi-account-tie" style="font-size: 18px;"></i> Manage Teams
                            </a>
                        </li>

                        <!-- Manage Projects -->
                        <li>
                            <a href="{{ route('admin.projects.index') }}">
                                <i class="mdi mdi-briefcase-outline" style="font-size: 18px;"></i> Manage Projects
                            </a>
                        </li>

                        <!-- Manage Tasks -->
                        <li>
                            <a href="{{ route('admin.tasks.index') }}">
                                <i class="mdi mdi-clipboard-check-outline" style="font-size: 18px;"></i> Manage Tasks
                            </a>
                        </li>

                        <!-- Project Schedule -->
                        <li>
                            <a href="{{ route('projectSchedule.index') }}">
                                <i class="mdi mdi-calendar-clock"
                                    style="font-size: 18px; vertical-align: middle; margin-right:8px;"></i>
                                Project Schedule
                            </a>
                        </li>


                        <!-- Developer Points -->
                        <li>
                            <a href="{{ route('developer.points') }}">
                                <i class="mdi mdi-star-outline" style="font-size: 18px;"></i> Developer Points
                            </a>
                        </li>

                        <!-- Manage Salaries -->
                        <li>
                            <a href="{{ route('admin.salaries.index') }}">
                                <i class="mdi mdi-currency-usd" style="font-size: 18px;"></i> Manage Salaries
                            </a>
                        </li>

                        <!-- Manage Partners -->
                        <li>
                            <a href="{{ route('admin.partners.index') }}">
                                <i class="bi bi-people"
                                    style="font-size: 18px; vertical-align: middle; margin-right:8px;"></i>
                                Manage Partners
                            </a>
                        </li>

                        <!-- Manage Business Developers -->
                        <li>
                            <a href="{{ route('business-developers.index') }}">
                                <i class="bi bi-people"
                                    style="font-size: 18px; vertical-align: middle; margin-right:8px;"></i>
                                Business Developer
                            </a>
                        </li>




                        {{-- =========================== TEAM MANAGER =========================== --}}
                    @elseif(auth()->user()->role === 'team manager')
                    <li>
                    <a href="{{ route('teamManager.dashboard') }}">
                        <i class="mdi mdi-view-dashboard-outline" style="font-size: 18px;"></i> Dashboard
                    </a>
                </li>
                        <li><a href="{{ route('add-users.index') }}">
                                <i class="mdi mdi-account-multiple-outline" style="font-size: 18px;"></i> Users
                            </a></li>

                        <li><a href="{{ route('developers.index') }}">
                                <i class="mdi mdi-code-braces" style="font-size: 18px;"></i> Developers
                            </a></li>

                        <li><a href="{{ route('clients.index') }}">
                                <i class="mdi mdi-account-tie-outline" style="font-size: 18px;"></i> Clients
                            </a></li>

                        <li><a href="{{ route('companyExpense.index') }}">
                                <i class="mdi mdi-cash-multiple" style="font-size: 18px;"></i> Company Expense
                            </a></li>

                        <li><a href="{{ route('attendances.index') }}">
                                <i class="mdi mdi-calendar-check-outline" style="font-size: 18px;"></i> Attendance
                            </a></li>

                        <li><a href="{{ route('admin.teams.index') }}">
                                <i class="mdi mdi-account-group-outline" style="font-size: 18px;"></i> Manage Teams
                            </a></li>

                        <li><a href="{{ route('admin.projects.index') }}">
                                <i class="mdi mdi-briefcase-outline" style="font-size: 18px;"></i> Manage Projects
                            </a></li>

                        <li><a href="{{ route('admin.tasks.index') }}">
                                <i class="mdi mdi-clipboard-check-outline" style="font-size: 18px;"></i> Manage Tasks
                            </a></li>

                        <li><a href="{{ route('projectSchedule.index') }}">
                                <i class="mdi mdi-calendar-clock-outline" style="font-size: 18px;"></i> Project Schedule
                            </a></li>

                        <li><a href="{{ route('developer.points') }}">
                                <i class="mdi mdi-star-outline" style="font-size: 18px;"></i> Developer Points
                            </a></li>

                        <li><a href="{{ route('admin.salaries.index') }}">
                                <i class="mdi mdi-currency-usd" style="font-size: 18px;"></i> Manage Salaries
                            </a></li>

                        {{-- =========================== BUSINESS DEVELOPER =========================== --}}
                    @elseif(auth()->user()->role === 'business developer')
                        {{-- =========================== DEVELOPER =========================== --}}
                    @elseif(auth()->user()->role === 'developer')
                        <li>
                            <a href="{{ route('developers.index') }}">
                                <i class="mdi mdi-view-dashboard-outline" style="font-size: 18px;"></i> Dashboard
                            </a>
                        </li>
                        <li><a href="{{ route('developer.points') }}">
                                <i class="mdi mdi-star-outline" style="font-size: 18px;"></i> My Points
                            </a></li>
                    @endif

                    {{-- =========================== CLIENT =========================== --}}
                    @if (auth()->check() && auth()->user()->role === 'client')
                        <li>
                            <a href="{{ route('client.dashboard') }}">
                                <i class="mdi mdi-briefcase-account-outline" style="font-size: 18px;"></i> My Projects
                            </a>
                        </li>
                    @endif
                    @if (auth()->check() && auth()->user()->role === 'partner')
                        <li>
                            <a href="{{ route('partner.dashboard') }}">
                                <i class="mdi mdi-briefcase-account-outline" style="font-size: 18px;"></i> Dashboard
                            </a>
                        </li>
                    @endif

                @endauth

            </ul>
        </div>
    </div>
</body>
