<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('user.account.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'preferred_language' => 'nullable|string|max:10',
            'name'               => 'required|string|max:100',
            'phone'              => ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);

        return redirect()->route('user.settings')
            ->with('success', 'Settings saved successfully.');
    }
}
