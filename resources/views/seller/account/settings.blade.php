@extends('layouts.seller')

@section('title', 'Store Settings & Preferences — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    autoHidePerishable: true,
    defaultThreshold: 10,
    smsAlerts: true,
    emailDigest: true,
    auctionOutbidAlert: true,
    settlementCycle: 't2'
}">

    <!-- Top Breadcrumb & Header Card -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-1">
                <div class="flex items-center gap-2">
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Seller Center</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-muted uppercase tracking-wider">Account</span>
                    <span class="text-outline-variant font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Store Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Store Settings &amp; Preferences</h1>
                    <span class="px-2 py-0.5 rounded-[6px] bg-surface-container text-on-surface font-mono text-xs font-semibold">
                        ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-brand-muted">Configure your operational defaults, inventory automation, alert channels, and settlement preferences.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.dashboard') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-xs font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Dashboard</span>
                </a>
                <button type="button" @click="$refs.settingsForm.submit()" class="h-11 px-5 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-xs font-heading font-bold shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span>Save Preferences</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Settings Form -->
    <form x-ref="settingsForm" method="POST" action="{{ route('seller.account.settings.update') }}" class="flex flex-col gap-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- Left Column: Operations & Inventory Automation (7 Cols) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                
                <!-- Inventory & Perishable Automation Card -->
                <div class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm border border-surface-container-highest/60">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-container-highest/60 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-[22px]">inventory</span>
                            <div>
                                <h2 class="font-heading text-base font-bold text-on-surface">Inventory &amp; Freshness Automation</h2>
                                <p class="text-xs text-on-surface-variant">Automated handling for perishable items and low-stock telemetry.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 text-xs font-sans">
                        <!-- Auto-hide perishable toggle -->
                        <div class="p-4 rounded-xl bg-surface-container-low flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-on-surface">Auto-Hide Expired Perishable Listings</p>
                                <p class="text-on-surface-variant mt-0.5">When harvest expiry window elapses, automatically de-list item from customer discovery to maintain high trust scores.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="auto_hide_perishable" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>

                        <!-- Low stock default threshold -->
                        <div class="p-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-on-surface">Default Low Stock Alert Threshold</p>
                                <p class="text-on-surface-variant mt-0.5">Threshold below which items trigger dashboard alerts and restock reminders.</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <input type="number" name="default_low_stock_threshold" value="10" min="1" max="100" class="w-20 px-3 py-1.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest text-on-surface font-mono text-center font-bold outline-none focus:border-brand-amber">
                                <span class="text-on-surface-variant font-mono">Units</span>
                            </div>
                        </div>

                        <!-- Preferred default unit type -->
                        <div class="p-4 rounded-xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-on-surface">Primary Product Unit Type</p>
                                <p class="text-on-surface-variant mt-0.5">Pre-selects standard unit when creating new catalog listings.</p>
                            </div>
                            <select name="default_unit_type" class="px-3 py-1.5 rounded-lg border border-surface-container-highest bg-surface-container-lowest text-on-surface font-sans text-xs outline-none focus:border-brand-amber">
                                <option value="kg" selected>Kilograms (kg)</option>
                                <option value="dozen">Dozen (dozen)</option>
                                <option value="bundle">Bundle (bundle)</option>
                                <option value="litre">Litres (litre)</option>
                                <option value="pcs">Pieces (pcs)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notifications & Alert Channels Card -->
                <div class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm border border-surface-container-highest/60">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-container-highest/60 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-[22px]">notifications_active</span>
                            <div>
                                <h2 class="font-heading text-base font-bold text-on-surface">Notification Channels &amp; Alerts</h2>
                                <p class="text-xs text-on-surface-variant">Control SMS, email, and push dispatch alerts for store events.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 text-xs font-sans">
                        <div class="p-3.5 rounded-xl bg-surface-container-low flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-on-surface">SMS Order Dispatch Alerts</p>
                                <p class="text-on-surface-variant mt-0.5">Receive instant OTP-backed SMS when a courier is dispatched for pickup.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_sms_orders" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-low flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-on-surface">Live Auction Outbid &amp; Hammer Alerts</p>
                                <p class="text-on-surface-variant mt-0.5">Instant browser alerts whenever new competitive bids arrive on wholesale lots.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_auction_bids" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-container-low flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-on-surface">Daily Escrow Settlement Email Digest</p>
                                <p class="text-on-surface-variant mt-0.5">Itemized statement of funds credited, commission deducted, and TDS withholdings.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_payout_digest" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-surface-container-high peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-secondary"></div>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Payouts & Compliance Overview (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                
                <!-- Escrow & Banking Account Card -->
                <div class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm border border-surface-container-highest/60">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-container-highest/60 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-secondary text-[22px]">account_balance</span>
                            <div>
                                <h2 class="font-heading text-base font-bold text-on-surface">Settlement Banking Desk</h2>
                                <p class="text-xs text-on-surface-variant">Verified destination for escrow release funds.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3.5 text-xs font-sans">
                        <div class="p-4 rounded-xl bg-surface-container-low border border-surface-container-highest/60 flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[11px] uppercase text-on-surface-variant font-semibold">Bank Status</span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-surface-container text-on-tertiary-container font-mono text-[10px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-on-tertiary-container"></span>
                                    VERIFIED
                                </span>
                            </div>
                            <p class="font-heading font-bold text-sm text-on-surface">
                                {{ $profile?->bank_name ?? 'HDFC Bank Ltd.' }}
                            </p>
                            <p class="font-mono text-xs text-on-surface-variant">
                                Account: •••• {{ $profile?->bank_account_number ? substr($profile->bank_account_number, -4) : '4092' }}
                            </p>
                            <p class="font-mono text-[11px] text-on-surface-variant">
                                IFSC: {{ $profile?->ifsc_code ?? 'HDFC0001842' }}
                            </p>
                        </div>

                        <!-- Settlement Cycle Options -->
                        <div class="pt-2">
                            <label class="block font-semibold text-on-surface mb-2">Escrow Clearance Cycle</label>
                            <div class="grid grid-cols-2 gap-2.5">
                                <label class="p-3 rounded-xl border border-secondary bg-secondary-container/20 flex flex-col gap-1 cursor-pointer">
                                    <input type="radio" name="settlement_cycle" value="t2" class="sr-only" checked>
                                    <span class="font-heading font-bold text-xs text-secondary">T+2 Standard</span>
                                    <span class="text-[11px] text-on-surface-variant">Automated NEFT batches</span>
                                </label>
                                <label class="p-3 rounded-xl border border-surface-container-highest bg-surface-container-low flex flex-col gap-1 cursor-pointer opacity-70">
                                    <input type="radio" name="settlement_cycle" value="t0" class="sr-only" disabled>
                                    <span class="font-heading font-bold text-xs text-on-surface">T+0 Instant</span>
                                    <span class="text-[10px] text-brand-amber font-mono font-bold">Tier 1 Prime Only</span>
                                </label>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-surface-container flex items-center gap-2 mt-2">
                            <span class="material-symbols-outlined text-[18px] text-secondary">verified_user</span>
                            <p class="text-[11px] text-on-surface-variant">Banking credentials are encrypted using AES-256 for RBI settlement compliance.</p>
                        </div>
                    </div>
                </div>

                <!-- Fast Actions Strip -->
                <div class="p-4 bg-surface-container-low rounded-xl border border-surface-container-highest/60 flex items-center justify-between text-xs">
                    <span class="text-on-surface-variant">Need to modify GSTIN or PAN?</span>
                    <a href="{{ route('seller.account.profile') }}" class="font-semibold text-secondary hover:underline flex items-center gap-1">
                        <span>Edit Shop Profile</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
