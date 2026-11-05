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
                    <select name="category" id="category" required
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
                    Attach Photo / Proof / Invoice <span class="text-slate-400 font-normal lowercase">(optional, JPG, PNG, WEBP, PDF up to 5MB)</span>
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
@endsection
