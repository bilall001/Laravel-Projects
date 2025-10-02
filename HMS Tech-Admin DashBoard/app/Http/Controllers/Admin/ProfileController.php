<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('admin.pages.auth.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|email|unique:add_users,email,{$user->id}",
        ]);

        $user->update($request->only('name', 'email'));

        return back()->with('success', '✅ Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', '❌ Current password does not match.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', '✅ Password updated successfully.');
    }
}
