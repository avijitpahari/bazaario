<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SellerProfileController extends Controller
{
    /**
     * Display the seller's storefront profile workstation.
     */
    public function profile(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            abort(404, 'Seller profile not found.');
        }

        return view('seller.account.profile', compact('seller', 'profile'));
    }

    /**
     * Update the seller's storefront branding, shop identity, and operating schedule.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            abort(404, 'Seller profile not found.');
        }

        if ($request->has('operating_days') && is_string($request->input('operating_days'))) {
            $decoded = json_decode($request->input('operating_days'), true);
            if (is_array($decoded)) {
                $request->merge(['operating_days' => $decoded]);
            }
        }

        $request->validate([
            'shop_name'        => 'required|string|max:150',
            'bio'              => 'nullable|string|max:500',
            'banner_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'logo_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'operating_days'   => 'nullable|array',
            'operating_days.*' => 'string|in:mon,tue,wed,thu,fri,sat,sun',
        ]);

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('seller/banners', 'public');
            $profile->banner_path = $path;
        }

        if ($request->hasFile('logo_image')) {
            $path = $request->file('logo_image')->store('seller/logos', 'public');
            $profile->logo_path = $path;
        }

        $profile->shop_name = $request->input('shop_name');
        $profile->bio = $request->input('bio');

        if ($request->has('operating_days')) {
            $profile->operating_days = $request->input('operating_days');
        }

        $profile->save();

        return redirect()->route('seller.account.profile')
            ->with('success', 'Shop profile and storefront details successfully updated.');
    }

    /**
     * Display the farm location telemetry and geofence settings screen.
     */
    public function location(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            abort(404, 'Seller profile not found.');
        }

        return view('seller.account.location', compact('seller', 'profile'));
    }

    /**
     * Update farm coordinates, address, and operating geofence radius.
     */
    public function updateLocation(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            abort(404, 'Seller profile not found.');
        }

        $request->validate([
            'address'             => 'required|string|max:255',
            'city'                => 'nullable|string|max:100',
            'state'               => 'nullable|string|max:100',
            'postal_code'         => 'nullable|string|max:20',
            'latitude'            => 'required|numeric|between:-90,90',
            'longitude'           => 'required|numeric|between:-180,180',
            'operating_radius_km' => 'required|integer|min:1|max:500',
        ]);

        $profile->address = $request->input('address');
        if ($request->filled('city')) {
            $profile->city = $request->input('city');
        }
        if ($request->filled('state')) {
            $profile->state = $request->input('state');
        }
        if ($request->filled('postal_code')) {
            $profile->postal_code = $request->input('postal_code');
        }

        $profile->latitude = (float) $request->input('latitude');
        $profile->longitude = (float) $request->input('longitude');
        $profile->operating_radius_km = (int) $request->input('operating_radius_km');
        $profile->save();

        return redirect()->route('seller.account.location')
            ->with('success', 'Farm origin location telemetry and geofence coordinates successfully saved.');
    }

    /**
     * Display account security and credentials workstation.
     */
    public function security(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;

        return view('seller.account.security', compact('seller', 'profile'));
    }

    /**
     * Update seller password with current password verification and complexity rules.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        if (!Hash::check($request->input('current_password'), $seller->password)) {
            return back()->withErrors([
                'current_password' => 'The provided current password does not match our records.',
            ]);
        }

        $seller->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return redirect()->route('seller.account.security')
            ->with('success', 'Security credentials and account password successfully updated.');
    }

    /**
     * Display seller notifications and operational alert feed.
     */
    public function notifications(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;

        return view('seller.account.notifications', compact('seller', 'profile'));
    }

    /**
     * Display seller operating preferences and notification settings.
     */
    public function settings(): View|RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;

        return view('seller.account.settings', compact('seller', 'profile'));
    }

    /**
     * Update seller account preferences and operational toggles.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        return redirect()->route('seller.account.settings')
            ->with('success', 'Store preferences and notification settings updated successfully.');
    }
}
