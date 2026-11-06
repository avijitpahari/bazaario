@extends('layouts.seller')

@section('title', 'Shop Profile & Settings — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $rawOperatingDays = $profile?->operating_days;
    if (is_array($rawOperatingDays)) {
        $operatingDays = $rawOperatingDays;
    } elseif (is_string($rawOperatingDays)) {
        $operatingDays = json_decode($rawOperatingDays, true) ?? ['mon','tue','wed','thu','fri','sat'];
    } else {
        $operatingDays = ['mon','tue','wed','thu','fri','sat'];
    }
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    tab: 'profile',
    charCount: {{ strlen($profile?->bio ?? '') }},
    unsavedChanges: false,
    operatingDays: {{ json_encode($operatingDays) }},
    toggleDay(day) {
        if (this.operatingDays.includes(day)) {
            if (this.operatingDays.length > 1) {
                this.operatingDays = this.operatingDays.filter(d => d !== day);
                this.unsavedChanges = true;
            }
        } else {
            this.operatingDays.push(day);
            this.unsavedChanges = true;
        }
    }
}">

    <!-- Top Breadcrumb & Action Header -->
    <div class="px-6 py-4 bg-white rounded-2xl shadow-xs mb-6 border border-slate-200/80">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Seller Center</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Account</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-amber-600 font-bold uppercase tracking-wider">Shop Profile &amp; Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-slate-900 font-extrabold tracking-tight">Shop Profile &amp; Settings</h1>
                    <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-semibold border border-slate-200">
                        ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">Manage your verified business identity, farm origin coordinates, dispatch operating hours, and authentication credentials.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.dashboard') }}" class="h-11 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/90 text-sm font-semibold flex items-center gap-2 shadow-2xs transition-colors">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Dashboard</span>
                </a>
                <button type="button" @click="$refs.profileForm.submit()" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 text-sm font-heading font-bold shadow-md transition-all flex items-center gap-2 active:scale-95">
                    <span class="material-symbols-outlined text-[20px]">check</span>
                    <span>Save All Changes</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: Navigation & Trust Widget (3 Cols) -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            
            <!-- Configuration Menu -->
            <div class="bg-white rounded-2xl p-3 shadow-xs border border-slate-200/80 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-amber-500/10 text-amber-950 font-heading font-bold border border-amber-300/60 shadow-2xs transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-amber-600">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-amber-500 shadow-xs"></span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>

            <!-- Trust & Verification Widget -->
            <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-200/80 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-xl overflow-hidden bg-slate-100 shrink-0 shadow-xs ring-2 ring-slate-100">
                        @php
                            $activeAvatar = $profile?->logo_path ? asset('storage/' . $profile->logo_path) : ($seller?->profile_image ? asset('storage/' . $seller?->profile_image) : null);
                        @endphp
                        @if($activeAvatar)
                            <img class="w-full h-full object-cover" 
                                 src="{{ $activeAvatar }}" 
                                 alt="{{ $profile?->shop_name ?? $seller?->name }}"
                                 onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');">
                            <div class="hidden w-full h-full bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-heading font-bold text-xl">
                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                            </div>
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-amber-500 to-amber-600 flex items-center justify-center text-slate-950 font-heading font-bold text-xl">
                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-600 rounded-full flex items-center justify-center ring-2 ring-white">
                            <span class="material-symbols-outlined text-white text-[10px]">check</span>
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h2 class="font-heading text-sm font-bold text-slate-900 truncate">{{ $profile?->shop_name ?? 'My Farm Store' }}</h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-mono text-[10px] font-bold inline-flex items-center gap-1">
                                <span class="material-symbols-outlined text-[12px] text-emerald-600">verified</span> Verified {{ $profile?->seller_type ?? 'Farmer' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col gap-2.5 font-mono text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-500">Merchant Status</span>
                        <span class="font-bold text-emerald-700 uppercase bg-emerald-50 border border-emerald-200/60 px-2 py-0.5 rounded-md text-[11px]">{{ $profile?->status ?? 'Approved' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-500">Commission Tier</span>
                        <span class="text-slate-900 font-bold font-mono">{{ $profile?->commission_rate ? $profile->commission_rate.'%' : '10.00%' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span class="text-slate-500">FSSAI License</span>
                        <span class="text-slate-700 font-mono text-[11px]">{{ $profile?->fssai_number ?? '#21524021000918' }}</span>
                    </div>
                </div>

                <!-- Trust Score Progress -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-heading font-semibold text-slate-900">Market Trust Index</span>
                        <span class="font-mono font-bold text-amber-600">{{ number_format($profile?->trust_score ?? 94, 0) }}/100</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden border border-slate-200/60">
                        <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full" style="width: {{ $profile?->trust_score ?? 94 }}%;"></div>
                    </div>
                    <span class="font-mono text-[10px] text-slate-400">Eligible for 0% Escrow Hold and Instant Payouts</span>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-amber-500 text-[18px] shrink-0 mt-0.5">help_center</span>
                    <div class="flex flex-col text-xs">
                        <span class="font-heading font-bold text-slate-900">Need Legal Updates?</span>
                        <span class="text-slate-500 text-[11px] leading-snug mt-0.5">GSTIN and legal filings require administrative compliance desk review.</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Settings Workspace (9 Cols) -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <!-- Subtab Navigation Bar -->
            <div class="bg-white p-1.5 rounded-2xl shadow-2xs border border-slate-200/80 flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('seller.account.profile') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-heading text-xs font-bold flex items-center gap-2 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>1. Shop Profile &amp; Identity</span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-heading text-xs font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                    <span>2. Farm Location &amp; Coordinates</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="px-4 py-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 font-heading text-xs font-semibold flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                    <span>3. Security &amp; Credentials</span>
                </a>
            </div>

            <form x-ref="profileForm" method="POST" action="{{ route('seller.account.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6" @input="unsavedChanges = true">
                @csrf
                @method('PUT')

                <!-- SECTION 1.1: Visual Brand & Farm Photography -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-5">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-slate-400">Section 1.1</span>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Visual Brand &amp; Farm Photography</h2>
                        <p class="text-sm text-slate-500">Store banner and verified orchard imagery shown to buyers in the wholesale catalog and live auction rooms.</p>
                    </div>

                    <!-- Cover Banner Preview with Logo Inset -->
                    <div class="relative w-full h-56 md:h-64 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shadow-inner group">
                        <img id="banner-preview-img" class="w-full h-full object-cover {{ $profile?->banner_path ? '' : 'hidden' }}" src="{{ $profile?->banner_path ? asset('storage/' . $profile->banner_path) : '' }}" alt="Store Banner">
                        <div id="banner-placeholder" class="w-full h-full bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 flex items-center justify-center {{ $profile?->banner_path ? 'hidden' : '' }}">
                            <span class="material-symbols-outlined text-white/30 text-[72px]">landscape</span>
                        </div>

                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/25 to-transparent flex flex-col justify-end p-5 text-white">
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-white p-1 shadow-md shrink-0 border border-white/40 flex items-center justify-center">
                                        <img id="logo-preview-img" class="w-full h-full object-cover rounded-lg {{ $activeAvatar ? '' : 'hidden' }}" src="{{ $activeAvatar ?? '' }}" alt="Logo">
                                        <div id="logo-placeholder" class="w-full h-full bg-slate-900 flex items-center justify-center text-amber-400 font-heading font-bold text-2xl rounded-lg {{ $activeAvatar ? 'hidden' : '' }}">
                                            {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-heading text-lg font-bold text-white">{{ $profile?->shop_name ?? 'My Farm Store' }}</span>
                                        <span class="font-mono text-[11px] text-slate-300 uppercase tracking-wider">PRIMARY STOREFRONT COVER • 2560 × 720 PX</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="h-10 px-4 rounded-xl bg-white/95 hover:bg-white text-slate-900 text-xs font-heading font-bold backdrop-blur-sm transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                                        <span>Replace Banner</span>
                                        <input type="file" name="banner_image" accept="image/*" class="hidden" onchange="previewProfileMedia(this, 'banner')">
                                    </label>
                                    <label class="h-10 px-4 rounded-xl bg-white/95 hover:bg-white text-slate-900 text-xs font-heading font-bold backdrop-blur-sm transition-all shadow-xs flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                        <span>Change Logo</span>
                                        <input type="file" name="logo_image" accept="image/*" class="hidden" onchange="previewProfileMedia(this, 'logo')">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch certification note -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-600 shrink-0">
                                <span class="material-symbols-outlined text-[22px]">cloud_upload</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-heading font-bold text-slate-900">Upload Farm Photography &amp; APMC Certification Slips</span>
                                <span class="text-xs text-slate-500">PNG, JPG, or WEBP up to 10MB each. High resolution recommended for buyer trust.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.2: Core Business Identity & Signatory Details -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-slate-400">Section 1.2</span>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Core Business Identity &amp; Signatory Details</h2>
                        <p class="text-sm text-slate-500">Official marketplace identity tied to legal GSTIN #{{ $profile?->gstin ?? '27AABCG1294F1Z8' }}.</p>
                    </div>

                    <!-- Gated Role Notice -->
                    <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[20px]">psychiatry</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading text-sm font-bold text-slate-900">Registered Role: {{ $profile?->seller_type ?? 'Farmer' }} &amp; Cultivator</span>
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-mono text-[10px] font-bold border border-emerald-200">VERIFIED PRODUCER</span>
                                </div>
                                <p class="text-xs text-slate-600 mt-1">
                                    Changing your merchant classification requires documentary review by Bazaario Agricultural Compliance. Existing auction bids and active harvests are locked during audit.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="h-9 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-800 text-xs font-heading font-semibold border border-slate-200 transition-all shadow-2xs shrink-0">
                            Request Change
                        </button>
                    </div>

                    <!-- Form Input Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800 flex items-center justify-between">
                                <span>Farm / Shop Commercial Name</span>
                                <span class="font-mono text-[10px] text-slate-400">Public Storefront</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px]">store</span>
                                <input type="text" name="shop_name" value="{{ old('shop_name', $profile?->shop_name) }}" class="w-full h-11 pl-11 pr-4 bg-slate-50/60 hover:bg-white focus:bg-white rounded-xl text-sm text-slate-900 border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" required>
                            </div>
                            @error('shop_name') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800 flex items-center justify-between">
                                <span>Registered Signatory / Proprietor</span>
                                <span class="font-mono text-[10px] text-emerald-700 font-bold">KYC Matched</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px]">badge</span>
                                <input type="text" value="{{ $seller?->name }}" class="w-full h-11 pl-11 pr-4 bg-slate-100/80 rounded-xl text-sm text-slate-600 border border-slate-200 cursor-not-allowed outline-none shadow-2xs" readonly>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800 flex items-center justify-between">
                                <span>Primary Business Email</span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono text-[10px] font-bold">VERIFIED</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px]">mail</span>
                                <input type="email" value="{{ $seller?->email }}" class="w-full h-11 pl-11 pr-10 bg-slate-100/80 rounded-xl font-mono text-sm text-slate-600 border border-slate-200 cursor-not-allowed outline-none shadow-2xs" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-emerald-600 text-[18px]">verified</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800 flex items-center justify-between">
                                <span>Primary Dispatch Phone</span>
                                <span class="px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono text-[10px] font-bold">OTP BOUND</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px]">call</span>
                                <input type="tel" value="{{ $seller?->phone ?? '+91 98765 43210' }}" class="w-full h-11 pl-11 pr-10 bg-slate-100/80 rounded-xl font-mono text-sm text-slate-600 border border-slate-200 cursor-not-allowed outline-none shadow-2xs" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-heading font-semibold text-slate-800 flex items-center justify-between">
                                <span>Public Store &amp; Provenance Description</span>
                                <span class="font-mono text-[10px] text-slate-400">Markdown Supported • <span x-text="charCount"></span>/500 Chars</span>
                            </label>
                            <textarea name="bio" rows="4" maxlength="500" @input="charCount = $el.value.length" class="w-full p-4 bg-slate-50/60 hover:bg-white focus:bg-white rounded-xl text-sm text-slate-900 border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all resize-none leading-relaxed">{{ old('bio', $profile?->bio) }}</textarea>
                            @error('bio') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.3: Operating Hours & Logistics Dispatch SLA -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-slate-400">Section 1.3</span>
                        <h2 class="font-heading text-xl font-bold text-slate-900">Operating Hours &amp; Logistics Dispatch SLA</h2>
                        <p class="text-sm text-slate-500">Configure order acceptance cut-offs and daily courier harvest pickup handovers.</p>
                    </div>

                    <!-- Operating Days Buttons -->
                    <div class="flex flex-col gap-2.5">
                        <span class="text-xs font-heading font-semibold text-slate-800">Active Operating Harvest Days</span>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach(['mon' => 'MON', 'tue' => 'TUE', 'wed' => 'WED', 'thu' => 'THU', 'fri' => 'FRI', 'sat' => 'SAT', 'sun' => 'SUN'] as $key => $lbl)
                                <button type="button" @click="toggleDay('{{ $key }}')" 
                                        :class="operatingDays.includes('{{ $key }}') ? 'bg-slate-900 text-white border-slate-900 shadow-2xs' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100'" 
                                        class="h-12 rounded-xl font-mono text-xs font-bold flex flex-col items-center justify-center border transition-all">
                                    <span>{{ $lbl }}</span>
                                    <span class="text-[9px] uppercase font-normal" :class="operatingDays.includes('{{ $key }}') ? 'text-amber-400' : 'text-slate-400'" x-text="operatingDays.includes('{{ $key }}') ? 'Active' : 'Off'"></span>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="operating_days" :value="JSON.stringify(operatingDays)">
                    </div>

                    <!-- Time Window Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/70 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-[20px]">schedule</span>
                                <span class="text-xs font-heading font-bold text-slate-800">Order Acceptance Window</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-slate-900 bg-white px-3 py-2 rounded-lg border border-slate-200/80 shadow-2xs">
                                06:00 AM – 08:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-slate-400">Immediate SMS &amp; Portal Notification</span>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/70 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-[20px]">local_shipping</span>
                                <span class="text-xs font-heading font-bold text-slate-800">Assigned Courier Dispatch</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-slate-900 bg-white px-3 py-2 rounded-lg border border-slate-200/80 shadow-2xs">
                                04:00 PM – 06:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-slate-400">Daily Hyperlocal Logistics Truck Hub</span>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/70 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-[20px]">timer</span>
                                <span class="text-xs font-heading font-bold text-slate-800">Same-Day Harvest Cutoff</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-slate-900 bg-white px-3 py-2 rounded-lg border border-slate-200/80 shadow-2xs">
                                12:00 PM (Noon)
                            </div>
                            <span class="font-mono text-[10px] text-slate-400">Orders after 12 PM ship next dawn</span>
                        </div>
                    </div>
                </div>

                <!-- Sticky Bottom Action Bar -->
                <div x-show="unsavedChanges" x-transition class="sticky bottom-6 z-30 bg-slate-900 text-white rounded-2xl p-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-slate-800 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">published_with_changes</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading text-sm font-bold text-white">Unsaved Profile Changes Detected</span>
                            <span class="text-xs text-slate-300">Modifications to store identity and dispatch schedule are pending save.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <button type="button" @click="location.reload()" class="h-10 px-4 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-heading font-semibold transition-colors">
                            Discard
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 text-slate-950 font-heading text-xs font-bold shadow-xs transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Save Profile &amp; Settings</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

@push('scripts')
<script>
function previewProfileMedia(input, type) {
    if (!input.files || !input.files[0]) return;
    const file = input.files[0];

    if (file.size > 10 * 1024 * 1024) {
        if (window.showToast) {
            window.showToast('Selected image exceeds the 10MB limit.', 'error');
        } else {
            alert('Selected image exceeds the 10MB limit.');
        }
        input.value = '';
        return;
    }

    const objectUrl = URL.createObjectURL(file);

    if (type === 'banner') {
        const bannerImg = document.getElementById('banner-preview-img');
        const placeholder = document.getElementById('banner-placeholder');
        if (bannerImg) {
            bannerImg.src = objectUrl;
            bannerImg.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
    } else if (type === 'logo') {
        const logoImg = document.getElementById('logo-preview-img');
        const placeholder = document.getElementById('logo-placeholder');
        if (logoImg) {
            logoImg.src = objectUrl;
            logoImg.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
    }
}
</script>
@endpush
@endsection
