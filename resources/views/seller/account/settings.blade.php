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
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Seller Center</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-slate-500 uppercase tracking-wider">Account</span>
                    <span class="text-slate-300 font-mono text-[11px]">/</span>
                    <span class="font-mono text-[11px] text-amber-700 font-semibold uppercase tracking-wider">Store Settings</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-slate-900 font-bold tracking-tight">Store Settings &amp; Preferences</h1>
                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-xs font-semibold border border-slate-200/80">
                        ID: #BZ-SLR-{{ str_pad($profile?->id ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <p class="text-sm text-slate-500">Configure your operational defaults, inventory automation, alert channels, and settlement preferences.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('seller.dashboard') }}" class="h-11 px-4 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/90 transition-colors text-xs font-semibold flex items-center gap-2 shadow-2xs">
                    <span class="material-symbols-outlined text-[18px]">undo</span>
                    <span>Dashboard</span>
                </a>
                <button type="button" @click="$refs.settingsForm.submit()" class="h-11 px-5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 text-slate-950 active:scale-95 transition-all text-xs font-heading font-bold shadow-xs flex items-center gap-2 cursor-pointer">
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
                <div class="bg-white rounded-2xl p-6 shadow-2xs border border-slate-200/80">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-amber-600 text-[22px]">inventory</span>
                            <div>
                                <h2 class="font-heading text-base font-bold text-slate-900">Inventory &amp; Freshness Automation</h2>
                                <p class="text-xs text-slate-500">Automated handling for perishable items and low-stock telemetry.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 text-xs font-sans">
                        <!-- Auto-hide perishable toggle -->
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-slate-900">Auto-Hide Expired Perishable Listings</p>
                                <p class="text-slate-500 mt-0.5">When harvest expiry window elapses, automatically de-list item from customer discovery to maintain high trust scores.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="auto_hide_perishable" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        <!-- Low stock default threshold -->
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">Default Low Stock Alert Threshold</p>
                                <p class="text-slate-500 mt-0.5">Threshold below which items trigger dashboard alerts and restock reminders.</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <input type="number" name="default_low_stock_threshold" value="10" min="1" max="100" class="w-20 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-900 font-mono text-center font-bold outline-none focus:border-amber-500 shadow-2xs">
                                <span class="text-slate-500 font-mono">Units</span>
                            </div>
                        </div>

                        <!-- Preferred default unit type -->
                        <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-semibold text-slate-900">Primary Product Unit Type</p>
                                <p class="text-slate-500 mt-0.5">Pre-selects standard unit when creating new catalog listings.</p>
                            </div>
                            <select name="default_unit_type" class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-900 font-sans text-xs outline-none focus:border-amber-500 shadow-2xs">
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
                <div class="bg-white rounded-2xl p-6 shadow-2xs border border-slate-200/80">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-amber-600 text-[22px]">notifications_active</span>
                            <div>
                                <h2 class="font-heading text-base font-bold text-slate-900">Notification Channels &amp; Alerts</h2>
                                <p class="text-xs text-slate-500">Control SMS, email, and push dispatch alerts for store events.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 text-xs font-sans">
                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">SMS Order Dispatch Alerts</p>
                                <p class="text-slate-500 mt-0.5">Receive instant OTP-backed SMS when a courier is dispatched for pickup.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_sms_orders" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">Live Auction Outbid &amp; Hammer Alerts</p>
                                <p class="text-slate-500 mt-0.5">Instant browser alerts whenever new competitive bids arrive on wholesale lots.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_auction_bids" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>

                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-slate-900">Daily Escrow Settlement Email Digest</p>
                                <p class="text-slate-500 mt-0.5">Itemized statement of funds credited, commission deducted, and TDS withholdings.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                                <input type="checkbox" name="notify_payout_digest" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Payouts & Compliance Overview (5 Cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6">
                
                <!-- Escrow & Banking Account Card -->
                <div class="bg-white rounded-2xl p-6 shadow-2xs border border-slate-200/80 flex flex-col gap-5" x-data="{ editingBank: {{ $profile?->bank_account_number ? 'false' : 'true' }} }">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-500/15 border border-amber-300/40 text-amber-700 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">account_balance</span>
                            </div>
                            <div>
                                <h2 class="font-heading text-base font-bold text-slate-900">Settlement Banking Desk</h2>
                                <p class="text-xs text-slate-500">Verified destination for escrow payouts &amp; auction settlements.</p>
                            </div>
                        </div>
                        @if($profile?->bank_account_number)
                            <button type="button" @click="editingBank = !editingBank" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-sans text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer">
                                <span class="material-symbols-outlined text-[14px]" x-text="editingBank ? 'close' : 'edit'"></span>
                                <span x-text="editingBank ? 'Cancel' : 'Edit Bank'"></span>
                            </button>
                        @endif
                    </div>

                    <!-- Current Linked Bank Status Strip -->
                    <div class="p-4 rounded-xl {{ $profile?->bank_account_number ? 'bg-slate-50/80 border border-slate-200/60' : 'bg-amber-50/60 border border-amber-200/80 text-amber-950' }} flex flex-col gap-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-[11px] uppercase {{ $profile?->bank_account_number ? 'text-slate-500' : 'text-amber-800' }} font-bold">Bank Connection Status</span>
                            @if($profile?->bank_account_number && $profile?->bank_doc_path)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200/80 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Verified &amp; Active
                                </span>
                            @elseif($profile?->bank_account_number)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-200/80 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                    Pending Proof
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 font-mono text-[10px] font-bold uppercase">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                    Not Configured
                                </span>
                            @endif
                        </div>

                        @if($profile?->bank_account_number)
                            <div class="flex flex-col gap-0.5">
                                <p class="font-heading font-bold text-sm text-slate-900">
                                    {{ $profile?->bank_name ?? 'HDFC Bank Ltd. (Verified)' }}
                                </p>
                                <div class="flex items-center gap-3 font-mono text-xs text-slate-500 mt-0.5">
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
                            <span class="font-sans text-xs font-bold text-slate-900">Bank Settlement Details</span>
                            
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
                                <label class="block font-medium text-slate-700 mb-1">Bank Account Number *</label>
                                <input type="text" 
                                       x-ref="bankAccountInput"
                                       name="bank_account_number" 
                                       value="{{ old('bank_account_number', $profile?->bank_account_number) }}" 
                                       placeholder="e.g. 918010023456789"
                                       maxlength="50"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl font-mono text-xs text-slate-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs">
                            </div>

                            <div>
                                <label class="block font-medium text-slate-700 mb-1">Bank IFSC Code *</label>
                                <input type="text" 
                                       x-ref="bankIfscInput"
                                       name="bank_ifsc" 
                                       value="{{ old('bank_ifsc', $profile?->bank_ifsc) }}" 
                                       placeholder="e.g. HDFC0001234"
                                       maxlength="20"
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl font-mono uppercase text-xs text-slate-900 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-all shadow-2xs">
                            </div>

                            <div>
                                <label class="block font-medium text-slate-700 mb-1">Passbook or Cancelled Cheque (Proof)</label>
                                <input type="file" 
                                       name="bank_doc" 
                                       accept=".pdf,.png,.jpg,.jpeg,.webp"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 border border-slate-300 rounded-xl bg-white p-1 cursor-pointer">
                                <span class="text-[10px] text-slate-400 mt-0.5 block">Accepted: PDF, PNG, JPG (Max 5MB).</span>
                            </div>
                        </div>

                        <!-- One-Click Instant Sandbox Bypass Action -->
                        <div class="mt-2 pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2.5">
                            <span class="text-[11px] text-slate-500 font-medium">Testing in sandbox or staging?</span>
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
                    <div class="pt-2 border-t border-slate-100">
                        <label class="block font-semibold text-slate-900 text-xs mb-2">Escrow Clearance Cycle</label>
                        <div class="grid grid-cols-2 gap-2.5 text-xs">
                            <label class="p-3 rounded-xl border border-amber-300/80 bg-amber-50/40 flex flex-col gap-1 cursor-pointer">
                                <input type="radio" name="settlement_cycle" value="t2" class="sr-only" checked>
                                <span class="font-heading font-bold text-xs text-amber-950">T+2 Standard</span>
                                <span class="text-[11px] text-slate-500">Automated NEFT batches</span>
                            </label>
                            <label class="p-3 rounded-xl border border-slate-200 bg-slate-50 flex flex-col gap-1 cursor-pointer opacity-70">
                                <input type="radio" name="settlement_cycle" value="t0" class="sr-only" disabled>
                                <span class="font-heading font-bold text-xs text-slate-500">T+0 Instant</span>
                                <span class="text-[10px] text-amber-700 font-mono font-bold">Tier 1 Prime Only</span>
                            </label>
                        </div>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50/80 border border-slate-200/60 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">verified_user</span>
                        <p class="text-[11px] text-slate-500">Banking credentials are encrypted using AES-256 for RBI settlement compliance.</p>
                    </div>
                </div>

                <!-- Fast Actions Strip -->
                <div class="p-4 bg-white rounded-xl border border-slate-200/80 flex items-center justify-between text-xs shadow-2xs">
                    <span class="text-slate-500">Need to modify GSTIN or PAN?</span>
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
