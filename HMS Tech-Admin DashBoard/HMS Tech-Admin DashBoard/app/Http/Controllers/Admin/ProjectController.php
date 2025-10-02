<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\Client;
use App\Models\AddUser;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Developer;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::with(['teams', 'developers', 'client', 'businessDeveloper','memberRoles.role', 'memberRoles.developer.user'])
            ->latest() // Orders by 'created_at' descending
            ->paginate(10); // Paginates the results, 10 per page
        $teams = Team::all();
        $users = Developer::with('user')->get();
        $businessDevelopers = AddUser::where('role', 'business developer')->get();
        $clients = Client::all();

        $editProject = null;
        $showModal = false;
        // $showProject = false;
        $showProject = null;
        if ($request->has('edit_id') && $request->filled('edit_id')) {
            $editProject = Project::with(['teams', 'developers'])->find($request->edit_id);
            $showModal = true;
        }

        if ($request->has('show_id')) {
            $showProject = Project::with(['teams', 'developers', 'client', 'businessDeveloper'])
                ->find($request->show_id);
            // $showProject = true;
        }
        if ($request->has('add')) {
            $showModal = true;
        }

        return view('admin.pages.project', compact(
            'projects',
            'teams',
            'users',
            'businessDevelopers',
            'clients',
            'editProject',
            'showModal',
            'showProject'
        ));
    }

    public function show(Project $project)
    {
        return view('admin.pages.show_project', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'paid_price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'developer_end_date' => 'nullable|date',
            'type' => 'required|in:team,individual',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'developers' => 'nullable|array',
            'developers.*' => 'exists:developers,id',
            'client_id' => 'nullable|exists:clients,id',
            'business_developer_id' => 'nullable|exists:add_users,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
            'body_html' => ['nullable', 'string'],
            'body_json' => ['nullable', 'string'],
        ]);

        $project = new Project();
        $project->title = $request->title;
        $project->type = $request->type;
        $project->client_id = $request->client_id;
        $project->business_developer_id = $request->business_developer_id;
        $project->price = floatval($request->price ?? 0);
        $project->paid_price = floatval($request->paid_price ?? 0);
        $project->remaining_price = max($project->price - $project->paid_price, 0);
        $project->duration = $request->duration;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->developer_end_date = $request->developer_end_date;
        $project->body_html = $request->body_html;
        $project->body_json = $request->body_json;
        if ($request->hasFile('file')) {
            $project->file = $request->file('file')->store('projects', 'public');
        }

        $project->save();

        // 🔹 Sync teams and developers
        if ($request->has('teams')) {
            $project->teams()->sync($request->teams);
        }
        if ($request->has('developers')) {
            $project->developers()->sync($request->developers);
        }

        return redirect()->route('admin.projects.index')
            ->with('success', 'Project created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'paid_price' => 'nullable|numeric',
            'duration' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'developer_end_date' => 'nullable|date',
            'type' => 'required|in:team,individual',
            'teams' => 'nullable|array',
            'teams.*' => 'exists:teams,id',
            'developers' => 'nullable|array',
            'developers.*' => 'exists:developers,id',
            'client_id' => 'nullable|exists:clients,id',
            'business_developer_id' => 'nullable|exists:add_users,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:2048',
            'body_html' => ['nullable', 'string'],
            'body_json' => ['nullable', 'string'],
        ]);

        $project = Project::findOrFail($id);
        $project->title = $request->title;
        $project->type = $request->type;
        $project->client_id = $request->client_id;
        $project->business_developer_id = $request->business_developer_id;
        $project->price = floatval($request->price ?? 0);
        $project->paid_price = floatval($request->paid_price ?? 0);
        $project->remaining_price = max($project->price - $project->paid_price, 0);
        $project->duration = $request->duration;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->developer_end_date = $request->developer_end_date;
        $project->body_html = $request->body_html;
        $project->body_json = $request->body_json;
        if ($request->hasFile('file')) {
            $project->file = $request->file('file')->store('projects', 'public');
        }

        $project->save();

        if ($request->type === 'team') {
            $project->teams()->sync($request->teams ?? []); // will attach/detach automatically
            $project->developers()->detach(); // clear old developers
        } elseif ($request->type === 'individual') {
            $project->developers()->sync($request->developers ?? []);
            $project->teams()->detach(); // clear old teams
        }


        return redirect()->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->teams()->detach();
        $project->developers()->detach();
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}
