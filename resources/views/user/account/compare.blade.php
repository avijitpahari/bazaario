@extends('layouts.user')

@section('title', 'Compare Products — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-slate-authority font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">compare_arrows</span> Compare
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Compare Products</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Side-by-side comparison of your selected products.</p>
        </div>
    </div>

    <div class="flex flex-col items-center justify-center py-16 gap-6 text-center">
        <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[42px] text-on-surface-variant">compare_arrows</span>
        </div>
        <h2 class="font-title-card text-title-card font-bold text-slate-authority">No products to compare</h2>
        <p class="font-body-small text-body-small text-on-surface-variant max-w-sm">Add products to your compare list from the product pages.</p>
        <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">Browse Products</a>
    </div>
</div>
@endsection