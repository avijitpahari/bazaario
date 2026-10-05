@extends('layouts.seller')

@section('title', 'Create Auction Listing — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full pb-16 max-w-4xl mx-auto" x-data="{
    startPrice: 1000,
    reservePrice: 1500,
    minIncrement: 100
}">

    <!-- Top Breadcrumb -->
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm mb-6 border border-[#E2DFD7]/60">
        <div class="flex items-center gap-2 text-xs font-mono text-brand-muted">
            <a href="{{ route('seller.dashboard') }}" class="hover:text-primary transition-colors">Seller Center</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a href="{{ route('seller.auctions.index') }}" class="hover:text-primary transition-colors">Auctions</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Create Auction</span>
        </div>
        <h1 class="font-heading text-2xl lg:text-3xl text-primary font-bold tracking-tight mt-2">Create Wholesale Auction Lot</h1>
        <p class="text-sm text-brand-muted">List a verified consignment of farm produce on the live spot-bidding exchange.</p>
    </div>

    <form method="POST" action="{{ route('seller.auctions.store') }}" class="bg-white rounded-[14px] p-6 lg:p-8 shadow-sm border border-[#E2DFD7]/60 flex flex-col gap-6">
        @csrf

        <!-- STEP 1: Product Selection -->
        <div class="flex flex-col gap-3 pb-6 border-b border-[#E2DFD7]/60">
            <div class="flex items-center justify-between">
                <span class="font-heading text-base font-bold text-primary">1. Select Certified Farm Product</span>
                <span class="font-mono text-xs text-brand-muted">Authenticated Inventory Only</span>
            </div>
            <select name="product_id" class="w-full h-12 px-4 bg-surface-container-low rounded-[12px] text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                <option value="" disabled selected>Select from your approved products...</option>
                @foreach($products ?? [] as $prod)
                    <option value="{{ $prod->id }}">{{ $prod->name }} (Available: {{ $prod->stock }} {{ $prod->unit_type }})</option>
                @endforeach
            </select>
            @error('product_id') <p class="text-xs text-error">{{ $message }}</p> @enderror
        </div>

        <!-- STEP 2: Pricing Structure -->
        <div class="flex flex-col gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <span class="font-heading text-base font-bold text-primary">2. Pricing Structure &amp; Reserve Floor</span>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Starting Price (₹)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="0.01" name="starting_price" value="{{ old('starting_price') }}" x-model="startPrice" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    </div>
                    @error('starting_price') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary flex items-center justify-between">
                        <span>Secret Reserve Price (₹)</span>
                        <span class="font-mono text-[10px] text-brand-muted">Optional</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="0.01" name="reserve_price" value="{{ old('reserve_price') }}" x-model="reservePrice" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm">
                    </div>
                    @error('reserve_price') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Minimum Increment (₹)</label>
                    <div class="relative flex items-center">
                        <span class="absolute left-3.5 font-mono text-sm text-brand-muted">₹</span>
                        <input type="number" step="1" name="minimum_increment" value="{{ old('minimum_increment', 100) }}" x-model="minIncrement" class="w-full h-11 pl-8 pr-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm font-bold text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    </div>
                    @error('minimum_increment') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="text-xs text-brand-muted">The secret reserve protects your consignment against low-demand sell-offs; bidding below this floor does not legally bind allocation.</p>
        </div>

        <!-- STEP 3: Timing & Duration -->
        <div class="flex flex-col gap-4 pb-6 border-b border-[#E2DFD7]/60">
            <span class="font-heading text-base font-bold text-primary">3. Time Window (APMC Synchronized)</span>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">Start Date &amp; Time</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    @error('starts_at') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-heading font-semibold text-primary">End Date &amp; Time</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at') }}" class="w-full h-11 px-4 bg-surface-container-lowest rounded-[12px] font-mono text-sm text-primary border border-[#E2DFD7] focus:border-brand-amber outline-none shadow-sm" required>
                    @error('ends_at') <p class="text-xs text-error">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Submit CTAs -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('seller.auctions.index') }}" class="h-11 px-5 rounded-[12px] bg-surface-container-low text-primary text-xs font-heading font-semibold hover:bg-surface-container transition-all">
                Cancel
            </a>
            <button type="submit" class="h-12 px-8 rounded-[12px] bg-brand-amber text-primary font-heading text-sm font-bold shadow-md hover:brightness-105 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">publish</span>
                <span>Schedule &amp; Launch Auction</span>
            </button>
        </div>
    </form>
</div>
@endsection
