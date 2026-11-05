@extends('layouts.user')

@section('title', 'Help & Customer Support — Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col gap-8">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 text-white p-8 sm:p-10 shadow-lg border border-slate-700/60">
        <div class="relative z-10 max-w-2xl flex flex-col gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-mono font-bold tracking-wider uppercase border border-amber-400/30 w-fit">
                <span class="material-symbols-outlined text-[16px]">support_agent</span>
                <span>Customer Care &amp; Resolution</span>
            </span>
            <h1 class="text-2xl sm:text-4xl font-black font-['Outfit'] tracking-tight text-white mt-1">
                How can we assist you today?
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mt-1">
                Track active orders, get help from verified merchants, manage product return inspections, and resolve inquiries with complete escrow buyer protection.
            </p>
            <div class="flex flex-wrap items-center gap-3 mt-4">
                <a href="{{ route('user.support.tickets.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition-all shadow-md">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Open Support Ticket</span>
                </a>
                <button type="button" 
                        onclick="if(window.bazaarioOpenChat) { window.bazaarioOpenChat(); } else { const btn = document.querySelector('#bazaario-support-chat button'); if(btn) btn.click(); }"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm border border-white/20 transition-all">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span>Start Live Chat</span>
                </button>
            </div>
        </div>

        <!-- Decorative background emblem -->
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none text-amber-400">
            <span class="material-symbols-outlined text-[240px]">shield_with_heart</span>
        </div>
    </div>

    <!-- Quick Assistance Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Orders -->
        <a href="{{ route('user.orders.index') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Track Orders &amp; Delivery</h3>
            <p class="text-xs text-slate-500 mt-1">Check real-time dispatch progress, live courier GPS status, and delivery slots.</p>
        </a>

        <!-- 2. Returns & Refunds -->
        <a href="{{ route('user.returns') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">keyboard_return</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Returns &amp; Refunds</h3>
            <p class="text-xs text-slate-500 mt-1">Track return package pickups, merchant quality checks, and escrow refunds.</p>
        </a>

        <!-- 3. Escrow Buyer Protection -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-2xl">verified_user</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Escrow Protection</h3>
            <p class="text-xs text-slate-500 mt-1">Funds stay safe in escrow until you inspect and approve your delivered goods.</p>
        </div>

        <!-- 4. Auction Inquiries -->
        <a href="{{ route('user.auctions') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-indigo-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Auction Settlements</h3>
            <p class="text-xs text-slate-500 mt-1">Inquire about winning bids, reserve prices, or hammer settlement checkouts.</p>
        </a>
    </div>

    <!-- Active Tickets & Inquiries Section -->
    <div id="tickets-section" class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold font-['Outfit'] text-slate-900">Your Support Tickets &amp; Inquiries</h2>
                <p class="text-xs text-slate-500">Track correspondence with vendors and customer care specialists.</p>
            </div>

            <!-- Filter tabs -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-xs self-start">
                <a href="{{ route('user.support.index') }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ is_null($statusFilter) ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    All ({{ $totalTickets }})
                </a>
                <a href="{{ route('user.support.index', ['status' => 'open']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'open' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Active ({{ $openTickets }})
                </a>
                <a href="{{ route('user.support.index', ['status' => 'resolved']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'resolved' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Resolved ({{ $resolvedCount ?? $resolvedTickets }})
                </a>
            </div>
        </div>

        @if($tickets->isNotEmpty())
            <div class="flex flex-col gap-3">
                @foreach($tickets as $ticket)
                    <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400/80 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-xl">confirmation_number</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $ticket->ticket_number }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ticket->status_color }}">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                                        {{ $ticket->priority }} Priority
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 hover:text-amber-700 transition-colors">
                                    <a href="{{ route('user.support.tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>Category: <strong>{{ $ticket->category_label }}</strong></span>
                                    @if($ticket->order)
                                        <span>•</span>
                                        <span>Order: <strong class="font-mono">{{ $ticket->order->order_number }}</strong></span>
                                    @endif
                                    @if($ticket->seller)
                                        <span>•</span>
                                        <span>Merchant: <strong>{{ $ticket->seller->sellerProfile->shop_name ?? $ticket->seller->name }}</strong></span>
                                    @endif
                                    <span>•</span>
                                    <span>Updated {{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                            @if($ticket->is_escalated)
                                <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                                    <span>Dispute Escalated</span>
                                </span>
                            @endif
                            <a href="{{ route('user.support.tickets.show', $ticket) }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors shadow-xs">
                                <span>View Thread</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="mt-2">
                    {{ $tickets->links() }}
                </div>
            </div>
        @else
            <div class="p-8 bg-white rounded-2xl border border-slate-200/90 text-center flex flex-col items-center justify-center gap-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-3xl">inbox</span>
                </div>
                <h3 class="text-base font-bold font-['Outfit'] text-slate-900">No support tickets found</h3>
                <p class="text-xs text-slate-500 max-w-md">
                    You currently have no {{ $statusFilter ? $statusFilter : '' }} tickets. Need help with an order or product?
                </p>
                <a href="{{ route('user.support.tickets.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-xs mt-1">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Create a Ticket</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Returns & Inspections Overview -->
    @if(isset($returns) && $returns->isNotEmpty())
        <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold font-['Outfit'] text-slate-900">Active Return &amp; Refund Requests</h2>
                    <p class="text-xs text-slate-500">Track seller inspection status and escrow refunds.</p>
                </div>
                <a href="{{ route('user.returns') }}" class="text-xs font-bold text-amber-600 hover:underline">
                    View All Returns
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($returns as $ret)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-xl bg-rose-50 text-rose-600 material-symbols-outlined text-xl">assignment_return</span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Return #RET-{{ $ret->id }}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">Reason: {{ $ret->reason }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ret->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $ret->status }}
                            </span>
                            <div class="text-xs font-bold text-slate-900 mt-1 font-mono">₹{{ number_format($ret->refund_amount ?? 0, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
