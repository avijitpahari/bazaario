@extends('layouts.admin')

@section('title', 'Wholesale Auction System Configuration')

@section('content')
@php
    $isEnabled = \App\Models\SiteSetting::isAuctionEnabled();
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold font-headline-lg text-[#0F172A]">Wholesale Auction System Controls</h1>
                @if($isEnabled)
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 font-mono text-[11px] font-bold border border-emerald-200/60 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-ping"></span>
                        ACTIVE
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-mono text-[11px] font-bold border border-amber-300 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                        PAUSED
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-1">Enable or disable the wholesale auction system, configure minimum increments, and tune anti-sniping buffers.</p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <a href="{{ route('admin.auctions.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-2xs transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[17px]">gavel</span>
                <span>Auction Terminal</span>
            </a>
            <a href="{{ route('admin.settings.index') }}" class="px-3.5 py-2 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-900 text-xs font-bold shadow-2xs transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[17px]">settings</span>
                <span>Platform Settings</span>
            </a>
        </div>
    </div>

    <!-- Quick Status Banner -->
    <div class="p-5 rounded-2xl border {{ $isEnabled ? 'bg-emerald-50/70 border-emerald-200 text-emerald-950' : 'bg-amber-50/80 border-amber-300 text-amber-950' }} flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl {{ $isEnabled ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-slate-950' }} flex items-center justify-center font-bold shrink-0">
                <span class="material-symbols-outlined text-2xl">{{ $isEnabled ? 'gavel' : 'pause' }}</span>
            </div>
            <div>
                <strong class="font-bold text-sm block">
                    {{ $isEnabled ? 'Auction System is Currently Active' : 'Auction System is Currently Paused' }}
                </strong>
                <p class="text-xs {{ $isEnabled ? 'text-emerald-800' : 'text-amber-800' }} mt-0.5">
                    {{ $isEnabled ? 'Shoppers can place bids on live lots, and verified merchants can list new wholesale floor lots.' : 'Public bidding is disabled and sellers cannot create new lots. Existing lot records and winner settlements are safely retained.' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.auctions.toggleSystem') }}" class="shrink-0"
              onsubmit="{{ $isEnabled ? "return confirm('Are you sure you want to pause wholesale auctions?');" : '' }}">
            @csrf
            <input type="hidden" name="enabled" value="{{ $isEnabled ? '0' : '1' }}">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl {{ $isEnabled ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-emerald-600 text-white hover:bg-emerald-700' }} font-bold text-xs transition-colors shadow-2xs flex items-center justify-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">{{ $isEnabled ? 'pause_circle' : 'play_circle' }}</span>
                <span>{{ $isEnabled ? 'Pause Auctions Now' : 'Enable Auctions Now' }}</span>
            </button>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="bg-white rounded-2xl border border-slate-200 p-6 space-y-5 shadow-2xs">
        @csrf
        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Auction System Operating Status</label>
            <select name="auction_system_enabled" class="w-full px-3.5 py-2.5 text-xs font-semibold border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none bg-slate-50">
                <option value="1" {{ $isEnabled ? 'selected' : '' }}>🟢 Enabled (Auctions active and open for live bidding)</option>
                <option value="0" {{ !$isEnabled ? 'selected' : '' }}>🔴 Paused (Auctions temporarily paused and bidding disabled)</option>
            </select>
            <p class="text-[11px] text-slate-400 mt-1">When paused, the wholesale floor displays an informative notice and bids are blocked.</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Default Minimum Bid Increment (₹ INR)</label>
            <input type="number" step="1" min="1" name="default_bid_increment" value="{{ \App\Models\SiteSetting::get('default_bid_increment', '50') }}" class="w-full px-3.5 py-2.5 text-xs font-semibold border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none bg-slate-50 font-mono">
            <p class="text-[11px] text-slate-400 mt-1">Minimum increment step required above the active highest bid.</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Anti-Sniping Buffer Extension (Seconds)</label>
            <input type="number" step="10" min="0" max="3600" name="auction_anti_snipe_seconds" value="{{ \App\Models\SiteSetting::get('auction_anti_snipe_seconds', '120') }}" class="w-full px-3.5 py-2.5 text-xs font-semibold border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 outline-none bg-slate-50 font-mono">
            <p class="text-[11px] text-slate-400 mt-1">Seconds appended to the end time when a competitive bid arrives within the final 2 minutes.</p>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Rules take effect across buyer and merchant nodes immediately.</span>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#F5A623] text-slate-950 font-bold text-xs hover:bg-amber-400 transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Save Auction Settings</span>
            </button>
        </div>
    </form>
</div>
@endsection
