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
use Carbon\Carbon;

class PointsController extends Controller
{
    /**
     * Show points and projects for logged-in developer
     */
    public function developerPoints()
    {
        $developer = Auth::user();

        $teams = $developer?->teams ?? collect();
        $developers = collect([$developer]);

        // ✅ Get the same projects as on the dashboard (assigned by admin)
        $projects = Project::where('user_id', $developer->id)->get();

        $points = Point::with(['project', 'team'])
            ->where('developer_id', $developer->id)
            ->latest()
            ->get();

        return view('admin.pages.points.all_points', compact(
            'developer',
            'teams',
            'developers',
            'points',
            'projects'
        ));
    }

    /**
     * Ajax: Get projects for developer or team
     */
    public function getProjectsForDeveloper(Request $request)
    {
        if ($request->team_id) {
            return Project::where('team_id', $request->team_id)
                ->with('user')
                ->get(['id', 'title', 'file', 'end_date', 'user_id']);
        }

        if ($request->developer_id) {
            return Project::where('user_id', $request->developer_id)
                ->with('user')
                ->get(['id', 'title', 'file', 'end_date', 'user_id']);
        }

        return response()->json([]);
    }

    /**
     * Store submission from developer
     */
    public function storeFromDeveloper(Request $request)
    {
        $request->validate([
            'team_id'    => 'nullable|exists:teams,id',
            'project_id' => 'required|exists:projects,id',
            'video_link' => 'nullable|url',
            'video_file' => 'nullable|file|mimes:mp4,mkv,avi,mov|max:50000',
        ]);

        $developer = Auth::user();
        $project   = Project::findOrFail($request->project_id);

        // ✅ Ensure developer owns the project
        if ($project->user_id !== $developer->id) {
            abort(403, 'Unauthorized action.');
        }

        // ✅ Prevent duplicate submission for the same project
        $alreadySubmitted = Point::where('developer_id', $developer->id)
            ->where('project_id', $project->id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()->route('developer.points')
                ->with('error', '❌ You have already submitted this project.');
        }

        // ✅ Use developer_end_date if available, else fall back to project end_date
        $endDate = $project->developer_end_date ?? $project->end_date;

        $today = Carbon::today();
        $points = 0;

        if ($endDate) {
            $endDateCarbon = Carbon::parse($endDate);

            // difference (negative if late)
            $diffDays = $today->diffInDays($endDateCarbon, false);

            // ✅ +10 for early days, -10 for late days
            $points = $diffDays * 10;
        }

        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('points', 'public');
        }

        Point::create([
            'developer_id' => $developer->id,
            'team_id'      => $request->team_id,
            'project_id'   => $request->project_id,
            'video_link'   => $request->video_link,
            'video_file'   => $videoPath,
            'points'       => $points,
            'uploaded_at'  => now(),
        ]);

        return redirect()->route('developer.points')
            ->with('success', '✅ Submission added. You earned ' . $points . ' points.');
    }

    /**
     * Delete developer’s own submission
     */
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
