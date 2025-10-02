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

class DeveloperController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'developer') {
            $developer = Developer::where('add_user_id', $user->id)->first();

            if ($developer) {
                $developer = Developer::where('add_user_id', $user->id)->first();
                // $projects = Project::where('user_id', $user->id) // projects directly assigned to developer
                //     ->orWhereHas('team.users', function ($query) use ($user) {
                //         $query->where('add_users.id', $user->id); // projects assigned via team membership
                //     })
                //     ->count();

                // $directProjects = Project::where('user_id', $user->id)->get();
                // $directProjectsCount = $directProjects->count();
                // $teamms = Team::whereHas('users', function ($query) use ($user) {
                //     $query->where('add_users.id', $user->id);
                // })->get();
                // $teamProjects = Project::whereIn('team_id', $teamms->pluck('id'))->get();

                // $teamProjectsCount = $teamProjects->count();
                $directProjects = Project::where('user_id', $user->id)
                    ->with('team', 'businessDeveloper')
                    ->get()
                    ->map(function ($project) {
                        $project->assignment_type = 'Individual';
                        return $project;
                    });
                $directProjectsCount = $directProjects->count();

                // 🔹 Teams the developer belongs to
                $teams = Team::whereHas('users', function ($query) use ($user) {
                    $query->where('add_users.id', $user->id);
                })->with('users')->get();

                // 🔹 Team projects
                $teamProjects = Project::whereIn('team_id', $teams->pluck('id'))
                    ->with('team', 'businessDeveloper')
                    ->get()
                    ->map(function ($project) {
                        $project->assignment_type = 'Team';
                        return $project;
                    });
                $teamProjectsCount = $teamProjects->count();

                // 🔹 Combine projects for table
                $allProjects = $directProjects->concat($teamProjects);
                // dd($allProjects);
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
                })
                    ->with(['users' => function ($query) use ($user) {
                        $query->where('add_users.id', '!=', $user->id); // exclude current developer
                    }])
                    ->get();
                // dd($teams);
                $teamCount = $teams->count();
            } else {
                $projects = collect();
                $salary = collect();
                $attendance = collect();
                $teams = collect();
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
