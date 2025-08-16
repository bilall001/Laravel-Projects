<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\Client;
use App\Models\AddUser;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = Project::all();
        $teams = Team::all();
        $users = AddUser::where('role', 'developer')->get();
        $businessDevelopers = AddUser::where('role', 'business developer')->get();
        $clients = Client::all();

        $editProject = null;
        $showModal = false;

        if ($request->has('edit_id') && $request->filled('edit_id')) {
            $editProject = Project::find($request->edit_id);
            $showModal = true;
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
            'showModal'
        ));
    }

    public function show(Project $project)
    {
        return view('admin.pages.show_project', compact('project'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'                  => 'required|string|max:255',
            'price'                  => 'nullable|numeric',
            'paid_price'             => 'nullable|numeric',
            'duration'               => 'nullable|string|max:255',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date',
            'developer_end_date'     => 'nullable|date',
            'type'                   => 'required|string',
            'team_id'                => 'nullable|integer',
            'user_id'                => 'nullable|integer',
            'client_id'              => 'nullable|integer',
            'business_developer_id'  => 'nullable|exists:add_users,id',
            'file'                   => 'nullable|file',
        ]);

        $project = new Project();
        $project->title = $request->title;
        $project->type = $request->type;
        $project->team_id = $request->team_id;
        $project->price = $request->price;
        $project->paid_price = $request->paid_price;
        $project->duration = $request->duration;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->developer_end_date = $request->developer_end_date;
        $project->user_id = $request->user_id;
        $project->client_id = $request->client_id;
        $project->business_developer_id = $request->business_developer_id;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('projects', 'public');
            $project->file = $filePath;
        }

        $project->save();

        return redirect()->back()->with('success', 'Project created successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title'                  => 'required|string|max:255',
            'price'                  => 'nullable|numeric',
            'paid_price'             => 'nullable|numeric',
            'duration'               => 'nullable|string|max:255',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date',
            'developer_end_date'     => 'nullable|date',
            'type'                   => 'required|in:team,individual',
            'team_id'                => 'nullable|exists:teams,id',
            'user_id'                => 'nullable|exists:add_users,id',
            'client_id'              => 'nullable|exists:clients,id',
            'business_developer_id'  => 'nullable|exists:add_users,id',
            'file'                   => 'nullable|file',
        ]);

        $project = Project::findOrFail($id);

        if ($request->hasFile('file')) {
            $project->file = $request->file('file')->store('projects', 'public');
        }

        $totalPrice = floatval($request->price ?? 0);
        $paidPrice = floatval($request->paid_price ?? 0);
        $remainingPrice = max($totalPrice - $paidPrice, 0);

        $project->title = $request->title;
        $project->price = $totalPrice;
        $project->paid_price = $paidPrice;
        $project->remaining_price = $remainingPrice;
        $project->duration = $request->duration;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->developer_end_date = $request->developer_end_date;
        $project->client_id = $request->client_id;
        $project->business_developer_id = $request->business_developer_id;

        $project->type = $request->type;
        $project->team_id = $request->type === 'team' ? $request->team_id : null;
        $project->user_id = $request->type === 'individual' ? $request->user_id : null;

        $project->save();

        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy($id)
    {
        $project = Project::findOrFail($id);
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }
}
