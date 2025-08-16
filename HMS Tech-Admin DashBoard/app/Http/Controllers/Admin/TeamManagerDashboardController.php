<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;

class TeamManagerDashboardController extends Controller
{
    public function index(){
    $manager = auth()->user()->teamManager->first(); // relationship in User model
     if (!$manager) {
        // Either redirect, show an error, or use a fallback
        abort(403, 'No team manager profile found for this user.');
    }
    // dd($manager);
    // $teamId = $manager->team_id;
    $teamId = $manager->teams->pluck('id');

    $clients = Client::whereHas('projects', function($q) use ($teamId) {
        $q->where('team_id', $teamId);
    })->count();

    $projects = Project::whereIn('team_id', $teamId)->count();
    // dd($projects);
    $currentProjects = Project::where('team_id', $teamId)
        ->whereDate('start_date', '<=', now())
        ->whereDate('end_date', '>=', now())
        ->count();

    $monthCompletedProjects = Project::where('team_id', $teamId)
        ->whereMonth('end_date', now()->month)
        ->whereYear('end_date', now()->year)
        ->count();

    $totalIncome = Project::where('team_id', $teamId)->sum('paid_price');

    $monthExpense = 0; // Placeholder — no expenses table yet
    $monthProfit = $totalIncome - $monthExpense;

    return view('admin.pages.teamManager.dashboard', compact(
        'clients', 'projects', 'currentProjects', 
        'monthCompletedProjects', 'totalIncome', 
        'monthExpense', 'monthProfit'
    ));
}
}   