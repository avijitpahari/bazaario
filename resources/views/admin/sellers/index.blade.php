@extends('layouts.admin')

@section('title', 'Merchant Directory & Governance — Bazaario HQ')
@section('page-title', 'Platform Merchant Stalls Directory')

@section('content')
<div class="space-y-6" x-data="{
    search: '',
    statusFilter: 'all',
    drawerOpen: false,
    selectedSeller: null,
    toastMsg: '',
    showToast: false,
    sellers: [
        { 
            id: 1, 
            name: 'Nordic Leather Atelier', 
            slug: 'nordic-leather', 
            owner: 'Erik Lindgren', 
            email: 'erik@nordicleather.se',
            phone: '+46 8 123 4567',
            location: 'Stockholm, Sweden',
            products: 28, 
            gmv: 42890.00,
            orders: 1420, 
            trust: 99.2, 
            feeRate: 8.5,
            status: 'active', 
            joined: 'Jan 14, 2023' 
        },
        { 
            id: 2, 
            name: 'AudioCraft Studios', 
            slug: 'audiocraft', 
            owner: 'Marc Vogel', 
            email: 'marc@audiocraft.de',
            phone: '+49 30 555 019',
            location: 'Berlin, Germany',
            products: 14, 
            gmv: 28450.00,
            orders: 840, 
            trust: 98.4, 
            feeRate: 7.5,
            status: 'active', 
            joined: 'Mar 02, 2023' 
        },
        { 
            id: 3, 
            name: 'Kyoto Clay Collective', 
            slug: 'kyoto-clay', 
            owner: 'Sora Takahashi', 
            email: 'sora@kyotoclay.jp',
            phone: '+81 75 994 012',
            location: 'Kyoto, Japan',
            products: 42, 
            gmv: 18920.00,
            orders: 310, 
            trust: 100.0, 
            feeRate: 8.5,
            status: 'active', 
            joined: 'Jul 19, 2023' 
        },
        { 
            id: 4, 
            name: 'Timber & Grain Workshop', 
            slug: 'timber-grain', 
            owner: 'Marcus Vance', 
            email: 'marcus@timberandgrain.com',
            phone: '+1 (555) 234-8901',
            location: 'Oregon, USA',
            products: 48, 
            gmv: 38120.00,
            orders: 2190, 
            trust: 99.4, 
            feeRate: 8.0,
            status: 'active', 
            joined: 'Feb 10, 2022' 
        },
        { 
            id: 5, 
            name: 'Shady Trinkets Co.', 
            slug: 'shady-trinkets', 
            owner: 'Tom R.', 
            email: 'tom@shadytrinkets.net',
            phone: '+1 (555) 990-2104',
            location: 'Nevada, USA',
            products: 6, 
            gmv: 840.00,
            orders: 12, 
            trust: 72.0, 
            feeRate: 8.5,
            status: 'suspended', 
            joined: 'Aug 18, 2026' 
        }
    ],
    openSellerDrawer(seller) {
        this.selectedSeller = seller;
        this.drawerOpen = true;
    },
    toggleSuspend(seller) {
        seller.status = seller.status === 'active' ? 'suspended' : 'active';
        this.toastMsg = 'Stall ' + seller.name + ' status updated to ' + seller.status.toUpperCase();
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
    },
    saveFeeRate() {
        this.drawerOpen = false;
        this.toastMsg = 'Custom commission fee of ' + this.selectedSeller.feeRate + '% saved for ' + this.selectedSeller.name;
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
    }
}">

    <!-- Toast -->
    <div x-show="showToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span x-text="toastMsg"></span>
    </div>

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold font-heading text-white">Merchant Stall Directory</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-indigo-500/20 text-indigo-300 border border-indigo-500/30" x-text="sellers.length + ' Total Stalls'"></span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Supervise merchant revenue, adjust custom commission rates, manage suspensions, and audit trust metrics.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-300 text-xs font-bold transition">
                <i class="fa-solid fa-file-arrow-down text-indigo-400"></i> Export Tax CSV
            </button>
            <a href="{{ route('admin.sellers.pending') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold hover:bg-amber-500/30 transition">
                <i class="fa-solid fa-user-clock"></i> 7 Pending Approvals
            </a>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="statusFilter = 'all'" :class="statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">All Stalls (5)</button>
            <button @click="statusFilter = 'active'" :class="statusFilter === 'active' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Active Verified (4)</button>
            <button @click="statusFilter = 'suspended'" :class="statusFilter === 'suspended' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Suspended (1)</button>
        </div>

        <div class="relative w-full sm:w-72">
            <i class="fa-solid fa-magnifying-glass text-slate-500 absolute left-3.5 top-3 text-xs"></i>
            <input type="text" x-model="search" placeholder="Search stall, owner, location..." class="w-full py-2 pl-9 pr-3 text-xs rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <!-- Sellers Directory Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Stall Profile & Owner</th>
                        <th class="py-3.5 px-4">Origin / Location</th>
                        <th class="py-3.5 px-4">Listings</th>
                        <th class="py-3.5 px-4">Gross Sales Volume</th>
                        <th class="py-3.5 px-4">Commission Rate</th>
                        <th class="py-3.5 px-4">Trust Score</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    <template x-for="seller in sellers" :key="seller.id">
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-amber-500/20 to-indigo-500/20 text-white flex items-center justify-center font-bold text-xs">
                                        <i class="fa-solid fa-store text-amber-400"></i>
                                    </div>
                                    <div>
                                        <button @click="openSellerDrawer(seller)" class="font-bold text-white hover:text-indigo-400 transition text-sm text-left block" x-text="seller.name"></button>
                                        <span class="text-[10px] text-slate-400">Owner: <strong class="text-slate-300" x-text="seller.owner"></strong></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-300" x-text="seller.location"></td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-white" x-text="seller.products + ' items'"></span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-white font-heading text-sm" x-text="'$' + seller.gmv.toLocaleString()"></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px]" :class="seller.feeRate < 8.5 ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : 'bg-slate-800 text-slate-300'" x-text="seller.feeRate + '%'"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-extrabold" :class="seller.trust >= 90 ? 'text-emerald-400' : 'text-rose-400'" x-text="seller.trust + '%'"></span>
                                    <i x-show="seller.trust >= 90" class="fa-solid fa-shield-check text-emerald-400 text-xs"></i>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span x-show="seller.status === 'active'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                                <span x-show="seller.status === 'suspended'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Suspended</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button @click="openSellerDrawer(seller)" class="p-1.5 rounded-lg bg-slate-800 text-slate-300 hover:text-white" title="Manage Stall">
                                        <i class="fa-solid fa-sliders"></i>
                                    </button>
                                    <button @click="toggleSuspend(seller)" :class="seller.status === 'active' ? 'bg-rose-500/20 text-rose-300 hover:bg-rose-500 hover:text-white' : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500 hover:text-white'" class="px-2.5 py-1 rounded-lg font-bold text-[11px] transition">
                                        <span x-text="seller.status === 'active' ? 'Suspend' : 'Reactivate'"></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SELLER MANAGEMENT DRAWER / SLIDE-OVER -->
    <div x-show="drawerOpen" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex justify-end">
        <div @click.outside="drawerOpen = false" class="bg-slate-900 border-l border-slate-800 w-full max-w-lg p-6 sm:p-8 space-y-6 flex flex-col justify-between overflow-y-auto custom-admin-scrollbar" x-show="selectedSeller">
            
            <div class="space-y-6 text-xs text-slate-300">
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold font-heading text-white" x-text="selectedSeller?.name"></h3>
                            <span class="text-[10px] text-slate-400" x-text="'Joined ' + selectedSeller?.joined"></span>
                        </div>
                    </div>
                    <button @click="drawerOpen = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <!-- Financial Stats Card -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Gross Sales</span>
                        <span class="text-lg font-bold font-heading text-white" x-text="'$' + selectedSeller?.gmv.toLocaleString()"></span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Fulfilled Orders</span>
                        <span class="text-lg font-bold font-heading text-emerald-400" x-text="selectedSeller?.orders"></span>
                    </div>
                </div>

                <!-- Custom Commission Tier Configuration -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                    <span class="font-bold text-white block">Custom Commission Override</span>
                    <p class="text-[11px] text-slate-400">Override the platform default (8.5%) for high-volume VIP stalls.</p>
                    
                    <div class="flex items-center gap-3">
                        <div class="relative flex-1">
                            <input type="number" step="0.1" x-model="selectedSeller.feeRate" class="w-full px-3 py-2 rounded-xl border border-slate-800 bg-slate-900 text-white font-bold font-mono">
                            <span class="absolute right-3 top-2 text-slate-500 font-bold">%</span>
                        </div>
                        <button @click="saveFeeRate()" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold transition">
                            Save Tier
                        </button>
                    </div>
                </div>

                <!-- Contact & Governance Info -->
                <div class="space-y-2 p-4 rounded-2xl bg-slate-950 border border-slate-800">
                    <span class="font-bold text-white block">Stall Contact Details</span>
                    <p class="text-slate-300">Email: <strong class="text-white" x-text="selectedSeller?.email"></strong></p>
                    <p class="text-slate-300">Phone: <strong class="text-white" x-text="selectedSeller?.phone"></strong></p>
                    <p class="text-slate-300">Workshop: <strong class="text-white" x-text="selectedSeller?.location"></strong></p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-between gap-3">
                <a :href="'/stall/' + selectedSeller?.slug" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Visit Storefront
                </a>
                <button @click="toggleSuspend(selectedSeller)" :class="selectedSeller?.status === 'active' ? 'bg-rose-600 hover:bg-rose-500 text-white' : 'bg-emerald-600 hover:bg-emerald-500 text-white'" class="px-4 py-2.5 rounded-xl text-xs font-bold transition">
                    <span x-text="selectedSeller?.status === 'active' ? 'Suspend Stall Listings' : 'Reactivate Stall'"></span>
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
