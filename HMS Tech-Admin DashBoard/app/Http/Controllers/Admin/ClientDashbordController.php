<?php

namespace App\Http\Controllers\Admin;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClientDashbordController extends Controller
{
    public function dashboard()
    {
        // Find the client record for logged-in user
        $clientModel = Client::where('user_id', Auth::id())->first();

        if (!$clientModel) {
            return view('admin.pages.clients.dashboard', [
                'client' => Auth::user(),
                'projects' => collect(),
                'totalProjects' => 0,
                'currentProjects' => 0,
                'completedProjects' => 0,
                'totalAmountSpent' => 0,
                'remainingAmount' => 0,
                'latestProject' => null,
                'previousProjects' => collect(),
            ]);
        }

        // Get projects linked to this client
        $projects = Project::where('client_id', $clientModel->id)->get();

        $totalProjects = $projects->count();
        $currentProjects = $projects->where('status', '!=', 'completed')->count();
        $completedProjects = $projects->where('status', 'completed')->count();

        // Use price, paid_price and remaining_price fields from table
        $totalAmountSpent = $projects->sum('price');
        $totalPaid = $projects->sum('paid_price');
        $remainingAmount = $projects->sum('remaining_price');
       $amountspent = $projects->sum('paid_price');
        // Latest project by creation date
        $latestProject = $projects->sortByDesc('created_at')->first();
        $previousProjects = $projects->sortByDesc('created_at')->skip(1);

        return view('admin.pages.clients.dashboard', compact(
            'clientModel',
            'projects',
            'totalProjects',
            'currentProjects',
            'completedProjects',
            'totalAmountSpent',
            'remainingAmount',
            'latestProject',
            'previousProjects',
            'amountspent'
        ))->with('client', Auth::user());
    }
}
