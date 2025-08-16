<?php

namespace App\Http\Controllers\Admin;

use App\Models\AddUser;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $roles = AddUser::select('role')->distinct()->pluck('role')->toArray();
        $selectedRole = $request->input('role');
        $date = $request->input('date', now()->toDateString());

        $users = AddUser::query();
        if ($selectedRole) {
            $users->where('role', $selectedRole);
        }
        $users = $users->get();

        $attendances = Attendance::whereDate('date', $date)->get()->keyBy('user_id');

        $totalUsers = $users->count();
        $totalPresents = $attendances->filter(fn($a) => $a->check_in && !$a->is_leave)->count();
        $totalLeaves = $attendances->filter(fn($a) => $a->is_leave)->count();
        $totalAbsents = $totalUsers - $totalPresents - $totalLeaves;

        return view('admin.pages.attendance.all_attendance', compact(
            'users',
            'roles',
            'selectedRole',
            'date',
            'attendances',
            'totalUsers',
            'totalPresents',
            'totalLeaves',
            'totalAbsents'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
   
   public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:add_users,id',
            'date' => 'required|date',
        ]);

        $attendance = Attendance::firstOrCreate([
            'user_id' => $request->user_id,
            'date' => $request->date,
        ]);

        if ($attendance->is_leave) {
            return redirect()->back()->with('success', 'User is marked on leave!');
        }

        if (!$attendance->check_in) {
            $attendance->check_in = now()->format('H:i:s');
        } elseif (!$attendance->check_out) {
            $attendance->check_out = now()->format('H:i:s');
        }

        $attendance->save();

        return redirect()->back()->with('success', 'Attendance updated!');
    }

    
    public function markLeave(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:add_users,id',
            'date' => 'required|date',
        ]);

        $attendance = Attendance::firstOrCreate([
            'user_id' => $request->user_id,
            'date' => $request->date,
        ]);

        $attendance->is_leave = true;
        $attendance->check_in = null;
        $attendance->check_out = null;
        $attendance->save();

        return redirect()->back()->with('success', 'Leave marked successfully!');
    }
  public function markAbsent(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:add_users,id',
        'date' => 'required|date',
    ]);

    Attendance::create([
        'user_id' => $request->user_id,
        'date' => $request->date,
        'is_absent' => true,
    ]);

    return back()->with('success', 'User marked as Absent.');
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}