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
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Seller Center</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Account</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-amber-700 font-semibold uppercase tracking-wider">Security &amp; Password</span>
                </div>
                <h1 class="font-heading text-2xl lg:text-3xl text-slate-900 font-bold tracking-tight">Security &amp; Access Credentials</h1>
                <p class="text-sm text-slate-500">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.account.profile') }}" class="h-11 px-4 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200/80 transition-colors text-sm font-semibold flex items-center gap-2 border border-slate-200/80 shadow-2xs">
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
            <div class="bg-white rounded-2xl p-3 shadow-xs border border-slate-200/80 flex flex-col gap-1">
                <div class="px-3 py-2">
                    <span class="font-mono text-[10px] uppercase tracking-wider text-slate-400 font-bold">Configuration Suite</span>
                </div>
                <a href="{{ route('seller.account.profile') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">storefront</span>
                        <span class="text-sm font-heading">Shop Profile</span>
                    </div>
                </a>
                <a href="{{ route('seller.account.location') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100/70 font-heading font-semibold transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-slate-400">explore</span>
                        <span class="text-sm font-heading">Location &amp; Geofence</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">GPS OK</span>
                </a>
                <a href="{{ route('seller.account.security') }}" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-amber-500/10 text-amber-950 font-heading font-bold border border-amber-300/60 shadow-2xs transition-all">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-[20px] text-amber-600">shield</span>
                        <span class="text-sm font-heading">Security &amp; Password</span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200/60 font-mono text-[10px] font-bold">2FA On</span>
                </a>
            </div>
        </aside>

        <!-- Right Content Area -->
        <main class="lg:col-span-9 flex flex-col gap-6">

            <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-xs border border-slate-200/80 flex flex-col gap-6">
                <div class="flex flex-col">
                    <span class="font-mono text-[10px] uppercase tracking-widest text-slate-400">Section 3.1</span>
                    <h2 class="font-heading text-xl font-bold text-slate-900">Account Security &amp; Access Credentials</h2>
                    <p class="text-sm text-slate-500">Update your merchant authentication keys and manage 2-factor hardware/SMS authentications.</p>
                </div>

                <form method="POST" action="{{ route('seller.account.security.update') }}" class="flex flex-col gap-5">
                    @csrf
                    @method('PUT')

                    <!-- Password Inputs (3 columns) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800">Current Password</label>
                            <div class="relative flex items-center">
                                <input :type="showCurr ? 'text' : 'password'" name="current_password" x-model="currPass" class="w-full h-11 px-4 pr-10 bg-slate-50/60 hover:bg-white focus:bg-white rounded-xl font-mono text-sm text-slate-900 border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" required>
                                <button type="button" @click="showCurr = !showCurr" class="absolute right-3 text-slate-400 hover:text-slate-700">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showCurr ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('current_password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800">New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showNew ? 'text' : 'password'" name="password" x-model="newPass" @input="evaluateStrength()" class="w-full h-11 px-4 pr-10 bg-slate-50/60 hover:bg-white focus:bg-white rounded-xl font-mono text-sm text-slate-900 border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" required>
                                <button type="button" @click="showNew = !showNew" class="absolute right-3 text-slate-400 hover:text-slate-700">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showNew ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            @error('password') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-heading font-semibold text-slate-800">Confirm New Password</label>
                            <div class="relative flex items-center">
                                <input :type="showConf ? 'text' : 'password'" name="password_confirmation" x-model="confPass" class="w-full h-11 px-4 pr-10 bg-slate-50/60 hover:bg-white focus:bg-white rounded-xl font-mono text-sm text-slate-900 border border-slate-300 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none shadow-2xs transition-all" required>
                                <button type="button" @click="showConf = !showConf" class="absolute right-3 text-slate-400 hover:text-slate-700">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showConf ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic 4-Tier Complexity Meter -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-heading font-bold text-slate-800">Password Complexity Score</span>
                            <span class="font-mono text-xs font-bold" :class="{
                                'text-rose-600': strengthLevel <= 1,
                                'text-amber-600': strengthLevel === 2,
                                'text-emerald-700': strengthLevel >= 3
                            }" x-text="strengthLabel">Weak (0%)</span>
                        </div>

                        <!-- 4-bar indicator -->
                        <div class="grid grid-cols-4 gap-2">
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 1 ? (strengthLevel === 1 ? 'bg-rose-500' : (strengthLevel === 2 ? 'bg-amber-500' : 'bg-emerald-500')) : 'bg-slate-200'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 2 ? (strengthLevel === 2 ? 'bg-amber-500' : 'bg-emerald-500') : 'bg-slate-200'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 3 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                            <div class="h-2 rounded-full transition-colors" :class="strengthLevel >= 4 ? 'bg-emerald-500' : 'bg-slate-200'"></div>
                        </div>

                        <!-- Checklist -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1 font-mono text-[11px]">
                            <div class="flex items-center gap-1.5" :class="hasMinLen ? 'text-emerald-700' : 'text-slate-400'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasMinLen ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Minimum 8 characters</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasUpper ? 'text-emerald-700' : 'text-slate-400'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasUpper ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains uppercase letter</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasNumber ? 'text-emerald-700' : 'text-slate-400'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasNumber ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains numeral (0-9)</span>
                            </div>
                            <div class="flex items-center gap-1.5" :class="hasSymbol ? 'text-emerald-700' : 'text-slate-400'">
                                <span class="material-symbols-outlined text-[16px]" x-text="hasSymbol ? 'check_circle' : 'radio_button_unchecked'"></span>
                                <span>Contains special symbol (!@#$%^&amp;*)</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="h-11 px-6 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-heading text-xs font-bold transition-all shadow-xs flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">key</span>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>

                <!-- 2FA Section -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/60 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">phonelink_lock</span>
                        </div>
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-heading text-sm font-bold text-slate-900">Two-Factor Authentication (2FA)</span>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-mono text-[10px] font-bold border border-emerald-200">ENFORCED</span>
                            </div>
                            <span class="text-xs text-slate-500 mt-0.5">Authenticator App (TOTP) + SMS fallback. Mandatory for escrow payout releases.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="h-9 px-4 rounded-xl bg-white text-slate-700 text-xs font-heading font-medium border border-slate-300 hover:bg-slate-50 transition-all shadow-2xs">
                            Rotate Recovery Keys
                        </button>
                    </div>
                </div>

                <!-- Audit info -->
                <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-slate-50 font-mono text-xs text-slate-500 border border-slate-200/60">
                    <span>Password last updated: Active session secured</span>
                    <span class="font-bold text-emerald-700">Audit Log Compliant</span>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
