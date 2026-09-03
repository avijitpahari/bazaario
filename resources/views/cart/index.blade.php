@extends('layouts.app')

@section('title', 'Shopping Cart — Multi-Vendor Unified Checkout | Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    couponCode: '',
    couponApplied: false,
    couponDiscount: 0,
    items: [
        { id: 1, sellerId: 'nordic-leather', sellerName: 'Nordic Leather Atelier', sellerTrust: '99.2%', title: 'Full-Grain Cognac Leather Messenger Briefcase', price: 149.00, qty: 1, image: 'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?auto=format&fit=crop&w=300&q=80', color: 'border-amber-500' },
        { id: 2, sellerId: 'nordic-leather', sellerName: 'Nordic Leather Atelier', sellerTrust: '99.2%', title: 'Solid Brass Heavy-Duty Leather Key Hook', price: 24.00, qty: 2, image: 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=300&q=80', color: 'border-amber-500' },
        { id: 3, sellerId: 'audiocraft', sellerName: 'AudioCraft Studios', sellerTrust: '98.4%', title: 'Aura Hi-Fi Studio Wireless Headphones', price: 219.00, qty: 1, image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=300&q=80', color: 'border-indigo-600' }
    ],
    removeItem(id) {
        this.items = this.items.filter(i => i.id !== id);
    },
    subtotal() {
        return this.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
    },
    applyCoupon() {
        if (this.couponCode.toUpperCase() === 'BAZAAR10') {
            this.couponApplied = true;
            this.couponDiscount = this.subtotal() * 0.10;
        } else {
            alert('Invalid code. Try BAZAAR10 for 10% off!');
        }
    },
    sellerSubtotal(sellerId) {
        return this.items.filter(i => i.sellerId === sellerId).reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
}">

    <!-- Cart Page Header -->
    <div class="flex items-center justify-between pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-3xl font-bold font-heading text-slate-900">Your Unified Cart</h1>
            <p class="text-sm text-slate-500 mt-1">
                <span x-text="items.length"></span> distinct item(s) from 2 independent verified stalls
            </p>
        </div>
        <a href="{{ route('products.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Continue Exploring Stalls
        </a>
    </div>

    <!-- Empty State -->
    <div x-show="items.length === 0" x-cloak class="py-16 text-center space-y-4">
        <div class="w-20 h-20 rounded-3xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-3xl">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <h2 class="text-xl font-bold text-slate-900 font-heading">Your cart is empty</h2>
        <p class="text-sm text-slate-500 max-w-sm mx-auto">Explore unique handmade and artisan goods from verified stalls across the marketplace.</p>
        <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg transition">
            <i class="fa-solid fa-compass"></i> Discover Products
        </a>
    </div>

    <!-- Main Content when Items Exist -->
    <div x-show="items.length > 0" class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Seller-Grouped Items (Span 8) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Multi-Vendor Notification Note -->
            <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-3 text-xs text-amber-900">
                <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-sm"></i>
                <div class="leading-relaxed">
                    <strong>Multi-Vendor Marketplace Notice:</strong> Items from different sellers are dispatched directly from each seller's workshop and may arrive in separate packages with dedicated tracking numbers.
                </div>
            </div>

            <!-- Group 1: Nordic Leather Atelier -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <!-- Seller Group Header -->
                <div class="p-4 sm:p-5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 rounded-full bg-amber-500"></div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('stall.show', 'nordic-leather') }}" class="font-bold text-sm text-slate-900 hover:text-amber-600 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-amber-500"></i> Nordic Leather Atelier
                                </a>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.2 rounded-full">99.2% Trust</span>
                            </div>
                            <span class="text-[11px] text-slate-400">Direct dispatch from Stockholm, Sweden</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Stall Subtotal: $<span x-text="sellerSubtotal('nordic-leather').toFixed(2)"></span></span>
                </div>

                <!-- Items in Group 1 -->
                <div class="divide-y divide-slate-100 p-4 sm:p-6 space-y-4">
                    
                    <template x-for="item in items.filter(i => i.sellerId === 'nordic-leather')" :key="item.id">
                        <div class="pt-4 first:pt-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img :src="item.image" class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shrink-0">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900" x-text="item.title"></h4>
                                    <div class="text-xs font-extrabold text-slate-900 font-heading mt-1">$<span x-text="item.price.toFixed(2)"></span></div>
                                    <button @click="removeItem(item.id)" class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold mt-1 flex items-center gap-1">
                                        <i class="fa-regular fa-trash-can"></i> Remove
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                                    <button @click="if(item.qty > 1) item.qty--" class="px-3 py-1.5 text-slate-600 hover:bg-slate-200"><i class="fa-solid fa-minus text-xs"></i></button>
                                    <span class="px-3 py-1 text-xs font-bold text-slate-900" x-text="item.qty"></span>
                                    <button @click="item.qty++" class="px-3 py-1.5 text-slate-600 hover:bg-slate-200"><i class="fa-solid fa-plus text-xs"></i></button>
                                </div>
                                <div class="text-right min-w-[80px]">
                                    <span class="text-sm font-black font-heading text-slate-900">$<span x-text="(item.price * item.qty).toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

            <!-- Group 2: AudioCraft Studios -->
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
                <!-- Seller Group Header -->
                <div class="p-4 sm:p-5 bg-slate-50/80 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 rounded-full bg-indigo-600"></div>
                        <div>
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('stall.show', 'audiocraft') }}" class="font-bold text-sm text-slate-900 hover:text-indigo-600 transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-indigo-600"></i> AudioCraft Studios
                                </a>
                                <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.2 rounded-full">98.4% Trust</span>
                            </div>
                            <span class="text-[11px] text-slate-400">Direct dispatch from Berlin, Germany</span>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-600">Stall Subtotal: $<span x-text="sellerSubtotal('audiocraft').toFixed(2)"></span></span>
                </div>

                <!-- Items in Group 2 -->
                <div class="divide-y divide-slate-100 p-4 sm:p-6 space-y-4">
                    <template x-for="item in items.filter(i => i.sellerId === 'audiocraft')" :key="item.id">
                        <div class="pt-4 first:pt-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img :src="item.image" class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shrink-0">
                                <div>
                                    <h4 class="font-bold text-sm text-slate-900" x-text="item.title"></h4>
                                    <div class="text-xs font-extrabold text-slate-900 font-heading mt-1">$<span x-text="item.price.toFixed(2)"></span></div>
                                    <button @click="removeItem(item.id)" class="text-[11px] text-rose-500 hover:text-rose-700 font-semibold mt-1 flex items-center gap-1">
                                        <i class="fa-regular fa-trash-can"></i> Remove
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
                                <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                                    <button @click="if(item.qty > 1) item.qty--" class="px-3 py-1.5 text-slate-600 hover:bg-slate-200"><i class="fa-solid fa-minus text-xs"></i></button>
                                    <span class="px-3 py-1 text-xs font-bold text-slate-900" x-text="item.qty"></span>
                                    <button @click="item.qty++" class="px-3 py-1.5 text-slate-600 hover:bg-slate-200"><i class="fa-solid fa-plus text-xs"></i></button>
                                </div>
                                <div class="text-right min-w-[80px]">
                                    <span class="text-sm font-black font-heading text-slate-900">$<span x-text="(item.price * item.qty).toFixed(2)"></span></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        <!-- Right: Sticky Order Summary (Span 4) -->
        <div class="lg:col-span-4">
            <div class="sticky top-28 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xl space-y-5">
                <h3 class="text-lg font-bold font-heading text-slate-900 border-b border-slate-100 pb-3">Order Summary</h3>
                
                <!-- Coupon Input -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Platform Coupon / Promo:</label>
                    <div class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="Try BAZAAR10" class="flex-1 px-3.5 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 uppercase font-semibold focus:outline-none focus:border-indigo-600">
                        <button @click="applyCoupon()" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-indigo-600 text-white text-xs font-bold transition">
                            Apply
                        </button>
                    </div>
                    <p x-show="couponApplied" class="text-xs text-emerald-600 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check"></i> 10% Bazaario discount applied!
                    </p>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-2.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                    <div class="flex justify-between">
                        <span>Items Subtotal:</span>
                        <span class="font-bold text-slate-900">$<span x-text="subtotal().toFixed(2)"></span></span>
                    </div>

                    <div x-show="couponApplied" class="flex justify-between text-emerald-600 font-bold">
                        <span>Coupon Discount (10%):</span>
                        <span>-$<span x-text="couponDiscount.toFixed(2)"></span></span>
                    </div>

                    <div class="flex justify-between">
                        <span>Direct Stall Shipping:</span>
                        <span class="font-bold text-emerald-600">FREE</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Estimated Tax / GST:</span>
                        <span class="font-bold text-slate-900">$0.00</span>
                    </div>
                </div>

                <!-- Total -->
                <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
                    <span class="text-base font-bold font-heading text-slate-900">Total:</span>
                    <span class="text-2xl font-black font-heading text-indigo-600">$<span x-text="(subtotal() - couponDiscount).toFixed(2)"></span></span>
                </div>

                <!-- Proceed to Checkout Button -->
                <a href="{{ route('checkout.index') }}" class="w-full py-4 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-xl shadow-indigo-600/25 flex items-center justify-center gap-2 hover:-translate-y-0.5 transition-all duration-200">
                    Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                </a>

                <!-- Guarantee Badges -->
                <div class="pt-3 text-[11px] text-slate-500 space-y-1.5 border-t border-slate-100">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-shield-check text-emerald-500"></i>
                        <span>Unified escrow buyer protection across all stalls</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-truck-fast text-indigo-500"></i>
                        <span>Separate parcel tracking per stall</span>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>
@endsection
