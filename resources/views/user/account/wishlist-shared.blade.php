@extends('layouts.app')

@section('title', ($wishlist->user->name ?? 'User') . "'s Saved Finds — Bazaario")

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex flex-col gap-8">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 font-mono">
        <a href="{{ route('home') }}" class="hover:text-slate-900 transition-colors">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-slate-900 transition-colors">Marketplace</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">Shared Wishlist</span>
    </nav>

    {{-- Curator Hero Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 text-white p-6 sm:p-10 shadow-xl border border-slate-700/60">
        <div class="absolute -right-12 -top-12 w-64 h-64 rounded-full bg-rose-500/15 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-amber-400 to-rose-400 text-slate-950 font-black text-2xl flex items-center justify-center shrink-0 shadow-md">
                    {{ strtoupper(substr($wishlist->user?->name ?? 'B', 0, 1)) }}
                </div>
                <div class="flex flex-col gap-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-rose-500/20 text-rose-300 text-xs font-mono font-bold tracking-wider uppercase border border-rose-400/30 w-fit">
                        <span class="material-symbols-outlined text-[14px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                        <span>Curated Collection</span>
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black font-['Outfit'] tracking-tight text-white mt-1">
                        {{ $wishlist->user?->name ?? 'User' }}'s Saved Finds
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300">
                        A handpicked collection of verified artisanal goods, fresh APMC farm consignments, and specialty produce on Bazaario.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/10 shrink-0">
                <div class="text-right">
                    <span class="text-[10px] text-slate-400 uppercase font-mono block">Collection Value</span>
                    <span class="text-xl font-bold font-['Outfit'] text-amber-400">₹{{ number_format($totalValue, 2) }}</span>
                </div>
                <div class="h-8 w-px bg-white/20"></div>
                <div>
                    <span class="text-[10px] text-slate-400 uppercase font-mono block">Saved SKUs</span>
                    <span class="text-xl font-bold font-['Outfit'] text-white">{{ $items->count() }} items</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Grid --}}
    @if($items->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach($items as $item)
            @php
                $product = $item->product;
                $img = $product?->primaryImage?->url ?? $product?->images->first()?->url;
                $inStock = ($product?->stock ?? 0) > 0;
            @endphp
            <div class="group flex flex-col rounded-3xl bg-white border border-slate-200 shadow-2xs hover:shadow-xl hover:border-amber-400/80 transition-all duration-300 overflow-hidden">
                <!-- Image -->
                <div class="relative w-full aspect-square overflow-hidden bg-slate-100">
                    <a href="{{ route('products.show', $product?->slug ?? $product?->id) }}" class="block w-full h-full">
                        @if($img)
                            <img alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" 
                                 src="{{ $img }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <span class="material-symbols-outlined text-[48px]">image</span>
                            </div>
                        @endif
                    </a>

                    <!-- Stock Status Overlay -->
                    <div class="absolute top-3 left-3 pointer-events-none">
                        @if(!$inStock)
                            <span class="px-2.5 py-1 rounded-full bg-rose-600/95 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                Out of Stock
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-full bg-emerald-600/90 text-white text-[10px] font-bold uppercase tracking-wider shadow-sm">
                                In Stock
                            </span>
                        @endif
                    </div>

                    <!-- Category -->
                    @if($product->category)
                        <div class="absolute bottom-3 left-3 pointer-events-none">
                            <span class="px-2 py-0.5 rounded-md bg-black/60 backdrop-blur-md text-white text-[10px] font-mono">
                                {{ $product->category->name }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Content -->
                <div class="p-4 sm:p-5 flex flex-col flex-1 justify-between gap-3 bg-white">
                    <div>
                        @if($product->seller)
                            <span class="text-[11px] text-slate-500 font-mono flex items-center gap-1">
                                <span class="material-symbols-outlined text-[13px] text-amber-600">storefront</span>
                                <span>{{ $product->seller->sellerProfile->shop_name ?? $product->seller->name }}</span>
                            </span>
                        @endif

                        <h3 class="font-bold text-sm text-slate-900 group-hover:text-amber-700 transition-colors line-clamp-2 mt-1">
                            <a href="{{ route('products.show', $product?->slug ?? $product?->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>

                        @if($item->note)
                            <div class="mt-2 p-2 rounded-xl bg-amber-50/70 border border-amber-200/80 text-amber-900 text-[11px] flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px] text-amber-600">notes</span>
                                <span class="italic">"{{ $item->note }}"</span>
                            </div>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <span class="text-base sm:text-lg font-black font-['Outfit'] text-slate-900">
                            ₹{{ number_format($product->price ?? 0, 2) }}
                        </span>

                        <div class="flex items-center gap-1.5">
                            <button type="button" 
                                    onclick="window.bazaarioToggleWishlist({{ $product->id }}, this)"
                                    data-wishlist-product="{{ $product->id }}"
                                    title="Save to My Wishlist"
                                    class="w-8 h-8 rounded-xl border border-slate-200 hover:border-rose-300 text-slate-600 hover:text-rose-500 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[18px]">favorite_border</span>
                            </button>

                            @if($inStock)
                                <form action="{{ route('cart.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors flex items-center gap-1 shadow-xs">
                                        <span class="material-symbols-outlined text-[15px] text-amber-400">add_shopping_cart</span>
                                        <span>Add</span>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-rose-500 font-semibold">Unavailable</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @else
    <div class="p-12 rounded-3xl bg-white border border-slate-200 text-center flex flex-col items-center justify-center gap-3">
        <span class="material-symbols-outlined text-4xl text-slate-400">inbox</span>
        <h3 class="text-lg font-bold text-slate-900">This collection is currently empty</h3>
        <p class="text-xs text-slate-500">The curator has not saved any items to this list yet.</p>
        <a href="{{ route('products.index') }}" class="mt-2 px-5 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-bold text-xs">Explore Marketplace</a>
    </div>
    @endif

</div>
@endsection
