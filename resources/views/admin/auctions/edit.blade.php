@extends('layouts.admin')

@section('title', 'Manage Auction')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Manage Live Auction Lot</h1>
            <p class="text-xs text-slate-500 mt-1">Review live lot telemetry, cancel lot, or trigger early completion.</p>
        </div>
        <a href="{{ route('admin.auctions.index') }}" class="text-xs text-amber-700 hover:underline flex items-center gap-1 font-semibold">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
            Back to Auction Floor
        </a>
    </div>

    @if(isset($auction))
    <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-6 shadow-2xs">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h3 class="font-bold text-sm text-slate-900">Lot #AUC-{{ $auction->id }} — {{ $auction->product?->name }}</h3>
                <p class="text-xs text-slate-500 font-mono">Current Bid: ₹{{ number_format($auction->current_price, 2) }} • Ends At: {{ $auction->ends_at }}</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $auction->status === 'live' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-800' }}">
                Status: {{ ucfirst($auction->status) }}
            </span>
        </div>

        <div class="flex items-center gap-3">
            @if($auction->status === 'live')
            <form method="POST" action="{{ route('admin.auctions.end', $auction->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-2xs">
                    End Auction Early (Select Current Leader)
                </button>
            </form>

            <form method="POST" action="{{ route('admin.auctions.cancel', $auction->id) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-100 hover:bg-rose-200 text-rose-800 font-bold text-xs">
                    Cancel Auction Lot
                </button>
            </form>
            @endif
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-xs text-slate-500">
        <p>Auction record not specified.</p>
        <a href="{{ route('admin.auctions.index') }}" class="mt-3 inline-block px-4 py-2 bg-amber-500 text-slate-950 rounded-lg font-bold">View Auction Floor</a>
    </div>
    @endif
</div>
@endsection
