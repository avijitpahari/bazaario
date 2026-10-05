<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * ImpersonationMiddleware
 *
 * Allows admins to access user/seller panels without disturbing their own
 * admin guard session. When impersonating, the admin is "virtually" signed
 * into the target panel via a session key, not a real guard login.
 */
class ImpersonationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // If an active admin is impersonating someone, let them pass through
        // the user/seller middleware checks by injecting a fake auth check.
        $impersonating = session('impersonating');

        if ($impersonating && Auth::guard('admin')->check()) {
            // Store on request for views to pick up
            $request->attributes->set('impersonating', $impersonating);
        }

        return $next($request);
    }
}
