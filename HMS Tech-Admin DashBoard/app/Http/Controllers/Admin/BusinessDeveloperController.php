<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessDeveloper;
use App\Models\AddUser;
use App\Models\Project;
use App\Models\Attendance;
use App\Models\Salary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessDeveloperController extends Controller
{
    /**
     * List all business developers (Admin view)
     */
    public function index()
    {
        $developers = BusinessDeveloper::with('addUser')->get();
        // ✅ role is "business developer" (with space)
        $users = AddUser::where('role', 'business developer')->get();

        return view('admin.pages.business-developer.busineesdeveloper', compact('developers', 'users'));
    }

    /**
     * Store a new business developer
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $this->handleUploads($request, $data);

        BusinessDeveloper::create($data);

        return redirect()->route('business-developers.index')
            ->with('success', 'Business Developer added successfully.');
    }

    /**
     * Update a business developer
     */
    public function update(Request $request, $id)
    {
        $developer = BusinessDeveloper::findOrFail($id);
        $data = $this->validateData($request, $id);
        $this->handleUploads($request, $data, $developer);

        $developer->update($data);

        return redirect()->route('business-developers.index')
            ->with('success', 'Business Developer updated successfully.');
    }

    /**
     * Delete a business developer
     */
    public function destroy($id)
    {
        $developer = BusinessDeveloper::findOrFail($id);

        foreach (['image', 'cnic_front', 'cnic_back'] as $field) {
            if ($developer->$field) {
                Storage::delete($developer->$field);
            }
        }

        $developer->delete();

        return redirect()->route('business-developers.index')
            ->with('success', 'Business Developer deleted successfully.');
    }

    /**
     * Show the dashboard for logged-in business developer
     */
    public function dashboard()
    {
        $user = auth()->user();
        // ✅ ensure role is "business developer"
        if ($user->role !== 'business developer') {
            abort(403, 'Unauthorized');
        }

        // Get Business Developer profile linked to this AddUser
        $bd = BusinessDeveloper::where('add_user_id', $user->id)->firstOrFail();
        // dd($bd->id);

        // All projects assigned to this Business Developer
        $projects = Project::where('business_developer_id', $user->id)->get();
        // dd($projects);
        // ✅ Stats
        $totalProjects = $projects->count();
        // Count current projects (status = inprogress)
        $currentProjects = Project::whereHas('schedules', function ($query) {
            $query->where('status', 'inProgress');
        })->count();

        // Count completed projects (status = completed)
        $completedProjects = Project::whereHas('schedules', function ($query) {
            $query->where('status', 'completed');
        })->count();
        // Attendance records
        // Get projects assigned to this business developer
        // $projects = Project::where('user_id', $user->id)->orWhere('team_id', $user->team_id)->get();

        // Attendance for this month
        $attendanceQuery = Attendance::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);

        $totalDays = $attendanceQuery->count();
        $presentDays = (clone $attendanceQuery)->where('status', 'present')->count();
        $absentDays = (clone $attendanceQuery)->where('is_absent', 1)->count();
        $leaveDays = (clone $attendanceQuery)->where('is_leave', 1)->count();

        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        $salaries = Salary::where('add_user_id', $user->id)
            ->where('is_paid', 1)
            ->orderByDesc('salary_date')
            ->get();
        return view('admin.pages.business-developer.dashboard', compact(
            'bd',
            'projects',
            'totalProjects',
            'currentProjects',
            'completedProjects',
            'totalDays',
            'presentDays',
            'absentDays',
            'leaveDays',
            'attendancePercentage',
            'salaries'
        ));
    }

    /**
     * Validate request data
     */
    private function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'add_user_id' => 'required|exists:add_users,id',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cnic_front' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'cnic_back' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    /**
     * Handle file uploads
     */
    private function handleUploads(Request $request, array &$data, $developer = null)
    {
        foreach (['image', 'cnic_front', 'cnic_back'] as $field) {
            if ($request->hasFile($field)) {
                if ($developer && $developer->$field) {
                    Storage::delete($developer->$field);
                }
                $data[$field] = $request->file($field)->store('developers');
            }
        }
    }
}
