<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CompanyExpense;
use App\Models\Project;
use App\Models\ProjectSchedule;
// use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminController extends Controller
{
    public function TotalCLients()
{
    // Existing logic
    $clients = Client::count();
    $projects = Project::count();
    $currentProjects = ProjectSchedule::where('status', 'deliver')->count();
    $monthCompletedProjects = ProjectSchedule::where('status', 'complete')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

    $totalIncome = Project::sum('price');
    $monthExpense  = CompanyExpense::whereMonth('created_at', Carbon::now()->month)
        ->sum('amount');
    $monthProfit = $totalIncome - $monthExpense;

    // 📊 Monthly Income, Expense, Profit
    $monthlyData = [];

    foreach (range(1, 12) as $month) {
        $income = Project::whereMonth('created_at', $month)->whereYear('created_at', date('Y'))->sum('price');
        $expense = CompanyExpense::whereMonth('created_at', $month)->whereYear('created_at', date('Y'))->sum('amount');
        $profit = $income - $expense;
 $profitPercentage = $income > 0 ? round(($profit / $income) * 100, 2) : 0;
        $monthlyData[] = [
            'month' => Carbon::create()->month($month)->format('M'),
            'income' => $income,
            'expense' => $expense,
            'profit' => $profit,
            'profit_percentage' => $profitPercentage,
        ];
    }

    return view('admin.index', compact(
        'clients', 'projects', 'currentProjects', 'monthCompletedProjects',
        'totalIncome', 'monthExpense', 'monthProfit', 'profitPercentage','monthlyData'
    ));
} 
}