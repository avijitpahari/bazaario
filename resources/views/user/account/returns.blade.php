@extends('layouts.user')

@section('title', 'Returns & Refunds — Bazaario')

@section('content')
<div class="max-w-container-max mx-auto px-gutter-md py-6 flex flex-col gap-6">

    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-card-white via-surface-container-low/80 to-surface-container/60 p-7 shadow-sm border border-white/90">
        <div class="relative z-10">
            <span class="font-label-eyebrow text-label-eyebrow uppercase tracking-widest text-error font-semibold flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">keyboard_return</span> Returns &amp; Protection
            </span>
            <h1 class="font-headline-section text-headline-section font-bold text-slate-authority mt-1">Returns &amp; Refunds</h1>
            <p class="font-body-small text-body-small text-on-surface-variant mt-1">Manage return requests, track inspections, and escrow refund releases.</p>
        </div>
    </div>

    @if(isset($returns) && count($returns) > 0)
        <div class="flex flex-col gap-4">
            @foreach($returns as $ret)
                <div class="p-5 rounded-2xl bg-card-white shadow-xs border border-slate-authority/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-surface-container flex items-center justify-center text-slate-authority shrink-0">
                            <span class="material-symbols-outlined text-[24px]">assignment_return</span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-authority">Return #RET-{{ $ret->id }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $ret->status === 'completed' ? 'bg-status-green/10 text-status-green' : 'bg-amber-action/15 text-amber-800' }}">
                                    {{ $ret->status }}
                                </span>
                            </div>
                            <p class="font-body-small text-xs text-on-surface-variant mt-0.5">
                                Reason: {{ $ret->reason }} • Requested {{ $ret->requested_at ? $ret->requested_at->format('d M Y') : $ret->created_at->format('d M Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="font-label-micro text-[10px] text-on-surface-variant uppercase block">Refund Amount</span>
                            <span class="font-mono font-bold text-sm text-slate-authority">₹{{ number_format($ret->refund_amount ?? 0, 2) }}</span>
                        </div>
                        <a href="{{ route('user.orders.show', $ret->order_id) }}" class="px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-slate-authority text-xs font-semibold transition-colors">
                            Order Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16 gap-6 text-center bg-card-white rounded-2xl border border-slate-authority/10">
            <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center">
                <span class="material-symbols-outlined text-[42px] text-on-surface-variant">package_2</span>
            </div>
            <div>
                <h2 class="font-title-card text-title-card font-bold text-slate-authority">No active return requests</h2>
                <p class="font-body-small text-body-small text-on-surface-variant max-w-sm mt-1">All your orders are protected by Bazaario's Escrow guarantee. You can initiate a return from your order details page if an item is damaged or not as described.</p>
            </div>
            <a href="{{ route('user.orders.index') }}" class="px-6 py-3 rounded-xl bg-slate-authority text-canvas-ivory font-button-text text-body-small font-semibold shadow-sm hover:bg-slate-authority/90 transition-all">
                View My Orders
            </a>
        </div>
    @endif
</div>
@endsection