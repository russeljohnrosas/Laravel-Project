<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show profile page
    public function index()
    {
        $user = User::find(session('user')['id']);

        return view('profile.index', compact('user'));
    }

    // Alias used by older links
    public function show()
    {
        return $this->index();
    }

    // Show edit form
    public function edit()
    {
        $user = User::find(session('user')['id']);

        return view('profile.edit', compact('user'));
    }

    // Update name, email, and optional profile picture (saves to public/uploads/)
    public function update(Request $request)
    {
        $userId = session('user')['id'];

        $request->validate([
            'name'    => 'required|string|min:3|max:100',
            'email'   => 'required|email|unique:users,email,' . $userId,
            'profile' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user        = User::find($userId);
        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->hasFile('profile')) {
            // Remove old file if it exists
            if ($user->profile_pic) {
                $oldPath = public_path('uploads/' . $user->profile_pic);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $file            = $request->file('profile');
            $filename        = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $user->profile_pic = $filename;
        }

        $user->save();

        // Keep session in sync including the new profile_pic
        session(['user' => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'is_admin'    => (bool) $user->is_admin,
            'profile_pic' => $user->profile_pic,
        ]]);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    // Change password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = User::find(session('user')['id']);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('open_password_modal', true);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }

    // Delete account
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'delete_password' => 'required|string',
        ]);

        $user = User::find(session('user')['id']);

        if (!Hash::check($request->delete_password, $user->password)) {
            return back()
                ->withErrors(['delete_password' => 'Password is incorrect.'])
                ->with('open_delete_modal', true);
        }

        // Remove profile picture file if it exists
        if ($user->profile_pic) {
            $path = public_path('uploads/' . $user->profile_pic);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $user->delete();
        session()->forget('user');

        return redirect()->route('login')->with('success', 'Your account has been deleted.');
    }
}
