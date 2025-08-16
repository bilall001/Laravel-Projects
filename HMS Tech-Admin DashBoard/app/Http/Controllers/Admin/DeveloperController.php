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
use Illuminate\Support\Facades\Auth;

class DeveloperController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'developer') {
            $developer = Developer::where('add_user_id', $user->id)->first();

            if ($developer) {
                $projects = Project::where('user_id', $user->id)->get();
                $salary = Project::where('user_id', $user->id)->get();
                // $salary = $developer->salaries()->orderBy('salary_date', 'desc')->get();
                $attendance = Attendance::where('user_id', $user->id)->get();
                $teams = Team::whereHas('users', function ($query) use ($user) {
                    $query->where('add_users.id', $user->id);
                })->with('users')->get();
            } else {
                $projects = collect();
                $salary = collect();
                $attendance = collect();
                $teams = collect();
            }

            return view('admin.pages.developers.dashboard', compact(
                'developer',
                'projects',
                'teams',
                'salary',
                'attendance'
            ));
        } else {
            $developers = Developer::orderBy('created_at', 'desc')->get();
            $users = AddUser::where('role', 'developer')->get();

            return view('admin.pages.developers.add_developer', compact('developers', 'users'));
        }
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
            'salary_type' => 'nullable|string|in:salary,project',
        ]);

        // Reset all work type booleans first
        $data['part_time'] = false;
        $data['full_time'] = false;
        $data['internship'] = false;
        $data['job'] = false;

        // Set booleans based on the input radio selections
        if (isset($data['time_type'])) {
            if ($data['time_type'] === 'part_time') {
                $data['part_time'] = true;
            } elseif ($data['time_type'] === 'full_time') {
                $data['full_time'] = true;
            }
        }

        if (isset($data['job_type'])) {
            if ($data['job_type'] === 'internship') {
                $data['internship'] = true;
            } elseif ($data['job_type'] === 'job') {
                $data['job'] = true;
            }
        }

        // Save salary_type as well if you want to store it in DB, else remove this
        $data['salary_type'] = $data['salary_type'] ?? null;

        // File uploads
        foreach (['profile_image', 'cnic_front', 'cnic_back', 'contract_file'] as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);
                $filename = time().'_'.$file->getClientOriginalName();
                $file->move(public_path('uploads/developers'), $filename);
                $data[$fileField] = 'uploads/developers/'.$filename;
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
    ]);

    $developer->add_user_id = $data['add_user_id'];
    $developer->skill = $data['skill'] ?? null;
    $developer->experience = $data['experience'] ?? null;
    $developer->salary = $data['salary'] ?? null;

    // Reset work type booleans
    $developer->part_time = false;
    $developer->full_time = false;
    $developer->internship = false;
    $developer->job = false;

    // Set based on submitted 'time_type' (radio)
    if (!empty($data['time_type'])) {
        if ($data['time_type'] === 'part_time') {
            $developer->part_time = true;
        } elseif ($data['time_type'] === 'full_time') {
            $developer->full_time = true;
        }
    }

    // Set based on submitted 'job_type' (radio)
    if (!empty($data['job_type'])) {
        if ($data['job_type'] === 'internship') {
            $developer->internship = true;
        } elseif ($data['job_type'] === 'job') {
            $developer->job = true;
        }
    }

    // File uploads with old file cleanup
    foreach (['profile_image', 'cnic_front', 'cnic_back', 'contract_file'] as $fileField) {
        if ($request->hasFile($fileField)) {
            if ($developer->$fileField && file_exists(public_path($developer->$fileField))) {
                unlink(public_path($developer->$fileField));
            }
            $file = $request->file($fileField);
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('uploads/developers'), $filename);
            $developer->$fileField = 'uploads/developers/'.$filename;
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
