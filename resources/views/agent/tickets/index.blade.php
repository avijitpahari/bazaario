@extends('layouts.agent')

@section('title', 'Ticket Workstation — Bazaario Support Specialist')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 uppercase tracking-wider flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>TICKET QUEUE</span>
                </span>
                <span class="text-xs text-slate-400 font-mono">
                    {{ match($view) {
                        'my_tickets' => 'Assigned Desk: ' . $agent->name,
                        'unassigned' => 'Awaiting Specialist Claim',
                        'escalated' => 'Dispute Escalations & Arbitration',
                        'resolved' => 'Closed & Resolved History',
                        default => 'All Active Inquiries',
                    } }}
                </span>
            </div>
            <h1 class="text-2xl font-display font-extrabold text-white tracking-tight mt-1.5 flex items-center gap-2.5">
                <span class="material-symbols-outlined text-amber-400 text-3xl">confirmation_number</span>
                <span>Support Inquiries Workstation</span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900/90 border border-slate-800 text-xs font-mono text-slate-400">
                Total in view: <strong class="text-white font-bold">{{ $tickets->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- Tab Navigation Pills -->
    <div class="flex items-center gap-2.5 overflow-x-auto pb-1 text-xs">
        <a href="{{ route('agent.tickets.index', ['view' => 'my_tickets']) }}" 
           class="px-4 py-2.5 rounded-2xl font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $view === 'my_tickets' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 shadow-lg shadow-amber-500/20 ring-1 ring-amber-300/40' : 'bg-[#0C1220] text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700' }}">
            <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
            <span>My Assigned</span>
            @if($myTicketsCount > 0)
                <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-black {{ $view === 'my_tickets' ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-300' }}">{{ $myTicketsCount }}</span>
            @endif
        </a>

        <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
           class="px-4 py-2.5 rounded-2xl font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $view === 'unassigned' ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/20 ring-1 ring-blue-300/40' : 'bg-[#0C1220] text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700' }}">
            <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
            <span>Unassigned Pool</span>
            @if($unassignedCount > 0)
                <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-black {{ $view === 'unassigned' ? 'bg-slate-950 text-blue-300' : 'bg-slate-800 text-slate-300' }}">{{ $unassignedCount }}</span>
            @endif
        </a>

        <a href="{{ route('agent.tickets.index', ['view' => 'escalated']) }}" 
           class="px-4 py-2.5 rounded-2xl font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $view === 'escalated' ? 'bg-gradient-to-r from-rose-500 to-pink-600 text-white shadow-lg shadow-rose-500/20 ring-1 ring-rose-300/40' : 'bg-[#0C1220] text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700' }}">
            <span class="material-symbols-outlined text-[18px]">gavel</span>
            <span>Escalated Disputes</span>
            @if($escalatedCount > 0)
                <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-black {{ $view === 'escalated' ? 'bg-slate-950 text-rose-300' : 'bg-slate-800 text-slate-300' }}">{{ $escalatedCount }}</span>
            @endif
        </a>

        <a href="{{ route('agent.tickets.index', ['view' => 'all']) }}" 
           class="px-4 py-2.5 rounded-2xl font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $view === 'all' ? 'bg-slate-700 text-white shadow-md' : 'bg-[#0C1220] text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700' }}">
            <span class="material-symbols-outlined text-[18px]">view_list</span>
            <span>All Active</span>
            <span class="px-2 py-0.2 rounded-full text-[10px] font-mono {{ $view === 'all' ? 'bg-slate-950 text-slate-200' : 'bg-slate-800 text-slate-300' }}">{{ $allActiveCount }}</span>
        </a>

        <a href="{{ route('agent.tickets.index', ['view' => 'resolved']) }}" 
           class="px-4 py-2.5 rounded-2xl font-bold transition-all flex items-center gap-2 whitespace-nowrap {{ $view === 'resolved' ? 'bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg shadow-emerald-600/20 ring-1 ring-emerald-300/40' : 'bg-[#0C1220] text-slate-400 hover:text-white border border-slate-800 hover:border-slate-700' }}">
            <span class="material-symbols-outlined text-[18px]">task_alt</span>
            <span>Resolved Archive</span>
            <span class="px-2 py-0.2 rounded-full text-[10px] font-mono {{ $view === 'resolved' ? 'bg-slate-950 text-emerald-300' : 'bg-slate-800 text-slate-300' }}">{{ $resolvedCount }}</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800/80 shadow-md flex flex-col md:flex-row items-center justify-between gap-3">
        <form action="{{ route('agent.tickets.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <input type="hidden" name="view" value="{{ $view }}">

            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search ticket #, subject, customer, store..." 
                       class="w-full pl-10 pr-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all">
            </div>

            <select name="category" class="px-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all">
                <option value="">All Categories</option>
                @foreach($ticketCategories as $key => $label)
                    <option value="{{ $key }}" {{ $category === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="priority" class="px-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all">
                <option value="">All Priorities</option>
                <option value="urgent" {{ $priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                <option value="high" {{ $priority === 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ $priority === 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ $priority === 'low' ? 'selected' : '' }}>Low</option>
            </select>

            <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs transition-all shadow-md shadow-amber-500/20">
                Apply Filter
            </button>
            @if(!empty($search) || !empty($category) || !empty($priority))
                <a href="{{ route('agent.tickets.index', ['view' => $view]) }}" class="px-3.5 py-2 text-xs text-slate-400 hover:text-white transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Ticket Table -->
    <div class="rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-900/60 text-slate-400 font-mono uppercase tracking-wider border-b border-slate-800/80 text-[11px]">
                        <th class="py-4 px-5">Ticket / Subject</th>
                        <th class="py-4 px-5">Category</th>
                        <th class="py-4 px-5">Customer / Store</th>
                        <th class="py-4 px-5">Priority &amp; Status</th>
                        <th class="py-4 px-5">Assigned Agent</th>
                        <th class="py-4 px-5">Updated</th>
                        <th class="py-4 px-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($tickets as $t)
                    <tr class="hover:bg-slate-800/30 transition-colors group">
                        <td class="py-4 px-5">
                            <div class="space-y-1.5 max-w-sm">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                        {{ $t->ticket_number }}
                                    </span>
                                    @if($t->is_escalated)
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[10px]">gavel</span>
                                            <span>Dispute</span>
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-bold text-white group-hover:text-amber-400 transition-colors line-clamp-1">
                                    <a href="{{ route('agent.tickets.show', $t) }}">{{ $t->subject }}</a>
                                </h3>
                                @if($t->order)
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        Order: <strong class="text-slate-200">#{{ $t->order->order_number }}</strong>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="py-4 px-5">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-[#090D16] text-slate-300 border border-slate-800">
                                {{ $t->category_label }}
                            </span>
                        </td>

                        <td class="py-4 px-5">
                            <div>
                                <div class="font-bold text-white">{{ $t->user?->name ?? 'Direct Customer' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $t->user?->email }}</div>
                                @if($t->seller)
                                    <div class="text-[10px] text-amber-400/90 font-medium mt-0.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">store</span>
                                        <span>{{ $t->seller?->sellerProfile?->shop_name ?? $t->seller?->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <td class="py-4 px-5">
                            <div class="space-y-1.5">
                                <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $t->priority_color }}">
                                    {{ $t->priority }}
                                </span>
                                <div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->status_color }}">
                                        {{ str_replace('_', ' ', $t->status) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-5">
                            @if($t->assignedAgent)
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 text-[10px] font-bold flex items-center justify-center uppercase shrink-0 ring-1 ring-amber-400/30">
                                        {{ $t->assignedAgent->initials }}
                                    </span>
                                    <span class="font-semibold text-white text-[11px]">
                                        {{ $t->assignedAgent->id === $agent->id ? 'Me' : $t->assignedAgent->name }}
                                    </span>
                                </div>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-mono text-slate-500 bg-[#090D16] border border-slate-800">
                                    Unassigned
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-5 text-slate-400 text-[11px] font-mono">
                            {{ $t->updated_at->diffForHumans() }}
                        </td>

                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(empty($t->assigned_agent_id))
                                    <form action="{{ route('agent.tickets.claim', $t) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-3.5 py-1.5 rounded-xl bg-blue-500/15 hover:bg-blue-500 text-blue-400 hover:text-white font-bold text-xs transition-all flex items-center gap-1 border border-blue-500/30 shadow-sm">
                                            <span class="material-symbols-outlined text-sm">pan_tool</span>
                                            <span>Claim</span>
                                        </button>
                                    </form>
                                @endif

                                <a href="{{ route('agent.tickets.show', $t) }}" 
                                   class="px-4 py-2 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-white font-semibold text-xs transition-colors flex items-center gap-1.5 border border-slate-700 shadow-sm">
                                    <span>Work Case</span>
                                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-500 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-3xl">inbox</span>
                                </div>
                                <h4 class="font-display font-bold text-white text-sm">No support tickets found in this queue</h4>
                                <p class="text-xs text-slate-500 max-w-sm">
                                    Try adjusting your category, priority, or search filters to find relevant customer tickets.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
