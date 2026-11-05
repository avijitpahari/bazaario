@extends('layouts.seller')

@section('title', 'Merchant Reviews & Trust Score — Bazaario Seller Center')

@section('content')
@php
    $seller = Auth::guard('seller')->user() ?? Auth::user();
    $profile = $seller?->sellerProfile;
    $reviews = \App\Models\Review::whereHas('product', function($q) use ($seller) {
        $q->where('seller_id', $seller->id);
    })->with(['user', 'product'])->latest()->paginate(10);
@endphp

<div class="flex flex-col w-full pb-16 space-y-6">
    <div class="px-6 py-4 bg-white/80 backdrop-blur-md rounded-[14px] shadow-sm border border-[#E2DFD7]/60 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold font-heading text-slate-900">Buyer Reviews & Trust Score Audit</h1>
            <p class="text-xs text-slate-500">Reputation rating based on fulfillment accuracy, speed, and buyer reviews.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-mono text-xs font-bold">
                {{ $profile?->trust_score ?? '99.0' }}% Trust Score
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-sm space-y-4">
        @if($reviews->isEmpty())
            <div class="py-12 text-center text-xs text-slate-400">
                <p>No customer reviews received yet. High fulfillment speed and quality will generate verified customer feedback here.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                @foreach($reviews as $rev)
                    <div class="py-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900">{{ $rev->user?->name ?? 'Customer' }}</span>
                            <span class="text-amber-500 text-xs font-bold">★ {{ $rev->rating }} / 5.0</span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $rev->comment }}</p>
                        <span class="text-[10px] text-slate-400">On product: {{ $rev->product?->name }} • {{ $rev->created_at?->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
            <div class="pt-4">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
