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
        <div class="flex items-center gap-1.5 font-mono text-[11px] text-outline uppercase tracking-wider">
            <a href="{{ route('seller.payouts.index') }}" class="hover:text-on-surface transition-colors">Payouts</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-on-surface font-semibold">#{{ $payout->payout_reference }}</span>
        </div>
        <div class="flex flex-wrap items-end justify-between gap-4 pt-1">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-3xl font-bold text-on-surface tracking-tight">Payout Receipt #{{ $payout->payout_reference }}</h1>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-[6px] bg-secondary-fixed text-on-secondary-fixed font-mono text-xs font-bold uppercase tracking-wider">
                        {{ strtoupper($payout->status) }}
                    </span>
                </div>
                <p class="font-sans text-sm text-on-surface-variant max-w-2xl mt-1">
                    Settlement reference generated on {{ $payout->created_at?->format('F j, Y, g:i A \I\S\T') ?? 'Today' }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('seller.payouts.index') }}" class="h-11 px-4 rounded-[14px] bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-colors shadow-sm flex items-center gap-2 font-sans text-xs font-semibold">
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
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm overflow-hidden p-6 flex flex-col gap-4">
                <h3 class="font-heading text-lg font-bold text-on-surface">Financial Ledger Summary</h3>

                <div class="bg-surface-container-low rounded-[14px] p-5 flex flex-col gap-3 font-sans text-xs">
                    <div class="flex items-center justify-between text-on-surface">
                        <span class="text-sm font-semibold">Gross Order Value</span>
                        <span class="font-mono text-sm font-bold">₹{{ number_format($payout->gross_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-error">
                        <span>Platform Commission (-10%)</span>
                        <span class="font-mono font-medium">-₹{{ number_format($payout->commission_amount, 2) }}</span>
                    </div>
                    @if($payout->apmc_cess > 0)
                        <div class="flex items-center justify-between text-error">
                            <span>APMC Mandi Cess / Tech Fee (-1.5%)</span>
                            <span class="font-mono font-medium">-₹{{ number_format($payout->apmc_cess, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>GST on Commission (18%)</span>
                        <span class="font-mono">₹0.00 <span class="text-[10px] text-on-tertiary-container font-semibold">(Agro Exempt)</span></span>
                    </div>
                    <div class="flex items-center justify-between text-on-surface-variant">
                        <span>Logistics &amp; Hub Fleet Fee</span>
                        <span class="font-mono">₹0.00 <span class="text-[10px] text-on-tertiary-container font-semibold">(Covered)</span></span>
                    </div>

                    <div class="h-px bg-surface-container-high my-1"></div>
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-on-surface">Net Deposit Settled</span>
                        <span class="font-mono text-xl font-bold text-on-tertiary-container">₹{{ number_format($payout->net_amount, 2) }}</span>
                    </div>
                </div>

                @if($order)
                    <div class="mt-4 pt-4 border-t border-surface-container-high">
                        <h4 class="font-heading text-base font-bold text-on-surface mb-3">Linked Consignment Order</h4>
                        <div class="bg-surface-container-low/40 rounded-[14px] p-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                            <div>
                                <span class="font-mono font-bold text-on-surface">Order #{{ $order->seller_order_number }}</span>
                                <span class="font-sans text-on-surface-variant block mt-0.5">
                                    Customer: {{ $order->order?->delivery_full_name ?? 'Customer' }}
                                </span>
                            </div>
                            <div>
                                <span class="font-mono text-outline">Delivery Slot:</span>
                                <span class="font-medium text-on-surface block">{{ $order->delivery_slot ?? 'Delivered' }}</span>
                            </div>
                            <a href="{{ route('seller.orders.show', $order) }}" class="px-3 py-1.5 rounded-[10px] bg-surface-container text-on-surface hover:bg-surface-container-high font-semibold">
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
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-6 flex flex-col gap-3">
                <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Settlement Bank Account</span>
                <div class="font-sans text-base font-semibold text-on-surface">{{ $bankName }}</div>
                <div class="font-mono text-xs text-on-surface-variant">Beneficiary: {{ $shopName }}</div>
                <div class="flex items-center justify-between pt-2 border-t border-surface-container-high font-mono text-xs text-on-surface">
                    <span>A/C: {{ $maskedBankAcc }}</span>
                    <span class="text-on-surface-variant">IFSC: {{ $bankIfsc }}</span>
                </div>
            </div>

            <!-- Settlement Telemetry -->
            <div class="bg-surface-container-lowest rounded-[14px] shadow-sm p-6 flex flex-col gap-3 text-xs">
                <span class="font-mono text-[11px] uppercase tracking-wider text-outline">Audit Telemetry</span>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Payment Method:</span>
                    <span class="font-mono font-semibold">Automated NEFT / RTGS</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Escrow Status:</span>
                    <span class="font-mono font-semibold text-on-tertiary-container">Settlement Released</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant">Processed Date:</span>
                    <span class="font-mono">{{ $payout->paid_at ? $payout->paid_at->format('M j, Y') : 'Pending Batch Clearance' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
