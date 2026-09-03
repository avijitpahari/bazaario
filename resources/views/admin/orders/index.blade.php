@extends('layouts.admin')

@section('title', 'Platform Orders & Dispute Mediation — Bazaario HQ')
@section('page-title', 'Platform-Wide Orders & Dispute Mediation')

@section('content')
<div class="space-y-6" x-data="{
    search: '',
    statusFilter: 'all',
    disputeModal: false,
    inspectModal: false,
    selectedOrder: null,
    resolutionNote: '',
    toastMsg: '',
    showToast: false,
    orders: [
        { 
            id: 'BZR-98241', 
            date: 'Today, 10:24 AM', 
            customer: 'Sarah Miller', 
            email: 'sarah.miller@example.com',
            shippingAddress: '742 Evergreen Terrace, Springfield, OR 97477',
            gross: 374.40, 
            fee: 31.82, 
            netPayout: 342.58,
            payment: 'COD (Doorstep Cash)', 
            status: 'processing', 
            dispute: false,
            packages: [
                { stall: 'Nordic Leather Atelier', items: 'Full-Grain Messenger Bag (1x), Brass Key Hook (2x)', subtotal: 197.00, status: 'Packing in Workshop', tracking: 'Pending' },
                { stall: 'AudioCraft Studios', items: 'Aura Hi-Fi Studio Wireless Headphones (1x)', subtotal: 219.00, status: 'In Transit', tracking: 'DHL-9948271' }
            ]
        },
        { 
            id: 'BZR-98210', 
            date: 'Yesterday, 03:15 PM', 
            customer: 'Liam Johnson', 
            email: 'liam.j@example.com',
            shippingAddress: '100 Market St, Seattle, WA 98101',
            gross: 64.00, 
            fee: 5.44, 
            netPayout: 58.56,
            payment: 'COD (Doorstep Cash)', 
            status: 'dispatched', 
            dispute: false,
            packages: [
                { stall: 'Timber & Grain Workshop', items: 'Solid Walnut Desk Tray (1x)', subtotal: 64.00, status: 'Dispatched via FedEx', tracking: 'FDX-8849201' }
            ]
        },
        { 
            id: 'BZR-98188', 
            date: 'Aug 29, 2026', 
            customer: 'Emma Watson', 
            email: 'emma.w@example.com',
            shippingAddress: '420 Sunset Blvd, Los Angeles, CA 90028',
            gross: 145.00, 
            fee: 12.33, 
            netPayout: 132.67,
            payment: 'COD (Doorstep Cash)', 
            status: 'disputed', 
            dispute: true,
            disputeReason: 'Buyer claims item received was visibly broken upon unboxing and merchant refuses exchange.',
            sellerResponse: 'Merchant claims damage occurred in transit via local courier.',
            packages: [
                { stall: 'Shady Trinkets Co.', items: 'Vintage Glass Vase (1x)', subtotal: 145.00, status: 'Delivered (Damaged)', tracking: 'USPS-449102' }
            ]
        },
        { 
            id: 'BZR-98150', 
            date: 'Aug 28, 2026', 
            customer: 'Carlos Gomez', 
            email: 'carlos.g@example.com',
            shippingAddress: '55 Ocean Ave, Miami, FL 33139',
            gross: 210.00, 
            fee: 17.85, 
            netPayout: 192.15,
            payment: 'COD (Doorstep Cash)', 
            status: 'delivered', 
            dispute: false,
            packages: [
                { stall: 'Kyoto Clay Collective', items: 'Glazed Matcha Bowl Set (2x)', subtotal: 210.00, status: 'Delivered & Confirmed', tracking: 'DHL-1194820' }
            ]
        }
    ],
    openInspect(order) {
        this.selectedOrder = order;
        this.inspectModal = true;
    },
    openDispute(order) {
        this.selectedOrder = order;
        this.disputeModal = true;
    },
    resolveDispute(action) {
        if (!this.selectedOrder) return;
        if (action === 'refund') {
            this.selectedOrder.status = 'refunded';
            this.selectedOrder.dispute = false;
            this.toastMsg = 'Dispute resolved: 100% Escrow refund ($' + this.selectedOrder.gross.toFixed(2) + ') authorized for ' + this.selectedOrder.customer;
        } else {
            this.selectedOrder.status = 'delivered';
            this.selectedOrder.dispute = false;
            this.toastMsg = 'Dispute dismissed: Escrow payout released to merchant.';
        }
        this.disputeModal = false;
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
    }
}">

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 text-xs font-bold">
        <i class="fa-solid fa-shield-halved text-emerald-400 text-base"></i>
        <span x-text="toastMsg"></span>
    </div>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold font-heading text-white">Platform Orders & Escrow Ledger</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-indigo-500/20 text-indigo-300 border border-indigo-500/30" x-text="orders.length + ' Orders'"></span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Audit multi-vendor package splits, monitor courier dispatch milestones, and mediate buyer protection disputes.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold flex items-center gap-1.5 font-mono">
                <i class="fa-solid fa-triangle-exclamation"></i> 1 Active Dispute
            </span>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">All Orders</button>
            <button @click="statusFilter = 'processing'" :class="statusFilter === 'processing' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">In Workshop</button>
            <button @click="statusFilter = 'dispatched'" :class="statusFilter === 'dispatched' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Dispatched</button>
            <button @click="statusFilter = 'delivered'" :class="statusFilter === 'delivered' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Delivered</button>
            <button @click="statusFilter = 'disputed'" :class="statusFilter === 'disputed' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Disputed</button>
        </div>

        <div class="relative w-full sm:w-72">
            <i class="fa-solid fa-magnifying-glass text-slate-500 absolute left-3.5 top-3 text-xs"></i>
            <input type="text" x-model="search" placeholder="Search order ID, customer, stall..." class="w-full py-2 pl-9 pr-3 text-xs rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500 font-mono">
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Order ID & Date</th>
                        <th class="py-3.5 px-4">Customer</th>
                        <th class="py-3.5 px-4">Stall Packages Split</th>
                        <th class="py-3.5 px-4">Gross Total</th>
                        <th class="py-3.5 px-4">Platform Fee (8.5%)</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    <template x-for="o in orders" :key="o.id">
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white font-mono block text-sm" x-text="'#' + o.id"></span>
                                <span class="text-[10px] text-slate-500" x-text="o.date"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-200 block" x-text="o.customer"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="o.email"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="pkg in o.packages" :key="pkg.stall">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-950 text-slate-300 text-[10px] font-bold border border-slate-800 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span x-text="pkg.stall"></span>
                                        </span>
                                    </template>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white font-heading text-sm" x-text="'$' + o.gross.toFixed(2)"></td>
                            <td class="py-3.5 px-4 font-mono text-emerald-400 font-bold" x-text="'+$' + o.fee.toFixed(2)"></td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span x-show="o.status === 'processing'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">In Workshop</span>
                                    <span x-show="o.status === 'dispatched'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Dispatched</span>
                                    <span x-show="o.status === 'delivered'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Delivered</span>
                                    <span x-show="o.status === 'disputed'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse">Dispute Open</span>
                                    <span x-show="o.status === 'refunded'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400">Refunded</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button x-show="o.dispute" @click="openDispute(o)" class="px-3 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs shadow transition flex items-center gap-1">
                                        <i class="fa-solid fa-gavel"></i> Mediate
                                    </button>
                                    <button @click="openInspect(o)" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition">
                                        Inspect
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ORDER INSPECTOR MODAL -->
    <div x-show="inspectModal" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="inspectModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 text-xs text-slate-300" x-show="selectedOrder">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-bold font-heading text-white">Order Details & Package Routing</h3>
                    <span class="font-mono text-indigo-400 font-bold" x-text="'#' + selectedOrder?.id"></span>
                </div>
                <button @click="inspectModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Customer & Delivery Spec -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block">Shipping Address & Customer</span>
                <p class="text-slate-200 font-bold" x-text="selectedOrder?.customer + ' (' + selectedOrder?.email + ')'"></p>
                <p class="text-slate-400 text-[11px]" x-text="selectedOrder?.shippingAddress"></p>
            </div>

            <!-- Packages Breakdown per Stall -->
            <div class="space-y-3">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px] block">Multi-Vendor Package Dispatches</span>
                <template x-for="pkg in selectedOrder?.packages" :key="pkg.stall">
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="font-bold text-white block text-sm" x-text="pkg.stall"></span>
                            <p class="text-slate-400 text-[11px]" x-text="pkg.items"></p>
                            <span class="text-indigo-400 font-mono text-[10px]" x-text="'Tracking: ' + pkg.tracking"></span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-white font-heading text-sm" x-text="'$' + pkg.subtotal.toFixed(2)"></span>
                            <span class="text-[10px] text-amber-400 block font-semibold" x-text="pkg.status"></span>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Financial Summary -->
            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex justify-between items-center text-xs">
                <div>
                    <span class="text-slate-400 block text-[11px]">Payment Mode</span>
                    <span class="text-emerald-400 font-bold" x-text="selectedOrder?.payment"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">8.5% Commission</span>
                    <span class="text-emerald-400 font-bold font-mono" x-text="'+$' + selectedOrder?.fee.toFixed(2)"></span>
                </div>
                <div>
                    <span class="text-slate-400 block text-[11px]">Gross Total</span>
                    <span class="text-lg font-black font-heading text-white" x-text="'$' + selectedOrder?.gross.toFixed(2)"></span>
                </div>
            </div>

            <div class="pt-3 flex justify-end border-t border-slate-800">
                <button @click="inspectModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold">Close Inspector</button>
            </div>
        </div>
    </div>

    <!-- DISPUTE MEDIATION MODAL -->
    <div x-show="disputeModal" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="disputeModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5 text-xs text-slate-300" x-show="selectedOrder">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold font-heading text-rose-400 flex items-center gap-2">
                    <i class="fa-solid fa-gavel"></i> Escrow Dispute Mediation
                </h3>
                <button @click="disputeModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>

            <div class="p-4 rounded-2xl bg-rose-950/40 border border-rose-800/60 space-y-2 text-rose-200">
                <span class="font-bold uppercase tracking-wider text-[10px] text-rose-400 block">Buyer Claim & Damage Evidence</span>
                <p x-text="selectedOrder?.disputeReason"></p>
                <div class="pt-2 border-t border-rose-900/50">
                    <span class="font-bold uppercase tracking-wider text-[10px] text-amber-400 block">Merchant Statement</span>
                    <p class="text-slate-300 text-[11px]" x-text="selectedOrder?.sellerResponse"></p>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="font-bold text-slate-200">Administrative Mediation Decision Log</label>
                <textarea rows="3" x-model="resolutionNote" placeholder="Enter reason for dispute outcome..." class="w-full px-3 py-2.5 rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500"></textarea>
            </div>

            <div class="pt-3 flex flex-wrap justify-end gap-2 border-t border-slate-800">
                <button @click="disputeModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:bg-slate-800">Cancel</button>
                <button @click="resolveDispute('dismiss')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold">Dismiss & Pay Merchant</button>
                <button @click="resolveDispute('refund')" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow">Authorize 100% Buyer Refund</button>
            </div>
        </div>
    </div>

</div>
@endsection
