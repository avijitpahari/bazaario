<!-- QUICK TICKET CONTROLS CARD -->
<div class="p-4 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-1.5 text-xs font-bold font-heading text-slate-800">
            <span class="material-symbols-outlined text-[18px] text-blue-600">tune</span>
            <span>Ticket Properties</span>
        </div>
        <span class="text-[10px] text-slate-400 font-mono">#{{ $activeTicket->ticket_number }}</span>
    </div>

    <form action="{{ route('admin.support.tickets.status', $activeTicket) }}" method="POST" class="space-y-3">
        @csrf
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Status</label>
            <select name="status" class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium">
                <option value="open" {{ $activeTicket->status === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ $activeTicket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="waiting_customer" {{ $activeTicket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                <option value="waiting_seller" {{ $activeTicket->status === 'waiting_seller' ? 'selected' : '' }}>Waiting on Seller</option>
                <option value="escalated" {{ $activeTicket->status === 'escalated' ? 'selected' : '' }}>Escalated to Dispute</option>
                <option value="resolved" {{ $activeTicket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ $activeTicket->status === 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Priority</label>
            <select name="priority" class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium">
                <option value="low" {{ $activeTicket->priority === 'low' ? 'selected' : '' }}>Low Priority</option>
                <option value="medium" {{ $activeTicket->priority === 'medium' ? 'selected' : '' }}>Medium Priority</option>
                <option value="high" {{ $activeTicket->priority === 'high' ? 'selected' : '' }}>High Priority</option>
                <option value="urgent" {{ $activeTicket->priority === 'urgent' ? 'selected' : '' }}>Urgent Priority</option>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-600 mb-1">Assigned Agent</label>
            <select name="assigned_agent_id" class="w-full text-xs px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 font-medium">
                <option value="">-- Unassigned --</option>
                @foreach($agents ?? [] as $agent)
                    <option value="{{ $agent->id }}" {{ $activeTicket->assigned_agent_id === $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }} ({{ ucfirst($agent->role) }})
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="w-full py-2 px-3 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold flex items-center justify-center gap-1.5 shadow-2xs transition-all cursor-pointer">
            <span class="material-symbols-outlined text-[15px] text-amber-400">check_circle</span>
            <span>Update Properties</span>
        </button>
    </form>
</div>

<!-- ARBITRATION ACTIONS CARD -->
<div class="p-4 rounded-2xl bg-gradient-to-br from-rose-50 to-amber-50 border border-rose-100 shadow-sm">
    <h3 class="font-heading font-bold text-sm text-rose-900 mb-3 flex items-center gap-2">
        <span class="material-symbols-outlined text-[18px]">gavel</span>
        Arbitration Actions
    </h3>
    <p class="text-[11px] text-rose-700 mb-4">Review evidence, hear both parties, and issue a binding decision.</p>

    <form action="{{ route('admin.support.tickets.arbitrate', $activeTicket) }}" method="POST" class="space-y-3">
        @csrf

        <!-- Decision Selection -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-2">Decision</label>
            <div class="space-y-1.5">
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-rose-400 hover:bg-rose-50 cursor-pointer transition-colors">
                    <input type="radio" name="decision" value="approve_refund" class="text-rose-600 focus:ring-rose-500" required>
                    <div class="flex-1">
                        <span class="font-semibold text-xs text-slate-900">✅ Approve Refund to Buyer</span>
                        <p class="text-[10px] text-slate-500">Refund buyer, void seller payout, mark return as refunded</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 cursor-pointer transition-colors">
                    <input type="radio" name="decision" value="reject_refund" class="text-emerald-600 focus:ring-emerald-500">
                    <div class="flex-1">
                        <span class="font-semibold text-xs text-slate-900">❌ Reject Refund / Release to Seller</span>
                        <p class="text-[10px] text-slate-500">Deny buyer claim, release escrow to seller, close ticket</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50 cursor-pointer transition-colors">
                    <input type="radio" name="decision" value="release_escrow" class="text-blue-600 focus:ring-blue-500">
                    <div class="flex-1">
                        <span class="font-semibold text-xs text-slate-900">💰 Release Escrow to Seller</span>
                        <p class="text-[10px] text-slate-500">Seller fulfilled obligation, release funds, close ticket</p>
                    </div>
                </label>
                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-colors">
                    <input type="radio" name="decision" value="custom" class="text-indigo-600 focus:ring-indigo-500">
                    <div class="flex-1">
                        <span class="font-semibold text-xs text-slate-900">⚖️ Custom Arbitration</span>
                        <p class="text-[10px] text-slate-500">Specify custom resolution action</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Custom Action Input -->
        <div id="custom-action-field" class="mt-2 hidden">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Custom Action Description</label>
            <input type="text" name="custom_action" placeholder="e.g. Partial refund 50%, split shipping cost, replace item"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all">
        </div>

        <!-- Refund Amount (for approve_refund) -->
        <div id="refund-amount-field" class="mt-2 hidden">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Refund Amount (₹)</label>
            <input type="number" name="refund_amount" step="0.01" min="0" max="{{ $activeTicket->order ? $activeTicket->order->total_amount : 10000000 }}"
                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"
                   placeholder="Max: {{ $activeTicket->order ? '₹' . number_format($activeTicket->order->total_amount, 2) : '₹10,00,000' }}">
        </div>

        <!-- Arbitration Notes -->
        <div class="mt-2">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Arbitration Notes / Reasoning <span class="text-rose-500">*</span></label>
            <textarea name="arbitration_notes" rows="3" required
                      placeholder="Document your reasoning, evidence reviewed, and decision rationale for the record."
                      class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
        </div>

        <!-- Submit Arbitration -->
        <button type="submit"
                class="w-full px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-heading font-semibold text-xs flex items-center justify-center gap-2 shadow-md transition-colors cursor-pointer">
            <span class="material-symbols-outlined text-[18px]">gavel</span>
            <span>Issue Arbitration Decision</span>
        </button>
    </form>
</div>

<!-- ESCALATION REASON (if escalated) -->
@if($activeTicket->is_escalated && $activeTicket->escalation_reason)
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200">
        <h4 class="font-mono text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">warning</span>
            Escalation Reason
        </h4>
        <p class="text-xs text-amber-900 bg-white p-3 rounded-xl border border-amber-200 whitespace-pre-line">{{ $activeTicket->escalation_reason }}</p>
        <p class="text-[10px] text-amber-700 mt-2 font-mono">Escalated: {{ $activeTicket->escalated_at ? $activeTicket->escalated_at->format('M d, Y h:i A') : 'Unknown' }}</p>
    </div>
@endif

<!-- RELATED RETURN/DISPUTE -->
@if($relatedReturn)
    <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200">
        <h4 class="font-mono text-[10px] font-bold text-blue-800 uppercase tracking-wider mb-2 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">assignment_return</span>
            Related Return/Refund Dispute
        </h4>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between">
                <span class="text-slate-500">Return ID</span>
                <span class="font-mono font-medium text-slate-900">DSP-{{ $relatedReturn->id }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Status</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $relatedReturn->status === 'refunded' ? 'bg-emerald-100 text-emerald-800' : ($relatedReturn->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($relatedReturn->status) }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Refund Amount</span>
                <span class="font-mono font-medium text-slate-900">₹{{ number_format($relatedReturn->refund_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Reason</span>
                <span class="font-medium text-slate-900 truncate max-w-[140px]">{{ $relatedReturn->reason }}</span>
            </div>
            <a href="{{ route('admin.disputes.index') }}#dispute-{{ $relatedReturn->id }}" class="text-[10px] text-blue-600 hover:underline font-mono">View in Dispute Portal →</a>
        </div>
    </div>
@endif

<!-- BUYER / CUSTOMER INFO -->
<div>
    <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
        <span class="material-symbols-outlined text-[14px]">person</span>
        Customer / Buyer
    </h4>
    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr($activeTicket->user->name ?? 'C', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h5 class="font-heading font-semibold text-xs text-slate-900 truncate">{{ $activeTicket->user->name ?? 'Unknown' }}</h5>
                <p class="text-[10px] text-slate-500 font-mono">{{ $activeTicket->user->email ?? 'No email' }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
            <div>
                <span class="text-[9px] text-slate-400 block font-mono">Orders</span>
                <span class="font-medium text-slate-700">{{ $activeTicket->user->orders_count ?? 0 }}</span>
            </div>
            <div>
                <span class="text-[9px] text-slate-400 block font-mono">Tickets</span>
                <span class="font-medium text-slate-700">{{ SupportTicket::where('user_id', $activeTicket->user_id)->count() }}</span>
            </div>
        </div>
        @if($activeTicket->user)
            <a href="{{ route('admin.customers.show', $activeTicket->user->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block">
                View Full Profile
            </a>
        @endif
    </div>
</div>

<!-- SELLER INFO -->
@if($activeTicket->seller_id || ($activeTicket->order && $activeTicket->order->sellerOrders->count()))
    <div>
        <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">store</span>
            Seller / Merchant
        </h4>
        @php
            $seller = $activeTicket->seller ?? ($activeTicket->order ? $activeTicket->order->sellerOrders->first()?->seller : null);
            $sellerProfile = $seller?->sellerProfile;
        @endphp
        @if($seller && $sellerProfile)
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($sellerProfile->shop_name ?? $seller->name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h5 class="font-heading font-semibold text-xs text-slate-900 truncate">{{ $sellerProfile->shop_name }}</h5>
                        <p class="text-[10px] text-slate-500 font-mono">{{ $seller->email }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
                    <div>
                        <span class="text-[9px] text-slate-400 block font-mono">KYC Status</span>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase {{ $sellerProfile->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($sellerProfile->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $sellerProfile->status }}
                        </span>
                    </div>
                    <div>
                        <span class="text-[9px] text-slate-400 block font-mono">Trust Score</span>
                        <span class="font-mono font-medium text-slate-700">{{ $sellerProfile->trust_score ?? 'N/A' }}</span>
                    </div>
                </div>
                <a href="{{ route('admin.sellers.show', $sellerProfile->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block">
                    View Merchant Dossier
                </a>
            </div>
        @endif
    </div>
@endif

<!-- ORDER DETAILS -->
@if($activeTicket->order)
    <div>
        <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
            <span class="material-symbols-outlined text-[14px]">shopping_cart</span>
            Order Details
        </h4>
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-2">
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Order Number</span>
                <span class="font-mono font-medium text-slate-900">{{ $activeTicket->order->order_number }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Status</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $activeTicket->order->order_status === 'completed' ? 'bg-emerald-100 text-emerald-800' : ($activeTicket->order->order_status === 'cancelled' ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ ucfirst($activeTicket->order->order_status) }}
                </span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Payment</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $activeTicket->order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-800' : ($activeTicket->order->payment_status === 'refunded' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ ucfirst($activeTicket->order->payment_status) }}
                </span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Total Amount</span>
                <span class="font-mono font-medium text-slate-900">₹{{ number_format($activeTicket->order->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-xs">
                <span class="text-slate-500">Order Date</span>
                <span class="font-mono font-medium text-slate-700">{{ $activeTicket->order->created_at->format('M d, Y') }}</span>
            </div>
            <a href="{{ route('admin.orders.show', $activeTicket->order->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block">
                View Full Order Dossier
            </a>
        </div>
    </div>
@endif

<script>
    // Toggle custom action and refund amount fields based on decision selection
    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'decision') {
            const customField = document.getElementById('custom-action-field');
            const refundField = document.getElementById('refund-amount-field');
            if (customField) {
                if (e.target.value === 'custom') {
                    customField.classList.remove('hidden');
                } else {
                    customField.classList.add('hidden');
                }
            }
            if (refundField) {
                if (e.target.value === 'approve_refund') {
                    refundField.classList.remove('hidden');
                } else {
                    refundField.classList.add('hidden');
                }
            }
        }
    });
</script>