@extends('layouts.seller')

@section('title', 'Customer Inquiries & Support — Bazaario Seller Center')

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Top Header & Filter Controls -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5 pt-2 pb-5 border-b border-slate-200/80">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="text-[11px] font-bold tracking-wider text-amber-700 uppercase font-mono">Store Customer Care</span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                <span class="text-[11px] font-medium text-slate-500">Buyer Inquiries &amp; Resolution Desk</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold font-heading text-slate-900 tracking-tight">
                Customer Inquiries &amp; Support
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 max-w-2xl">
                Respond to customer inquiries, resolve product and shipping issues promptly, and avoid dispute escalations.
            </p>
        </div>

        <!-- Filter Presets -->
        <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-2xs self-start lg:self-center">
            <a href="{{ route('seller.support.index') }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs transition-all {{ is_null($statusFilter) ? 'bg-white text-slate-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium' }}">
                All ({{ $totalCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'open']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs transition-all {{ $statusFilter === 'open' ? 'bg-white text-amber-900 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium' }}">
                Action Required ({{ $openCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'escalated']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs transition-all {{ $statusFilter === 'escalated' ? 'bg-white text-rose-700 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium' }}">
                Escalated ({{ $escalatedCount }})
            </a>
            <a href="{{ route('seller.support.index', ['status' => 'resolved']) }}" 
               class="px-3.5 py-1.5 rounded-lg text-xs transition-all {{ $statusFilter === 'resolved' ? 'bg-white text-emerald-800 shadow-2xs font-bold' : 'text-slate-600 hover:text-slate-900 font-medium' }}">
                Resolved ({{ $resolvedCount }})
            </a>
        </div>
    </div>

    <!-- KPI Metric Summary Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase text-slate-500 font-mono tracking-wider">TOTAL INQUIRIES</span>
                <span class="material-symbols-outlined text-[20px] text-slate-400">forum</span>
            </div>
            <div class="text-2xl font-black font-heading text-slate-900 mt-2">{{ $totalCount }}</div>
            <div class="text-[11px] text-slate-500 mt-1">Across all store orders</div>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase text-amber-700 font-mono tracking-wider">NEEDS SELLER ACTION</span>
                <span class="material-symbols-outlined text-[20px] text-amber-500">pending_actions</span>
            </div>
            <div class="text-2xl font-black font-heading text-amber-600 mt-2">{{ $openCount }}</div>
            <div class="text-[11px] text-amber-800 mt-1">Awaiting merchant reply</div>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase text-rose-700 font-mono tracking-wider">ESCALATED DISPUTES</span>
                <span class="material-symbols-outlined text-[20px] text-rose-500">warning</span>
            </div>
            <div class="text-2xl font-black font-heading text-rose-600 mt-2">{{ $escalatedCount }}</div>
            <div class="text-[11px] text-rose-800 mt-1">Escrow hold active</div>
        </div>

        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-2xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase text-emerald-700 font-mono tracking-wider">RESOLVED CASES</span>
                <span class="material-symbols-outlined text-[20px] text-emerald-500">task_alt</span>
            </div>
            <div class="text-2xl font-black font-heading text-emerald-600 mt-2">{{ $resolvedCount }}</div>
            <div class="text-[11px] text-emerald-800 mt-1">Cleared customer inquiries</div>
        </div>
    </div>

    <!-- Inquiries List / Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-500 text-[20px]">inbox</span>
                <h2 class="font-heading text-base font-bold text-slate-900">Communication Threads</h2>
            </div>
            <span class="text-xs text-slate-500 font-mono">Showing {{ $tickets->count() }} of {{ $totalCount }} tickets</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/90 text-slate-500 font-semibold uppercase tracking-wider text-[10px] font-mono border-b border-slate-200/80">
                        <th class="py-3.5 px-4">Ticket &amp; Customer</th>
                        <th class="py-3.5 px-4">Category</th>
                        <th class="py-3.5 px-4">Linked Order</th>
                        <th class="py-3.5 px-4">Priority</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Last Updated</th>
                        <th class="py-3.5 px-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-900">{{ $ticket->ticket_number }}</span>
                                    <span class="font-semibold text-slate-800 mt-0.5">{{ $ticket->subject }}</span>
                                    <span class="text-[11px] text-slate-500">Buyer: {{ $ticket->user->name ?? 'Customer' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="text-slate-700 font-medium">{{ $ticket->category_label }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($ticket->order)
                                    <a href="{{ route('seller.orders.show', $ticket->order_id) }}" class="font-mono text-amber-700 font-bold hover:underline">
                                        #{{ $ticket->order->order_number }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-mono">General</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                                    {{ $ticket->priority }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ticket->status_color }}">
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('seller.support.tickets.show', $ticket) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-semibold text-xs transition-colors shadow-2xs">
                                    <span>Reply</span>
                                    <span class="material-symbols-outlined text-[15px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-xs text-slate-500">
                                <span class="material-symbols-outlined text-4xl text-slate-300 block mb-2">mark_chat_read</span>
                                <span class="font-medium text-slate-700 text-sm block">No buyer inquiries requiring attention</span>
                                <span class="text-slate-400 text-xs mt-1 block">Great job keeping orders on schedule!</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
