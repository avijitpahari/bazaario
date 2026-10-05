@extends('layouts.admin')

@section('title', 'Customer Trust Dossier #' . ($customer ? 'CUST-' . $customer->id : 'N/A'))

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    @if(!$customer)
        <div class="bg-white rounded-xl p-8 text-center text-slate-500 border border-slate-200">
            <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">person_off</span>
            <h2 class="text-lg font-bold text-[#0F172A]">Customer Not Found</h2>
            <p class="text-xs text-slate-500 mt-1">The requested customer record does not exist in the platform database.</p>
            <a href="{{ route('admin.customers.index') }}" class="mt-4 inline-block px-4 py-2 bg-[#0F172A] text-white rounded-xl text-xs font-semibold">
                Back to Directory
            </a>
        </div>
    @else
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md bg-white p-5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.customers.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">badge</span>
                        <span>Customers</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="font-mono text-xs text-amber-700 font-bold">Dossier #CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <h1 class="font-headline-lg text-2xl font-bold text-[#0F172A] mt-1 tracking-tight">Customer Trust & Identity Dossier</h1>
                <p class="font-body-md text-xs text-slate-500">Comprehensive order history, telemetry signals, verified addresses, and platform engagement.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 font-mono text-xs font-bold border border-emerald-200/60">
                    Status: {{ strtoupper($customer->status) }}
                </span>
                <form method="POST" action="{{ route('admin.customers.toggle-status', $customer->id) }}" onsubmit="return confirm('Change status for this customer?');">
                    @csrf
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold shadow-2xs transition-colors {{ $customer->status === 'suspended' ? 'bg-emerald-600 hover:bg-emerald-500 text-white' : 'bg-red-600 hover:bg-red-500 text-white' }}">
                        {{ $customer->status === 'suspended' ? 'Reactivate Account' : 'Suspend Buyer' }}
                    </button>
                </form>
                {{-- ── Admin Impersonation: Preview as this user ── --}}
                <form method="POST" action="{{ route('admin.impersonate', $customer->id) }}" onsubmit="return confirm('Enter the user panel as {{ addslashes($customer->name) }}?');">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-amber-400 hover:bg-amber-300 text-slate-900 shadow-2xs transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview as User
                    </button>
                </form>
            </div>
        </div>

        <!-- Customer Overview Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <div class="lg:col-span-4 flex flex-col gap-4">
                <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-[#0F172A] text-amber-400 flex items-center justify-center font-bold text-base shrink-0">
                            {{ strtoupper(substr((string)($customer->name ?? 'CU'), 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-[#0F172A]">{{ $customer->name }}</h3>
                            <span class="font-mono text-[11px] text-slate-400">#CUST-{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }} • Joined {{ $customer->created_at ? $customer->created_at->format('M Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex flex-col gap-2.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Email:</span>
                            <span class="font-mono font-medium text-[#0F172A]">{{ $customer->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Phone:</span>
                            <span class="font-mono font-medium text-[#0F172A]">{{ $customer->phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Orders:</span>
                            <span class="font-mono font-bold text-emerald-700">{{ $customer->orders->count() }} orders</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Lifetime Spend:</span>
                            <span class="font-mono font-bold text-[#0F172A]">₹{{ number_format((float)($customer->orders->sum('total_amount') ?? 0), 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Email Verified:</span>
                            <span class="font-mono font-bold text-emerald-700">{{ $customer->email_verified_at ? 'Yes ✓' : 'No' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Addresses Card -->
                <div class="bg-white rounded-xl p-5 border border-slate-200/90 shadow-2xs flex flex-col gap-3">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-slate-500 font-mono">Saved Shipping Addresses</h3>
                    <div class="flex flex-col gap-2">
                        @forelse($customer->addresses as $address)
                            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 text-xs">
                                <span class="font-bold text-[#0F172A] block">{{ $address->full_name ?? $customer->name }}</span>
                                <span class="text-slate-600 block mt-0.5">{{ $address->address_line_1 ?? 'Address' }}</span>
                                @if($address->address_line_2)
                                    <span class="text-slate-500 block">{{ $address->address_line_2 }}</span>
                                @endif
                                <span class="text-slate-500 block font-mono text-[11px]">{{ $address->city ?? '' }}, {{ $address->state ?? '' }} - {{ $address->postal_code ?? '' }}</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">No saved shipping addresses on file.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="lg:col-span-8 flex flex-col gap-4">
                <!-- Recent Transactions Table -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Order History ({{ $customer->orders->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                    <th class="py-2.5 px-4">Order Ref</th>
                                    <th class="py-2.5 px-4">Payment Method</th>
                                    <th class="py-2.5 px-4 text-right">Amount</th>
                                    <th class="py-2.5 px-4 text-center">Status</th>
                                    <th class="py-2.5 px-4 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-body-sm">
                                @forelse($customer->orders as $ord)
                                    <tr>
                                        <td class="py-3 px-4 font-mono font-bold text-[#0F172A]">
                                            <a href="{{ route('admin.orders.show', $ord->order_number) }}" class="hover:text-amber-600 transition-colors">
                                                #{{ $ord->order_number }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 uppercase font-mono text-slate-600">{{ $ord->payment_method ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 font-mono font-bold text-right text-[#0F172A]">₹{{ number_format((float)($ord->total_amount ?? 0), 2) }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if($ord->order_status === 'completed')
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold">Completed</span>
                                            @elseif($ord->order_status === 'processing')
                                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-mono text-[10px] font-bold">Processing</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-mono text-[10px] font-bold">{{ ucfirst($ord->order_status) }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 font-mono text-slate-400 text-right">
                                            {{ $ord->created_at ? $ord->created_at->format('d M Y') : 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">
                                            No orders placed yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Auction Bids Table -->
                <div class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Recent Live Auction Bids ({{ $bids->count() }})</h3>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 font-mono text-[10px] uppercase text-slate-500 border-b border-slate-100">
                                    <th class="py-2.5 px-4">Lot Ref</th>
                                    <th class="py-2.5 px-4">Item Name</th>
                                    <th class="py-2.5 px-4 text-right">Bid Amount</th>
                                    <th class="py-2.5 px-4 text-right">Bid Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-body-sm">
                                @forelse($bids as $b)
                                    <tr>
                                        <td class="py-3 px-4 font-mono font-bold text-amber-700">
                                            <a href="{{ route('admin.auctions.show', $b->auction_id) }}" class="hover:underline">
                                                #AUC-{{ $b->auction_id }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-slate-700">{{ $b->auction?->product?->name ?? 'Auction Lot #' . $b->auction_id }}</td>
                                        <td class="py-3 px-4 font-mono font-bold text-right text-emerald-700">₹{{ number_format((float)($b->amount ?? 0), 2) }}</td>
                                        <td class="py-3 px-4 font-mono text-slate-400 text-right">{{ $b->created_at ? $b->created_at->format('M d, H:i') : 'Logged' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-slate-400">
                                            No auction bids placed by this buyer yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
