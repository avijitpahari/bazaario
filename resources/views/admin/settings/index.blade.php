@extends('layouts.admin')

@section('title', 'Platform Configuration & Settings')

@section('content')
<div class="flex flex-col w-full gap-space-lg" x-data="{ activeTab: 'general', saving: false }">

    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Platform Configuration & Branding</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-mono text-[11px] font-bold border border-amber-200/60 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full {{ ($settings['platform_mode'] ?? 'multi_vendor') === 'single_vendor' ? 'bg-indigo-600' : 'bg-emerald-600' }}"></span>
                    {{ ($settings['platform_mode'] ?? 'multi_vendor') === 'single_vendor' ? 'SINGLE-VENDOR STORE' : 'MULTI-VENDOR MARKETPLACE' }}
                </span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Manage global marketplace branding, commission schedules, operating model toggles, and live auction parameters.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.settings.ai') }}" class="px-3.5 py-2 rounded-xl border border-indigo-200 bg-indigo-50/70 text-indigo-900 font-body-md text-xs font-bold hover:bg-indigo-100 transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">smart_toy</span>
                <span>AI Engine Settings</span>
            </a>
            <button type="submit" form="settingsForm" :disabled="saving" class="px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-colors flex items-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                <span x-show="!saving" class="material-symbols-outlined text-[18px]">save</span>
                <span x-show="saving" x-cloak><x-loading-spinner size="16px" /></span>
                <span x-text="saving ? 'Saving...' : 'Save All Settings'">Save All Settings</span>
            </button>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-slate-200 flex flex-wrap gap-2 text-xs font-bold">
        <button type="button" @click="activeTab = 'general'" :class="activeTab === 'general' ? 'border-[#F5A623] text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-600 hover:text-slate-900'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">storefront</span>
            General & Branding
        </button>
        <button type="button" @click="activeTab = 'platform'" :class="activeTab === 'platform' ? 'border-[#F5A623] text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-600 hover:text-slate-900'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">hub</span>
            Platform Architecture & Mode
        </button>
        <button type="button" @click="activeTab = 'commissions'" :class="activeTab === 'commissions' ? 'border-[#F5A623] text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-600 hover:text-slate-900'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">payments</span>
            Global Commissions & Escrow
        </button>
        <button type="button" @click="activeTab = 'auctions'" :class="activeTab === 'auctions' ? 'border-[#F5A623] text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-600 hover:text-slate-900'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">gavel</span>
            Live Auctions
        </button>
        <button type="button" @click="activeTab = 'payments'" :class="activeTab === 'payments' ? 'border-[#F5A623] text-amber-800 bg-amber-50/60' : 'border-transparent text-slate-600 hover:text-slate-900'" class="px-4 py-2.5 border-b-2 rounded-t-lg transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">credit_card</span>
            Payments & Orders
        </button>
    </div>

    <!-- Main Settings Form -->
    <form id="settingsForm" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="flex flex-col gap-6">
        @csrf

        <!-- 1. GENERAL & BRANDING TAB -->
        <div x-show="activeTab === 'general'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-amber-600 text-xl">palette</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Platform Brand Assets</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Site / Marketplace Name</label>
                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'Bazaario' }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Displayed in page headers, emails, and invoices.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Public Tagline</label>
                    <input type="text" name="site_tagline" value="{{ $settings['site_tagline'] ?? 'AI-Powered Configurable Multi-Vendor Marketplace' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Short slogan displayed beneath the brand title.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Brand Logo (PNG, SVG, WEBP)</label>
                    @if(\App\Models\SiteSetting::hasCustomLogo())
                        <div class="mb-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ \App\Models\SiteSetting::getLogoUrl() }}" alt="Current Logo" class="h-8 max-w-[140px] object-contain rounded">
                                <div>
                                    <span class="text-[11px] text-emerald-700 font-semibold block">Active Custom Logo</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ $settings['site_logo'] }}</span>
                                </div>
                            </div>
                            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 border border-rose-200 text-rose-700 hover:bg-rose-100 text-xs font-medium cursor-pointer transition-colors" title="Remove custom logo and use dynamic designed monogram">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                                <span>Remove Custom Logo</span>
                            </label>
                        </div>
                    @else
                        <div class="mb-2 p-2.5 bg-amber-50/50 border border-amber-200/60 rounded-lg flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-950 font-black font-['Outfit'] text-sm shadow-xs">
                                    {{ strtoupper(substr($settings['site_name'] ?? 'B', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-[11px] text-amber-900 font-semibold block">Active Modern Brand Monogram</span>
                                    <span class="text-[10px] text-amber-700/80 font-mono">No static image required (SVG/CSS emblem)</span>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">Upload to override</span>
                        </div>
                    @endif
                    <input type="file" name="site_logo" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-800 hover:file:bg-amber-100">
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-indigo-600 text-xl">contact_support</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Support & Governance Contact</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Customer Support Email</label>
                    <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? 'support@bazaario.in' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Recipient for automated escalated dispute notices.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Helpdesk Phone / Helpline</label>
                    <input type="text" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+91 800-BAZAARIO' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Shown in public contact pages and invoice headers.</p>
                </div>
            </div>
        </div>

        <!-- 2. PLATFORM MODE TAB -->
        <div x-show="activeTab === 'platform'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-purple-600 text-xl">account_tree</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Operating Mode Switch</h3>
                </div>

                <div class="space-y-4">
                    <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition {{ ($settings['platform_mode'] ?? 'multi_vendor') === 'multi_vendor' ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500/30' : 'border-slate-200' }}">
                        <input type="radio" name="platform_mode" value="multi_vendor" {{ ($settings['platform_mode'] ?? 'multi_vendor') === 'multi_vendor' ? 'checked' : '' }} class="mt-0.5 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Multi-Vendor Marketplace (Default)</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Independent third-party sellers can register, onboard through KYC verification, upload product catalogs, and fulfill their orders.</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-3 p-3.5 border rounded-xl cursor-pointer hover:bg-slate-50 transition {{ ($settings['platform_mode'] ?? '') === 'single_vendor' ? 'border-amber-500 bg-amber-50/40 ring-1 ring-amber-500/30' : 'border-slate-200' }}">
                        <input type="radio" name="platform_mode" value="single_vendor" {{ ($settings['platform_mode'] ?? '') === 'single_vendor' ? 'checked' : '' }} class="mt-0.5 text-amber-600 focus:ring-amber-500">
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Single-Vendor Storefront</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Operate as a centralized first-party brand. New merchant registrations are paused, and storefront focuses exclusively on platform-curated inventory.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-xs text-slate-900 mb-2">Architectural Guardrails</h4>
                    <ul class="text-[11px] text-slate-600 space-y-2">
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                            <span>Seamless toggle: Changing modes does not drop merchant or seller data.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="material-symbols-outlined text-emerald-600 text-sm">check_circle</span>
                            <span>Orders placed during either mode preserve complete seller-order splits and payout accounting history.</span>
                        </li>
                    </ul>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200/60 text-[11px] text-amber-900">
                    <strong>Tip:</strong> In Single-Vendor mode, the unified cart automatically groups all checkouts under the platform primary store.
                </div>
            </div>
        </div>

        <!-- 3. COMMISSIONS TAB -->
        <div x-show="activeTab === 'commissions'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-emerald-600 text-xl">percent</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Global Platform Commission</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Standard Base Commission Rate (%)</label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="{{ $settings['commission_rate'] ?? '8.5' }}" required class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none pr-8">
                        <span class="absolute right-3 top-2 text-xs text-slate-400 font-bold">%</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Platform fee deducted from gross seller order totals upon delivery confirmation.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Minimum Payout Withdrawal (₹ INR)</label>
                    <input type="number" step="10" min="0" name="min_payout_amount" value="{{ $settings['min_payout_amount'] ?? '500' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Minimum accumulated balance required for sellers to request a bank transfer.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-xs text-slate-900 mb-2">Custom Seller Commission Overrides</h4>
                    <p class="text-[11px] text-slate-500 mb-3">Individual merchant rates can be overridden on the verified merchant inspection view in the Sellers directory.</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200/60 text-[11px] text-emerald-900">
                    Transparent rates are displayed to sellers before order dispatch to maintain complete ledger trust.
                </div>
            </div>
        </div>

        <!-- 4. LIVE AUCTIONS TAB -->
        <div x-show="activeTab === 'auctions'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-amber-600 text-xl">gavel</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Auction Floor Operating Rules</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Auction System Status</label>
                    <select name="auction_system_enabled" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                        <option value="1" {{ ($settings['auction_system_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled (Live bidding active)</option>
                        <option value="0" {{ ($settings['auction_system_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Paused (Auctions hidden from catalog)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Default Minimum Bid Increment (₹ INR)</label>
                    <input type="number" step="1" min="1" name="default_bid_increment" value="{{ $settings['default_bid_increment'] ?? '50' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Default minimum step between successive competitive bids.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Anti-Sniping Buffer Extension (Seconds)</label>
                    <input type="number" step="10" min="0" max="3600" name="auction_anti_snipe_seconds" value="{{ $settings['auction_anti_snipe_seconds'] ?? '120' }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                    <p class="text-[11px] text-slate-400 mt-1">Seconds added to the auction timer if a bid arrives in the final minutes.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-xs text-slate-900 mb-2">Automated Lifecycle Execution</h4>
                    <p class="text-[11px] text-slate-500">
                        The automated background scheduler runs the <code>auctions:close-expired</code> artisan worker to evaluate ended lots, audit reserve prices, and invoice winning bidders.
                    </p>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200/60 text-[11px] text-amber-900 font-mono">
                    php artisan auctions:close-expired
                </div>
            </div>
        </div>

        <!-- 5. PAYMENTS & ORDERS TAB -->
        <div x-show="activeTab === 'payments'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-space-lg">
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs p-5 flex flex-col gap-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-blue-600 text-xl">local_atm</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Payment Gateways & Options</h3>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cash on Delivery (COD)</label>
                    <select name="cod_enabled" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none">
                        <option value="1" {{ ($settings['cod_enabled'] ?? '1') == '1' ? 'selected' : '' }}>Enabled (Permit cash payments at doorstep)</option>
                        <option value="0" {{ ($settings['cod_enabled'] ?? '1') == '0' ? 'selected' : '' }}>Disabled (Only digital checkout allowed)</option>
                    </select>
                </div>

                <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-[11px] text-slate-600 space-y-1">
                    <p class="font-bold text-slate-800">Currency Default: Indian Rupee (₹ INR)</p>
                    <p>All pricing, bids, payouts, and invoices format with standard Indian currency grouping.</p>
                </div>
            </div>
        </div>

        <!-- Bottom Save Bar -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <button type="submit" form="settingsForm" :disabled="saving" class="px-5 py-2.5 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-sm hover:bg-amber-400 transition-colors flex items-center gap-1.5 disabled:opacity-60 disabled:cursor-not-allowed">
                <span x-show="!saving" class="material-symbols-outlined text-[18px]">save</span>
                <span x-show="saving" x-cloak><x-loading-spinner size="16px" /></span>
                <span x-text="saving ? 'Saving...' : 'Save All Settings'">Save All Settings</span>
            </button>
        </div>

    </form>

</div>
@endsection
