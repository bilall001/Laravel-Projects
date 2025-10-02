<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PointController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PointsController;
use App\Http\Controllers\Admin\SalaryController;
use App\Http\Controllers\Admin\AddUserController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\PasswordControler;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\DeveloperController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\TeamManagerController;
use App\Http\Controllers\Admin\ClientDashbordController;
use App\Http\Controllers\Admin\CompanyExpenseController;
use App\Http\Controllers\Admin\ProjectScheduleController;
use App\Http\Controllers\Admin\BusinessDeveloperController;
use App\Http\Controllers\Admin\DeveloperProjectPaymentController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\TeamManagerDashboardController;
use App\Http\Controllers\Admin\KhataAccountController;
use App\Http\Controllers\Admin\KhataEntryController;
use App\Http\Controllers\Admin\ImpersonationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProfitController;
use App\Http\Controllers\Admin\ProjectRoleController;
use App\Http\Controllers\Admin\TaskAssetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Redirect root to login or dashboard
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login.form');
    }
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'business developer' => redirect()->route('business-developer.dashboard'),
        'client' => redirect()->route('client.dashboard'),
        'partner' => redirect()->route('admin.dashboard'),
        'team manager' => redirect()->route('teamManager.dashboard'),
        'developer' => redirect()->route('developer.dashboard'),
        default => redirect()->route('login.form'),
    };
});

// START impersonation: only admins can start
Route::middleware(['auth', 'can_impersonate'])->post('/admin/impersonate/{user}', [ImpersonationController::class, 'start'])
    ->name('impersonate.start');

// STOP impersonation: ANY logged-in session can stop (admin is currently impersonating a non-admin)
Route::middleware(['auth'])->post('/admin/impersonate/stop', [ImpersonationController::class, 'stop'])
    ->name('impersonate.stop');

// Everything below here requires authentication    
Route::middleware(['auth'])->group(function () {

    // Admin Dashboard
    Route::get('/admin', [AdminController::class, 'TotalCLients'])->name('admin.dashboard');

    // Passwords
    Route::resource('passwords', PasswordControler::class);

    // Profile
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    // Add Users
    Route::resource('add-users', AddUserController::class)->except(['create']);

    // Developers
    Route::resource('developers', DeveloperController::class);

    // Developer Project Payments
    Route::resource('developer_project_payments', DeveloperProjectPaymentController::class)->except(['show', 'create', 'edit']);
    // Leads
    Route::resource('leads', LeadController::class);
    // Route::post('/leads/show', [LeadController::class, 'show'])->name('leads.show');
    // Route::post('/leads/fields', [LeadController::class, 'fields'])->name('leads.fields');

    // Accounts
    Route::get('/khata/accounts',          [KhataAccountController::class, 'index'])->name('khata.accounts.index');
    Route::post('/khata/accounts',         [KhataAccountController::class, 'store'])->name('khata.accounts.store');
    Route::get('/khata/accounts/{id}/modal', [KhataAccountController::class, 'showModal'])
        ->name('khata.accounts.modal');
    Route::patch('/khata/accounts/{id}',   [KhataAccountController::class, 'update'])->name('khata.accounts.update');
    Route::delete('/khata/accounts/{id}',  [KhataAccountController::class, 'destroy'])->name('khata.accounts.destroy');


    // Entries
    Route::post('/khata/accounts/{khataAccountId}/entries', [KhataEntryController::class, 'store'])->name('khata.entries.store');
    Route::patch('/khata/entries/{entryId}',               [KhataEntryController::class, 'update'])->name('khata.entries.update');
    Route::delete('/khata/entries/{entryId}',              [KhataEntryController::class, 'destroy'])->name('khata.entries.destroy');
    // Clients
    Route::prefix('admin')->group(function () {
        Route::resource('clients', ClientController::class);
    });
    // Team-Manager
    Route::prefix('admin')->group(function () {
        Route::resource('team_managers', TeamManagerController::class)->except(['show', 'create', 'edit']);
    });

    //Tasks all routes
    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Task assets (editor images)
    Route::post('/tasks/assets/upload', [TaskAssetController::class, 'upload'])->name('tasks.assets.upload');
    Route::delete('/tasks/assets/{image}', [TaskAssetController::class, 'destroy'])->name('tasks.assets.destroy');

    // Project roles
    Route::get('/projects/{project}/roles', [ProjectRoleController::class, 'index'])->name('projects.roles.index');
    Route::post('/projects/{project}/roles/assign/devs', [ProjectRoleController::class, 'assignToDevelopers'])->name('projects.roles.assign.devs');
    Route::post('/projects/{project}/roles/assign/teams', [ProjectRoleController::class, 'assignToTeams'])->name('projects.roles.assign.teams');
    Route::post('/projects/{project}/roles/revoke/devs', [ProjectRoleController::class, 'revokeFromDevelopers'])->name('projects.roles.revoke.devs');
    // project text images
    Route::post('/projects/assets/upload', [\App\Http\Controllers\Admin\ProjectAssetController::class, 'store'])
        ->name('projects.assets.upload');
    Route::delete('/projects/assets/{asset}', [\App\Http\Controllers\Admin\ProjectAssetController::class, 'destroy'])
        ->name('projects.assets.destroy');
    // roles
    Route::prefix('projects/{project}/roles')->name('admin.projects.roles.')->group(function () {
        Route::get('/', [ProjectRoleController::class, 'index'])->name('index');
        Route::post('/assign-developers', [ProjectRoleController::class, 'assignToDevelopers'])->name('assignToDevelopers');
        Route::post('/assign-teams', [ProjectRoleController::class, 'assignToTeams'])->name('assignToTeams');
        Route::post('/revoke', [ProjectRoleController::class, 'revokeFromDevelopers'])->name('revokeFromDevelopers');
    });

    // Company Expenses
    Route::resource('companyExpense', CompanyExpenseController::class);

    // Teams
    Route::resource('teams', TeamController::class);

    // Client Dashboard
    Route::get('/client/dashboard', [ClientDashbordController::class, 'dashboard'])
        ->middleware(['role:client'])
        ->name('client.dashboard');
    // developer dashboard
    Route::get('/developer/dashboard', [DeveloperController::class, 'index'])
        ->middleware(['role:developer'])
        ->name('developer.dashboard');
    // Team Managers Dashboard
    Route::get('/TeamManager/dashboard', [TeamManagerDashboardController::class, 'index'])
        ->middleware(['role:team manager'])
        ->name('teamManager.dashboard');

    // Attendance
    Route::prefix('admin')->name('attendances.')->group(function () {
        Route::get('/attendances', [AttendanceController::class, 'index'])->name('index');
        Route::post('/attendances/store', [AttendanceController::class, 'store'])->name('store');
        Route::post('/attendances/mark-leave', [AttendanceController::class, 'markLeave'])->name('markLeave');
        Route::post('/attendances/mark-absent', [AttendanceController::class, 'markAbsent'])->name('markAbsent');
    });

    // Points for Developers
    Route::get('/developer/points', [PointsController::class, 'developerPoints'])->name('developer.points');
    Route::post('/developer/points', [PointsController::class, 'storeFromDeveloper'])->name('developer.points.store');
    Route::delete('/developer/points/{id}', [PointsController::class, 'destroy'])->name('developer.points.destroy');
    Route::get('/points/get-projects', [PointsController::class, 'getProjectsForDeveloper']);
    Route::put('/developer/points/{id}', [PointsController::class, 'update'])->name('developer.points.update');
    Route::get('/admin/developer-points', [PointsController::class, 'indexForAdmin'])
        ->name('admin.developer.points'); // you can also restrict to role:admin

    // Project Schedule
    Route::resource('projectSchedule', ProjectScheduleController::class);

    // Admin Teams
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('teams', TeamController::class)->except(['show']);
    });

    // Admin Projects
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('projects', ProjectController::class);
    });

    // Admin Dashboard (Stats) + Tasks
    Route::prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'TotalCLients'])->name('admin.dashboard');

        //     Route::get('/tasks', [TaskController::class, 'index'])->name('admin.tasks.index');
        //     Route::post('/tasks', [TaskController::class, 'store'])->name('admin.tasks.store');
        //     Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('admin.tasks.edit');
        //     Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('admin.tasks.update');
        //     Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('admin.tasks.destroy');

        //     Route::get('/project-files/{filename}', [TaskController::class, 'viewProjectFile'])
        //         ->name('projects.view_file');
    });

    // Extra task routes
    Route::get('/admin/teams/{id}/users', [TeamController::class, 'getUsers']);
    // Route::get('/admin/tasks/project-info/{title}', [TaskController::class, 'getProjectInfo']);

    // Salaries
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('salaries', SalaryController::class);
    });

    // Partners
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('partners', PartnerController::class);
    });
});
Route::prefix('admin/profits')->name('admin.profits.')->group(function() {
    Route::get('/', [ProfitController::class, 'index'])->name('index');
    Route::post('/generate', [ProfitController::class, 'generateMonthlyProfit'])->name('generate');
    Route::put('/{id}/received', [ProfitController::class, 'markReceived'])->name('received');
    Route::put('/{id}/reinvest', [ProfitController::class, 'markReinvested'])->name('reinvest');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('business-developers', BusinessDeveloperController::class);
    Route::get('business-developer/dashboard', [BusinessDeveloperController::class, 'dashboard'])
        ->name('business-developer.dashboard');
});



Route::middleware(['auth', 'role:partner'])->group(function () {
    Route::get('/partner/dashboard', [PartnerController::class, 'dashboard'])->name('partner.dashboard');
});
