<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectSchedule;
use Illuminate\Http\Request;

class ProjectScheduleController extends Controller
{
    public function index()
    {
        $schedules = ProjectSchedule::orderBy('id', 'desc')->get();
        return view('admin.pages.projectSchedule.add_project_schedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'  => 'required|string',
            'date'   => 'required|date',
            'status' => 'required'
        ]);

        ProjectSchedule::create($data);

        return redirect()
            ->route('projectSchedule.index')
            ->with('success', 'Schedule created successfully.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title'  => 'required|string',
            'date'   => 'required|date',
            'status' => 'required'
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
