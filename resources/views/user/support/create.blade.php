@extends('layouts.user')

@section('title', 'Open Support Ticket — Bazaario')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8 flex flex-col gap-6">

    <!-- Header Breadcrumb & Title -->
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('user.support.index') }}" class="hover:text-slate-900 transition-colors">Help &amp; Support</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold">New Ticket</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-black font-['Outfit'] text-slate-900 tracking-tight">
            Create Support Ticket
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Submit an inquiry regarding your order, shipment, return, or live auction. The verified merchant and our customer care team will review your case.
        </p>
    </div>

    <!-- Ticket Form Card with Quick FAQ Suggestions -->
    <div x-data="{
            selectedCategory: '{{ old('category', request('category', 'order_issue')) }}',
            showFaqSuggestion: true,
            solved: false,
            
            get faqTip() {
                switch(this.selectedCategory) {
                    case 'return_refund':
                        return {
                            title: 'Need a return or refund?',
                            answer: 'Bazaario provides a 7-day return window on craft and pantry goods, and a 48-hour freshness guarantee on farm produce. You can initiate an instant doorstep return with 1-click in the Returns Portal without waiting for a support ticket reply!',
                            action_url: '{{ route('user.returns') }}',
                            action_label: 'Open Returns Portal',
                            chat_action: 'faq_returns'
                        };
                    case 'delivery_delay':
                        return {
                            title: 'Tracking or shipment delay?',
                            answer: 'Real-time courier telemetry tracks your package from Packing & Sealing to Doorstep Handover. If your delivery slot is delayed, our automated dispatch monitor alerts the logistics desk immediately.',
                            action_url: '{{ route('user.orders.index') }}',
                            action_label: 'Track Active Orders',
                            chat_action: 'track_order'
                        };
                    case 'auction':
                        return {
                            title: 'Wholesale auction question?',
                            answer: 'Winning bidders receive an immediate hammer allocation notice and have 24 hours to complete escrow checkout. All live auctions feature an Anti-Snipe +2 minute soft-close guard.',
                            action_url: '{{ route('auctions.index') }}',
                            action_label: 'Browse Live Floor',
                            chat_action: 'faq_auctions'
                        };
                    case 'billing':
                        return {
                            title: 'Payment or escrow question?',
                            answer: 'All payments on Bazaario are 100% safeguarded in an APMC-grade Escrow Vault. Funds are never released to the merchant until you inspect and approve your delivery. Zero extra surcharges.',
                            action_url: '{{ route('user.orders.index') }}',
                            action_label: 'View Orders & Receipts',
                            chat_action: 'faq_payments'
                        };
                    default:
                        return {
                            title: 'Need quick assistance?',
                            answer: 'Our automated support bot can answer questions about tracking, returns, escrow guarantees, and product sourcing in real-time.',
                            action_url: '{{ route('user.support.index') }}#quick-faq-section',
                            action_label: 'Browse Quick FAQs',
                            chat_action: 'other'
                        };
                }
            },
            
            triggerChat(action) {
                if (window.bazaarioTriggerFaq) {
                    window.bazaarioTriggerFaq(action);
                } else if (window.bazaarioOpenChat) {
                    window.bazaarioOpenChat();
                }
            }
         }"
         class="flex flex-col gap-6">

        <!-- Dynamic Quick FAQ Auto-Reply Suggestion Banner -->
        <div x-show="showFaqSuggestion && !solved" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-300/80 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-700 flex items-center justify-center shrink-0 mt-0.5 border border-amber-400/30">
                    <span class="material-symbols-outlined text-[20px]">bolt</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.2 rounded-md bg-amber-500 text-slate-950 text-[9px] font-mono font-black uppercase tracking-wider">
                            QUICK FAQ AUTO-REPLY
                        </span>
                        <h4 class="font-bold text-xs sm:text-sm text-slate-900 font-['Outfit']" x-text="faqTip.title"></h4>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed max-w-2xl" x-text="faqTip.answer"></p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                <button type="button" 
                        @click="triggerChat(faqTip.chat_action)"
                        class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition-colors flex items-center gap-1 shadow-2xs cursor-pointer">
                    <span class="material-symbols-outlined text-[15px]">chat</span>
                    <span>Ask in Chat</span>
                </button>
                <a :href="faqTip.action_url" 
                   class="px-3 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-800 font-semibold text-xs border border-slate-200 transition-colors shadow-2xs">
                    <span x-text="faqTip.action_label"></span>
                </a>
                <button type="button" 
                        @click="showFaqSuggestion = false" 
                        title="Dismiss Tip"
                        class="text-slate-400 hover:text-slate-600 p-1">
                    ✕
                </button>
            </div>
        </div>

        <!-- Ticket Form Card -->
        <div class="p-6 sm:p-8 bg-white rounded-3xl border border-slate-200/90 shadow-sm">
            <form action="{{ route('user.support.tickets.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-6">
                @csrf

                <!-- Form Row 1: Category & Priority -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Inquiry Category <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" id="category" required x-model="selectedCategory"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-slate-50/50">
                        <option value="order_issue" {{ old('category') === 'order_issue' ? 'selected' : '' }}>Order &amp; Product Defect</option>
                        <option value="return_refund" {{ old('category') === 'return_refund' ? 'selected' : '' }}>Return &amp; Escrow Refund Request</option>
                        <option value="delivery_delay" {{ old('category') === 'delivery_delay' ? 'selected' : '' }}>Shipping / Delivery Delay</option>
                        <option value="auction" {{ old('category') === 'auction' ? 'selected' : '' }}>Live Auction Settlement</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing &amp; Payment Inquiry</option>
                        <option value="general" {{ old('category', 'general') === 'general' ? 'selected' : '' }}>General Question</option>
                    </select>
                    @error('category')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Priority Level <span class="text-rose-500">*</span>
                    </label>
                    <select name="priority" id="priority" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-slate-50/50">
                        <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low — General questions &amp; feedback</option>
                        <option value="medium" {{ old('priority', 'medium') === 'medium' ? 'selected' : '' }}>Medium — Standard order inquiry</option>
                        <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High — Damaged item or dispatch delay</option>
                        <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent — Wrong item delivered / payment deduction</option>
                    </select>
                    @error('priority')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Row 2: Link Order (Optional) -->
            <div>
                <label for="order_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Link Recent Order <span class="text-slate-400 font-normal lowercase">(optional but recommended)</span>
                </label>
                <select name="order_id" id="order_id"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all bg-slate-50/50">
                    <option value="">-- No specific order linked --</option>
                    @foreach($recentOrders as $order)
                        <option value="{{ $order->id }}" {{ (old('order_id', $selectedOrder?->id) == $order->id) ? 'selected' : '' }}>
                            Order #{{ $order->order_number }} • ₹{{ number_format($order->total_amount, 2) }} • {{ $order->placed_at ? $order->placed_at->format('d M Y') : $order->created_at->format('d M Y') }} ({{ ucfirst($order->order_status) }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Linking an order notifies the specific store merchant so they can immediately inspect your tracking and items.</p>
                @error('order_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Row 3: Subject -->
            <div>
                <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Ticket Subject <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required placeholder="e.g. Broken item received in package / Tracking not updating"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all placeholder:text-slate-400">
                @error('subject')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Row 4: Detailed Message -->
            <div>
                <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Describe Your Issue in Detail <span class="text-rose-500">*</span>
                </label>
                <textarea name="message" id="message" rows="5" required placeholder="Please provide clear details, unboxing notes, or specific questions so the store can resolve this quickly."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all placeholder:text-slate-400">{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Row 5: Photo / Document Attachment -->
            <div>
                <label for="attachment" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Attach Photo / Proof / Invoice <span class="text-slate-400 font-normal lowercase">(optional, JPG, PNG, WEBP, PDF up to 10MB)</span>
                </label>
                <input type="file" name="attachment" id="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf"
                       class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                @error('attachment')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('user.support.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm transition-all shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-400">send</span>
                    <span>Submit Ticket</span>
                </button>
            </div>
        </form>
    </div>
</div>

</div>
@endsection
