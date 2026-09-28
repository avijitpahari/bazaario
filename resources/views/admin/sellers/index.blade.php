@extends('layouts.admin')

@section('title', 'Seller Operations Directory')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Sellers</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[11px] font-bold border border-amber-200/60">Admin Central</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Manage local farmers, artisans, retailers, and individual merchants across all regional hubs.
            </p>
        </div>
        <!-- Top Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export Manifest</span>
            </button>
            <a href="{{ route('admin.sellers.approvals') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-[#0F172A] font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-all">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>KYC Approval Queue ({{ $metrics['pending'] ?? 4 }})</span>
            </a>
        </div>
    </div>

    <!-- Metrics Bar: 4 Primary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <!-- Card 1 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Registered</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ $metrics['total'] ?? 8 }}</span>
                <span class="font-label-sm text-xs text-emerald-700 font-semibold flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span> Live DB
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Active Operating Ratio</span>
                <span class="font-semibold text-[#0F172A]">100% Onboarded</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Verified Merchants</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-[#0F172A]">{{ $metrics['approved'] ?? 4 }}</span>
                <span class="font-label-sm text-xs text-emerald-600 font-medium">KYC Cleared</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="truncate">Kolkata, Jaipur, Varanasi</span>
                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Pending KYC Audit</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-amber-600">{{ $metrics['pending'] ?? 4 }}</span>
                <span class="font-label-sm text-xs text-amber-600 font-semibold">Action Required</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Shantipur, Canning, Kurseong</span>
                <a href="{{ route('admin.sellers.approvals') }}" class="font-bold text-amber-700 hover:underline">Review &rarr;</a>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Suspended Sellers</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">block</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-label-lg text-2xl font-bold text-slate-700">{{ $metrics['suspended'] ?? 0 }}</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-[10px] font-bold">0% Policy Flags</span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="truncate">Compliance Integrity</span>
                <span class="font-semibold text-emerald-600">Optimal</span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Console -->
    <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
        <form action="{{ route('admin.sellers.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">search</span>
                <input 
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#F5A623] transition-all font-body-md" 
                    placeholder="Search by Merchant Name, City, Phone, or GSTIN..." type="text"/>
            </div>
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <select name="city" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] cursor-pointer">
                    <option value="">All Regional Hubs</option>
                    @foreach($cities as $c)
                        <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] cursor-pointer">
                    <option value="">All KYC Status</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Verified & Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Audit</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-[#0F172A] text-white hover:bg-slate-800 rounded-xl font-semibold flex items-center gap-1.5 transition cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply</span>
                </button>

                @if(request()->anyFilled(['search', 'city', 'status']))
                    <a href="{{ route('admin.sellers.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition" title="Reset Filters">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sellers Data Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-label-sm text-[11px] uppercase tracking-wider border-b border-slate-100">
                        <th class="py-3 px-4">Merchant Name & ID</th>
                        <th class="py-3 px-4">Contact Person</th>
                        <th class="py-3 px-4">Regional Hub</th>
                        <th class="py-3 px-4 text-center">KYC Status</th>
                        <th class="py-3 px-4 text-center">Catalog</th>
                        <th class="py-3 px-4 text-center">Commission</th>
                        <th class="py-3 px-4 text-center">Trust Rating</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($sellers as $seller)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Merchant Name & ID -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-900 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($seller->shop_name, 0, 2)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <a href="{{ route('admin.sellers.show', $seller->id) }}" class="font-bold text-[#0F172A] hover:text-amber-700 transition-colors truncate text-sm">
                                            {{ $seller->shop_name }}
                                        </a>
                                        <span class="font-label-sm text-[10px] text-slate-400">
                                            #SLR-{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }} • GSTIN: {{ $seller->gstin ?? 'Verified' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Person -->
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-800">{{ $seller->user->name ?? 'N/A' }}</span>
                                <div class="text-[10px] text-slate-400">{{ $seller->user->email ?? '' }}</div>
                            </td>

                            <!-- Regional Hub -->
                            <td class="py-3.5 px-4 text-slate-600 font-medium">
                                {{ $seller->city ?? 'West Bengal' }}, {{ $seller->state ?? 'India' }}
                            </td>

                            <!-- KYC Status -->
                            <td class="py-3.5 px-4 text-center">
                                @if($seller->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-label-sm text-[10px] font-bold border border-emerald-200/60">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                                    </span>
                                @elseif($seller->status === 'pending')
                                    <a href="{{ route('admin.sellers.approvals', ['selected' => $seller->id]) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-label-sm text-[10px] font-bold border border-amber-200/60 hover:bg-amber-100 transition">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Audit Queue
                                    </a>
                                @elseif($seller->status === 'suspended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 font-label-sm text-[10px] font-bold border border-rose-200/60">
                                        Suspended
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-[10px] font-bold">
                                        {{ ucfirst($seller->status) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Live Catalog Items -->
                            <td class="py-3.5 px-4 text-center font-label-md font-bold">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                    {{ $seller->products ? $seller->products->count() : 0 }} items
                                </span>
                            </td>

                            <!-- Commission Rate -->
                            <td class="py-3.5 px-4 text-center font-label-md font-bold text-[#0F172A]">
                                {{ $seller->commission_rate ?? 8.5 }}%
                            </td>

                            <!-- Rating -->
                            <td class="py-3.5 px-4 text-center">
                                <span class="text-amber-600 font-bold font-label-md text-xs">
                                    ★ {{ number_format($seller->trust_score, 1) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($seller->status === 'pending')
                                        <a href="{{ route('admin.sellers.approvals', ['selected' => $seller->id]) }}" class="px-2.5 py-1 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-[11px] font-bold transition-colors">
                                            Audit KYC
                                        </a>
                                    @else
                                        <a href="{{ route('admin.sellers.show', $seller->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-[11px] font-semibold transition-colors">
                                            Dossier
                                        </a>
                                        <!-- Suspend / Re-activate Form -->
                                        <form action="{{ route('admin.sellers.toggle-status', $seller->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 text-[11px] font-semibold transition cursor-pointer">
                                                {{ $seller->status === 'suspended' ? 'Activate' : 'Suspend' }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300">store_mall_directory</span>
                                <p class="mt-2 font-headline-sm text-sm font-semibold text-slate-600">No sellers matching the selected criteria</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sellers->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500 font-label-sm">
                    Showing {{ $sellers->firstItem() }} to {{ $sellers->lastItem() }} of {{ $sellers->total() }} merchants
                </div>
                <div>
                    {{ $sellers->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
