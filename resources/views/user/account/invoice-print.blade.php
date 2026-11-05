<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice #{{ $order->order_number }} — Bazaario Marketplace</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-4 sm:p-8">
    <div class="max-w-3xl mx-auto bg-white p-6 sm:p-10 rounded-2xl shadow-lg border border-slate-200">
        {{-- Header Bar --}}
        <div class="flex items-center justify-between border-b border-slate-200 pb-6 mb-6">
            <div>
                <img src="{{ asset('images/bazaario-logo.png') }}" alt="Bazaario" class="h-10 mb-2">
                <p class="text-xs text-slate-500 font-mono">Bazaario Regional Commerce & Logistics Pvt Ltd</p>
                <p class="text-xs text-slate-500">GSTIN: 19AAACB1234F1Z5 • Contai, West Bengal, India</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-amber-100 text-amber-900 font-mono font-bold text-xs uppercase tracking-wider mb-2">Original Tax Invoice</span>
                <h1 class="text-lg font-bold font-mono text-slate-900">#{{ $order->order_number }}</h1>
                <p class="text-xs text-slate-500">Date: {{ $order->placed_at ? $order->placed_at->format('d M Y, h:i A') : $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
        </div>

        {{-- Buyer & Shipping Info --}}
        <div class="grid grid-cols-2 gap-6 text-xs mb-6 p-4 rounded-xl bg-slate-50 border border-slate-100">
            <div>
                <h3 class="font-bold text-slate-700 uppercase tracking-wider font-mono mb-1">Billed & Delivered To:</h3>
                <p class="font-bold text-sm text-slate-900">{{ $order->delivery_full_name ?? $order->user?->name }}</p>
                <p class="text-slate-600 mt-0.5">{{ $order->delivery_address_line_1 }}</p>
                @if($order->delivery_address_line_2)<p class="text-slate-600">{{ $order->delivery_address_line_2 }}</p>@endif
                <p class="text-slate-600">{{ $order->delivery_city }}, {{ $order->delivery_state }} - {{ $order->delivery_postal_code }}</p>
                <p class="text-slate-600 font-mono mt-1">Phone: {{ $order->delivery_phone ?? $order->user?->phone }}</p>
            </div>
            <div class="text-right">
                <h3 class="font-bold text-slate-700 uppercase tracking-wider font-mono mb-1">Payment Information:</h3>
                <p class="text-slate-800 font-semibold">Method: <span class="uppercase font-mono">{{ $order->payment_method }}</span></p>
                <p class="text-slate-800 font-semibold mt-0.5">Status: <span class="uppercase font-mono text-emerald-700">{{ $order->payment_status }}</span></p>
                @if($order->payments->first())
                    <p class="text-slate-500 font-mono text-[11px] mt-1">Txn ID: {{ $order->payments->first()->transaction_id }}</p>
                @endif
            </div>
        </div>

        {{-- Order Items Table --}}
        <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-100 text-slate-700 font-bold uppercase font-mono text-[11px] border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Description</th>
                        <th class="py-3 px-4 text-center">Qty</th>
                        <th class="py-3 px-4 text-right">Unit Price</th>
                        <th class="py-3 px-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @foreach($order->sellerOrders as $so)
                        @foreach($so->items as $item)
                        <tr>
                            <td class="py-3 px-4">
                                <span class="font-sans font-semibold text-slate-900 block">{{ $item->product_name }}</span>
                                <span class="text-[10px] text-slate-400">Sold by: {{ $so->shop_name ?? ($so->seller?->name ?? 'Artisan Seller') }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3 px-4 text-right font-bold">₹{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Financial Summary Breakdown --}}
        <div class="flex justify-end mb-8">
            <div class="w-64 space-y-1.5 text-xs font-mono">
                <div class="flex justify-between text-slate-600">
                    <span>Items Subtotal:</span>
                    <span>₹{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-700 font-semibold">
                    <span>Coupon Discount:</span>
                    <span>-₹{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-slate-600">
                    <span>Shipping & Handling:</span>
                    <span>₹{{ number_format($order->shipping_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>GST (Included):</span>
                    <span>₹{{ number_format($order->total_amount * 0.05, 2) }} (5%)</span>
                </div>
                <div class="border-t-2 border-slate-900 pt-2 flex justify-between text-sm font-bold text-slate-900">
                    <span>Total Paid:</span>
                    <span>₹{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Print & Action Bar --}}
        <div class="no-print pt-6 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('user.invoices') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition flex items-center gap-1">
                &larr; Back to Invoices
            </a>
            <button onclick="window.print()" class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-2 cursor-pointer shadow-md">
                <span>Print / Save as PDF</span>
            </button>
        </div>
    </div>
</body>
</html>
