@extends('layouts.seller')

@section('title', 'Manage Orders — Stall Merchant Fulfillment | Bazaario')
@section('page-title', 'Stall Order Fulfillment')

@section('content')
<div class="space-y-6" x-data="{
    statusFilter: 'all',
    updateModal: false,
    selectedOrder: null,
    trackingInput: '',
    orders: [
        { id: 'BZR-98241', date: 'Today, 10:24 AM', customer: 'Sarah Miller', destination: 'Springfield, OR', items: 'Full-Grain Messenger Bag (1x), Brass Key Hook (2x)', gross: 197.00, net: 180.25, status: 'packing', tracking: '' },
        { id: 'BZR-98210', date: 'Yesterday, 3:15 PM', customer: 'Liam Johnson', destination: 'Seattle, WA', items: 'Solid Walnut Desk Tray (1x)', gross: 64.00, net: 58.56, status: 'shipped', tracking: 'FDX-9948271' },
        { id: 'BZR-98194', date: 'Sep 01, 2026', customer: 'Chloe Adams', destination: 'Austin, TX', items: 'Slim Bifold Card Wallet (1x)', gross: 45.00, net: 41.17, status: 'delivered', tracking: 'USPS-88472910' }
    ]
}">

    <!-- Top Summary & Search -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold font-heading text-slate-900">Assigned Stall Orders (<span x-text="orders.length"></span>)</h2>
            <p class="text-xs text-slate-500">Only order items assigned to your stall are visible in this view.</p>
        </div>
        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition shadow">
            <i class="fa-solid fa-print"></i> Batch Print Packing Slips
        </button>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto p-2 bg-white rounded-2xl border border-slate-200 shadow-sm">
        <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition">All Orders (3)</button>
        <button @click="statusFilter = 'placed'" :class="statusFilter === 'placed' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition">New Placed (0)</button>
        <button @click="statusFilter = 'packing'" :class="statusFilter === 'packing' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Packing (1)</button>
        <button @click="statusFilter = 'shipped'" :class="statusFilter === 'shipped' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Shipped (1)</button>
        <button @click="statusFilter = 'delivered'" :class="statusFilter === 'delivered' ? 'bg-indigo-600 text-white' : 'text-slate-600 hover:bg-slate-100'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Delivered (1)</button>
    </div>

    <!-- Orders Cards / Table -->
    <div class="space-y-4">
        <template x-for="order in orders" :key="order.id">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 hover:border-slate-300 transition">
                
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-base font-extrabold font-heading text-slate-900" x-text="'#' + order.id"></span>
                        <span class="text-xs text-slate-400" x-text="order.date"></span>
                        
                        <!-- Status Badge -->
                        <span x-show="order.status === 'packing'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                            Packing in Workshop
                        </span>
                        <span x-show="order.status === 'shipped'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800">
                            Dispatched / Shipped
                        </span>
                        <span x-show="order.status === 'delivered'" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Delivered to Buyer
                        </span>
                    </div>

                    <div class="text-xs text-slate-700 font-semibold" x-text="order.items"></div>

                    <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-500 pt-1">
                        <span><i class="fa-regular fa-user text-slate-400 mr-1"></i> <strong class="text-slate-700" x-text="order.customer"></strong></span>
                        <span>•</span>
                        <span><i class="fa-solid fa-location-dot text-slate-400 mr-1"></i> <span x-text="order.destination"></span></span>
                        <span x-show="order.tracking">•</span>
                        <span x-show="order.tracking" class="font-mono text-indigo-600 font-bold"><i class="fa-solid fa-barcode mr-1"></i> <span x-text="order.tracking"></span></span>
                    </div>
                </div>

                <!-- Financials & Fulfillment Actions -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 w-full lg:w-auto justify-between border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <div class="text-left lg:text-right">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Net Payout (8.5% Fee)</span>
                        <div class="text-lg font-black font-heading text-emerald-600" x-text="'$' + order.net.toFixed(2)"></div>
                        <span class="text-[10px] text-slate-400" x-text="'Gross: $' + order.gross.toFixed(2)"></span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="selectedOrder = order; updateModal = true" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow transition flex items-center gap-1.5">
                            <i class="fa-solid fa-truck-ramp-box"></i> Update Status
                        </button>
                        <button class="p-2 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs" title="Print Packing Slip">
                            <i class="fa-solid fa-print"></i>
                        </button>
                    </div>
                </div>

            </div>
        </template>
    </div>

    <!-- Status Update Modal -->
    <div x-show="updateModal" x-cloak class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="updateModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-slate-100 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold font-heading text-slate-900">Fulfill Stall Order</h3>
                <button @click="updateModal = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-base"></i></button>
            </div>

            <div class="space-y-3 text-xs" x-show="selectedOrder">
                <p class="text-slate-600">Update stage for Order <strong class="text-slate-900" x-text="'#' + selectedOrder?.id"></strong></p>

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Fulfillment Stage</label>
                    <select x-model="selectedOrder.status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-semibold">
                        <option value="packing">Packing in Workshop</option>
                        <option value="shipped">Shipped (Dispatched to Courier)</option>
                        <option value="delivered">Mark Fulfilled / Delivered</option>
                    </select>
                </div>

                <div class="space-y-1.5" x-show="selectedOrder?.status === 'shipped'">
                    <label class="font-bold text-slate-700">Courier Tracking Number</label>
                    <input type="text" x-model="selectedOrder.tracking" placeholder="e.g. DHL-8892401" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 font-mono">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-100">
                <button @click="updateModal = false" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100">Cancel</button>
                <button @click="updateModal = false" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow">Save & Notify Customer</button>
            </div>
        </div>
    </div>

</div>
@endsection
