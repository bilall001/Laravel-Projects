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
        $users = AddUser::where('role', 'business developer')->get();
        // dd($developers);
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

    // Ensure this user is a business developer
    if ($user->role !== 'business developer') {
        abort(403, 'Unauthorized');
    }

    // Get the Business Developer profile linked to this AddUser
    $bd = BusinessDeveloper::where('add_user_id', $user->id)->firstOrFail();

    // Projects assigned to this Business Developer
    $projects = Project::where('business_developer_id', $bd->id)->get();

    // Attendance records
    $attendance = Attendance::where('user_id', $user->id)
        ->orderBy('date', 'desc')
        ->get();

    // // Salary / payment records
    // $salary = Salary::where('user_id', $user->id)
    //     ->orderBy('salary_date', 'desc')
    //     ->get();

    // Return the dashboard view with all data
    return view('admin.pages.business-developer.dashboard', compact('bd', 'projects', 'attendance'));
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
     * Handle file uploads for image and CNIC
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
