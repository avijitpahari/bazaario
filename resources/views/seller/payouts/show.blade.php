@extends('layouts.seller')

@section('title', 'Payout #' . $payout->payout_reference . ' — Bazaario')

@section('content')
@php
    $profile = $sellerProfile ?? Auth::guard('seller')->user()?->sellerProfile ?? Auth::user()?->sellerProfile;
    $shopName = $profile?->shop_name ?? 'My Farm';
    $rawAccount = $profile?->bank_account_number;
    $hasBankDetails = !empty($rawAccount) && strlen($rawAccount) >= 4;
    $maskedBankAcc = $hasBankDetails ? '•••• ' . substr($rawAccount, -4) : 'Not configured';
    $bankIfsc = $profile?->bank_ifsc ?: 'Not configured';
    $bankName = $hasBankDetails ? 'HDFC Bank' : 'Not configured';
    $order = $payout->sellerOrder;
@endphp

<div class="flex flex-col w-full pb-16">
    <!-- Breadcrumb & Header -->
    <div class="flex flex-col gap-1 mb-6">
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-slate-500 uppercase tracking-wider">
            <a href="{{ route('seller.payouts.index') }}" class="hover:text-slate-900 transition-colors">Payouts</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-slate-900 font-semibold">#{{ $payout->payout_reference }}</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-3xl font-bold text-slate-900 tracking-tight">Payout Receipt #{{ $payout->payout_reference }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md {{ $payout->status === 'paid' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200/80' : 'bg-amber-50 text-amber-900 border border-amber-200/80' }} font-mono text-xs font-bold uppercase tracking-wider">
                        {{ strtoupper($payout->status) }}
                    </span>
                </div>
                <p class="font-sans text-sm text-slate-500 max-w-2xl mt-1">
                    Settlement reference generated on {{ $payout->created_at?->format('F j, Y, g:i A \I\S\T') ?? 'Today' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.payouts.summary') }}" class="h-11 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-heading text-xs font-bold transition-all shadow-xs flex items-center gap-2 cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">summarize</span>
                    <span>View Payout Summary</span>
                </a>
                <a href="{{ route('seller.payouts.index') }}" class="h-11 px-4 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border border-slate-200/90 transition-colors shadow-2xs flex items-center gap-2 font-sans text-xs font-semibold">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Back to Payouts</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2 Column Workspace -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <!-- Left: Itemized Financial Breakdown -->
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 overflow-hidden p-6 flex flex-col gap-4">
                <h3 class="font-heading text-lg font-bold text-slate-900">Financial Ledger Summary</h3>

                <div class="bg-slate-50/80 border border-slate-200/70 rounded-xl p-5 flex flex-col gap-3 font-sans text-xs">
                    <div class="flex items-center justify-between text-slate-900">
                        <span class="text-sm font-semibold">Gross Order Value</span>
                        <span class="font-mono text-sm font-bold">₹{{ number_format($payout->gross_amount, 2) }}</span>
                    </div>
                    @php
                        $commRate = ($payout->gross_amount > 0 && $payout->commission_amount > 0)
                            ? round(($payout->commission_amount / $payout->gross_amount) * 100, 1)
                            : (float) ($payout->sellerOrder?->commission_rate ?? \App\Models\SiteSetting::getCommissionRate());
                    @endphp
                    <div class="flex items-center justify-between text-rose-600">
                        <span>Platform Commission (-{{ number_format($commRate, 1) }}%)</span>
                        <span class="font-mono font-medium">-₹{{ number_format($payout->commission_amount, 2) }}</span>
                    </div>
                    @if($payout->apmc_cess > 0)
                        <div class="flex items-center justify-between text-rose-600">
                            <span>APMC Mandi Cess / Tech Fee (-1.5%)</span>
                            <span class="font-mono font-medium">-₹{{ number_format($payout->apmc_cess, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-slate-500">
                        <span>GST on Commission (18%)</span>
                        <span class="font-mono">₹0.00 <span class="text-[10px] text-emerald-700 font-semibold">(Agro Exempt)</span></span>
                    </div>
                    <div class="flex items-center justify-between text-slate-500">
                        <span>Logistics &amp; Hub Fleet Fee</span>
                        <span class="font-mono">₹0.00 <span class="text-[10px] text-emerald-700 font-semibold">(Covered)</span></span>
                    </div>

                    <div class="h-px bg-slate-200/80 my-1"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-slate-900">Net Deposit Settled</span>
                        <span class="font-mono text-xl font-bold text-emerald-700">₹{{ number_format($payout->net_amount, 2) }}</span>
                    </div>
                </div>

                @if($order)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <h4 class="font-heading text-base font-bold text-slate-900 mb-3">Linked Consignment Order</h4>
                        <div class="bg-slate-50/60 border border-slate-200/60 rounded-xl p-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                            <div>
                                <span class="font-mono font-bold text-slate-900">Order #{{ $order->seller_order_number }}</span>
                                <span class="font-sans text-slate-500 block mt-0.5">
                                    Customer: {{ $order->order?->delivery_full_name ?? 'Customer' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-mono text-slate-400">Delivery Slot:</span>
                                <span class="font-medium text-slate-900 block">{{ $order->delivery_slot ?? 'Delivered' }}</span>
                            </div>
                            <a href="{{ route('seller.orders.show', $order) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 font-semibold shadow-2xs transition-colors">
                                View Order
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Destination Bank & Telemetry -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <!-- Bank Account Box -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Settlement Bank Account</span>
                <div class="font-sans text-base font-semibold text-slate-900">{{ $bankName }}</div>
                <div class="font-mono text-xs text-slate-500">Beneficiary: {{ $shopName }}</div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-100 font-mono text-xs text-slate-700">
                    <span>A/C: <strong class="text-slate-900">{{ $maskedBankAcc }}</strong></span>
                    <span class="text-slate-500">IFSC: <strong class="text-slate-700">{{ $bankIfsc }}</strong></span>
                </div>
            </div>

            <!-- Settlement Telemetry -->
            <div class="bg-white rounded-2xl shadow-2xs border border-slate-200/80 p-6 flex flex-col gap-3 text-xs">
                <span class="font-mono text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Audit Telemetry</span>
                <div class="flex justify-between">
                    <span class="text-slate-500">Payment Method:</span>
                    <span class="font-mono font-semibold text-slate-900">Automated NEFT / RTGS</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Escrow Status:</span>
                    <span class="font-mono font-semibold text-emerald-700">Settlement Released</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Processed Date:</span>
                    <span class="font-mono text-slate-700">{{ $payout->paid_at ? $payout->paid_at->format('M j, Y') : 'Pending Batch Clearance' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
            </div>
        </div>
    </div>
</div>
@endsection
