<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamManagerDashboardController extends Controller
{
    public function index()
    {
        $manager = auth()->user()->teamManager->first(); // relationship in User model

        // dd($manager);
        if (!$manager) {
            // Either redirect, show an error, or use a fallback
            abort(403, 'No team manager profile found for this user.');
        }
        // $teamId = $manager->team_id;
        $teamId = $manager->teams->pluck('id');

        // dd($teamId);

        $clients = DB::table('clients')
            ->whereExists(function ($query) use ($manager) {
                $query->select(DB::raw(1))
                    ->from('projects')
                    ->join('team_manager_teams', 'projects.team_id', '=', 'team_manager_teams.team_id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('team_manager_teams.team_manager_id', $manager->id);
            })
            ->count();
        // dd($clients);
        $projects = Project::whereIn('team_id', $teamId)->count();
        // dd($projects);
        $today = now()->toDateString(); // e.g., '2025-08-18'
        // dd($today, gettype($today));
        $currentProjects = DB::table('projects')
            ->whereIn('team_id', $teamId)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->count();
        // dd($currentProjects);
        // dd((new \App\Models\Project)->getGlobalScopes());
        $query = DB::table('projects')->where('team_id', 2);

        // dd($query->toSql(), $query->getBindings());
        $monthCompletedProjects =  DB::table('projects')
            ->whereIn('team_id', $teamId)
            ->whereDate('end_date', '<=', now())
            ->whereMonth('end_date', now()->month)
            ->whereYear('end_date', now()->year)
            ->count();
            // dd($monthCompletedProjects);
        // $totalIncome = Project::where('team_id', $teamId)->sum('paid_price');
        $totalIncome = DB::table('projects')->where('team_id', 2)->sum('paid_price');
        $monthExpense = 0; // Placeholder — no expenses table yet
        $monthProfit = $totalIncome - $monthExpense;

        return view('admin.pages.teamManager.dashboard', compact(
            'clients',
            'projects',
            'currentProjects',
            'monthCompletedProjects',
            'totalIncome',
            'monthExpense',
            'monthProfit'
        ));
    }
}
