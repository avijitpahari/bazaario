<!-- Live Chat Window Header -->
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
    <template x-for="pmsg in chatPolledMessages" :key="pmsg.id">
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
        📦 In Stock & Shipping
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