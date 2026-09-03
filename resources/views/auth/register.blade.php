@extends('layouts.auth')

@section('title', 'Create an Account — Customer or Seller | Bazaario')

@section('content')
<div class="space-y-6" x-data="{
    selectedRole: 'customer',
    showPass: false
}">

    <!-- Brand and Switcher -->
    <div>
        <h1 class="text-2xl font-bold font-heading text-slate-900">Create your account</h1>
        <p class="text-xs text-slate-500 mt-1">Join as a marketplace buyer or register your stall as an independent seller.</p>
    </div>

    <!-- Tab Switcher -->
    <div class="grid grid-cols-2 p-1 rounded-2xl bg-slate-100 text-xs font-bold">
        <a href="{{ route('login') }}" class="py-2.5 text-center rounded-xl text-slate-500 hover:text-slate-900 transition">
            Sign In
        </a>
        <a href="{{ route('register') }}" class="py-2.5 text-center rounded-xl bg-white text-slate-900 shadow-sm">
            Create Account
        </a>
    </div>

    <!-- Role Selector Cards -->
    <div class="space-y-2">
        <label class="text-xs font-bold uppercase tracking-wider text-slate-500 block">I am joining as a:</label>
        <div class="grid grid-cols-2 gap-3">
            
            <!-- Customer Card -->
            <button type="button" @click="selectedRole = 'customer'" :class="selectedRole === 'customer' ? 'border-indigo-600 bg-indigo-50/60 ring-2 ring-indigo-600/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="w-8 h-8 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-sm mb-2">
                    <i class="fa-solid fa-bag-shopping"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs">Customer</h4>
                    <p class="text-[10px] text-slate-500 mt-0.5">Browse & buy from verified stalls</p>
                </div>
            </button>

            <!-- Seller Card -->
            <button type="button" @click="selectedRole = 'seller'" :class="selectedRole === 'seller' ? 'border-amber-500 bg-amber-50/60 ring-2 ring-amber-500/20 text-slate-900' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50'" class="p-4 rounded-2xl border text-left transition flex flex-col justify-between">
                <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-sm mb-2">
                    <i class="fa-solid fa-store"></i>
                </div>
                <div>
                    <h4 class="font-bold text-xs">Stall Merchant</h4>
                    <p class="text-[10px] text-slate-500 mt-0.5">Sell products & receive payouts</p>
                </div>
            </button>

        </div>
    </div>

    <!-- Customer Form -->
    <form x-show="selectedRole === 'customer'" action="{{ route('account.index') }}" method="GET" class="space-y-4">
        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700">Full Name</label>
            <input type="text" placeholder="Sarah Miller" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700">Email Address</label>
            <input type="email" placeholder="sarah@example.com" required class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
        </div>

        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700">Create Password</label>
            <div class="relative">
                <input :type="showPass ? 'text' : 'password'" placeholder="••••••••" required class="w-full pl-4 pr-10 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600">
                <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3 text-slate-400">
                    <i class="fa-regular" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        <div class="text-xs text-slate-500 pt-1">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" required checked class="w-4 h-4 mt-0.5 rounded text-indigo-600 focus:ring-indigo-500">
                <span>I agree to the <a href="#" class="text-indigo-600 font-semibold underline">Bazaario Buyer Terms</a> & Privacy Policy.</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 transition">
            Create Customer Account
        </button>
    </form>

    <!-- Seller Quick Link / Redirect Notice -->
    <div x-show="selectedRole === 'seller'" x-cloak class="p-5 rounded-2xl bg-amber-50 border border-amber-200 space-y-4 text-xs text-amber-900">
        <div class="flex items-center gap-2 font-bold text-sm text-amber-950">
            <i class="fa-solid fa-sparkles text-amber-600"></i> Seller Onboarding Wizard
        </div>
        <p class="leading-relaxed">
            Opening an independent stall requires shop name registration, business verification, and automated catalog setup.
        </p>
        <a href="{{ route('seller.register') }}" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs flex items-center justify-center gap-2 shadow-md transition">
            Proceed to Seller Onboarding Wizard &rarr;
        </a>
    </div>

</div>
@endsection
