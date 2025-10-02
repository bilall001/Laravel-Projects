<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use App\Models\Salary;
use App\Models\AddUser;
use App\Models\Project;
use App\Models\Developer;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\Task;

class DeveloperController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // inside your controller method...
        if ($user->role === 'developer') {
            // 1) Find the developer row linked to this user
            $developer = Developer::where('add_user_id', $user->id)->first();

            if ($developer) {
                // 2) Projects assigned DIRECTLY to this developer (project_developer pivot)
                $directProjects = Project::query()
                    ->whereHas('developers', function ($q) use ($developer) {
                        $q->where('developers.id', $developer->id);
                        // or: $q->where('project_developer.developer_id', $developer->id);
                    })
                    ->with(['teams', 'businessDeveloper'])
                    ->get()
                    ->map(function ($project) {
                        $project->setAttribute('assignment_type', 'Individual');
                        return $project;
                    });
                $directProjectsCount = $directProjects->count();

                // 3) Teams the current user belongs to
                $teams = Team::whereHas('developers', function ($q) use ($developer) {
                    // adjust table/column names in where() to match your pivot if needed
                    $q->where('developers.id', $developer->id);
                })
                    ->with('developers')
                    ->get();

                $teamIds = $teams->pluck('id');

                // 4) Projects assigned to ANY of those teams (project_team pivot)
                $teamProjects = Project::query()
                    ->whereHas('teams', function ($q) use ($teamIds) {
                        $q->whereIn('teams.id', $teamIds);
                        // or: $q->whereIn('project_team.team_id', $teamIds);
                    })
                    ->with(['teams', 'businessDeveloper'])
                    ->get()
                    ->map(function ($project) {
                        $project->setAttribute('assignment_type', 'Team');
                        return $project;
                    });
                $teamProjectsCount = $teamProjects->count();

                // 5) Combine for table
                $allProjects = $directProjects->concat($teamProjects);

                // 6) Salaries (paid only)
                $salaries = Salary::where('add_user_id', $user->id)
                    ->where('is_paid', 1)
                    ->orderByDesc('salary_date')
                    ->get();

                // 7) Attendance (this month)
                $attendanceQuery = Attendance::where('user_id', $user->id)
                    ->whereMonth('date', now()->month)
                    ->whereYear('date', now()->year);

                $totalDays = (clone $attendanceQuery)->count();
                $presentDays = (clone $attendanceQuery)->where('status', 'present')->count();
                $absentDays = (clone $attendanceQuery)->where('is_absent', 1)->count();
                $leaveDays = (clone $attendanceQuery)->where('is_leave', 1)->count();
                $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

                $teamCount = $teams->count();
                // 8) Direct tasks assigned to this developer
                $directTasks = Task::whereHas('developers', function ($q) use ($developer) {
                    $q->where('developers.id', $developer->id);
                })
                    ->with('project')
                    ->get()
                    ->map(function ($task) {
                        $task->assignment_type = 'Individual';
                        return $task;
                    });
                $directTasksCount = $directTasks->count();

                // 9) Tasks via team projects
                $teamTasks = Task::whereHas('project.teams.developers', function ($q) use ($developer) {
                    $q->where('developers.id', $developer->id);
                })
                    ->with('project')
                    ->get()
                    ->map(function ($task) {
                        $task->assignment_type = 'Team';
                        return $task;
                    });
                $teamTasksCount = $teamTasks->count();

                // 10) Combine all tasks
                $allTasks = $directTasks->concat($teamTasks);

                // 11) Points calculation
                $totalPoints = 0;
                $bestScore = 0;
                $submissionsCount = 0;

                if ($developer) {
                    $points = Point::where('developer_id', $developer->id)->get();
                    $totalPoints = $points->sum('points');
                    $bestScore = $points->max('points') ?? 0;
                    $submissionsCount = $points->count();
                }
            } else {
                // Safe defaults if no developer row found for this user
                $developer = null;
                $directProjectsCount = 0;
                $teamProjectsCount = 0;
                $allProjects = collect();
                $teams = collect();
                $teamCount = 0;
                $salaries = collect();
                $totalDays = $presentDays = $absentDays = $leaveDays = $attendancePercentage = 0;
            }

            return view('admin.pages.developers.dashboard', compact(
                'developer',
                'directProjectsCount',
                'teamProjectsCount',
                'allProjects',
                'teams',
                'teamCount',
                'salaries',
                'totalDays',
                'presentDays',
                'absentDays',
                'leaveDays',
                'attendancePercentage',
                'allTasks',
                'directTasksCount',
                'teamTasksCount',
                'totalPoints',
                'bestScore',
                'submissionsCount'
            ));
        } else {
            $developers = Developer::orderBy('created_at', 'desc')->get();
            $users = AddUser::where('role', 'developer')->get();

            return view('admin.pages.developers.add_developer', compact('developers', 'users'));
        }
    }

    /**
     * 🔹 Dedicated dashboard for developers
     */
    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role !== 'developer') {
            abort(403, 'Unauthorized');
        }

        $developer = Developer::where('add_user_id', $user->id)->first();
        $projects = Project::where('user_id', $user->id)->get();
        // $salary = Salary::where('add_user_id', $user->id)
        //     ->orderBy('salary_date', 'desc')
        //     ->get();
        $salaries = Salary::where('add_user_id', $user->id)
            ->where('is_paid', 1)
            ->orderByDesc('salary_date')
            ->get();
        $attendanceQuery = Attendance::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $totalDays = $attendanceQuery->count();
        // dd($totalDays);
        $presentDays = (clone $attendanceQuery)->where('status', 'present')->count();
        $absentDays = (clone $attendanceQuery)->where('is_absent', 1)->count();
        $leaveDays = (clone $attendanceQuery)->where('is_leave', 1)->count();
        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;
        $teams = Team::whereHas('users', function ($query) use ($user) {
            $query->where('add_users.id', $user->id);
        })->with('users')->get();

        return view('admin.pages.developers.dashboard', compact(
            'developer',
            'projects',
            'teams',
            'salaries',
            'totalDays',
            'presentDays',
            'absentDays',
            'leaveDays',
            'attendancePercentage',
        ));
    }

    public function create()
    {
        $developers = Developer::with('user')->get();
        $users = AddUser::where('role', 'developer')->get();

        return view('admin.pages.developers.add_developer', compact('developers', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'add_user_id' => 'required|exists:add_users,id',
            'profile_image' => 'nullable|image',
            'cnic_front' => 'nullable|image',
            'cnic_back' => 'nullable|image',
            'contract_file' => 'nullable|file',
            'skill' => 'nullable|string',
            'experience' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'time_type' => 'nullable|string|in:part_time,full_time',
            'job_type' => 'nullable|string|in:internship,job',
            'salary_type' => 'required|string|in:salary_based,project_based',
        ]);
        // dd($data);
        // Reset work types
        $data['part_time'] = $data['time_type'] === 'part_time';
        $data['full_time'] = $data['time_type'] === 'full_time';
        $data['internship'] = $data['job_type'] === 'internship';
        $data['job'] = $data['job_type'] === 'job';

        if ($data['salary_type'] === 'project_based') {
            $data['salary'] = null;
        }
        foreach (['profile_image', 'cnic_front', 'cnic_back', 'contract_file'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/developers'), $filename);
                $data[$fileField] = 'uploads/developers/' . $filename;
            }
        }

        Developer::create($data);

        return redirect()->route('developers.index')->with('success', 'Developer created!');
    }

    public function show(string $id)
    {
        $developer = Developer::with('user')->findOrFail($id);

        return view('admin.pages.developers.add_developer', compact('developer'));
    }

    public function update(Request $request, string $id)
    {
        $developer = Developer::findOrFail($id);

        $data = $request->validate([
            'add_user_id' => 'required|exists:add_users,id',
            'skill' => 'nullable|string',
            'experience' => 'nullable|string',
            'salary' => 'nullable|numeric',
            'profile_image' => 'nullable|image',
            'cnic_front' => 'nullable|image',
            'cnic_back' => 'nullable|image',
            'contract_file' => 'nullable|file',
            'time_type' => 'nullable|string|in:part_time,full_time',
            'job_type' => 'nullable|string|in:internship,job',
            'salary_type' => 'nullable|string|in:salary_based,project_based',
        ]);

        $developer->fill($data);

        $developer->part_time = $data['time_type'] === 'part_time';
        $developer->full_time = $data['time_type'] === 'full_time';
        $developer->internship = $data['job_type'] === 'internship';
        $developer->job = $data['job_type'] === 'job';

        foreach (['profile_image', 'cnic_front', 'cnic_back', 'contract_file'] as $fileField) {
            if ($request->hasFile($fileField)) {
                if ($developer->$fileField && file_exists(public_path($developer->$fileField))) {
                    unlink(public_path($developer->$fileField));
                }
                $file = $request->file($fileField);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/developers'), $filename);
                $developer->$fileField = 'uploads/developers/' . $filename;
            }
        }

        $developer->save();

        return redirect()->route('developers.index')->with('success', 'Developer updated successfully!');
    }

    public function destroy(string $id)
    {
        $developer = Developer::findOrFail($id);

        foreach (['profile_image', 'cnic_front', 'cnic_back', 'contract_file'] as $fileField) {
            if ($developer->$fileField && file_exists(public_path($developer->$fileField))) {
                unlink(public_path($developer->$fileField));
            }
        }

        $developer->delete();

        return redirect()->route('developers.index')->with('success', 'Developer deleted successfully!');
    }
}
