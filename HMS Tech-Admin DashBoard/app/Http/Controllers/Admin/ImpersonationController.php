<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function start(Request $request, AddUser $user)
    {
        $admin = Auth::user();

        // Safety: don’t impersonate self or other admins
        if ($user->id === $admin->id) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'You cannot impersonate another admin.');
        }

        // Save original admin ID in session
        $request->session()->put('impersonator_id', $admin->id);

        // Switch to target user
        Auth::loginUsingId($user->id);

        // Use your existing role → dashboard mapping
        return $this->redirectByRole($user->role)
            ->with('success', 'You are now impersonating ' . ($user->name ?? $user->email));
    }

    public function stop(Request $request)
    {
        $impersonatorId = $request->session()->get('impersonator_id');

        if (!$impersonatorId) {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'No impersonation session found.');
        }

        // Log back in as admin
        $admin = AddUser::findOrFail($impersonatorId);
        if (!$admin) {
            Auth::logout();
            return redirect()->route('login.form')->with('error', 'Original admin account not found.');
        }
        Auth::login($admin);
        $request->session()->forget('impersonator_id');
        // Send them to admin dashboard
        return redirect()->route('admin.dashboard')
            ->with('success', 'Returned to your admin session.');
    }

    private function redirectByRole(string $role)
    {
        return match (strtolower($role)) {
            'admin' => redirect()->route('admin.dashboard'),
            'business developer' => redirect()->route('business-developer.dashboard'),
            'client' => redirect()->route('client.dashboard'),
            'partner' => redirect()->route('admin.dashboard'),
            'team manager' => redirect()->route('teamManager.dashboard'),
            'developer' => redirect()->route('developer.dashboard'),
            default => redirect()->route('login.form'),
        };
    }
}
