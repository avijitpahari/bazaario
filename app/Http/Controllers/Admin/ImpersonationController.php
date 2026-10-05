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

        // Also log them into the target guard so existing middleware passes
        Auth::guard($panel)->loginUsingId($target->id);

        $request->session()->save();

        $redirect = $panel === 'seller'
            ? route('seller.dashboard')
            : route('user.dashboard');

        return redirect($redirect)
            ->with('impersonation_started', "Now viewing as {$target->name} ({$target->role}).");
    }

    /**
     * Stop impersonating and return to admin dashboard.
     * Logs out the impersonated guard but keeps admin guard intact.
     */
    public function stop(Request $request)
    {
        $impersonating = session('impersonating');

        if ($impersonating) {
            $panel = $impersonating['panel'] ?? 'user';

            // Log out the impersonated guard only
            Auth::guard($panel)->logout();
        }

        // Remove impersonation state
        session()->forget('impersonating');

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation ended. You are back as Admin.');
    }
}
