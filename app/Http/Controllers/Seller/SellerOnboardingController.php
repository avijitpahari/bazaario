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
        $user = Auth::guard('seller')->user() ?? Auth::user();

        if (!$user || $user->role !== 'seller') {
            return redirect()->route('login');
        }

        // Map lowercase / slugified seller_type to standard display titles
        $sellerTypeInput = $request->input('seller_type');
        $typeMapping = [
            'farmer' => 'Farmer',
            'kirana' => 'Kirana Store',
            'kirana store' => 'Kirana Store',
            'darkstore' => 'Dark Store',
            'dark store' => 'Dark Store',
            'individual' => 'Individual',
            'Farmer' => 'Farmer',
            'Kirana Store' => 'Kirana Store',
            'Dark Store' => 'Dark Store',
            'Individual' => 'Individual',
        ];
        if (isset($typeMapping[$sellerTypeInput])) {
            $request->merge(['seller_type' => $typeMapping[$sellerTypeInput]]);
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
            'pan_number'           => ['nullable', 'string', 'max:15'],
            'trade_license_number' => ['nullable', 'string', 'max:50'],
            'bank_account_number'  => ['nullable', 'string', 'max:30'],
            'bank_ifsc'            => ['nullable', 'string', 'max:15'],
            'fssai_number'         => ['nullable', 'string', 'max:30'],
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
        ]);

        // 2. Handle Storefront Image Upload to Public Disk
        $imagePath = null;
        if ($request->hasFile('storefront_image')) {
            $file = $request->file('storefront_image');
            $filename = 'storefront_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('storefronts', $filename, 'public');
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
        $profile = DB::transaction(function () use ($user, $validated, $slug, $imagePath) {
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
            ->with('success', 'Your onboarding dossier has been submitted successfully and is awaiting administrator verification.');
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
}
