@extends('layouts.seller')

@section('title', 'Merchant Alerts & Notifications — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;

    // Operational alerts model for seller workspace
    $notifications = [
        [
            'id' => 1,
            'type' => 'orders',
            'title' => 'New Multi-Seller Consignment Order Assigned',
            'message' => 'Order #BZ-8924 was placed by Ananya Sharma for 4 kg Organic Alphonso Mango. Prepare lot for courier pickup.',
            'created_at' => '12 minutes ago',
            'icon' => 'local_shipping',
            'color' => 'amber',
            'unread' => true,
            'action_url' => route('seller.orders.index'),
            'action_label' => 'View Order',
        ],
        [
            'id' => 2,
            'type' => 'auctions',
            'title' => 'Wholesale Live Auction Bidding Pulse',
            'message' => 'New competitive bid of ₹16,400 submitted on Wholesale Harvest Lot #AUC-001 by Mandi Wholesaler.',
            'created_at' => '1 hour ago',
            'icon' => 'gavel',
            'color' => 'blue',
            'unread' => true,
            'action_url' => route('seller.auctions.live'),
            'action_label' => 'Auction Terminal',
        ],
        [
            'id' => 3,
            'type' => 'payouts',
            'title' => 'Escrow Payout Settled to Bank Account',
            'message' => 'Weekly escrow settlement of ₹12,450.00 has been processed via NEFT to your registered bank account.',
            'created_at' => 'Yesterday',
            'icon' => 'account_balance_wallet',
            'color' => 'green',
            'unread' => false,
            'action_url' => route('seller.payouts.index'),
            'action_label' => 'Payout Ledger',
        ],
        [
            'id' => 4,
            'type' => 'system',
            'title' => 'Tier 1 Prime Seller Badge Maintained',
            'message' => 'Congratulations! Your shop fulfillment rate reached 99.4%, qualifying you for zero-delay escrow clearances.',
            'created_at' => '3 days ago',
            'icon' => 'verified',
            'color' => 'green',
            'unread' => false,
            'action_url' => route('seller.account.profile'),
            'action_label' => 'View Rating',
        ],
    ];
@endphp

<div class="flex flex-col w-full pb-16" x-data="{
    filter: 'all',
    items: {{ json_encode($notifications) }},
    markAllRead() {
        this.items.forEach(item => item.unread = false);
    },
    markSingleRead(id) {
        const item = this.items.find(i => i.id === id);
        if (item) item.unread = false;
    },
    get unreadCount() {
        return this.items.filter(i => i.unread).length;
    }
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
                    <span class="font-mono text-[11px] text-brand-amber font-semibold uppercase tracking-wider">Notifications</span>
                </div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight">Store Alerts &amp; Notifications</h1>
                    <span x-show="unreadCount > 0" x-text="unreadCount + ' New'" class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-mono text-xs font-bold"></span>
                </div>
                <p class="text-sm text-brand-muted">Real-time alerts for customer orders, auction bids, escrow settlement releases, and compliance audits.</p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <button type="button" @click="markAllRead()" class="h-11 px-4 rounded-[14px] bg-surface-container-low text-on-surface hover:bg-surface-container-high transition-colors text-xs font-medium flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">done_all</span>
                    <span>Mark All Read</span>
                </button>
                <a href="{{ route('seller.account.settings') }}" class="h-11 px-4 rounded-[14px] bg-brand-amber text-primary hover:brightness-105 active:scale-95 transition-all text-xs font-heading font-bold shadow-sm flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Notification Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Tabs Strip -->
    <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-4 text-xs font-sans">
        <button type="button" @click="filter = 'all'" :class="filter === 'all' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-surface-container-highest'" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
            <span>All Alerts</span>
            <span class="font-mono text-[11px] px-1.5 py-0.2 rounded bg-white/20 text-white" x-text="items.length"></span>
        </button>
        <button type="button" @click="filter = 'orders'" :class="filter === 'orders' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-surface-container-highest'" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-[16px]">local_shipping</span>
            <span>Orders &amp; Dispatch</span>
        </button>
        <button type="button" @click="filter = 'auctions'" :class="filter === 'auctions' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-surface-container-highest'" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-[16px]">gavel</span>
            <span>Auction Bids</span>
        </button>
        <button type="button" @click="filter = 'payouts'" :class="filter === 'payouts' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-surface-container-highest'" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
            <span>Escrow &amp; Payouts</span>
        </button>
        <button type="button" @click="filter = 'system'" :class="filter === 'system' ? 'bg-primary text-white font-semibold shadow-xs' : 'bg-surface-container-lowest text-on-surface hover:bg-surface-container-low border border-surface-container-highest'" class="px-4 py-2 rounded-xl transition-all flex items-center gap-2 whitespace-nowrap">
            <span class="material-symbols-outlined text-[16px]">info</span>
            <span>Platform Updates</span>
        </button>
    </div>

    <!-- Notifications Container -->
    <div class="bg-surface-container-lowest rounded-[14px] border border-surface-container-highest/60 shadow-sm overflow-hidden">
        <div class="divide-y divide-surface-container-highest">
            <template x-for="item in items.filter(i => filter === 'all' || i.type === filter)" :key="item.id">
                <div class="p-4 sm:p-5 flex items-start justify-between gap-4 transition-colors hover:bg-surface-container-low" :class="item.unread ? 'bg-amber-50/30' : ''">
                    <div class="flex items-start gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" :class="item.color === 'amber' ? 'bg-amber-100 text-amber-800' : (item.color === 'blue' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800')">
                            <span class="material-symbols-outlined text-[20px]" x-text="item.icon"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <h3 class="font-heading font-bold text-sm text-on-surface" x-text="item.title"></h3>
                                <span x-show="item.unread" class="w-2 h-2 rounded-full bg-brand-amber"></span>
                                <span class="font-mono text-[10px] text-on-surface-variant" x-text="item.created_at"></span>
                            </div>
                            <p class="text-xs text-on-surface-variant leading-relaxed mb-3 max-w-2xl" x-text="item.message"></p>
                            <div class="flex items-center gap-3">
                                <a :href="item.action_url" class="inline-flex items-center gap-1 text-xs font-semibold text-secondary hover:underline">
                                    <span x-text="item.action_label"></span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                                <button type="button" x-show="item.unread" @click="markSingleRead(item.id)" class="text-[11px] text-on-surface-variant hover:text-on-surface underline">
                                    Mark as read
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="items.filter(i => filter === 'all' || i.type === filter).length === 0" class="py-16 text-center flex flex-col items-center justify-center text-on-surface-variant px-4">
            <div class="w-12 h-12 rounded-full bg-surface-container flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-2xl text-on-surface-variant">notifications_off</span>
            </div>
            <h3 class="font-heading font-bold text-base text-on-surface mb-1">No alerts found in this view</h3>
            <p class="text-xs text-on-surface-variant max-w-sm mb-4">You have caught up with all activity in this section. New alerts will be delivered automatically.</p>
            <button type="button" @click="filter = 'all'" class="px-4 py-2 rounded-xl bg-surface-container text-on-surface text-xs font-semibold hover:bg-surface-container-high transition">
                Show All Alerts
            </button>
        </div>
    </div>

</div>
@endsection
