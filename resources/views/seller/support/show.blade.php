@extends('layouts.seller')

@section('title', "Customer Inquiry #{$ticket->ticket_number} — Bazaario Seller Center")

@section('content')
<div class="flex flex-col w-full gap-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-3 border-b border-[#E2DFD7]/80">
        <div>
            <div class="flex items-center gap-2 text-xs text-on-surface-variant mb-1 font-mono">
                <a href="{{ route('seller.support.index') }}" class="hover:text-on-surface transition-colors">Inquiries &amp; Support</a>
                <span>/</span>
                <span class="font-bold text-on-surface">{{ $ticket->ticket_number }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-2 mt-1">
                <h1 class="text-xl sm:text-2xl font-bold font-heading text-on-surface tracking-tight">
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

        <!-- Status Action Dropdown & Navigation -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Update Status Form -->
            <form action="{{ route('seller.support.tickets.status', $ticket) }}" method="POST" class="flex items-center gap-2">
                @csrf
                <select name="status" onchange="this.form.submit()" 
                        class="px-3 py-2 rounded-xl bg-surface-container border border-outline-variant/60 text-xs font-semibold text-on-surface focus:outline-none focus:ring-2 focus:ring-amber-500/20 cursor-pointer">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Status: Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>Status: In Progress</option>
                    <option value="waiting_customer" {{ $ticket->status === 'waiting_customer' ? 'selected' : '' }}>Status: Waiting on Customer</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Status: Resolved</option>
                </select>
            </form>

            <a href="{{ route('seller.support.index') }}" class="px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface font-semibold text-xs transition-colors">
                Back to Inquiries
            </a>
        </div>
    </div>

    <!-- Order Context & Customer Profile Banner -->
    <div class="p-5 bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs">
        <div class="flex items-start sm:items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">person</span>
            </div>
            <div>
                <div class="font-bold text-on-surface text-sm">Customer: {{ $ticket->user->name ?? 'Buyer' }}</div>
                <div class="text-on-surface-variant mt-0.5">
                    Email: {{ $ticket->user->email ?? 'N/A' }}
                    @if($ticket->order)
                        • Order: <strong class="font-mono text-on-surface">#{{ $ticket->order->order_number }}</strong> (₹{{ number_format($ticket->order->total_amount, 2) }})
                    @endif
                </div>
            </div>
        </div>

        @if($ticket->order)
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('seller.orders.show', $ticket->order->id) }}" 
                   class="px-3.5 py-2 rounded-xl bg-surface-container hover:bg-surface-container-high text-secondary font-bold text-xs transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                    <span>View Order Details</span>
                </a>
            </div>
        @endif
    </div>

    <!-- Escalation Warning Banner (if escalated) -->
    @if($ticket->is_escalated)
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-300 flex items-start gap-3">
            <span class="material-symbols-outlined text-rose-600 text-2xl mt-0.5 shrink-0">warning</span>
            <div>
                <h4 class="font-bold text-rose-900 text-sm">Escalated to Formal Dispute Arbitration</h4>
                <p class="text-xs text-rose-700 mt-0.5 leading-relaxed">
                    The customer has escalated this ticket for senior dispute review. Reason given: <em>"{{ $ticket->escalation_reason }}"</em>. Please provide clear photographic evidence or tracking updates below.
                </p>
            </div>
        </div>
    @endif

    <!-- Discussion Timeline Stream Card -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/50 shadow-xs flex flex-col overflow-hidden">
        
        <div class="p-4 border-b border-outline-variant/30 flex items-center justify-between text-xs text-on-surface-variant bg-surface-container/30">
            <span class="font-bold text-on-surface uppercase tracking-wider text-[11px] font-mono">Inquiry Message Thread</span>
            <span>Category: <strong class="text-on-surface">{{ $ticket->category_label }}</strong></span>
        </div>

        <!-- Thread Messages -->
        <div class="p-5 sm:p-6 flex flex-col gap-5 divide-y divide-outline-variant/20">
            @foreach($ticket->messages as $msg)
                <div class="pt-4 first:pt-0">
                    @if($msg->sender_type === 'system')
                        <!-- System Notification -->
                        <div class="p-3 rounded-xl bg-surface-container/60 border border-outline-variant/30 text-xs text-on-surface-variant flex items-center gap-2 font-medium">
                            <span class="material-symbols-outlined text-on-surface-variant text-base">info</span>
                            <span>{{ $msg->message }}</span>
                            <span class="ml-auto text-[10px] text-outline-variant font-mono">{{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                    @else
                        <!-- Message Bubble -->
                        <div class="flex items-start gap-3.5 {{ $msg->sender_type === 'seller' ? 'flex-row-reverse text-right' : '' }}">
                            
                            <!-- Avatar -->
                            <div class="w-9 h-9 rounded-full shrink-0 flex items-center justify-center font-bold text-xs {{ $msg->sender_type === 'seller' ? 'bg-amber-500 text-slate-950 shadow-xs' : 'bg-surface-container text-on-surface' }}">
                                @if($msg->sender_type === 'seller')
                                    <span class="material-symbols-outlined text-[18px]">storefront</span>
                                @else
                                    <span>{{ strtoupper(substr($msg->user?->name ?? 'C', 0, 1)) }}</span>
                                @endif
                            </div>

                            <div class="flex flex-col gap-1 max-w-xl {{ $msg->sender_type === 'seller' ? 'items-end' : 'items-start' }}">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="font-bold text-on-surface">
                                        {{ $msg->sender_type === 'seller' ? 'You (Store)' : ($msg->user?->name ?? 'Customer') }}
                                    </span>
                                    <span class="text-on-surface-variant font-mono text-[10px]">
                                        {{ $msg->created_at->format('d M Y, h:i A') }}
                                    </span>
                                </div>

                                <div class="p-4 rounded-2xl text-xs sm:text-sm leading-relaxed {{ $msg->sender_type === 'seller' ? 'bg-amber-500 text-slate-950 font-medium rounded-tr-none shadow-xs' : 'bg-surface-container text-on-surface rounded-tl-none border border-outline-variant/40' }}">
                                    <p class="whitespace-pre-line text-left">{{ $msg->message }}</p>

                                    @if($msg->attachment_path)
                                        <div class="mt-3 pt-2.5 border-t {{ $msg->sender_type === 'seller' ? 'border-amber-600/40' : 'border-outline-variant/40' }}">
                                            @if($msg->isImageAttachment())
                                                <a href="{{ $msg->attachment_url }}" target="_blank" class="block group relative overflow-hidden rounded-xl border border-slate-700/20 max-w-xs">
                                                    <img src="{{ $msg->attachment_url }}" alt="Attachment" class="w-full max-h-48 object-cover group-hover:scale-105 transition-transform duration-200">
                                                    <span class="absolute bottom-1 right-1 px-2 py-0.5 rounded bg-black/70 text-white text-[10px] font-mono">View full image</span>
                                                </a>
                                            @else
                                                <a href="{{ $msg->attachment_url }}" target="_blank" 
                                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $msg->sender_type === 'seller' ? 'bg-amber-600/20 text-slate-950' : 'bg-surface-container-lowest text-on-surface border border-outline-variant/40' }} text-xs font-semibold hover:underline">
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

        <!-- Reply Form Box for Seller -->
        <div class="p-5 bg-surface-container/40 border-t border-outline-variant/30">
            <form action="{{ route('seller.support.tickets.reply', $ticket) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                @csrf
                <label for="seller_reply_message" class="block text-xs font-bold uppercase tracking-wider text-on-surface font-mono">
                    Reply to Customer
                </label>
                <textarea name="message" id="seller_reply_message" rows="3" required placeholder="Type your response to the customer. Provide tracking info, return guidance, or clarification..."
                          class="w-full p-3.5 rounded-2xl border border-outline-variant/60 text-xs sm:text-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-surface-container-lowest placeholder:text-on-surface-variant"></textarea>
                @error('message')
                    <p class="text-rose-600 text-xs">{{ $message }}</p>
                @enderror

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
                    <div class="flex items-center gap-2">
                        <label for="seller_reply_attachment" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-surface-container hover:bg-surface-container-high text-on-surface border border-outline-variant/60 text-xs font-semibold cursor-pointer transition-colors">
                            <span class="material-symbols-outlined text-[16px]">attach_file</span>
                            <span>Attach Proof / Label</span>
                        </label>
                        <input type="file" name="attachment" id="seller_reply_attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="hidden" onchange="document.getElementById('seller-file-name').textContent = this.files[0] ? this.files[0].name : '';">
                        <span id="seller-file-name" class="text-xs text-on-surface-variant font-mono truncate max-w-[200px]"></span>
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-on-surface hover:bg-slate-800 text-surface font-bold text-xs sm:text-sm transition-all shadow-md flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-amber-400">send</span>
                        <span>Send Response</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
