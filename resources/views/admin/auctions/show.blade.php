@extends('layouts.admin')

@section('title', 'Auction Lot Controller #' . ($auction ? 'AUC-' . $auction->id : 'AUC-N/A'))

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    @if(!$auction)
        <div class="bg-white rounded-xl p-8 text-center text-slate-500 border border-slate-200">
            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">gavel</span>
            <h2 class="text-lg font-bold text-[#0F172A]">Auction Lot Not Found</h2>
            <p class="text-xs text-slate-500 mt-1">The requested auction lot could not be located in the database.</p>
            <a href="{{ route('admin.auctions.index') }}" class="mt-4 inline-block px-4 py-2 bg-[#0F172A] text-white rounded-xl text-xs font-semibold">
                Back to Live Auctions
            </a>
        </div>
    @else
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.auctions.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">bolt</span>
                        <span>Auctions</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <a href="{{ route('admin.auctions.disputes.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors">
                        Disputes Desk
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="font-mono text-xs text-amber-700 font-bold">Lot #AUC-{{ $auction->id }}</span>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-1">
                    <h1 class="font-headline-lg text-2xl font-bold text-[#0F172A] tracking-tight">Auction Lot Control Center</h1>
                    @if(isset($disputes) && $disputes->count() > 0)
                        <a href="#disputes-section" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1 hover:bg-rose-100 transition-colors">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                            {{ $disputes->count() }} {{ Str::plural('Dispute', $disputes->count()) }} Active
                        </a>
                    @endif
                </div>
                <p class="font-body-md text-xs text-slate-500 mt-0.5">Live bidding arbitration, escrow deposit locking, and lot status management.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.auctions.disputes.index') }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-colors shadow-2xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">balance</span>
                    <span>Disputes Desk</span>
                </a>

                @if($auction->status === 'live')
                    <form method="POST" action="{{ route('admin.auctions.cancel', $auction->id) }}" onsubmit="return confirm('Cancel this auction lot?');" class="inline">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-white border border-red-200 hover:bg-red-50 text-xs font-semibold text-red-700 transition-colors shadow-2xs">
                            Cancel Lot
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.auctions.end', $auction->id) }}" onsubmit="return confirm('Hammer fall: award auction lot to highest bidder?');" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-slate-950 text-xs font-bold transition-colors shadow-2xs">
                            Hammer Fall (Award Bid)
                        </button>
                    </form>
                @else
                    <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                        STATUS: {{ strtoupper($auction->status) }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Alert Notifications -->
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

        <!-- Lot Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <div class="lg:col-span-8 flex flex-col gap-4">
                <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row items-start gap-4">
                        <div class="w-24 h-24 rounded-xl bg-amber-50 border border-amber-100 flex items-center justify-center text-5xl shrink-0">
                            🏺
                        </div>
                        <div class="flex flex-col min-w-0">
                            <span class="font-mono text-xs text-amber-700 font-bold">LOT #AUC-{{ $auction->id }} • {{ $auction->product?->category?->name ?? 'GENERAL' }}</span>
                            <h2 class="text-lg font-bold text-[#0F172A] mt-0.5">{{ $auction->product?->name ?? 'Auction Item #' . $auction->product_id }}</h2>
                            <p class="text-xs text-slate-500 mt-1">Consigned by <strong>{{ $auction->seller?->name ?? 'Producer' }}</strong> ({{ $auction->seller?->email ?? 'seller@bazaario.com' }}).</p>
                            <div class="flex flex-wrap items-center gap-4 mt-2 text-xs font-mono">
                                <span class="text-slate-600">Reserve Price: <strong class="text-[#0F172A]">₹{{ number_format((float)($auction->reserve_price ?? 0), 2) }}</strong></span>
                                <span class="text-slate-600">Min Increment: <strong class="text-[#0F172A]">₹{{ number_format((float)($auction->minimum_increment ?? 0), 2) }}</strong></span>
                                <span class="text-emerald-700 font-bold">Bids Count: {{ $bids->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Live Bid Progression Ladder -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 font-mono">Bidding Progression Ladder</h3>
                            <span class="text-xs text-slate-400 font-mono">{{ $bids->count() }} Recorded Bids</span>
                        </div>
                        <div class="overflow-x-auto w-full">
                            <table class="w-full text-left text-xs">
                                <thead>
                                    <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                        <th class="py-2 px-3">Bid Rank</th>
                                        <th class="py-2 px-3">Bidder Identity</th>
                                        <th class="py-2 px-3">Account Status</th>
                                        <th class="py-2 px-3 text-right">Bid Amount</th>
                                        <th class="py-2 px-3 text-right">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono">
                                    @forelse($bids as $index => $bid)
                                        <tr class="{{ $index === 0 ? 'bg-amber-50/50 font-bold text-[#0F172A]' : 'text-slate-700 hover:bg-slate-50' }}">
                                            <td class="py-2.5 px-3 font-mono">
                                                @if($index === 0)
                                                    <span class="px-2 py-0.5 rounded bg-amber-500 text-slate-950 font-bold text-[10px]">LEADER #1</span>
                                                @else
                                                    #{{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span class="font-bold text-[#0F172A]">{{ $bid->user?->name ?? 'User #' . $bid->user_id }}</span>
                                                <span class="text-[10px] text-slate-400 block font-normal">{{ $bid->user?->email ?? '' }}</span>
                                            </td>
                                            <td class="py-2.5 px-3">
                                                <span class="text-emerald-700 font-bold text-[11px]">Verified Buyer</span>
                                            </td>
                                            <td class="py-2.5 px-3 text-right text-amber-700 text-sm font-bold">
                                                ₹{{ number_format((float)($bid->amount ?? 0), 2) }}
                                            </td>
                                            <td class="py-2.5 px-3 text-right text-slate-500 text-[11px]">
                                                {{ $bid->created_at ? $bid->created_at->format('M d, H:i:s') : 'Recorded' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-slate-400 font-sans">
                                                No bids placed on this lot yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Associated Disputes & Claims Section -->
                <div id="disputes-section" class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-600">balance</span>
                            <div>
                                <h3 class="font-bold text-sm text-[#0F172A]">Associated Disputes & Arbitration Claims</h3>
                                <p class="text-xs text-slate-400 font-mono">Consignment discrepancies, winner defaults, or arbitration rulings</p>
                            </div>
                        </div>
                        <button type="button" onclick="document.getElementById('lodge-dispute-box').classList.toggle('hidden');" class="px-3 py-1.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition-colors flex items-center gap-1 self-start sm:self-auto">
                            <span class="material-symbols-outlined text-[16px]">add_circle</span>
                            <span>Lodge Dispute</span>
                        </button>
                    </div>

                    <!-- Collapsible Administrative Dispute Creation Box -->
                    <div id="lodge-dispute-box" class="hidden p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col gap-3">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-xs text-slate-900 font-mono uppercase">Lodge Administrative Auction Dispute</span>
                            <button type="button" onclick="document.getElementById('lodge-dispute-box').classList.add('hidden');" class="text-xs text-slate-400 hover:text-slate-600">✕ Close</button>
                        </div>
                        <form method="POST" action="{{ route('admin.auctions.disputes.create', $auction->id) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            @csrf

                            <!-- Claimant User -->
                            <div>
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Complainant / Claimant User *</label>
                                <select name="user_id" required class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800">
                                    <option value="">Select Claimant User</option>
                                    @if($auction->winner)
                                        <option value="{{ $auction->winner_id }}">Winner: {{ $auction->winner->name }} ({{ $auction->winner->email }})</option>
                                    @endif
                                    @if($auction->seller && $auction->seller->user)
                                        <option value="{{ $auction->seller->user_id }}">Consignor: {{ $auction->seller->user->name }} ({{ $auction->seller->user->email }})</option>
                                    @endif
                                    @foreach($bids->pluck('user')->unique('id')->filter() as $u)
                                        @if($u->id !== $auction->winner_id && (!$auction->seller || $u->id !== $auction->seller->user_id))
                                            <option value="{{ $u->id }}">Bidder: {{ $u->name }} ({{ $u->email }})</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Dispute Type -->
                            <div>
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Dispute Type / Cause *</label>
                                <select name="dispute_type" required class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800">
                                    <option value="quality_mismatch">Catalog / Grade Mismatch</option>
                                    <option value="non_payment">Winner Non-Payment / Default</option>
                                    <option value="seller_refusal">Seller Refusal to Honor Lot</option>
                                    <option value="damaged_transit">Damaged in Consignment Transit</option>
                                    <option value="shill_bidding">Shill / Fraudulent Bidding</option>
                                    <option value="reserve_dispute">Reserve Price Conflict</option>
                                    <option value="other">Other Conflict</option>
                                </select>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Priority Level *</label>
                                <select name="priority" required class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800">
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="low">Low</option>
                                </select>
                            </div>

                            <!-- Contested Amount -->
                            <div>
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Contested Amount (₹)</label>
                                <input type="number" step="0.01" min="0" name="contested_amount" value="{{ $auction->current_price ?? 0 }}" class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800 font-mono">
                            </div>

                            <!-- Subject -->
                            <div class="sm:col-span-2">
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Dispute Subject / Headline *</label>
                                <input type="text" name="subject" required placeholder="e.g. Winner refused payment after hammer fall" class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800">
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label class="block font-mono text-[10px] uppercase font-bold text-slate-500 mb-1">Detailed Grievance / Administrative Findings *</label>
                                <textarea name="claim_description" rows="3" required placeholder="Enter complete grievance details or reason for lodging dispute..." class="w-full p-2 bg-white border border-slate-200 rounded-lg text-xs text-slate-800"></textarea>
                            </div>

                            <!-- Submit -->
                            <div class="sm:col-span-2 flex justify-end">
                                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-lg text-xs shadow-2xs transition-colors">
                                    File Auction Dispute
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Disputes Table -->
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                    <th class="py-2 px-3">Dispute #</th>
                                    <th class="py-2 px-3">Complainant</th>
                                    <th class="py-2 px-3">Nature</th>
                                    <th class="py-2 px-3 text-right">Contested Amount</th>
                                    <th class="py-2 px-3 text-center">Status</th>
                                    <th class="py-2 px-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-mono">
                                @forelse($disputes as $disp)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-2.5 px-3">
                                            <a href="{{ route('admin.auctions.disputes.show', $disp->id) }}" class="font-bold text-slate-900 hover:text-amber-700">
                                                #{{ $disp->dispute_number }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 block font-normal">
                                                {{ $disp->created_at ? $disp->created_at->format('M d, H:i') : '' }}
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 font-sans">
                                            <span class="font-bold text-slate-800 block">{{ $disp->claimant?->name ?? 'User #' . $disp->user_id }}</span>
                                            <span class="text-[10px] text-slate-400 font-mono">{{ $disp->claimant?->email ?? '' }}</span>
                                        </td>
                                        <td class="py-2.5 px-3 font-sans">
                                            <span class="text-slate-700 block">{{ $disp->type_label }}</span>
                                            <span class="text-[10px] font-mono uppercase {{ $disp->priority === 'urgent' ? 'text-rose-600 font-bold' : 'text-slate-400' }}">
                                                {{ $disp->priority }} priority
                                            </span>
                                        </td>
                                        <td class="py-2.5 px-3 text-right font-bold text-rose-700">
                                            ₹{{ number_format((float)$disp->contested_amount, 2) }}
                                        </td>
                                        <td class="py-2.5 px-3 text-center">
                                            @if($disp->status === 'open')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    OPEN
                                                </span>
                                            @elseif($disp->status === 'under_review')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    INVESTIGATING
                                                </span>
                                            @elseif($disp->status === 'resolved')
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    RESOLVED
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    DISMISSED
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2.5 px-3 text-right">
                                            <a href="{{ route('admin.auctions.disputes.show', $disp->id) }}" class="px-2.5 py-1 rounded bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold font-sans transition-colors inline-flex items-center gap-1 shadow-2xs">
                                                <span>Arbitrate</span>
                                                <span class="material-symbols-outlined text-[12px]">arrow_forward</span>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400 font-sans">
                                            No disputes lodged for this auction lot.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col gap-4">
                <!-- Lot Summary Card -->
                <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 font-mono">Lot Escrow Summary</h3>
                    <div class="flex flex-col gap-2.5 text-xs font-mono">
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Starting Price:</span>
                            <span class="font-bold text-[#0F172A]">₹{{ number_format((float)($auction->starting_price ?? 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Reserve Price:</span>
                            <span class="font-bold text-[#0F172A]">₹{{ number_format((float)($auction->reserve_price ?? 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Current High Bid:</span>
                            <span class="font-bold text-amber-700 text-sm">₹{{ number_format((float)($auction->current_price ?? ($auction->starting_price ?? 0)), 2) }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Reserve Met:</span>
                            @if(($auction->current_price ?? 0) >= ($auction->reserve_price ?? 0))
                                <span class="text-emerald-700 font-bold">YES ✓</span>
                            @else
                                <span class="text-amber-600 font-bold">BELOW RESERVE</span>
                            @endif
                        </div>
                        <div class="flex justify-between border-b border-slate-100 pb-2">
                            <span class="text-slate-500">Auction Scheduled:</span>
                            <span class="text-slate-700">{{ $auction->starts_at ? $auction->starts_at->format('d M Y') : 'Immediate' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Auction End:</span>
                            <span class="text-slate-700">{{ $auction->ends_at ? $auction->ends_at->format('d M Y, H:i') : 'Open' }}</span>
                        </div>
                        @if($auction->winner_id)
                            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 mt-2">
                                <span class="text-[10px] uppercase font-bold text-emerald-800 block">Winning Bidder</span>
                                <span class="font-bold text-emerald-950 text-xs mt-0.5 block">User #{{ $auction->winner_id }} ({{ $auction->winner?->name ?? 'Verified Winner' }})</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Disputes Desk Quick Card -->
                <div class="p-5 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 font-mono">Disputes Desk Quick Link</h3>
                        <span class="material-symbols-outlined text-slate-400 text-base">gavel</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed font-sans">
                        Manage all wholesale dispute filings, evidence reviews, and escrow arbitrations across all catalog lots.
                    </p>
                    <a href="{{ route('admin.auctions.disputes.index') }}" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs text-center transition-colors flex items-center justify-center gap-1.5 shadow-2xs">
                        <span>Open Disputes Console</span>
                        <span class="material-symbols-outlined text-sm">open_in_new</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
