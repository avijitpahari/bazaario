@extends('layouts.admin')

@section('title', 'Arbitration Dossier #' . $dispute->dispute_number . ' - Wholesale Auction Disputes')

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs">
        <div>
            <div class="flex items-center gap-space-sm text-xs">
                <a href="{{ route('admin.auctions.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">bolt</span>
                    <span>Auctions</span>
                </a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('admin.auctions.disputes.index') }}" class="text-slate-500 hover:text-slate-800 transition-colors">
                    Disputes Desk
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-mono text-rose-700 font-bold uppercase">#{{ $dispute->dispute_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 mt-1.5">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">
                    Dispute Dossier #{{ $dispute->dispute_number }}
                </h1>
                
                <!-- Status Badge -->
                @if($dispute->status === 'open')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                        ACTION REQUIRED
                    </span>
                @elseif($dispute->status === 'under_review')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        UNDER INVESTIGATION
                    </span>
                @elseif($dispute->status === 'resolved')
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">gavel</span>
                        ARBITRATED & RESOLVED
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-slate-100 text-slate-700 border border-slate-200 flex items-center gap-1">
                        DISMISSED
                    </span>
                @endif

                <!-- Priority Badge -->
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold uppercase border
                    {{ $dispute->priority === 'urgent' ? 'bg-rose-100 text-rose-900 border-rose-300' :
                       ($dispute->priority === 'high' ? 'bg-orange-100 text-orange-900 border-orange-300' :
                       ($dispute->priority === 'medium' ? 'bg-blue-50 text-blue-800 border-blue-200' : 'bg-slate-100 text-slate-700 border-slate-200')) }}">
                    Priority: {{ $dispute->priority }}
                </span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500 mt-1">
                Lodged on {{ $dispute->created_at ? $dispute->created_at->format('F d, Y \a\t H:i:s') : 'N/A' }} 
                • Cause: <span class="font-semibold text-slate-800">{{ $dispute->type_label }}</span>
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.auctions.disputes.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Back to Disputes</span>
            </a>
            @if($auction)
                <a href="{{ route('admin.auctions.show', $auction->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-2xs transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">gavel</span>
                    <span>View Lot #AUC-{{ $auction->id }}</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-center gap-3 text-sm">
            <span class="material-symbols-outlined text-rose-600">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-sm">
            <div class="font-bold mb-1 flex items-center gap-1.5">
                <span class="material-symbols-outlined text-rose-600 text-base">warning</span>
                <span>Please correct the errors below:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Dossier Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">

        <!-- Left Column: Case Details & History (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col gap-space-lg">

            <!-- Official Ruling Certificate Banner (If already arbitrated) -->
            @if(in_array($dispute->status, ['resolved', 'dismissed']))
                <div class="rounded-2xl p-6 border shadow-2xs {{ $dispute->status === 'resolved' ? 'bg-linear-to-br from-emerald-50 to-teal-50/40 border-emerald-300' : 'bg-linear-to-br from-slate-50 to-slate-100/50 border-slate-300' }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-2xs {{ $dispute->status === 'resolved' ? 'bg-emerald-600 text-white' : 'bg-slate-700 text-white' }}">
                                <span class="material-symbols-outlined text-2xl">gavel</span>
                            </div>
                            <div>
                                <span class="font-mono text-[10px] uppercase font-bold tracking-wider {{ $dispute->status === 'resolved' ? 'text-emerald-700' : 'text-slate-600' }}">
                                    Official Administrative Finding • Case Closed
                                </span>
                                <h3 class="text-xl font-bold text-[#0F172A] mt-0.5">
                                    {{ $dispute->ruling_label }}
                                </h3>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="px-2.5 py-1 rounded-full text-xs font-mono font-bold uppercase {{ $dispute->status === 'resolved' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-800' }}">
                                {{ strtoupper($dispute->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t {{ $dispute->status === 'resolved' ? 'border-emerald-200' : 'border-slate-200' }} grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs font-mono">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Arbitrated By</span>
                            <span class="font-bold text-slate-900 font-sans text-sm">{{ $dispute->arbitratedBy?->name ?? 'Executive Admin' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Date & Time</span>
                            <span class="font-bold text-slate-900">{{ $dispute->arbitrated_at ? $dispute->arbitrated_at->format('d M Y, H:i') : 'Recorded' }}</span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Refund Issued</span>
                            <span class="font-bold {{ $dispute->refund_amount > 0 ? 'text-emerald-700' : 'text-slate-700' }}">
                                ₹{{ number_format((float)$dispute->refund_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    @if($dispute->ruling_notes)
                        <div class="mt-4 p-4 rounded-xl bg-white/80 border {{ $dispute->status === 'resolved' ? 'border-emerald-200/80' : 'border-slate-200' }} text-xs text-slate-700 leading-relaxed font-sans">
                            <span class="font-mono text-[10px] uppercase font-bold text-slate-500 block mb-1">Administrative Justification & Ruling Findings:</span>
                            {{ $dispute->ruling_notes }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Complainant Claim & Grievance Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-600">article</span>
                        <h2 class="text-base font-bold text-[#0F172A]">Complainant Claim & Statement</h2>
                    </div>
                    <span class="font-mono text-xs text-slate-400">Lodged: {{ $dispute->created_at ? $dispute->created_at->diffForHumans() : '' }}</span>
                </div>

                <!-- Subject Header -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-mono uppercase text-slate-400 block font-bold">Claim Subject</span>
                    <h3 class="text-sm font-bold text-[#0F172A] mt-0.5">{{ $dispute->subject }}</h3>
                </div>

                <!-- Claimant Identity Box -->
                <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-sm font-mono shrink-0">
                            {{ strtoupper(substr($dispute->claimant?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <span class="font-bold text-slate-900 text-sm block">{{ $dispute->claimant?->name ?? 'User #' . $dispute->user_id }}</span>
                            <span class="text-xs text-slate-500 font-mono">{{ $dispute->claimant?->email ?? 'No email provided' }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($dispute->winner_id === $dispute->user_id)
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-amber-100 text-amber-800 border border-amber-200">
                                Hammer Winner / Buyer
                            </span>
                        @elseif($dispute->auction && $dispute->auction->seller && $dispute->auction->seller->user_id === $dispute->user_id)
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-purple-100 text-purple-800 border border-purple-200">
                                Lot Consignor / Seller
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold font-mono bg-slate-100 text-slate-700 border border-slate-200">
                                Participating Bidder
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Full Statement -->
                <div class="flex flex-col gap-1.5">
                    <span class="font-mono text-[10px] uppercase font-bold text-slate-400">Detailed Grievance & Legal Allegation</span>
                    <div class="p-4 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 leading-relaxed font-sans whitespace-pre-line">
                        {{ $dispute->claim_description }}
                    </div>
                </div>

                <!-- Evidence & Uploaded Files -->
                <div class="flex flex-col gap-2 pt-2 border-t border-slate-100">
                    <span class="font-mono text-[10px] uppercase font-bold text-slate-400">Attached Photographic / Digital Evidence</span>
                    @if(!empty($dispute->evidence_urls) && is_array($dispute->evidence_urls) && count($dispute->evidence_urls) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-1">
                            @foreach($dispute->evidence_urls as $idx => $url)
                                <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="group relative rounded-xl border border-slate-200 overflow-hidden bg-slate-50 aspect-video flex items-center justify-center hover:border-amber-400 transition-all">
                                    <img src="{{ $url }}" alt="Evidence {{ $idx + 1 }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" onerror="this.src='/images/placeholder.png';">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold gap-1">
                                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                                        <span>View</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 border border-dashed border-slate-200 text-center text-xs text-slate-400 font-mono">
                            No digital media attachments were uploaded with this dispute filing.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Auction Lot Context Card -->
            @if($auction)
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-600">gavel</span>
                            <h2 class="text-base font-bold text-[#0F172A]">Contested Auction Lot Details</h2>
                        </div>
                        <a href="{{ route('admin.auctions.show', $auction->id) }}" class="text-xs text-amber-700 hover:text-amber-800 font-bold flex items-center gap-1 font-mono">
                            <span>Open Lot Control Center</span>
                            <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <div class="w-20 h-20 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-4xl shrink-0 overflow-hidden">
                            @if($auction->product && $auction->product->image_url)
                                <img src="{{ $auction->product->image_url }}" alt="{{ $auction->product->name }}" class="w-full h-full object-cover">
                            @else
                                🏺
                            @endif
                        </div>
                        <div class="flex flex-col min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-mono text-xs text-amber-700 font-bold">LOT #AUC-{{ $auction->id }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="text-xs text-slate-500 font-mono uppercase">{{ $auction->product?->category?->name ?? 'GENERAL WHOLESALE' }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase
                                    {{ $auction->status === 'live' ? 'bg-amber-100 text-amber-800' :
                                       ($auction->status === 'ended' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $auction->status }}
                                </span>
                            </div>
                            <h3 class="text-base font-bold text-[#0F172A] mt-1">{{ $auction->product?->name ?? 'Consigned Wholesale Lot #' . $auction->product_id }}</h3>
                            <p class="text-xs text-slate-500 mt-1">
                                Consigned by <strong>{{ $auction->seller?->name ?? 'Consignor' }}</strong> 
                                ({{ $auction->seller?->email ?? 'N/A' }}).
                            </p>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-3 pt-3 border-t border-slate-100 text-xs font-mono">
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase block">Starting Price</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format((float)($auction->starting_price ?? 0), 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase block">Reserve Price</span>
                                    <span class="font-bold text-slate-800">₹{{ number_format((float)($auction->reserve_price ?? 0), 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase block">High Bid</span>
                                    <span class="font-bold text-amber-700 text-sm">₹{{ number_format((float)($auction->current_price ?? 0), 2) }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase block">Reserve Met?</span>
                                    <span class="font-bold {{ ($auction->current_price ?? 0) >= ($auction->reserve_price ?? 0) ? 'text-emerald-700' : 'text-amber-600' }}">
                                        {{ ($auction->current_price ?? 0) >= ($auction->reserve_price ?? 0) ? 'YES ✓' : 'BELOW RESERVE' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bidding Ladder & Audit History -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-[#0F172A]">Bidding Audit Ladder</h2>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">Recorded bids evaluated for shill bidding or runner-up re-award</p>
                        </div>
                        <span class="text-xs font-mono text-slate-500 font-bold">{{ $bids->count() }} Total Bids</span>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-200">
                                    <th class="py-2.5 px-3 font-bold">Rank</th>
                                    <th class="py-2.5 px-3 font-bold">Bidder Details</th>
                                    <th class="py-2.5 px-3 font-bold">Role / Standing</th>
                                    <th class="py-2.5 px-3 text-right font-bold">Bid Amount</th>
                                    <th class="py-2.5 px-3 text-right font-bold">Recorded At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @forelse($bids as $index => $bid)
                                    <tr class="{{ $index === 0 ? 'bg-amber-50/60 font-bold' : ($index === 1 ? 'bg-blue-50/40' : 'hover:bg-slate-50') }}">
                                        <td class="py-2.5 px-3">
                                            @if($index === 0)
                                                <span class="px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-bold text-[10px]">
                                                    #1 WINNER
                                                </span>
                                            @elseif($index === 1)
                                                <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold text-[10px]">
                                                    #2 RUNNER-UP
                                                </span>
                                            @else
                                                <span class="text-slate-400">#{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 font-sans">
                                            <span class="font-bold text-[#0F172A] block">{{ $bid->user?->name ?? 'User #' . $bid->user_id }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $bid->user?->email ?? '' }}</span>
                                        </td>
                                        <td class="py-2.5 px-3">
                                            @if($bid->user_id === $dispute->user_id)
                                                <span class="text-rose-700 font-bold text-[10px] font-mono">CLAIMANT</span>
                                            @elseif($bid->user_id === $auction->winner_id)
                                                <span class="text-emerald-700 font-bold text-[10px] font-mono">CURRENT HAMMER</span>
                                            @else
                                                <span class="text-slate-500 text-[10px] font-mono">Eligible Bidder</span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-amber-700 font-bold">
                                            ₹{{ number_format((float)($bid->amount ?? 0), 2) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-right text-slate-400 text-[11px]">
                                            {{ $bid->created_at ? $bid->created_at->format('M d, H:i:s') : 'Recorded' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400 font-sans">
                                            No bids placed on this auction lot.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: Arbitration Action & Escrow Stakes (4 Cols) -->
        <div class="lg:col-span-4 flex flex-col gap-space-lg">

            <!-- Escrow Stakes Summary Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                    <span class="font-mono text-xs uppercase font-bold text-slate-500">Escrow Capital Summary</span>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">lock</span>
                </div>

                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 flex flex-col items-center justify-center text-center">
                    <span class="font-mono text-[10px] uppercase font-bold text-rose-600">Frozen Contested Amount</span>
                    <span class="font-mono text-3xl font-bold text-rose-700 mt-0.5">
                        ₹{{ number_format((float)$dispute->contested_amount, 2) }}
                    </span>
                    <span class="text-[10px] text-rose-500 mt-1 font-mono">Locked under buyer protection escrow vault</span>
                </div>

                <div class="flex flex-col gap-2 text-xs font-mono pt-1">
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-500">Auction Current High:</span>
                        <span class="font-bold text-[#0F172A]">₹{{ number_format((float)($auction?->current_price ?? 0), 2) }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-500">Consignor / Seller:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[150px]">{{ $auction?->seller?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between border-b border-slate-100 pb-2">
                        <span class="text-slate-500">Winning Bidder:</span>
                        <span class="font-bold text-slate-800 truncate max-w-[150px]">{{ $dispute->winner?->name ?? $auction?->winner?->name ?? 'Unassigned' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Claimant:</span>
                        <span class="font-bold text-rose-700 truncate max-w-[150px]">{{ $dispute->claimant?->name ?? 'User #' . $dispute->user_id }}</span>
                    </div>
                </div>
            </div>

            <!-- Executive Arbitration Console Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2.5">
                    <span class="material-symbols-outlined text-amber-600">balance</span>
                    <h3 class="font-bold text-sm text-[#0F172A]">Executive Arbitration Panel</h3>
                </div>

                @if(in_array($dispute->status, ['open', 'under_review']))
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Select a binding administrative ruling below. Executing this ruling will update auction lot status, release or refund escrow funds, and log an entry into the audit feed.
                    </p>

                    <form method="POST" action="{{ route('admin.auctions.disputes.arbitrate', $dispute->id) }}" onsubmit="return confirm('Execute binding arbitration ruling? This will update the auction status and execute escrow funds movement.');" class="flex flex-col gap-4">
                        @csrf

                        <!-- Ruling Radios -->
                        <div class="flex flex-col gap-2">
                            <label class="font-mono text-[10px] uppercase font-bold text-slate-500">Select Arbitration Ruling *</label>

                            <!-- Option 1: Full Refund to Buyer -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 cursor-pointer transition-all has-checked:border-emerald-500 has-checked:bg-emerald-50/40">
                                <input type="radio" name="ruling" value="refund_buyer" class="mt-0.5 text-emerald-600 focus:ring-emerald-500" required>
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Full Escrow Refund to Buyer</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Void auction, nullify lot, and return full funds to winning buyer.</span>
                                </div>
                            </label>

                            <!-- Option 2: Release Escrow to Seller -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 cursor-pointer transition-all has-checked:border-emerald-500 has-checked:bg-emerald-50/40">
                                <input type="radio" name="ruling" value="payout_seller" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Release Escrow Payout to Seller</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Dismiss buyer claim, affirm sale validity, and unlock consignor payout.</span>
                                </div>
                            </label>

                            <!-- Option 3: Disqualify Winner & Award to Runner-up -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 cursor-pointer transition-all has-checked:border-emerald-500 has-checked:bg-emerald-50/40">
                                <input type="radio" name="ruling" value="reassign_winner" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Re-award to 2nd Highest Bidder</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Disqualify top bidder for default or fraud; assign hammer to runner-up.</span>
                                </div>
                            </label>

                            <!-- Option 4: Void Lot -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 cursor-pointer transition-all has-checked:border-emerald-500 has-checked:bg-emerald-50/40">
                                <input type="radio" name="ruling" value="void_lot" class="mt-0.5 text-emerald-600 focus:ring-emerald-500">
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Void Lot (Consignor Fault / Shill)</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Cancel auction entirely, invalidate all bids, and unlock deposits.</span>
                                </div>
                            </label>

                            <!-- Option 5: Dismiss Claim -->
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-amber-400 bg-slate-50/50 cursor-pointer transition-all has-checked:border-slate-500 has-checked:bg-slate-100">
                                <input type="radio" name="ruling" value="dismissed" class="mt-0.5 text-slate-600 focus:ring-slate-500">
                                <div>
                                    <span class="font-bold text-xs text-slate-900 block">Dismiss Claim (No Action)</span>
                                    <span class="text-[11px] text-slate-500 block leading-tight mt-0.5">Reject complainant allegation as unsubstantiated; maintain auction status.</span>
                                </div>
                            </label>
                        </div>

                        <!-- Refund Amount Input (Shown for refund) -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-mono text-[10px] uppercase font-bold text-slate-500">Authorized Refund Amount (₹)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-mono">₹</span>
                                <input type="number" step="0.01" min="0" name="refund_amount" value="{{ old('refund_amount', $dispute->contested_amount) }}" class="w-full pl-7 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:outline-none text-xs font-mono text-slate-800">
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono">Applies when "Full Escrow Refund to Buyer" is ruled.</span>
                        </div>

                        <!-- Arbitration Findings Notes -->
                        <div class="flex flex-col gap-1.5">
                            <label class="font-mono text-[10px] uppercase font-bold text-slate-500">Executive Findings & Legal Reason *</label>
                            <textarea name="ruling_notes" rows="4" required placeholder="Detail the evidence analyzed, policy citations, and binding administrative determination..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-amber-500 focus:outline-none text-xs text-slate-800 placeholder:text-slate-400 leading-relaxed">{{ old('ruling_notes') }}</textarea>
                            <span class="text-[10px] text-slate-400 font-mono">This text is recorded in the permanent audit record and visible to both parties.</span>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-sm transition-all flex items-center justify-center gap-1.5 mt-1">
                            <span class="material-symbols-outlined text-[18px]">gavel</span>
                            <span>Issue Binding Arbitration Ruling</span>
                        </button>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center flex flex-col items-center gap-2">
                        <span class="material-symbols-outlined text-slate-400 text-3xl">lock</span>
                        <span class="font-bold text-xs text-slate-700">Case Arbitrated & Locked</span>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            This dispute was finalized on {{ $dispute->resolved_at ? $dispute->resolved_at->format('M d, Y') : 'N/A' }} with ruling <strong>{{ $dispute->ruling_label }}</strong>. Further modifications require administrative review.
                        </p>
                    </div>
                @endif
            </div>

            <!-- Quick Navigation Card -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-2 text-xs">
                <span class="font-mono text-[10px] uppercase font-bold text-slate-400">Quick Navigation</span>
                <a href="{{ route('admin.auctions.index') }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-slate-400">bolt</span>
                        <span>Auction Master Terminal</span>
                    </span>
                    <span class="material-symbols-outlined text-slate-300 text-sm">chevron_right</span>
                </a>
                <a href="{{ route('admin.support.index') }}" class="p-2.5 rounded-xl hover:bg-slate-50 text-slate-700 flex items-center justify-between transition-colors">
                    <span class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-base text-slate-400">support_agent</span>
                        <span>Live Support Tickets</span>
                    </span>
                    <span class="material-symbols-outlined text-slate-300 text-sm">chevron_right</span>
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
