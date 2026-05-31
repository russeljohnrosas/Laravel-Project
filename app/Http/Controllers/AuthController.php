<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show registration form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Register user
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name'     => 'required|string|min:3|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        // Hash the password and create user
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Please log in.');
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Login user
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Store user info in session
            session(['user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'is_admin'    => (bool) $user->is_admin,
                'profile_pic' => $user->profile_pic,
            ]]);

            return redirect()->route('dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Invalid credentials
        return back()->withInput(['email' => $request->email])
                     ->with('error', 'Invalid email or password.');
    }

    // Logout user
    public function logout()
    {
        // Destroy session
        session()->forget('user');

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
