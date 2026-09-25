@extends('layouts.user')

@section('title', 'My Coupons & Perks — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-status-green/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-status-green font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">confirmation_number</span> Perks & Savings
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Coupons & Perks</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Your available discount codes and rewards.</p>
        </div>
    </div>

    {{-- Coupons Grid (dynamic from DB in future — placeholder for now) --}}
    <div class="flex flex-col items-center justify-center py-16 gap-6 text-center">
        <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[42px] text-on-surface-variant">confirmation_number</span>
        </div>
        <div>
            <h2 class="font-title-card text-title-card font-bold text-slate-authority">No coupons available</h2>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Watch for deals and seasonal offers. New coupons appear here automatically.</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">
            Shop & Earn Rewards
        </a>
    </div>
</div>
@endsection