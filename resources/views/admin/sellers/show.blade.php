@extends('layouts.admin')

@section('title', 'Merchant Dossier — ' . ($seller->shop_name ?? 'Seller'))

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sellers.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">storefront</span>
                    <span>Sellers</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-mono text-xs text-amber-700 font-bold">#SLR-{{ str_pad($seller->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <h1 class="font-headline-lg text-2xl font-bold text-[#0F172A] mt-1 tracking-tight">{{ $seller->shop_name }}</h1>
            <p class="font-body-md text-xs text-slate-500">
                Operated by <strong>{{ $seller->user->name ?? 'Merchant User' }}</strong> ({{ $seller->user->email ?? 'N/A' }}) • Regional Hub: {{ $seller->city ?? 'West Bengal' }}, {{ $seller->state ?? 'India' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($seller->status === 'approved')
                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-mono text-xs font-bold border border-emerald-200">
                    KYC APPROVED ✓
                </span>
            @elseif($seller->status === 'pending')
                <span class="px-3 py-1.5 rounded-xl bg-amber-50 text-amber-800 font-mono text-xs font-bold border border-amber-200">
                    KYC PENDING AUDIT
                </span>
            @else
                <span class="px-3 py-1.5 rounded-xl bg-red-50 text-red-800 font-mono text-xs font-bold border border-red-200">
                    {{ strtoupper($seller->status) }}
                </span>
            @endif

            <form method="POST" action="{{ route('admin.sellers.toggle-status', $seller->id) }}" onsubmit="return confirm('Change status for this merchant?');">
                @csrf
                <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold shadow-2xs transition-colors {{ $seller->status === 'suspended' ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-red-600 hover:bg-red-500 text-white' }}">
                    {{ $seller->status === 'suspended' ? 'Reactivate Merchant' : 'Suspend Merchant' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Merchant Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
        <!-- Left: KYC Compliance & Banking Dossier -->
        <div class="lg:col-span-4 flex flex-col gap-4">
            <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center font-bold text-lg shrink-0">
                        {{ strtoupper(substr($seller->shop_name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-[#0F172A]">{{ $seller->shop_name }}</h3>
                        <span class="font-mono text-[11px] text-slate-400">Trust Score: ★ {{ number_format($seller->trust_score, 1) }}</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Contact Owner:</span>
                        <span class="font-bold text-[#0F172A]">{{ $seller->user->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email:</span>
                        <span class="font-mono text-[#0F172A]">{{ $seller->user->email ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Phone:</span>
                        <span class="font-mono text-[#0F172A]">{{ $seller->user->phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Location:</span>
                        <span class="text-[#0F172A]">{{ $seller->city ?? 'Contai' }}, {{ $seller->state ?? 'WB' }}</span>
                    </div>
                </div>

                <!-- KYC & Tax Registrations -->
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <span class="font-mono text-[10px] text-slate-400 uppercase font-bold">Tax & Statutory Compliance</span>
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex flex-col gap-1.5 font-mono text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">GSTIN:</span>
                            <span class="font-bold text-[#0F172A]">{{ $seller->gstin ?? 'Not Provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">PAN:</span>
                            <span class="font-bold text-[#0F172A]">{{ $seller->pan_number ?? 'Not Provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Trade License:</span>
                            <span class="font-bold text-[#0F172A]">{{ $seller->trade_license_number ?? 'Verified Municipality' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bank IFSC:</span>
                            <span class="font-bold text-[#0F172A]">{{ $seller->bank_ifsc ?? 'SBIN0000058' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Bank A/C:</span>
                            <span class="font-bold text-[#0F172A]">•••• {{ substr($seller->bank_account_number ?? '1234567890', -4) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Commission Rate Editor -->
                <div class="pt-3 border-t border-slate-100 flex flex-col gap-2">
                    <span class="font-mono text-[10px] text-slate-400 uppercase font-bold">Custom Commission Tier</span>
                    <form method="POST" action="{{ route('admin.sellers.commission', $seller->id) }}" class="flex items-center gap-2">
                        @csrf
                        <div class="relative flex-1">
                            <input type="number" step="0.1" name="commission_rate" value="{{ $seller->commission_rate }}" class="w-full pl-3 pr-7 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-[#0F172A] focus:outline-none focus:border-amber-500" />
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 font-mono text-xs text-slate-400">%</span>
                        </div>
                        <button type="submit" class="px-3 py-1.5 bg-[#0F172A] hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-colors">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: Listed Catalog SKUs & Consignments -->
        <div class="lg:col-span-8 flex flex-col gap-4">
            <!-- Catalog SKUs Table -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Listed Catalog SKUs ({{ $seller->products->count() }})</h3>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-amber-700 font-semibold hover:underline">
                        Catalog Directory →
                    </a>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                <th class="py-2.5 px-4">Product Name</th>
                                <th class="py-2.5 px-4">Sale Type</th>
                                <th class="py-2.5 px-4 text-right">Price</th>
                                <th class="py-2.5 px-4 text-center">Stock</th>
                                <th class="py-2.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-body-sm">
                            @forelse($seller->products as $p)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-[#0F172A]">
                                        <a href="{{ url('/products/' . ($p->slug ?? $p->id)) }}" target="_blank" class="hover:text-amber-600 transition-colors">
                                            {{ $p->name }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-500">
                                        <span class="px-2 py-0.5 rounded-full {{ $p->sale_type === 'auction' ? 'bg-amber-50 text-amber-800' : 'bg-blue-50 text-blue-800' }} text-[10px] font-bold">
                                            {{ strtoupper($p->sale_type ?? 'DIRECT') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-mono font-bold text-right text-[#0F172A]">₹{{ number_format($p->price, 2) }}</td>
                                    <td class="py-3 px-4 font-mono text-center">{{ $p->stock }} units</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold">
                                            {{ strtoupper($p->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        No products listed by this merchant yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Consignment Orders -->
            <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Recent Fulfilled Consignments ({{ $recentOrders->count() }})</h3>
                </div>
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                <th class="py-2.5 px-4">Sub-Order #</th>
                                <th class="py-2.5 px-4">Parent Order</th>
                                <th class="py-2.5 px-4 text-right">Subtotal</th>
                                <th class="py-2.5 px-4 text-right">Net Payout</th>
                                <th class="py-2.5 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-body-sm">
                            @forelse($recentOrders as $so)
                                <tr>
                                    <td class="py-3 px-4 font-mono font-bold text-[#0F172A]">{{ $so->seller_order_number }}</td>
                                    <td class="py-3 px-4 font-mono text-amber-700 font-bold">
                                        <a href="{{ route('admin.orders.show', $so->order->order_number ?? $so->order_id) }}" class="hover:underline">
                                            #{{ $so->order->order_number ?? 'BZ-ORDER' }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 font-mono text-right text-slate-700">₹{{ number_format($so->subtotal, 2) }}</td>
                                    <td class="py-3 px-4 font-mono font-bold text-right text-emerald-700">₹{{ number_format($so->payout_amount, 2) }}</td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-mono text-[10px] font-bold">
                                            {{ strtoupper($so->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">
                                        No consignment orders fulfilled yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
