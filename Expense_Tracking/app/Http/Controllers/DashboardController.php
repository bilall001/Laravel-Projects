<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $totalExpenses = Expense::where('user_id', $userId)->sum('amount');
        $budgetLimit = 50000; // You can fetch from budgets table if dynamic
        $recentExpenses = Expense::with('category')
                                ->where('user_id', $userId)
                                ->latest()
                                ->take(5)
                                ->get();

        // Get all expenses with categories
        $expenses = Expense::with('category')
                        ->where('user_id', $userId)
                        ->get();

        // Pie Chart: Group by Category
        $pieGrouped = $expenses->groupBy(fn($e) => $e->category->name ?? 'Uncategorized');
        $pieChartLabels = $pieGrouped->keys()->toJson();
        $pieChartData = $pieGrouped->map->sum('amount')->values()->toJson();

        // Bar Chart: Group by date (last 7 days)
        $barExpenses = Expense::where('user_id', $userId)
                            ->whereDate('expense_date', '>=', now()->subDays(6))
                            ->orderBy('expense_date')
                            ->get();

        $barGrouped = $barExpenses->groupBy(fn($e) => Carbon::parse($e->expense_date)->format('d M'));
        $barChartLabels = $barGrouped->keys()->toJson();
        $barChartData = $barGrouped->map->sum('amount')->values()->toJson();

        return view('dashboard', [
            'totalExpenses' => $totalExpenses,
            'budgetLimit' => $budgetLimit,
            'recentExpenses' => $recentExpenses,
            'pieChartLabels' => $pieChartLabels,
            'pieChartData' => $pieChartData,
            'barChartLabels' => $barChartLabels,
            'barChartData' => $barChartData,
        ]);
    }
}
