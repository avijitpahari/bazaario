@extends('layouts.agent')

@section('title', 'Ticket #' . $ticket->ticket_number . ' Workstation — Bazaario')

@section('content')
<div class="space-y-6">
    <!-- Top Breadcrumb & Header -->
    <div class="flex items-center gap-2 text-xs text-slate-400 font-mono">
        <a href="{{ route('agent.dashboard') }}" class="hover:text-amber-400 transition-colors">Workstation</a>
        <span>/</span>
        <a href="{{ route('agent.tickets.index') }}" class="hover:text-amber-400 transition-colors">Tickets</a>
        <span>/</span>
        <span class="text-amber-400 font-mono font-bold">#{{ $ticket->ticket_number }}</span>
    </div>

    <!-- Ticket Summary Banner -->
    <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl flex flex-col lg:flex-row items-start lg:items-center justify-between gap-5">
        <div class="space-y-2 flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="font-mono text-sm font-black text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-md border border-amber-500/20">
                    #{{ $ticket->ticket_number }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ticket->status_color }}">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                    {{ $ticket->priority }} Priority
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-[#090D16] text-slate-300 border border-slate-800">
                    {{ $ticket->category_label }}
                </span>
                @if($ticket->is_escalated)
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/40 flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined text-xs">gavel</span>
                        <span>Dispute Escalated</span>
                    </span>
                @endif
            </div>

            <h1 class="text-xl md:text-2xl font-display font-black text-white tracking-tight">
                {{ $ticket->subject }}
            </h1>

            <div class="flex items-center gap-3 text-xs text-slate-400">
                <span>Opened {{ $ticket->created_at->format('d M Y, h:i A') }} ({{ $ticket->created_at->diffForHumans() }})</span>
                <span>•</span>
                <span>Assigned: <strong class="text-white">{{ $ticket->assignedAgent ? ($ticket->assignedAgent->id === $agent->id ? 'You' : $ticket->assignedAgent->name) : 'Unassigned' }}</strong></span>
            </div>
        </div>

        <div class="flex items-center gap-3 shrink-0 self-end lg:self-center">
            @if(empty($ticket->assigned_agent_id))
                <form action="{{ route('agent.tickets.claim', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-400 hover:to-indigo-500 text-white font-bold text-xs transition-all flex items-center gap-2 shadow-lg shadow-blue-500/25">
                        <span class="material-symbols-outlined text-[18px]">pan_tool</span>
                        <span>Claim This Ticket</span>
                    </button>
                </form>
            @elseif($ticket->assigned_agent_id !== $agent->id)
                <form action="{{ route('agent.tickets.claim', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors flex items-center gap-1.5 border border-slate-700">
                        <span class="material-symbols-outlined text-[16px]">sync</span>
                        <span>Reassign to Me</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('agent.tickets.index') }}" 
               class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold transition-colors border border-slate-700/80 flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Back to Queue</span>
            </a>
        </div>
    </div>

    @if($ticket->is_escalated)
    <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-start gap-3.5 shadow-lg shadow-rose-950/20">
        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/30">
            <span class="material-symbols-outlined text-xl">gavel</span>
        </div>
        <div>
            <h4 class="font-bold text-rose-200 text-sm">Dispute Escalation In Progress</h4>
            <p class="text-xs text-rose-300/90 mt-0.5 leading-relaxed">
                Escalation reason: <em>"{{ $ticket->escalation_reason }}"</em>. Executive Admin arbitration team has been alerted. Escrow funds for associated order remain safeguarded.
            </p>
        </div>
    </div>
    @endif

    <!-- Main Workspace Grid: 2 Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- ── LEFT / MAIN: MESSAGE STREAM & RESPONSE COMPOSER (2 COLS) ── -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Messages Conversation Thread -->
            <div class="rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl flex flex-col">
                <div class="p-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-400 text-lg">forum</span>
                        <h2 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Case Conversation Thread</h2>
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono bg-slate-800 px-2 py-0.5 rounded">{{ $ticket->messages->count() }} messages</span>
                </div>

                <div class="p-6 space-y-5 max-h-[600px] overflow-y-auto divide-y divide-slate-800/60 bg-[#090D16]/40">
                    @foreach($ticket->messages as $msg)
                        @php
                            $isAgent = in_array($msg->sender_type, ['agent', 'admin']);
                            $isCustomer = $msg->sender_type === 'user';
                            $isSeller = $msg->sender_type === 'seller';
                            $isSystem = $msg->sender_type === 'system';
                        @endphp

                        <div class="pt-5 first:pt-0">
                            @if($isSystem)
                                <div class="p-3.5 rounded-2xl bg-[#0E1526]/80 border border-slate-800 text-center shadow-xs">
                                    <span class="text-[11px] text-slate-300 font-mono flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-sm text-amber-400">info</span>
                                        <span>{{ $msg->message }}</span>
                                        <span class="text-slate-500">• {{ $msg->created_at->format('h:i A') }}</span>
                                    </span>
                                </div>
                            @else
                                <div class="flex items-start gap-3.5">
                                    <div class="w-9 h-9 rounded-2xl flex items-center justify-center font-bold text-xs uppercase shrink-0 shadow-sm
                                        {{ $isAgent ? 'bg-gradient-to-br from-amber-400 to-amber-600 text-slate-950 font-black ring-2 ring-amber-400/30' : ($isCustomer ? 'bg-gradient-to-br from-blue-500 to-indigo-600 text-white ring-2 ring-blue-400/30' : 'bg-gradient-to-br from-emerald-500 to-teal-600 text-white ring-2 ring-emerald-400/30') }}">
                                        {{ $msg->user ? $msg->user->initials : ($isAgent ? 'AG' : 'US') }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2 mb-1.5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-bold text-xs text-white">
                                                    {{ $msg->user?->name ?? ucfirst($msg->sender_type) }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold uppercase
                                                    {{ $isAgent ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : ($isCustomer ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30') }}">
                                                    {{ $isAgent ? 'Support Specialist' : ($isCustomer ? 'Buyer / Customer' : 'Store Merchant') }}
                                                </span>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-mono bg-slate-900/80 px-2 py-0.5 rounded border border-slate-800">{{ $msg->created_at->format('d M, h:i A') }}</span>
                                        </div>

                                        <div class="p-4 rounded-2xl text-xs leading-relaxed whitespace-pre-line shadow-sm
                                            {{ $isAgent ? 'bg-amber-500/10 border border-amber-500/25 text-amber-100 shadow-amber-500/5' : 'bg-[#0E1526] border border-slate-700/80 text-slate-200 shadow-black/20' }}">
                                            {{ $msg->message }}
                                        </div>

                                        @if($msg->attachment_path)
                                            <div class="mt-2.5">
                                                <a href="{{ $msg->attachment_url }}" target="_blank" 
                                                   class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 text-xs font-mono font-semibold transition-colors border border-slate-700">
                                                    <span class="material-symbols-outlined text-sm">attachment</span>
                                                    <span>View Case Attachment</span>
                                                    <span class="material-symbols-outlined text-xs">open_in_new</span>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Specialist Response Composer -->
            <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-400 text-base">rate_review</span>
                        <span>Post Specialist Response</span>
                    </h3>
                    <span class="text-[11px] text-slate-400">Replying as <strong class="text-white">{{ $agent->name }}</strong></span>
                </div>

                <!-- Canned Responses Quick Chips -->
                <div class="space-y-1.5">
                    <span class="text-[10px] font-mono text-slate-400 uppercase tracking-wider block">Quick Response Presets:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach($cannedReplies as $canned)
                            <button type="button" 
                                    onclick="insertCannedText({{ json_encode($canned['text']) }})"
                                    class="px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] font-medium transition-colors border border-slate-700/80 flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-xs text-amber-400">bolt</span>
                                <span>{{ $canned['title'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Reply Form -->
                <form action="{{ route('agent.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <textarea id="reply_message" 
                                  name="message" 
                                  rows="4" 
                                  required
                                  placeholder="Write your response to the customer and store merchant..." 
                                  class="w-full px-4 py-3 bg-[#090D16] border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all leading-relaxed"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <!-- Attachment Upload -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-mono uppercase text-slate-400">
                                Supporting Document / Photo (Optional)
                            </label>
                            <input type="file" 
                                   name="attachment" 
                                   accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                        </div>

                        <!-- Status Transition -->
                        <div class="space-y-1">
                            <label class="block text-[10px] font-mono uppercase text-slate-400">
                                Set Ticket Status On Send
                            </label>
                            <select name="status" class="w-full px-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                                <option value="waiting_customer" selected>Waiting on Customer Response</option>
                                <option value="waiting_seller">Waiting on Store Merchant</option>
                                <option value="in_progress">In Progress / Investigating</option>
                                <option value="resolved">Mark Ticket as Resolved</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-slate-800/80">
                        <span class="text-[11px] text-slate-500 font-mono">
                            Dispatched to customer &amp; seller notifications.
                        </span>

                        <button type="submit" 
                                class="px-6 py-2.5 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-amber-500/25 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">send</span>
                            <span>Send Specialist Reply</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ── RIGHT SIDEBAR: DOSSIER & ACTIONS (1 COL) ── -->
        <div class="space-y-6">
            <!-- Workstation Ticket Properties -->
            <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2 pb-2.5 border-b border-slate-800/80">
                    <span class="material-symbols-outlined text-amber-400 text-base">tune</span>
                    <span>Ticket Properties</span>
                </h3>

                <form action="{{ route('agent.tickets.status', $ticket) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1.5">Status</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Waiting on Customer</option>
                            <option value="waiting_seller" {{ $ticket->status === 'waiting_seller' ? 'selected' : '' }}>Waiting on Seller</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-mono text-slate-400 uppercase mb-1.5">Priority</label>
                        <select name="priority" class="w-full px-3.5 py-2.5 bg-[#090D16] border border-slate-700/80 rounded-xl text-xs text-white focus:outline-none focus:border-amber-500">
                            <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>High</option>
                            <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>Urgent</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs rounded-xl transition-colors border border-slate-700 shadow-sm">
                        Update Properties
                    </button>
                </form>
            </div>

            <!-- Customer Dossier -->
            <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2 pb-2.5 border-b border-slate-800/80">
                    <span class="material-symbols-outlined text-blue-400 text-base">person</span>
                    <span>Customer Details</span>
                </h3>

                @if($ticket->user)
                    <div class="space-y-1.5 text-xs">
                        <div class="font-bold text-white text-sm">{{ $ticket->user->name }}</div>
                        <div class="text-slate-400 font-mono">{{ $ticket->user->email }}</div>
                        <div class="text-slate-400">{{ $ticket->user->phone ?: 'No phone provided' }}</div>
                        <div class="text-[11px] text-amber-400/90 pt-1 font-mono">
                            {{ $ticket->user->buyer_trust_rank }}
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-400">Direct / Guest inquiry.</p>
                @endif
            </div>

            <!-- Linked Order Dossier -->
            @if($ticket->order)
            <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-3">
                <div class="flex items-center justify-between pb-2.5 border-b border-slate-800/80">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-amber-400 text-base">inventory_2</span>
                        <span>Linked Order</span>
                    </h3>
                    <span class="text-xs font-mono font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                        #{{ $ticket->order->order_number }}
                    </span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Total Escrow Amount:</span>
                        <strong class="text-white font-mono font-bold text-sm">₹{{ number_format($ticket->order->total_amount, 2) }}</strong>
                    </div>
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Payment Status:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            {{ $ticket->order->payment_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Order Status:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-slate-800 text-slate-300">
                            {{ $ticket->order->order_status }}
                        </span>
                    </div>

                    @if($ticket->order->items && $ticket->order->items->isNotEmpty())
                        <div class="pt-2 border-t border-slate-800/80 space-y-1">
                            <span class="text-[10px] font-mono text-slate-400 uppercase block">Items:</span>
                            @foreach($ticket->order->items as $item)
                                <div class="text-[11px] text-slate-300 truncate">
                                    • {{ $item->product_name ?? 'Consignment Item' }} (x{{ $item->quantity }})
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Merchant Store Dossier -->
            @if($ticket->seller)
            <div class="p-6 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 shadow-xl space-y-3">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider font-mono flex items-center gap-2 pb-2.5 border-b border-slate-800/80">
                    <span class="material-symbols-outlined text-amber-400 text-base">storefront</span>
                    <span>Merchant Dossier</span>
                </h3>

                <div class="space-y-1.5 text-xs">
                    <div class="font-bold text-white text-sm">
                        {{ $ticket->seller?->sellerProfile?->shop_name ?? $ticket->seller->name }}
                    </div>
                    <div class="text-slate-400 font-mono">{{ $ticket->seller->email }}</div>
                    <div class="text-[10px] text-emerald-400 font-mono pt-1">
                        Status: {{ ucfirst($ticket->seller?->sellerProfile?->status ?? 'Active') }}
                    </div>
                </div>
            </div>
            @endif

            <!-- Escalate to Admin Dispute Arbitration -->
            @if(!$ticket->is_escalated)
            <div class="p-6 rounded-3xl bg-rose-950/20 border border-rose-900/60 shadow-xl space-y-3">
                <div class="flex items-center gap-2 text-rose-300 font-bold text-xs">
                    <span class="material-symbols-outlined text-base">gavel</span>
                    <span>Escalate to Admin Arbitration</span>
                </div>
                <p class="text-[11px] text-rose-200/70 leading-relaxed">
                    If this inquiry involves unresolvable merchant misconduct, counterfeit goods, or severe delivery failure, escalate directly to the Executive Admin Dispute Council.
                </p>

                <form action="{{ route('agent.tickets.escalate', $ticket) }}" method="POST" class="space-y-2.5">
                    @csrf
                    <textarea name="escalation_reason" 
                              rows="2" 
                              required
                              placeholder="Reason for escalating to Admin Arbitration..." 
                              class="w-full px-3.5 py-2.5 bg-[#090D16] border border-rose-900/80 rounded-xl text-xs text-white placeholder-rose-300/40 focus:outline-none focus:border-rose-500"></textarea>

                    <button type="submit" 
                            class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs transition-colors flex items-center justify-center gap-1.5 shadow-md shadow-rose-950/40">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        <span>Escalate to Admin</span>
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function insertCannedText(text) {
        const textarea = document.getElementById('reply_message');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
        }
    }
</script>
@endpush
