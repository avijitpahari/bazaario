@extends('layouts.agent')

@section('title', 'Live Chat Console — Bazaario Support Specialist')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800/80">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 uppercase flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>LIVE CONSOLE</span>
                </span>
                <span class="text-xs text-slate-400 font-mono tracking-wider">REAL-TIME VISITOR SESSIONS</span>
            </div>
            <h1 class="text-2xl font-display font-extrabold text-white tracking-tight mt-1.5 flex items-center gap-2.5">
                <span class="material-symbols-outlined text-emerald-400 text-3xl">forum</span>
                <span>Live Support Desk &amp; Chat Console</span>
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <span class="px-3 py-1.5 rounded-xl bg-slate-900/90 border border-slate-800 text-xs font-mono text-slate-400">
                Active Threads: <strong class="text-emerald-400 font-bold">{{ $conversations->total() }}</strong>
            </span>
        </div>
    </div>

    <!-- 2 Column Split Chat Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-[640px]">
        <!-- ── LEFT: CONVERSATIONS LIST (1 COL) ── -->
        <div class="rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl flex flex-col">
            <div class="p-4 border-b border-slate-800/80 bg-slate-900/40 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-amber-400 text-base">forum</span>
                    <h2 class="text-xs font-bold text-white uppercase tracking-wider font-mono">Visitor Inquiries</h2>
                </div>
                <span class="text-[10px] text-slate-400 font-mono bg-slate-800 px-2 py-0.5 rounded">{{ $conversations->total() }} total</span>
            </div>

            <div class="divide-y divide-slate-800/60 overflow-y-auto max-h-[600px] flex-1">
                @forelse($conversations as $conv)
                    @php
                        $isSelected = $activeConversation && $activeConversation->id === $conv->id;
                        $lastMsg = $conv->messages->first();
                        $displayName = $conv->guest_name ?: ($conv->user?->name ?: 'Visitor #' . $conv->id);
                        $initials = strtoupper(substr($displayName, 0, 2));
                    @endphp
                    <a href="{{ route('agent.chats.index', ['conversation_id' => $conv->id]) }}" 
                       class="p-4 block transition-all relative border-l-[3px] group {{ $isSelected ? 'bg-gradient-to-r from-amber-500/15 via-amber-500/5 to-transparent border-amber-400 shadow-inner' : 'border-transparent hover:bg-slate-800/40 hover:border-slate-700' }}">
                        <div class="flex items-start gap-3">
                            <!-- Avatar with online indicator ring -->
                            <div class="relative shrink-0 mt-0.5">
                                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/10 text-emerald-400 border border-emerald-500/30 font-bold flex items-center justify-center text-xs shadow-sm ring-1 ring-emerald-400/20">
                                    {{ $initials }}
                                </div>
                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full border-2 border-[#0C1220] {{ in_array($conv->status, ['open', 'pending']) ? 'bg-emerald-400 ring-1 ring-emerald-400/40 animate-pulse' : 'bg-slate-500' }}"></span>
                            </div>

                            <!-- Details -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-1 mb-1">
                                    <span class="font-bold text-xs text-white truncate block group-hover:text-amber-300 transition-colors">
                                        {{ $displayName }}
                                    </span>
                                    <span class="text-[10px] font-mono text-slate-400 shrink-0">
                                        {{ $conv->last_message_at ? $conv->last_message_at->diffForHumans() : $conv->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Clean message snippet (zero padding on clamp prevents any clipped text) -->
                                <p class="text-xs text-slate-300 line-clamp-2 leading-relaxed mb-2.5">
                                    {{ $lastMsg ? $lastMsg->message : 'Session initiated...' }}
                                </p>

                                <!-- Metadata footer: User type + Status pill -->
                                <div class="flex items-center justify-between text-[10px] font-mono pt-2 border-t border-slate-800/60">
                                    <span class="text-slate-400 flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-[14px] text-slate-500">{{ $conv->user ? 'person' : 'public' }}</span>
                                        <span>{{ $conv->user ? 'Registered User' : 'Web Guest' }}</span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md font-bold uppercase tracking-wider text-[9px] {{ in_array($conv->status, ['open', 'pending']) ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700' }}">
                                        {{ $conv->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="p-12 text-center text-slate-400 flex flex-col items-center justify-center gap-3">
                        <div class="w-14 h-14 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-500 flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl">forum</span>
                        </div>
                        <h4 class="font-display font-bold text-white text-sm">No Active Live Chats</h4>
                        <p class="text-xs text-slate-500 max-w-xs">
                            When website shoppers initiate a live chat widget conversation, they will appear here instantly.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- ── RIGHT: ACTIVE CHAT STREAM & COMPOSER (2 COLS) ── -->
        <div class="lg:col-span-2 rounded-3xl bg-[#0C1220]/90 backdrop-blur-xl border border-slate-800/80 overflow-hidden shadow-xl flex flex-col">
            @if($activeConversation)
                <!-- Active Chat Header -->
                <div class="p-4 border-b border-slate-800/80 bg-slate-900/60 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/10 text-emerald-400 border border-emerald-500/40 font-bold flex items-center justify-center text-xs shrink-0 ring-2 ring-emerald-400/20">
                            {{ strtoupper(substr($activeConversation->guest_name ?: ($activeConversation->user?->name ?: 'V'), 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-white truncate">
                                    {{ $activeConversation->guest_name ?: ($activeConversation->user?->name ?: 'Website Visitor') }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ in_array($activeConversation->status, ['open', 'pending']) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $activeConversation->status }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-400 truncate font-mono">
                                {{ $activeConversation->guest_email ?: ($activeConversation->user?->email ?: 'Anonymous Visitor') }} • Live Widget
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <!-- Mark Resolved / Status Update -->
                        <form action="{{ route('agent.chats.status', $activeConversation) }}" method="POST">
                            @csrf
                            @if(in_array($activeConversation->status, ['open', 'pending']))
                                <input type="hidden" name="status" value="resolved">
                                <button type="submit" 
                                        class="px-3.5 py-2 rounded-xl bg-emerald-500/15 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 font-bold text-xs transition-all flex items-center gap-1.5 border border-emerald-500/30 shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">task_alt</span>
                                    <span>Resolve Session</span>
                                </button>
                            @else
                                <input type="hidden" name="status" value="open">
                                <button type="submit" 
                                        class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors flex items-center gap-1.5 border border-slate-700 shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">replay</span>
                                    <span>Reopen</span>
                                </button>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Messages Stream -->
                <div id="agent-chat-stream" class="p-6 space-y-4 flex-1 overflow-y-auto max-h-[480px] bg-[#090D16]/60">
                    @foreach($activeConversation->messages as $msg)
                        @php
                            $isAgent = in_array($msg->sender_type, ['agent', 'bot']);
                        @endphp
                        <div class="flex flex-col {{ $isAgent ? 'items-end' : 'items-start' }} gap-1">
                            <span class="text-[10px] font-mono text-slate-500 px-1">
                                {{ $msg->sender_name ?: ($isAgent ? 'Support Specialist' : 'Visitor') }} • {{ $msg->created_at->format('h:i A') }}
                            </span>
                            <div class="max-w-lg p-3.5 rounded-2xl text-xs leading-relaxed whitespace-pre-line shadow-md
                                {{ $isAgent ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-medium rounded-tr-xs shadow-amber-500/15' : 'bg-[#0E1526] text-white rounded-tl-xs border border-slate-700/80 shadow-black/30' }}">
                                {{ $msg->message }}

                                @if($msg->attachment_path)
                                    <div class="mt-2.5 pt-2 border-t border-black/10">
                                        <a href="{{ $msg->attachment_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-bold underline">
                                            <span class="material-symbols-outlined text-sm">image</span>
                                            <span>View Image Attachment</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Agent Message Composer -->
                <div class="p-4 border-t border-slate-800/80 bg-[#0C1220]">
                    <form id="agent-chat-form" action="{{ route('agent.chats.reply', $activeConversation) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="flex items-center gap-2.5">
                            <input type="text" 
                                   id="agent_chat_input" 
                                   name="message" 
                                   required
                                   autocomplete="off"
                                   placeholder="Type reply to visitor in real-time..." 
                                   class="flex-1 px-4 py-3 bg-[#090D16] border border-slate-700/80 rounded-2xl text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500/30 transition-all">

                            <button type="submit" 
                                    class="px-6 py-3 rounded-2xl bg-gradient-to-r from-amber-500 via-amber-400 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-slate-950 font-black text-xs transition-all shadow-lg shadow-amber-500/25 flex items-center gap-1.5 shrink-0">
                                <span class="material-symbols-outlined text-[18px]">send</span>
                                <span>Send</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="p-16 text-center text-slate-400 flex flex-col items-center justify-center gap-3.5 my-auto">
                    <div class="w-16 h-16 rounded-3xl bg-slate-800/80 flex items-center justify-center text-slate-500 border border-slate-700 shadow-inner">
                        <span class="material-symbols-outlined text-4xl">forum</span>
                    </div>
                    <h3 class="text-base font-display font-bold text-white">No Conversation Selected</h3>
                    <p class="text-xs text-slate-400 max-w-sm">
                        Select an active visitor thread from the roster on the left to begin live assistance.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stream = document.getElementById('agent-chat-stream');
        if (stream) {
            stream.scrollTop = stream.scrollHeight;
        }

        @if($activeConversation)
            let lastId = {{ $activeConversation->messages->last()?->id ?? 0 }};
            const pollUrl = "{{ route('agent.chats.poll', $activeConversation) }}";

            setInterval(async () => {
                try {
                    const res = await fetch(`${pollUrl}?after_id=${lastId}`);
                    const data = await res.json();
                    if (data.success && data.messages && data.messages.length > 0) {
                        data.messages.forEach(m => {
                            lastId = Math.max(lastId, m.id);
                            const isAgent = m.sender_type === 'agent' || m.sender_type === 'bot';
                            const bubble = document.createElement('div');
                            bubble.className = `flex flex-col ${isAgent ? 'items-end' : 'items-start'} gap-1`;
                            bubble.innerHTML = `
                                <span class="text-[10px] font-mono text-slate-500 px-1">
                                    ${m.sender_name || (isAgent ? 'Support Specialist' : 'Visitor')} • ${m.time}
                                </span>
                                <div class="max-w-lg p-3.5 rounded-2xl text-xs leading-relaxed whitespace-pre-line shadow-md ${isAgent ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-slate-950 font-medium rounded-tr-xs shadow-amber-500/15' : 'bg-[#0E1526] text-white rounded-tl-xs border border-slate-700/80 shadow-black/30'}">
                                    ${m.message}
                                    ${m.attachment_url ? `<div class="mt-2.5 pt-2 border-t border-black/10"><a href="${m.attachment_url}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-bold underline"><span class="material-symbols-outlined text-sm">image</span><span>View Attachment</span></a></div>` : ''}
                                </div>
                            `;
                            stream.appendChild(bubble);
                        });
                        stream.scrollTop = stream.scrollHeight;
                    }
                } catch (e) {}
            }, 3500);
        @endif
    });
</script>
@endpush
