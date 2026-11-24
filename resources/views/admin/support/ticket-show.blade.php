@extends('layouts.admin')

@section('title', 'Support Ticket Arbitration — ' . \App\Models\SiteSetting::getSiteName())

@section('content')
<div x-data="adminTicketArbitration({
    ticketId: {{ $ticket->id }},
    csrfToken: '{{ csrf_token() }}',
    pollUrl: '{{ route('admin.support.tickets.poll', $ticket) }}',
    arbitrateUrl: '{{ route('admin.support.tickets.arbitrate', $ticket) }}'
})" class="h-[calc(100vh-68px)] flex flex-col overflow-hidden">

    <!-- Top Sub-Header Bar -->
    <div class="px-6 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 shadow-support-surface">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]">gavel</span>
            </div>
            <div>
                <h1 class="font-heading font-bold text-base text-slate-900 leading-tight">Ticket Arbitration Desk</h1>
                <p class="text-[11px] text-slate-500 font-sans">Multi-party mediation: Buyer inquiry • Seller defense • Order evidence</p>
            </div>
        </div>

        <!-- Quick Status Pills -->
        <div class="flex items-center gap-3 text-xs">
            <x-support::pill variant="status" :value="$ticket->status" size="sm" dot="true" />
            <x-support::pill variant="priority" :value="$ticket->priority" size="sm" dot="true" />
            @if($ticket->is_escalated)
                <x-support::pill variant="escalated" value="escalated" size="sm" :animate="true" />
            @endif
            @php $roleVal = $ticket->seller_id ? 'seller' : 'customer'; @endphp
            <x-support::pill variant="role" :value="$roleVal" size="sm" />
        </div>
    </div>

    <!-- 3-Column Arbitration Grid -->
    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-[360px_1fr_320px] gap-4 p-4 overflow-hidden">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 1: TICKET LIST / NAVIGATION (360px fixed)                          -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 rounded-2xl flex flex-col h-full overflow-hidden support-stagger lg:w-[360px] min-w-0">

            <!-- Filter Tabs -->
            <x-support::section-header
                icon="list"
                title="All Tickets"
                subtitle="Navigate between tickets"
                :count="$tickets->total()"
                countLabel="Total"
            />

            <div class="px-4 pb-3">
                <x-support::tabs
                    :vertical="true"
                    :items="[
                        ['label' => 'Live Chats', 'url' => route('admin.support.tickets.index', ['view' => 'chats']), 'count' => $chatOpenCount + $chatPendingCount, 'icon' => 'chat_bubble', 'active' => false, 'color' => 'blue'],
                        ['label' => 'Support Tickets', 'url' => route('admin.support.tickets.index', ['view' => 'tickets']), 'count' => $ticketOpenCount, 'icon' => 'confirmation_number', 'active' => $viewMode === 'tickets', 'color' => 'amber'],
                        ['label' => 'Seller Tickets', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'seller']), 'count' => $ticketSellerCount, 'icon' => 'store', 'active' => $tab === 'seller', 'color' => 'indigo'],
                        ['label' => 'Customer Tickets', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'customer']), 'count' => $ticketCustomerCount, 'icon' => 'person', 'active' => $tab === 'customer', 'color' => 'blue'],
                        ['label' => 'Escalated / Disputes', 'url' => route('admin.support.tickets.escalated'), 'count' => $ticketEscalatedCount, 'icon' => 'gavel', 'active' => $viewMode === 'escalated', 'color' => 'rose'],
                    ]"
                />
            </div>

            <!-- Search & Filters -->
            <div class="px-4 pb-3">
                <x-support::search-filter
                    action="{{ route('admin.support.tickets.index') }}"
                    :search="['name' => 'search', 'placeholder' => 'Search tickets...', 'value' => request('search')]"
                    :filters="[
                        ['name' => 'priority', 'label' => 'All Priorities', 'options' => ['urgent' => 'Urgent', 'high' => 'High', 'medium' => 'Medium', 'low' => 'Low']],
                        ['name' => 'category', 'label' => 'All Categories', 'options' => collect($ticketCategories)->mapWithKeys(fn($c) => [$c => ucfirst(str_replace('_', ' ', $c))])->toArray()],
                    ]"
                    :hidden="[['name' => 'view', 'value' => $viewMode], ['name' => 'tab', 'value' => $tab]]"
                />
            </div>

            <!-- Tickets List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($tickets as $t)
                    @php
                        $isSelected = $activeTicket && $activeTicket->id === $t->id;
                        $initiatorName = $t->seller_id ? ($t->seller->sellerProfile->shop_name ?? $t->seller->name) : ($t->user->name ?? 'Unknown');
                        $initiatorType = $t->seller_id ? 'seller' : 'customer';
                        $latest = $t->latestMessage;
                        $status = $t->is_escalated ? 'escalated' : ($t->status === 'open' ? 'open' : ($t->status === 'in_progress' ? 'progress' : 'closed'));
                    @endphp
                    <a href="{{ route('admin.support.tickets.index', ['view' => $viewMode, 'tab' => $tab, 'ticket_id' => $t->id, 'search' => request('search'), 'priority' => request('priority'), 'category' => request('category')]) }}"
                       class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors block relative support-focus {{ $isSelected ? 'bg-blue-50/70 border-l-4 border-blue-600' : '' }}">

                        <x-support::avatar
                            :type="$initiatorType"
                            size="md"
                            :name="$initiatorName"
                            :status="$status"
                            :sr-label="ucfirst($initiatorType) . ' ticket: ' . $t->ticket_number"
                        />

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-1 mb-0.5">
                                <h3 class="font-heading font-semibold text-xs text-slate-900 truncate {{ $isSelected ? 'text-blue-900 font-bold' : '' }}">
                                    {{ $t->ticket_number }}
                                </h3>
                                <span class="font-mono text-[10px] text-slate-400 shrink-0">
                                    {{ $t->updated_at ? $t->updated_at->diffForHumans(null, true, true) : 'new' }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-500 truncate leading-relaxed font-medium">
                                {{ $t->subject }}
                            </p>

                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                <x-support::pill variant="priority" :value="$t->priority" size="sm" />
                                <x-support::pill variant="role" :value="$initiatorType" size="sm" />
                            </div>
                        </div>
                    </a>
                @empty
                    <x-support::empty-state
                        icon="inbox"
                        title="No tickets found"
                        description="No tickets match your current filters."
                    />
                @endforelse
            </div>

            <!-- Pagination -->
            @if($tickets->hasPages())
                <div class="p-2.5 border-t border-slate-200 bg-white">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 2: TICKET THREAD & EVIDENCE (6 cols)                        -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 rounded-2xl flex flex-col h-full overflow-hidden min-w-0 flex-1">
            @if($activeTicket)
                <div class="flex-1 flex flex-col h-full overflow-hidden">
                    <!-- Ticket Header -->
                    <div class="px-5 py-3.5 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-3">
                            @php
                                $avatarType = $activeTicket->seller_id ? 'seller' : 'customer';
                                $avatarName = $activeTicket->seller_id ? ($activeTicket->seller->sellerProfile->shop_name ?? $activeTicket->seller->name) : ($activeTicket->user->name ?? 'Unknown');
                                $avatarStatus = $activeTicket->is_escalated ? 'escalated' : ($activeTicket->status === 'open' ? 'open' : ($activeTicket->status === 'in_progress' ? 'progress' : 'closed'));
                            @endphp
                            <x-support::avatar
                                :type="$avatarType"
                                size="lg"
                                :name="$avatarName"
                                :status="$avatarStatus"
                            />

                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h2 class="font-heading font-bold text-sm text-slate-900">{{ $activeTicket->ticket_number }}</h2>
                                    <x-support::pill variant="status" :value="$activeTicket->status" size="sm" dot="true" />
                                    <x-support::pill variant="priority" :value="$activeTicket->priority" size="sm" dot="true" />
                                    @if($activeTicket->is_escalated)
                                        <x-support::pill variant="escalated" value="escalated" size="sm" :animate="true" />
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 font-mono">
                                    {{ $activeTicket->seller_id ? 'Seller: ' . ($activeTicket->seller->sellerProfile->shop_name ?? $activeTicket->seller->name) : 'Customer: ' . ($activeTicket->user->name ?? 'Unknown') }}
                                    · {{ $activeTicket->created_at ? $activeTicket->created_at->format('M d, Y h:i A') : 'Today' }}
                                </p>
                            </div>
                        </div>

                        <!-- Header Actions -->
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.support.tickets.index', ['view' => $viewMode, 'tab' => $tab]) }}" method="GET">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors support-focus flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                    <span>Back to List</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Ticket Meta Bar -->
                    <div class="px-5 py-2.5 bg-slate-50/50 border-b border-slate-200 flex flex-wrap items-center gap-4 text-xs shrink-0">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400">Category:</span>
                            @php $categoryLabel = ucfirst(str_replace('_', ' ', $activeTicket->category)); @endphp
                            <x-support::pill variant="default" :value="$categoryLabel" size="sm" />
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-400">Priority:</span>
                            <x-support::pill variant="priority" :value="$activeTicket->priority" size="sm" />
                        </div>
                        @if($activeTicket->order_id)
                            <div class="flex items-center gap-1.5">
                                <span class="text-slate-400">Order:</span>
                                <a href="{{ route('admin.orders.show', $activeTicket->order_id) }}" class="px-2 py-0.5 rounded bg-white border border-slate-200 font-mono text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors support-focus">
                                    #{{ $activeTicket->order->order_number ?? $activeTicket->order_id }}
                                </a>
                            </div>
                        @endif
                        @if($activeTicket->seller_id && $activeTicket->seller->sellerProfile)
                            <div class="flex items-center gap-1.5">
                                <span class="text-slate-400">Store:</span>
                                <a href="{{ route('admin.sellers.show', $activeTicket->seller->sellerProfile->id) }}" class="px-2 py-0.5 rounded bg-white border border-slate-200 font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 transition-colors support-focus">
                                    {{ $activeTicket->seller->sellerProfile->shop_name }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Message Thread -->
                    <div class="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50/60 scroll-smooth" id="ticket-chat-thread">

                        <div class="flex items-center justify-center my-2">
                            <span class="px-3 py-1 bg-white border border-slate-200 text-slate-400 text-[10px] font-mono rounded-full uppercase shadow-2xs">
                                Ticket Conversation Timeline
                            </span>
                        </div>

                        @foreach($activeTicket->messages as $msg)
                            @php
                                $sender = $msg->sender_type;
                                $senderName = $sender === 'customer' ? ($msg->user?->name ?? 'Customer') : ($sender === 'seller' ? ($msg->user?->sellerProfile?->shop_name ?? $msg->user?->name ?? 'Seller') : ($sender === 'agent' ? ($msg->sender_name ?? 'Live Agent') : 'System'));
                                $isArbitration = str_contains($msg->message, 'ARBITRATION') || str_contains($msg->message, 'ARBITRATED') || str_contains($msg->message, 'ESCALATED');
                                $isEscalation = str_contains($msg->message, 'ESCALATED');
                                $isStatusChange = str_contains($msg->message, 'updated status') || str_contains($msg->message, 'status to:');
                                $systemType = $isArbitration ? 'arbitration' : ($isEscalation ? 'escalation' : ($isStatusChange ? 'status-change' : null));
                                $messageTime = $msg->created_at ? $msg->created_at->format('h:i A') : '';
                            @endphp
                            <x-support::bubble
                                :sender="$sender"
                                :message="$msg->message"
                                :time="$messageTime"
                                :name="$senderName"
                                :attachment="$msg->attachment_url"
                                :system-type="$systemType"
                            />
                        @endforeach

                        <!-- Polled Messages -->
                        <template x-for="pmsg in polledMessages" :key="pmsg.id">
                            <div class="w-full flex" :class="['customer', 'seller'].includes(pmsg.sender_type) ? 'justify-start' : 'justify-center'">
                                <div class="p-3 rounded-2xl text-xs max-w-[85%]" :class="pmsg.sender_type === 'customer' ? 'bg-white border border-slate-200 text-slate-800' : (pmsg.sender_type === 'seller' ? 'bg-indigo-50 border border-indigo-100 text-slate-800' : 'bg-slate-100 border border-slate-200 text-slate-700')">
                                    <div class="chat-markdown" x-html="renderChatMarkdown(pmsg.message)"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Admin Reply Form -->
                    <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                        <form action="{{ route('admin.support.tickets.show', $activeTicket) }}#ticket-chat-thread" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="_method" value="POST">

                            <!-- File Upload -->
                            <label class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-colors support-focus" title="Attach evidence/document">
                                <span class="material-symbols-outlined text-[20px]">attach_file</span>
                                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" @change="previewAttachment($event)">
                            </label>

                            <!-- Input -->
                            <div class="flex-1 relative">
                                <textarea name="message"
                                          x-model="replyText"
                                          rows="2"
                                          placeholder="Type admin response or arbitration notes..."
                                          class="w-full bg-slate-50 text-slate-800 text-xs px-4 py-3 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-500 focus:outline-none transition-all resize-none support-focus"></textarea>
                            </div>

                            <!-- Send Button -->
                            <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-heading font-semibold text-xs flex items-center gap-2 shadow-sm transition-colors cursor-pointer support-focus">
                                <span>Reply</span>
                                <span class="material-symbols-outlined text-[16px]">send</span>
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-slate-400 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl text-slate-400">gavel</span>
                    </div>
                    <h3 class="font-heading font-bold text-base text-slate-700">No Ticket Selected</h3>
                    <p class="text-xs max-w-sm mt-1">Select a ticket from the left panel to begin arbitration and review the conversation thread.</p>
                </div>
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 3: ARBITRATION PANEL & CONTEXT (3 cols)                     -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="hidden lg:flex bg-white border border-slate-200 rounded-2xl flex-col h-full overflow-y-auto p-5 space-y-6 support-stagger lg:w-[320px] min-w-0">
            @if($activeTicket)
                <!-- ARBITRATION ACTIONS CARD -->
                <x-support::card elevation="overlay" class="bg-gradient-to-br from-rose-50 to-amber-50 border border-rose-100">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-[18px] text-rose-600">gavel</span>
                        <h3 class="font-heading font-bold text-sm text-rose-900">Arbitration Actions</h3>
                    </div>
                    <p class="text-[11px] text-rose-700 mb-4">Review evidence, hear both parties, and issue a binding decision.</p>

                    <form action="{{ route('admin.support.tickets.arbitrate', $activeTicket) }}" method="POST" class="space-y-3" @submit.prevent="submitArbitration">
                        @csrf

                        <!-- Decision Selection -->
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-2">Decision</label>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-rose-400 hover:bg-rose-50 focus:ring-rose-500 cursor-pointer transition-colors" x-on:click="selectedDecision = 'approve_refund'">
                                    <input type="radio" name="decision" value="approve_refund" class="text-rose-600 focus:ring-rose-500" :checked="selectedDecision === 'approve_refund'" aria-label="Approve Refund to Buyer">
                                    <span class="material-symbols-outlined text-[20px] text-rose-600 shrink-0">check_circle</span>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-xs text-slate-900 truncate block">Approve Refund to Buyer</span>
                                        <p class="text-[10px] text-slate-500 truncate">Refund buyer, void seller payout, mark return as refunded</p>
                                    </div>
                                    <span x-show="selectedDecision === 'approve_refund'" class="material-symbols-outlined text-[20px] text-rose-600 shrink-0">check</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 focus:ring-emerald-500 cursor-pointer transition-colors" x-on:click="selectedDecision = 'reject_refund'">
                                    <input type="radio" name="decision" value="reject_refund" class="text-emerald-600 focus:ring-emerald-500" :checked="selectedDecision === 'reject_refund'" aria-label="Reject Refund / Release to Seller">
                                    <span class="material-symbols-outlined text-[20px] text-emerald-600 shrink-0">block</span>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-xs text-slate-900 truncate block">Reject Refund / Release to Seller</span>
                                        <p class="text-[10px] text-slate-500 truncate">Deny buyer claim, release escrow to seller, close ticket</p>
                                    </div>
                                    <span x-show="selectedDecision === 'reject_refund'" class="material-symbols-outlined text-[20px] text-emerald-600 shrink-0">check</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50 focus:ring-blue-500 cursor-pointer transition-colors" x-on:click="selectedDecision = 'release_escrow'">
                                    <input type="radio" name="decision" value="release_escrow" class="text-blue-600 focus:ring-blue-500" :checked="selectedDecision === 'release_escrow'" aria-label="Release Escrow to Seller">
                                    <span class="material-symbols-outlined text-[20px] text-blue-600 shrink-0">payments</span>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-xs text-slate-900 truncate block">Release Escrow to Seller</span>
                                        <p class="text-[10px] text-slate-500 truncate">Seller fulfilled obligation, release funds, close ticket</p>
                                    </div>
                                    <span x-show="selectedDecision === 'release_escrow'" class="material-symbols-outlined text-[20px] text-blue-600 shrink-0">check</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 focus:ring-indigo-500 cursor-pointer transition-colors" x-on:click="selectedDecision = 'custom'">
                                    <input type="radio" name="decision" value="custom" class="text-indigo-600 focus:ring-indigo-500" :checked="selectedDecision === 'custom'" aria-label="Custom Arbitration">
                                    <span class="material-symbols-outlined text-[20px] text-indigo-600 shrink-0">gavel</span>
                                    <div class="flex-1 min-w-0">
                                        <span class="font-semibold text-xs text-slate-900 truncate block">Custom Arbitration</span>
                                        <p class="text-[10px] text-slate-500 truncate">Specify custom resolution action</p>
                                    </div>
                                    <span x-show="selectedDecision === 'custom'" class="material-symbols-outlined text-[20px] text-indigo-600 shrink-0">check</span>
                                </label>
                            </div>
                        </div>

                        <!-- Custom Action Input -->
                        <div x-show="selectedDecision === 'custom'" x-transition class="mt-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Custom Action Description</label>
                            <input type="text" name="custom_action" x-model="customAction" placeholder="e.g. Partial refund 50%, split shipping cost, replace item"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all support-focus">
                        </div>

                        <!-- Refund Amount (for approve_refund) -->
                        <div x-show="selectedDecision === 'approve_refund'" x-transition class="mt-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Refund Amount (₹)</label>
                            <input type="number" name="refund_amount" step="0.01" min="0" :max="maxRefund" x-model.number="refundAmount"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all support-focus"
                                   :placeholder="'Max: ₹' + maxRefund.toLocaleString()">
                        </div>

                        <!-- Arbitration Notes -->
                        <div class="mt-2">
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-700 mb-1.5">Arbitration Notes / Reasoning <span class="text-rose-500">*</span></label>
                            <textarea name="arbitration_notes" x-model="arbitrationNotes" rows="3" required
                                      placeholder="Document your reasoning, evidence reviewed, and decision rationale for the record."
                                      class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all support-focus"></textarea>
                        </div>

                        <!-- Submit Arbitration -->
                        <button type="submit"
                                :disabled="!canSubmit"
                                class="w-full px-4 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-heading font-semibold text-xs flex items-center justify-center gap-2 shadow-md transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed support-focus">
                            <span class="material-symbols-outlined text-[18px]">gavel</span>
                            <span>Issue Arbitration Decision</span>
                        </button>
                    </form>
                </x-support::card>

                <!-- ESCALATION REASON (if escalated) -->
                @if($activeTicket->is_escalated && $activeTicket->escalation_reason)
                    <x-support::card elevation="surface" class="bg-amber-50 border border-amber-200">
                        <h4 class="font-mono text-[10px] font-bold text-amber-800 uppercase tracking-wider mb-2 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">warning</span>
                            Escalation Reason
                        </h4>
                        <p class="text-xs text-amber-900 bg-white p-3 rounded-xl border border-amber-200 whitespace-pre-line">{{ $activeTicket->escalation_reason }}</p>
                        <p class="text-[10px] text-amber-700 mt-2 font-mono">Escalated: {{ $activeTicket->escalated_at ? $activeTicket->escalated_at->format('M d, Y h:i A') : 'Unknown' }}</p>
                    </x-support::card>
                @endif

                <!-- RELATED RETURN/DISPUTE -->
                @if($relatedReturn)
                    <x-support::card elevation="surface" class="bg-blue-50 border border-blue-200">
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
                                <x-support::pill variant="status" :value="$relatedReturn->status" size="sm" />
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
                    </x-support::card>
                @endif

                <!-- BUYER / CUSTOMER INFO -->
                <x-support::card elevation="surface">
                    <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">person</span>
                        Customer / Buyer
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                                <x-support::avatar type="customer" size="lg" :name="$activeTicket->user->name ?? 'Unknown'" />
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
                                <span class="font-medium text-slate-700">{{ $activeTicket->user->supportTicketsCount ?? \App\Models\SupportTicket::where('user_id', $activeTicket->user_id)->count() }}</span>
                            </div>
                        </div>
                        @if($activeTicket->user)
                            <a href="{{ route('admin.customers.show', $activeTicket->user->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block support-focus">
                                View Full Profile
                            </a>
                        @endif
                    </div>
                </x-support::card>

                <!-- SELLER INFO (if seller ticket or order has seller) -->
                @if($activeTicket->seller_id || ($activeTicket->order && $activeTicket->order->sellerOrders->count()))
                    @php
                        $seller = $activeTicket->seller ?? ($activeTicket->order ? $activeTicket->order->sellerOrders->first()?->seller : null);
                        $sellerProfile = $seller?->sellerProfile;
                    @endphp
                    @if($seller && $sellerProfile)
                        <x-support::card elevation="surface">
                            <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">store</span>
                                Seller / Merchant
                            </h4>
                            <div class="space-y-3">
                                <div class="flex items-center gap-3">
                                    <x-support::avatar type="seller" size="lg" :name="$sellerProfile->shop_name" />
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-heading font-semibold text-xs text-slate-900 truncate">{{ $sellerProfile->shop_name }}</h5>
                                        <p class="text-[10px] text-slate-500 font-mono">{{ $seller->email }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200">
                                    <div>
                                        <span class="text-[9px] text-slate-400 block font-mono">KYC Status</span>
                                        <x-support::pill variant="status" :value="$sellerProfile->status" size="sm" />
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 block font-mono">Trust Score</span>
                                        <span class="font-mono font-medium text-slate-700">{{ $sellerProfile->trust_score ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.sellers.show', $sellerProfile->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block support-focus">
                                    View Merchant Dossier
                                </a>
                            </div>
                        </x-support::card>
                    @endif
                @endif

                <!-- ORDER DETAILS -->
                @if($activeTicket->order)
                    <x-support::card elevation="surface">
                        <h4 class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">shopping_cart</span>
                            Order Details
                        </h4>
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Order Number</span>
                                <span class="font-mono font-medium text-slate-900">{{ $activeTicket->order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Status</span>
                                <x-support::pill variant="status" :value="$activeTicket->order->order_status" size="sm" />
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Payment</span>
                                <x-support::pill variant="status" :value="$activeTicket->order->payment_status" size="sm" />
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Amount</span>
                                <span class="font-mono font-medium text-slate-900">₹{{ number_format($activeTicket->order->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Order Date</span>
                                <span class="font-mono font-medium text-slate-700">{{ $activeTicket->order->created_at->format('M d, Y') }}</span>
                            </div>
                            <a href="{{ route('admin.orders.show', $activeTicket->order->id) }}" class="w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-[10px] font-semibold rounded-xl text-center transition-colors block support-focus">
                                View Full Order Dossier
                            </a>
                        </div>
                    </x-support::card>
                @endif

            @else
                <div class="text-center py-12 text-slate-400 text-xs">
                    <p>Select a ticket to view arbitration context.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function adminTicketArbitration(config) {
        return {
            replyText: '',
            polledMessages: [],
            lastId: 0,
            pollTimer: null,
            selectedDecision: '',
            customAction: '',
            arbitrationNotes: '',
            refundAmount: 0,
            maxRefund: {{ $activeTicket && $activeTicket->order ? $activeTicket->order->total_amount : 0 }},

            init() {
                this.scrollToBottom();
                if (config.ticketId && config.pollUrl) {
                    this.startPolling();
                }
                // Watch for decision changes
                this.$watch('selectedDecision', (val) => {
                    this.selectedDecision = val;
                });
            },

            get canSubmit() {
                if (!this.arbitrationNotes.trim()) return false;
                if (this.selectedDecision === 'custom' && !this.customAction.trim()) return false;
                if (this.selectedDecision === 'approve_refund' && this.refundAmount <= 0) return false;
                return this.selectedDecision !== '';
            },

            renderChatMarkdown(text) {
                if (window.renderChatMarkdown) return window.renderChatMarkdown(text);
                return text;
            },

            previewAttachment(event) {
                const file = event.target.files[0];
                if (file && window.showToast) {
                    window.showToast('Selected file: ' + file.name, 'info');
                }
            },

            submitArbitration() {
                if (!this.canSubmit) return;
                if (!confirm('Are you sure you want to issue this arbitration decision? This action cannot be undone.')) {
                    return;
                }
                // Form will submit normally
            },

            startPolling() {
                this.pollTimer = setInterval(async () => {
                    if (!config.ticketId) return;
                    try {
                        const res = await fetch(`${config.pollUrl}?after_id=${this.lastId}`);
                        const data = await res.json();
                        if (data.success && data.messages && data.messages.length > 0) {
                            data.messages.forEach(m => {
                                if (!this.polledMessages.find(ex => ex.id === m.id)) {
                                    this.polledMessages.push(m);
                                    this.lastId = Math.max(this.lastId, m.id);
                                }
                            });
                            this.scrollToBottom();
                        }
                    } catch (e) {}
                }, 5000);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const thread = document.getElementById('ticket-chat-thread');
                    if (thread) {
                        thread.scrollTop = thread.scrollHeight;
                    }
                });
            }
        };
    }
</script>
@endsection