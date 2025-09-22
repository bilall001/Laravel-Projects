<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanImpersonate
{
    // app/Http/Middleware/CanImpersonate.php
public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if (!$user) {
        // Let 'auth' middleware handle redirects; or explicitly:
        return redirect()->route('login.form');
    }

    if (!in_array($user->role, ['admin','super_admin'])) {
        abort(403, 'You are not allowed to impersonate users.');
    }

    return $next($request);
}

}
