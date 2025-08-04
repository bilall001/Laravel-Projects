<?php

namespace App\Http\Controllers;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){
        $user =  Auth::user();
        return view('dashboard',compact('user'));
    }
       public function dashboard()
{
        $statusCounts = Task::selectRaw("status, COUNT(*) as count")
        ->groupBy('status')
        ->pluck('count', 'status');

    $totalTasks = Task::count();
    $pendingTasks = Task::where('status', 'pending')->count();
    $completedTasks = Task::where('status', 'completed')->count();
    return view('dashboard', compact('statusCounts','totalTasks', 'pendingTasks', 'completedTasks'));
}
}
