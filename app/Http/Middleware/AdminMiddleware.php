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
            return redirect()->route('admin.login');
        }

        $user = Auth::guard('admin')->user();

        if ($user->role !== 'admin') {

            Auth::guard('admin')->logout();

            return redirect()->route('admin.login')
                ->withErrors([
                    'email' => 'Unauthorized access.'
                ]);
        }

        return $next($request);
    }
}