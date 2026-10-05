<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request and set the application locale based on session, user preference, or cookie.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = ['en', 'hi', 'bn'];
        $locale = session('locale');

        if (!$locale || !in_array($locale, $supportedLocales)) {
            $user = Auth::user() ?? Auth::guard('user')->user() ?? Auth::guard('seller')->user() ?? Auth::guard('admin')->user();
            if ($user && in_array($user->preferred_language, $supportedLocales)) {
                $locale = $user->preferred_language;
                session(['locale' => $locale]);
            } elseif ($request->hasCookie('locale') && in_array($request->cookie('locale'), $supportedLocales)) {
                $locale = $request->cookie('locale');
                session(['locale' => $locale]);
            } else {
                $locale = config('app.locale', 'en');
            }
        }

        if (in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
