@extends('layouts.user')

@section('title', "Ticket #{$ticket->ticket_number} — Bazaario Support")

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 flex flex-col gap-6" x-data="{ escalateModalOpen: false }">

    <!-- Header Breadcrumb & Actions Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('user.support.index') }}" class="hover:text-slate-900 transition-colors">Help &amp; Support</a>
                <span>/</span>
                <span class="font-mono text-slate-700 font-bold">{{ $ticket->ticket_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <h1 class="text-xl sm:text-2xl font-black font-['Outfit'] text-slate-900 tracking-tight">
                    {{ $ticket->subject }}
                </h1>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase border {{ $ticket->status_color }}">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase border {{ $ticket->priority_color }}">
                    {{ $ticket->priority }} Priority
                </span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            @if($ticket->status !== 'resolved' && $ticket->status !== 'closed')
                <!-- Mark Resolved Button -->
                <form action="{{ route('user.support.tickets.close', $ticket) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition-colors shadow-xs flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">check_circle</span>
                        <span>Mark Resolved</span>
                    </button>
                </form>

                <!-- Escalate Button (if not already escalated) -->
                @if(!$ticket->is_escalated)
                    <button type="button" @click="escalateModalOpen = true"
                            class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 transition-colors flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">gavel</span>
                        <span>Escalate to Dispute</span>
                    </button>
                @endif
            @endif

            <a href="{{ route('user.support.index') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                Back to Hub
            </a>
        </div>
    </div>

    <!-- Linked Order Context Banner (if available) -->
    @if($ticket->order)
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-amber-500/20 text-amber-800 material-symbols-outlined text-2xl shrink-0">inventory_2</span>
                <div>
                    <div class="font-bold text-slate-900 text-sm">
                        Linked Order: #{{ $ticket->order->order_number }}
                    </div>
                    <div class="text-slate-600 mt-0.5">
                        Placed on {{ $ticket->order->placed_at ? $ticket->order->placed_at->format('d M Y, h:i A') : $ticket->order->created_at->format('d M Y') }} • Total: <strong class="font-mono">₹{{ number_format($ticket->order->total_amount, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($ticket->seller)
                    <div class="text-right hidden sm:block">
                        <span class="text-[10px] text-slate-500 uppercase font-mono block">Assigned Store</span>
                        <span class="font-bold text-slate-900">{{ $ticket->seller->sellerProfile->shop_name ?? $ticket->seller->name }}</span>
                    </div>
                @endif
                <a href="{{ route('user.orders.show', $ticket->order->id) }}" 
                   class="px-3 py-1.5 rounded-lg bg-white border border-amber-300 text-amber-900 font-semibold hover:bg-amber-100/50 transition-colors">
                    View Full Order
                </a>
            </div>
        </div>
    @endif

    <!-- Escalation Notice Banner -->
    @if($ticket->is_escalated)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 flex items-start gap-3">
            <span class="material-symbols-outlined text-rose-600 text-2xl mt-0.5 shrink-0">warning</span>
            <div>
                <h4 class="font-bold text-rose-900 text-sm">Case Under Formal Dispute Arbitration</h4>
                <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                    This ticket has been escalated for dispute arbitration. Reason: <em>"{{ $ticket->escalation_reason }}"</em>. Seller payout funds for this order remain safeguarded in escrow pending resolution.
                </p>
            </div>
        </div>
    @endif

    <!-- Conversation Stream Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm flex flex-col overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 flex items-center justify-between text-xs text-slate-500 bg-slate-50/60">
            <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] font-mono">Discussion Timeline</span>
            <span>Category: <strong class="text-slate-900">{{ $ticket->category_label }}</strong></span>
        </div>

        <!-- Messages Thread -->
        <div class="p-5 sm:p-6 flex flex-col gap-5 divide-y divide-slate-100">
            @foreach($ticket->messages as $msg)
                <div class="pt-4 first:pt-0">
                    @if($msg->sender_type === 'system')
                        <!-- System Alert Message -->
                        <div class="p-3.5 rounded-xl bg-slate-100/80 border border-slate-200 text-xs text-slate-700 flex items-center gap-2.5 font-medium">
                            <span class="material-symbols-outlined text-slate-500 text-lg">info</span>
                            <span>{{ $msg->message }}</span>
                            <span class="ml-auto text-[10px] text-slate-400 font-mono">{{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                    @else
                        <!-- Chat Bubble Message -->
                        <div class="flex items-start gap-3.5 {{ $msg->sender_type === 'customer' ? 'flex-row-reverse text-right' : '' }}">
                            
                            <!-- Avatar -->
                            <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center font-bold text-xs {{ $msg->sender_type === 'customer' ? 'bg-slate-900 text-amber-400' : 'bg-amber-500 text-slate-950 shadow-xs' }}">
                                @if($msg->sender_type === 'customer')
                                    <span>{{ strtoupper(substr($msg->user?->name ?? 'You', 0, 1)) }}</span>
                                @else
                                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-1 max-w-xl {{ $msg->sender_type === 'customer' ? 'items-end' : 'items-start' }}">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-bold text-slate-900">
                                        {{ $msg->sender_type === 'customer' ? 'You' : ($ticket->seller?->sellerProfile?->shop_name ?? 'Store Merchant') }}
                                    </span>
                                    <span class="text-slate-400 font-mono text-[10px]">
                                        {{ $msg->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>

                                <div class="p-4 rounded-2xl text-xs sm:text-sm leading-relaxed {{ $msg->sender_type === 'customer' ? 'bg-slate-900 text-white rounded-tr-none' : 'bg-slate-100 text-slate-800 rounded-tl-none border border-slate-200/60' }}">
                                    <p class="whitespace-pre-line text-left">{{ $msg->message }}</p>

                                    <!-- Attachment Preview -->
                                    @if($msg->attachment_path)
                                        <div class="mt-3 pt-2.5 border-t {{ $msg->sender_type === 'customer' ? 'border-slate-800' : 'border-slate-200' }}">
                                            @if($msg->isImageAttachment())
                                                <a href="{{ $msg->attachment_url }}" target="_blank" class="block group relative overflow-hidden rounded-xl border border-slate-700/30 max-w-xs">
                                                    <img src="{{ $msg->attachment_url }}" alt="Attachment" class="w-full max-h-48 object-cover group-hover:scale-105 transition-transform duration-200">
                                                    <span class="absolute bottom-1 right-1 px-2 py-0.5 rounded bg-black/70 text-white text-[10px] font-mono">Click to enlarge</span>
                                                </a>
                                            @else
                                                <a href="{{ $msg->attachment_url }}" target="_blank" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $msg->sender_type === 'customer' ? 'bg-slate-800 text-amber-300' : 'bg-white text-slate-800 border border-slate-200' }} text-xs font-semibold hover:underline">
                                                    <span class="material-symbols-outlined text-[16px]">attach_file</span>
                                                    <span>Download Document ({{ strtoupper($msg->attachment_type ?? 'PDF') }})</span>
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Reply Form Box -->
        @if($ticket->status !== 'closed')
            <div class="p-5 sm:p-6 bg-slate-50/80 border-t border-slate-200">
                <form action="{{ route('user.support.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                    @csrf
                    <label for="reply_message" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Post a Reply
                    </label>
                    <textarea name="message" id="reply_message" rows="3" required placeholder="Type your response here..."
                              class="w-full p-3.5 rounded-2xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-white placeholder:text-slate-400"></textarea>
                    @error('message')
                        <p class="text-rose-600 text-xs">{{ $message }}</p>
                    @enderror

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                        <div class="flex items-center gap-2">
                            <label for="reply_attachment" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-semibold cursor-pointer transition-colors">
                                <span class="material-symbols-outlined text-[16px]">attach_file</span>
                                <span>Attach File / Photo</span>
                            </label>
                            <input type="file" name="attachment" id="reply_attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" onchange="document.getElementById('file-chosen-name').textContent = this.files[0] ? this.files[0].name : '';">
                            <span id="file-chosen-name" class="text-xs text-slate-500 font-mono truncate max-w-[200px]"></span>
                        </div>

                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-amber-400">send</span>
                            <span>Send Reply</span>
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="p-6 bg-slate-50 border-t border-slate-200 text-center text-xs text-slate-500">
                This ticket has been marked as closed. If you have further issues, you can open a new ticket from the support hub.
            </div>
        @endif

    </div>

    <!-- Escalate Modal -->
    <div x-cloak x-show="escalateModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-lg w-full shadow-2xl border border-slate-200"
             @click.outside="escalateModalOpen = false">
            <div class="flex items-center gap-3 text-rose-600 mb-3">
                <span class="p-2.5 rounded-2xl bg-rose-100 material-symbols-outlined text-2xl">gavel</span>
                <h3 class="text-lg font-bold font-['Outfit'] text-slate-900">Escalate to Senior Arbitration</h3>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                If the seller is unresponsive, rejects your return unfairly, or fails to resolve your issue, you can escalate this case for formal dispute arbitration. Escrow funds will remain safely held until an official ruling is issued.
            </p>

            <form action="{{ route('user.support.tickets.escalate', $ticket) }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label for="escalation_reason" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        State Your Reason for Escalation <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="escalation_reason" id="escalation_reason" rows="4" required placeholder="Please explain why you are requesting formal intervention (e.g. seller refused return for broken item, no reply for 48 hours)..."
                              class="w-full p-3 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 transition-all"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="escalateModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md transition-colors">
                        Confirm Escalation
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
