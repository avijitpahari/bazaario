<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SellerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Impersonation & Direct Admin View Bypass
        |--------------------------------------------------------------------------
        | If an admin is logged in or actively impersonating a user/seller session,
        | ensure seller guard is populated and allow them through without redirect loops.
        */
        $impersonating = session('impersonating');
        if ($impersonating && Auth::guard('admin')->check()) {
            if (!Auth::guard('seller')->check() && !empty($impersonating['user_id'])) {
                Auth::guard('seller')->loginUsingId($impersonating['user_id']);
            }
            return $next($request);
        }

        if (Auth::guard('admin')->check() && !Auth::guard('seller')->check()) {
            $adminUser = Auth::guard('admin')->user();
            Auth::guard('seller')->setUser($adminUser);
            return $next($request);
        }



        /*
        |--------------------------------------------------------------------------
        | Check Seller Authentication
        |--------------------------------------------------------------------------
        */
        if (!Auth::guard('seller')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Please login as a seller to continue.',
                ]);
        }

        $user = Auth::guard('seller')->user();

        /*
        |--------------------------------------------------------------------------
        | Check Account Status
        |--------------------------------------------------------------------------
        */
        if ($user->status !== 'active') {
            Auth::guard('seller')->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Your seller account is ' . $user->status . '.',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Your seller account is ' . $user->status . '.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Seller Role
        |--------------------------------------------------------------------------
        */
        if ($user->role !== 'seller') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized access.'], 403);
            }

            if ($user->role === 'user') {
                return redirect()->route('products.index');
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            Auth::guard('seller')->logout();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Unauthorized access.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check Seller Approval Status
        |--------------------------------------------------------------------------
        | Unapproved sellers cannot access /seller/dashboard or operational routes.
        | Exempted routes: seller.pending, seller.onboarding, and logout.
        */
        if (!$this->isExemptFromApprovalCheck($request)) {
            $profile = $user->sellerProfile;

            if (!$profile || $profile->status !== 'approved') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Your seller account is waiting for admin approval.',
                        'status' => $profile ? $profile->status : 'incomplete',
                    ], 403);
                }

                return redirect()
                    ->route('seller.pending')
                    ->with('warning', 'Your seller account is waiting for admin approval.');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the current request is exempt from the approval gate check.
     */
    protected function isExemptFromApprovalCheck(Request $request): bool
    {
        // 1. Pending approval waiting screen & document upload endpoints
        if ($request->is('seller/pending') || $request->is('seller/pending/*') || $request->routeIs('seller.pending*')) {
            return true;
        }

        // 2. Onboarding wizard endpoints
        if (
            $request->is('seller/onboarding') ||
            $request->is('seller/onboarding/*') ||
            $request->routeIs('seller.onboarding*')
        ) {
            return true;
        }

        // 3. Logout endpoints
        if (
            $request->is('logout') ||
            $request->is('seller/logout') ||
            $request->routeIs('logout') ||
            $request->routeIs('seller.logout')
        ) {
            return true;
        }

        return false;
    }
}
