@extends('layouts.admin')

@section('title', 'Marketplace Coupons & Promotional Campaigns')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-space-sm mb-1">
                <h1 class="font-headline-lg text-2xl md:text-3xl font-bold text-[#0F172A] tracking-tight">Coupons & Campaigns</h1>
                <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-900 font-mono text-[11px] font-bold border border-teal-200/60">Marketing Ops</span>
            </div>
            <p class="font-body-md text-xs sm:text-sm text-slate-500">
                Manage promotional discount vouchers, festive campaigns, minimum spend thresholds, and regional merchant allowances.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="document.getElementById('createCouponDrawer').classList.toggle('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#F5A623] text-slate-950 font-body-md text-xs font-bold shadow-2xs hover:bg-amber-400 transition-colors">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Create New Coupon</span>
            </button>
        </div>
    </div>

    <!-- Create Coupon Collapsible Drawer -->
    <div id="createCouponDrawer" class="hidden bg-white rounded-xl p-5 border border-amber-200/80 shadow-sm transition-all">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-xl">confirmation_number</span>
                <h3 class="font-bold text-sm text-[#0F172A]">Create New Promotional Voucher</h3>
            </div>
            <button type="button" onclick="document.getElementById('createCouponDrawer').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.coupons.store') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Coupon Code *</label>
                <input type="text" name="code" required placeholder="e.g. PUJA2026" class="w-full uppercase px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono font-bold" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Discount Type *</label>
                <select name="discount_type" required class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-body-sm">
                    <option value="percentage">Percentage Discount (%)</option>
                    <option value="fixed">Fixed Rupee Discount (₹)</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Discount Value *</label>
                <input type="number" step="0.01" name="discount_value" required placeholder="e.g. 15 for 15% or 100 for ₹100" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Min Order Amount (₹)</label>
                <input type="number" step="0.01" name="minimum_order_amount" value="499" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Total Usage Limit</label>
                <input type="number" name="usage_limit" value="1000" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                <input type="date" name="expires_at" class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 font-mono" />
            </div>
            <div class="sm:col-span-2 lg:col-span-3 flex items-center gap-2 pt-2 border-t border-slate-100">
                <button type="submit" class="px-5 py-2 bg-[#0F172A] hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-colors">
                    Deploy Voucher Campaign
                </button>
                <button type="button" onclick="document.getElementById('createCouponDrawer').classList.add('hidden')" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Total Platform Coupons</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-[#0F172A]">{{ $stats['total'] }}</span>
            </div>
            <span class="text-xs text-slate-400">Configured promotional codes</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Live Active Campaigns</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-emerald-700">{{ $stats['active'] }}</span>
            </div>
            <span class="text-xs text-emerald-700 font-bold">Ready for buyer checkout</span>
        </div>

        <div class="bg-white rounded-xl p-4 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
            <span class="font-mono text-[10px] text-slate-500 tracking-wider uppercase font-bold">Total Redemptions</span>
            <div class="my-2">
                <span class="font-mono text-2xl font-bold text-amber-700">{{ number_format($stats['total_redeemed']) }}</span>
            </div>
            <span class="text-xs text-slate-400">Applied by checkout carts</span>
        </div>
    </div>

    <!-- Active Coupons Table -->
    <div class="rounded-xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-xs uppercase tracking-wider text-slate-700 font-mono">Platform Promotional Vouchers</h3>
            <span class="text-xs text-slate-400 font-mono">Showing {{ $coupons->total() }} Coupons</span>
        </div>
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-[#FFFDF8] text-slate-500 font-mono text-[10px] uppercase border-b border-slate-100">
                        <th class="py-3 px-4">Coupon Code</th>
                        <th class="py-3 px-4">Discount Value</th>
                        <th class="py-3 px-4 text-right">Min Spend</th>
                        <th class="py-3 px-4 text-center">Redemptions</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Expiry Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-body-sm text-[#0F172A]">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-900 font-mono font-bold text-xs border border-amber-200">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold text-emerald-700 font-mono">
                                @if($coupon->discount_type === 'percentage')
                                    {{ $coupon->discount_value }}% OFF
                                @else
                                    Flat ₹{{ number_format($coupon->discount_value, 2) }} Instant Off
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-right text-slate-700">₹{{ number_format($coupon->minimum_order_amount, 2) }}</td>
                            <td class="py-3 px-4 font-mono text-center font-semibold">
                                <span class="text-[#0F172A]">{{ $coupon->used_count }}</span>
                                <span class="text-slate-400">/ {{ $coupon->usage_limit ?? '∞' }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($coupon->status === 'active')
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[10px] font-bold border border-emerald-200/60">Active</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-mono text-[10px] font-bold border border-slate-200">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500 text-right">
                                {{ $coupon->expires_at ? $coupon->expires_at->format('d M, Y') : 'No Expiry' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-colors {{ $coupon->status === 'active' ? 'bg-slate-100 text-slate-700 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                            {{ $coupon->status === 'active' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                    @if(($coupon->used_count ?? 0) === 0 && ($coupon->orders_count ?? 0) === 0)
                                        <form method="POST" action="{{ route('admin.coupons.destroy', $coupon->id) }}" onsubmit="return confirm('Delete coupon {{ $coupon->code }}?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors" title="Delete Coupon">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="p-1 rounded-lg text-slate-300 cursor-not-allowed" title="Cannot delete: coupon has {{ max($coupon->used_count ?? 0, $coupon->orders_count ?? 0) }} redemptions">
                                            <span class="material-symbols-outlined text-[18px]">lock</span>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400">
                                <span class="material-symbols-outlined text-3xl mb-1 text-slate-300">loyalty</span>
                                <p>No platform coupons found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
