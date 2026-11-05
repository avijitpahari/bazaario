<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        /*
        |--------------------------------------------------------------------------
        | Admin Impersonation & Direct Admin View Bypass
        |--------------------------------------------------------------------------
        | If an admin is logged in or actively impersonating a user/seller session,
        | ensure user guard is populated and allow them through without redirect loops.
        */
        $impersonating = session('impersonating');
        if ($impersonating && Auth::guard('admin')->check()) {
            if (!Auth::guard('user')->check() && !empty($impersonating['user_id'])) {
                Auth::guard('user')->loginUsingId($impersonating['user_id']);
            }
            return $next($request);
        }

        if (Auth::guard('admin')->check() && !Auth::guard('user')->check()) {
            $adminUser = Auth::guard('admin')->user();
            Auth::guard('user')->setUser($adminUser);
            return $next($request);
        }



        /*
        |--------------------------------------------------------------------------
        | Check User Authentication
        |--------------------------------------------------------------------------
        */

        if (!Auth::guard('user')->check()) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please login to continue.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Get Authenticated User
        |--------------------------------------------------------------------------
        */

        $user = Auth::guard('user')->user();


        /*
        |--------------------------------------------------------------------------
        | Check Customer Role
        |--------------------------------------------------------------------------
        */

        if ($user->role !== 'user') {

            /*
            |--------------------------------------------------------------------------
            | Seller
            |--------------------------------------------------------------------------
            */

            if ($user->role === 'seller') {

                if (
                    $user->sellerProfile &&
                    $user->sellerProfile->status === 'approved'
                ) {
                    return redirect()
                        ->route('seller.dashboard');
                }

                return redirect()
                    ->route('seller.pending')
                    ->with(
                        'info',
                        'Your seller account is waiting for admin approval.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Admin
            |--------------------------------------------------------------------------
            */

            if ($user->role === 'admin') {

                Auth::guard('user')->logout();

                return redirect()
                    ->route('admin.login')
                    ->withErrors([
                        'email' => 'Please use the Admin Login page.',
                    ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Unknown Role
            |--------------------------------------------------------------------------
            */

            Auth::guard('user')->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Unauthorized access.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Customer — Allow Request
        |--------------------------------------------------------------------------
        */

        return $next($request);
    }
}