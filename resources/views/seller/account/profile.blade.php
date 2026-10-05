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
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Shop Profile &amp; Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Shop Profile &amp; Settings</h1>
                    <span class="px-2 py-0.5 rounded-[6px] bg-secondary-container/40 text-on-secondary-container font-mono text-xs font-semibold">
                        ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-brand-muted">Manage your verified business identity, farm origin coordinates, dispatch operating hours, and authentication credentials.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.dashboard') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Dashboard</span>
                </a>
                <button type="button" @click="$refs.profileForm.submit()" class="h-11 px-5 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-sm font-heading font-bold shadow-sm flex items-center gap-2">
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
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                    <span class="w-2 h-2 rounded-full bg-brand-amber"></span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>

            <!-- Trust & Verification Widget -->
            <div class="bg-white rounded-[14px] p-5 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="relative w-12 h-12 rounded-[14px] overflow-hidden bg-surface-container shrink-0 shadow-sm border border-[#E2DFD7]/60">
                        @if($profile?->logo_path)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $profile->logo_path) }}" alt="{{ $profile->shop_name }}">
                        @else
                            <div class="w-full h-full bg-primary flex items-center justify-center text-brand-amber font-heading font-bold text-xl">
                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-brand-green rounded-full flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[10px]">check</span>
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h2 class="font-heading text-sm font-bold text-primary truncate">{{ $profile?->shop_name ?? 'My Farm Store' }}</h2>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold inline-flex items-center gap-0.5">
                                <span class="material-symbols-outlined text-[11px]">verified</span> Verified {{ $profile?->seller_type ?? 'Farmer' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-[10px] bg-surface-container-low flex flex-col gap-2 font-mono text-xs">
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>Merchant Status</span>
                        <span class="font-bold text-brand-green uppercase">{{ $profile?->status ?? 'Approved' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>Commission Tier</span>
                        <span class="text-primary font-bold">{{ $profile?->commission_rate ? $profile->commission_rate.'%' : '10.00%' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>FSSAI License</span>
                        <span class="text-primary font-mono text-[11px]">{{ $profile?->fssai_number ?? '#21524021000918' }}</span>
                    </div>
                </div>

                <!-- Trust Score Progress -->
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-heading font-semibold text-primary">Market Trust Index</span>
                        <span class="font-mono font-bold text-brand-amber">{{ number_format($profile?->trust_score ?? 94, 0) }}/100</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-surface-container overflow-hidden">
                        <div class="h-full bg-brand-amber rounded-full" style="width: {{ $profile?->trust_score ?? 94 }}%;"></div>
                    </div>
                    <span class="font-mono text-[10px] text-brand-muted">Eligible for 0% Escrow Hold and Instant Payouts</span>
                </div>

                <div class="p-3 rounded-[10px] bg-surface-container-high/60 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-brand-amber text-[18px] shrink-0 mt-0.5">help_center</span>
                    <div class="flex flex-col text-xs">
                        <span class="font-heading font-bold text-primary">Need Legal Updates?</span>
                        <span class="text-brand-muted text-[11px] leading-snug mt-0.5">GSTIN and legal filings require administrative compliance desk review.</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- RIGHT COLUMN: Settings Workspace (9 Cols) -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <!-- Subtab Navigation Bar -->
            <div class="bg-white p-1.5 rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('seller.account.profile') }}" class="px-4 py-2 rounded-[10px] bg-primary text-white font-heading text-xs font-semibold flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                    <span>1. Shop Profile &amp; Identity</span>
                </a>
                <a href="{{ route('seller.account.location') }}" class="px-4 py-2 rounded-[10px] text-on-surface-variant hover:bg-surface-container-high font-heading text-xs font-medium flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">pin_drop</span>
                    <span>2. Farm Location &amp; Coordinates</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="px-4 py-2 rounded-[10px] text-on-surface-variant hover:bg-surface-container-high font-heading text-xs font-medium flex items-center gap-2 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                    <span>3. Security &amp; Credentials</span>
                </a>
            </div>

            <form x-ref="profileForm" method="POST" action="{{ route('seller.account.profile.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6" @input="unsavedChanges = true">
                @csrf
                @method('PUT')

                <!-- SECTION 1.1: Visual Brand & Farm Photography -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-5">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.1</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Visual Brand &amp; Farm Photography</h2>
                        <p class="text-sm text-brand-muted">Store banner and verified orchard imagery shown to buyers in the wholesale catalog and live auction rooms.</p>
                    </div>

                    <!-- Cover Banner Preview with Logo Inset -->
                    <div class="relative w-full h-56 md:h-64 rounded-[14px] overflow-hidden bg-surface-container border border-[#E2DFD7]/60 shadow-inner group">
                        @if($profile?->banner_path)
                            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $profile->banner_path) }}" alt="Store Banner">
                        @else
                            <div class="w-full h-full bg-gradient-to-r from-primary via-slate-800 to-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-white/30 text-[72px]">landscape</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-primary/85 via-primary/25 to-transparent flex flex-col justify-end p-5 text-white">
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-[14px] overflow-hidden bg-white p-1 shadow-md shrink-0 border border-white/40">
                                        @if($profile?->logo_path)
                                            <img class="w-full h-full object-cover rounded-[10px]" src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo">
                                        @else
                                            <div class="w-full h-full bg-primary flex items-center justify-center text-brand-amber font-heading font-bold text-2xl rounded-[10px]">
                                                {{ strtoupper(substr($profile?->shop_name ?? 'B', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-heading text-lg font-bold text-white">{{ $profile?->shop_name ?? 'My Farm Store' }}</span>
                                        <span class="font-mono text-[11px] text-surface-container-high uppercase tracking-wider">PRIMARY STOREFRONT COVER • 2560 × 720 PX</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="h-10 px-4 rounded-[10px] bg-white/90 hover:bg-white text-primary text-xs font-heading font-bold backdrop-blur-sm transition-all shadow flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">photo_camera</span>
                                        <span>Replace Banner</span>
                                        <input type="file" name="banner_image" accept="image/*" class="hidden">
                                    </label>
                                    <label class="h-10 px-4 rounded-[10px] bg-white/90 hover:bg-white text-primary text-xs font-heading font-bold backdrop-blur-sm transition-all shadow flex items-center gap-1.5 cursor-pointer">
                                        <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                        <span>Change Logo</span>
                                        <input type="file" name="logo_image" accept="image/*" class="hidden">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Batch certification note -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-[10px] bg-brand-amber/20 flex items-center justify-center text-brand-amber shrink-0">
                                <span class="material-symbols-outlined text-[22px]">cloud_upload</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-sm font-heading font-bold text-primary">Upload Farm Photography &amp; APMC Certification Slips</span>
                                <span class="text-xs text-brand-muted">PNG, JPG, or WEBP up to 5MB each. High resolution recommended for buyer trust.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.2: Core Business Identity & Signatory Details -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.2</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Core Business Identity &amp; Signatory Details</h2>
                        <p class="text-sm text-brand-muted">Official marketplace identity tied to legal GSTIN #{{ $profile?->gstin ?? '27AABCG1294F1Z8' }}.</p>
                    </div>

                    <!-- Gated Role Notice -->
                    <div class="p-4 rounded-[14px] bg-brand-amber/10 border border-brand-amber/20 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-[10px] bg-brand-amber text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[20px]">psychiatry</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-heading text-sm font-bold text-primary">Registered Role: {{ $profile?->seller_type ?? 'Farmer' }} &amp; Cultivator</span>
                                    <span class="px-2 py-0.5 rounded-[4px] bg-brand-green text-white font-mono text-[10px] font-bold">VERIFIED PRODUCER</span>
                                </div>
                                <p class="text-xs text-brand-muted mt-1">
                                    Changing your merchant classification requires documentary review by Bazaario Agricultural Compliance. Existing auction bids and active harvests are locked during audit.
                                </p>
                            </div>
                        </div>
                        <button type="button" class="h-9 px-4 rounded-[10px] bg-white hover:bg-surface-container-high text-primary text-xs font-heading font-semibold transition-all shadow-sm shrink-0">
                            Request Change
                        </button>
                    </div>

                    <!-- Form Input Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Farm / Shop Commercial Name</span>
                                <span class="font-mono text-[10px] text-brand-muted">Public Storefront</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">store</span>
                                <input type="text" name="shop_name" value="{{ old('shop_name', $profile?->shop_name) }}" class="w-full h-11 pl-11 pr-4 bg-surface-container-lowest rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm transition-colors" required>
                            </div>
                            @error('shop_name') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Registered Signatory / Proprietor</span>
                                <span class="font-mono text-[10px] text-brand-green font-bold">KYC Matched</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">badge</span>
                                <input type="text" value="{{ $seller?->name }}" class="w-full h-11 pl-11 pr-4 bg-surface-container-low rounded-[12px] text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Primary Business Email</span>
                                <span class="px-1.5 py-0.5 rounded bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">VERIFIED</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">mail</span>
                                <input type="email" value="{{ $seller?->email }}" class="w-full h-11 pl-11 pr-10 bg-surface-container-low rounded-[12px] font-mono text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-brand-green text-[18px]">verified</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Primary Dispatch Phone</span>
                                <span class="px-1.5 py-0.5 rounded bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">OTP BOUND</span>
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3.5 text-brand-muted text-[20px]">call</span>
                                <input type="tel" value="{{ $seller?->phone ?? '+91 98765 43210' }}" class="w-full h-11 pl-11 pr-10 bg-surface-container-low rounded-[12px] font-mono text-sm text-brand-muted border border-[#E2DFD7] cursor-not-allowed outline-none shadow-sm" readonly>
                                <span class="absolute right-3 material-symbols-outlined text-brand-green text-[18px]">check_circle</span>
                            </div>
                        </div>

                        <div class="flex flex-col gap-1.5 md:col-span-2">
                            <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                                <span>Public Store &amp; Provenance Description</span>
                                <span class="font-mono text-[10px] text-brand-muted">Markdown Supported • <span x-text="charCount"></span>/500 Chars</span>
                            </label>
                            <textarea name="bio" rows="4" maxlength="500" @input="charCount = $el.value.length" class="w-full p-4 bg-surface-container-lowest rounded-[14px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm transition-colors resize-none leading-relaxed">{{ old('bio', $profile?->bio) }}</textarea>
                            @error('bio') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 1.3: Operating Hours & Logistics Dispatch SLA -->
                <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                    <div class="flex flex-col">
                        <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 1.3</span>
                        <h2 class="font-heading text-xl font-bold text-primary">Operating Hours &amp; Logistics Dispatch SLA</h2>
                        <p class="text-sm text-brand-muted">Configure order acceptance cut-offs and daily courier harvest pickup handovers.</p>
                    </div>

                    <!-- Operating Days Buttons -->
                    <div class="flex flex-col gap-2.5">
                        <span class="text-xs font-heading font-semibold text-primary">Active Operating Harvest Days</span>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach(['mon' => 'MON', 'tue' => 'TUE', 'wed' => 'WED', 'thu' => 'THU', 'fri' => 'FRI', 'sat' => 'SAT', 'sun' => 'SUN'] as $key => $lbl)
                                <button type="button" @click="toggleDay('{{ $key }}')" 
                                        :class="operatingDays.includes('{{ $key }}') ? 'bg-primary text-white border-primary' : 'bg-surface-container-low text-brand-muted border-[#E2DFD7]'" 
                                        class="h-12 rounded-[10px] font-mono text-xs font-bold flex flex-col items-center justify-center border transition-all">
                                    <span>{{ $lbl }}</span>
                                    <span class="text-[9px] uppercase font-normal" :class="operatingDays.includes('{{ $key }}') ? 'text-brand-green' : 'text-brand-muted'" x-text="operatingDays.includes('{{ $key }}') ? 'Active' : 'Off'"></span>
                                </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="operating_days" :value="JSON.stringify(operatingDays)">
                    </div>

                    <!-- Time Window Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">schedule</span>
                                <span class="text-xs font-heading font-bold text-primary">Order Acceptance Window</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                06:00 AM – 08:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Immediate SMS &amp; Portal Notification</span>
                        </div>

                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">local_shipping</span>
                                <span class="text-xs font-heading font-bold text-primary">Assigned Courier Dispatch</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                04:00 PM – 06:00 PM
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Daily Hyperlocal Logistics Truck Hub</span>
                        </div>

                        <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-brand-amber text-[20px]">timer</span>
                                <span class="text-xs font-heading font-bold text-primary">Same-Day Harvest Cutoff</span>
                            </div>
                            <div class="font-mono text-sm font-bold text-primary bg-white px-3 py-2 rounded-[8px] border border-[#E2DFD7]/60 shadow-sm">
                                12:00 PM (Noon)
                            </div>
                            <span class="font-mono text-[10px] text-brand-muted">Orders after 12 PM ship next dawn</span>
                        </div>
                    </div>
                </div>

                <!-- Sticky Bottom Action Bar -->
                <div x-show="unsavedChanges" x-transition class="sticky bottom-6 z-30 bg-primary text-white rounded-[14px] p-4 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4 border border-white/10 backdrop-blur-md">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-brand-amber text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">published_with_changes</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-heading text-sm font-bold text-white">Unsaved Profile Changes Detected</span>
                            <span class="text-xs text-white/70">Modifications to store identity and dispatch schedule are pending save.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                        <button type="button" @click="location.reload()" class="h-10 px-4 rounded-[10px] bg-white/10 hover:bg-white/20 text-white text-xs font-heading font-medium transition-colors">
                            Discard
                        </button>
                        <button type="submit" class="h-10 px-5 rounded-[10px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 font-heading text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">verified</span>
                            <span>Save Profile &amp; Settings</span>
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>
@endsection
