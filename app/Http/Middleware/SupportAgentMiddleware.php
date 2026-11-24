<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SupportAgentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. If admin is logged in, allow admin to access or supervise
        if (Auth::guard('admin')->check()) {
            return $next($request);
        }

        // 2. Check if user guard is authenticated
        if (!Auth::guard('user')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('info', 'Please sign in with your support credentials to access the support desk.');
        }

        $user = Auth::guard('user')->user();

        // 3. Ensure user account is active
        if ($user->status !== 'active') {
            Auth::guard('user')->logout();
            return redirect()->route('login')->withErrors(['email' => 'Your account is currently ' . $user->status . '. Please contact administration.']);
        }

        // 4. Check if user has support_agent role (or support, or admin)
        if ($user->isSupportAgent() || $user->isAdmin()) {
            return $next($request);
        }

        // 5. Otherwise, not authorized for agent panel
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Forbidden. Support specialist role required.'], 403);
        }

        return redirect()->route('user.dashboard')->with('error', 'You do not have permission to access the Support Specialist Workstation.');
    }
}
