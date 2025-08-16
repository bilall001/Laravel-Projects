<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\AddUser;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        // $teams = Team::with('users')->get();
        $teams = Team::with(['teamLead.user', 'users'])->get();

        // Fetch only users with role 'developer' who are present in Developer table
        $developerIds = Developer::pluck('add_user_id');
        $users = AddUser::where('role', 'developer')
                        ->whereIn('id', $developerIds)
                        ->get();
        $developers = Developer::with('user')->get();
        $teamToEdit = null;
        // dd($developers); 
        if ($request->has('edit')) {
            $teamToEdit = Team::with('users')->findOrFail($request->get('edit'));
        }

        return view('admin.pages.team', compact('teams','developers', 'users', 'teamToEdit'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array',
            'users.*' => 'exists:add_users,id',
            'team_lead_id' => 'nullable|exists:developers,id'
        ]);

        // Ensure team lead is part of the selected members
        // if (!empty($validated['team_lead_id']) && !in_array($validated['team_lead_id'], $validated['users'])) {
        //     return back()->withErrors(['team_lead_id' => 'The team lead must be one of the selected team members.'])->withInput();
        // }
        
        $team = Team::create([
            'name' => $validated['name'],
            'team_lead_id' => $validated['team_lead_id'] ?? null,
        ]);

        $team->users()->attach($validated['users']);

        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully.');
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array',
            'users.*' => 'exists:add_users,id',
            'team_lead_id' => 'nullable|exists:developers,id'
        ]);

        // Ensure team lead is part of the selected members
        // if (!empty($validated['team_lead_id']) && !in_array($validated['team_lead_id'], $validated['users'])) {
        //     return back()->withErrors(['team_lead_id' => 'The team lead must be one of the selected team members.'])->withInput();
        // }

        $team->update([
            'name' => $validated['name'],
            'team_lead_id' => $validated['team_lead_id'] ?? null,
        ]);

        $team->users()->sync($validated['users']);

        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->users()->detach();
        $team->delete();

        return back()->with('success', 'Team deleted successfully.');
    }

    public function getTeamUsers($teamId)
    {
        $team = Team::with('users')->findOrFail($teamId);
        return response()->json($team->users);
    }
}
