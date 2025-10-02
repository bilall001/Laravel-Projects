<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Team;
use App\Models\Project;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * List tasks (optionally by project)
     */
    public function index(Request $request)
    {
        $query = Task::with([
            'project.teams.developers.user', // for filters + names
            'project.developers.user',       // for individual projects
            'assignees.user',
            'assignees.teams',
            'teams',
            'images',
            'creator',
            'assignedDeveloper.user', 'project.memberRoles.role',
        ]);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->paginate(20);

        // Projects data for create/edit modals
        $projects = Project::with([
            'teams:id,name',
            'teams.developers:id,add_user_id',
            'teams.developers.user:id,name',
            'developers:id,add_user_id',
            'developers.user:id,name',
        ])->orderBy('title')->get();

        return view('admin.pages.task', compact('tasks', 'projects'));
    }

    /**
     * Create task
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => ['required', Rule::exists('projects', 'id')],
            'title'      => ['required', 'string', 'max:255'],
            'body_html'  => ['nullable', 'string'],
            'body_json'  => ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
           'status' => ['nullable', Rule::in(['pending', 'in_progress', 'review', 'completed'])],
            'priority'   => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],

            'team_ids'       => ['nullable', 'array'],
            'team_ids.*'     => [Rule::exists('teams', 'id')],
            'developer_ids'  => ['nullable', 'array'],
            'developer_ids.*'=> [Rule::exists('developers', 'id')],

            'assign_all_team_members' => ['nullable', 'boolean'],
        ]);

        $project = Project::with(['teams.developers', 'developers'])->findOrFail($request->project_id);

        // Eligible devs for this project
        $eligibleIds = $this->eligibleDeveloperIds($project);

        // Build assignees
        $developerIds = collect($request->input('developer_ids', []));

        if ($request->boolean('assign_all_team_members') && $request->filled('team_ids')) {
            $teamMemberIds = Team::whereIn('id', $request->team_ids)
                ->with('developers:id')
                ->get()
                ->flatMap(fn($t) => $t->developers->pluck('id'))
                ->unique();
            $developerIds = $developerIds->merge($teamMemberIds);
        }

        $developerIds = $developerIds->unique()->intersect($eligibleIds)->values();

        DB::transaction(function () use ($request, $project, $developerIds) {
            $task = Task::create([
                'project_id' => $project->id,
                'title'      => $request->title,
                'body_html'  => $request->body_html,
                'body_json'  => $request->body_json,
                'due_date'   => $request->due_date,
                'status'     => $request->input('status', 'pending'),
                'priority'   => $request->input('priority', 'normal'),
                'created_by' => Auth::id(),
            ]);

            // Team labels only for team projects
            if ($project->type === 'team' && $request->filled('team_ids') && method_exists($task, 'teams')) {
                $task->teams()->sync($request->team_ids);
            }

            $task->assignees()->sync($developerIds);
        });

        return back()->with('success', 'Task created successfully.');
    }

    /**
     * Update task
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title'      => ['required', 'string', 'max:255'],
            'body_html'  => ['nullable', 'string'],
            'body_json'  => ['nullable', 'string'],
            'due_date'   => ['nullable', 'date'],
           'status' => ['nullable', Rule::in(['pending', 'in_progress', 'review', 'completed'])],
            'priority'   => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],

            'team_ids'       => ['nullable', 'array'],
            'team_ids.*'     => [Rule::exists('teams', 'id')],
            'developer_ids'  => ['nullable', 'array'],
            'developer_ids.*'=> [Rule::exists('developers', 'id')],

            'assign_all_team_members' => ['nullable', 'boolean'],
        ]);

        $project = $task->project()->with(['teams.developers', 'developers'])->firstOrFail();
        $eligibleIds = $this->eligibleDeveloperIds($project);

        $developerIds = collect($request->input('developer_ids', []));

        if ($request->boolean('assign_all_team_members') && $request->filled('team_ids')) {
            $teamMemberIds = Team::whereIn('id', $request->team_ids)
                ->with('developers:id')
                ->get()
                ->flatMap(fn($t) => $t->developers->pluck('id'))
                ->unique();
            $developerIds = $developerIds->merge($teamMemberIds);
        }

        $developerIds = $developerIds->unique()->intersect($eligibleIds)->values();

        DB::transaction(function () use ($request, $task, $project, $developerIds) {
            $task->update([
                'title'     => $request->title,
                'body_html' => $request->body_html,
                'body_json' => $request->body_json,
                'due_date'  => $request->due_date,
                'status'    => $request->input('status', $task->status),
                'priority'  => $request->input('priority', $task->priority),
            ]);

            // Team labels only for team projects; clear otherwise
            if ($project->type === 'team' && $request->filled('team_ids') && method_exists($task, 'teams')) {
                $task->teams()->sync($request->team_ids);
            } else {
                if (method_exists($task, 'teams')) {
                    $task->teams()->detach();
                }
            }

            $task->assignees()->sync($developerIds);
        });

        return back()->with('success', 'Task updated successfully.');
    }

    /**
     * Show task
     */
    public function show(Task $task)
    {
        $task->load(['project', 'assignees.user', 'teams', 'images', 'creator']);
        return view('admin.pages.tasks.show', compact('task'));
    }

    /**
     * Delete task
     */
    public function destroy(Task $task)
    {
        $task->assignees()->detach();
        if (method_exists($task, 'teams')) {
            $task->teams()->detach();
        }
        $task->delete();
        return back()->with('success', 'Task deleted successfully.');
    }

    /**
     * Eligible developer IDs for a project
     */
    protected function eligibleDeveloperIds(Project $project)
    {
        if ($project->type === 'individual') {
            return $project->developers()->pluck('developers.id'); // direct project devs
        }

        // team-type: union of all members of attached teams
        $teamIds = $project->teams()->pluck('teams.id');

        return Developer::whereHas('teams', function ($q) use ($teamIds) {
            $q->whereIn('teams.id', $teamIds);
        })->pluck('developers.id');
    }
}
