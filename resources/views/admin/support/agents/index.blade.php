@extends('layouts.admin')

@section('title', 'Support Staff & Agents Management — ' . \App\Models\SiteSetting::getSiteName())

@section('content')
<div class="space-y-6">
    <!-- Top Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-5 sm:p-6 flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                    ACCESS GOVERNANCE
                </span>
                <span class="text-xs text-slate-400 font-mono">•</span>
                <span class="text-xs text-slate-500 font-mono font-medium">STAFF ROSTER &amp; DESK PERFORMANCE</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-[#0F172A] tracking-tight mt-1.5 flex items-center gap-2.5">
                <span class="material-symbols-outlined text-[#F5A623] text-3xl">badge</span>
                <span>Support Specialists &amp; Agents</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                Only platform administrators can provision support specialist accounts. Agents authenticate securely from the primary portal login and are routed directly into their dedicated workstation.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="{{ route('admin.support.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 hover:text-slate-900 text-xs font-semibold transition-colors flex items-center gap-2 border border-slate-200 shadow-2xs">
                <span class="material-symbols-outlined text-[18px] text-[#F5A623]">support_agent</span>
                <span>Unified Support Console</span>
            </a>
            <a href="{{ route('admin.support.agents.create') }}" 
               class="px-4 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-xs hover:shadow flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Add Support Agent</span>
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-between hover:border-amber-200 transition-colors">
            <div>
                <span class="text-[11px] font-mono uppercase text-slate-500 tracking-wider font-semibold">Total Agents</span>
                <div class="text-2xl font-black text-[#0F172A] mt-1">{{ $totalAgents }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">groups</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-between hover:border-emerald-200 transition-colors">
            <div>
                <span class="text-[11px] font-mono uppercase text-slate-500 tracking-wider font-semibold">Online Now</span>
                <div class="text-2xl font-black text-emerald-600 mt-1 flex items-center gap-2">
                    <span>{{ $onlineAgents }}</span>
                    @if($onlineAgents > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    @endif
                </div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">wifi</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-between hover:border-blue-200 transition-colors">
            <div>
                <span class="text-[11px] font-mono uppercase text-slate-500 tracking-wider font-semibold">Active Staff Tickets</span>
                <div class="text-2xl font-black text-blue-600 mt-1">{{ $activeStaffTickets }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">confirmation_number</span>
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex items-center justify-between hover:border-teal-200 transition-colors">
            <div>
                <span class="text-[11px] font-mono uppercase text-slate-500 tracking-wider font-semibold">Staff Resolved</span>
                <div class="text-2xl font-black text-teal-600 mt-1">{{ $totalStaffResolved }}</div>
            </div>
            <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 border border-teal-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl">task_alt</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-2xs flex flex-col md:flex-row items-center justify-between gap-3">
        <form action="{{ route('admin.support.agents.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative flex-1 min-w-[240px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search by name, email, staff ID or department..." 
                       class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition-colors">
            </div>

            @if($departments->isNotEmpty())
            <select name="department" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:border-amber-500 transition-colors">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept }}" {{ $department === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                @endforeach
            </select>
            @endif

            <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:border-amber-500 transition-colors">
                <option value="">All Statuses</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs shadow-2xs transition-colors flex items-center gap-1.5 cursor-pointer">
                <span class="material-symbols-outlined text-[16px]">filter_list</span>
                <span>Filter</span>
            </button>
            @if(!empty($search) || !empty($department) || !empty($status))
                <a href="{{ route('admin.support.agents.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-800 font-medium transition-colors">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Agent Roster Table -->
    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/90 text-slate-600 font-mono font-bold uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3.5 px-5">Specialist / Staff ID</th>
                        <th class="py-3.5 px-4">Department</th>
                        <th class="py-3.5 px-4">Account Status</th>
                        <th class="py-3.5 px-4">Workstation</th>
                        <th class="py-3.5 px-4">Active Tickets</th>
                        <th class="py-3.5 px-4">Resolved</th>
                        <th class="py-3.5 px-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($agents as $agt)
                    <tr class="hover:bg-amber-50/30 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 border border-amber-200 font-bold flex items-center justify-center shrink-0 uppercase text-xs shadow-2xs">
                                    {{ $agt->initials }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                        <span>{{ $agt->name }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                            {{ $agt->staff_id ?? 'BZ-AGT-' . str_pad($agt->id, 3, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $agt->email }} • {{ $agt->phone ?: 'No phone' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-4">
                            <span class="px-2.5 py-1 rounded-lg text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                {{ $agt->department ?: 'General Concierge' }}
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            @if($agt->status === 'active')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Active
                                </span>
                            @elseif($agt->status === 'inactive')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    Inactive
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Suspended
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-4">
                            @if($agt->is_online)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Online</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                    <span>Offline</span>
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-mono font-bold text-sm {{ $agt->active_tickets_count > 0 ? 'text-amber-600' : 'text-slate-400' }}">
                                {{ $agt->active_tickets_count }}
                            </span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-mono font-bold text-sm text-emerald-600">
                                {{ $agt->resolved_tickets_count }}
                            </span>
                        </td>

                        <td class="py-4 px-5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form action="{{ route('admin.support.agents.toggle', $agt) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2 rounded-xl text-slate-400 hover:text-amber-600 hover:bg-amber-50 transition-colors cursor-pointer" 
                                            title="{{ $agt->status === 'active' ? 'Deactivate Agent' : 'Activate Agent' }}">
                                        <span class="material-symbols-outlined text-[19px]">
                                            {{ $agt->status === 'active' ? 'toggle_on' : 'toggle_off' }}
                                        </span>
                                    </button>
                                </form>

                                <a href="{{ route('admin.support.agents.edit', $agt) }}" 
                                   class="p-2 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors cursor-pointer"
                                   title="Edit Agent Profile">
                                    <span class="material-symbols-outlined text-[19px]">edit</span>
                                </a>

                                <form action="{{ route('admin.support.agents.destroy', $agt) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('Are you sure you want to remove agent {{ $agt->name }}? Any active tickets assigned to them will safely revert to the unassigned queue.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors cursor-pointer"
                                            title="Delete Agent">
                                        <span class="material-symbols-outlined text-[19px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-16 px-6 text-center">
                            <div class="flex flex-col items-center justify-center max-w-md mx-auto">
                                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-600 flex items-center justify-center mb-4 shadow-2xs">
                                    <span class="material-symbols-outlined text-3xl">badge</span>
                                </div>
                                <h3 class="text-base font-bold text-slate-900 mb-1">No Support Specialists Found</h3>
                                <p class="text-xs text-slate-500 mb-6 leading-relaxed">
                                    Provision dedicated support agents to arbitrate tickets, manage customer queries, and operate the live support workstation.
                                </p>
                                <a href="{{ route('admin.support.agents.create') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-xs hover:shadow transition-all">
                                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                                    <span>+ Provision First Support Agent</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($agents->hasPages())
        <div class="p-4 border-t border-slate-200 bg-slate-50/60">
            {{ $agents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
