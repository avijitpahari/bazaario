@extends('layouts.admin')

@section('title', 'Platform Mode & Architecture Settings — Bazaario HQ')
@section('page-title', 'Platform Mode & Commission Architecture')

@section('content')
<div class="space-y-8" x-data="{
    platformMode: 'multi',
    commissionRate: 8.5,
    vipCommissionRate: 6.5,
    escrowDays: 7,
    maxCodLimit: 1000,
    codEnabled: true,
    maintenanceMode: false,
    savedToast: false
}">

    <!-- Toast Notification -->
    <div x-show="savedToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-slate-950 px-5 py-3.5 rounded-2xl shadow-2xl font-bold text-xs flex items-center gap-2.5">
        <i class="fa-solid fa-sliders text-base"></i> Platform operating rules applied site-wide immediately!
    </div>

    <!-- 1. Platform Mode Architecture Switcher (Section 4.7 & 8.3) -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
        <div>
            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 px-2.5 py-0.5 rounded-full font-mono">Core Marketplace Engine</span>
            <h2 class="text-xl font-bold font-heading text-white mt-2">Platform Operating Architecture</h2>
            <p class="text-xs text-slate-400 mt-1">
                Toggle between an open multi-vendor bazaar or a dedicated single-brand direct storefront without data loss.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            
            <!-- Option 1: Multi-Vendor Marketplace -->
            <label @click="platformMode = 'multi'" :class="platformMode === 'multi' ? 'border-indigo-500 bg-indigo-950/40 ring-2 ring-indigo-500/20' : 'border-slate-800 bg-slate-950/60'" class="p-6 rounded-3xl border cursor-pointer transition flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <input type="radio" name="modeSelect" value="multi" checked class="text-indigo-600 focus:ring-indigo-500">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold font-heading text-white">Multi-Vendor Marketplace</h3>
                        <span class="text-[10px] font-bold px-2 py-0.2 rounded bg-emerald-500/20 text-emerald-400">Active</span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Enables independent seller registration, merchant onboarding wizards, distinct stall storefronts (`/stall/{slug}`), Trust Score badges, and multi-seller split packages in checkout.
                    </p>
                </div>
                <div class="text-[11px] text-emerald-400 font-bold flex items-center gap-1.5 pt-2 border-t border-slate-800">
                    <i class="fa-solid fa-check"></i> 124 Independent Merchant Stalls Enabled
                </div>
            </label>

            <!-- Option 2: Single-Vendor Flagship Store -->
            <label @click="platformMode = 'single'" :class="platformMode === 'single' ? 'border-amber-500 bg-amber-950/40 ring-2 ring-amber-500/20' : 'border-slate-800 bg-slate-950/60'" class="p-6 rounded-3xl border cursor-pointer transition flex flex-col justify-between space-y-4">
                <div class="flex items-center justify-between">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <input type="radio" name="modeSelect" value="single" class="text-amber-600 focus:ring-amber-500">
                </div>
                <div>
                    <h3 class="text-base font-bold font-heading text-white">Single-Vendor Direct Store</h3>
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                        Hides seller onboarding links, removes seller badges from product cards, and consolidates cart and checkout into a direct 1-brand purchase flow.
                    </p>
                </div>
                <div class="text-[11px] text-amber-400 font-bold flex items-center gap-1.5 pt-2 border-t border-slate-800">
                    <i class="fa-solid fa-layer-group"></i> Single-Brand Direct Sales
                </div>
            </label>

        </div>
    </div>

    <!-- 2. Commission Rate & Escrow Engine (Section 4.7) -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 text-xs text-slate-300">
        <div>
            <h3 class="text-base font-bold font-heading text-white">Platform Commission & Escrow Payout Rules</h3>
            <p class="text-xs text-slate-400 mt-1">Configure automated take-rates, escrow hold durations for buyer returns, and COD thresholds.</p>
        </div>

        <form @submit.prevent="savedToast = true; setTimeout(() => savedToast = false, 3500)" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Default Standard Commission Rate (%)</label>
                    <div class="relative">
                        <input type="number" step="0.1" x-model="commissionRate" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-bold text-sm focus:outline-none focus:border-indigo-500 font-mono">
                        <span class="absolute right-4 top-2.5 text-xs font-bold text-slate-500">%</span>
                    </div>
                    <span class="text-[10px] text-slate-500">Applied to all newly registered merchant stalls</span>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">High-Volume VIP Tier Rate (%)</label>
                    <div class="relative">
                        <input type="number" step="0.1" x-model="vipCommissionRate" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-bold text-sm focus:outline-none focus:border-indigo-500 font-mono">
                        <span class="absolute right-4 top-2.5 text-xs font-bold text-slate-500">%</span>
                    </div>
                    <span class="text-[10px] text-slate-500">For stalls with &gt; $25,000 monthly GMV</span>
                </div>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-200">Escrow Hold Period (Days)</label>
                    <div class="relative">
                        <input type="number" x-model="escrowDays" class="w-full px-4 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white font-bold text-sm focus:outline-none focus:border-indigo-500 font-mono">
                        <span class="absolute right-4 top-2.5 text-xs font-bold text-slate-500">Days</span>
                    </div>
                    <span class="text-[10px] text-slate-500">Funds held after delivery for return window</span>
                </div>
            </div>

            <!-- Payment Gateway & COD Controls -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <h4 class="font-bold uppercase tracking-wider text-slate-400">Payment & Operational Toggles</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center justify-between p-4 rounded-2xl bg-slate-950 border border-slate-800 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <span class="font-bold text-white block">Cash on Delivery (COD)</span>
                                <span class="text-[10px] text-slate-500">Doorstep cash collection active for all stalls</span>
                            </div>
                        </div>
                        <input type="checkbox" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                    </label>

                    <label class="flex items-center justify-between p-4 rounded-2xl bg-slate-950 border border-slate-800 cursor-pointer">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </div>
                            <div>
                                <span class="font-bold text-white block">Maintenance Mode</span>
                                <span class="text-[10px] text-slate-500">Displays `/maintenance` splash screen to public</span>
                            </div>
                        </div>
                        <input type="checkbox" x-model="maintenanceMode" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 transition">
                    Save Platform Architecture & Rates
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
