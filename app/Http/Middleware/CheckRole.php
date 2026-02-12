<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        // Use the custom hasRole function we added to User.php
        if (!Auth::user()->hasRole($role)) {
            abort(403, "USER DOES NOT HAVE THE RIGHT ROLES.");
        }

        return $next($request);
    }
}