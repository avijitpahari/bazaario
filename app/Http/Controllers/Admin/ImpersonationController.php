<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    /**
     * Start impersonating a user or seller.
     * Stores impersonation data in session — admin guard remains intact.
     */
    public function impersonate(Request $request, int $userId)
    {
        // Must be admin
        if (!Auth::guard('admin')->check()) {
            abort(403, 'Only admins can impersonate.');
        }

        $target = User::findOrFail($userId);

        // Determine which panel to enter based on target's role
        $panel = match ($target->role) {
            'seller' => 'seller',
            default  => 'user',
        };

        // Store impersonation in session — does NOT touch admin guard session key
        session([
            'impersonating' => [
                'user_id'    => $target->id,
                'name'       => $target->name,
                'email'      => $target->email,
                'role'       => $target->role,
                'panel'      => $panel,
                'admin_id'   => Auth::guard('admin')->id(),
                'admin_name' => Auth::guard('admin')->user()->name,
                'started_at' => now()->toIso8601String(),
            ],
        ]);

        // Log target into both user and seller guards so multi-panel navigation works without conflicts
        Auth::guard('user')->loginUsingId($target->id);
        if ($target->role === 'seller' || $target->sellerProfile) {
            Auth::guard('seller')->loginUsingId($target->id);
        }

        $request->session()->save();

        $redirect = $panel === 'seller'
            ? route('seller.dashboard')
            : route('user.dashboard');

        return redirect($redirect)
            ->with('impersonation_started', "Now viewing as {$target->name} ({$target->role}).");
    }

    /**
     * Stop impersonating and return to admin dashboard.
     * Logs out the impersonated guards but keeps admin guard intact.
     */
    public function stop(Request $request)
    {
        // Log out both target guards without clearing admin session
        Auth::guard('user')->logout();
        Auth::guard('seller')->logout();

        // Remove impersonation state
        session()->forget('impersonating');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation ended. You are back as Admin.');
    }
}
