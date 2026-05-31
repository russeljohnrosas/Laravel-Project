<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show profile page
    public function index()
    {
        $user = User::find(session('user')['id']);

        return view('profile.index', compact('user'));
    }

    // Show edit form
    public function edit()
    {
        $user = User::find(session('user')['id']);

        return view('profile.edit', compact('user'));
    }

    // Keep old show() so existing links don't break
    public function show()
    {
        return $this->index();
    }

    // Update name, email, and optional picture in one form
    public function update(Request $request)
    {
        $userId = session('user')['id'];

        $request->validate([
            'name'    => 'required|string|min:3|max:100',
            'email'   => 'required|email|unique:users,email,' . $userId,
            'picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        $user = User::find($userId);
        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Handle optional picture upload
        if ($request->hasFile('picture')) {
            // Delete old picture if stored in the new path format
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $ext      = $request->file('picture')->getClientOriginalExtension();
            $filename = 'profiles/user_' . $userId . '_' . time() . '.' . $ext;
            $request->file('picture')->storeAs('', $filename, 'public');

            $data['profile_picture'] = $filename;
        }

        $user->update($data);

        // Keep session in sync
        session(['user' => [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'is_admin' => (bool) $user->is_admin,
        ]]);

        return redirect()->route('profile.index')
            ->with('success', 'Profile updated successfully!');
    }

    // Upload profile picture
    public function uploadPicture(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'picture' => 'required|image|max:2048|mimes:jpeg,png,jpg,gif',
        ]);

        $user = User::find(session('user')['id']);

        // Delete old picture if it exists
        if ($user->profile_picture) {
            $oldPath = 'uploads/profiles/' . $user->profile_picture;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Store the new picture
        $extension = $request->file('picture')->getClientOriginalExtension();
        $filename  = time() . '_' . $user->id . '.' . $extension;
        $request->file('picture')->storeAs('uploads/profiles', $filename, 'public');

        // Save the filename to the database
        $user->update(['profile_picture' => $filename]);

        return back()->with('success', 'Profile picture updated successfully.');
    }

    // Change password
    public function changePassword(Request $request)
    {
        // Validate input
        $request->validate([
            'current_password' => 'required|string',
            'password'         => 'required|string|min:6|confirmed',
        ]);

        $user = User::find(session('user')['id']);

        // Check current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->with('open_password_modal', true);
        }

        // Update the password
        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password changed successfully.');
    }

    // Delete account
    public function deleteAccount(Request $request)
    {
        // Validate input
        $request->validate([
            'delete_password' => 'required|string',
        ]);

        $user = User::find(session('user')['id']);

        // Check password is correct before deleting
        if (!Hash::check($request->delete_password, $user->password)) {
            return back()
                ->withErrors(['delete_password' => 'Password is incorrect.'])
                ->with('open_delete_modal', true);
        }

        // Delete profile picture if exists
        if ($user->profile_picture) {
            $path = 'uploads/profiles/' . $user->profile_picture;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Delete user and clear session
        $user->delete();
        session()->forget('user');

        return redirect()->route('login')->with('success', 'Your account has been deleted.');
    }
}
