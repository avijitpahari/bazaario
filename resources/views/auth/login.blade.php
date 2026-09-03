@extends('layouts.auth')

@section('title', 'Log In — Bazaario Multi-Vendor Marketplace')

@section('content')
<div class="space-y-6" x-data="{ showPass: false }">

    <!-- Brand and Switcher -->
    <div>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold font-heading text-slate-900">Welcome Back</h1>
            <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-400 hover:text-indigo-600 lg:hidden">
                <i class="fa-solid fa-arrow-left"></i> Home
            </a>
        </div>
        <p class="text-xs text-slate-500 mt-1">Access your customer orders, saved stalls, or seller dashboard.</p>
    </div>

    <!-- Tab Switcher -->
    <div class="grid grid-cols-2 p-1 rounded-2xl bg-slate-100 text-xs font-bold">
        <a href="{{ route('login') }}" class="py-2.5 text-center rounded-xl bg-white text-slate-900 shadow-sm">
            Sign In
        </a>
        <a href="{{ route('register') }}" class="py-2.5 text-center rounded-xl text-slate-500 hover:text-slate-900 transition">
            Create Account
        </a>
    </div>

    <!-- Login Form -->
    <form action="{{ route('account.index') }}" method="GET" class="space-y-4">
        
        <div class="space-y-1.5">
            <label class="text-xs font-bold text-slate-700">Email Address</label>
            <div class="relative">
                <i class="fa-regular fa-envelope text-slate-400 absolute left-3.5 top-3 text-sm"></i>
                <input type="email" value="sarah.miller@example.com" required placeholder="name@domain.com" class="w-full pl-10 pr-4 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
            </div>
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="text-xs font-bold text-slate-700">Password</label>
                <a href="#" class="text-[11px] font-semibold text-indigo-600 hover:underline">Forgot password?</a>
            </div>
            <div class="relative">
                <i class="fa-solid fa-lock text-slate-400 absolute left-3.5 top-3 text-sm"></i>
                <input :type="showPass ? 'text' : 'password'" value="secret123" required class="w-full pl-10 pr-10 py-2.5 text-xs rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 focus:bg-white transition">
                <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600">
                    <i class="fa-regular" :class="showPass ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                <input type="checkbox" checked class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                <span>Remember me for 30 days</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/25 transition">
            Sign In to Bazaario
        </button>

    </form>

    <!-- Demo Role Quick Switcher / Logins -->
    <div class="pt-4 border-t border-slate-100 space-y-2">
        <p class="text-[11px] text-slate-400 text-center uppercase tracking-wider font-bold">Quick Demo Access</p>
        <div class="grid grid-cols-2 gap-2 text-xs font-semibold">
            <a href="{{ route('account.index') }}" class="p-2.5 rounded-xl bg-slate-50 hover:bg-indigo-50 hover:text-indigo-700 text-slate-700 border border-slate-200 text-center transition">
                <i class="fa-solid fa-user text-indigo-500 mr-1"></i> Customer Account
            </a>
            <a href="{{ route('seller.dashboard') }}" class="p-2.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-center transition">
                <i class="fa-solid fa-store text-amber-600 mr-1"></i> Seller Dashboard
            </a>
        </div>
    </div>

</div>
@endsection
