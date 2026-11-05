@extends('layouts.seller')

@section('title', 'Manage Auction Lot — Bazaario Seller Center')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-16">
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold font-heading text-slate-900">Manage Auction Lot</h1>
            <p class="text-xs text-slate-500">View bidding floor activity or cancel eligible lots.</p>
        </div>
        <a href="{{ route('seller.auctions.index') }}" class="group inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-amber-50 text-slate-700 hover:text-amber-800 text-xs font-semibold border border-slate-200/80 hover:border-amber-200 transition-all">
            <span class="material-symbols-outlined text-[15px] transition-transform duration-200 group-hover:-translate-x-1">arrow_back</span>
            <span>Back to Auction Catalog</span>
        </a>
    </div>

    @if(isset($auction))
    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-sm text-slate-900">{{ $auction->product?->name }}</h3>
                <p class="text-xs text-slate-500 font-mono">Lot #AUC-{{ $auction->id }} • Current Price: ₹{{ number_format($auction->current_price, 2) }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $auction->status === 'live' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800' }}">
                {{ ucfirst($auction->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-3.5 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Starting Price</span>
                <span class="text-base font-bold font-mono text-slate-800">₹{{ number_format($auction->starting_price, 2) }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Reserve Price</span>
                <span class="text-base font-bold font-mono text-slate-800">₹{{ number_format($auction->reserve_price ?? 0, 2) }}</span>
            </div>
            <div class="p-3.5 bg-slate-50 rounded-xl">
                <span class="text-[11px] text-slate-400 font-semibold block">Ends At</span>
                <span class="text-xs font-bold font-mono text-slate-800">{{ $auction->ends_at }}</span>
            </div>
        </div>

        @if($auction->status === 'live' && ($auction->bids?->count() ?? 0) === 0)
        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <form method="POST" action="{{ route('seller.auctions.cancel', $auction->id) }}" onsubmit="return confirm('Are you sure you want to cancel this auction lot?');">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition">
                    Cancel Auction Lot
                </button>
            </form>
        </div>
        @endif
    </div>
    @else
    <div class="bg-white rounded-2xl p-8 text-center text-xs text-slate-500">
        <p>Auction lot not specified.</p>
        <a href="{{ route('seller.auctions.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">Go to Auctions</a>
    </div>
    @endif
</div>
@endsection
