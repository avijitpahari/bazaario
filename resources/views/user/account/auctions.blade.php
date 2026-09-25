@extends('layouts.user')

@section('title', 'Live Auctions — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-authority via-slate-authority/95 to-slate-authority/90 p-7 shadow-lg border border-white/10 text-canvas-ivory">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-amber-action/15 blur-3xl pointer-events-none"></div>
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-action animate-ping"></span>
                Live Now
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-canvas-ivory mt-1">Live Auctions</h1>
            <p class="font-body-small text-body-small text-canvas-ivory/70 mt-1">Bid on unique, rare, and artisan items. Secured by Bazaario Escrow.</p>
        </div>
    </div>

    <div class="flex flex-col items-center justify-center py-16 gap-6 text-center">
        <div class="w-20 h-20 rounded-full bg-amber-action/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-[42px] text-amber-action">gavel</span>
        </div>
        <div>
            <h2 class="font-title-card text-title-card font-bold text-slate-authority">No live auctions right now</h2>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Check back soon — new auctions go live every day!</p>
        </div>
        <a href="{{ route('home') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">Go to Homepage</a>
    </div>
</div>
@endsection