@extends('layouts.user')

@section('title', 'AI Recommendations — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="relative z-10 max-w-xl">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-amber-action font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">auto_awesome</span> Personalised Intelligence
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">AI Recommendations</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Curated picks tailored to your shopping behavior, wishlist, and trending verified listings.</p>
        </div>
        <div class="relative z-10 shrink-0">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-bold hover:bg-slate-authority/90 transition-all flex items-center gap-2">
                <span>Explore All Categories</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>

    @if(isset($recommendedProducts) && count($recommendedProducts) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($recommendedProducts as $prod)
                @php
                    $imgPath = $prod->primaryImage->image_path ?? optional($prod->images->first())->image_path;
                    if (!$imgPath) {
                        $imgUrl = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=600&auto=format&fit=crop&q=80';
                    } elseif (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
                        $imgUrl = $imgPath;
                    } elseif (file_exists(public_path(ltrim($imgPath, '/')))) {
                        $imgUrl = asset(ltrim($imgPath, '/'));
                    } elseif (file_exists(public_path('images/' . ltrim($imgPath, '/')))) {
                        $imgUrl = asset('images/' . ltrim($imgPath, '/'));
                    } else {
                        $imgUrl = Storage::url($imgPath);
                    }
                @endphp
                <div class="rounded-2xl bg-card-white border border-slate-authority/10 shadow-xs hover:shadow-md transition-all overflow-hidden flex flex-col group">
                    <a href="{{ route('products.show', $prod->slug ?? $prod->id) }}" class="relative h-48 w-full bg-surface-container overflow-hidden block">
                        <img src="{{ $imgUrl }}" alt="{{ $prod->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <span class="absolute top-2.5 left-2.5 px-2.5 py-0.5 rounded-full bg-slate-authority/80 backdrop-blur text-canvas-ivory font-label-micro text-label-micro font-semibold">
                            {{ $prod->category->name ?? 'Artisan' }}
                        </span>
                    </a>
                    <div class="p-4 flex flex-col flex-1 justify-between gap-3">
                        <div>
                            <div class="font-label-micro text-label-micro text-on-surface-variant flex items-center justify-between">
                                <span>{{ $prod->seller->sellerProfile->business_name ?? 'Verified Store' }}</span>
                                <span class="text-amber-action flex items-center gap-0.5 font-bold">
                                    <span class="material-symbols-outlined text-[13px]">star</span> 4.8
                                </span>
                            </div>
                            <h3 class="font-title-card text-sm font-bold text-slate-authority mt-1 line-clamp-1 group-hover:text-amber-action transition-colors">
                                <a href="{{ route('products.show', $prod->slug ?? $prod->id) }}">{{ $prod->name }}</a>
                            </h3>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-slate-authority/5">
                            <div>
                                <span class="font-mono font-bold text-base text-slate-authority">₹{{ number_format($prod->price, 0) }}</span>
                            </div>
                            <a href="{{ route('products.show', $prod->slug ?? $prod->id) }}" class="px-3 py-1.5 rounded-lg bg-surface-container hover:bg-amber-action hover:text-slate-authority text-slate-authority font-button-text text-xs font-semibold transition-all">
                                View Item
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 gap-6 text-center bg-card-white rounded-2xl border border-slate-authority/10">
            <div class="w-20 h-20 rounded-full bg-amber-action/10 flex items-center justify-center">
                <span class="material-symbols-outlined text-[42px] text-amber-action">auto_awesome</span>
            </div>
            <div>
                <h2 class="font-title-card text-title-card font-bold text-slate-authority">Building your taste profile</h2>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1 max-w-sm">Shop more to unlock AI-powered recommendations tailored to your style.</p>
            </div>
            <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm">Start Exploring</a>
        </div>
    @endif
</div>
@endsection