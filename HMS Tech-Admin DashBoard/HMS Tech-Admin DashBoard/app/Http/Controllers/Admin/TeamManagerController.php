<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamManager;
use App\Models\AddUser;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TeamManagerController extends Controller
{
    public function index()
    {
        $teamManagers = TeamManager::with('user', 'teams')->get();
        // dd($teamManagers);
        $users = AddUser::where('role', 'team manager')->get();
        $teams = Team::all();
        return view('admin.pages.teamManager.index', compact('teamManagers', 'users', 'teams'));
    }

    public function store(Request $request)
    {
        $request->merge([
    'team_ids' => array_filter($request->team_ids) // removes null/empty
]);

        $request->validate([
            'user_id' => 'required|exists:add_users,id|unique:team_managers,user_id',
            // 'team_id' => 'required|exists:teams,id|unique:team_managers,team_id',
            'phone' => 'required|string|max:20',
            'experience' => 'nullable|string|max:255',
            'skill1' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:2048', // max 2MB
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // max 5MB
            'team_ids' => 'required|array',
            'team_ids.*' => 'exists:teams,id'
        ]);


        $data = $request->only(['user_id', 'phone', 'experience', 'skill1', 'profile_image', 'contract_file']);
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('team_managers/profile_images', 'public');
        }
        
        if ($request->hasFile('contract_file')) {
            $data['contract_file'] = $request->file('contract_file')->store('team_managers/contracts', 'public');
        }
        
        // TeamManager::create($data);
        // Create the manager
        $manager = TeamManager::create($data);
        $manager->teams()->sync($request->team_ids);
        // dd($manager);
        
    // Sync multiple teams in pivot table

        return redirect()->route('team_managers.index')->with('success', 'Team Manager added successfully.');
    }

   public function update(Request $request, TeamManager $team_manager)
{
    $request->validate([
        'user_id' => 'required|exists:add_users,id|unique:team_managers,user_id,' . $team_manager->id,
        'phone' => 'required|string|max:20',
        'experience' => 'nullable|string|max:255',
        'skill1' => 'required|string|max:255',
        'profile_image' => 'nullable|image|max:2048',
        'contract_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        'team_ids' => 'required|array',
        'team_ids.*' => 'nullable|exists:teams,id',
    ]);

    $data = $request->only([
        'user_id',
        'phone',
        'experience',
        'skill1',
        'profile_image',
        'contract_file'
    ]);

    // Handle profile image
    if ($request->hasFile('profile_image')) {
        if ($team_manager->profile_image) {
            Storage::disk('public')->delete($team_manager->profile_image);
        }
        $data['profile_image'] = $request->file('profile_image')
            ->store('team_managers/profile_images', 'public');
    }

    // Handle contract file
    if ($request->hasFile('contract_file')) {
        if ($team_manager->contract_file) {
            Storage::disk('public')->delete($team_manager->contract_file);
        }
        $data['contract_file'] = $request->file('contract_file')
            ->store('team_managers/contracts', 'public');
    }

    // Update manager
    $team_manager->update($data);

    // Sync teams
    $team_manager->teams()->sync(array_filter($request->team_ids));

    return redirect()->route('team_managers.index')
        ->with('success', 'Team Manager updated successfully.');
}

    public function destroy(TeamManager $team_manager)
    {
        $team_manager->delete();

        return redirect()->route('team_managers.index')->with('success', 'Team Manager deleted successfully.');
    }
}
