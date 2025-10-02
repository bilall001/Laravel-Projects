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
        $client = Client::where('user_id', Auth::id())->first();

        if (!$client) {
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
        // $projects = Project::where('client_id', $clientModel->id)->get();
        $projects = $client
            ? Project::where('client_id', $client->id)
            ->with(['schedules' => function ($query) {
                $query->orderByDesc('id'); // latest schedule first
            }])
            ->get()
            : collect();

        $totalProjects = $projects->count();
        $completedProjects = Project::where('client_id', $client->id)
            ->whereHas('schedules', function ($query) {
                $query->where('status', 'completed');
            })->count();

        $currentProjects = Project::where('client_id', $client->id)
            ->whereHas('schedules', function ($query) {
                $query->where('status', 'inProgress');
            })->count();

        // Use price, paid_price and remaining_price fields from table
        $totalAmountSpent = $projects->sum('price');
        $totalPaid = $projects->sum('paid_price');
        $remainingAmount = $projects->sum('remaining_price');
        $amountspent = $projects->sum('paid_price');
        // Latest project by creation date
        $latestProject = $projects->sortByDesc('created_at')->first();
        $previousProjects = $projects->sortByDesc('created_at')->skip(1);

        return view('admin.pages.clients.dashboard', compact(
            'client',
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
