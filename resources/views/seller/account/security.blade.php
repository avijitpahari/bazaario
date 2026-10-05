@extends('layouts.seller')

@section('title', 'Security & Credentials — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    currPass: '',
    newPass: '',
    confPass: '',
    showCurr: false,
    showNew: false,
    showConf: false,
    strengthLabel: 'Weak (0%)',
    strengthLevel: 0,
    hasMinLen: false,
    hasUpper: false,
    hasNumber: false,
    hasSymbol: false,
    evaluateStrength() {
        const val = this.newPass;
        this.hasMinLen = val.length >= 8;
        this.hasUpper = /[A-Z]/.test(val);
        this.hasNumber = /[0-9]/.test(val);
        this.hasSymbol = /[^A-Za-z0-9]/.test(val);

        let score = 0;
        if (this.hasMinLen) score++;
        if (this.hasUpper) score++;
        if (this.hasNumber) score++;
        if (this.hasSymbol) score++;

        this.strengthLevel = score;
        if (score === 0) this.strengthLabel = 'Weak (0%)';
        else if (score <= 2) this.strengthLabel = 'Medium (50%)';
        else if (score === 3) this.strengthLabel = 'Strong (75%)';
        else if (score === 4) this.strengthLabel = 'Enterprise (100%)';
    }
}">

    <!-- Top Breadcrumb -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Security &amp; Password</span>
                </div>
                <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Security &amp; Access Credentials</h1>
                <p class="text-sm text-brand-muted">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.account.profile') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-sm font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Back to Profile</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Navigation -->
        <aside class="lg:col-span-3 flex flex-col gap-6 sticky top-24">
            <div class="bg-white rounded-[14px] p-3 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] text-on-surface-variant hover:bg-surface-container-low transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px]">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3 py-2.5 rounded-[10px] bg-secondary-container/30 text-on-secondary-container font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-secondary">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-1.5 py-0.5 rounded-[4px] bg-brand-green/20 text-brand-green font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>
        </aside>

        <!-- Right Content Area -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <div class="bg-white rounded-[14px] p-6 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-outline">Section 3.1</span>
                    <h2 class="font-heading text-xl font-bold text-primary">Account Security &amp; Access Credentials</h2>
                    <p class="text-sm text-brand-muted">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
                </div>

                <form method="POST" action="{{ route('seller.account.security.update') }}" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')

                    <!-- Password Inputs (3 columns) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Current Password</label>
                            <div class="relative flex items-center">
                                <input :type="showCurr ? 'text' : 'password'" name="current_password" x-model="currPass" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showCurr = !showCurr" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showCurr ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('current_password') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showNew ? 'text' : 'password'" name="password" x-model="newPass" @input="evaluateStrength()" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showNew = !showNew" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showNew ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-primary">Confirm New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showConf ? 'text' : 'password'" name="password_confirmation" x-model="confPass" class="w-full h-11 px-4 pr-10 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                                <button type="button" @click="showConf = !showConf" class="absolute right-3 text-brand-muted hover:text-primary">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showConf ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic 4-Tier Complexity Meter -->
                    <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-heading font-bold text-primary">Password Complexity Score</span>
                            <span class="font-mono text-xs font-bold" :class="{
                                'text-error': strengthLevel <= 1,
                                'text-brand-amber': strengthLevel === 2,
                                'text-brand-green': strengthLevel >= 3
                            }" x-text="strengthLabel">Weak (0%)</span>
                        </div>

                        <!-- 4-bar indicator -->
                        <div class="grid grid-cols-4 gap-2">
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 1 ? (strengthLevel === 1 ? 'bg-error' : (strengthLevel === 2 ? 'bg-brand-amber' : 'bg-brand-green')) : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 2 ? (strengthLevel === 2 ? 'bg-brand-amber' : 'bg-brand-green') : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 3 ? 'bg-brand-green' : 'bg-surface-container-high'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 4 ? 'bg-brand-green' : 'bg-surface-container-high'"></div>
                        </div>

                        <!-- Checklist -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 font-mono text-[11px]">
                            <div class="flex items-center gap-1.5" :class="hasMinLen ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasMinLen ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Minimum 8 characters</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasUpper ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasUpper ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains uppercase letter</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasNumber ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasNumber ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains numeral (0-9)</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasSymbol ? 'text-brand-green' : 'text-brand-muted'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasSymbol ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains special symbol (!@#$%^&amp;*)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="h-11 px-6 rounded-[12px] bg-primary text-white hover:bg-slate-800 font-heading text-xs font-bold transition-all shadow-md flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>

                <!-- 2FA Section -->
                <div class="p-4 rounded-[14px] bg-surface-container-low border border-[#E2DFD7]/60 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-[10px] bg-brand-green/20 text-brand-green flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">phonelink_lock</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-sm font-bold text-primary">Two-Factor Authentication (2FA)</span>
                                <span class="px-2 py-0.5 rounded-[4px] bg-brand-green text-white font-mono text-[10px] font-bold">ENFORCED</span>
                            </div>
                            <span class="text-xs text-brand-muted mt-0.5">Authenticator App (TOTP) + SMS fallback. Mandatory for escrow payout releases.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="h-9 px-4 rounded-[10px] bg-white text-primary text-xs font-heading font-medium border border-[#E2DFD7] hover:bg-surface-container transition-all">
                            Rotate Recovery Keys
                        </button>
                    </div>
                </div>

                <!-- Audit info -->
                <div class="flex items-center justify-between px-4 py-3 rounded-[10px] bg-surface-container-low font-mono text-xs text-brand-muted border border-[#E2DFD7]/40">
                    <span>Password last updated: Active session secured</span>
                    <span class="font-bold text-primary">Audit Log Compliant</span>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
