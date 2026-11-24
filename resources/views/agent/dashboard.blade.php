@extends('layouts.agent')

@section('title', 'Workstation Dashboard — Bazaario Support Specialist')

@section('content')
<div class="space-y-7">
    <!-- Hero Banner with Cyber-Ops Telemetry -->
    <div class="relative overflow-hidden rounded-3xl p-6 md:p-8 bg-gradient-to-r from-[#0F172A] via-[#121B30] to-[#1A162B] border border-slate-700/60 shadow-2xl group">
        <!-- Ambient Background Glow & Decorative Grid -->
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-amber-500/10 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-72 h-72 bg-emerald-500/10 rounded-full blur-[80px] pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 tracking-wider shadow-sm flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        <span>SPECIALIST DESK</span>
                    </span>
                    <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-slate-800/80 text-slate-300 border border-slate-700/80">
                        STAFF ID: {{ $agent->staff_id ?? 'BZ-AGT-' . str_pad($agent->id, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono text-slate-400 border border-slate-800 hidden sm:inline-block">
                        SHIFT: DAY ALPHA • SLA TARGET &lt; 15M
                    </span>
                </div>

                <h1 class="text-2xl md:text-3xl font-display font-extrabold text-white tracking-tight">
                    Welcome back, <span class="bg-gradient-to-r from-white via-slate-100 to-amber-200 bg-clip-text text-transparent">{{ $agent->name }}</span>
                </h1>

                <p class="text-xs text-slate-300/80 max-w-2xl leading-relaxed">
                    Unit: <strong class="text-amber-300/90 font-semibold">{{ $agent->department ?? 'Order Inquiries & Disputes' }}</strong> • Review your assigned inquiries, claim pending dispute cases from the unassigned pool, or provide live assistance to active marketplace shoppers.
                </p>
            </div>

            <!-- Header Quick Controls -->
            <div class="flex items-center gap-3 shrink-0 self-stretch sm:self-auto flex-wrap">
                <form action="{{ route('agent.status.toggle') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 rounded-2xl font-bold text-xs flex items-center gap-2.5 transition-all shadow-md {{ $agent->is_online ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/25 hover:border-emerald-400/60' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:text-white' }}">
                        <span class="relative flex h-2 w-2">
                            @if($agent->is_online)
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            @endif
                            <span class="relative inline-flex rounded-full h-2 w-2 {{ $agent->is_online ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                        </span>
                        <span>{{ $agent->is_online ? 'Available for Assignment' : 'Status: Away / Offline' }}</span>
                    </button>
                </form>

                <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
                   class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-amber-500/25 hover:shadow-amber-500/40 hover:scale-[1.02] active:scale-[0.98] flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
                    <span>Claim Tickets ({{ $unassignedQueueCount }})</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 5 KPI Metrics Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- KPI 1: My Active Tickets -->
        <a href="{{ route('agent.tickets.index', ['view' => 'my_tickets']) }}" 
           class="p-5 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800 hover:border-amber-500/60 transition-all duration-200 flex items-center justify-between group shadow-lg hover:shadow-amber-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-amber-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-wider text-slate-400 group-hover:text-amber-400 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>My Active</span>
                </div>
                <div class="text-3xl font-display font-black text-white mt-1 group-hover:text-amber-400 transition-colors">{{ $myOpenTicketsCount }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Assigned to desk</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-400 border border-amber-500/30 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner">
                <span class="material-symbols-outlined text-[26px]">assignment_ind</span>
            </div>
        </a>

        <!-- KPI 2: Unassigned Pool -->
        <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
           class="p-5 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800 hover:border-blue-500/60 transition-all duration-200 flex items-center justify-between group shadow-lg hover:shadow-blue-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-wider text-slate-400 group-hover:text-blue-400 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                    <span>Unassigned Pool</span>
                </div>
                <div class="text-3xl font-display font-black text-blue-400 mt-1">{{ $unassignedQueueCount }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Awaiting claim</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-400 border border-blue-500/30 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner">
                <span class="material-symbols-outlined text-[26px]">move_to_inbox</span>
            </div>
        </a>

        <!-- KPI 3: My Escalated -->
        <a href="{{ route('agent.tickets.index', ['view' => 'escalated']) }}" 
           class="p-5 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800 hover:border-rose-500/60 transition-all duration-200 flex items-center justify-between group shadow-lg hover:shadow-rose-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-rose-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-wider text-slate-400 group-hover:text-rose-400 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    <span>Escalated</span>
                </div>
                <div class="text-3xl font-display font-black text-rose-400 mt-1">{{ $myEscalatedCount }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Admin arbitration</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-rose-400 border border-rose-500/30 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner">
                <span class="material-symbols-outlined text-[26px]">gavel</span>
            </div>
        </a>

        <!-- KPI 4: Live Visitor Chats -->
        <a href="{{ route('agent.chats.index') }}" 
           class="p-5 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800 hover:border-emerald-500/60 transition-all duration-200 flex items-center justify-between group shadow-lg hover:shadow-emerald-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-wider text-slate-400 group-hover:text-emerald-400 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Live Chats</span>
                </div>
                <div class="text-3xl font-display font-black text-emerald-400 mt-1">{{ $liveActiveChatsCount }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Real-time sessions</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner">
                <span class="material-symbols-outlined text-[26px]">forum</span>
            </div>
        </a>

        <!-- KPI 5: My Resolved Archive -->
        <a href="{{ route('agent.tickets.index', ['view' => 'resolved']) }}" 
           class="p-5 rounded-2xl bg-[#0C1220]/90 backdrop-blur-md border border-slate-800 hover:border-purple-500/60 transition-all duration-200 flex items-center justify-between group shadow-lg hover:shadow-purple-500/10 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-[2px] bg-gradient-to-r from-transparent via-purple-500 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div>
                <div class="flex items-center gap-1.5 text-[10px] font-mono uppercase tracking-wider text-slate-400 group-hover:text-purple-400 transition-colors">
                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                    <span>Resolved</span>
                </div>
                <div class="text-3xl font-display font-black text-purple-300 mt-1">{{ $myResolvedCount }}</div>
                <div class="text-[10px] text-slate-500 font-mono mt-0.5">Closed case log</div>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-500/15 text-purple-400 border border-purple-500/30 flex items-center justify-center group-hover:scale-105 transition-transform shadow-inner">
                <span class="material-symbols-outlined text-[26px]">task_alt</span>
            </div>
        </a>
    </div>

    <!-- 2 Column Workstation Queues -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: My Active Tickets Queue -->
        <div class="rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl flex flex-col">
            <div class="p-4 md:p-5 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500/15 text-amber-400 flex items-center justify-center border border-amber-500/30">
                        <span class="material-symbols-outlined text-[18px]">assignment_ind</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                            <span>My Active Inquiries</span>
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                {{ $myTickets->count() }}
                            </span>
                        </h2>
                    </div>
                </div>
                <a href="{{ route('agent.tickets.index', ['view' => 'my_tickets']) }}" 
                   class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1 group transition-colors">
                    <span>View all queue</span>
                    <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </a>
            </div>

            <div class="divide-y divide-slate-800/60 flex-1">
                @forelse($myTickets as $t)
                <div class="p-4 hover:bg-slate-800/30 transition-colors flex items-start justify-between gap-3">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                {{ $t->ticket_number }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $t->status_color }}">
                                {{ str_replace('_', ' ', $t->status) }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $t->priority_color }}">
                                {{ $t->priority }}
                            </span>
                            @if($t->is_escalated)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">gavel</span>
                                    <span>Escalated</span>
                                </span>
                            @endif
                        </div>
                        <h3 class="text-xs font-bold text-white hover:text-amber-400 transition-colors truncate">
                            <a href="{{ route('agent.tickets.show', $t) }}">{{ $t->subject }}</a>
                        </h3>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <span>User: <strong class="text-slate-300">{{ $t->user?->name ?? 'Guest' }}</strong></span>
                            @if($t->order)
                                <span>•</span>
                                <span>Order: <strong class="font-mono text-slate-300">#{{ $t->order->order_number }}</strong></span>
                            @endif
                            <span>•</span>
                            <span class="font-mono text-[10px]">{{ $t->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <a href="{{ route('agent.tickets.show', $t) }}" 
                       class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold shrink-0 transition-colors flex items-center gap-1.5 border border-slate-700/80 shadow-sm">
                        <span>Open</span>
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                    </a>
                </div>
                @empty
                <!-- Elevated Modern Empty State -->
                <div class="p-10 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-amber-500/15 to-orange-500/5 border border-amber-500/30 text-amber-400 flex items-center justify-center shadow-lg shadow-amber-500/10 mb-3.5">
                        <span class="material-symbols-outlined text-3xl">task_alt</span>
                    </div>
                    <h3 class="font-display font-bold text-white text-sm">Queue Cleared — Excellent Work!</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">
                        All inquiries assigned to your desk have been responded to or resolved.
                    </p>
                    <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
                       class="mt-4 px-4 py-2 rounded-xl bg-amber-500/15 hover:bg-amber-500/25 text-amber-300 font-bold text-xs border border-amber-500/30 transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">pan_tool</span>
                        <span>Claim from Unassigned Pool →</span>
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Unassigned Queue Pool (Claimable) -->
        <div class="rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl flex flex-col">
            <div class="p-4 md:p-5 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/40">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-500/15 text-blue-400 flex items-center justify-center border border-blue-500/30">
                        <span class="material-symbols-outlined text-[18px]">move_to_inbox</span>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                            <span>Unassigned Pool</span>
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                {{ $unassignedTickets->count() }}
                            </span>
                        </h2>
                    </div>
                </div>
                <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
                   class="text-xs text-blue-400 hover:text-blue-300 font-semibold flex items-center gap-1 group transition-colors">
                    <span>View all pool</span>
                    <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </a>
            </div>

            <div class="divide-y divide-slate-800/60 flex-1">
                @forelse($unassignedTickets as $t)
                <div class="p-4 hover:bg-slate-800/30 transition-colors flex items-start justify-between gap-3">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-mono text-xs font-bold text-slate-300 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700/60">
                                {{ $t->ticket_number }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border {{ $t->priority_color }}">
                                {{ $t->priority }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono bg-slate-900/80 px-2 py-0.5 rounded border border-slate-800">
                                {{ $t->category_label }}
                            </span>
                        </div>
                        <h3 class="text-xs font-bold text-white hover:text-blue-400 transition-colors truncate">
                            <a href="{{ route('agent.tickets.show', $t) }}">{{ $t->subject }}</a>
                        </h3>
                        <div class="text-[11px] text-slate-400">
                            From: <strong class="text-slate-300">{{ $t->user?->name ?? 'Guest' }}</strong> • Opened <span class="font-mono text-[10px]">{{ $t->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <form action="{{ route('agent.tickets.claim', $t) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl bg-blue-500/15 hover:bg-blue-500 text-blue-400 hover:text-white font-bold text-xs transition-all flex items-center gap-1.5 border border-blue-500/30 shadow-sm">
                            <span class="material-symbols-outlined text-sm">pan_tool</span>
                            <span>Claim</span>
                        </button>
                    </form>
                </div>
                @empty
                <!-- Elevated Modern Empty State -->
                <div class="p-10 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-3xl bg-gradient-to-br from-blue-500/15 to-indigo-500/5 border border-blue-500/30 text-blue-400 flex items-center justify-center shadow-lg shadow-blue-500/10 mb-3.5">
                        <span class="material-symbols-outlined text-3xl">inbox</span>
                    </div>
                    <h3 class="font-display font-bold text-white text-sm">Unassigned Pool is Empty</h3>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">
                        All incoming buyer and merchant support inquiries have been claimed by the desk team.
                    </p>
                    <span class="mt-4 px-3 py-1 rounded-full text-[10px] font-mono text-slate-500 bg-slate-900/60 border border-slate-800">
                        Triage status: Nominal • 100% Claimed
                    </span>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Live Visitor Chats Snippet -->
    @if($liveChats->isNotEmpty())
    <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-400"></span>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-white tracking-tight flex items-center gap-2">
                        <span>Live Visitor Sessions Needing Attention</span>
                        <span class="px-2 py-0.2 rounded-full text-[10px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $liveChats->count() }} active
                        </span>
                    </h2>
                </div>
            </div>
            <a href="{{ route('agent.chats.index') }}" 
               class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold flex items-center gap-1 group transition-colors">
                <span>Open Live Chat Workstation</span>
                <span class="material-symbols-outlined text-sm group-hover:translate-x-0.5 transition-transform">chevron_right</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($liveChats as $c)
            @php
                $visitorTitle = $c->guest_name ?: ($c->user?->name ?: 'Website Visitor');
                $initials = strtoupper(substr($visitorTitle, 0, 2));
            @endphp
            <a href="{{ route('agent.chats.index', ['conversation_id' => $c->id]) }}" 
               class="p-4 rounded-2xl bg-gradient-to-b from-slate-800/40 to-slate-900/60 border border-slate-700/60 hover:border-emerald-500/60 transition-all duration-200 flex flex-col justify-between gap-3 group shadow-md hover:shadow-emerald-500/10">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold flex items-center justify-center text-xs shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <span class="font-bold text-xs text-white truncate block group-hover:text-emerald-300 transition-colors">
                                {{ $visitorTitle }}
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono block">
                                {{ $c->user ? 'Registered User' : 'Web Guest' }}
                            </span>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono text-slate-400 bg-slate-900/80 px-2 py-0.5 rounded border border-slate-800 shrink-0">
                        {{ $c->last_message_at ? $c->last_message_at->diffForHumans() : 'Just now' }}
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-[#090D16]/80 border border-slate-800">
                    <p class="text-[11px] text-slate-300 leading-relaxed line-clamp-2">
                        "{{ $c->messages->first()?->message ?: 'Visitor initiated a live support consultation...' }}"
                    </p>
                </div>

                <div class="flex items-center justify-between text-[10px] font-mono pt-2 border-t border-slate-800/80">
                    <span class="px-2 py-0.5 rounded font-bold uppercase {{ in_array($c->status, ['open', 'pending']) ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                        {{ $c->status }}
                    </span>
                    <span class="text-emerald-400 font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform">
                        <span>Join Session</span>
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
