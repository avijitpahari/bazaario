@extends('layouts.seller-onboarding')

@section('title', 'Seller Registration & Onboarding — Bazaario')

@php
    $initialStep = 1;
    if ($errors->hasAny(['seller_type'])) {
        $initialStep = 2;
    } elseif ($errors->hasAny(['shop_name', 'bio', 'storefront_image'])) {
        $initialStep = 3;
    } elseif ($errors->hasAny(['address', 'city', 'state', 'postal_code', 'latitude', 'longitude'])) {
        $initialStep = 4;
    } elseif ($errors->any()) {
        $initialStep = 5;
    }

    $existingType = old('seller_type', $profile?->seller_type ?? 'Farmer');
    $existingName = old('shop_name', $profile?->shop_name ?? ($user->name . '\'s Shop'));
    $existingBio = old('bio', $profile?->bio ?? '');
    $existingAddress = old('address', $profile?->address ?? '');
    $existingCity = old('city', $profile?->city ?? 'Contai');
    $existingState = old('state', $profile?->state ?? 'West Bengal');
    $existingPostal = old('postal_code', $profile?->postal_code ?? '721401');
    $existingLat = old('latitude', $profile?->latitude ?? '21.7781');
    $existingLng = old('longitude', $profile?->longitude ?? '87.7516');
    $existingGstin = old('gstin', $profile?->gstin ?? '');
    $existingPan = old('pan_number', $profile?->pan_number ?? '');
    $existingTrade = old('trade_license_number', $profile?->trade_license_number ?? '');
    $existingFssai = old('fssai_number', $profile?->fssai_number ?? '');
    $existingBank = old('bank_account_number', $profile?->bank_account_number ?? '');
    $existingIfsc = old('bank_ifsc', $profile?->bank_ifsc ?? '');
    $existingAadhaar = old('aadhaar_number', $profile?->aadhaar_number ?? '');
    $existingMandi = old('mandi_license_number', $profile?->mandi_license_number ?? '');
    $existingUdyam = old('udyam_number', $profile?->udyam_number ?? '');

    $existingProvided = array_values(array_unique(array_merge(
        ['pan', 'bank'],
        $profile?->provided_documents ?? [],
        $profile?->gstin || $profile?->gstin_doc_path ? ['gstin'] : [],
        $profile?->trade_license_number || $profile?->trade_license_doc_path ? ['trade_license'] : [],
        $profile?->fssai_number || $profile?->fssai_doc_path ? ['fssai'] : [],
        $profile?->aadhaar_number || $profile?->aadhaar_doc_path ? ['aadhaar'] : [],
        $profile?->mandi_license_number || $profile?->mandi_license_doc_path ? ['mandi_license'] : [],
        $profile?->udyam_number || $profile?->udyam_doc_path ? ['udyam'] : []
    )));
@endphp

@section('content')
<div x-data="sellerWizard({
    initialStep: {{ $initialStep }},
    initialType: '{{ $existingType }}',
    initialShopName: '{{ addslashes($existingName) }}',
    initialBio: '{{ addslashes($existingBio) }}',
    initialAddress: '{{ addslashes($existingAddress) }}',
    initialCity: '{{ addslashes($existingCity) }}',
    initialState: '{{ addslashes($existingState) }}',
    initialPostal: '{{ addslashes($existingPostal) }}',
    initialLat: '{{ $existingLat }}',
    initialLng: '{{ $existingLng }}',
    initialGstin: '{{ addslashes($existingGstin) }}',
    initialPan: '{{ addslashes($existingPan) }}',
    initialTrade: '{{ addslashes($existingTrade) }}',
    initialFssai: '{{ addslashes($existingFssai) }}',
    initialBank: '{{ addslashes($existingBank) }}',
    initialIfsc: '{{ addslashes($existingIfsc) }}',
    initialAadhaar: '{{ addslashes($existingAadhaar) }}',
    initialMandi: '{{ addslashes($existingMandi) }}',
    initialUdyam: '{{ addslashes($existingUdyam) }}',
    initialActiveDocs: @json($existingProvided),
    existingLogo: '{{ $profile?->logo_path ? asset('storage/' . $profile->logo_path) : '' }}'
})" class="w-full">

    <!-- MOBILE TOP PROGRESS BAR -->
    <div class="lg:hidden mb-8 bg-white border border-brand-outline p-4 rounded-[14px] shadow-subtle">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-mono uppercase tracking-wider text-brand-muted" x-text="'STEP 0' + currentStep + ' OF 05'">STEP 01 OF 05</span>
            <span class="text-xs font-semibold font-heading text-brand-slate" x-text="stepTitles[currentStep - 1]">Account Registration</span>
        </div>
        <div class="w-full bg-[#EAE6DC] h-2 rounded-full overflow-hidden">
            <div class="bg-brand-amber h-full transition-all duration-300 rounded-full" :style="'width: ' + (currentStep * 20) + '%;'"></div>
        </div>
        <div class="grid grid-cols-5 gap-1 mt-3">
            <template x-for="step in [1, 2, 3, 4, 5]" :key="step">
                <div class="h-1 rounded transition-all" :class="currentStep >= step ? 'bg-brand-amber' : 'bg-[#EAE6DC]'"></div>
            </template>
        </div>
    </div>

    <!-- TWO-COLUMN ONBOARDING GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
        
        <!-- LEFT SIDEBAR: STEP TIMELINE & CONTEXT -->
        <aside class="hidden lg:block lg:col-span-4 sticky top-28 space-y-6">
            <div class="bg-white border border-brand-outline rounded-[14px] p-6 shadow-subtle">
                <div class="mb-6">
                    <span class="text-xs font-mono text-brand-muted uppercase tracking-wider">Bazaario Seller Hub</span>
                    <h2 class="text-xl font-heading font-bold text-brand-slate mt-1">Partner Onboarding</h2>
                    <p class="text-xs text-brand-muted mt-1 leading-relaxed">
                        Join thousands of farmers, kirana shops, and independent merchants connecting with hyper-local buyers.
                    </p>
                </div>

                <!-- Vertical Timeline Steps -->
                <nav aria-label="Progress" class="space-y-1 relative">
                    <!-- Connecting line -->
                    <div class="absolute left-[19px] top-4 bottom-4 w-0.5 bg-[#EAE6DC] -z-0"></div>

                    <!-- Step 1: Account -->
                    <div class="relative z-10 flex items-start group cursor-pointer" @click="goToStep(1)">
                        <div class="w-10 h-10 rounded-[14px] flex items-center justify-center font-mono text-xs font-bold transition-all shadow-sm"
                             :class="currentStep === 1 ? 'bg-brand-amber text-brand-slate ring-4 ring-amber-100' : (currentStep > 1 ? 'bg-brand-green text-white' : 'bg-[#FAF8F2] border border-brand-outline text-brand-muted')">
                            <span x-show="currentStep > 1" class="material-symbols-outlined text-[18px]">check</span>
                            <span x-show="currentStep <= 1">01</span>
                        </div>
                        <div class="ml-4 flex-1 pt-1">
                            <p class="text-sm font-semibold font-heading" :class="currentStep === 1 ? 'text-brand-slate' : (currentStep > 1 ? 'text-brand-slate' : 'text-brand-muted')">Account</p>
                            <p class="text-xs text-brand-muted">Basic credentials &amp; verification</p>
                        </div>
                    </div>

                    <!-- Step 2: Seller Type -->
                    <div class="relative z-10 flex items-start group cursor-pointer pt-4" @click="goToStep(2)">
                        <div class="w-10 h-10 rounded-[14px] flex items-center justify-center font-mono text-xs font-bold transition-all shadow-sm"
                             :class="currentStep === 2 ? 'bg-brand-amber text-brand-slate ring-4 ring-amber-100' : (currentStep > 2 ? 'bg-brand-green text-white' : 'bg-[#FAF8F2] border border-brand-outline text-brand-muted')">
                            <span x-show="currentStep > 2" class="material-symbols-outlined text-[18px]">check</span>
                            <span x-show="currentStep <= 2">02</span>
                        </div>
                        <div class="ml-4 flex-1 pt-1">
                            <p class="text-sm font-semibold font-heading" :class="currentStep === 2 ? 'text-brand-slate' : (currentStep > 2 ? 'text-brand-slate' : 'text-brand-muted')">Seller Type</p>
                            <p class="text-xs text-brand-muted">Farmer, Kirana, Dark Store</p>
                        </div>
                    </div>

                    <!-- Step 3: Shop/Farm -->
                    <div class="relative z-10 flex items-start group cursor-pointer pt-4" @click="goToStep(3)">
                        <div class="w-10 h-10 rounded-[14px] flex items-center justify-center font-mono text-xs font-bold transition-all shadow-sm"
                             :class="currentStep === 3 ? 'bg-brand-amber text-brand-slate ring-4 ring-amber-100' : (currentStep > 3 ? 'bg-brand-green text-white' : 'bg-[#FAF8F2] border border-brand-outline text-brand-muted')">
                            <span x-show="currentStep > 3" class="material-symbols-outlined text-[18px]">check</span>
                            <span x-show="currentStep <= 3">03</span>
                        </div>
                        <div class="ml-4 flex-1 pt-1">
                            <p class="text-sm font-semibold font-heading" :class="currentStep === 3 ? 'text-brand-slate' : (currentStep > 3 ? 'text-brand-slate' : 'text-brand-muted')">Shop / Farm</p>
                            <p class="text-xs text-brand-muted">Business info &amp; storefront image</p>
                        </div>
                    </div>

                    <!-- Step 4: Location -->
                    <div class="relative z-10 flex items-start group cursor-pointer pt-4" @click="goToStep(4)">
                        <div class="w-10 h-10 rounded-[14px] flex items-center justify-center font-mono text-xs font-bold transition-all shadow-sm"
                             :class="currentStep === 4 ? 'bg-brand-amber text-brand-slate ring-4 ring-amber-100' : (currentStep > 4 ? 'bg-brand-green text-white' : 'bg-[#FAF8F2] border border-brand-outline text-brand-muted')">
                            <span x-show="currentStep > 4" class="material-symbols-outlined text-[18px]">check</span>
                            <span x-show="currentStep <= 4">04</span>
                        </div>
                        <div class="ml-4 flex-1 pt-1">
                            <p class="text-sm font-semibold font-heading" :class="currentStep === 4 ? 'text-brand-slate' : (currentStep > 4 ? 'text-brand-slate' : 'text-brand-muted')">Location</p>
                            <p class="text-xs text-brand-muted">Geo coordinates &amp; radar preview</p>
                        </div>
                    </div>

                    <!-- Step 5: Review -->
                    <div class="relative z-10 flex items-start group cursor-pointer pt-4" @click="goToStep(5)">
                        <div class="w-10 h-10 rounded-[14px] flex items-center justify-center font-mono text-xs font-bold transition-all shadow-sm"
                             :class="currentStep === 5 ? 'bg-brand-amber text-brand-slate ring-4 ring-amber-100' : 'bg-[#FAF8F2] border border-brand-outline text-brand-muted'">
                            05
                        </div>
                        <div class="ml-4 flex-1 pt-1">
                            <p class="text-sm font-semibold font-heading" :class="currentStep === 5 ? 'text-brand-slate' : 'text-brand-muted'">Review</p>
                            <p class="text-xs text-brand-muted">Verify submission packet</p>
                        </div>
                    </div>
                </nav>
            </div>

            <!-- Trust Badges & Guarantee Card -->
            <div class="bg-brand-subtle border border-brand-outline rounded-[14px] p-5">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-[10px] bg-brand-green/10 flex items-center justify-center text-brand-green">
                        <span class="material-symbols-outlined text-[20px]">verified</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold font-heading text-brand-slate">Verified Seller Guarantee</p>
                        <p class="text-[11px] text-brand-muted">Zero commission for first 90 days</p>
                    </div>
                </div>
                <p class="text-[11px] text-brand-muted leading-relaxed">
                    Your details and GPS location are securely encrypted and verified by Bazaario regional desk within 24–48 hours.
                </p>
            </div>
        </aside>

        <!-- RIGHT COLUMN: STEP FORMS -->
        <section class="lg:col-span-8">

            @if ($errors->any())
                <div class="mb-6 p-4 rounded-[14px] bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-600 text-2xl shrink-0 mt-0.5">error</span>
                    <div class="flex-1">
                        <h4 class="font-heading font-bold text-sm text-rose-900">Submission Blocked — Please correct the following:</h4>
                        <ul class="mt-1 list-disc list-inside text-xs font-mono space-y-0.5 text-rose-800">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form id="onboarding-form" method="POST" action="{{ route('seller.onboarding.submit') }}" enctype="multipart/form-data" @submit="isSubmitting = true">
                @csrf
                <input type="hidden" name="seller_type" :value="sellerType">

                <!-- ============================================================== -->
                <!-- STEP 1: REGISTER / CONFIRM ACCOUNT CREDENTIALS -->
                <!-- ============================================================== -->
                <div x-show="currentStep === 1" class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card space-y-6">
                    <div>
                        <span class="inline-block bg-[#F5A623]/15 text-[#B45309] font-mono text-xs font-bold px-2.5 py-1 rounded-[8px] mb-2">STEP 01</span>
                        <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate">Verify Seller Account</h1>
                        <p class="text-brand-muted text-sm mt-2">
                            Confirm your merchant identity credentials. These are linked directly to your authenticated seller profile.
                        </p>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                            Merchant Legal Name
                        </label>
                        <input type="text" value="{{ $user->name }}" readonly class="w-full px-4 py-3.5 bg-brand-subtle border border-brand-outline text-brand-slate text-sm rounded-[14px] cursor-not-allowed">
                        <p class="text-[11px] text-brand-muted mt-1 font-mono">Linked to your Bazaario account: #UID-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>

                    <!-- Email & Phone Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                                Registered Email
                            </label>
                            <input type="email" value="{{ $user->email }}" readonly class="w-full px-4 py-3.5 bg-brand-subtle border border-brand-outline text-brand-slate text-sm rounded-[14px] cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                                Primary Contact Phone
                            </label>
                            <input type="text" value="{{ $user->phone ?? '+91 98765 43210' }}" readonly class="w-full px-4 py-3.5 bg-brand-subtle border border-brand-outline text-brand-slate text-sm rounded-[14px] cursor-not-allowed">
                        </div>
                    </div>

                    <!-- Terms Notice -->
                    <div class="p-4 bg-brand-subtle rounded-[14px] border border-brand-outline flex items-start gap-3">
                        <span class="material-symbols-outlined text-brand-amber text-[20px] shrink-0 mt-0.5">verified_user</span>
                        <p class="text-xs text-brand-muted leading-relaxed">
                            By proceeding, you verify that you represent a legitimate trading stall, agricultural plot, or retail enterprise compliant with APMC and local market standards.
                        </p>
                    </div>

                    <!-- Step 1 Bottom Action -->
                    <div class="pt-4 flex items-center justify-end border-t border-brand-outline/60">
                        <button type="button" @click="goToStep(2)" class="px-8 py-3.5 bg-brand-amber hover:bg-brand-amber-dark text-brand-slate font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2">
                            <span>Continue to Seller Type</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- STEP 2: CHOOSE SELLER TYPE -->
                <!-- ============================================================== -->
                <div x-show="currentStep === 2" class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card space-y-6">
                    <div>
                        <span class="inline-block bg-[#F5A623]/15 text-[#B45309] font-mono text-xs font-bold px-2.5 py-1 rounded-[8px] mb-2">STEP 02</span>
                        <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate">Choose Your Seller Type</h1>
                        <p class="text-brand-muted text-sm mt-2">
                            Select the classification that best fits your business model. This configures your operational units and auction options.
                        </p>
                    </div>

                    @error('seller_type')
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-[10px]">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- 4 Selection Cards Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        
                        <!-- CARD 1: Farmer -->
                        <div @click="setSellerType('Farmer')"
                             class="relative p-6 rounded-[14px] cursor-pointer transition-all hover:shadow-md border-2"
                             :class="sellerType === 'Farmer' ? 'border-brand-amber bg-[#FFFDF7]' : 'border-brand-outline bg-white hover:border-brand-amber/50'">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-[14px] bg-amber-100 flex items-center justify-center text-2xl">
                                    🌾
                                </div>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                     :class="sellerType === 'Farmer' ? 'bg-brand-amber text-brand-slate shadow-sm' : 'border border-brand-outline text-transparent'">
                                    <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                </div>
                            </div>
                            <h3 class="font-heading font-bold text-lg text-brand-slate mt-4">Farmer</h3>
                            <p class="text-xs text-brand-muted mt-2 leading-relaxed">
                                Sell fresh crops and harvests directly to local buyers. Access live wholesale auction lots.
                            </p>
                            <div class="mt-4 pt-3 border-t border-brand-outline/40 flex items-center gap-2">
                                <span class="text-[10px] font-mono uppercase bg-amber-100 text-amber-900 px-2 py-0.5 rounded-[6px]">Direct Harvest</span>
                                <span class="text-[10px] font-mono uppercase bg-green-100 text-green-800 px-2 py-0.5 rounded-[6px]">Live Auctions</span>
                            </div>
                        </div>

                        <!-- CARD 2: Kirana Store -->
                        <div @click="setSellerType('Kirana Store')"
                             class="relative p-6 rounded-[14px] cursor-pointer transition-all hover:shadow-md border-2"
                             :class="sellerType === 'Kirana Store' ? 'border-brand-amber bg-[#FFFDF7]' : 'border-brand-outline bg-white hover:border-brand-amber/50'">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-[14px] bg-slate-100 flex items-center justify-center text-2xl">
                                    🏪
                                </div>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                     :class="sellerType === 'Kirana Store' ? 'bg-brand-amber text-brand-slate shadow-sm' : 'border border-brand-outline text-transparent'">
                                    <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                </div>
                            </div>
                            <h3 class="font-heading font-bold text-lg text-brand-slate mt-4">Kirana Store</h3>
                            <p class="text-xs text-brand-muted mt-2 leading-relaxed">
                                Sell everyday grocery, packaged staples, and FMCG household essentials with rapid local dispatch.
                            </p>
                            <div class="mt-4 pt-3 border-t border-brand-outline/40 flex items-center gap-2">
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">Hyperlocal 30m</span>
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">FMCG Stock</span>
                            </div>
                        </div>

                        <!-- CARD 3: Dark Store -->
                        <div @click="setSellerType('Dark Store')"
                             class="relative p-6 rounded-[14px] cursor-pointer transition-all hover:shadow-md border-2"
                             :class="sellerType === 'Dark Store' ? 'border-brand-amber bg-[#FFFDF7]' : 'border-brand-outline bg-white hover:border-brand-amber/50'">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-[14px] bg-slate-100 flex items-center justify-center text-2xl">
                                    📦
                                </div>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                     :class="sellerType === 'Dark Store' ? 'bg-brand-amber text-brand-slate shadow-sm' : 'border border-brand-outline text-transparent'">
                                    <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                </div>
                            </div>
                            <h3 class="font-heading font-bold text-lg text-brand-slate mt-4">Dark Store</h3>
                            <p class="text-xs text-brand-muted mt-2 leading-relaxed">
                                High-velocity micro-fulfillment center managing high SKU counts and automated courier dispatch.
                            </p>
                            <div class="mt-4 pt-3 border-t border-brand-outline/40 flex items-center gap-2">
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">High Volume</span>
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">Express SLA</span>
                            </div>
                        </div>

                        <!-- CARD 4: Individual -->
                        <div @click="setSellerType('Individual')"
                             class="relative p-6 rounded-[14px] cursor-pointer transition-all hover:shadow-md border-2"
                             :class="sellerType === 'Individual' ? 'border-brand-amber bg-[#FFFDF7]' : 'border-brand-outline bg-white hover:border-brand-amber/50'">
                            <div class="flex items-start justify-between">
                                <div class="w-12 h-12 rounded-[14px] bg-slate-100 flex items-center justify-center text-2xl">
                                    👤
                                </div>
                                <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors"
                                     :class="sellerType === 'Individual' ? 'bg-brand-amber text-brand-slate shadow-sm' : 'border border-brand-outline text-transparent'">
                                    <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                </div>
                            </div>
                            <h3 class="font-heading font-bold text-lg text-brand-slate mt-4">Individual</h3>
                            <p class="text-xs text-brand-muted mt-2 leading-relaxed">
                                Sell homemade foods, handicrafts, handlooms, or personal artisan creations independently.
                            </p>
                            <div class="mt-4 pt-3 border-t border-brand-outline/40 flex items-center gap-2">
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">Home Made</span>
                                <span class="text-[10px] font-mono uppercase bg-slate-100 text-brand-muted px-2 py-0.5 rounded-[6px]">Artisan</span>
                            </div>
                        </div>

                    </div>

                    <!-- Step 2 Bottom Navigation -->
                    <div class="pt-6 flex items-center justify-between border-t border-brand-outline/60">
                        <button type="button" @click="goToStep(1)" class="px-5 py-3 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition">
                            Back
                        </button>
                        <button type="button" @click="goToStep(3)" class="px-8 py-3.5 bg-brand-amber hover:bg-brand-amber-dark text-brand-slate font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2">
                            <span>Continue to Shop Details</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- STEP 3: BUSINESS & FARM DETAILS -->
                <!-- ============================================================== -->
                <div x-show="currentStep === 3" class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="inline-block bg-[#F5A623]/15 text-[#B45309] font-mono text-xs font-bold px-2.5 py-1 rounded-[8px] mb-2">STEP 03</span>
                            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate">Shop &amp; Farm Details</h1>
                        </div>
                        <span class="text-xs font-mono bg-brand-slate text-brand-amber px-3 py-1.5 rounded-[8px] uppercase tracking-wide font-bold" x-text="sellerType.toUpperCase() + ' PROFILE'">
                            FARMER PROFILE
                        </span>
                    </div>
                    <p class="text-brand-muted text-sm">
                        Provide descriptive commercial details for your public storefront and buyer presentation.
                    </p>

                    <!-- Shop Name -->
                    <div>
                        <label for="shop_name" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                            <span x-text="sellerType === 'Farmer' ? 'Farm / Agro Enterprise Name' : (sellerType === 'Kirana Store' ? 'Kirana Store Name' : 'Shop / Brand Name')">Shop Name</span>
                            <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="shop_name" name="shop_name" x-model="shopName" required
                               placeholder="e.g. Patel Organic Greens"
                               class="w-full px-4 py-3.5 bg-brand-subtle border @error('shop_name') border-rose-500 ring-2 ring-rose-200 @else border-brand-outline @enderror text-brand-slate text-sm rounded-[14px] focus:outline-none focus:border-brand-amber transition">
                        @error('shop_name')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Business Description -->
                    <div>
                        <label for="bio" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                            Storefront Bio &amp; Story
                        </label>
                        <textarea id="bio" name="bio" rows="3" x-model="bio"
                                  placeholder="Describe your produce quality, farming methods, or storefront narrative..."
                                  class="w-full px-4 py-3 bg-brand-subtle border border-brand-outline text-brand-slate text-sm rounded-[14px] focus:outline-none focus:border-brand-amber transition"></textarea>
                    </div>

                    <!-- Business Verification, Tax & Compliance Documents Section -->
                    <div class="p-5 sm:p-6 bg-brand-subtle border border-brand-outline rounded-[14px] space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[22px]">folder_shared</span>
                                <div>
                                    <h4 class="text-xs font-bold font-heading text-brand-slate uppercase tracking-wider">Business Verification &amp; Compliance Documents</h4>
                                    <p class="text-[11px] text-brand-muted">Choose which documents you can provide from the dropdown below to verify your merchant store.</p>
                                </div>
                            </div>
                            <span class="text-[11px] font-mono text-emerald-800 font-bold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200 shrink-0 self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span x-text="activeDocs.length + ' Documents Selected'">2 Documents Selected</span>
                            </span>
                        </div>

                        <!-- DOCUMENT CHOOSER DROPDOWN SELECTOR -->
                        <div class="p-4 bg-white border border-brand-outline rounded-[12px] shadow-2xs space-y-2.5">
                            <label class="block text-xs font-heading font-bold text-brand-slate flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-brand-amber text-[18px]">add_circle</span>
                                <span>Choose Documents to Provide:</span>
                            </label>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                                <div class="relative flex-1">
                                    <select x-model="selectedDocToAdd" 
                                            @change="if(selectedDocToAdd) { addDocument(selectedDocToAdd); }" 
                                            class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline text-brand-slate text-xs rounded-[10px] focus:outline-none focus:border-brand-amber font-body cursor-pointer">
                                        <option value="">-- Choose a document from the list to add (+ Select) --</option>
                                        <template x-for="item in availableDocCatalog" :key="item.key">
                                            <option :value="item.key" 
                                                    :disabled="isDocActive(item.key)" 
                                                    x-text="(isDocActive(item.key) ? '✓ ' : '+ ') + item.label + ' (' + item.badge + ')' + (item.mandatory ? ' [Recommended]' : '')">
                                            </option>
                                        </template>
                                    </select>
                                </div>
                                <button type="button" 
                                        @click="addDocument(selectedDocToAdd)" 
                                        :disabled="!selectedDocToAdd || isDocActive(selectedDocToAdd)" 
                                        class="px-4 py-2.5 bg-brand-amber hover:bg-amber-400 disabled:opacity-40 disabled:cursor-not-allowed text-brand-slate font-heading text-xs font-bold rounded-[10px] transition shrink-0 flex items-center justify-center gap-1 cursor-pointer">
                                    <span class="material-symbols-outlined text-[16px]">add</span>
                                    <span>Add Document</span>
                                </button>
                            </div>
                            
                            <!-- Quick-add suggestion chips -->
                            <div class="flex items-center gap-1.5 pt-1 flex-wrap text-[11px] text-slate-500">
                                <span class="font-semibold text-slate-600">Quick suggestions:</span>
                                <template x-for="item in availableDocCatalog" :key="'chip-' + item.key">
                                    <button type="button" 
                                            @click="isDocActive(item.key) ? null : addDocument(item.key)" 
                                            class="px-2 py-0.5 rounded text-[10px] font-mono transition cursor-pointer flex items-center gap-1"
                                            :class="isDocActive(item.key) ? 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300' : 'bg-slate-100 text-slate-700 hover:bg-amber-100 border border-slate-200'">
                                        <span x-show="isDocActive(item.key)">✓</span>
                                        <span x-text="item.shortLabel || item.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- HIDDEN TRACKING INPUTS FOR CHOSEN DOCUMENTS -->
                        <template x-for="docKey in activeDocs" :key="'hidden-prov-' + docKey">
                            <input type="hidden" name="provided_documents[]" :value="docKey">
                        </template>

                        <!-- ACTIVE SELECTED DOCUMENTS CARDS -->
                        <div class="space-y-4 pt-1">
                            
                            <!-- 1. PAN Card Card -->
                            <div x-show="isDocActive('pan')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-600 text-[18px]">badge</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">Permanent Account Number (PAN Card)</h5>
                                            <span class="text-[10px] font-mono font-bold text-amber-800 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200">Direct Tax ID • Recommended</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('pan')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">PAN Number</label>
                                        <input type="text" name="pan_number" x-model="panNumber" @input="panNumber = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" maxlength="20" placeholder="e.g. ABCDE1234F" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] uppercase font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach PAN Card Proof File</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.pan ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="pan_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.pan = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.pan || 'PDF, JPG, PNG (Max 5MB)'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Bank Details Card -->
                            <div x-show="isDocActive('bank')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">account_balance</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">Escrow Bank Account &amp; Cancelled Cheque / Passbook</h5>
                                            <span class="text-[10px] font-mono font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.2 rounded border border-emerald-200">Payouts &amp; Escrow • Required</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('bank')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Bank Account Number</label>
                                        <input type="text" name="bank_account_number" x-model="bankAccount" @input="bankAccount = $event.target.value.replace(/[^0-9A-Za-z]/g, '')" maxlength="50" placeholder="e.g. 918010023456789" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Bank IFSC Code</label>
                                        <input type="text" name="bank_ifsc" x-model="bankIfsc" @input="bankIfsc = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" maxlength="20" placeholder="e.g. HDFC0001234" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] uppercase font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach Cheque / Passbook</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.bank ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="bank_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.bank = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.bank || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. GSTIN Registration Card -->
                            <div x-show="isDocActive('gstin')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-blue-600 text-[18px]">receipt_long</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">GST Registration Certificate (GSTIN)</h5>
                                            <span class="text-[10px] font-mono font-bold text-blue-800 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200">Indirect Tax</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('gstin')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">GSTIN Number</label>
                                        <input type="text" name="gstin" x-model="gstin" @input="gstin = $event.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" maxlength="20" placeholder="e.g. 19AAACG1234A1Z5" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] uppercase font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach GST Certificate File</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.gstin ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="gstin_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.gstin = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.gstin || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. Trade License Card -->
                            <div x-show="isDocActive('trade_license')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-purple-600 text-[18px]">domain</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">Municipality Trade License / Shop &amp; Establishment</h5>
                                            <span class="text-[10px] font-mono font-bold text-purple-800 bg-purple-50 px-1.5 py-0.2 rounded border border-purple-200">Local Municipal Body</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('trade_license')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Trade License Number</label>
                                        <input type="text" name="trade_license_number" x-model="tradeLicense" maxlength="50" placeholder="e.g. TL-MED-2024-8841" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach Trade License Proof</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.trade_license ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="trade_license_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.trade_license = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.trade_license || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 5. FSSAI Food Safety Card -->
                            <div x-show="isDocActive('fssai')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-green-600 text-[18px]">restaurant</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">FSSAI Food Safety &amp; Standards License / Registration</h5>
                                            <span class="text-[10px] font-mono font-bold text-green-800 bg-green-50 px-1.5 py-0.2 rounded border border-green-200">Food, Grains &amp; Agro</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('fssai')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">FSSAI License / Registration No</label>
                                        <input type="text" name="fssai_number" x-model="fssaiNumber" maxlength="30" placeholder="e.g. 10020031000123" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach FSSAI Certificate File</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.fssai ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="fssai_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.fssai = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.fssai || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 6. Aadhaar / Gov Photo ID Card -->
                            <div x-show="isDocActive('aadhaar')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-indigo-600 text-[18px]">fingerprint</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">Aadhaar Card / Government Photo ID Proof</h5>
                                            <span class="text-[10px] font-mono font-bold text-indigo-800 bg-indigo-50 px-1.5 py-0.2 rounded border border-indigo-200">Proprietor Identity</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('aadhaar')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Aadhaar / Photo ID Number</label>
                                        <input type="text" name="aadhaar_number" x-model="aadhaarNumber" maxlength="50" placeholder="e.g. 9876 5432 1098 or Voter ID" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach Photo ID Proof File</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.aadhaar ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="aadhaar_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.aadhaar = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.aadhaar || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 7. APMC / Mandi License Card -->
                            <div x-show="isDocActive('mandi_license')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-amber-600 text-[18px]">agriculture</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">APMC / Mandi Market Trading License</h5>
                                            <span class="text-[10px] font-mono font-bold text-amber-900 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-300">Wholesale &amp; Live Auctions</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('mandi_license')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">APMC License Number</label>
                                        <input type="text" name="mandi_license_number" x-model="mandiLicense" maxlength="50" placeholder="e.g. APMC-WB-2024-1049" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach APMC Permit Document</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.mandi_license ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="mandi_license_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.mandi_license = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.mandi_license || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 8. Udyam / MSME Certificate Card -->
                            <div x-show="isDocActive('udyam')" x-transition class="p-4 bg-white border border-brand-outline rounded-[12px] space-y-3 shadow-2xs hover:border-brand-amber/50 transition">
                                <div class="flex items-center justify-between border-b border-brand-outline/60 pb-2.5">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-slate-600 text-[18px]">workspace_premium</span>
                                        <div>
                                            <h5 class="text-xs font-bold font-heading text-brand-slate">Udyam / MSME Registration Certificate</h5>
                                            <span class="text-[10px] font-mono font-bold text-slate-800 bg-slate-100 px-1.5 py-0.2 rounded border border-slate-300">Small Enterprise Registration</span>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeDocument('udyam')" class="text-slate-400 hover:text-rose-600 text-[11px] font-mono transition cursor-pointer flex items-center gap-0.5" title="Remove from list">
                                        <span class="material-symbols-outlined text-[14px]">close</span>
                                        <span>Remove</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Udyam Registration Number</label>
                                        <input type="text" name="udyam_number" x-model="udyamNumber" maxlength="50" placeholder="e.g. UDYAM-WB-00-1234567" class="w-full px-3.5 py-2.5 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs">
                                    </div>
                                    <div>
                                        <label class="block font-medium text-brand-slate mb-1">Attach Udyam Certificate File</label>
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 bg-brand-subtle hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold rounded-lg border border-brand-outline transition shrink-0">
                                                <span class="material-symbols-outlined text-[16px] text-brand-amber">upload_file</span>
                                                <span x-text="docFiles.udyam ? 'Replace File' : 'Choose File'">Choose File</span>
                                                <input type="file" name="udyam_doc" accept=".pdf,.png,.jpg,.jpeg,.webp" class="hidden" @change="docFiles.udyam = $event.target.files[0] ? $event.target.files[0].name : ''">
                                            </label>
                                            <span class="text-[11px] font-mono text-slate-500 truncate" x-text="docFiles.udyam || 'PDF, JPG, PNG'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Storefront Image Drag-and-Drop Dropzone -->
                    <div>
                        <label class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                            Storefront / Farm Gate Image
                        </label>
                        <div class="border-2 border-dashed border-brand-outline hover:border-brand-amber bg-brand-subtle rounded-[14px] p-6 text-center transition">
                            <input type="file" id="storefront_image" name="storefront_image" accept="image/*" class="hidden" @change="handleImageUpload($event)">
                            
                            <!-- Empty State -->
                            <div x-show="!imagePreview" class="space-y-3">
                                <div class="w-12 h-12 mx-auto rounded-full bg-amber-100/60 text-brand-amber flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[26px]">add_photo_alternate</span>
                                </div>
                                <div>
                                    <button type="button" @click="document.getElementById('storefront_image').click()" class="text-sm font-semibold font-heading text-brand-slate hover:text-amber-600 transition-colors">
                                        Browse Files
                                    </button>
                                    <span class="text-xs text-brand-muted"> or select storefront image</span>
                                </div>
                                <p class="text-[11px] text-brand-muted font-mono">PNG, JPG or WEBP up to 5MB (1600x900 recommended)</p>
                            </div>

                            <!-- Preview State -->
                            <div x-show="imagePreview" class="flex flex-col sm:flex-row items-center gap-4 text-left" style="display: none;">
                                <div class="relative w-36 h-24 rounded-[10px] overflow-hidden bg-slate-200 border border-brand-outline shrink-0">
                                    <img :src="imagePreview" alt="Storefront Preview" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 right-1 bg-black/70 text-white font-mono text-[9px] px-1.5 py-0.5 rounded" x-text="imageSize"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold font-heading text-brand-slate truncate" x-text="imageName"></p>
                                    <p class="text-[11px] text-brand-green font-mono flex items-center gap-1 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-green"></span> Ready for upload
                                    </p>
                                    <div class="flex items-center gap-2 mt-3">
                                        <button type="button" @click="document.getElementById('storefront_image').click()" class="px-2.5 py-1 text-[11px] font-mono bg-white border border-brand-outline hover:bg-slate-50 text-brand-slate rounded-[6px]">
                                            Replace
                                        </button>
                                        <button type="button" @click="removeImage()" class="px-2.5 py-1 text-[11px] font-mono bg-red-50 border border-red-200 text-red-700 hover:bg-red-100 rounded-[6px]">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Bottom Navigation -->
                    <div class="pt-6 flex items-center justify-between border-t border-brand-outline/60">
                        <button type="button" @click="goToStep(2)" class="px-5 py-3 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition">
                            Back
                        </button>
                        <button type="button" @click="goToStep(4)" class="px-8 py-3.5 bg-brand-amber hover:bg-brand-amber-dark text-brand-slate font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2">
                            <span>Continue to Location</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- STEP 4: LOCATION & GEOLOCATION CAPTURE -->
                <!-- ============================================================== -->
                <div x-show="currentStep === 4" class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card space-y-6">
                    <div>
                        <span class="inline-block bg-[#F5A623]/15 text-[#B45309] font-mono text-xs font-bold px-2.5 py-1 rounded-[8px] mb-2">STEP 04</span>
                        <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate">Set Your Business Location</h1>
                        <p class="text-brand-muted text-sm mt-2">
                            Customers on Bazaario discover sellers within their immediate radius. Set an accurate operating address and GPS pin.
                        </p>
                    </div>

                    <!-- Recommended Method: Browser GPS Auto-Detect -->
                    <div class="p-4 bg-[#FFF9ED] border border-brand-amber/40 rounded-[14px] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-[10px] bg-brand-amber/20 flex items-center justify-center text-amber-800 shrink-0">
                                <span class="material-symbols-outlined text-[22px]">my_location</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold font-heading text-brand-slate uppercase">Recommended Method</h4>
                                <p class="text-xs text-brand-muted">Detect latitude &amp; longitude directly via browser device sensor</p>
                            </div>
                        </div>
                        <button type="button" @click="detectLocation()" :disabled="locating" class="px-4 py-2.5 bg-brand-slate text-brand-amber hover:bg-slate-800 text-xs font-mono font-bold rounded-[14px] transition flex items-center justify-center gap-2 shrink-0">
                            <span x-show="!locating" class="material-symbols-outlined text-[16px]">near_me</span>
                            <span x-show="locating" class="material-symbols-outlined text-[16px] animate-spin">sync</span>
                            <span x-text="locating ? 'Detecting Coordinates...' : (gpsLocked ? '✓ GPS Synced' : 'Use Current Location')">Use Current Location</span>
                        </button>
                    </div>

                    <!-- Address Input -->
                    <div>
                        <label for="address" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                            Street Address / Plot No. <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="address" name="address" x-model="address" required
                               placeholder="e.g. Plot 42, Tamluk Rural Sector, Near Kangsabati Canal"
                               class="w-full px-4 py-3.5 bg-brand-subtle border @error('address') border-rose-500 ring-2 ring-rose-200 @else border-brand-outline @enderror text-brand-slate text-sm rounded-[14px] focus:outline-none focus:border-brand-amber transition">
                        @error('address')
                            <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City, District/State, PIN Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                                City / Town <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="city" name="city" x-model="city" required
                                   placeholder="Contai"
                                   class="w-full px-3.5 py-3 bg-brand-subtle border @error('city') border-rose-500 @else border-brand-outline @enderror text-brand-slate text-sm rounded-[14px]">
                        </div>
                        <div>
                            <label for="state" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                                State <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" id="state" name="state" x-model="state" required
                                   placeholder="West Bengal"
                                   class="w-full px-3.5 py-3 bg-brand-subtle border @error('state') border-rose-500 @else border-brand-outline @enderror text-brand-slate text-sm rounded-[14px]">
                        </div>
                        <div>
                            <label for="postal_code" class="block text-xs font-semibold font-heading text-brand-slate uppercase tracking-wider mb-2">
                                Postal / PIN Code
                            </label>
                            <input type="text" id="postal_code" name="postal_code" x-model="postalCode"
                                   placeholder="721401"
                                   class="w-full px-3.5 py-3 bg-brand-subtle border border-brand-outline text-brand-slate text-sm rounded-[14px] font-mono">
                        </div>
                    </div>

                    <!-- Google Maps Interactive Picker & Coordinates -->
                    <div class="p-5 bg-white border border-brand-outline rounded-[14px] shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="text-xs font-heading font-bold text-brand-slate uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-[18px] text-brand-amber">map</span>
                                    Google Map Location Pin
                                </span>
                                <p class="text-[11px] text-brand-muted mt-0.5">Drag map / update pin to verify your operating location</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a :href="'https://www.google.com/maps/search/?api=1&query=' + latitude + ',' + longitude" target="_blank" class="px-2.5 py-1 rounded-[8px] bg-brand-subtle hover:bg-slate-100 text-brand-slate text-[11px] font-mono font-medium border border-brand-outline flex items-center gap-1 transition">
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                    <span>Open in Google Maps</span>
                                </a>
                                <span class="text-[11px] font-mono text-brand-green bg-green-50 px-2 py-0.5 rounded border border-green-200" x-text="gpsLocked ? '✓ GPS Locked' : 'Google Map Active'">
                                    Google Map Active
                                </span>
                            </div>
                        </div>

                        <!-- Embedded Interactive Google Map -->
                        <div class="relative w-full h-64 sm:h-72 rounded-[12px] overflow-hidden border border-brand-outline bg-slate-100 shadow-inner">
                            <iframe
                                class="w-full h-full border-0"
                                loading="lazy"
                                allowfullscreen
                                referrerpolicy="no-referrer-when-downgrade"
                                :src="'https://maps.google.com/maps?q=' + latitude + ',' + longitude + '&z=15&output=embed'">
                            </iframe>
                            
                            <!-- Overlay badge showing selected store pin -->
                            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-md px-3 py-1.5 rounded-[10px] shadow border border-brand-outline/80 flex items-center gap-2 pointer-events-none">
                                <div class="w-6 h-6 rounded-full bg-brand-slate border-2 border-brand-amber text-brand-amber flex items-center justify-center text-xs">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-mono text-brand-muted uppercase">Selected Pin</span>
                                    <span class="text-xs font-heading font-bold text-brand-slate truncate max-w-[180px]" x-text="shopName || 'Business Location'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Coordinate Controls & Quick Search -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label for="latitude" class="block text-[11px] font-mono uppercase text-brand-muted">
                                    Latitude Coordinate <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <input type="text" id="latitude" name="latitude" x-model="latitude" required
                                           class="w-full pl-8 pr-3 py-2 bg-brand-subtle border @error('latitude') border-rose-500 ring-2 ring-rose-200 @else border-brand-outline @enderror rounded-[10px] font-mono text-xs text-brand-slate focus:bg-white focus:outline-none focus:border-brand-amber">
                                    <span class="material-symbols-outlined text-brand-amber absolute left-2.5 top-1/2 -translate-y-1/2 text-[15px]">north</span>
                                </div>
                                @error('latitude')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="longitude" class="block text-[11px] font-mono uppercase text-brand-muted">
                                    Longitude Coordinate <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <input type="text" id="longitude" name="longitude" x-model="longitude" required
                                           class="w-full pl-8 pr-3 py-2 bg-brand-subtle border @error('longitude') border-rose-500 ring-2 ring-rose-200 @else border-brand-outline @enderror rounded-[10px] font-mono text-xs text-brand-slate focus:bg-white focus:outline-none focus:border-brand-amber">
                                    <span class="material-symbols-outlined text-brand-amber absolute left-2.5 top-1/2 -translate-y-1/2 text-[15px]">east</span>
                                </div>
                                @error('longitude')
                                    <p class="text-xs text-rose-600 mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 Bottom Navigation -->
                    <div class="pt-6 flex items-center justify-between border-t border-brand-outline/60">
                        <button type="button" @click="goToStep(3)" class="px-5 py-3 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition">
                            Back
                        </button>
                        <button type="button" @click="goToStep(5)" class="px-8 py-3.5 bg-brand-amber hover:bg-brand-amber-dark text-brand-slate font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2">
                            <span>Review Application</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </button>
                    </div>
                </div>

                <!-- ============================================================== -->
                <!-- STEP 5: REVIEW APPLICATION MATRIX -->
                <!-- ============================================================== -->
                <div x-show="currentStep === 5" class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card space-y-6">
                    <div>
                        <span class="inline-block bg-[#F5A623]/15 text-[#B45309] font-mono text-xs font-bold px-2.5 py-1 rounded-[8px] mb-2">STEP 05</span>
                        <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate">Review Your Application</h1>
                        <p class="text-brand-muted text-sm mt-2">
                            Please verify your information before dispatching the registration packet for administrator review.
                        </p>
                    </div>

                    @if ($errors->any())
                        <div class="p-4 rounded-[12px] bg-rose-50 border border-rose-300 text-rose-800 text-xs font-mono space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-rose-900">
                                <span class="material-symbols-outlined text-[16px]">warning</span>
                                <span>The application could not be submitted due to the following errors:</span>
                            </div>
                            @foreach ($errors->all() as $err)
                                <p class="ml-5 list-item text-rose-700 font-medium">{{ $err }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-5">
                        
                        <!-- CARD 1: ACCOUNT -->
                        <div class="border border-brand-outline rounded-[14px] p-5 bg-brand-subtle/50">
                            <div class="flex items-center justify-between border-b border-brand-outline/80 pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    <h3 class="font-heading font-bold text-xs text-brand-slate uppercase tracking-wider">Account Credentials</h3>
                                </div>
                                <button type="button" @click="goToStep(1)" class="text-xs font-mono font-semibold text-brand-amber hover:text-brand-amber-dark flex items-center gap-1">
                                    Edit ✏️
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Full Name</span>
                                    <span class="font-semibold text-brand-slate text-sm">{{ $user->name }}</span>
                                </div>
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Email Address</span>
                                    <span class="font-semibold text-brand-slate text-sm">{{ $user->email }}</span>
                                </div>
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Phone Number</span>
                                    <span class="font-semibold text-brand-slate text-sm">{{ $user->phone ?? '+91 98765 43210' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 2: SELLER TYPE -->
                        <div class="border border-brand-outline rounded-[14px] p-5 bg-brand-subtle/50">
                            <div class="flex items-center justify-between border-b border-brand-outline/80 pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    <h3 class="font-heading font-bold text-xs text-brand-slate uppercase tracking-wider">Seller Type</h3>
                                </div>
                                <button type="button" @click="goToStep(2)" class="text-xs font-mono font-semibold text-brand-amber hover:text-brand-amber-dark flex items-center gap-1">
                                    Edit ✏️
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-2xl" x-text="sellerType === 'Farmer' ? '🌾' : (sellerType === 'Kirana Store' ? '🏪' : (sellerType === 'Dark Store' ? '📦' : '👤'))">🌾</span>
                                <div>
                                    <p class="font-heading font-bold text-base text-brand-slate" x-text="sellerType">Farmer</p>
                                    <p class="text-xs text-brand-muted" x-text="sellerType === 'Farmer' ? 'Direct harvest supply and wholesale auctions' : (sellerType === 'Kirana Store' ? 'Hyperlocal neighborhood retail essentials' : (sellerType === 'Dark Store' ? 'High volume automated micro-fulfillment' : 'Artisan and independent creator'))"></p>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 3: BUSINESS DETAILS -->
                        <div class="border border-brand-outline rounded-[14px] p-5 bg-brand-subtle/50">
                            <div class="flex items-center justify-between border-b border-brand-outline/80 pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    <h3 class="font-heading font-bold text-xs text-brand-slate uppercase tracking-wider">Business Details</h3>
                                </div>
                                <button type="button" @click="goToStep(3)" class="text-xs font-mono font-semibold text-brand-amber hover:text-brand-amber-dark flex items-center gap-1">
                                    Edit ✏️
                                </button>
                            </div>
                            <div class="mb-3">
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Shop / Farm Name</span>
                                    <span class="font-semibold text-brand-slate text-sm" x-text="shopName || 'Not specified'"></span>
                                </div>
                            </div>
                            <div>
                                <span class="font-mono text-brand-muted block uppercase text-[10px]">Narrative Bio</span>
                                <p class="text-xs text-brand-slate mt-1 leading-relaxed" x-text="bio || 'No description provided.'"></p>
                            </div>
                        </div>

                        <!-- CARD 4: LOCATION -->
                        <div class="border border-brand-outline rounded-[14px] p-5 bg-brand-subtle/50">
                            <div class="flex items-center justify-between border-b border-brand-outline/80 pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    <h3 class="font-heading font-bold text-xs text-brand-slate uppercase tracking-wider">Location &amp; Coordinates</h3>
                                </div>
                                <button type="button" @click="goToStep(4)" class="text-xs font-mono font-semibold text-brand-amber hover:text-brand-amber-dark flex items-center gap-1">
                                    Edit ✏️
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Street Address</span>
                                    <span class="font-semibold text-brand-slate text-sm" x-text="address || 'Not specified'"></span>
                                </div>
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">City / State / PIN</span>
                                    <span class="font-semibold text-brand-slate text-sm" x-text="(city || 'City') + ', ' + (state || 'State') + ' - ' + (postalCode || 'PIN')"></span>
                                </div>
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">GPS Coordinates</span>
                                    <span class="font-mono font-semibold text-brand-slate text-sm" x-text="formatCoord(latitude, longitude)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- CARD 5: VERIFICATION & COMPLIANCE DOCUMENTS PACKET -->
                        <div class="border border-brand-outline rounded-[14px] p-5 bg-brand-subtle/50">
                            <div class="flex items-center justify-between border-b border-brand-outline/80 pb-3 mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                    <h3 class="font-heading font-bold text-xs text-brand-slate uppercase tracking-wider">Verification Documents Packet (<span x-text="activeDocs.length"></span> Documents)</h3>
                                </div>
                                <button type="button" @click="goToStep(3)" class="text-xs font-mono font-semibold text-brand-amber hover:text-brand-amber-dark flex items-center gap-1">
                                    Edit ✏️
                                </button>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <template x-for="docKey in activeDocs" :key="'review-' + docKey">
                                    <div class="p-3 bg-white border border-brand-outline rounded-[10px] space-y-1 shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-brand-slate" x-text="getDocLabel(docKey)"></span>
                                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded"
                                                  :class="docFiles[docKey] ? 'bg-emerald-100 text-emerald-800 font-bold border border-emerald-300' : 'bg-amber-100 text-amber-850 font-semibold border border-amber-200'">
                                                <span x-text="docFiles[docKey] ? '✓ File Attached' : 'Details Pending File'"></span>
                                            </span>
                                        </div>
                                        <div class="text-[11px] font-mono text-brand-muted truncate">
                                            <span x-text="getDocSummaryNumber(docKey)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>

                    <!-- Step 5 Bottom Navigation -->
                    <div class="pt-8 flex items-center justify-between border-t border-brand-outline/60 mt-8">
                        <button type="button" @click="goToStep(4)" class="px-5 py-3 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition">
                            Back
                        </button>
                        <button type="button" 
                                @click="openConfirmModal()" 
                                :disabled="isSubmitting"
                                class="px-8 py-4 bg-brand-green hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2 cursor-pointer">
                            <span x-show="!isSubmitting" class="material-symbols-outlined text-[20px]">send</span>
                            <span x-show="isSubmitting" class="material-symbols-outlined text-[20px] animate-spin">progress_activity</span>
                            <span x-text="isSubmitting ? 'Submitting Application...' : 'Submit for Approval'">Submit for Approval</span>
                        </button>
                    </div>
                </div>

            </form>
        </section>

    </div>

    <!-- CONFIRMATION MODAL -->
    <div x-show="confirmModal" 
         x-transition:enter="transition ease-out duration-200" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-150" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0" 
         class="fixed inset-0 z-50 bg-brand-slate/60 backdrop-blur-sm flex items-center justify-center p-4" 
         style="display: none;" 
         @keydown.escape.window="confirmModal = false">
        
        <div class="bg-white border border-brand-outline rounded-[14px] max-w-md w-full p-6 sm:p-8 shadow-modal transform transition-all space-y-5">
            <div class="w-12 h-12 rounded-[14px] bg-amber-100 text-brand-amber flex items-center justify-center text-2xl">
                📋
            </div>
            <h3 class="text-xl font-heading font-bold text-brand-slate">Submit Application?</h3>
            <p class="text-xs text-brand-muted leading-relaxed">
                By submitting, you certify that all farm/shop details and geographic coordinates provided are true and accurate. Your application will be sent to the Bazaario Regional Compliance team for verification.
            </p>
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-brand-outline">
                <button type="button" @click="confirmModal = false" :disabled="isSubmitting" class="px-4 py-2.5 border border-brand-outline text-brand-slate font-heading text-xs font-semibold rounded-[12px] hover:bg-slate-100 transition disabled:opacity-50">
                    Go Back &amp; Edit
                </button>
                <button type="button" @click="submitFinalForm()" :disabled="isSubmitting" class="px-6 py-2.5 bg-brand-amber hover:bg-brand-amber-dark disabled:opacity-60 text-brand-slate font-heading font-bold text-xs rounded-[12px] transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                    <span x-show="isSubmitting" class="material-symbols-outlined text-[16px] animate-spin">progress_activity</span>
                    <span x-text="isSubmitting ? 'Submitting...' : 'Yes, Submit Now'">Yes, Submit Now</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function sellerWizard(config) {
    return {
        currentStep: config.initialStep || 1,
        sellerType: config.initialType || 'Farmer',
        shopName: config.initialShopName || '',
        bio: config.initialBio || '',
        address: config.initialAddress || '',
        city: config.initialCity || 'Contai',
        state: config.initialState || 'West Bengal',
        postalCode: config.initialPostal || '721401',
        latitude: config.initialLat || '21.7781',
        longitude: config.initialLng || '87.7516',
        gstin: config.initialGstin || '',
        panNumber: config.initialPan || '',
        tradeLicense: config.initialTrade || '',
        fssaiNumber: config.initialFssai || '',
        bankAccount: config.initialBank || '',
        bankIfsc: config.initialIfsc || '',
        aadhaarNumber: config.initialAadhaar || '',
        mandiLicense: config.initialMandi || '',
        udyamNumber: config.initialUdyam || '',
        activeDocs: (config.initialActiveDocs && config.initialActiveDocs.length) ? config.initialActiveDocs : ['pan', 'bank'],
        selectedDocToAdd: '',

        availableDocCatalog: [
            { key: 'pan', label: 'Permanent Account Number (PAN Card)', shortLabel: 'PAN Card', badge: 'Direct Tax ID', icon: 'badge', mandatory: true },
            { key: 'bank', label: 'Escrow Bank Account & Cheque / Passbook', shortLabel: 'Bank Account', badge: 'Payouts & Escrow', icon: 'account_balance', mandatory: true },
            { key: 'gstin', label: 'GST Registration Certificate (GSTIN)', shortLabel: 'GSTIN Certificate', badge: 'Indirect Tax', icon: 'receipt_long', mandatory: false },
            { key: 'trade_license', label: 'Municipality Trade License / Shop & Est.', shortLabel: 'Trade License', badge: 'Municipal Body', icon: 'domain', mandatory: false },
            { key: 'fssai', label: 'FSSAI Food Safety License / Registration', shortLabel: 'FSSAI License', badge: 'Food & Agro', icon: 'restaurant', mandatory: false },
            { key: 'aadhaar', label: 'Aadhaar Card / Government Photo ID', shortLabel: 'Aadhaar / Gov ID', badge: 'Proprietor Identity', icon: 'fingerprint', mandatory: false },
            { key: 'mandi_license', label: 'APMC / Mandi Market Trading License', shortLabel: 'Mandi License', badge: 'Wholesale & Auctions', icon: 'agriculture', mandatory: false },
            { key: 'udyam', label: 'Udyam / MSME Registration Certificate', shortLabel: 'MSME Certificate', badge: 'Small Business', icon: 'workspace_premium', mandatory: false }
        ],

        docFiles: {
            pan: '',
            bank: '',
            gstin: '',
            trade_license: '',
            fssai: '',
            aadhaar: '',
            mandi_license: '',
            udyam: ''
        },

        addDocument(key) {
            if (!key) key = this.selectedDocToAdd;
            if (!key) return;
            if (!this.activeDocs.includes(key)) {
                this.activeDocs.push(key);
                this.notify('Added ' + this.getDocLabel(key) + ' to your application packet.', 'info');
            }
            this.selectedDocToAdd = '';
        },

        removeDocument(key) {
            this.activeDocs = this.activeDocs.filter(k => k !== key);
            this.notify('Removed document from application packet.', 'info');
        },

        isDocActive(key) {
            return this.activeDocs.includes(key);
        },

        getDocLabel(key) {
            const found = this.availableDocCatalog.find(d => d.key === key);
            return found ? found.label : key.toUpperCase();
        },

        getDocSummaryNumber(key) {
            switch (key) {
                case 'pan': return this.panNumber ? 'PAN: ' + this.panNumber : 'PAN No Pending';
                case 'bank': return (this.bankAccount ? 'A/C: ••••' + this.bankAccount.slice(-4) : 'A/C Pending') + (this.bankIfsc ? ' • IFSC: ' + this.bankIfsc : '');
                case 'gstin': return this.gstin ? 'GSTIN: ' + this.gstin : 'GSTIN Pending';
                case 'trade_license': return this.tradeLicense ? 'Lic: ' + this.tradeLicense : 'Trade Lic Pending';
                case 'fssai': return this.fssaiNumber ? 'FSSAI: ' + this.fssaiNumber : 'FSSAI Pending';
                case 'aadhaar': return this.aadhaarNumber ? 'ID: ' + this.aadhaarNumber : 'ID Pending';
                case 'mandi_license': return this.mandiLicense ? 'APMC: ' + this.mandiLicense : 'APMC Pending';
                case 'udyam': return this.udyamNumber ? 'Udyam: ' + this.udyamNumber : 'Udyam Pending';
                default: return 'Details Attached';
            }
        },

        imagePreview: config.existingLogo || '',
        imageName: config.existingLogo ? 'storefront_image.jpg' : '',
        imageSize: config.existingLogo ? '1.2 MB' : '',
        locating: false,
        gpsLocked: false,
        confirmModal: false,
        isSubmitting: false,
        stepTitles: [
            'Account Registration',
            'Seller Classification',
            'Shop & Farm Details',
            'Business Location',
            'Application Review'
        ],

        notify(msg, type = 'info') {
            if (window.showToast) {
                window.showToast(msg, type);
            } else {
                console.log(`[Toast ${type}]: ${msg}`);
            }
        },

        formatCoord(lat, lng) {
            const cleanLat = (lat !== undefined && lat !== null ? String(lat) : '').replace(/[^0-9.\-+]/g, '');
            const cleanLng = (lng !== undefined && lng !== null ? String(lng) : '').replace(/[^0-9.\-+]/g, '');
            return (cleanLat || '0.0000') + '° N, ' + (cleanLng || '0.0000') + '° E';
        },

        goToStep(step) {
            if (step < 1) step = 1;
            if (step > 5) step = 5;
            this.currentStep = step;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        setSellerType(type) {
            this.sellerType = type;
        },

        handleImageUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {
                this.notify('Storefront image must be under 5MB.', 'error');
                event.target.value = '';
                return;
            }

            this.imageName = file.name;
            this.imageSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';

            const reader = new FileReader();
            reader.onload = (e) => {
                this.imagePreview = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        removeImage() {
            this.imagePreview = '';
            this.imageName = '';
            this.imageSize = '';
            const input = document.getElementById('storefront_image');
            if (input) input.value = '';
        },

        detectLocation() {
            if (!navigator.geolocation) {
                this.notify('Browser does not support geolocation. Please enter coordinates manually.', 'warning');
                return;
            }
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    this.latitude = pos.coords.latitude.toFixed(4);
                    this.longitude = pos.coords.longitude.toFixed(4);
                    this.gpsLocked = true;
                    this.locating = false;
                },
                (err) => {
                    console.warn('Geolocation error:', err);
                    this.locating = false;
                    this.notify('Unable to retrieve location automatically. Please enter coordinates manually.', 'warning');
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        },

        openConfirmModal() {
            if (!this.shopName || !this.address || !this.city || !this.state) {
                this.notify('Please fill out all required fields before submitting.', 'warning');
                return;
            }
            this.confirmModal = true;
        },

        submitFinalForm() {
            this.confirmModal = false;
            this.isSubmitting = true;
            const form = document.getElementById('onboarding-form');
            if (form) {
                form.submit();
            }
        }
    };
}
</script>
@endpush
