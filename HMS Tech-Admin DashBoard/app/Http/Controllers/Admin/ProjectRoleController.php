<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Team;
use App\Models\Developer;
use App\Models\Role;
use App\Models\ProjectMemberRole;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProjectRoleController extends Controller
{
    /**
     * Optional index: roles overview for a project (for a modal)
     */
    public function index(Project $project)
    {
        $project->load(['teams.developers.user', 'developers.user']);
        $roles = Role::orderBy('name')->get();

        // Eligible developers for this project (use your helper if you want)
        $eligibleDevelopers = $project->eligibleDevelopers();

        // Existing assignments
        $memberRoles = ProjectMemberRole::with(['developer.user', 'role'])
            ->where('project_id', $project->id)
            ->get();

        return view('admin.pages.roles', compact('project', 'roles', 'eligibleDevelopers', 'memberRoles'));
    }

    /**
     * Assign a role to selected developers in a project
     */
    public function assignToDevelopers(Request $request, Project $project)
    {
        $request->validate([
            'role_id'        => ['required', Rule::exists('roles', 'id')],
            'developer_ids'  => ['required', 'array'],
            'developer_ids.*'=> [Rule::exists('developers', 'id')],
        ]);

        // ensure targets are eligible
        $eligibleIds = $project->eligibleDevelopers()->pluck('id');
        $targetIds = collect($request->developer_ids)->intersect($eligibleIds)->values();

        DB::transaction(function () use ($project, $request, $targetIds) {
            $rows = $targetIds->map(fn ($devId) => [
                'project_id'   => $project->id,
                'developer_id' => $devId,
                'role_id'      => $request->role_id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ])->all();

            // Use upsert for idempotency
            ProjectMemberRole::upsert(
                $rows,
                ['project_id', 'developer_id', 'role_id'],
                ['updated_at']
            );
        });

        return back()->with('success', 'Role assigned to selected developers.');
    }

    /**
     * Assign a role to all members of selected team(s) within the project
     */
    public function assignToTeams(Request $request, Project $project)
    {
        $request->validate([
            'role_id'   => ['required', Rule::exists('roles', 'id')],
            'team_ids'  => ['required', 'array'],
            'team_ids.*'=> [Rule::exists('teams', 'id')],
        ]);

        // Only consider teams attached to the project (optional extra guard)
        $projectTeamIds = $project->teams()->pluck('teams.id')->toArray();
        $teamIds = collect($request->team_ids)->intersect($projectTeamIds);

        // Gather members of those teams
        $developerIds = Team::whereIn('id', $teamIds)
            ->with('developers:id')
            ->get()
            ->flatMap(fn ($t) => $t->developers->pluck('id'))
            ->unique()
            ->values();

        // Also intersect with eligible (in case)
        $eligibleIds = $project->eligibleDevelopers()->pluck('id');
        $developerIds = $developerIds->intersect($eligibleIds)->values();

        DB::transaction(function () use ($project, $request, $developerIds) {
            $rows = $developerIds->map(fn ($devId) => [
                'project_id'   => $project->id,
                'developer_id' => $devId,
                'role_id'      => $request->role_id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ])->all();

            ProjectMemberRole::upsert(
                $rows,
                ['project_id', 'developer_id', 'role_id'],
                ['updated_at']
            );
        });

        return back()->with('success', 'Role assigned to all members of selected teams.');
    }

    /**
     * Revoke a role from selected developers in this project
     */
    public function revokeFromDevelopers(Request $request, Project $project)
    {
        $request->validate([
            'role_id'        => ['required', Rule::exists('roles', 'id')],
            'developer_ids'  => ['required', 'array'],
            'developer_ids.*'=> [Rule::exists('developers', 'id')],
        ]);

        ProjectMemberRole::where('project_id', $project->id)
            ->where('role_id', $request->role_id)
            ->whereIn('developer_id', $request->developer_ids)
            ->delete();

        return back()->with('success', 'Role revoked from selected developers.');
    }
}
