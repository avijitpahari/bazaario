@extends('layouts.app')

@section('title', 'Secure Checkout — Multi-Vendor Order | Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{
    paymentMethod: 'cod',
    savedAddress: 'home',
    fullName: 'Sarah Miller',
    email: 'sarah.miller@example.com',
    phone: '+1 (555) 234-5678',
    address: '742 Evergreen Terrace, Apt 4B',
    city: 'Springfield',
    state: 'OR',
    zip: '97477',
    isSubmitting: false
}">

    <!-- Step Progress Indicator -->
    <div class="max-w-2xl mx-auto mb-10">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full -z-10"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-indigo-600 w-1/2 -z-10"></div>

            <!-- Step 1 (Done) -->
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center gap-1.5 bg-slate-50 px-2 group">
                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-md">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">1. Cart</span>
            </a>

            <!-- Step 2 (Active) -->
            <div class="flex flex-col items-center gap-1.5 bg-slate-50 px-2">
                <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs ring-4 ring-indigo-100 shadow-md">
                    2
                </div>
                <span class="text-xs font-bold text-indigo-600">2. Delivery & Payment</span>
            </div>

            <!-- Step 3 (Pending) -->
            <div class="flex flex-col items-center gap-1.5 bg-slate-50 px-2">
                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center font-bold text-xs">
                    3
                </div>
                <span class="text-xs font-bold text-slate-400">3. Confirmation</span>
            </div>
        </div>
    </div>

    <!-- Main Checkout Form & Summary Grid -->
    <form action="{{ route('orders.confirmation', 'BZR-98241') }}" method="GET" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Delivery Details & Payment Method (Span 8) -->
        <div class="lg:col-span-8 space-y-8">
            
            <!-- 1. Delivery Address Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <h2 class="text-lg font-bold font-heading text-slate-900">Delivery Address</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Step 1 of 2</span>
                </div>

                <!-- Saved Address Selector -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label @click="savedAddress = 'home'" :class="savedAddress === 'home' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600/20' : 'border-slate-200 bg-slate-50'" class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-house text-indigo-600"></i> Primary Residence (Oregon)
                            </span>
                            <input type="radio" name="addressSelect" checked class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Sarah Miller • 742 Evergreen Terrace, Apt 4B, Springfield, OR 97477
                        </p>
                    </label>

                    <label @click="savedAddress = 'office'" :class="savedAddress === 'office' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-600/20' : 'border-slate-200 bg-slate-50'" class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-briefcase text-slate-500"></i> Office Studio
                            </span>
                            <input type="radio" name="addressSelect" class="text-indigo-600 focus:ring-indigo-500">
                        </div>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Sarah Miller • 100 Market St, Suite 400, Portland, OR 97201
                        </p>
                    </label>
                </div>

                <!-- Detailed Form Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Full Name</label>
                        <input type="text" x-model="fullName" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Email Address (for order tracking)</label>
                        <input type="email" x-model="email" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                    <div class="sm:col-span-2 space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">Street Address & Apartment / Unit</label>
                        <input type="text" x-model="address" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700">City</label>
                        <input type="text" x-model="city" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">State / Province</label>
                            <input type="text" x-model="state" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-700">Postal / Zip Code</label>
                            <input type="text" x-model="zip" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                        </div>
                    </div>
                </div>

            </div>

            <!-- 2. Payment Method Selector -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <h2 class="text-lg font-bold font-heading text-slate-900">Select Payment Method</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-400">Step 2 of 2</span>
                </div>

                <div class="space-y-3">
                    
                    <!-- Option 1: Cash on Delivery (ACTIVE & ENABLED) -->
                    <label @click="paymentMethod = 'cod'" :class="paymentMethod === 'cod' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-600/20' : 'border-slate-200'" class="p-4 rounded-2xl border cursor-pointer transition flex items-center justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-slate-900">Cash on Delivery (COD)</h4>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">Active & Available</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">Pay in cash upon doorstep delivery to your courier.</p>
                            </div>
                        </div>
                        <input type="radio" name="payment" value="cod" checked class="text-indigo-600 focus:ring-indigo-500">
                    </label>

                    <!-- Option 2: Card Payment (COMING SOON) -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 opacity-60 flex items-center justify-between cursor-not-allowed">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-slate-800">Credit / Debit Card (Stripe)</h4>
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">Coming Soon</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">Visa, Mastercard, American Express direct settlement</p>
                            </div>
                        </div>
                        <input type="radio" disabled class="text-slate-400">
                    </div>

                    <!-- Option 3: UPI / Digital Wallet (COMING SOON) -->
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/70 opacity-60 flex items-center justify-between cursor-not-allowed">
                        <div class="flex items-center gap-3.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-mobile-screen-button"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-sm text-slate-800">UPI / Digital Wallets / Net Banking</h4>
                                    <span class="text-[10px] font-bold text-slate-600 bg-slate-200 px-2 py-0.5 rounded-full">Coming Soon</span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">Google Pay, Apple Pay, PhonePe, Paytm</p>
                            </div>
                        </div>
                        <input type="radio" disabled class="text-slate-400">
                    </div>

                </div>

            </div>

        </div>

        <!-- Right: Seller-Grouped Order Recap & Place Order (Span 4) -->
        <div class="lg:col-span-4">
            <div class="sticky top-28 bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xl space-y-5">
                <h3 class="text-lg font-bold font-heading text-slate-900 border-b border-slate-100 pb-3">Package Breakdown</h3>

                <!-- Seller Group 1 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Nordic Leather Atelier
                        </span>
                        <span class="font-bold text-slate-700">$197.00</span>
                    </div>
                    <p class="text-[11px] text-slate-500">2 items • Direct courier dispatch</p>
                </div>

                <!-- Seller Group 2 -->
                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-900 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-indigo-600"></span> AudioCraft Studios
                        </span>
                        <span class="font-bold text-slate-700">$219.00</span>
                    </div>
                    <p class="text-[11px] text-slate-500">1 item • Direct courier dispatch</p>
                </div>

                <!-- Totals -->
                <div class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-100">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-bold text-slate-900">$416.00</span>
                    </div>
                    <div class="flex justify-between text-emerald-600 font-bold">
                        <span>Multi-Vendor Promo:</span>
                        <span>-$41.60</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Shipping (All Stalls):</span>
                        <span class="font-bold text-emerald-600">FREE</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                        <span class="text-base font-bold font-heading text-slate-900">Total Due:</span>
                        <span class="text-2xl font-black font-heading text-indigo-600">$374.40</span>
                    </div>
                </div>

                <!-- Place Order Action -->
                <button type="submit" class="w-full py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-xl shadow-emerald-600/25 flex items-center justify-center gap-2 hover:-translate-y-0.5 transition-all duration-200">
                    <i class="fa-solid fa-lock"></i> Place Order (Pay on Delivery)
                </button>

                <p class="text-[11px] text-slate-400 text-center">
                    By clicking "Place Order", your order items will be split and routed to respective seller dashboards immediately.
                </p>
            </div>
        </div>

    </form>

</div>
@endsection
