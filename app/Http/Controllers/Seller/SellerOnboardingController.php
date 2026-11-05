<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SellerProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerOnboardingController extends Controller
{
    /**
     * Render the multi-step onboarding wizard.
     *
     * State Machine:
     * - Unauthenticated or non-seller: Redirect to login.
     * - Approved seller: Redirect to seller.dashboard.
     * - Pending / Unsubmitted seller: Render wizard with prefilled user & profile data.
     */
    public function showWizard(Request $request): View|RedirectResponse
    {
        if (\App\Models\SiteSetting::get('platform_mode', 'multi_vendor') === 'single_vendor') {
            return redirect()->route('home')->with('info', 'Marketplace is currently operating in Single-Vendor mode. Third-party merchant onboarding is temporarily paused.');
        }

        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        // 1. If already approved, redirect to seller dashboard
        if ($profile && $profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        // 2. If explicit redirection to pending gate is requested via query
        if ($request->query('view') === 'pending' && $profile && $profile->status === 'pending') {
            return redirect()->route('seller.pending');
        }

        // 3. Render wizard view, passing authenticated user and existing profile (if any)
        return view('seller.onboarding.wizard', compact('user', 'profile'));
    }

    /**
     * Handle complete onboarding form submission.
     *
     * Processes seller_type, shop_name, bio, storefront image, address,
     * city, state, postal code, and latitude/longitude coordinates.
     */
    public function submitWizard(Request $request): RedirectResponse
    {
        if (\App\Models\SiteSetting::get('platform_mode', 'multi_vendor') === 'single_vendor') {
            return redirect()->route('home')->with('info', 'Marketplace is currently operating in Single-Vendor mode. Third-party merchant onboarding is temporarily paused.');
        }

        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        // Map lowercase / slugified seller_type to standard display titles
        $sellerTypeInput = trim((string) $request->input('seller_type'));
        $typeMapping = [
            'farmer'               => 'Farmer',
            'farmer / producer'    => 'Farmer',
            'producer'             => 'Farmer',
            'kirana'               => 'Kirana Store',
            'kirana store'         => 'Kirana Store',
            'kirana / retail'      => 'Kirana Store',
            'darkstore'            => 'Dark Store',
            'dark store'           => 'Dark Store',
            'individual'           => 'Individual',
            'individual / artisan' => 'Individual',
            'artisan'              => 'Individual',
            'Farmer'               => 'Farmer',
            'Kirana Store'         => 'Kirana Store',
            'Dark Store'           => 'Dark Store',
            'Individual'           => 'Individual',
        ];
        $normalizedTypeKey = strtolower($sellerTypeInput);
        if (isset($typeMapping[$sellerTypeInput])) {
            $request->merge(['seller_type' => $typeMapping[$sellerTypeInput]]);
        } elseif (isset($typeMapping[$normalizedTypeKey])) {
            $request->merge(['seller_type' => $typeMapping[$normalizedTypeKey]]);
        }

        // Sanitize Latitude and Longitude if submitted with degree symbols or cardinal directions
        if ($request->filled('latitude')) {
            $rawLat = trim((string) $request->input('latitude'));
            $isSouth = (bool) preg_match('/[sS]\b|[sS]$/', $rawLat);
            $cleanLat = preg_replace('/[^0-9.\-+]/', '', $rawLat);
            if (is_numeric($cleanLat)) {
                $val = (float) $cleanLat;
                if ($isSouth && $val > 0) {
                    $val = -$val;
                }
                $request->merge(['latitude' => $val]);
            }
        }

        if ($request->filled('longitude')) {
            $rawLng = trim((string) $request->input('longitude'));
            $isWest = (bool) preg_match('/[wW]\b|[wW]$/', $rawLng);
            $cleanLng = preg_replace('/[^0-9.\-+]/', '', $rawLng);
            if (is_numeric($cleanLng)) {
                $val = (float) $cleanLng;
                if ($isWest && $val > 0) {
                    $val = -$val;
                }
                $request->merge(['longitude' => $val]);
            }
        }

        // Clean and sanitize tax, bank, and identification inputs before validation
        if ($request->has('gstin')) {
            $rawGstin = (string) $request->input('gstin');
            $cleanGstin = preg_replace('/^(gstin|gst)[\s:\-#]*/i', '', trim($rawGstin));
            if (preg_match('/([0-9]{2})[\s\-]*([A-Za-z]{5})[\s\-]*([0-9]{4})[\s\-]*([A-Za-z]{1})[\s\-]*([0-9A-Za-z]{1})[\s\-]*([A-Za-z0-9]{1})[\s\-]*([A-Za-z0-9]{1})/i', $cleanGstin, $m)) {
                $cleanGstin = strtoupper($m[1] . $m[2] . $m[3] . $m[4] . $m[5] . $m[6] . $m[7]);
            } else {
                $cleanGstin = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanGstin));
            }
            $request->merge(['gstin' => $cleanGstin !== '' ? substr($cleanGstin, 0, 20) : null]);
        }

        if ($request->has('pan_number')) {
            $rawPan = (string) $request->input('pan_number');
            $cleanPan = preg_replace('/^(pan|pan\s*no|pan\s*number)[\s:\-#]*/i', '', trim($rawPan));
            if (preg_match('/([A-Za-z]{5})[\s\-]*([0-9]{4})[\s\-]*([A-Za-z]{1})/i', $cleanPan, $m)) {
                $cleanPan = strtoupper($m[1] . $m[2] . $m[3]);
            } else {
                $cleanPan = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanPan));
            }
            $request->merge(['pan_number' => $cleanPan !== '' ? substr($cleanPan, 0, 20) : null]);
        }

        if ($request->has('bank_ifsc')) {
            $rawIfsc = (string) $request->input('bank_ifsc');
            if (preg_match('/([A-Za-z]{4})[\s\-]*0[\s\-]*([A-Za-z0-9]{6})/i', $rawIfsc, $matches)) {
                $cleanIfsc = strtoupper($matches[1] . '0' . $matches[2]);
            } else {
                $cleanIfsc = preg_replace('/^(ifsc|ifsc\s*code)[\s:\-#]*/i', '', trim($rawIfsc));
                $cleanIfsc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanIfsc));
            }
            $request->merge(['bank_ifsc' => $cleanIfsc !== '' ? substr($cleanIfsc, 0, 20) : null]);
        }

        if ($request->has('bank_account_number')) {
            $rawBank = (string) $request->input('bank_account_number');
            $cleanBank = preg_replace('/^(a\/c|ac|account|acc\s*no)[\s:\-#]*/i', '', trim($rawBank));
            $cleanBank = preg_replace('/[^0-9A-Za-z]/', '', $cleanBank);
            $request->merge(['bank_account_number' => $cleanBank !== '' ? substr($cleanBank, 0, 50) : null]);
        }

        if ($request->has('fssai_number')) {
            $rawFssai = (string) $request->input('fssai_number');
            $cleanFssai = preg_replace('/^(fssai|fssai\s*no|license|licence)[\s:\-#]*/i', '', trim($rawFssai));
            $cleanFssai = preg_replace('/[^0-9A-Za-z]/', '', $cleanFssai);
            $request->merge(['fssai_number' => $cleanFssai !== '' ? substr($cleanFssai, 0, 30) : null]);
        }

        if ($request->has('trade_license_number')) {
            $cleanTrade = trim((string) $request->input('trade_license_number'));
            $request->merge(['trade_license_number' => $cleanTrade !== '' ? substr($cleanTrade, 0, 50) : null]);
        }

        if ($request->has('postal_code')) {
            $cleanPostal = trim((string) $request->input('postal_code'));
            $request->merge(['postal_code' => $cleanPostal !== '' ? substr($cleanPostal, 0, 20) : null]);
        }

        // 1. Strict Server-Side Form Validation
        $validated = $request->validate([
            'seller_type'      => ['required', 'string', Rule::in(['Farmer', 'Kirana Store', 'Dark Store', 'Individual'])],
            'shop_name'        => ['required', 'string', 'min:3', 'max:150'],
            'bio'              => ['nullable', 'string', 'max:1000'],
            'storefront_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'address'              => ['required', 'string', 'max:255'],
            'city'                 => ['required', 'string', 'max:100'],
            'state'                => ['required', 'string', 'max:100'],
            'country'              => ['nullable', 'string', 'max:100'],
            'postal_code'          => ['nullable', 'string', 'max:20'],
            'latitude'             => ['required', 'numeric', 'between:-90,90'],
            'longitude'            => ['required', 'numeric', 'between:-180,180'],
            'gstin'                => ['nullable', 'string', 'max:20'],
            'pan_number'           => ['nullable', 'string', 'max:20'],
            'trade_license_number' => ['nullable', 'string', 'max:50'],
            'bank_account_number'  => ['nullable', 'string', 'max:50'],
            'bank_ifsc'            => ['nullable', 'string', 'max:20'],
            'fssai_number'         => ['nullable', 'string', 'max:30'],
            'gstin_doc'            => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'pan_doc'              => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'trade_license_doc'    => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'bank_doc'             => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'fssai_doc'            => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
        ], [
            'seller_type.required'     => 'Please choose a seller classification (Farmer, Kirana, Dark Store, or Individual).',
            'seller_type.in'           => 'The selected seller classification is invalid.',
            'shop_name.required'       => 'Your shop or farm name is required.',
            'shop_name.min'            => 'Shop or farm name must be at least 3 characters.',
            'storefront_image.image'   => 'The storefront photo must be an image file (JPEG, PNG, JPG, or WEBP).',
            'storefront_image.max'     => 'Storefront image size must not exceed 5MB.',
            'address.required'         => 'Operating street address or farm plot is required.',
            'city.required'            => 'City or town is required.',
            'state.required'           => 'State or province is required.',
            'latitude.required'        => 'Latitude coordinate is required for hyperlocal discovery.',
            'latitude.between'         => 'Latitude must be between -90 and 90 degrees.',
            'longitude.required'       => 'Longitude coordinate is required for hyperlocal discovery.',
            'longitude.between'        => 'Longitude must be between -180 and 180 degrees.',
            'gstin.max'                => 'The GSTIN must not be greater than 20 characters (standard Indian GSTIN is 15 characters).',
            'bank_ifsc.max'            => 'The bank IFSC code must not be greater than 20 characters (standard IFSC is 11 characters).',
            'pan_number.max'           => 'The PAN number must not be greater than 20 characters.',
            'bank_account_number.max'  => 'The bank account number must not be greater than 50 characters.',
        ]);

        // 2. Handle Storefront Image Upload to Public Disk
        $imagePath = null;
        if ($request->hasFile('storefront_image')) {
            $file = $request->file('storefront_image');
            $filename = 'storefront_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('storefronts', $filename, 'public');
        }

        // Handle Compliance Document Uploads
        $gstinDocPath = null;
        if ($request->hasFile('gstin_doc')) {
            $gstinDocPath = $request->file('gstin_doc')->storeAs('seller_documents/' . $user->id, 'gstin_' . $user->id . '_' . time() . '.' . $request->file('gstin_doc')->getClientOriginalExtension(), 'public');
        }

        $panDocPath = null;
        if ($request->hasFile('pan_doc')) {
            $panDocPath = $request->file('pan_doc')->storeAs('seller_documents/' . $user->id, 'pan_' . $user->id . '_' . time() . '.' . $request->file('pan_doc')->getClientOriginalExtension(), 'public');
        }

        $tradeLicenseDocPath = null;
        if ($request->hasFile('trade_license_doc')) {
            $tradeLicenseDocPath = $request->file('trade_license_doc')->storeAs('seller_documents/' . $user->id, 'trade_license_' . $user->id . '_' . time() . '.' . $request->file('trade_license_doc')->getClientOriginalExtension(), 'public');
        }

        $bankDocPath = null;
        if ($request->hasFile('bank_doc')) {
            $bankDocPath = $request->file('bank_doc')->storeAs('seller_documents/' . $user->id, 'bank_' . $user->id . '_' . time() . '.' . $request->file('bank_doc')->getClientOriginalExtension(), 'public');
        }

        $fssaiDocPath = null;
        if ($request->hasFile('fssai_doc')) {
            $fssaiDocPath = $request->file('fssai_doc')->storeAs('seller_documents/' . $user->id, 'fssai_' . $user->id . '_' . time() . '.' . $request->file('fssai_doc')->getClientOriginalExtension(), 'public');
        }

        // 3. Generate Deterministic Unique Shop Slug
        $baseSlug = Str::slug($validated['shop_name']);
        if (empty($baseSlug)) {
            $baseSlug = 'shop-' . $user->id;
        }
        $slug = $baseSlug;
        $counter = 1;
        while (SellerProfile::where('shop_slug', $slug)->where('user_id', '!=', $user->id)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        // 4. Atomic Database Mutation
        $profile = DB::transaction(function () use ($user, $validated, $slug, $imagePath, $gstinDocPath, $panDocPath, $tradeLicenseDocPath, $bankDocPath, $fssaiDocPath) {
            $attributes = [
                'shop_name'            => $validated['shop_name'],
                'shop_slug'            => $slug,
                'seller_type'          => $validated['seller_type'],
                'bio'                  => $validated['bio'] ?? null,
                'city'                 => $validated['city'],
                'state'                => $validated['state'],
                'country'              => $validated['country'] ?? 'India',
                'latitude'             => (float) $validated['latitude'],
                'longitude'            => (float) $validated['longitude'],
                'gstin'                => $validated['gstin'] ?? null,
                'pan_number'           => $validated['pan_number'] ?? null,
                'trade_license_number' => $validated['trade_license_number'] ?? null,
                'bank_account_number'  => $validated['bank_account_number'] ?? null,
                'bank_ifsc'            => $validated['bank_ifsc'] ?? null,
                'fssai_number'         => $validated['fssai_number'] ?? null,
                'status'               => 'pending',
            ];

            if ($gstinDocPath) {
                $attributes['gstin_doc_path'] = $gstinDocPath;
            }
            if ($panDocPath) {
                $attributes['pan_doc_path'] = $panDocPath;
            }
            if ($tradeLicenseDocPath) {
                $attributes['trade_license_doc_path'] = $tradeLicenseDocPath;
            }
            if ($bankDocPath) {
                $attributes['bank_doc_path'] = $bankDocPath;
            }
            if ($fssaiDocPath) {
                $attributes['fssai_doc_path'] = $fssaiDocPath;
            }

            // Only attach columns if they exist in schema or model fillables
            if (Schema::hasColumn('seller_profiles', 'address')) {
                $attributes['address'] = $validated['address'];
            }
            if (Schema::hasColumn('seller_profiles', 'postal_code') && !empty($validated['postal_code'])) {
                $attributes['postal_code'] = $validated['postal_code'];
            }

            if ($imagePath) {
                $attributes['logo_path'] = $imagePath;
                $attributes['banner_path'] = $imagePath;
            }

            return SellerProfile::updateOrCreate(
                ['user_id' => $user->id],
                $attributes
            );
        });

        // 5. Activity Logging
        Log::info('Seller submitted onboarding wizard dossier', [
            'user_id'      => $user->id,
            'email'        => $user->email,
            'seller_type'  => $profile->seller_type,
            'shop_name'    => $profile->shop_name,
            'shop_slug'    => $profile->shop_slug,
            'city'         => $profile->city,
            'coordinates'  => [$profile->latitude, $profile->longitude],
            'submitted_at' => now()->toIso8601String(),
        ]);

        // 6. Redirect to Pending Approval Gate
        return redirect()->route('seller.pending')
            ->with('success', 'Your seller registration details have been submitted successfully. Our team will verify your account shortly.');
    }

    /**
     * Render the pending approval review dossier page.
     */
    public function pending(Request $request): View|RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;

        // If approved, redirect immediately to dashboard
        if ($profile && $profile->status === 'approved') {
            return redirect()->route('seller.dashboard');
        }

        return view('seller.pending', compact('user', 'profile'));
    }

    /**
     * Handle document upload from the seller pending status page.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        $profile = $user->sellerProfile;
        if (!$profile) {
            return redirect()->route('seller.onboarding')->with('error', 'Please complete basic onboarding first.');
        }

        // Clean and sanitize tax, bank, and identification inputs before validation
        if ($request->has('gstin')) {
            $rawGstin = (string) $request->input('gstin');
            $cleanGstin = preg_replace('/^(gstin|gst)[\s:\-#]*/i', '', trim($rawGstin));
            if (preg_match('/([0-9]{2})[\s\-]*([A-Za-z]{5})[\s\-]*([0-9]{4})[\s\-]*([A-Za-z]{1})[\s\-]*([0-9A-Za-z]{1})[\s\-]*([A-Za-z0-9]{1})[\s\-]*([A-Za-z0-9]{1})/i', $cleanGstin, $m)) {
                $cleanGstin = strtoupper($m[1] . $m[2] . $m[3] . $m[4] . $m[5] . $m[6] . $m[7]);
            } else {
                $cleanGstin = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanGstin));
            }
            $request->merge(['gstin' => $cleanGstin !== '' ? substr($cleanGstin, 0, 20) : null]);
        }

        if ($request->has('pan_number')) {
            $rawPan = (string) $request->input('pan_number');
            $cleanPan = preg_replace('/^(pan|pan\s*no|pan\s*number)[\s:\-#]*/i', '', trim($rawPan));
            if (preg_match('/([A-Za-z]{5})[\s\-]*([0-9]{4})[\s\-]*([A-Za-z]{1})/i', $cleanPan, $m)) {
                $cleanPan = strtoupper($m[1] . $m[2] . $m[3]);
            } else {
                $cleanPan = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanPan));
            }
            $request->merge(['pan_number' => $cleanPan !== '' ? substr($cleanPan, 0, 20) : null]);
        }

        if ($request->has('bank_ifsc')) {
            $rawIfsc = (string) $request->input('bank_ifsc');
            if (preg_match('/([A-Za-z]{4})[\s\-]*0[\s\-]*([A-Za-z0-9]{6})/i', $rawIfsc, $matches)) {
                $cleanIfsc = strtoupper($matches[1] . '0' . $matches[2]);
            } else {
                $cleanIfsc = preg_replace('/^(ifsc|ifsc\s*code)[\s:\-#]*/i', '', trim($rawIfsc));
                $cleanIfsc = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cleanIfsc));
            }
            $request->merge(['bank_ifsc' => $cleanIfsc !== '' ? substr($cleanIfsc, 0, 20) : null]);
        }

        if ($request->has('bank_account_number')) {
            $rawBank = (string) $request->input('bank_account_number');
            $cleanBank = preg_replace('/^(a\/c|ac|account|acc\s*no)[\s:\-#]*/i', '', trim($rawBank));
            $cleanBank = preg_replace('/[^0-9A-Za-z]/', '', $cleanBank);
            $request->merge(['bank_account_number' => $cleanBank !== '' ? substr($cleanBank, 0, 50) : null]);
        }

        if ($request->has('fssai_number')) {
            $rawFssai = (string) $request->input('fssai_number');
            $cleanFssai = preg_replace('/^(fssai|fssai\s*no|license|licence)[\s:\-#]*/i', '', trim($rawFssai));
            $cleanFssai = preg_replace('/[^0-9A-Za-z]/', '', $cleanFssai);
            $request->merge(['fssai_number' => $cleanFssai !== '' ? substr($cleanFssai, 0, 30) : null]);
        }

        if ($request->has('trade_license_number')) {
            $cleanTrade = trim((string) $request->input('trade_license_number'));
            $request->merge(['trade_license_number' => $cleanTrade !== '' ? substr($cleanTrade, 0, 50) : null]);
        }

        $validated = $request->validate([
            'doc_type'             => ['required', 'string', Rule::in(['gstin', 'trade_license', 'bank', 'fssai', 'pan'])],
            'document_file'        => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'], // Max 5MB
            'gstin'                => ['nullable', 'string', 'max:20'],
            'pan_number'           => ['nullable', 'string', 'max:20'],
            'trade_license_number' => ['nullable', 'string', 'max:50'],
            'bank_account_number'  => ['nullable', 'string', 'max:50'],
            'bank_ifsc'            => ['nullable', 'string', 'max:20'],
            'fssai_number'         => ['nullable', 'string', 'max:30'],
        ], [
            'gstin.max'            => 'The GSTIN must not be greater than 20 characters (standard Indian GSTIN is 15 characters).',
            'bank_ifsc.max'        => 'The bank IFSC code must not be greater than 20 characters (standard IFSC is 11 characters).',
            'pan_number.max'       => 'The PAN number must not be greater than 20 characters.',
            'bank_account_number.max' => 'The bank account number must not be greater than 50 characters.',
        ]);

        $docType = $validated['doc_type'];
        $filePath = null;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $filename = $docType . '_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('seller_documents/' . $user->id, $filename, 'public');
        }

        $docStatuses = $profile->document_statuses ?? [];

        switch ($docType) {
            case 'gstin':
                if (!empty($validated['gstin'])) {
                    $profile->gstin = strtoupper(trim($validated['gstin']));
                }
                if (!empty($validated['pan_number'])) {
                    $profile->pan_number = strtoupper(trim($validated['pan_number']));
                }
                if ($filePath) {
                    $profile->gstin_doc_path = $filePath;
                }
                $docStatuses['gstin'] = 'uploaded';
                $msg = 'GSTIN Registration & PAN certificate details uploaded successfully.';
                break;

            case 'pan':
                if (!empty($validated['pan_number'])) {
                    $profile->pan_number = strtoupper(trim($validated['pan_number']));
                }
                if ($filePath) {
                    $profile->pan_doc_path = $filePath;
                }
                $docStatuses['pan'] = 'uploaded';
                $msg = 'Permanent Account Number (PAN) details and document proof uploaded successfully.';
                break;

            case 'trade_license':
                if (!empty($validated['trade_license_number'])) {
                    $profile->trade_license_number = trim($validated['trade_license_number']);
                }
                if ($filePath) {
                    $profile->trade_license_doc_path = $filePath;
                }
                $docStatuses['trade_license'] = 'uploaded';
                $msg = 'Municipality Trade License document uploaded successfully.';
                break;

            case 'bank':
                if (!empty($validated['bank_account_number'])) {
                    $profile->bank_account_number = trim($validated['bank_account_number']);
                }
                if (!empty($validated['bank_ifsc'])) {
                    $profile->bank_ifsc = strtoupper(trim($validated['bank_ifsc']));
                }
                if ($filePath) {
                    $profile->bank_doc_path = $filePath;
                }
                $docStatuses['bank'] = 'uploaded';
                $msg = 'Escrow Bank account & cancelled cheque proof uploaded successfully.';
                break;

            case 'fssai':
                if (!empty($validated['fssai_number'])) {
                    $profile->fssai_number = trim($validated['fssai_number']);
                }
                if ($filePath) {
                    $profile->fssai_doc_path = $filePath;
                }
                $docStatuses['fssai'] = 'uploaded';
                $msg = 'FSSAI / GI Certification details uploaded successfully.';
                break;
        }

        $profile->document_statuses = $docStatuses;
        $profile->save();

        Log::info("Seller uploaded document: {$docType}", [
            'user_id'   => $user->id,
            'doc_type'  => $docType,
            'file_path' => $filePath,
        ]);

        return redirect()->route('seller.pending')->with('success', $msg);
    }
}
