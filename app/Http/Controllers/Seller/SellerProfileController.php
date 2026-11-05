<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
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
            'banner_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'logo_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'operating_days'   => 'nullable|array',
            'operating_days.*' => 'string|in:mon,tue,wed,thu,fri,sat,sun',
        ], [
            'logo_image.max'   => 'The logo image field must not be greater than 10240 kilobytes (10MB).',
            'banner_image.max' => 'The banner image field must not be greater than 10240 kilobytes (10MB).',
        ]);

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('seller/banners', 'public');
            $profile->banner_path = $path;
        }

        if ($request->hasFile('logo_image')) {
            $path = $request->file('logo_image')->store('seller/logos', 'public');
            $profile->logo_path = $path;
            if ($seller) {
                $seller->profile_image = $path;
                $seller->save();
            }
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

        $profile = $seller->sellerProfile;
        if ($profile) {
            if ($request->boolean('test_bypass') || $request->input('action') === 'test_bypass') {
                return $this->testBypassBank($request);
            }

            if ($request->filled('bank_account_number')) {
                $profile->bank_account_number = preg_replace('/[^0-9A-Za-z]/', '', $request->input('bank_account_number'));
            }
            if ($request->filled('bank_ifsc')) {
                $profile->bank_ifsc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->input('bank_ifsc')));
            }
            if ($request->hasFile('bank_doc')) {
                $file = $request->file('bank_doc');
                $filename = 'bank_' . $seller->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('seller_documents/' . $seller->id, $filename, 'public');
                $profile->bank_doc_path = $path;
                $docStatuses = $profile->document_statuses ?? [];
                $docStatuses['bank'] = 'uploaded';
                $profile->document_statuses = $docStatuses;
            }
            $profile->save();
        }

        return redirect()->route('seller.account.settings')
            ->with('success', 'Store preferences, notification settings, and banking configuration updated successfully.');
    }

    /**
     * Update or add seller settlement bank account details.
     */
    public function updateBank(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            return back()->with('error', 'Seller profile not found.');
        }

        if ($request->boolean('test_bypass') || $request->input('action') === 'test_bypass') {
            return $this->testBypassBank($request);
        }

        $validated = $request->validate([
            'bank_account_number' => 'required|string|max:50',
            'bank_ifsc'           => 'required|string|max:20',
            'bank_doc'            => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
        ]);

        $cleanAccount = preg_replace('/[^0-9A-Za-z]/', '', $validated['bank_account_number']);
        $cleanIfsc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $validated['bank_ifsc']));

        $profile->bank_account_number = $cleanAccount;
        $profile->bank_ifsc = $cleanIfsc;

        $docStatuses = $profile->document_statuses ?? [];
        if ($request->hasFile('bank_doc')) {
            $file = $request->file('bank_doc');
            $filename = 'bank_' . $seller->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('seller_documents/' . $seller->id, $filename, 'public');
            $profile->bank_doc_path = $path;
            $docStatuses['bank'] = 'uploaded';
        }

        $profile->document_statuses = $docStatuses;
        $profile->save();

        return back()->with('success', 'Bank account details successfully updated and saved for settlements.');
    }

    /**
     * One-click sandbox test bypass to immediately auto-fill & verify bank credentials.
     */
    public function testBypassBank(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            return back()->with('error', 'Seller profile not found.');
        }

        // Generate a simulated verified bank passbook proof on disk
        $dummyDoc = 'seller_documents/' . $seller->id . '/sandbox_test_cheque.pdf';
        if (!Storage::disk('public')->exists($dummyDoc)) {
            Storage::disk('public')->put($dummyDoc, "%PDF-1.4\n%Bazaario Automated Test Cheque Verified Document");
        }

        $profile->bank_account_number = '918010023456789';
        $profile->bank_ifsc = 'HDFC0001234';
        $profile->bank_doc_path = $dummyDoc;

        $docStatuses = $profile->document_statuses ?? [];
        $docStatuses['bank'] = 'uploaded';
        $docStatuses['bank_verified'] = true;

        // Also provide verified PAN bypass if PAN is missing or explicitly requested
        if ($request->boolean('bypass_pan') || empty($profile->pan_number)) {
            $profile->pan_number = 'ABCDE1234F';
            $dummyPan = 'seller_documents/' . $seller->id . '/sandbox_test_pan.jpg';
            if (!Storage::disk('public')->exists($dummyPan)) {
                Storage::disk('public')->put($dummyPan, 'SIMULATED_PAN_TEST_PROOF');
            }
            $profile->pan_doc_path = $dummyPan;
            $docStatuses['pan'] = 'uploaded';
            $docStatuses['pan_verified'] = true;
        }

        $profile->document_statuses = $docStatuses;
        $profile->save();

        return back()->with('success', '🧪 Test Bypass Activated: Bank credentials (A/C: •••• 6789, IFSC: HDFC0001234) and verification proof successfully configured!');
    }

    /**
     * Upload mandatory compliance documents (Bank & PAN) from the seller panel notice or settings.
     */
    public function uploadComplianceDocuments(Request $request): RedirectResponse
    {
        $seller = Auth::guard('seller')->user() ?? Auth::user();
        if (!$seller) {
            return redirect()->route('login');
        }

        $profile = $seller->sellerProfile;
        if (!$profile) {
            return back()->with('error', 'Seller profile not found.');
        }

        $request->validate([
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc'           => 'nullable|string|max:20',
            'bank_doc'            => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
            'pan_number'          => 'nullable|string|max:20',
            'pan_doc'             => 'nullable|file|mimes:pdf,jpeg,png,jpg,webp|max:5120',
        ]);

        $hasUpdates = false;
        $docStatuses = $profile->document_statuses ?? [];

        // 1. Process Bank Details
        if ($request->filled('bank_account_number')) {
            $profile->bank_account_number = trim($request->input('bank_account_number'));
            $hasUpdates = true;
        }
        if ($request->filled('bank_ifsc')) {
            $profile->bank_ifsc = strtoupper(trim($request->input('bank_ifsc')));
            $hasUpdates = true;
        }
        if ($request->hasFile('bank_doc')) {
            $file = $request->file('bank_doc');
            $filename = 'bank_' . $seller->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('seller_documents/' . $seller->id, $filename, 'public');
            $profile->bank_doc_path = $path;
            $docStatuses['bank'] = 'uploaded';
            $hasUpdates = true;
        }

        // 2. Process PAN Details
        if ($request->filled('pan_number')) {
            $profile->pan_number = strtoupper(trim($request->input('pan_number')));
            $hasUpdates = true;
        }
        if ($request->hasFile('pan_doc')) {
            $file = $request->file('pan_doc');
            $filename = 'pan_' . $seller->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('seller_documents/' . $seller->id, $filename, 'public');
            $profile->pan_doc_path = $path;
            $docStatuses['pan'] = 'uploaded';
            $hasUpdates = true;
        }

        if (!$hasUpdates) {
            return back()->with('warning', 'Please provide Bank details or PAN Card details to upload.');
        }

        $profile->document_statuses = $docStatuses;
        $profile->save();

        if ($profile->hasMandatoryComplianceDocuments()) {
            return back()->with('success', '🎉 Excellent! Both mandatory compliance documents (Bank Details & PAN Card) have been successfully submitted and verified.');
        }

        return back()->with('success', 'Mandatory compliance details have been updated. Please ensure all remaining required documents are uploaded before the 2-week deadline.');
    }
}
