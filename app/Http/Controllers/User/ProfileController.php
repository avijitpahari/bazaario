<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show()
    {
        $user      = Auth::user();
        $addresses = $user->addresses()->latest()->limit(3)->get();

        return view('user.account.profile', compact('user', 'addresses'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('user.account.edit-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'phone'              => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
            'preferred_language' => 'nullable|string|max:10',
        ]);

        $user->update($data);

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $user = Auth::user();

        // Delete old image
        if ($user->profile_image && Storage::exists($user->profile_image)) {
            Storage::delete($user->profile_image);
        }

        $path = $request->file('profile_image')->store('profile-images', 'public');
        $user->update(['profile_image' => $path]);

        return redirect()->route('user.profile')
            ->with('success', 'Profile photo updated.');
    }
}
