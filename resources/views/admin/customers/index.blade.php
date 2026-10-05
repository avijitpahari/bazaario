@extends('layouts.admin')

@section('title', 'Customer Directory & Trust Dossier')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Customer Directory</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-cyan-100 text-cyan-900 font-mono text-[11px] font-bold border border-cyan-200/60">Trust & Identity</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Inspect registered buyers, trust scores, lifetime spend, order history, and fraud prevention signals.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 font-mono text-xs font-bold text-[#0F172A]">
                {{ $stats['total'] }} Registered Buyers
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 font-mono text-xs font-bold">
                {{ $stats['active'] }} Active
            </span>
            @if($stats['suspended'] > 0)
                <span class="px-3 py-1.5 rounded-xl bg-red-50 text-red-700 border border-red-200 font-mono text-xs font-bold">
                    {{ $stats['suspended'] }} Suspended
                </span>
            @endif
        </div>
    </div>

    <!-- Filters & Search Bar -->
    <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex-1 flex flex-col sm:flex-row items-center gap-3">
            <div class="relative w-full sm:max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search customer by name, email, or phone..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-body-sm"
                />
            </div>
            <button type="submit" class="px-4 py-2 bg-[#0F172A] text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors shrink-0">
                Search
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 shrink-0">
                    Clear Filters
                </a>
            @endif
        </form>

        <div class="flex items-center gap-1.5 shrink-0">
            <a href="{{ route('admin.customers.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ !request('status') ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                All
            </a>
            <a href="{{ route('admin.customers.index', ['status' => 'active']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ request('status') === 'active' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Active
            </a>
            <a href="{{ route('admin.customers.index', ['status' => 'suspended']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border {{ request('status') === 'suspended' ? 'bg-red-600 text-white border-red-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-50' }}">
                Suspended
            </a>
        </div>
    </div>

    <!-- Customers Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-mono text-[10px] uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Customer Name & ID</th>
                        <th class="py-3 px-4">Email Address</th>
                        <th class="py-3 px-4">Phone Number</th>
                        <th class="py-3 px-4 text-center">Orders</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4">Joined Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr((string)($customer->name ?? 'CU'), 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-[#0F172A] block">{{ $customer->name }}</span>
                                        <span class="font-mono text-[10px] text-slate-400">#CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $customer->email }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $customer->phone ?? 'Not provided' }}</td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-[#0F172A]">
                                {{ $customer->orders_count }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($customer->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200/60">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-red-50 text-red-700 font-mono text-[10px] font-bold border border-red-200/60">Suspended</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-400">
                                {{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold transition-colors">
                                        Dossier
                                    </a>
                                    <form method="POST" action="{{ route('admin.customers.toggle-status', $customer->id) }}" onsubmit="return confirm('Toggle account status for {{ addslashes($customer->name) }}?');" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ $customer->status === 'suspended' ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-red-50 text-red-700 hover:bg-red-100' }}">
                                            {{ $customer->status === 'suspended' ? 'Activate' : 'Suspend' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl mb-1 text-slate-300">group_off</span>
                                <p>No customers match the current search filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $customers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
