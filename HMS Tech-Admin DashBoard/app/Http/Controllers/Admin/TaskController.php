<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;

class TaskController extends Controller
{
    public function index()
    {
$tasks = Task::with(['project.client', 'team', 'user'])->get();
        $projects = Project::with(['team', 'user','client.user'])->get();
        $uniqueTitles = $projects->pluck('title')->unique();

        return view('admin.pages.task', compact('tasks', 'projects', 'uniqueTitles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'required|string',
            'end_date' => 'required|date',
        ]);

        $project = Project::with(['team', 'user'])->findOrFail($request->project_id);

        Task::create([
            'project_id' => $project->id,
            'team_id' => $project->type === 'team' ? $project->team_id : null,
            'user_id' => $project->type === 'individual' ? $project->user_id : null,
            'description' => $request->description,
            'end_date' => $request->end_date,
        ]);

        return redirect()->route('admin.tasks.index')->with('success', 'Task created successfully.');
    }

public function getProjectInfo($title)
{
    $project = Project::where('title', $title)->with(['team', 'user', 'client.user'])->first();

    if (!$project) {
        return response()->json(['error' => 'Project not found'], 404);
    }
    // dd($project);
    return response()->json($project);

    // return response()->json([
    //     'id' => $project->id,
    //     'file' => $project->file,
    //     'type' => $project->type,
    //     'team' => $project->team,
    //     'user' => $project->user,
    //     'client' => $project->client,
    // ]);
}



 public function edit($id)
{
    $task = Task::with(['project', 'team', 'user'])->findOrFail($id);
    $tasks = Task::with(['project', 'team', 'user'])->get();
    $projects = Project::all();
    $uniqueTitles = Project::pluck('title')->unique();

    return view('admin.pages.task', compact('task', 'tasks', 'projects', 'uniqueTitles'));
}

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'description' => 'required|string',
            'end_date' => 'required|date',
        ]);

        $project = Project::with(['team', 'user'])->findOrFail($request->project_id);

        $task->update([
            'project_id' => $project->id,
            'team_id' => $project->type === 'team' ? $project->team_id : null,
            'user_id' => $project->type === 'individual' ? $project->user_id : null,
            'description' => $request->description,
            'end_date' => $request->end_date,
        ]);

        return back()->with('success', 'Task updated successfully.');
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return back()->with('success', 'Task deleted.');
    }
}