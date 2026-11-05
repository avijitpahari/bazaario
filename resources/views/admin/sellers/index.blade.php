@extends('layouts.admin')

@section('title', 'Seller Operations Directory')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div class="flex flex-col">
            <div class="flex items-center gap-space-sm">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Sellers & Merchant Directory</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-900 font-label-sm text-[11px] font-bold border border-amber-200/60">Admin Central</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Oversee local farmers, artisans, retailers, and individual merchants across all regional dispatch hubs.
            </p>
        </div>
        <!-- Top Action Buttons -->
        <div class="flex items-center flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200/90 text-[#0F172A] font-body-md text-xs font-semibold shadow-2xs hover:bg-slate-50 transition-all cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span>Export Manifest</span>
            </button>
            <a href="{{ route('admin.sellers.approvals') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-all">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>KYC Approval Queue</span>
                @if(($metrics['pending'] ?? 0) > 0)
                    <span class="ml-1 px-2 py-0.5 rounded-full bg-slate-950 text-amber-400 font-mono text-[10px] font-bold animate-pulse">
                        {{ $metrics['pending'] }}
                    </span>
                @endif
            </a>
        </div>
    </div>

    <!-- Metrics Bar: 4 Primary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-md">
        <!-- Card 1 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between hover:shadow-xs transition-shadow">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Total Registered</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-700">
                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-['Outfit'] text-2xl font-black text-[#0F172A]">{{ number_format($metrics['total'] ?? 0) }}</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-200/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live DB
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Active Operating Ratio</span>
                <span class="font-semibold text-[#0F172A]">100% Onboarded</span>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between hover:shadow-xs transition-shadow">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Verified Merchants</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-['Outfit'] text-2xl font-black text-[#0F172A]">{{ number_format($metrics['approved'] ?? 0) }}</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold border border-emerald-200/60">
                    <span class="material-symbols-outlined text-[12px]">check</span> KYC Cleared
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="truncate">Kolkata, Contai, Kurseong</span>
                <span class="material-symbols-outlined text-[14px] text-emerald-600">check_circle</span>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between hover:shadow-xs transition-shadow">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Pending KYC Audit</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">pending_actions</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-['Outfit'] text-2xl font-black text-amber-600">{{ number_format($metrics['pending'] ?? 0) }}</span>
                @if(($metrics['pending'] ?? 0) > 0)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-[11px] font-bold border border-amber-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Action Required
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-medium">
                        Queue Cleared
                    </span>
                @endif
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Compliance Verification Desk</span>
                <a href="{{ route('admin.sellers.approvals') }}" class="font-bold text-amber-700 hover:text-amber-800 hover:underline inline-flex items-center gap-0.5">
                    <span>Review</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col justify-between hover:shadow-xs transition-shadow">
            <div class="flex items-start justify-between">
                <span class="font-label-md text-xs uppercase tracking-wider text-slate-500 font-semibold">Suspended Sellers</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">block</span>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="font-['Outfit'] text-2xl font-black text-[#0F172A]">{{ number_format($metrics['suspended'] ?? 0) }}</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-label-sm text-[10px] font-bold">
                    {{ ($metrics['suspended'] ?? 0) > 0 ? 'Review Needed' : '0% Policy Flags' }}
                </span>
            </div>
            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="truncate">Compliance Integrity</span>
                <span class="font-semibold text-emerald-600 flex items-center gap-0.5">
                    <span class="material-symbols-outlined text-[13px]">shield</span> Optimal
                </span>
            </div>
        </div>
    </div>

    <!-- Filter & Search Console -->
    <div class="p-4 rounded-xl bg-white border border-slate-200/90 shadow-2xs">
        <form action="{{ route('admin.sellers.index') }}" method="GET" class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
            <!-- Search Text -->
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[18px] text-slate-400 pointer-events-none">search</span>
                <input 
                    name="search"
                    value="{{ request('search') }}"
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] placeholder:text-slate-400 focus:outline-none focus:bg-white focus:border-[#F5A623] focus:ring-2 focus:ring-amber-500/10 transition-all font-body-md" 
                    placeholder="Search by Merchant Name, City, Phone, Email, or GSTIN..." type="text"/>
            </div>

            <!-- Filter Controls -->
            <div class="flex items-center flex-wrap gap-2 text-xs">
                <!-- Regional Hub -->
                <div class="relative">
                    <select name="city" class="pl-3 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] focus:bg-white cursor-pointer appearance-none">
                        <option value="">All Regional Hubs</option>
                        @foreach($cities as $c)
                            <option value="{{ $c }}" {{ request('city') == $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                </div>

                <!-- KYC Status -->
                <div class="relative">
                    <select name="status" class="pl-3 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] focus:bg-white cursor-pointer appearance-none">
                        <option value="">All KYC Status</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Verified & Active</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Audit</option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                </div>

                <!-- Seller Type -->
                <div class="relative">
                    <select name="seller_type" class="pl-3 pr-8 py-2 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 font-medium focus:outline-none focus:border-[#F5A623] focus:bg-white cursor-pointer appearance-none">
                        <option value="">All Seller Types</option>
                        <option value="Farmer" {{ request('seller_type') == 'Farmer' ? 'selected' : '' }}>Farmer</option>
                        <option value="Artisan" {{ request('seller_type') == 'Artisan' ? 'selected' : '' }}>Artisan</option>
                        <option value="Kirana Store" {{ request('seller_type') == 'Kirana Store' ? 'selected' : '' }}>Kirana Store</option>
                        <option value="Dark Store" {{ request('seller_type') == 'Dark Store' ? 'selected' : '' }}>Dark Store</option>
                        <option value="Individual" {{ request('seller_type') == 'Individual' ? 'selected' : '' }}>Individual</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[16px] text-slate-400 pointer-events-none">expand_more</span>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="px-4 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl font-semibold flex items-center gap-1.5 transition cursor-pointer shadow-2xs">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Apply</span>
                </button>

                <!-- Reset Button -->
                @if(request()->anyFilled(['search', 'city', 'status', 'seller_type']))
                    <a href="{{ route('admin.sellers.index') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-medium transition flex items-center gap-1" title="Reset Filters">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Sellers Data Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="font-headline-sm text-sm font-bold text-[#0F172A]">Registered Merchants Catalog</span>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono text-[11px] font-semibold">
                    {{ $sellers->total() }} recorded
                </span>
            </div>
            <div class="text-xs text-slate-500 font-body-sm">
                Showing page {{ $sellers->currentPage() }} of {{ $sellers->lastPage() }}
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full min-w-[1150px] text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] uppercase tracking-wider text-slate-500 font-label-md whitespace-nowrap">
                        <th class="py-3 px-4 min-w-[240px]">Merchant Name & ID</th>
                        <th class="py-3 px-4 min-w-[180px]">Contact Person</th>
                        <th class="py-3 px-4 min-w-[130px]">Regional Hub</th>
                        <th class="py-3 px-4 min-w-[120px] text-center">KYC Status</th>
                        <th class="py-3 px-4 min-w-[100px] text-center">Catalog</th>
                        <th class="py-3 px-4 min-w-[100px] text-center">Commission</th>
                        <th class="py-3 px-4 min-w-[130px] text-center">Trust Rating</th>
                        <th class="py-3 px-4 min-w-[210px] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($sellers as $seller)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Merchant Name & ID -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    @if($seller->logo_path)
                                        <div class="w-9 h-9 rounded-xl overflow-hidden shrink-0 border border-slate-200 bg-slate-50 shadow-2xs flex items-center justify-center">
                                            <img src="{{ asset('storage/' . $seller->logo_path) }}" alt="{{ $seller->shop_name }}" class="w-full h-full object-cover">
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 text-slate-950 flex items-center justify-center font-black text-xs shrink-0 shadow-2xs border border-amber-300/60 font-['Outfit']">
                                            {{ strtoupper(substr((string)($seller->shop_name ?? 'SL'), 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <a href="{{ route('admin.sellers.show', $seller->id) }}" class="font-bold text-[#0F172A] hover:text-amber-600 transition-colors truncate text-sm">
                                                {{ $seller->shop_name }}
                                            </a>
                                            @if($seller->seller_type)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/60">
                                                    {{ $seller->seller_type }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-400 font-mono">
                                            <span>#SLR-{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            <span>•</span>
                                            @if($seller->gstin && strlen($seller->gstin) >= 6)
                                                <span>GSTIN: {{ $seller->gstin }}</span>
                                            @elseif($seller->status === 'approved')
                                                <span class="text-emerald-600 font-semibold">GSTIN: Verified</span>
                                            @else
                                                <span class="text-amber-600">GSTIN: Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact Person -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-slate-400 text-[14px]">person</span>
                                    <span class="font-semibold text-slate-800 text-xs">{{ $seller->user?->name ?? 'Direct Merchant' }}</span>
                                </div>
                                @if($seller->user?->email)
                                    <a href="mailto:{{ $seller->user->email }}" class="text-[11px] text-slate-400 hover:text-amber-600 transition-colors block truncate max-w-[170px]">
                                        {{ $seller->user->email }}
                                    </a>
                                @endif
                                @if($seller->user?->phone)
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $seller->user->phone }}</div>
                                @endif
                            </td>

                            <!-- Regional Hub -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="inline-flex items-center gap-1 text-slate-700 text-xs font-medium">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                                    <span>{{ $seller->city ?? 'West Bengal' }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 pl-4.5">{{ $seller->state ?? 'India' }}</div>
                            </td>

                            <!-- KYC Status -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($seller->status === 'approved')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-bold border border-emerald-200/80 shadow-2xs">
                                        <span class="material-symbols-outlined text-[13px] text-emerald-600">verified</span>
                                        <span>Verified</span>
                                    </span>
                                @elseif($seller->status === 'pending')
                                    <a href="{{ route('admin.sellers.approvals', ['selected' => $seller->id]) }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-800 text-[11px] font-bold border border-amber-200/80 hover:bg-amber-100 hover:border-amber-300 transition shadow-2xs" 
                                       title="Click to review KYC documents">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        <span>Audit Queue</span>
                                    </a>
                                @elseif($seller->status === 'suspended')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200/80 shadow-2xs">
                                        <span class="material-symbols-outlined text-[13px]">block</span>
                                        <span>Suspended</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold border border-slate-200 shadow-2xs">
                                        <span>{{ ucfirst($seller->status) }}</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Live Catalog Items -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @php $count = $seller->products_count ?? ($seller->products ? $seller->products->count() : 0); @endphp
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/80 text-xs font-semibold shadow-2xs">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">inventory_2</span>
                                    <span>{{ $count }} {{ Str::plural('item', $count) }}</span>
                                </span>
                            </td>

                            <!-- Commission Rate -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <span class="inline-block px-2.5 py-1 rounded-lg bg-amber-50/70 border border-amber-200/70 text-amber-900 font-mono text-xs font-bold shadow-2xs">
                                    {{ number_format((float)($seller->commission_rate ?? 8.5), 1) }}%
                                </span>
                            </td>

                            <!-- Trust Rating -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if((float)($seller->trust_score ?? 0) > 0)
                                    <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-bold shadow-2xs">
                                        <span class="material-symbols-outlined text-[13px] text-amber-500 fill-1">star</span>
                                        <span>{{ number_format((float)$seller->trust_score, 1) }}%</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold border border-slate-200/80" title="Newly registered merchant - accumulating review score">
                                        <span class="material-symbols-outlined text-[13px] text-amber-500">auto_awesome</span>
                                        <span>New Merchant</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5 justify-end">
                                    @if($seller->status === 'pending')
                                        <a href="{{ route('admin.sellers.approvals', ['selected' => $seller->id]) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-[#F5A623] hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-2xs transition-all cursor-pointer"
                                           title="Review merchant registration and KYC documents">
                                            <span class="material-symbols-outlined text-[15px]">verified_user</span>
                                            <span>Audit KYC</span>
                                        </a>
                                    @else
                                        <!-- View Store -->
                                        <a href="{{ route('admin.sellers.show', $seller->id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
                                           title="View merchant profile and catalog">
                                            <span class="material-symbols-outlined text-[15px] text-slate-500">storefront</span>
                                            <span>View Store</span>
                                        </a>

                                        <!-- Suspend / Activate Toggle -->
                                        <form action="{{ route('admin.sellers.toggle-status', $seller->id) }}" method="POST" class="inline" onsubmit="return confirm('{{ $seller->status === 'suspended' ? 'Reactivate merchant \'' . addslashes($seller->shop_name) . '\'?' : 'Are you sure you want to suspend merchant \'' . addslashes($seller->shop_name) . '\'?' }}');">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition-colors cursor-pointer {{ $seller->status === 'suspended' ? 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200' }}"
                                                    title="{{ $seller->status === 'suspended' ? 'Reactivate Merchant Account' : 'Suspend Merchant Account' }}">
                                                <span class="material-symbols-outlined text-[15px]">{{ $seller->status === 'suspended' ? 'check_circle' : 'block' }}</span>
                                                <span>{{ $seller->status === 'suspended' ? 'Activate' : 'Suspend' }}</span>
                                            </button>
                                        </form>

                                        <!-- Impersonate -->
                                        @if($seller->user_id)
                                            <form action="{{ route('admin.impersonate', $seller->user_id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 border border-slate-200/80 transition-colors cursor-pointer" 
                                                        title="Log in to Seller Portal as {{ addslashes($seller->shop_name) }}">
                                                    <span class="material-symbols-outlined text-[16px]">switch_account</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300">store_mall_directory</span>
                                <p class="mt-2 font-headline-sm text-sm font-semibold text-slate-600">No sellers matching the selected criteria</p>
                                <p class="text-xs text-slate-400 mt-1">Try resetting your search query or regional filters.</p>
                                <a href="{{ route('admin.sellers.index') }}" class="mt-3 inline-block px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium">
                                    Reset Filters
                                </a>
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
