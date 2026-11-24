@extends('layouts.admin')

@section('title', 'Unified Support Console — ' . \App\Models\SiteSetting::getSiteName())

@section('content')
<div x-data="adminUnifiedSupport({
    viewMode: '{{ $viewMode }}',
    currentTab: '{{ $tab }}',
    conversationId: {{ $activeConversation ? $activeConversation->id : 'null' }},
    ticketId: {{ $activeTicket ? $activeTicket->id : 'null' }},
    csrfToken: '{{ csrf_token() }}',
    chatPollUrl: '{{ $activeConversation ? route('admin.support.poll', $activeConversation->id) : '' }}',
    ticketPollUrl: '{{ $activeTicket ? route('admin.support.tickets.poll', $activeTicket) : '' }}'
})" class="h-[calc(100vh-68px)] flex flex-col overflow-hidden">

    <!-- Top Sub-Header Bar -->
    <div class="px-6 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 shadow-support-surface">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-600 to-rose-600 text-white flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]">dashboard_customize</span>
            </div>
            <div>
                <h1 class="font-heading font-bold text-base text-slate-900 leading-tight">Unified Support Console</h1>
                <p class="text-[11px] text-slate-500 font-sans">Live Chat Triage • Support Ticket Management • Dispute Arbitration</p>
            </div>
        </div>

        <!-- View Mode Switcher -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl">
            <a href="{{ route('admin.support.index', ['view' => 'chats', 'tab' => 'open']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all support-focus {{ $viewMode === 'chats' ? 'bg-white text-blue-700 shadow-support-surface' : 'text-slate-600 hover:text-slate-900' }} flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">chat_bubble</span>
                Live Chats
            </a>
            <a href="{{ route('admin.support.tickets.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all support-focus {{ $viewMode === 'tickets' ? 'bg-white text-amber-700 shadow-support-surface' : 'text-slate-600 hover:text-slate-900' }} flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">confirmation_number</span>
                Support Tickets
            </a>
            <a href="{{ route('admin.support.tickets.escalated') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold transition-all support-focus {{ $viewMode === 'escalated' ? 'bg-white text-rose-700 shadow-support-surface' : 'text-slate-600 hover:text-slate-900' }} flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">gavel</span>
                Escalated
            </a>
        </div>
    </div>

    <!-- 3-Column Support Grid -->
    <div class="flex-1 min-w-0 grid grid-cols-1 lg:grid-cols-[360px_1fr_320px] gap-4 p-4 overflow-hidden">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 1: QUEUE / NAVIGATION (360px fixed)                       -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 rounded-2xl flex flex-col h-full overflow-hidden support-stagger lg:w-[360px] min-w-0">

            @if($viewMode === 'chats')
                <!-- LIVE CHAT QUEUE -->
                <div class="px-4 py-3 border-b border-slate-200 bg-white shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">chat_bubble</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-heading font-bold text-sm text-slate-900 truncate">Live Conversations</h3>
                            <p class="text-[11px] text-slate-500 font-sans">Real-time customer chat triage</p>
                        </div>
                        <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                            Active {{ $chatOpenCount + $chatPendingCount }}
                        </span>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="px-4 pb-3">
                    @php
                        $chatTabItems = [
                            ['label' => 'Open', 'url' => route('admin.support.index', ['view' => 'chats', 'tab' => 'open']), 'count' => $chatOpenCount, 'icon' => 'chat', 'active' => $tab === 'open', 'color' => 'blue'],
                            ['label' => 'Pending', 'url' => route('admin.support.index', ['view' => 'chats', 'tab' => 'pending']), 'count' => $chatPendingCount, 'icon' => 'hourglass_top', 'active' => $tab === 'pending', 'color' => 'amber'],
                            ['label' => 'Resolved', 'url' => route('admin.support.index', ['view' => 'chats', 'tab' => 'resolved']), 'count' => $chatResolvedCount, 'icon' => 'check_circle', 'active' => $tab === 'resolved', 'color' => 'emerald'],
                        ];
                        $colorMap = [
                            'slate' => ['active' => 'bg-white text-slate-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                            'blue' => ['active' => 'bg-white text-blue-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                            'amber' => ['active' => 'bg-white text-amber-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                            'emerald' => ['active' => 'bg-white text-emerald-700 shadow-xs', 'inactive' => 'text-slate-600 hover:text-slate-900'],
                        ];
                    @endphp
                    <div class="flex flex-col gap-1">
                        @foreach($chatTabItems as $item)
                            @php
                                $colors = $colorMap[$item['color']] ?? $colorMap['slate'];
                                $classes = 'px-3 py-1.5 rounded-lg text-xs font-semibold transition-all ' . ($item['active'] ? $colors['active'] : $colors['inactive']);
                            @endphp
                            <a href="{{ $item['url'] }}" class="{{ $classes }} flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">{{ $item['icon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono {{ $item['active'] ? 'bg-' . $item['color'] . '-100 text-' . $item['color'] . '-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $item['count'] }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Search -->
                <div class="px-4 pb-3">
                    <form method="GET" action="{{ route('admin.support.index') }}">
                        <input type="hidden" name="view" value="chats">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search conversations..."
                                   class="w-full bg-slate-50 text-slate-800 text-xs pl-9 pr-3 py-2 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-400 focus:outline-none transition-colors support-focus">
                        </div>
                    </form>
                </div>

                <!-- Conversations List -->
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @forelse($conversations as $conv)
                        @php
                            $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                            $customerName = $conv->customer_display_name;
                            $latest = $conv->latestMessage;
                            $status = $conv->status === 'open' ? 'open' : ($conv->status === 'pending' ? 'waiting' : 'closed');
                        @endphp
                        <a href="{{ route('admin.support.index', ['view' => 'chats', 'conversation_id' => $conv->id, 'tab' => $tab, 'search' => request('search')]) }}"
                           class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors block relative support-focus {{ $isSelected ? 'bg-blue-50/70 border-l-4 border-blue-600' : '' }}">

                            <x-support::avatar
                                type="customer"
                                size="md"
                                :name="$customerName"
                                :status="$status"
                                :sr-label="'Chat with ' . $customerName"
                            />

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h3 class="font-heading font-semibold text-xs text-slate-900 truncate {{ $isSelected ? 'text-blue-900 font-bold' : '' }}">
                                        {{ $customerName }}
                                    </h3>
                                    <span class="font-mono text-[10px] text-slate-400 shrink-0">
                                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(null, true, true) : 'new' }}
                                    </span>
                                </div>

                                <p class="text-[11px] text-slate-500 truncate leading-relaxed">
                                    {{ $latest ? $latest->message : 'Session initiated.' }}
                                </p>

                                <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                    @if($conv->priority === 'high')
                                        <x-support::pill variant="priority" value="high" size="sm" />
                                    @endif
                                    @if($conv->user)
                                        <x-support::pill variant="role" value="customer" size="sm" />
                                    @else
                                        <x-support::pill variant="role" value="system" size="sm" label="Visitor" />
                                    @endif
                                    @if($conv->assignedAgent)
                                        <span class="text-[9px] text-slate-400 truncate">Agent: {{ $conv->assignedAgent->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @empty
                        <x-support::empty-state
                            icon="chat_bubble_outline"
                            title="No conversations found"
                            description="No {{ $tab }} conversations at the moment."
                        />
                    @endforelse
                </div>

                @if($conversations->hasPages())
                    <div class="p-2.5 border-t border-slate-200 bg-white">
                        {{ $conversations->links() }}
                    </div>
                @endif

            @else
                <!-- SUPPORT TICKET QUEUE -->
                <x-support::section-header
                    icon="confirmation_number"
                    title="Support Tickets"
                    subtitle="Customer & merchant inquiry management"
                    :count="$tickets->total()"
                    countLabel="Total"
                />

                <!-- Sub-tabs for tickets -->
                <div class="px-4 pb-3">
                    <x-support::tabs
                        :vertical="true"
                        :items="[
                            ['label' => 'Open', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'open']), 'count' => $ticketOpenCount, 'icon' => 'inbox', 'active' => $tab === 'open', 'color' => 'amber'],
                            ['label' => 'Seller Tickets', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'seller']), 'count' => $ticketSellerCount, 'icon' => 'store', 'active' => $tab === 'seller', 'color' => 'indigo'],
                            ['label' => 'Customer Tickets', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'customer']), 'count' => $ticketCustomerCount, 'icon' => 'person', 'active' => $tab === 'customer', 'color' => 'blue'],
                            ['label' => 'Resolved', 'url' => route('admin.support.tickets.index', ['view' => 'tickets', 'tab' => 'resolved']), 'count' => $ticketResolvedCount, 'icon' => 'check_circle', 'active' => $tab === 'resolved', 'color' => 'emerald'],
                            ['label' => 'Escalated', 'url' => route('admin.support.tickets.escalated'), 'count' => $ticketEscalatedCount, 'icon' => 'gavel', 'active' => $viewMode === 'escalated', 'color' => 'rose'],
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
                            $initiatorName = $t->seller_id ? ($t->seller?->sellerProfile?->shop_name ?? $t->seller?->name ?? 'Seller') : ($t->user?->name ?? 'Unknown');
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
                                    @if($t->is_escalated)
                                        <x-support::pill variant="escalated" value="escalated" size="sm" :animate="true" />
                                    @endif
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

                @if($tickets->hasPages())
                    <div class="p-2.5 border-t border-slate-200 bg-white">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 2: ACTIVE PANEL - CHAT OR TICKET (5.5 cols)                  -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="bg-white border border-slate-200 rounded-2xl flex flex-col h-full overflow-hidden min-w-0 flex-1">

            @if($viewMode === 'chats')
                <!-- LIVE CHAT PANEL -->
                @if($activeConversation)
                    @include('admin.support.partials.chat-panel')
                @else
                    <div class="flex-1 flex flex-col items-center justify-center p-8 text-slate-400 text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                            <span class="material-symbols-outlined text-3xl text-slate-400">chat_bubble_outline</span>
                        </div>
                        <h3 class="font-heading font-bold text-base text-slate-700">No Conversation Selected</h3>
                        <p class="text-xs max-w-sm mt-1">Select a conversation from the left panel to start chatting.</p>
                    </div>
                @endif

            @else
                <!-- SUPPORT TICKET PANEL -->
                @if($activeTicket)
                    <div class="flex-1 flex flex-col h-full overflow-hidden">
                        <!-- Ticket Header -->
                        <div class="px-5 py-3.5 border-b border-slate-200 bg-white flex flex-wrap items-center justify-between gap-3 shrink-0">
                            <div class="flex items-center gap-3">
                                @php
                                    $avatarType = $activeTicket->seller_id ? 'seller' : 'customer';
                                    $avatarName = $activeTicket->seller_id ? ($activeTicket->seller?->sellerProfile?->shop_name ?? $activeTicket->seller?->name ?? 'Seller') : ($activeTicket->user?->name ?? 'Unknown');
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
                                        {{ $activeTicket->seller_id ? 'Seller: ' . ($activeTicket->seller?->sellerProfile?->shop_name ?? $activeTicket->seller?->name ?? 'Seller') : 'Customer: ' . ($activeTicket->user?->name ?? 'Unknown') }}
                                        · {{ $activeTicket->created_at ? $activeTicket->created_at->format('M d, Y h:i A') : 'Today' }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.support.tickets.show', $activeTicket) }}" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold flex items-center gap-1 transition-colors support-focus">
                                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                                    Arbitrate
                                </a>
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
                            @if($activeTicket->assignedAgent)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400">Assigned:</span>
                                    <span class="font-medium text-slate-800">{{ $activeTicket->assignedAgent->name }}</span>
                                </div>
                            @endif
                            @if($activeTicket->order_id)
                                <div class="flex items-center gap-1.5">
                                    <span class="text-slate-400">Order:</span>
                                    <a href="{{ route('admin.orders.show', $activeTicket->order_id) }}" class="px-2 py-0.5 rounded bg-white border border-slate-200 font-mono text-blue-600 hover:text-blue-800 hover:bg-blue-50 transition-colors support-focus">
                                        #{{ $activeTicket->order->order_number ?? $activeTicket->order_id }}
                                    </a>
                                </div>
                            @endif
                            @if($activeTicket->seller_id && $activeTicket->seller?->sellerProfile)
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
                                @endphp
                                <x-support::bubble
                                    :sender="$sender"
                                    :message="$msg->message"
                                    :time="$msg->created_at ? $msg->created_at->format('h:i A') : ''"
                                    :name="$senderName"
                                    :attachment="$msg->attachment_url"
                                    :system-type="$systemType"
                                />
                            @endforeach

                            <template x-for="pmsg in polledMessages" :key="pmsg.id">
                                <div class="w-full flex" :class="['customer', 'seller'].includes(pmsg.sender_type) ? 'justify-start' : 'justify-center'">
                                    <div class="p-3 rounded-2xl text-xs max-w-[85%]" :class="pmsg.sender_type === 'customer' ? 'bg-white border border-slate-200 text-slate-800' : (pmsg.sender_type === 'seller' ? 'bg-indigo-50 border border-indigo-100 text-slate-800' : 'bg-slate-100 border border-slate-200 text-slate-700')">
                                        <div class="chat-markdown" x-html="renderChatMarkdown(pmsg.message)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Admin Quick Reply -->
                        <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                            <form action="{{ route('admin.support.tickets.reply', $activeTicket) }}#ticket-chat-thread" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                                @csrf
                                <label class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-colors support-focus" title="Attach evidence/document">
                                    <span class="material-symbols-outlined text-[20px]">attach_file</span>
                                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" @change="previewAttachment($event)">
                                </label>

                                <div class="flex-1 relative">
                                    <textarea name="message"
                                              x-model="replyText"
                                              rows="2"
                                              placeholder="Type admin response..."
                                              class="w-full bg-slate-50 text-slate-800 text-xs px-4 py-3 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-500 focus:outline-none transition-all resize-none support-focus"></textarea>
                                </div>

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
                            <span class="material-symbols-outlined text-3xl text-slate-400">{{ $viewMode === 'escalated' ? 'gavel' : 'confirmation_number' }}</span>
                        </div>
                        <h3 class="font-heading font-bold text-base text-slate-700">{{ $viewMode === 'escalated' ? 'No Escalated Ticket Selected' : 'No Ticket Selected' }}</h3>
                        <p class="text-xs max-w-sm mt-1">Select a ticket from the left panel to review the conversation thread and arbitrate.</p>
                    </div>
                @endif
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 3: CONTEXT & INTELLIGENCE (320px fixed)                   -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="hidden lg:flex bg-white border border-slate-200 rounded-2xl flex-col h-full overflow-y-auto p-5 space-y-6 lg:w-[320px] min-w-0">

            @if($viewMode === 'chats' && $activeConversation)
                <!-- CHAT CONTEXT -->
                @include('admin.support.partials.chat-context')

            @elseif(($viewMode === 'tickets' || $viewMode === 'escalated') && $activeTicket)
                <!-- TICKET ARBITRATION CONTEXT -->
                @include('admin.support.partials.ticket-context')

            @else
                <div class="text-center py-12 text-slate-400 text-xs">
                    <p>{{ $viewMode === 'chats' ? 'Select a conversation to view customer intelligence.' : 'Select a ticket to view arbitration context.' }}</p>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function adminUnifiedSupport(config) {
        return {
            // Chat state
            replyText: '',
            chatPolledMessages: [],
            chatLastId: 0,
            chatPollTimer: null,

            // Ticket state
            ticketReplyText: '',
            ticketPolledMessages: [],
            ticketLastId: 0,
            ticketPollTimer: null,

            // View mode
            viewMode: config.viewMode,
            currentTab: config.currentTab,

            init() {
                // Chat polling
                if (config.conversationId && config.chatPollUrl) {
                    this.startChatPolling();
                }
                // Ticket polling
                if (config.ticketId && config.ticketPollUrl) {
                    this.startTicketPolling();
                }
                this.scrollToBottom('admin-chat-thread');
                this.scrollToBottom('ticket-chat-thread');
            },

            // === CHAT METHODS ===
            insertCanned(text) {
                this.replyText = text;
            },

            startChatPolling() {
                this.chatPollTimer = setInterval(async () => {
                    if (!config.conversationId) return;
                    try {
                        const res = await fetch(`${config.chatPollUrl}?after_id=${this.chatLastId}`);
                        const data = await res.json();
                        if (data.success && data.messages && data.messages.length > 0) {
                            data.messages.forEach(m => {
                                if (!this.chatPolledMessages.find(ex => ex.id === m.id)) {
                                    this.chatPolledMessages.push(m);
                                    this.chatLastId = Math.max(this.chatLastId, m.id);
                                }
                            });
                            this.scrollToBottom('admin-chat-thread');
                        }
                    } catch (e) {}
                }, 4000);
            },

            // === TICKET METHODS ===
            startTicketPolling() {
                this.ticketPollTimer = setInterval(async () => {
                    if (!config.ticketId) return;
                    try {
                        const res = await fetch(`${config.ticketPollUrl}?after_id=${this.ticketLastId}`);
                        const data = await res.json();
                        if (data.success && data.messages && data.messages.length > 0) {
                            data.messages.forEach(m => {
                                if (!this.ticketPolledMessages.find(ex => ex.id === m.id)) {
                                    this.ticketPolledMessages.push(m);
                                    this.ticketLastId = Math.max(this.ticketLastId, m.id);
                                }
                            });
                            this.scrollToBottom('ticket-chat-thread');
                        }
                    } catch (e) {}
                }, 5000);
            },

            previewAttachment(event) {
                const file = event.target.files[0];
                if (file && window.showToast) {
                    window.showToast('Selected file: ' + file.name, 'info');
                }
            },

            renderChatMarkdown(text) {
                if (window.renderChatMarkdown) return window.renderChatMarkdown(text);
                return text;
            },

            scrollToBottom(elementId) {
                this.$nextTick(() => {
                    const thread = document.getElementById(elementId);
                    if (thread) {
                        thread.scrollTop = thread.scrollHeight;
                    }
                });
            }
        };
    }
</script>
@endsection