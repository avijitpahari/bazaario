@extends('layouts.user')

@section('title', 'Recommendations — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">auto_awesome</span> Personalised
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">AI Recommendations</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Products curated just for you by Bazaario AI.</p>
        </div>
    </div>

    <div class="flex flex-col items-center justify-center py-16 gap-6 text-center">
        <div class="w-20 h-20 rounded-full bg-amber-action/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-[42px] text-amber-action">auto_awesome</span>
        </div>
        <div>
            <h2 class="font-title-card text-title-card font-bold text-slate-authority">Building your taste profile</h2>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1 max-w-sm">Shop more to unlock AI-powered recommendations tailored to your style.</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">Start Exploring</a>
    </div>
</div>
@endsection