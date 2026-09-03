@extends('layouts.app')

@section('title', '403 — Unauthorized Role Access | Bazaario')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16 text-center">
    <div class="max-w-md space-y-6">
        <div class="relative">
            <span class="text-8xl sm:text-9xl font-black font-heading text-rose-100 block">403</span>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 text-white flex items-center justify-center text-2xl shadow-xl shadow-rose-600/30">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-3 py-1 rounded-full border border-rose-200">
                Restricted Role Access
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 mt-2">Access Denied to this Area</h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                You do not possess the necessary role credentials (Customer, Approved Seller, or Platform Admin) to access this route.
            </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 text-left space-y-2">
            <div class="flex items-center gap-2 font-bold text-slate-800">
                <i class="fa-solid fa-circle-info text-indigo-600"></i> Need Access?
            </div>
            <p>If you intended to access the seller dashboard, please ensure you have registered your stall and received admin approval.</p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            <a href="{{ route('home') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-lg transition">
                <i class="fa-solid fa-house"></i> Return to Buyer Store
            </a>
            <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 shadow-sm transition">
                <i class="fa-solid fa-arrow-right-to-bracket"></i> Switch Account
            </a>
        </div>
    </div>
</div>
@endsection
