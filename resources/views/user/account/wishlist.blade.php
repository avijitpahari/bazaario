@extends('layouts.user')

@section('title', 'My Wishlist — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-error/10 blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-error font-semibold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    Saved Items
                </span>
                <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">My Wishlist</h1>
                <p class="font-body-small text-body-small text-on-surface-variant mt-1">{{ $items->count() }} item(s) saved</p>
            </div>
            <a href="{{ route('products.index') }}" class="self-start md:self-auto px-5 py-2.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold shadow-sm hover:opacity-95 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">explore</span>
                Discover More
            </a>
        </div>
    </div>

    {{-- Items Grid --}}
    @if($items->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($items as $item)
        @php $product = $item->product; $img = $product?->images->first(); @endphp
        <div class="group flex flex-col rounded-2xl bg-surface-container-lowest/90 backdrop-blur-md p-3.5 shadow-sm hover:shadow-md transition-all duration-300 border border-white/60">
            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-surface-container mb-3.5">
                @if($img)
                    <img alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $img->url }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-[48px]">image</span>
                    </div>
                @endif
                <form action="{{ route('user.wishlist.destroy', $item->id) }}" method="POST" class="absolute top-2.5 right-2.5">
                    @csrf @method('DELETE')
                    <button type="submit" title="Remove from wishlist" class="w-8 h-8 rounded-full bg-surface-container-lowest/80 backdrop-blur-md text-error flex items-center justify-center hover:bg-error hover:text-white transition-all shadow-sm">
                        <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">favorite</span>
                    </button>
                </form>
            </div>
            <div class="flex flex-col flex-1 justify-between">
                <div>
                    <h4 class="font-title-card text-title-card font-semibold text-slate-authority leading-snug line-clamp-2">{{ $product->name ?? 'Product' }}</h4>
                    <p class="font-body-small text-body-small text-on-surface-variant line-clamp-1 mt-0.5">{{ $product->description ?? '' }}</p>
                </div>
                <div class="mt-4 pt-3 border-t border-surface-container-high flex items-center justify-between gap-2">
                    <div>
                        <span class="font-label-micro text-label-micro text-on-surface-variant block">Price</span>
                        <span class="font-title-card text-title-card font-bold text-slate-authority">₹{{ number_format($product->price ?? 0, 2) }}</span>
                    </div>
                    <form action="{{ route('cart.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-action text-slate-authority font-button-text text-body-small font-semibold hover:opacity-90 active:scale-[0.99] transition-all flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span> Add
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="flex flex-col items-center justify-center py-20 gap-6 text-center">
        <div class="w-24 h-24 rounded-full bg-surface-container flex items-center justify-center">
            <span class="material-symbols-outlined text-[48px] text-on-surface-variant">favorite_border</span>
        </div>
        <div>
            <h2 class="font-headline-section text-headline-section font-bold text-slate-authority">Your wishlist is empty</h2>
            <p class="font-body-regular text-body-regular text-on-surface-variant mt-2 max-w-md">Save items you love and come back to them anytime.</p>
        </div>
        <a href="{{ route('products.index') }}" class="px-8 py-3.5 rounded-xl bg-amber-action text-slate-authority font-button-text text-button-text font-bold shadow-sm hover:opacity-95">
            Explore Products
        </a>
    </div>
    @endif
</div>
@endsection