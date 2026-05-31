<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthCustom
{
    // Check if user is logged in
    public function handle(Request $request, Closure $next)
    {
        // If no session, redirect to login
        if (!session('user')) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        return $next($request);
    }
}
