@extends('layouts.seller')

@section('title', 'Customer Inquiries & Support — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Top Header & Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-3 border-b border-[#E2DFD7]/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-bold tracking-wider text-amber-700 uppercase font-mono">Store Customer Care</span>
                <span class="w-1.5 h-1.5 rounded-full bg-outline-variant"></span>
                <span class="text-[11px] font-medium text-on-surface-variant">Buyer Inquiries &amp; Resolution Desk</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-on-surface tracking-tight">
                Customer Inquiries &amp; Support
            </h1>
            <p class="text-xs sm:text-sm text-on-surface-variant mt-0.5">
                Respond to customer inquiries, resolve product and shipping issues promptly, and avoid dispute escalations.
            </p>
        </div>

        <!-- Filter Presets -->
        <div class="inline-flex items-center p-1 bg-surface-container rounded-xl border border-outline-variant/40 shadow-xs">
            <a href="{{ route('seller.support.index') }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ is_null($statusFilter) ? 'bg-surface-container-lowest text-on-surface shadow-xs font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                All ({{ $totalCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'open']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'open' ? 'bg-surface-container-lowest text-on-surface shadow-xs font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                Action Required ({{ $openCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'escalated']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'escalated' ? 'bg-surface-container-lowest text-rose-700 shadow-xs font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                Escalated ({{ $escalatedCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'resolved']) }}" 
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'resolved' ? 'bg-surface-container-lowest text-on-surface shadow-xs font-bold' : 'text-on-surface-variant hover:text-on-surface' }}">
                Resolved ({{ $resolvedCount }})
            </a>
        </div>
    </div>

    <!-- KPI Metric Summary Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
            <div class="text-[10px] font-bold uppercase text-on-surface-variant font-mono">TOTAL INQUIRIES</div>
            <div class="text-2xl font-black font-heading text-on-surface mt-1">{{ $totalCount }}</div>
            <div class="text-[11px] text-on-surface-variant mt-0.5">Across all store orders</div>
        </div>

        <div class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
            <div class="text-[10px] font-bold uppercase text-amber-700 font-mono">NEEDS SELLER ACTION</div>
            <div class="text-2xl font-black font-heading text-amber-600 mt-1">{{ $openCount }}</div>
            <div class="text-[11px] text-amber-800 mt-0.5">Awaiting merchant reply</div>
        </div>

        <div class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
            <div class="text-[10px] font-bold uppercase text-rose-700 font-mono">ESCALATED DISPUTES</div>
            <div class="text-2xl font-black font-heading text-rose-600 mt-1">{{ $escalatedCount }}</div>
            <div class="text-[11px] text-rose-800 mt-0.5">Escrow hold active</div>
        </div>

        <div class="p-4 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
            <div class="text-[10px] font-bold uppercase text-emerald-700 font-mono">RESOLVED CASES</div>
            <div class="text-2xl font-black font-heading text-emerald-600 mt-1">{{ $resolvedCount }}</div>
            <div class="text-[11px] text-emerald-800 mt-0.5">Cleared customer inquiries</div>
        </div>
    </div>

    <!-- Inquiries List / Table Card -->
    <div class="p-5 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-outline-variant/40 text-on-surface-variant font-semibold uppercase tracking-wider text-[10px] font-mono">
                        <th class="py-3 px-3">Ticket &amp; Customer</th>
                        <th class="py-3 px-3">Category</th>
                        <th class="py-3 px-3">Linked Order</th>
                        <th class="py-3 px-3">Priority</th>
                        <th class="py-3 px-3">Status</th>
                        <th class="py-3 px-3">Last Updated</th>
                        <th class="py-3 px-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-surface-container/40 transition-colors">
                            <td class="py-3 px-3">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-on-surface">{{ $ticket->ticket_number }}</span>
                                    <span class="font-bold text-slate-800 mt-0.5">{{ $ticket->subject }}</span>
                                    <span class="text-[10px] text-on-surface-variant">Buyer: {{ $ticket->user->name ?? 'Customer' }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <span class="text-on-surface font-medium">{{ $ticket->category_label }}</span>
                            </td>
                            <td class="py-3 px-3">
                                @if($ticket->order)
                                    <a href="{{ route('seller.orders.show', $ticket->order_id) }}" class="font-mono text-secondary font-bold hover:underline">
                                        #{{ $ticket->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">General</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ticket->status_color }}">
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-on-surface-variant font-mono text-[11px]">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="py-3 px-3 text-right">
                                <a href="{{ route('seller.support.tickets.show', $ticket) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-on-surface text-surface hover:bg-slate-800 font-semibold text-[11px] transition-colors shadow-2xs">
                                    <span>Reply</span>
                                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-on-surface-variant">
                                <span class="material-symbols-outlined text-3xl text-outline-variant block mb-1">mark_chat_read</span>
                                <span>No buyer inquiries requiring attention. Great job keeping orders on schedule!</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="mt-4 pt-3 border-t border-outline-variant/30">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
