@extends('layouts.user')

@section('title', 'My Invoices — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-slate-authority font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span> Billing
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Invoices</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Download and manage your purchase invoices.</p>
        </div>
    </div>

    <div class="flex flex-col gap-3">
        @forelse(auth()->user()->orders()->where('payment_status', 'paid')->latest('placed_at')->get() as $order)
        <div class="p-4 rounded-2xl bg-surface-container-lowest/90 shadow-sm border border-white/60 flex items-center justify-between gap-4">
            <div>
                <p class="font-body-small text-body-small font-bold text-slate-authority">#{{ $order->order_number }}</p>
                <p class="font-label-micro text-label-micro text-on-surface-variant">{{ $order->placed_at ? $order->placed_at->format('d M Y') : $order->created_at->format('d M Y') }} • ₹{{ number_format($order->total_amount, 2) }}</p>
            </div>
            <a href="{{ route('user.orders.show', $order->id) }}" class="px-4 py-2 rounded-xl bg-surface-container text-slate-authority font-button-text text-body-small font-semibold hover:bg-surface-container-high transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">visibility</span> View
            </a>
        </div>
        @empty
        <div class="flex flex-col items-center justify-center py-16 gap-4 text-center">
            <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-[42px] text-on-surface-variant">receipt_long</span>
            </div>
            <h2 class="font-title-card text-title-card font-bold text-slate-authority">No invoices yet</h2>
            <p class="font-body-small text-body-small text-on-surface-variant">Invoices are generated after successful payment.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection