@extends('layouts.admin')

@section('title', 'Live Agent Chat & Support Desk — ' . \App\Models\SiteSetting::getSiteName())

@section('content')
<div x-data="adminSupportDesk({
    conversationId: {{ $activeConversation ? $activeConversation->id : 'null' }},
    currentTab: '{{ $tab }}',
    csrfToken: '{{ csrf_token() }}',
    pollUrl: '{{ $activeConversation ? route('admin.support.poll', $activeConversation->id) : '' }}'
})" class="h-[calc(100vh-68px)] flex flex-col -m-4 md:-m-space-xl overflow-hidden">

    <!-- Top Sub-Header Bar -->
    <div class="px-6 py-3.5 bg-white border-b border-slate-200 flex items-center justify-between shrink-0 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]">headset_mic</span>
            </div>
            <div>
                <h1 class="font-heading font-bold text-base text-slate-900 leading-tight">ChatSupport Desk</h1>
                <p class="text-[11px] text-slate-500 font-sans">Live customer triage, multi-agent dispatch, &amp; order telemetry</p>
            </div>
        </div>

        <!-- Quick Status Pills -->
        <div class="flex items-center gap-4 text-xs">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 border border-slate-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-medium text-slate-700">Live Agent Desk: <strong>Active</strong></span>
            </div>
            <div class="hidden sm:flex items-center gap-2 text-slate-500 font-mono text-[11px]">
                <span>Agents Online: <strong class="text-blue-600">{{ count($agents) }}</strong></span>
            </div>
        </div>
    </div>

    <!-- 3-Column Support Grid -->
    <div class="flex-1 grid grid-cols-12 overflow-hidden bg-slate-100/70">

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 1: CONVERSATIONS QUEUE (3.5 cols)                            -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="col-span-12 md:col-span-4 lg:col-span-3.5 bg-white border-r border-slate-200 flex flex-col h-full overflow-hidden">
            
            <!-- Conversation Filters / Tabs -->
            <div class="p-3.5 border-b border-slate-200 space-y-3 shrink-0">
                <div class="flex items-center justify-between">
                    <h2 class="font-heading font-bold text-sm text-slate-900">Conversations</h2>
                    <span class="text-xs font-mono font-semibold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                        {{ $openCount + $pendingCount }} Active
                    </span>
                </div>

                <!-- Tabs: Open / Pending / Resolved -->
                <div class="flex items-center bg-slate-100 p-1 rounded-xl text-xs font-semibold">
                    <a href="{{ route('admin.support.index', ['tab' => 'open']) }}" 
                       class="flex-1 text-center py-1.5 rounded-lg transition-all {{ $tab === 'open' ? 'bg-white text-blue-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Open ({{ $openCount }})
                    </a>
                    <a href="{{ route('admin.support.index', ['tab' => 'pending']) }}" 
                       class="flex-1 text-center py-1.5 rounded-lg transition-all {{ $tab === 'pending' ? 'bg-white text-amber-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Pending ({{ $pendingCount }})
                    </a>
                    <a href="{{ route('admin.support.index', ['tab' => 'resolved']) }}" 
                       class="flex-1 text-center py-1.5 rounded-lg transition-all {{ $tab === 'resolved' ? 'bg-white text-emerald-700 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Resolved ({{ $resolvedCount }})
                    </a>
                </div>

                <!-- Search Input -->
                <form method="GET" action="{{ route('admin.support.index') }}" class="relative">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-slate-400">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search conversations..." 
                           class="w-full bg-slate-50 text-slate-800 text-xs pl-9 pr-3 py-2 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-400 focus:outline-none transition-colors">
                </form>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                @forelse($conversations as $conv)
                    @php
                        $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                        $customerName = $conv->customer_display_name;
                        $latest = $conv->latestMessage;
                    @endphp
                    <a href="{{ route('admin.support.index', ['conversation_id' => $conv->id, 'tab' => $tab]) }}"
                       class="p-3.5 flex items-start gap-3 hover:bg-slate-50 transition-colors block relative {{ $isSelected ? 'bg-blue-50/70 border-l-4 border-blue-600' : '' }}">
                        
                        <!-- Customer Avatar -->
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs ring-1 ring-slate-300">
                                {{ strtoupper(substr($customerName, 0, 2)) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full ring-2 ring-white {{ $conv->status === 'open' ? 'bg-emerald-500' : ($conv->status === 'pending' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                        </div>

                        <!-- Conversation Snippet -->
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

                            <!-- Tags / Metadata pills -->
                            <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                                @if($conv->priority === 'high')
                                    <span class="px-1.5 py-0.2 rounded bg-rose-100 text-rose-700 font-mono text-[9px] font-bold">URGENT</span>
                                @endif
                                @if($conv->user)
                                    <span class="px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 font-mono text-[9px]">Verified User</span>
                                @else
                                    <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-mono text-[9px]">Visitor</span>
                                @endif
                                @if($conv->assignedAgent)
                                    <span class="text-[9px] text-slate-400 truncate">Agent: {{ $conv->assignedAgent->name }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        <span class="material-symbols-outlined text-3xl mb-1 text-slate-300">inbox</span>
                        <p>No conversations found in {{ $tab }}.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if($conversations->hasPages())
                <div class="p-2.5 border-t border-slate-200 bg-white">
                    {{ $conversations->links() }}
                </div>
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 2: ACTIVE LIVE CHAT WINDOW (5.5 cols)                        -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="col-span-12 md:col-span-8 lg:col-span-5.5 bg-white flex flex-col h-full border-r border-slate-200 overflow-hidden">
            @if($activeConversation)
                <!-- Chat Window Header -->
                <div class="px-5 py-3.5 border-b border-slate-200 bg-white flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                {{ strtoupper(substr($activeConversation->customer_display_name, 0, 2)) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-heading font-bold text-sm text-slate-900">{{ $activeConversation->customer_display_name }}</h2>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase {{ $activeConversation->status === 'open' ? 'bg-emerald-100 text-emerald-800' : ($activeConversation->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-200 text-slate-700') }}">
                                    {{ $activeConversation->status }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-mono">
                                {{ $activeConversation->customer_email ?: 'No email registered' }} · Started {{ $activeConversation->created_at ? $activeConversation->created_at->format('M d, h:i A') : 'Today' }}
                            </p>
                        </div>
                    </div>

                    <!-- Header Action Buttons -->
                    <div class="flex items-center gap-2">
                        <!-- Status Transition Menu -->
                        <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST">
                            @csrf
                            <select name="status" onchange="this.form.submit()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-2.5 py-1.5 rounded-lg border-none font-semibold cursor-pointer">
                                <option value="open" {{ $activeConversation->status === 'open' ? 'selected' : '' }}>Mark Open</option>
                                <option value="pending" {{ $activeConversation->status === 'pending' ? 'selected' : '' }}>Mark Pending</option>
                                <option value="resolved" {{ $activeConversation->status === 'resolved' ? 'selected' : '' }}>Mark Resolved</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Live Message Thread Stream -->
                <div class="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50/60 scroll-smooth" id="admin-chat-thread">
                    
                    <!-- Date Divider -->
                    <div class="flex items-center justify-center my-2">
                        <span class="px-3 py-1 bg-white border border-slate-200 text-slate-400 text-[10px] font-mono rounded-full uppercase shadow-2xs">
                            Session Telemetry Log
                        </span>
                    </div>

                    @foreach($activeConversation->messages as $msg)
                        <div class="w-full flex {{ $msg->sender_type === 'agent' ? 'justify-end' : 'justify-start' }}">
                            
                            <!-- If User/Customer Message (Left) -->
                            @if($msg->sender_type === 'user')
                                <div class="flex items-start gap-2.5 max-w-[80%]">
                                    <div class="w-7 h-7 rounded-full bg-slate-300 text-slate-700 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        {{ strtoupper(substr($activeConversation->customer_display_name, 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <div class="bg-white border border-slate-200/90 p-3 rounded-2xl rounded-tl-sm text-xs text-slate-800 leading-relaxed shadow-sm">
                                            <div class="chat-markdown text-slate-800 leading-relaxed">
                                                {!! \Illuminate\Support\Str::markdown($msg->message) !!}
                                            </div>
                                            @if($msg->attachment_url)
                                                <div class="mt-2 rounded-xl overflow-hidden border border-slate-200">
                                                    <a href="{{ $msg->attachment_url }}" target="_blank">
                                                        <img src="{{ $msg->attachment_url }}" alt="Attachment" class="max-h-52 w-auto object-cover rounded-lg hover:opacity-95 transition-opacity">
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono ml-1">{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                    </div>
                                </div>

                            <!-- If AI Assistant Bot (Left, Distinct) -->
                            @elseif($msg->sender_type === 'bot')
                                <div class="flex items-start gap-2.5 max-w-[80%]">
                                    <div class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">
                                        <span class="material-symbols-outlined text-[16px]">smart_toy</span>
                                    </div>
                                    <div class="flex flex-col gap-1">
                                        <div class="bg-indigo-50/70 border border-indigo-100 p-3 rounded-2xl rounded-tl-sm text-xs text-slate-800 leading-relaxed shadow-sm">
                                            <div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-indigo-100 text-indigo-600 font-mono text-[10px] font-bold">
                                                <span>AI Assistant Auto-Reply</span>
                                            </div>
                                            <div class="chat-markdown text-slate-800 leading-relaxed">
                                                {!! \Illuminate\Support\Str::markdown($msg->message) !!}
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-mono ml-1">{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                    </div>
                                </div>

                            <!-- If Agent Reply (Right, Blue) -->
                            @else
                                <div class="flex flex-col items-end gap-1 max-w-[80%]">
                                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3 rounded-2xl rounded-tr-sm text-xs leading-relaxed shadow-sm">
                                        <div class="flex items-center gap-1.5 mb-1 pb-1 border-b border-white/20 text-blue-100 font-mono text-[10px]">
                                            <span class="font-bold">{{ $msg->sender_name ?: 'Live Agent' }}</span>
                                        </div>
                                        <p class="whitespace-pre-line">{{ $msg->message }}</p>
                                        @if($msg->attachment_url)
                                            <div class="mt-2 rounded-xl overflow-hidden border border-white/20">
                                                <a href="{{ $msg->attachment_url }}" target="_blank">
                                                    <img src="{{ $msg->attachment_url }}" alt="Attachment" class="max-h-52 w-auto object-cover rounded-lg">
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 font-mono text-[10px] text-slate-400 mr-1">
                                        <span>{{ $msg->created_at ? $msg->created_at->format('h:i A') : '' }}</span>
                                        <span class="material-symbols-outlined text-[13px] text-blue-500">done_all</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Real-time Polled Messages Container -->
                    <template x-for="pmsg in polledMessages" :key="pmsg.id">
                        <div class="w-full flex" :class="pmsg.sender_type === 'agent' ? 'justify-end' : 'justify-start'">
                            <div class="p-3 rounded-2xl text-xs" :class="pmsg.sender_type === 'agent' ? 'bg-blue-600 text-white' : 'bg-white border border-slate-200 text-slate-800'">
                                <div class="chat-markdown" x-html="renderChatMarkdown(pmsg.message)"></div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Quick Canned Replies Carousel -->
                <div class="px-4 py-2 bg-slate-50 border-t border-slate-200 flex items-center gap-2 overflow-x-auto no-scrollbar shrink-0">
                    <span class="text-[10px] font-mono uppercase text-slate-400 font-bold shrink-0">Quick Canned:</span>
                    <button type="button" @click="insertCanned('Hello! How can I assist you with your order today?')" class="text-xs px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-600 whitespace-nowrap transition-colors">
                        👋 Greeting
                    </button>
                    <button type="button" @click="insertCanned('Yes! It is in stock. You can order now and it will be shipped today. 🚚')" class="text-xs px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-600 whitespace-nowrap transition-colors">
                        📦 In Stock &amp; Shipping
                    </button>
                    <button type="button" @click="insertCanned('Checking our logistics system for your courier tracking reference now...')" class="text-xs px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-600 whitespace-nowrap transition-colors">
                        🔍 Checking Tracking
                    </button>
                    <button type="button" @click="insertCanned('I have marked your case as resolved. Thank you for choosing Bazaario!')" class="text-xs px-2.5 py-1 rounded-full bg-white border border-slate-200 hover:border-blue-400 hover:text-blue-600 text-slate-600 whitespace-nowrap transition-colors">
                        ✅ Case Resolution
                    </button>
                </div>

                <!-- Agent Reply Bar -->
                <div class="p-4 bg-white border-t border-slate-200 shrink-0">
                    <form action="{{ route('admin.support.reply', $activeConversation->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                        @csrf
                        
                        <!-- File Upload Clip -->
                        <label class="w-9 h-9 rounded-xl border border-slate-200 text-slate-500 hover:text-blue-600 hover:bg-slate-50 flex items-center justify-center cursor-pointer transition-colors" title="Attach product image or invoice">
                            <span class="material-symbols-outlined text-[20px]">attach_file</span>
                            <input type="file" name="attachment" accept="image/*" class="hidden" @change="previewAttachment($event)">
                        </label>

                        <!-- Input -->
                        <div class="flex-1 relative">
                            <input type="text" 
                                   name="message" 
                                   x-model="replyText" 
                                   placeholder="Type a reply to customer..." 
                                   class="w-full bg-slate-50 text-slate-800 text-xs px-4 py-3 rounded-xl border border-slate-200 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <!-- Send Button -->
                        <button type="submit" class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-heading font-semibold text-xs flex items-center gap-2 shadow-sm transition-colors cursor-pointer">
                            <span>Send</span>
                            <span class="material-symbols-outlined text-[16px]">send</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-slate-400 text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl text-slate-400">forum</span>
                    </div>
                    <h3 class="font-heading font-bold text-base text-slate-700">No Conversation Selected</h3>
                    <p class="text-xs max-w-sm mt-1">Select a customer conversation from the left queue to commence live agent chat and inspection.</p>
                </div>
            @endif
        </div>

        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <!-- COLUMN 3: CUSTOMER INTELLIGENCE & CONTEXT (3 cols)                  -->
        <!-- ═══════════════════════════════════════════════════════════════════ -->
        <div class="hidden lg:flex col-span-3 bg-white flex-col h-full overflow-y-auto p-5 space-y-6">
            @if($activeConversation)
                <!-- Customer Info Card -->
                <div>
                    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-3">Customer Info</h3>
                    <div class="flex flex-col items-center text-center p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div class="w-16 h-16 rounded-full bg-slate-200 text-slate-800 font-bold text-lg flex items-center justify-center ring-2 ring-blue-500/30 mb-2">
                            {{ strtoupper(substr($activeConversation->customer_display_name, 0, 2)) }}
                        </div>
                        <h4 class="font-heading font-bold text-sm text-slate-900">{{ $activeConversation->customer_display_name }}</h4>
                        <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $activeConversation->customer_email ?: 'No email' }}</p>
                        
                        <div class="w-full grid grid-cols-2 gap-2 mt-4 pt-3 border-t border-slate-200 text-left text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-mono">Location</span>
                                <span class="font-medium text-slate-700 truncate block">West Bengal, IN</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-mono">Account</span>
                                <span class="font-medium text-slate-700 block">{{ $activeConversation->user ? 'Verified User' : 'Guest Visitor' }}</span>
                            </div>
                        </div>

                        @if($activeConversation->user)
                            <a href="{{ route('admin.customers.show', $activeConversation->user->id) }}" class="mt-4 w-full py-2 bg-white hover:bg-slate-100 text-slate-700 border border-slate-200 font-heading text-xs font-semibold rounded-xl text-center transition-colors">
                                View Profile
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Conversation Details -->
                <div class="space-y-3">
                    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">Conversation Details</h3>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Status</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase {{ $activeConversation->status === 'open' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                {{ $activeConversation->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Source</span>
                            <span class="font-medium text-slate-800">Website Chat Widget</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Assigned To</span>
                            <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST">
                                @csrf
                                <select name="assigned_agent_id" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded px-1.5 py-0.5 text-slate-800">
                                    <option value="">Unassigned</option>
                                    @foreach($agents as $ag)
                                        <option value="{{ $ag->id }}" {{ $activeConversation->assigned_agent_id === $ag->id ? 'selected' : '' }}>
                                            {{ $ag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Started</span>
                            <span class="font-mono text-slate-600 text-[11px]">{{ $activeConversation->created_at ? $activeConversation->created_at->format('M d, h:i A') : 'Today' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Conversation Tags -->
                <div>
                    <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Tags</h3>
                    <div class="flex flex-wrap gap-1.5 mb-2">
                        @foreach($activeConversation->tags ?? ['Product', 'Website'] as $tag)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium border border-blue-100">
                                <span>{{ $tag }}</span>
                                <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="remove_tag" value="{{ $tag }}">
                                    <button type="submit" class="hover:text-rose-600 ml-0.5 text-[12px]">×</button>
                                </form>
                            </span>
                        @endforeach
                    </div>

                    <!-- Add Tag Form -->
                    <form action="{{ route('admin.support.status', $activeConversation->id) }}" method="POST" class="flex gap-1.5">
                        @csrf
                        <input type="text" name="tag" placeholder="+ Add Tag" class="flex-1 text-xs px-2.5 py-1.5 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:outline-none">
                        <button type="submit" class="px-2.5 py-1.5 bg-slate-200 text-slate-700 text-xs font-semibold rounded-lg hover:bg-slate-300">Add</button>
                    </form>
                </div>

                <!-- Active Online Agents -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-mono text-[11px] font-bold text-slate-400 uppercase tracking-wider">Online Agents</h3>
                        <span class="text-[10px] font-mono text-emerald-600 font-bold">{{ count($agents) }} Online</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($agents as $ag)
                            <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 font-bold text-[10px] flex items-center justify-center">
                                        {{ strtoupper(substr($ag->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $ag->name }}</span>
                                </div>
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            </div>
                        @endforeach
                    </div>
                </div>

            @else
                <div class="text-center py-12 text-slate-400 text-xs">
                    <p>No active customer selected.</p>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
    function adminSupportDesk(config) {
        return {
            replyText: '',
            polledMessages: [],
            lastId: 0,
            pollTimer: null,

            init() {
                this.scrollToBottom();
                if (config.conversationId && config.pollUrl) {
                    this.startPolling();
                }
            },

            insertCanned(text) {
                this.replyText = text;
            },

            renderChatMarkdown(text) {
                if (window.renderChatMarkdown) return window.renderChatMarkdown(text);
                return text;
            },

            previewAttachment(event) {
                const file = event.target.files[0];
                if (file) {
                    if (window.showToast) {
                        window.showToast('Selected file: ' + file.name, 'info');
                    }
                }
            },

            startPolling() {
                this.pollTimer = setInterval(async () => {
                    if (!config.conversationId) return;
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
                }, 4000);
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const thread = document.getElementById('admin-chat-thread');
                    if (thread) {
                        thread.scrollTop = thread.scrollHeight;
                    }
                });
            }
        };
    }
</script>
@endsection
