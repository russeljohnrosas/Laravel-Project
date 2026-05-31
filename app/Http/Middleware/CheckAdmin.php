<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdmin
{
    // Check if the logged-in user is an admin
    public function handle(Request $request, Closure $next)
    {
        // If not admin, redirect to dashboard
        if (!session('user') || !session('user')['is_admin']) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Admins only.');
        }

        return $next($request);
    }
}
