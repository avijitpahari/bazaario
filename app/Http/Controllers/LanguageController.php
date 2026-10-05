<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Switch application language and persist in session, cookie, and database.
     */
    public function switch(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,hi,bn'],
        ]);

        $locale = $validated['locale'];

        // 1. Persist to session
        session(['locale' => $locale]);

        // 2. Set current application locale
        App::setLocale($locale);

        // 3. Persist to user record if authenticated
        $user = Auth::user() ?? Auth::guard('user')->user() ?? Auth::guard('seller')->user() ?? Auth::guard('admin')->user();
        if ($user) {
            $user->update(['preferred_language' => $locale]);
        }

        // 4. Queue long-lived cookie (1 year)
        cookie()->queue(cookie('locale', $locale, 60 * 24 * 365));

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'locale'  => $locale,
                'message' => 'Language updated successfully.',
            ]);
        }

        return back()->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }
}
