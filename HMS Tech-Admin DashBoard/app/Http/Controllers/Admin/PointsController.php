<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\Point;
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
    $user = Auth::user();

    // ✅ Try to get developer profile
    $developer = $user->developer;

    // ✅ If none exists (admin, tester, etc.), create one on the fly
    if (!$developer) {
        $developer = \App\Models\Developer::firstOrCreate(
            ['add_user_id' => $user->id],
            [
                'skill' => 'Admin Tester',
                'experience' => '0',
                'part_time' => false,
                'full_time' => false,
                'internship' => false,
                'job' => false,
                'salary_type' => 'project_based',
                'salary' => 0,
            ]
        );
    }

    // ✅ Fetch data normally (same as for real developers)
    $teams = $developer->teams ?? collect();
    $projects = $developer->projects()->with('teams')->get();
    $points = $developer->points()->with(['project', 'team'])->latest()->get();

    return view('admin.pages.points.all_points', compact(
        'developer',
        'teams',
        'projects',
        'points'
    ));
}

    /**
     * Ajax: Get projects for developer or team
     */
    public function getProjectsForDeveloper(Request $request)
    {
        if ($request->team_id) {
            $team = Team::with('projects')->find($request->team_id);
            return $team ? $team->projects()->with('developers')->get() : [];
        }

        if ($request->developer_id) {
            $developer = \App\Models\Developer::with('projects')->find($request->developer_id);
            return $developer ? $developer->projects()->with('teams')->get() : [];
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
            'github_url' => 'nullable|url',
        ]);

        $user = Auth::user();
        $developer = $user->developer;

        if (!$developer) {
            abort(403, 'Unauthorized: You are not a developer.');
        }

        $project = Project::with('developers')->findOrFail($request->project_id);

        // ✅ Ensure developer is assigned to the project
        if (!$project->developers->contains($developer->id)) {
            abort(403, 'Unauthorized: This project is not assigned to you.');
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
             'github_url'   => $request->github_url,  
            'video_file'   => $videoPath,
            'points'       => $points,
            'uploaded_at'  => now(),
        ]);

        return redirect()->route('developer.points')
            ->with('success', '✅ Submission added. You earned ' . $points . ' points.');
    }

  public function update(Request $request, $id)
{
    $developer = Auth::user()->developer;

    $point = Point::where('id', $id)
        ->where('developer_id', $developer->id)
        ->with('project')
        ->firstOrFail();

    // ✅ Check if still editable (before deadline)
    $endDate = $point->project->developer_end_date ?? $point->project->end_date;
    if ($endDate && Carbon::today()->gt(Carbon::parse($endDate))) {
        return redirect()->route('developer.points')
            ->with('error', '❌ Submission can no longer be edited (deadline passed).');
    }

    $request->validate([
        'video_link' => 'nullable|url',
        'video_file' => 'nullable|file|mimes:mp4,mkv,avi,mov|max:50000',
        'github_url' => 'nullable|url',
    ]);

    $videoPath = $point->video_file;
    if ($request->hasFile('video_file')) {
        $videoPath = $request->file('video_file')->store('points', 'public');
    }

    // ✅ Recalculate points based on TODAY
    $points = 0;
    if ($endDate) {
        $endDateCarbon = Carbon::parse($endDate);
        $diffDays = Carbon::today()->diffInDays($endDateCarbon, false);
        $points = $diffDays * 10; // +10 per early day, -10 per late day
    }

    $point->update([
        'video_link'  => $request->video_link,
        'video_file'  => $videoPath,
        'github_url'  => $request->github_url,
        'uploaded_at' => now(),
        'points'      => $points,   // ✅ updated points
    ]);

    return redirect()->route('developer.points')
        ->with('success', '✅ Submission updated. New points: ' . $points);
}

public function indexForAdmin(Request $request)
{
    $query = Point::with(['developer.user', 'project', 'team']);

    // 🔎 Filter by Developer
    if ($request->filled('developer_id')) {
        $query->where('developer_id', $request->developer_id);
    }

    // 🔎 Filter by Team
    if ($request->filled('team_id')) {
        $query->where('team_id', $request->team_id);
    }

    // 🔎 Filter by Project
    if ($request->filled('project_id')) {
        $query->where('project_id', $request->project_id);
    }

    // 🔎 Filter by Date Range
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('uploaded_at', [
            Carbon::parse($request->from_date)->startOfDay(),
            Carbon::parse($request->to_date)->endOfDay(),
        ]);
    }

    $points = $query->latest()->paginate(20);

    // For filter dropdowns
    $developers = \App\Models\Developer::with('user')->get();
    $teams = \App\Models\Team::all();
    $projects = \App\Models\Project::all();

    return view('admin.pages.points.index', compact('points', 'developers', 'teams', 'projects'));
}


    /**
     * Delete developer’s own submission
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $developer = $user->developer;

        $point = Point::where('id', $id)
            ->where('developer_id', $developer->id)
            ->firstOrFail();

        $point->delete();

        return redirect()->route('developer.points')->with('success', 'Submission deleted.');
    }
}
