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
            <form id="onboarding-form" method="POST" action="{{ route('seller.onboarding.submit') }}" enctype="multipart/form-data">
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

                    <!-- Dynamic Produce / Specialty Attribute -->
                    <div class="p-5 bg-brand-subtle border border-brand-outline rounded-[14px] space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-brand-amber text-[18px]">tune</span>
                            <h4 class="text-xs font-bold font-heading text-brand-slate uppercase tracking-wider">Classification Attributes</h4>
                        </div>
                        
                        <!-- When Farmer -->
                        <div x-show="sellerType === 'Farmer'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Primary Produce Specialty</label>
                                <input type="text" x-model="extraAttribute1" placeholder="e.g. Seasonal Vegetables, Desi Wheat, Pulses" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Farm Land Area (Acres)</label>
                                <input type="text" x-model="extraAttribute2" placeholder="e.g. 12.5 Acres" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                        </div>

                        <!-- When Kirana Store -->
                        <div x-show="sellerType === 'Kirana Store'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Primary Inventory Category</label>
                                <input type="text" x-model="extraAttribute1" placeholder="e.g. Grains, Oil, Spices, Dairy" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Daily Operating Hours</label>
                                <input type="text" x-model="extraAttribute2" placeholder="e.g. 07:00 AM - 10:30 PM" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                        </div>

                        <!-- When Dark Store -->
                        <div x-show="sellerType === 'Dark Store'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Storage Infrastructure</label>
                                <input type="text" x-model="extraAttribute1" placeholder="e.g. Ambient &amp; Cold Chain Storage" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Fulfillment Capability</label>
                                <input type="text" x-model="extraAttribute2" placeholder="e.g. 500 orders / shift" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                        </div>

                        <!-- When Individual -->
                        <div x-show="sellerType === 'Individual'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Craft / Specialty Category</label>
                                <input type="text" x-model="extraAttribute1" placeholder="e.g. Handmade Sweets, Terracotta, Textiles" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Weekly Output Volume</label>
                                <input type="text" x-model="extraAttribute2" placeholder="e.g. 50-100 units / week" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px]">
                            </div>
                        </div>
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

                    <!-- Business Verification, Tax & Bank Details Section -->
                    <div class="p-5 bg-brand-subtle border border-brand-outline rounded-[14px] space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-brand-amber text-[18px]">verified_user</span>
                            <h4 class="text-xs font-bold font-heading text-brand-slate uppercase tracking-wider">Business Verification &amp; Bank Details</h4>
                        </div>
                        <p class="text-xs text-brand-muted">Provide your registration details and escrow bank credentials for admin clearance.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">GSTIN Number (Optional)</label>
                                <input type="text" name="gstin" x-model="gstin" placeholder="e.g. 19AAACG1234A1Z5" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] uppercase font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">PAN Number (Optional)</label>
                                <input type="text" name="pan_number" x-model="panNumber" placeholder="e.g. ABCDE1234F" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] uppercase font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Trade License Number (Optional)</label>
                                <input type="text" name="trade_license_number" x-model="tradeLicense" placeholder="e.g. TL-MED-2024-8841" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">FSSAI / GI Certification (Optional)</label>
                                <input type="text" name="fssai_number" x-model="fssaiNumber" placeholder="e.g. 10020031000123" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Escrow Bank Account Number</label>
                                <input type="text" name="bank_account_number" x-model="bankAccount" placeholder="e.g. 918010023456789" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] font-mono">
                            </div>
                            <div>
                                <label class="block font-medium text-brand-slate mb-1">Bank IFSC Code</label>
                                <input type="text" name="bank_ifsc" x-model="bankIfsc" placeholder="e.g. HDFC0001234" class="w-full px-3.5 py-2.5 bg-white border border-brand-outline rounded-[10px] uppercase font-mono">
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
                                    <button type="button" @click="document.getElementById('storefront_image').click()" class="text-sm font-semibold font-heading text-brand-slate hover:text-brand-amber underline underline-offset-4">
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

                    <!-- Radar Map Preview & Manual Lat/Lng -->
                    <div class="p-5 bg-white border border-brand-outline rounded-[14px] shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-heading font-bold text-brand-slate uppercase tracking-wider">
                                Location Preview &amp; Pin Coordinates
                            </span>
                            <span class="text-[11px] font-mono text-brand-green bg-green-50 px-2 py-0.5 rounded border border-green-200" x-text="gpsLocked ? 'GPS Locked' : 'Radar Calibrated'">
                                GPS Locked
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                            
                            <!-- Stylized Radar Map Preview -->
                            <div class="relative h-44 rounded-[10px] bg-[#E9E5DC] overflow-hidden border border-brand-outline flex items-center justify-center">
                                <div class="absolute inset-0 opacity-40" style="background-image: radial-gradient(#0F172A 1px, transparent 1px); background-size: 16px 16px;"></div>
                                <div class="absolute w-28 h-28 rounded-full bg-brand-amber/20 border-2 border-brand-amber/50 animate-pulse"></div>
                                <div class="relative z-10 flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full bg-brand-slate border-2 border-brand-amber text-brand-amber flex items-center justify-center shadow-lg">
                                        <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    </div>
                                    <span class="mt-1 bg-brand-slate text-white text-[10px] font-mono px-2 py-0.5 rounded-[6px] shadow truncate max-w-[160px]" x-text="shopName || 'My Enterprise'">
                                        Patel Organic Greens
                                    </span>
                                </div>
                                <div class="absolute bottom-2 left-2 bg-white/90 backdrop-blur px-2 py-1 rounded text-[10px] font-mono text-brand-muted border border-brand-outline/80">
                                    Radius: 25 km delivery
                                </div>
                            </div>

                            <!-- Manual Coordinate Controls -->
                            <div class="space-y-3">
                                <div>
                                    <label for="latitude" class="block text-[11px] font-mono uppercase text-brand-muted">
                                        Latitude Coordinate <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="latitude" name="latitude" x-model="latitude" required
                                           class="w-full mt-1 px-3 py-2 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs text-brand-slate">
                                </div>
                                <div>
                                    <label for="longitude" class="block text-[11px] font-mono uppercase text-brand-muted">
                                        Longitude Coordinate <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="longitude" name="longitude" x-model="longitude" required
                                           class="w-full mt-1 px-3 py-2 bg-brand-subtle border border-brand-outline rounded-[10px] font-mono text-xs text-brand-slate">
                                </div>
                                <p class="text-[11px] text-brand-muted leading-relaxed">
                                    Coordinates are used by the Bazaario hyperlocal discovery algorithm to display your catalog to nearby customers.
                                </p>
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-3">
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Shop / Farm Name</span>
                                    <span class="font-semibold text-brand-slate text-sm" x-text="shopName || 'Not specified'"></span>
                                </div>
                                <div>
                                    <span class="font-mono text-brand-muted block uppercase text-[10px]">Specialty Attributes</span>
                                    <span class="font-semibold text-brand-slate text-sm" x-text="(extraAttribute1 ? extraAttribute1 + ', ' : '') + (extraAttribute2 || 'General catalog')"></span>
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
                                    <span class="font-mono font-semibold text-brand-slate text-sm" x-text="(latitude || '0') + '° N, ' + (longitude || '0') + '° E'"></span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Step 5 Bottom Navigation -->
                    <div class="pt-8 flex items-center justify-between border-t border-brand-outline/60 mt-8">
                        <button type="button" @click="goToStep(4)" class="px-5 py-3 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition">
                            Back
                        </button>
                        <button type="button" @click="openConfirmModal()" class="px-8 py-4 bg-brand-green hover:bg-green-700 text-white font-heading font-bold text-sm rounded-[14px] shadow-sm transition flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                            <span>Submit for Approval</span>
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
                <button type="button" @click="confirmModal = false" class="px-4 py-2.5 border border-brand-outline text-brand-slate font-heading text-xs font-semibold rounded-[12px] hover:bg-slate-100 transition">
                    Go Back &amp; Edit
                </button>
                <button type="button" @click="submitFinalForm()" class="px-6 py-2.5 bg-brand-amber hover:bg-brand-amber-dark text-brand-slate font-heading font-bold text-xs rounded-[12px] transition shadow-sm">
                    Yes, Submit Now
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
        extraAttribute1: 'Fresh Produce',
        extraAttribute2: 'Local Distribution',
        imagePreview: config.existingLogo || '',
        imageName: config.existingLogo ? 'storefront_image.jpg' : '',
        imageSize: config.existingLogo ? '1.2 MB' : '',
        locating: false,
        gpsLocked: false,
        confirmModal: false,
        stepTitles: [
            'Account Registration',
            'Seller Classification',
            'Shop & Farm Details',
            'Business Location',
            'Application Review'
        ],

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
                alert('Storefront image must be under 5MB.');
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
                alert('Browser does not support geolocation. Please enter coordinates manually.');
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
                    alert('Unable to retrieve location automatically. Please enter coordinates manually.');
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        },

        openConfirmModal() {
            if (!this.shopName || !this.address || !this.city || !this.state) {
                alert('Please fill out all required fields before submitting.');
                return;
            }
            this.confirmModal = true;
        },

        submitFinalForm() {
            this.confirmModal = false;
            document.getElementById('onboarding-form').submit();
        }
    };
}
</script>
@endpush
