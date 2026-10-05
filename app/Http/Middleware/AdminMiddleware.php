<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        if (!$user || $user->role !== 'admin' || $user->status !== 'active') {
            Auth::guard('admin')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access. Administrator privileges required.'], 403);
            }

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => 'Unauthorized access. Administrator privileges required.'
                ]);
        }

        return $next($request);
    }
}