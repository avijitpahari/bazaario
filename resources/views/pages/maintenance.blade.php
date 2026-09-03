@extends('layouts.app')

@section('title', 'Platform Maintenance & Coming Soon — Bazaario')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 py-16 text-center">
    <div class="max-w-md space-y-6">
        <div class="relative">
            <div class="w-20 h-20 rounded-3xl bg-amber-100 text-amber-600 mx-auto flex items-center justify-center text-3xl shadow-xl ring-8 ring-amber-50">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 bg-amber-100 px-3 py-1 rounded-full">
                Scheduled Upgrade in Progress
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900 mt-2">Bazaario Engine Maintenance</h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                We are currently rolling out platform enhancements for our multi-vendor order routing and stall payout systems. We will be back shortly!
            </p>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm text-xs space-y-3">
            <div class="flex items-center justify-between text-slate-700 font-bold">
                <span>Estimated Completion:</span>
                <span class="text-amber-600">~ 25 minutes</span>
            </div>
            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full w-[78%] animate-pulse"></div>
            </div>
        </div>

        <div class="flex items-center justify-center gap-3 pt-2">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/20 transition">
                <i class="fa-solid fa-house"></i> Check Main Store
            </a>
        </div>
    </div>
</div>
@endsection
