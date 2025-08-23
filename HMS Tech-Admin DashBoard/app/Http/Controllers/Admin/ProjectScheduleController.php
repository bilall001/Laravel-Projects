<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectSchedule;
use App\Models\Project; // Add this
use Illuminate\Http\Request;

class ProjectScheduleController extends Controller
{
    public function index()
    {
        // Get schedules with related project
        $schedules = ProjectSchedule::with('project')->orderBy('id', 'desc')->get();
        // Also get all projects to populate a dropdown in your view
        $projects = Project::all();

        return view('admin.pages.projectSchedule.add_project_schedule', compact('schedules', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date'       => 'required|date',
            'status'     => 'required|string'
        ]);

        ProjectSchedule::create($data);

        return redirect()
            ->route('projectSchedule.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'date'       => 'required|date',
            'status'     => 'required|string'
        ]);

        $schedule = ProjectSchedule::findOrFail($id);
        $schedule->update($data);

        return redirect()
            ->route('projectSchedule.index')
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy($id)
    {
        ProjectSchedule::findOrFail($id)->delete();

        return redirect()
            ->route('projectSchedule.index')
            ->with('success', 'Schedule deleted successfully.');
    }
}

