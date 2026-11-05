<div x-data="bazaarioSupportWidget()" 
     x-init="initWidget()"
     class="fixed bottom-20 lg:bottom-6 right-4 sm:right-6 z-[99998] font-sans antialiased text-slate-800 pointer-events-none"
     id="bazaario-support-chat">

    <!-- 1. CLOSED STATE (Floating Button + Tooltip) -->
    <div x-show="!isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-75 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         class="pointer-events-auto flex items-center gap-3">
        
        <!-- "Need help? Chat with us" Tooltip Bubble (Left of the button) -->
        <div x-show="showPrompt && !isOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-200"
             style="background: #ffffff; color: #1e293b; padding: 8px 14px; border-radius: 16px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.12); border: 1px solid #e2e8f0; font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 8px; white-space: nowrap; position: relative; user-select: none;">
            <span>Need help? <strong style="color: #2563eb;">Chat with us</strong></span>
            <button type="button" @click.stop="showPrompt = false" style="color: #94a3b8; margin-left: 2px; border: none; background: none; cursor: pointer; display: flex; align-items: center;" title="Dismiss">
                <span class="material-symbols-outlined text-[15px]">close</span>
            </button>
            <!-- Caret arrow pointing toward the chat button -->
            <div style="position: absolute; right: -5px; top: 50%; transform: translateY(-50%) rotate(45deg); width: 10px; height: 10px; background: #ffffff; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0;"></div>
        </div>

        <!-- Floating Chat Circle Button -->
        <button type="button"
                @click="openChat()"
                style="width: 60px; height: 60px; min-width: 60px; min-height: 60px; border-radius: 9999px; background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%); color: #ffffff; box-shadow: 0 10px 25px -5px rgba(37,99,235,0.5); display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; position: relative; transition: transform 0.2s;"
                class="hover:scale-105 active:scale-95 focus:outline-none">
            <span class="material-symbols-outlined" 
                  style="font-size: 30px; color: #ffffff; line-height: 1;">chat</span>

            <!-- Unread Badge Counter -->
            <span x-show="unreadCount > 0" 
                  x-text="unreadCount"
                  style="position: absolute; top: -2px; right: -2px; width: 22px; height: 22px; border-radius: 9999px; background: #ef4444; color: #ffffff; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff; box-shadow: 0 2px 4px rgba(0,0,0,0.15);"
                  class="animate-pulse">1</span>
        </button>
    </div>

    <!-- 2. OPEN STATE (Floating Live Chat Window - Pure Floating Card, Zero Screen Blur) -->
    <div x-show="isOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="pointer-events-auto bg-white rounded-2xl sm:rounded-3xl shadow-2xl flex flex-col border border-slate-200 overflow-hidden"
         style="width: 380px; max-width: calc(100vw - 2rem); height: 560px; max-height: calc(100vh - 6.5rem); box-shadow: 0 20px 40px -10px rgba(0,0,0,0.22), 0 10px 20px -5px rgba(0,0,0,0.1);">
        
        <!-- Chat Header -->
        <div style="background: linear-gradient(135deg, #2563eb 0%, #4338ca 100%); color: #ffffff; padding: 14px 16px;" 
             class="flex items-center justify-between shrink-0 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div style="width: 38px; height: 38px; border-radius: 9999px; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.3); overflow: hidden; display: flex; align-items: center; justify-content: center;">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80" 
                             alt="Support Avatar" 
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <!-- Online indicator dot -->
                    <span style="position: absolute; bottom: 0; right: 0; width: 10px; height: 10px; border-radius: 9999px; background: #34d399; border: 2px solid #2563eb;" class="animate-pulse"></span>
                </div>
                <div>
                    <h3 class="font-heading font-bold text-sm tracking-wide leading-tight text-white m-0">Support Team</h3>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span style="width: 6px; height: 6px; border-radius: 9999px; background: #6ee7b7; display: inline-block;"></span>
                        <span style="font-size: 11px; color: #dbeafe; font-weight: 500;">Online · Typically replies in 2m</span>
                    </div>
                </div>
            </div>

            <!-- Header Action Controls -->
            <div class="flex items-center gap-1">
                <button type="button" @click="minimizeChat()" style="width: 30px; height: 30px; border-radius: 9999px; background: rgba(255,255,255,0.12); color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" class="hover:bg-white/25 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">remove</span>
                </button>
                <button type="button" @click="closeChat()" style="width: 30px; height: 30px; border-radius: 9999px; background: rgba(255,255,255,0.12); color: #ffffff; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" class="hover:bg-white/25 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
        </div>

        <!-- Message Body Stream -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3.5 bg-slate-50/60 scroll-smooth" id="support-chat-stream" @click="handleStreamClick($event)">
            
            <!-- Welcome Bot Bubble (Only if thread is fresh with no messages) -->
            <div x-show="messages.length === 0" class="flex items-start gap-2.5 max-w-[85%]">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-600 border border-blue-200/80 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs shadow-2xs">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                </div>
                <div class="flex flex-col gap-1">
                    <div class="bg-white border border-slate-200/80 p-3.5 rounded-2xl rounded-tl-sm text-xs leading-relaxed text-slate-800 shadow-sm">
                        <p class="font-semibold text-slate-900 mb-1">👋 Hi there!</p>
                        <p>Welcome to Bazaario. How can we help you today?</p>
                    </div>
                    <span class="text-[10px] text-slate-400 font-mono ml-1">Now</span>
                </div>
            </div>

            <!-- Quick Reply Action Pills (shown on open) -->
            <div x-show="showQuickReplies" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="grid grid-cols-2 gap-2 my-2 pt-1">
                
                <button type="button" 
                        @click="sendQuickReply('product_info')"
                        class="p-2.5 bg-white hover:bg-blue-50/80 border border-slate-200 hover:border-blue-300 rounded-xl text-left text-xs text-slate-700 hover:text-blue-700 flex items-center gap-2 transition-all shadow-2xs group">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[15px]">info</span>
                    </span>
                    <span class="font-medium text-[11px] leading-tight">Product information</span>
                </button>

                <button type="button" 
                        @click="sendQuickReply('track_order')"
                        class="p-2.5 bg-white hover:bg-blue-50/80 border border-slate-200 hover:border-blue-300 rounded-xl text-left text-xs text-slate-700 hover:text-blue-700 flex items-center gap-2 transition-all shadow-2xs group">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                    </span>
                    <span class="font-medium text-[11px] leading-tight">Track my order</span>
                </button>

                <button type="button" 
                        @click="sendQuickReply('talk_agent')"
                        class="p-2.5 bg-white hover:bg-blue-50/80 border border-slate-200 hover:border-blue-300 rounded-xl text-left text-xs text-slate-700 hover:text-blue-700 flex items-center gap-2 transition-all shadow-2xs group">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[15px]">person</span>
                    </span>
                    <span class="font-medium text-[11px] leading-tight">Talk to an agent</span>
                </button>

                <button type="button" 
                        @click="sendQuickReply('other')"
                        class="p-2.5 bg-white hover:bg-blue-50/80 border border-slate-200 hover:border-blue-300 rounded-xl text-left text-xs text-slate-700 hover:text-blue-700 flex items-center gap-2 transition-all shadow-2xs group">
                    <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <span class="material-symbols-outlined text-[15px]">help_outline</span>
                    </span>
                    <span class="font-medium text-[11px] leading-tight">Other question</span>
                </button>
            </div>

            <!-- Dynamic Messages List -->
            <template x-for="(msg, index) in messages" :key="msg.id || index">
                <div class="w-full flex" :class="msg.sender_type === 'user' ? 'justify-end' : 'justify-start'">
                    
                    <!-- Customer Message Bubble (Right) -->
                    <template x-if="msg.sender_type === 'user'">
                        <div class="flex flex-col items-end gap-1 max-w-[85%]">
                            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-3 rounded-2xl rounded-tr-sm text-xs leading-relaxed shadow-sm">
                                <p x-text="msg.message" class="whitespace-pre-line"></p>
                                <!-- Optional Attachment -->
                                <template x-if="msg.attachment_url">
                                    <div class="mt-2 rounded-xl overflow-hidden border border-white/20">
                                        <img :src="msg.attachment_url" alt="Attachment" class="max-h-48 w-auto object-cover rounded-lg cursor-pointer" @click="previewImage(msg.attachment_url)">
                                    </div>
                                </template>
                            </div>
                            <div class="flex items-center gap-1 font-mono text-[10px] text-slate-400 mr-1">
                                <span x-text="msg.time"></span>
                                <span class="material-symbols-outlined text-[13px] text-blue-500">done_all</span>
                            </div>
                        </div>
                    </template>

                    <!-- Support / Agent / Bot Bubble (Left) -->
                    <template x-if="msg.sender_type !== 'user'">
                        <div class="flex items-start gap-2.5 max-w-[85%]">
                            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-600 border border-blue-200/80 flex items-center justify-center shrink-0 mt-0.5 font-bold text-xs overflow-hidden shadow-2xs">
                                <template x-if="msg.sender_type === 'agent'">
                                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=60&auto=format&fit=crop&q=80" class="w-full h-full object-cover">
                                </template>
                                <template x-if="msg.sender_type !== 'agent'">
                                    <span class="material-symbols-outlined text-[16px] text-blue-600">smart_toy</span>
                                </template>
                            </div>
                            <div class="flex flex-col gap-1 max-w-[calc(100%-2.25rem)]">
                                <div class="bg-white border border-slate-200/80 p-3 rounded-2xl rounded-tl-sm text-xs leading-relaxed text-slate-800 shadow-sm">
                                    <div class="flex items-center justify-between gap-2 mb-1.5 border-b border-slate-100 pb-1">
                                        <span class="font-bold text-[11px] text-slate-900 truncate" x-text="msg.sender_name || 'Support Team'"></span>
                                        <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 uppercase shrink-0 font-semibold tracking-wide" x-text="msg.sender_type === 'agent' ? 'Live Agent' : 'AI Assistant'"></span>
                                    </div>
                                    <div class="chat-markdown text-slate-800" x-html="formatMessage(msg.message)"></div>
                                    
                                    <!-- Image Attachment Preview inside support message -->
                                    <template x-if="msg.attachment_url">
                                        <div class="mt-2 rounded-xl overflow-hidden border border-slate-200">
                                            <img :src="msg.attachment_url" alt="Image" class="max-h-48 w-auto object-cover rounded-lg cursor-pointer" @click="previewImage(msg.attachment_url)">
                                        </div>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono ml-1" x-text="msg.time"></span>
                            </div>
                        </div>
                    </template>

                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="isTyping" class="flex items-center gap-2 text-slate-400 text-xs py-1">
                <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[14px] text-slate-500 animate-spin">autorenew</span>
                </div>
                <span class="text-[11px] text-slate-500 italic">Support is replying...</span>
            </div>
        </div>

        <!-- Selected File Preview Bar (Before Sending) -->
        <div x-show="selectedFile" class="px-4 py-2 bg-blue-50 border-t border-blue-100 flex items-center justify-between text-xs text-blue-900">
            <div class="flex items-center gap-2 truncate">
                <span class="material-symbols-outlined text-[18px] text-blue-600">image</span>
                <span x-text="selectedFileName" class="truncate font-mono text-[11px]"></span>
            </div>
            <button type="button" @click="clearSelectedFile()" class="text-blue-500 hover:text-blue-800">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>

        <!-- Chat Input Footer -->
        <div class="p-3 bg-white border-t border-slate-200 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <!-- Emoji toggle button -->
                <button type="button" 
                        @click="insertEmoji('👋')" 
                        title="Insert emoji"
                        class="w-8 h-8 rounded-full text-slate-400 hover:text-amber-500 hover:bg-slate-100 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[20px]">sentiment_satisfied</span>
                </button>

                <!-- File upload button -->
                <label class="w-8 h-8 rounded-full text-slate-400 hover:text-blue-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer" title="Attach file or image">
                    <span class="material-symbols-outlined text-[20px]">attach_file</span>
                    <input type="file" @change="handleFileSelect($event)" accept="image/*" class="hidden">
                </label>

                <!-- Input Text Field -->
                <input type="text" 
                       x-model="inputMessage" 
                       placeholder="Type your message..." 
                       class="flex-1 bg-slate-100 text-slate-800 text-xs px-3.5 py-2.5 rounded-full border border-transparent focus:border-blue-400 focus:bg-white focus:outline-none transition-all"
                       :disabled="isSending">

                <!-- Send Button -->
                <button type="submit" 
                        :disabled="(!inputMessage.trim() && !selectedFile) || isSending"
                        class="w-9 h-9 rounded-full bg-blue-600 hover:bg-blue-700 disabled:opacity-40 disabled:hover:bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 transition-all cursor-pointer">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    function bazaarioSupportWidget() {
        return {
            isOpen: false,
            showPrompt: true,
            showQuickReplies: true,
            unreadCount: 1,
            conversationId: null,
            visitorId: localStorage.getItem('bazaario_visitor_id') || '',
            messages: [],
            inputMessage: '',
            selectedFile: null,
            selectedFileName: '',
            isSending: false,
            isTyping: false,
            pollTimer: null,
            lastMessageId: 0,
            lightboxUrl: null,

            initWidget() {
                // Auto hide prompt after 10s if not interacted
                setTimeout(() => { this.showPrompt = false; }, 10000);

                // Fetch or initialize session
                this.initSession();
            },

            async initSession() {
                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch("{{ route('support.chat.init') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        },
                        body: JSON.stringify({ visitor_id: this.visitorId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.conversationId = data.conversation_id;
                        this.visitorId = data.visitor_id;
                        localStorage.setItem('bazaario_visitor_id', data.visitor_id);
                        if (data.messages && data.messages.length > 0) {
                            this.messages = data.messages;
                            this.lastMessageId = Math.max(...data.messages.map(m => m.id || 0));
                        }
                    }
                } catch (err) {
                    console.warn('Support Chat session init fallback:', err);
                }
            },

            openChat() {
                this.isOpen = true;
                this.unreadCount = 0;
                this.showPrompt = false;
                this.scrollToBottom();
                this.startPolling();
            },

            minimizeChat() {
                this.isOpen = false;
                this.stopPolling();
            },

            closeChat() {
                this.isOpen = false;
                this.stopPolling();
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        if (window.showToast) window.showToast('File size must be under 10MB', 'warning');
                        return;
                    }
                    this.selectedFile = file;
                    this.selectedFileName = file.name;
                }
            },

            clearSelectedFile() {
                this.selectedFile = null;
                this.selectedFileName = '';
            },

            insertEmoji(emoji) {
                this.inputMessage += emoji;
            },

            formatMessage(text) {
                if (!text) return '';

                // Clean awkward asterisks, quotation marks, and bullet formats
                let clean = String(text)
                    .replace(/^[\s]*[-*•]\s*[*_]["']?([^"'\n]+?)["']?[*_]?\s*$/gm, '- $1')
                    .replace(/^[\s]*[-*•]\s*["']([^"'\n]+?)["']\s*$/gm, '- $1')
                    .replace(/\*["']([^"']+)["']\*/g, '$1')
                    .replace(/["']\*([^*]+)\*["']/g, '$1');

                if (window.renderChatMarkdown) {
                    return window.renderChatMarkdown(clean);
                }
                if (window.marked && typeof window.marked.parse === 'function') {
                    try {
                        return window.marked.parse(clean);
                    } catch (e) {}
                }

                // Safe standalone regex parser
                let escaped = clean
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                escaped = escaped.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
                escaped = escaped.replace(/\*(.+?)\*/g, '<em>$1</em>');
                escaped = escaped.replace(/`([^`]+)`/g, '<code class="bg-slate-100 text-slate-800 px-1 py-0.5 rounded font-mono text-[11px]">$1</code>');
                escaped = escaped.replace(/^[\s]*[-*•]\s+(.+)$/gm, '<li class="ml-4">$1</li>');
                escaped = escaped.replace(/(<li[\s\S]*?<\/li>)/g, '<ul class="my-1.5 space-y-0.5">$1</ul>');
                escaped = escaped.replace(/\n/g, '<br>');
                return escaped;
            },

            handleStreamClick(event) {
                const li = event.target.closest('li');
                if (li && li.closest('.chat-markdown ul')) {
                    const text = li.textContent.replace(/^[→•\s]+/, '').trim();
                    if (text && !this.isSending) {
                        this.inputMessage = text;
                        this.sendMessage();
                    }
                }
            },

            previewImage(url) {
                if (url) window.open(url, '_blank');
            },

            async sendQuickReply(action) {
                this.showQuickReplies = false;
                this.isTyping = true;
                this.scrollToBottom();

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const res = await fetch("{{ route('support.chat.quick-reply') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || ''
                        },
                        body: JSON.stringify({
                            conversation_id: this.conversationId,
                            action: action
                        })
                    });
                    const data = await res.json();
                    this.isTyping = false;
                    if (data.success) {
                        this.messages.push({
                            id: Date.now(),
                            sender_type: 'user',
                            sender_name: 'You',
                            message: data.user_message.message,
                            time: data.user_message.time
                        });
                        this.messages.push({
                            id: Date.now() + 1,
                            sender_type: 'bot',
                            sender_name: data.support_message.sender_name,
                            message: data.support_message.message,
                            time: data.support_message.time
                        });
                        this.scrollToBottom();
                    }
                } catch (e) {
                    this.isTyping = false;
                }
            },

            async sendMessage() {
                const text = this.inputMessage.trim();
                const file = this.selectedFile;
                if (!text && !file) return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                this.isSending = true;

                const formData = new FormData();
                formData.append('conversation_id', this.conversationId);
                if (text) formData.append('message', text);
                if (file) formData.append('attachment', file);

                // Optimistic UI push
                const tempId = Date.now();
                this.messages.push({
                    id: tempId,
                    sender_type: 'user',
                    sender_name: 'You',
                    message: text || 'Sent an image attachment.',
                    attachment_url: file ? URL.createObjectURL(file) : null,
                    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                });

                this.inputMessage = '';
                this.clearSelectedFile();
                this.scrollToBottom();
                this.isTyping = true;

                try {
                    const res = await fetch("{{ route('support.chat.send') }}", {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token || '' },
                        body: formData
                    });
                    const data = await res.json();
                    this.isSending = false;
                    this.isTyping = false;

                    if (data.success && data.bot_reply) {
                        this.messages.push(data.bot_reply);
                        this.lastMessageId = data.bot_reply.id;
                        this.scrollToBottom();
                    }
                } catch (err) {
                    this.isSending = false;
                    this.isTyping = false;
                }
            },

            startPolling() {
                this.stopPolling();
                this.pollTimer = setInterval(async () => {
                    if (!this.isOpen || !this.conversationId) return;
                    try {
                        const url = `{{ route('support.chat.poll') }}?conversation_id=${this.conversationId}&after_id=${this.lastMessageId}`;
                        const res = await fetch(url);
                        const data = await res.json();
                        if (data.success && data.messages && data.messages.length > 0) {
                            data.messages.forEach(m => {
                                if (!this.messages.find(ex => ex.id === m.id)) {
                                    this.messages.push(m);
                                    this.lastMessageId = Math.max(this.lastMessageId, m.id);
                                }
                            });
                            this.scrollToBottom();
                        }
                    } catch (e) {}
                }, 3500);
            },

            stopPolling() {
                if (this.pollTimer) {
                    clearInterval(this.pollTimer);
                    this.pollTimer = null;
                }
            },

            scrollToBottom() {
                this.$nextTick(() => {
                    const stream = document.getElementById('support-chat-stream');
                    if (stream) {
                        stream.scrollTop = stream.scrollHeight;
                    }
                });
            }
        };
    }
</script>
