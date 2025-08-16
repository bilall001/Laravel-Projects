<?php

namespace App\Http\Controllers\Admin;

use App\Models\Task;
use App\Models\Team;
use App\Models\Point;
use App\Models\AddUser;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PointsController extends Controller
{
public function developerPoints()
    {
        $developer = Auth::user();  // ✅ get logged-in developer

        $teams = $developer?->teams ?? collect();  // ✅ only their teams

        $developers = collect([$developer]);  // ✅ only show themselves

        $points = Point::with(['project', 'team'])
            ->where('developer_id', $developer->id)
            ->latest()
            ->get();

        return view('admin.pages.points.all_points', compact('teams', 'developers', 'points'));
    }


public function getProjectsForDeveloper(Request $request)
{
    // Return projects by team if team_id provided
    if ($request->team_id) {
        return Project::where('team_id', $request->team_id)
            ->with('user')
            ->get(['id', 'title', 'file', 'end_date', 'user_id']);
    }

    // Return projects by developer if developer_id provided
    if ($request->developer_id) {
        return Project::where('user_id', $request->developer_id)
            ->with('user')
            ->get(['id', 'title', 'file', 'end_date', 'user_id']);
    }

    // Otherwise, empty
    return response()->json([]);
}





   public function storeFromDeveloper(Request $request)
{
    $request->validate([
        'team_id' => 'nullable|exists:teams,id',
        'project_id' => 'required|exists:projects,id',
        'video_link' => 'nullable|url',
        'video_file' => 'nullable|file|mimes:mp4,mkv,avi,mov|max:50000',
    ]);

    $developer = Auth::user();

    $project = Project::findOrFail($request->project_id);

    // You can also ensure that this developer owns the project, if you like:
    if ($project->user_id !== $developer->id) {
        abort(403, 'Unauthorized action.');
    }

    $task = Task::where('project_id', $project->id)->first();
    $endDate = $task ? $task->end_date : $project->end_date;

    $today = now()->toDateString();
    $points = ($endDate && $today <= $endDate) ? 20 : -10;

    $videoPath = null;
    if ($request->hasFile('video_file')) {
        $videoPath = $request->file('video_file')->store('points', 'public');
    }

    Point::create([
        'developer_id' => $developer->id,
        'team_id' => $request->team_id,
        'project_id' => $request->project_id,
        'video_link' => $request->video_link,
        'video_file' => $videoPath,
        'points' => $points,
        'uploaded_at' => now(),
    ]);

    return redirect()->route('developer.points')->with('success', 'Submission added. You earned ' . $points . ' points.');
}

  public function destroy($id)
{
    $developer = Auth::user();

    $point = Point::where('id', $id)
                  ->where('developer_id', $developer->id)
                  ->firstOrFail();

    $point->delete();

    return redirect()->route('developer.points')->with('success', 'Submission deleted.');
}

}