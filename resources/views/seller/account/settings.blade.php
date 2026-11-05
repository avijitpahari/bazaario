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
                <div class="bg-surface-container-lowest rounded-[14px] p-6 shadow-sm border border-surface-container-highest/60 flex flex-col gap-5" x-data="{ editingBank: {{ $profile?->bank_account_number ? 'false' : 'true' }} }">
                    <div class="flex items-center justify-between pb-4 border-b border-surface-container-highest/60">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-300/40 text-amber-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">account_balance</span>
                            </div>
                            <div>
                                <h2 class="font-heading text-base font-bold text-on-surface">Settlement Banking Desk</h2>
                                <p class="text-xs text-on-surface-variant">Verified destination for escrow payouts &amp; auction settlements.</p>
                            </div>
                        </div>
                        @if($profile?->bank_account_number)
                            <button type="button" @click="editingBank = !editingBank" class="px-2.5 py-1 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-sans text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]" x-text="editingBank ? 'close' : 'edit'"></span>
                                <span x-text="editingBank ? 'Cancel' : 'Edit Bank'"></span>
                            </button>
                        @endif
                    </div>

                    <!-- Current Linked Bank Status Strip -->
                    <div class="p-4 rounded-xl {{ $profile?->bank_account_number ? 'bg-surface-container-low border border-surface-container-highest/60' : 'bg-amber-50/60 border border-amber-200/80 text-amber-950' }} flex flex-col gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] uppercase {{ $profile?->bank_account_number ? 'text-on-surface-variant' : 'text-amber-800' }} font-bold">Bank Connection Status</span>
                            @if($profile?->bank_account_number && $profile?->bank_doc_path)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Verified &amp; Active
                                </span>
                            @elseif($profile?->bank_account_number)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-300 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    Pending Proof
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                    Not Configured
                                </span>
                            @endif
                        </div>

                        @if($profile?->bank_account_number)
                            <div class="flex flex-col gap-0.5">
                                <p class="font-heading font-bold text-sm text-on-surface">
                                    {{ $profile?->bank_name ?? 'HDFC Bank Ltd. (Verified)' }}
                                </p>
                                <div class="flex items-center gap-3 font-mono text-xs text-on-surface-variant mt-0.5">
                                    <span>A/C: •••• {{ substr($profile->bank_account_number, -4) }}</span>
                                    <span>•</span>
                                    <span>IFSC: {{ $profile->bank_ifsc ?? 'HDFC0001234' }}</span>
                                </div>
                                @if($profile->bank_doc_path)
                                    <div class="mt-1 flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                        <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                        <span>Passbook / Cheque proof verified on file</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-xs text-amber-900 leading-relaxed">
                                No bank account has been connected yet. Order revenues and auction payouts will accrue in escrow until bank details are linked.
                            </p>
                        @endif
                    </div>

                    <!-- Editable Bank Account Fields -->
                    <div x-show="editingBank" class="flex flex-col gap-3.5 pt-1">
                        <div class="flex items-center justify-between">
                            <span class="font-sans text-xs font-bold text-on-surface">Bank Settlement Details</span>
                            
                            <!-- Test Auto-Fill Helper -->
                            <button type="button" 
                                    @click="
                                        $refs.bankAccountInput.value = '918010023456789';
                                        $refs.bankIfscInput.value = 'HDFC0001234';
                                        $refs.bankAccountInput.dispatchEvent(new Event('input'));
                                        $refs.bankIfscInput.dispatchEvent(new Event('input'));
                                    "
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200 font-mono text-[11px] font-bold transition-colors cursor-pointer"
                                    title="Auto-fill with test bank account and IFSC code">
                                <span class="material-symbols-outlined text-[14px] text-amber-700">science</span>
                                <span>Auto-Fill Test Bank</span>
                            </button>
                        </div>

                        <div class="flex flex-col gap-3 text-xs">
                            <div>
                                <label class="block font-medium text-on-surface mb-1">Bank Account Number *</label>
                                <input type="text" 
                                       x-ref="bankAccountInput"
                                       name="bank_account_number" 
                                       value="{{ old('bank_account_number', $profile?->bank_account_number) }}" 
                                       placeholder="e.g. 918010023456789"
                                       maxlength="50"
                                       class="w-full px-3.5 py-2.5 bg-surface-container-low border border-surface-container-highest/80 rounded-xl font-mono text-xs text-on-surface focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                            </div>

                            <div>
                                <label class="block font-medium text-on-surface mb-1">Bank IFSC Code *</label>
                                <input type="text" 
                                       x-ref="bankIfscInput"
                                       name="bank_ifsc" 
                                       value="{{ old('bank_ifsc', $profile?->bank_ifsc) }}" 
                                       placeholder="e.g. HDFC0001234"
                                       maxlength="20"
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                                       class="w-full px-3.5 py-2.5 bg-surface-container-low border border-surface-container-highest/80 rounded-xl font-mono uppercase text-xs text-on-surface focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all">
                            </div>

                            <div>
                                <label class="block font-medium text-on-surface mb-1">Passbook or Cancelled Cheque (Proof)</label>
                                <input type="file" 
                                       name="bank_doc" 
                                       accept=".pdf,.png,.jpg,.jpeg,.webp"
                                       class="w-full text-xs text-on-surface-variant file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-surface-container-high file:text-on-surface hover:file:bg-surface-container-highest border border-surface-container-highest/80 rounded-xl bg-surface-container-low p-1 cursor-pointer">
                                <span class="text-[10px] text-on-surface-variant mt-0.5 block">Accepted: PDF, PNG, JPG (Max 5MB).</span>
                            </div>
                        </div>

                        <!-- One-Click Instant Sandbox Bypass Action -->
                        <div class="mt-2 pt-3 border-t border-surface-container-highest/60 flex flex-wrap items-center justify-between gap-2.5">
                            <span class="text-[11px] text-on-surface-variant font-medium">Testing in sandbox or staging?</span>
                            <button type="submit" 
                                    name="action" 
                                    value="test_bypass" 
                                    class="h-9 px-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-heading text-xs font-bold inline-flex items-center gap-1.5 shadow-2xs hover:shadow-xs transition-all active:scale-95 cursor-pointer">
                                <span class="material-symbols-outlined text-[16px]">flash_on</span>
                                <span>⚡ Test Bypass &amp; Auto-Verify Bank</span>
                            </button>
                        </div>
                    </div>

                    <!-- Settlement Cycle Options -->
                    <div class="pt-2 border-t border-surface-container-highest/60">
                        <label class="block font-semibold text-on-surface text-xs mb-2">Escrow Clearance Cycle</label>
                        <div class="grid grid-cols-2 gap-2.5 text-xs">
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

                    <div class="p-3 rounded-xl bg-surface-container flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-secondary">verified_user</span>
                        <p class="text-[11px] text-on-surface-variant">Banking credentials are encrypted using AES-256 for RBI settlement compliance.</p>
                    </div>
                </div>

                <!-- Fast Actions Strip -->
                <div class="p-4 bg-surface-container-low rounded-xl border border-surface-container-highest/60 flex items-center justify-between text-xs">
                    <span class="text-on-surface-variant">Need to modify GSTIN or PAN?</span>
                    <a href="{{ route('seller.account.profile') }}" class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100/90 text-amber-900 font-bold text-xs border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                        <span>Edit Shop Profile</span>
                        <span class="material-symbols-outlined text-[14px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-1">arrow_forward</span>
                    </a>
                </div>

            </div>

        </div>
    </form>

</div>
@endsection
