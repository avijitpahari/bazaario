<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    <title>@yield('title', 'Support Specialist Workstation — Bazaario')</title>

    <!-- Compiled Tailwind CSS & App JS via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        @layer base {
            html, body { margin: 0; padding: 0; }
        }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0f172a; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>

    @stack('styles')
</head>

<body class="bg-[#090D16] text-slate-100 font-sans antialiased min-h-screen flex flex-col md:flex-row relative selection:bg-amber-500 selection:text-slate-950">

    <!-- Ambient Ops Glow Effects -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-32 left-1/4 w-[600px] h-[600px] bg-amber-500/5 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/3 -right-32 w-[500px] h-[500px] bg-emerald-500/5 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-32 left-1/3 w-[500px] h-[500px] bg-blue-500/5 rounded-full blur-[140px]"></div>
    </div>

    <!-- Global Toast Notifications -->
    <x-toast />

    @php
        $currentAgent = Auth::guard('user')->user() ?? Auth::user();
        $currentRoute = Route::currentRouteName() ?? '';
        $myActiveTicketsBadge = \App\Models\SupportTicket::where('assigned_agent_id', $currentAgent?->id)->whereNotIn('status', ['resolved', 'closed'])->count();
        $unassignedBadge = \App\Models\SupportTicket::whereNull('assigned_agent_id')->whereNotIn('status', ['resolved', 'closed'])->count();
        $chatsBadge = \App\Models\ChatConversation::whereIn('status', ['open', 'pending'])->count();
    @endphp

    <!-- ── LEFT WORKSTATION SIDEBAR ── -->
    <aside class="w-full md:w-72 bg-[#0C1220]/95 backdrop-blur-2xl border-r border-slate-800/80 flex flex-col shrink-0 relative z-20 shadow-2xl">
        <!-- Brand Header -->
        <div class="p-5 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500 text-slate-950 font-bold flex items-center justify-center shadow-lg shadow-amber-500/25 ring-1 ring-amber-300/40 shrink-0">
                    <span class="material-symbols-outlined text-[24px]">support_agent</span>
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="font-display font-extrabold text-white text-base tracking-tight block">BAZAARIO</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    </div>
                    <span class="text-[10px] font-mono uppercase tracking-wider text-amber-400/90 font-bold block">Specialist Desk</span>
                </div>
            </div>

            <span class="px-2.5 py-1 rounded-lg text-[10px] font-mono font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm">
                DESK v2.4
            </span>
        </div>

        <!-- Specialist Profile Badge -->
        <div class="p-3.5 mx-4 my-3.5 rounded-2xl bg-gradient-to-b from-slate-800/60 to-slate-900/80 border border-slate-700/60 shadow-inner">
            <div class="flex items-center gap-3">
                <div class="relative shrink-0">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-500/10 text-amber-400 border border-amber-500/40 font-bold flex items-center justify-center text-sm shadow-sm ring-2 ring-amber-400/20">
                        {{ $currentAgent?->initials }}
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-[#0C1220] {{ $currentAgent?->is_online ? 'bg-emerald-400 ring-2 ring-emerald-400/40 animate-pulse' : 'bg-slate-500' }}"></span>
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-1.5">
                        <span class="font-bold text-xs text-white truncate">{{ $currentAgent?->name }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="text-[10px] text-amber-400 font-mono font-semibold px-1.5 py-0.2 rounded bg-amber-500/10 border border-amber-500/20 truncate">
                            {{ $currentAgent?->staff_id ?? 'BZ-AGT-' . str_pad($currentAgent?->id ?? 1, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <div class="text-[10px] text-slate-400 truncate mt-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs text-slate-500">verified_user</span>
                        <span>{{ $currentAgent?->department ?? 'Order Inquiries & Disputes' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3.5 py-2 space-y-1.5 text-xs font-semibold overflow-y-auto">
            <!-- Workstation Dashboard -->
            <a href="{{ route('agent.dashboard') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ $currentRoute === 'agent.dashboard' ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-bold shadow-lg shadow-amber-500/25 ring-1 ring-amber-300/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] {{ $currentRoute === 'agent.dashboard' ? 'text-slate-950' : 'text-amber-400' }}">dashboard</span>
                    <span>Dashboard</span>
                </div>
                @if($currentRoute === 'agent.dashboard')
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-950"></span>
                @endif
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-mono font-bold uppercase text-slate-400 tracking-widest flex items-center justify-between">
                <span>Ticket Queues</span>
                <span class="text-[9px] text-slate-600">TRIAGE</span>
            </div>

            <!-- My Assigned Tickets -->
            <a href="{{ route('agent.tickets.index', ['view' => 'my_tickets']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ ($currentRoute === 'agent.tickets.index' && request('view', 'my_tickets') === 'my_tickets') ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-amber-400">assignment_ind</span>
                    <span>My Assigned</span>
                </div>
                @if($myActiveTicketsBadge > 0)
                    <span class="px-2 py-0.5 rounded-full bg-amber-500 text-slate-950 font-mono text-[10px] font-extrabold shadow-sm">
                        {{ $myActiveTicketsBadge }}
                    </span>
                @endif
            </a>

            <!-- Unassigned Pool -->
            <a href="{{ route('agent.tickets.index', ['view' => 'unassigned']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ ($currentRoute === 'agent.tickets.index' && request('view') === 'unassigned') ? 'bg-blue-500/20 text-blue-300 font-bold border border-blue-500/40 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-blue-400">move_to_inbox</span>
                    <span>Unassigned Pool</span>
                </div>
                @if($unassignedBadge > 0)
                    <span class="px-2 py-0.5 rounded-full bg-blue-500/25 text-blue-300 font-mono text-[10px] font-extrabold border border-blue-500/40">
                        {{ $unassignedBadge }}
                    </span>
                @endif
            </a>

            <!-- Escalated Disputes -->
            <a href="{{ route('agent.tickets.index', ['view' => 'escalated']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ ($currentRoute === 'agent.tickets.index' && request('view') === 'escalated') ? 'bg-rose-500/20 text-rose-300 font-bold border border-rose-500/40 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-rose-400">gavel</span>
                    <span>Escalated Disputes</span>
                </div>
                <span class="text-[9px] font-mono uppercase text-rose-400/80 bg-rose-500/10 px-1.5 py-0.2 rounded border border-rose-500/20">Admin</span>
            </a>

            <!-- All Active Tickets -->
            <a href="{{ route('agent.tickets.index', ['view' => 'all']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ ($currentRoute === 'agent.tickets.index' && request('view') === 'all') ? 'bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-slate-400">view_list</span>
                    <span>All Active Tickets</span>
                </div>
            </a>

            <!-- Resolved Archive -->
            <a href="{{ route('agent.tickets.index', ['view' => 'resolved']) }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ ($currentRoute === 'agent.tickets.index' && request('view') === 'resolved') ? 'bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40 shadow-sm' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] text-emerald-400">task_alt</span>
                    <span>Resolved Archive</span>
                </div>
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-mono font-bold uppercase text-slate-400 tracking-widest flex items-center justify-between">
                <span>Live Messaging</span>
                <span class="text-[9px] text-emerald-400/90 flex items-center gap-1 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SYNC</span>
                </span>
            </div>

            <!-- Live Chat Desk -->
            <a href="{{ route('agent.chats.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all {{ str_starts_with($currentRoute, 'agent.chats') ? 'bg-gradient-to-r from-emerald-500 to-teal-600 text-slate-950 font-bold shadow-lg shadow-emerald-500/25 ring-1 ring-emerald-300/40' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-[20px] {{ str_starts_with($currentRoute, 'agent.chats') ? 'text-slate-950' : 'text-emerald-400' }}">forum</span>
                    <span>Live Visitor Chats</span>
                </div>
                @if($chatsBadge > 0)
                    <span class="px-2 py-0.5 rounded-full {{ str_starts_with($currentRoute, 'agent.chats') ? 'bg-slate-950 text-emerald-400' : 'bg-emerald-500 text-slate-950' }} font-mono text-[10px] font-extrabold shadow-sm">
                        {{ $chatsBadge }}
                    </span>
                @endif
            </a>
        </nav>

        <!-- Availability Toggle & Logout Footer -->
        <div class="p-4 border-t border-slate-800/80 bg-[#0A0F1A]/80 space-y-3">
            <form action="{{ route('agent.status.toggle') }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full px-3.5 py-2.5 rounded-xl text-xs font-semibold flex items-center justify-between transition-all {{ $currentAgent?->is_online ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 shadow-sm shadow-emerald-500/10' : 'bg-slate-800/90 text-slate-400 border border-slate-700 hover:text-white' }}">
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex h-2.5 w-2.5">
                            @if($currentAgent?->is_online)
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            @endif
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $currentAgent?->is_online ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                        </span>
                        <span class="font-bold tracking-tight">{{ $currentAgent?->is_online ? 'Workstation Online' : 'Workstation Offline' }}</span>
                    </div>
                    <span class="material-symbols-outlined text-sm opacity-70">sync</span>
                </button>
            </form>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-3 py-2 rounded-xl bg-slate-900/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 text-xs font-semibold flex items-center justify-center gap-2 border border-slate-800 hover:border-rose-500/40 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Sign Out Desk</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ── RIGHT MAIN CONTENT WORKSPACE ── -->
    <div class="flex-1 flex flex-col min-w-0 bg-[#090D16] relative z-10">
        <!-- Top Utility Bar -->
        <header class="h-16 bg-[#0C1220]/80 backdrop-blur-xl border-b border-slate-800/80 flex items-center justify-between px-6 md:px-8 sticky top-0 z-30">
            <div class="flex items-center gap-3">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-400"></span>
                </span>
                <div class="flex items-center gap-2 font-mono text-xs">
                    <span class="text-white font-semibold tracking-tight">Dispute &amp; Escalation Telemetry Rail</span>
                    <span class="text-slate-600">•</span>
                    <span class="text-emerald-400 font-bold uppercase tracking-wider text-[11px] px-2 py-0.5 rounded bg-emerald-500/10 border border-emerald-500/20">Active</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-900/80 border border-slate-800 text-[11px] font-mono text-slate-400">
                    <span class="material-symbols-outlined text-xs text-amber-400">schedule</span>
                    <span id="agentWorkstationClock">{{ now()->format('H:i:s') }} IST</span>
                </div>

                <a href="{{ route('home') }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-semibold flex items-center gap-2 border border-slate-700/80 transition-all shadow-sm group" title="Open Storefront in New Tab">
                    <span class="material-symbols-outlined text-[16px] text-amber-400 group-hover:scale-110 transition-transform">storefront</span>
                    <span>Storefront</span>
                    <span class="material-symbols-outlined text-[13px] text-slate-400">open_in_new</span>
                </a>
            </div>
        </header>

        <!-- Main Page View Content -->
        <main class="flex-1 p-5 md:p-8 overflow-y-auto">
            @yield('content')
        </main>
    </div>

    <script>
        // Real-time Station Clock
        function updateWorkstationClock() {
            const clockEl = document.getElementById('agentWorkstationClock');
            if (!clockEl) return;
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${hours}:${minutes}:${seconds} IST`;
        }
        setInterval(updateWorkstationClock, 1000);
    </script>

    @stack('scripts')
</body>
</html>
