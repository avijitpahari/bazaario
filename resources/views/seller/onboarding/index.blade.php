@extends('layouts.app')

@section('title', 'Seller Onboarding Wizard — Register Your Stall | Bazaario')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{
    step: 1,
    shopName: 'Heritage Wood & Iron',
    shopCategory: 'artisan',
    shopBio: 'Handcrafted rustic wood tables, live-edge shelving and custom forged hardware.',
    submitted: false
}">

    <!-- Wizard Stepper Indicator -->
    <div class="mb-10">
        <div class="flex items-center justify-between relative max-w-md mx-auto">
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-slate-200 w-full -z-10"></div>
            <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-amber-500 transition-all duration-300 -z-10" :style="'width: ' + ((step - 1) / 2 * 100) + '%'"></div>

            <!-- Step 1 -->
            <button @click="if(!submitted) step = 1" class="flex flex-col items-center gap-1.5 bg-slate-50 px-2 focus:outline-none">
                <div :class="step >= 1 ? 'bg-amber-500 text-slate-950 ring-4 ring-amber-100' : 'bg-slate-200 text-slate-500'" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-md transition">
                    <span x-show="step <= 1">1</span>
                    <i x-show="step > 1" class="fa-solid fa-check"></i>
                </div>
                <span class="text-xs font-bold" :class="step >= 1 ? 'text-amber-700' : 'text-slate-400'">Basic Account</span>
            </button>

            <!-- Step 2 -->
            <button @click="if(!submitted && step >= 2) step = 2" class="flex flex-col items-center gap-1.5 bg-slate-50 px-2 focus:outline-none">
                <div :class="step >= 2 ? 'bg-amber-500 text-slate-950 ring-4 ring-amber-100' : 'bg-slate-200 text-slate-500'" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-md transition">
                    <span x-show="step <= 2">2</span>
                    <i x-show="step > 2" class="fa-solid fa-check"></i>
                </div>
                <span class="text-xs font-bold" :class="step >= 2 ? 'text-amber-700' : 'text-slate-400'">Stall Profile</span>
            </button>

            <!-- Step 3 -->
            <div class="flex flex-col items-center gap-1.5 bg-slate-50 px-2">
                <div :class="step === 3 ? 'bg-emerald-600 text-white ring-4 ring-emerald-100' : 'bg-slate-200 text-slate-500'" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shadow-md transition">
                    3
                </div>
                <span class="text-xs font-bold" :class="step === 3 ? 'text-emerald-700' : 'text-slate-400'">Approval Status</span>
            </div>
        </div>
    </div>

    <!-- Step 1: Basic Merchant Credentials -->
    <div x-show="step === 1" class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Step 1 of 3</span>
            <h2 class="text-2xl font-bold font-heading text-slate-900 mt-1">Merchant Contact & Access</h2>
            <p class="text-xs text-slate-500 mt-1">Tell us who is managing this stall on Bazaario marketplace.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Owner Full Name</label>
                <input type="text" value="Marcus Vance" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Business / Contact Email</label>
                <input type="email" value="marcus@heritagewood.com" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Phone Number (SMS order alerts)</label>
                <input type="tel" value="+1 (555) 890-1234" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
            </div>
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Account Password</label>
                <input type="password" value="secret12345" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button @click="step = 2" class="px-8 py-3.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-2">
                Continue to Stall Details <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>

    <!-- Step 2: Stall Details & Identity -->
    <div x-show="step === 2" x-cloak class="bg-white rounded-3xl p-8 border border-slate-200 shadow-xl space-y-6">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Step 2 of 3</span>
            <h2 class="text-2xl font-bold font-heading text-slate-900 mt-1">Stall Branding & Category</h2>
            <p class="text-xs text-slate-500 mt-1">This information will be displayed to buyers on your storefront page.</p>
        </div>

        <div class="space-y-4">
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Public Stall Name</label>
                <input type="text" x-model="shopName" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
                <span class="text-[11px] text-slate-400 block">Store URL: bazaario.com/stall/<strong class="text-slate-700" x-text="shopName.toLowerCase().replace(/[^a-z0-9]/g, '-')"></strong></span>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Primary Product Taxonomy / Category</label>
                <select x-model="shopCategory" class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500">
                    <option value="artisan">Artisan, Woodwork & Crafts</option>
                    <option value="electronics">Electronics & Audio Gear</option>
                    <option value="apparel">Apparel & Textiles</option>
                    <option value="home">Home & Furniture</option>
                    <option value="organic">Organic Food & Gourmet</option>
                </select>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-700">Stall Bio & Story</label>
                <textarea rows="3" x-model="shopBio" class="w-full px-4 py-2 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-amber-500"></textarea>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <div>
                        <span class="font-bold text-xs text-slate-900 block">Stall Logo & Banner</span>
                        <span class="text-[10px] text-slate-400">PNG or JPG, max 2MB</span>
                    </div>
                </div>
                <button type="button" class="px-3.5 py-1.5 rounded-xl border border-slate-300 text-xs font-bold bg-white hover:bg-slate-50">Upload Media</button>
            </div>
        </div>

        <div class="pt-4 flex items-center justify-between border-t border-slate-100">
            <button @click="step = 1" class="text-xs font-bold text-slate-500 hover:text-slate-800">
                &larr; Back
            </button>
            <button @click="step = 3; submitted = true" class="px-8 py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                Submit Stall Application <i class="fa-solid fa-check"></i>
            </button>
        </div>
    </div>

    <!-- Step 3: Confirmation & "Awaiting Admin Approval" State -->
    <div x-show="step === 3" x-cloak class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-xl text-center space-y-6">
        <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-600 mx-auto flex items-center justify-center text-3xl shadow-lg ring-8 ring-amber-50">
            <i class="fa-solid fa-clock-rotate-left"></i>
        </div>

        <div>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-800 bg-amber-100 px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> Status: Awaiting Admin Approval
            </span>
            <h2 class="text-3xl font-extrabold font-heading text-slate-900 mt-3">Your Stall is Under Review</h2>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mt-2 leading-relaxed">
                Thank you, <strong>Marcus Vance</strong>! Your stall <strong class="text-slate-800">"Heritage Wood & Iron"</strong> has been placed in the Admin Governance Queue. Approvals are typically completed within 2 to 4 business hours.
            </p>
        </div>

        <!-- Progress Card -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 max-w-md mx-auto text-left text-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="font-bold text-slate-900">Application Progress:</span>
                <span class="text-indigo-600 font-bold">85% Completed</span>
            </div>
            <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full w-[85%]"></div>
            </div>
            <p class="text-[11px] text-slate-400">Once approved by Admin, your seller dashboard will automatically unlock and your products will appear in the marketplace catalog.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-4">
            <a href="{{ route('seller.dashboard') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow transition flex items-center justify-center gap-2">
                <i class="fa-solid fa-gauge"></i> Preview Seller Dashboard Demo
            </a>
            <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs transition">
                Return to Homepage
            </a>
        </div>
    </div>

</div>
@endsection
