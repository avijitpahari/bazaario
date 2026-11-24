@extends('layouts.user')

@section('title', 'Help & Customer Support — Bazaario')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col gap-8">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-amber-950 text-white p-8 sm:p-10 shadow-lg border border-slate-700/60">
        <div class="relative z-10 max-w-2xl flex flex-col gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-xs font-mono font-bold tracking-wider uppercase border border-amber-400/30 w-fit">
                <span class="material-symbols-outlined text-[16px]">support_agent</span>
                <span>Customer Care &amp; Resolution</span>
            </span>
            <h1 class="text-2xl sm:text-4xl font-black font-['Outfit'] tracking-tight text-white mt-1">
                How can we assist you today?
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed mt-1">
                Track active orders, get help from verified merchants, manage product return inspections, and resolve inquiries with complete escrow buyer protection.
            </p>
            <div class="flex flex-wrap items-center gap-3 mt-4">
                <a href="{{ route('user.support.tickets.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition-all shadow-md">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>Open Support Ticket</span>
                </a>
                <button type="button" 
                        onclick="if(window.bazaarioOpenChat) { window.bazaarioOpenChat(); } else { const btn = document.querySelector('#bazaario-support-chat button'); if(btn) btn.click(); }"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm border border-white/20 transition-all">
                    <span class="material-symbols-outlined text-[18px]">chat</span>
                    <span>Start Live Chat</span>
                </button>
            </div>
        </div>

        <!-- Decorative background emblem -->
        <div class="absolute -right-8 -bottom-10 opacity-10 pointer-events-none text-amber-400">
            <span class="material-symbols-outlined text-[240px]">shield_with_heart</span>
        </div>
    </div>

    <!-- Quick Assistance Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Orders -->
        <a href="{{ route('user.orders.index') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Track Orders &amp; Delivery</h3>
            <p class="text-xs text-slate-500 mt-1">Check real-time dispatch progress, live courier GPS status, and delivery slots.</p>
        </a>

        <!-- 2. Returns & Refunds -->
        <a href="{{ route('user.returns') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">keyboard_return</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Returns &amp; Refunds</h3>
            <p class="text-xs text-slate-500 mt-1">Track return package pickups, merchant quality checks, and escrow refunds.</p>
        </a>

        <!-- 3. Escrow Buyer Protection -->
        <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-2xl">verified_user</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Escrow Protection</h3>
            <p class="text-xs text-slate-500 mt-1">Funds stay safe in escrow until you inspect and approve your delivered goods.</p>
        </div>

        <!-- 4. Auction Inquiries -->
        <a href="{{ route('user.auctions') }}" class="group p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-indigo-400 hover:shadow-md transition-all">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-2xl">gavel</span>
            </div>
            <h3 class="font-bold text-slate-900 text-sm font-['Outfit']">Auction Settlements</h3>
            <p class="text-xs text-slate-500 mt-1">Inquire about winning bids, reserve prices, or hammer settlement checkouts.</p>
        </a>
    </div>

    <!-- ── 147: QUICK FAQ AUTO-REPLY INTERACTIVE MODULE ── -->
    <div x-data="{
            faqs: {{ Js::from($faqs) }},
            searchQuery: '',
            selectedCategory: 'all',
            expandedFaq: null,
            helpfulFeedback: {},
            
            get filteredFaqs() {
                const q = this.searchQuery.toLowerCase().trim();
                return this.faqs.filter(faq => {
                    const matchCategory = this.selectedCategory === 'all' || faq.category === this.selectedCategory;
                    if (!matchCategory) return false;
                    if (!q) return true;
                    return faq.question.toLowerCase().includes(q) ||
                           faq.summary.toLowerCase().includes(q) ||
                           faq.answer.toLowerCase().includes(q) ||
                           (faq.tags && faq.tags.some(t => t.toLowerCase().includes(q)));
                });
            },
            
            toggleFaq(id) {
                this.expandedFaq = (this.expandedFaq === id) ? null : id;
            },
            
            triggerChatFaq(action) {
                if (window.bazaarioTriggerFaq) {
                    window.bazaarioTriggerFaq(action);
                } else if (window.bazaarioOpenChat) {
                    window.bazaarioOpenChat();
                }
            },
            
            rateHelpful(id, isHelpful) {
                this.helpfulFeedback[id] = isHelpful;
            },
            
            formatFaqText(text) {
                if (!text) return '';
                let formatted = text
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\*\*(.+?)\*\*/g, '<strong class=\'font-bold text-slate-900\'>$1</strong>')
                    .replace(/•\s+(.+)$/gm, '<li class=\'flex items-start gap-2 text-slate-700 my-1\'><span class=\'w-1.5 h-1.5 rounded-full bg-amber-500 mt-1.5 shrink-0\'></span><span>$1</span></li>')
                    .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href=\'$2\' class=\'font-bold text-blue-600 hover:text-blue-800 underline underline-offset-2 inline-flex items-center gap-0.5\'>$1 &rarr;</a>')
                    .replace(/\n\n/g, '<div class=\'h-2\'></div>')
                    .replace(/\n/g, '<br>');
                return formatted;
            }
         }"
         id="quick-faq-section"
         class="p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm flex flex-col gap-6">

        <!-- Module Header -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[10px] font-mono font-bold uppercase tracking-wider border border-blue-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>QUICK FAQ AUTO-REPLY</span>
                    </span>
                    <span class="text-xs font-mono text-slate-400">INSTANT AUTO-ANSWERS</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black font-['Outfit'] text-slate-900 tracking-tight">
                    Instant Auto-Reply Solutions &amp; FAQs
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Get immediate, verified answers to common inquiries without waiting in the support agent queue.
                </p>
            </div>

            <!-- Quick Live Chat Trigger -->
            <button type="button" 
                    @click="triggerChatFaq('other')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-xs transition-all shadow-md shadow-blue-500/20 shrink-0 self-start lg:self-auto cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>Ask Support Bot in Real-Time</span>
            </button>
        </div>

        <!-- Search Bar & Category Filter Pills -->
        <div class="flex flex-col gap-3">
            <!-- Search Input -->
            <div class="relative w-full">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Search instant FAQ auto-replies (e.g. returns, escrow, delivery delay, wholesale bidding, refunds)..." 
                       class="w-full pl-10 pr-10 py-3 rounded-2xl bg-slate-50 border border-slate-200/90 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all">
                <button type="button" 
                        x-show="searchQuery.length > 0" 
                        @click="searchQuery = ''" 
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs font-bold bg-slate-200/60 w-5 h-5 rounded-full flex items-center justify-center">
                    ✕
                </button>
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <button type="button" 
                        @click="selectedCategory = 'all'" 
                        :class="selectedCategory === 'all' ? 'bg-slate-900 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:text-slate-900 hover:bg-slate-200/70'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap cursor-pointer">
                    All FAQs (<span x-text="faqs.length"></span>)
                </button>
                <button type="button" 
                        @click="selectedCategory = 'orders'" 
                        :class="selectedCategory === 'orders' ? 'bg-emerald-700 text-white font-bold shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">local_shipping</span>
                    <span>Orders &amp; Tracking</span>
                </button>
                <button type="button" 
                        @click="selectedCategory = 'returns'" 
                        :class="selectedCategory === 'returns' ? 'bg-rose-700 text-white font-bold shadow-xs' : 'bg-rose-50 text-rose-800 hover:bg-rose-100'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">keyboard_return</span>
                    <span>Returns &amp; Refunds</span>
                </button>
                <button type="button" 
                        @click="selectedCategory = 'payments'" 
                        :class="selectedCategory === 'payments' ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-amber-50 text-amber-800 hover:bg-amber-100'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">verified_user</span>
                    <span>Escrow &amp; Payments</span>
                </button>
                <button type="button" 
                        @click="selectedCategory = 'auctions'" 
                        :class="selectedCategory === 'auctions' ? 'bg-indigo-700 text-white font-bold shadow-xs' : 'bg-indigo-50 text-indigo-800 hover:bg-indigo-100'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">gavel</span>
                    <span>Wholesale Auctions</span>
                </button>
                <button type="button" 
                        @click="selectedCategory = 'support'" 
                        :class="selectedCategory === 'support' ? 'bg-blue-700 text-white font-bold shadow-xs' : 'bg-blue-50 text-blue-800 hover:bg-blue-100'"
                        class="px-3.5 py-1.5 rounded-xl transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">confirmation_number</span>
                    <span>Tickets &amp; Disputes</span>
                </button>
            </div>
        </div>

        <!-- FAQ Auto-Reply Accordion List -->
        <div class="space-y-3">
            <template x-for="faq in filteredFaqs" :key="faq.id">
                <div class="border rounded-2xl overflow-hidden transition-all duration-200"
                     :class="expandedFaq === faq.id ? 'border-amber-400/90 shadow-md bg-amber-500/[0.02]' : 'border-slate-200/90 hover:border-slate-300 bg-white'">
                    
                    <!-- Question Trigger Row -->
                    <button type="button" 
                            @click="toggleFaq(faq.id)" 
                            class="w-full p-4 sm:p-5 text-left flex items-start sm:items-center justify-between gap-3 cursor-pointer group">
                        
                        <div class="flex items-start sm:items-center gap-3.5 flex-1 min-w-0">
                            <!-- Category Icon -->
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 mt-0.5 sm:mt-0 transition-transform group-hover:scale-105"
                                 :class="{
                                     'bg-emerald-100 text-emerald-700': faq.category === 'orders',
                                     'bg-rose-100 text-rose-700': faq.category === 'returns',
                                     'bg-amber-100 text-amber-800': faq.category === 'payments',
                                     'bg-indigo-100 text-indigo-700': faq.category === 'auctions',
                                     'bg-blue-100 text-blue-700': faq.category === 'support'
                                 }">
                                <span class="material-symbols-outlined text-[20px]" x-text="faq.icon"></span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="px-2 py-0.2 rounded-md text-[9px] font-mono font-bold uppercase tracking-wider"
                                          :class="{
                                              'bg-emerald-50 text-emerald-700 border border-emerald-200': faq.category === 'orders',
                                              'bg-rose-50 text-rose-700 border border-rose-200': faq.category === 'returns',
                                              'bg-amber-50 text-amber-800 border border-amber-200': faq.category === 'payments',
                                              'bg-indigo-50 text-indigo-700 border border-indigo-200': faq.category === 'auctions',
                                              'bg-blue-50 text-blue-700 border border-blue-200': faq.category === 'support'
                                          }"
                                          x-text="faq.category_label"></span>
                                    <span class="px-2 py-0.2 rounded-md bg-slate-100 text-slate-600 text-[9px] font-mono font-semibold">
                                        ⚡ Auto-Reply Ready
                                    </span>
                                </div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 font-['Outfit'] group-hover:text-amber-800 transition-colors leading-snug"
                                    x-text="faq.question"></h3>
                                <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1" x-text="faq.summary"></p>
                            </div>
                        </div>

                        <!-- Chevron indicator -->
                        <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:bg-slate-200"
                             :class="expandedFaq === faq.id ? 'rotate-180 bg-amber-500 text-slate-950 font-bold' : ''">
                            <span class="material-symbols-outlined text-sm">keyboard_arrow_down</span>
                        </div>
                    </button>

                    <!-- Expandable Auto-Reply Content Body -->
                    <div x-show="expandedFaq === faq.id" 
                         x-collapse 
                         class="px-4 pb-5 sm:px-5 sm:pb-6 pt-1 border-t border-slate-100">
                        
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs sm:text-sm leading-relaxed text-slate-800 shadow-2xs">
                            <div class="flex items-center gap-2 mb-2 pb-2 border-b border-slate-200/60 text-[11px] font-mono text-slate-500">
                                <span class="material-symbols-outlined text-sm text-emerald-600">verified</span>
                                <span>Official Bazaario Customer Care Auto-Reply</span>
                            </div>

                            <div class="chat-faq-body text-slate-800" x-html="formatFaqText(faq.answer)"></div>

                            <!-- Action Triggers -->
                            <div class="mt-4 pt-3 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <!-- 1. Trigger Live Chat with this FAQ -->
                                    <button type="button" 
                                            @click="triggerChatFaq(faq.chat_action)"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined text-[15px]">chat</span>
                                        <span>Ask in Live Chat</span>
                                    </button>

                                    <!-- 2. Direct Portal Link -->
                                    <a :href="faq.action_url" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 transition-colors shadow-2xs">
                                        <span x-text="faq.action_label"></span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </div>

                                <!-- Feedback Widget -->
                                <div class="flex items-center gap-2 text-xs text-slate-500 font-mono">
                                    <span>Helpful?</span>
                                    <template x-if="helpfulFeedback[faq.id] === undefined">
                                        <div class="flex items-center gap-1">
                                            <button type="button" @click="rateHelpful(faq.id, true)" class="p-1 hover:text-emerald-600 transition-colors cursor-pointer" title="Yes, helpful">
                                                👍
                                            </button>
                                            <button type="button" @click="rateHelpful(faq.id, false)" class="p-1 hover:text-rose-600 transition-colors cursor-pointer" title="No, need more help">
                                                👎
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="helpfulFeedback[faq.id] === true">
                                        <span class="text-emerald-600 font-bold flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">check</span>
                                            <span>Thanks for your feedback!</span>
                                        </span>
                                    </template>
                                    <template x-if="helpfulFeedback[faq.id] === false">
                                        <a href="{{ route('user.support.tickets.create') }}" class="text-blue-600 font-bold underline">
                                            Open Ticket &rarr;
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </template>

            <!-- Search No-Match Empty State -->
            <div x-show="filteredFaqs.length === 0" 
                 class="p-8 text-center flex flex-col items-center justify-center gap-2 rounded-2xl bg-slate-50 border border-dashed border-slate-300">
                <span class="material-symbols-outlined text-3xl text-slate-400">search_off</span>
                <p class="text-xs sm:text-sm font-bold text-slate-700">No matching Quick FAQ auto-replies found for "<span x-text="searchQuery"></span>"</p>
                <p class="text-xs text-slate-500">Need specific help? Contact our live support specialists directly:</p>
                <div class="flex items-center gap-2 mt-2">
                    <button type="button" 
                            @click="triggerChatFaq('other')" 
                            class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors cursor-pointer">
                        Ask in Live Chat
                    </button>
                    <a href="{{ route('user.support.tickets.create') }}" 
                       class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-colors">
                        Create Support Ticket
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tickets & Inquiries Section -->
    <div id="tickets-section" class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold font-['Outfit'] text-slate-900">Your Support Tickets &amp; Inquiries</h2>
                <p class="text-xs text-slate-500">Track correspondence with vendors and customer care specialists.</p>
            </div>

            <!-- Filter tabs -->
            <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/80 shadow-xs self-start">
                <a href="{{ route('user.support.index') }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ is_null($statusFilter) ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    All ({{ $totalTickets }})
                </a>
                <a href="{{ route('user.support.index', ['status' => 'open']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'open' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Active ({{ $openTickets }})
                </a>
                <a href="{{ route('user.support.index', ['status' => 'resolved']) }}" 
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition-all {{ $statusFilter === 'resolved' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Resolved ({{ $resolvedCount ?? $resolvedTickets }})
                </a>
            </div>
        </div>

        @if($tickets->isNotEmpty())
            <div class="flex flex-col gap-3">
                @foreach($tickets as $ticket)
                    <div class="p-5 bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:border-amber-400/80 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-xl">confirmation_number</span>
                            </div>
                            <div class="flex flex-col gap-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-mono text-xs font-bold text-slate-900">{{ $ticket->ticket_number }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $ticket->status_color }}">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                                        {{ $ticket->priority }} Priority
                                    </span>
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 hover:text-amber-700 transition-colors">
                                    <a href="{{ route('user.support.tickets.show', $ticket) }}">{{ $ticket->subject }}</a>
                                </h3>
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>Category: <strong>{{ $ticket->category_label }}</strong></span>
                                    @if($ticket->order)
                                        <span>•</span>
                                        <span>Order: <strong class="font-mono">{{ $ticket->order->order_number }}</strong></span>
                                    @endif
                                    @if($ticket->seller)
                                        <span>•</span>
                                        <span>Merchant: <strong>{{ $ticket->seller?->sellerProfile?->shop_name ?? $ticket->seller?->name ?? 'Merchant' }}</strong></span>
                                    @endif
                                    <span>•</span>
                                    <span>Updated {{ $ticket->updated_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 shrink-0 self-end md:self-center">
                            @if($ticket->is_escalated)
                                <span class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">gavel</span>
                                    <span>Dispute Escalated</span>
                                </span>
                            @endif
                            <a href="{{ route('user.support.tickets.show', $ticket) }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs transition-colors shadow-xs">
                                <span>View Thread</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        </div>
                    </div>
                @endforeach

                <div class="mt-2">
                    {{ $tickets->links() }}
                </div>
            </div>
        @else
            <div class="p-8 bg-white rounded-2xl border border-slate-200/90 text-center flex flex-col items-center justify-center gap-3">
                <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-3xl">inbox</span>
                </div>
                <h3 class="text-base font-bold font-['Outfit'] text-slate-900">No support tickets found</h3>
                <p class="text-xs text-slate-500 max-w-md">
                    You currently have no {{ $statusFilter ? $statusFilter : '' }} tickets. Need help with an order or product?
                </p>
                <a href="{{ route('user.support.tickets.create') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-all shadow-xs mt-1">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Create a Ticket</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Returns & Inspections Overview -->
    @if(isset($returns) && $returns->isNotEmpty())
        <div class="p-6 bg-white rounded-3xl border border-slate-200/90 shadow-2xs flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold font-['Outfit'] text-slate-900">Active Return &amp; Refund Requests</h2>
                    <p class="text-xs text-slate-500">Track seller inspection status and escrow refunds.</p>
                </div>
                <a href="{{ route('user.returns') }}" class="text-xs font-bold text-amber-600 hover:underline">
                    View All Returns
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($returns as $ret)
                    <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/60 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="p-2 rounded-xl bg-rose-50 text-rose-600 material-symbols-outlined text-xl">assignment_return</span>
                            <div>
                                <div class="text-xs font-bold text-slate-900">Return #RET-{{ $ret->id }}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">Reason: {{ $ret->reason }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $ret->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $ret->status }}
                            </span>
                            <div class="text-xs font-bold text-slate-900 mt-1 font-mono">₹{{ number_format($ret->refund_amount ?? 0, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
