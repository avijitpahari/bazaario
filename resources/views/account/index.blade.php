@extends('layouts.app')

@section('title', 'My Account & Order History — Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    activeTab: '{{ request()->routeIs('account.orders') ? 'orders' : 'orders' }}',
    orderFilter: 'all'
}">

    <!-- Account Top Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-xl mb-8">
        <div class="flex items-center gap-4">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" alt="Sarah Miller" class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover ring-4 ring-white/10">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-bold font-heading text-white">Sarah Miller</h1>
                    <span class="bg-indigo-500/30 text-indigo-300 border border-indigo-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full">Customer</span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5">sarah.miller@example.com • Member since 2023</p>
                <div class="flex items-center gap-4 text-xs text-slate-300 mt-2">
                    <span><strong class="text-white">14</strong> total orders</span>
                    <span>•</span>
                    <span><strong class="text-white">6</strong> favorite stalls</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('seller.register') }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i class="fa-solid fa-store"></i> Open a Seller Stall
            </a>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex border-b border-slate-200 mb-8 overflow-x-auto">
        <button @click="activeTab = 'orders'" :class="activeTab === 'orders' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-box-open"></i> Orders & Shipments (14)
        </button>
        <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2 shrink-0">
            <i class="fa-regular fa-user"></i> Personal Profile & Security
        </button>
        <button @click="activeTab = 'addresses'" :class="activeTab === 'addresses' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2 shrink-0">
            <i class="fa-solid fa-location-dot"></i> Saved Addresses (2)
        </button>
    </div>

    <!-- TAB 1: ORDERS & TIMELINE TRACKING -->
    <div x-show="activeTab === 'orders'" class="space-y-6">
        
        <!-- Order Filters -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2">
            <button @click="orderFilter = 'all'" :class="orderFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition">All Orders (14)</button>
            <button @click="orderFilter = 'active'" :class="orderFilter === 'active' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Active / In-Transit (2)</button>
            <button @click="orderFilter = 'delivered'" :class="orderFilter === 'delivered' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Delivered (11)</button>
            <button @click="orderFilter = 'cancelled'" :class="orderFilter === 'cancelled' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'" class="px-4 py-2 rounded-xl text-xs font-bold transition">Cancelled (1)</button>
        </div>

        <!-- Order Card 1 (Multi-Vendor Active Order) -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm space-y-6 p-6 sm:p-8">
            
            <!-- Order Header -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-base font-heading text-slate-900">Order #BZR-98241</h3>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Partially Dispatched</span>
                        </div>
                        <p class="text-xs text-slate-400">Placed on Sep 02, 2026 • 2 independent stall shipments</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Total Due (COD):</span>
                    <div class="text-lg font-black font-heading text-indigo-600">$374.40</div>
                </div>
            </div>

            <!-- Vertical Timeline & Per-Seller Breakdown -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Package A: Nordic Leather Atelier -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-xs font-bold text-slate-900">Nordic Leather Atelier</span>
                        </div>
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Processing</span>
                    </div>

                    <!-- Visual Stepper -->
                    <div class="flex items-center justify-between text-[11px] relative pt-2">
                        <div class="text-center font-bold text-emerald-600">
                            <i class="fa-solid fa-circle-check"></i>
                            <div>Placed</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-amber-500 mx-1"></div>
                        <div class="text-center font-bold text-amber-600">
                            <i class="fa-solid fa-boxes-packing"></i>
                            <div>Packing</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-slate-200 mx-1"></div>
                        <div class="text-center text-slate-400">
                            <i class="fa-solid fa-truck"></i>
                            <div>Shipped</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-slate-200 mx-1"></div>
                        <div class="text-center text-slate-400">
                            <i class="fa-solid fa-house-chimney"></i>
                            <div>Delivered</div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 space-y-1.5 pt-2 border-t border-slate-200/60">
                        <div class="flex justify-between">
                            <span>1x Full-Grain Messenger Briefcase</span>
                            <span class="font-bold">$149.00</span>
                        </div>
                        <div class="flex justify-between">
                            <span>2x Solid Brass Leather Key Hook</span>
                            <span class="font-bold">$48.00</span>
                        </div>
                    </div>
                </div>

                <!-- Package B: AudioCraft Studios -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                            <span class="text-xs font-bold text-slate-900">AudioCraft Studios</span>
                        </div>
                        <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">In Transit (DHL #9948271)</span>
                    </div>

                    <!-- Visual Stepper -->
                    <div class="flex items-center justify-between text-[11px] relative pt-2">
                        <div class="text-center font-bold text-emerald-600">
                            <i class="fa-solid fa-circle-check"></i>
                            <div>Placed</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-emerald-500 mx-1"></div>
                        <div class="text-center font-bold text-emerald-600">
                            <i class="fa-solid fa-boxes-packing"></i>
                            <div>Packed</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-indigo-600 mx-1"></div>
                        <div class="text-center font-bold text-indigo-600">
                            <i class="fa-solid fa-truck-fast"></i>
                            <div>Shipped</div>
                        </div>
                        <div class="h-0.5 flex-1 bg-slate-200 mx-1"></div>
                        <div class="text-center text-slate-400">
                            <i class="fa-solid fa-house-chimney"></i>
                            <div>Delivered</div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-700 space-y-1.5 pt-2 border-t border-slate-200/60">
                        <div class="flex justify-between">
                            <span>1x Aura Hi-Fi Studio Wireless Headphones</span>
                            <span class="font-bold">$219.00</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 text-xs">
                <span class="text-slate-500"><i class="fa-solid fa-location-dot text-indigo-500"></i> Shipping to 742 Evergreen Terrace, Springfield, OR</span>
                <div class="flex gap-2">
                    <button class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                        <i class="fa-solid fa-download"></i> Download Invoice
                    </button>
                    <button class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold transition">
                        Cancel Order
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- TAB 2: PROFILE & SECURITY -->
    <div x-show="activeTab === 'profile'" x-cloak class="max-w-2xl bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold font-heading text-slate-900 border-b border-slate-100 pb-3">Edit Profile Information</h3>
        
        <form class="space-y-4">
            <div class="flex items-center gap-4">
                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80" class="w-16 h-16 rounded-2xl object-cover ring-2 ring-indigo-500">
                <button type="button" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold hover:bg-slate-50 text-slate-700">Change Photo</button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Full Name</label>
                    <input type="text" value="Sarah Miller" class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Email</label>
                    <input type="email" value="sarah.miller@example.com" class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700">Phone</label>
                    <input type="text" value="+1 (555) 234-5678" class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Change Password</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="password" placeholder="Current Password" class="px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                    <input type="password" placeholder="New Password" class="px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="button" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 3: SAVED ADDRESSES -->
    <div x-show="activeTab === 'addresses'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-3 relative">
            <span class="absolute top-4 right-4 text-[10px] font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded-full">Default</span>
            <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                <i class="fa-solid fa-house text-indigo-600"></i> Primary Residence
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                Sarah Miller<br>
                742 Evergreen Terrace, Apt 4B<br>
                Springfield, OR 97477<br>
                Phone: +1 (555) 234-5678
            </p>
            <div class="flex gap-2 pt-2 border-t border-slate-100 text-xs">
                <button class="text-indigo-600 font-bold hover:underline">Edit</button>
                <span class="text-slate-300">•</span>
                <button class="text-rose-600 font-bold hover:underline">Delete</button>
            </div>
        </div>

        <div class="p-6 rounded-3xl bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center cursor-pointer hover:bg-slate-100 transition min-h-[160px]">
            <i class="fa-solid fa-plus text-slate-400 text-xl mb-1"></i>
            <span class="text-xs font-bold text-slate-700">Add New Delivery Address</span>
            <span class="text-[10px] text-slate-400">Save for quick 1-click checkout</span>
        </div>
    </div>

</div>
@endsection
