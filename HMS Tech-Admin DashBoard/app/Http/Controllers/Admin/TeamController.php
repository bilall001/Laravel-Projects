<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\AddUser;
use App\Models\Developer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    public function index(Request $request)
    {
        // Eager load team lead and members (developers)
        $teams = Team::with(['teamLead.user', 'developers.user'])->get();

        // Build developer list for the picker (value = Developer.id, label = AddUser.name)
        $developers = Developer::with('user')->get();

        $teamToEdit = null;
        if ($request->has('edit')) {
            $teamToEdit = Team::with('developers')->findOrFail($request->get('edit'));
        }

        // You no longer need $users (AddUser) for the picker — we’ll use $developers
        return view('admin.pages.team', compact('teams','developers','teamToEdit'));
    }

    public function store(Request $request)
    {
        Log::info('Team store request:', $request->all());

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'users'         => 'required|array',              // array of Developer IDs
            'users.*'       => 'exists:developers,id',
            'team_lead_id'  => 'nullable|exists:developers,id',
        ]);

        // Ensure team lead is one of the selected members
        if (!empty($validated['team_lead_id']) && !in_array($validated['team_lead_id'], $validated['users'])) {
            return back()->withErrors(['team_lead_id' => 'The team lead must be one of the selected team members.'])
                         ->withInput();
        }

        $team = Team::create([
            'name'         => $validated['name'],
            'team_lead_id' => $validated['team_lead_id'] ?? null,
        ]);

        // Attach developer IDs to pivot (team_user.team_id / developer_id)
        $team->developers()->attach($validated['users']);

        Log::info('Team created & developers attached', [
            'team_id' => $team->id,
            'developers' => $validated['users'],
        ]);

        return redirect()->route('admin.teams.index')->with('success', 'Team created successfully.');
    }

    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'users'         => 'required|array',         // array of Developer IDs
            'users.*'       => 'exists:developers,id',
            'team_lead_id'  => 'nullable|exists:developers,id',
        ]);
        // dd($validated['users']);    
        // Optional: enforce lead ∈ members
        // if (!empty($validated['team_lead_id']) && !in_array($validated['team_lead_id'], $validated['users'])) {
            //     return back()->withErrors(['team_lead_id' => 'The team lead must be one of the selected team members.'])
            //                  ->withInput();
            // }
            
            $team->update([
                'name'         => $validated['name'],
                'team_lead_id' => $validated['team_lead_id'] ?? null,
            ]);
            
            // Sync developer IDs on pivot
            $team->developers()->sync($validated['users']);
            Log::info('Update team users:', $request->input('users', []));
        return redirect()->route('admin.teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->developers()->detach();
        $team->delete();

        return back()->with('success', 'Team deleted successfully.');
    }

    public function getTeamUsers($teamId)
    {
        $team = Team::with('developers.user')->findOrFail($teamId);
        return response()->json($team->developers);
    }
}
