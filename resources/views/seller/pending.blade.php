@extends('layouts.seller-onboarding')

@section('title', 'Application Status Terminal — Bazaario Seller Hub')

@php
    $user = $user ?? Auth::guard('seller')->user() ?? Auth::user();
    $profile = $user?->sellerProfile;
    $initialState = request()->query('preview_state', $profile?->status ?? 'pending');
    $rejectionReason = $profile?->rejection_reason ?? 'Your application packet is currently under regional admin verification.';
@endphp

@section('content')
<div x-data="pendingTerminal({
    state: '{{ $initialState }}',
    appId: 'BZ-{{ str_pad($profile?->id ?? 1, 5, '0', STR_PAD_LEFT) }}',
    subDate: '{{ $profile?->created_at?->format('M d, Y') ?? now()->format('M d, Y') }}',
    sellerType: '{{ $profile?->seller_type ?? 'Farmer' }}',
    shopName: '{{ addslashes($profile?->shop_name ?? ($user?->name . '\'s Enterprise')) }}',
    rejectionReason: '{{ addslashes($rejectionReason) }}'
})" class="max-w-4xl mx-auto space-y-6">


    <!-- MAIN STATUS CARD -->
    <div class="bg-white border border-brand-outline rounded-[14px] p-6 sm:p-10 shadow-card">
        
        <!-- Dynamic State Header -->
        <div class="mb-8">
            <!-- State Badge -->
            <div class="inline-flex items-center gap-2 text-xs font-mono font-bold px-3 py-1 rounded-[8px] mb-3"
                 :class="{
                     'bg-amber-50 border border-brand-amber/30 text-amber-800': currentState === 'pending',
                     'bg-green-50 border border-green-300 text-green-800': currentState === 'approved',
                     'bg-blue-50 border border-blue-300 text-blue-800': currentState === 'more_info',
                     'bg-red-50 border border-red-300 text-red-800': currentState === 'rejected',
                     'bg-slate-100 border border-slate-300 text-slate-800': currentState === 'suspended'
                 }">
                <span class="w-2 h-2 rounded-full"
                      :class="{
                          'bg-brand-amber': currentState === 'pending',
                          'bg-brand-green': currentState === 'approved',
                          'bg-blue-600': currentState === 'more_info',
                          'bg-red-600': currentState === 'rejected',
                          'bg-slate-600': currentState === 'suspended'
                      }"></span>
                <span x-text="getStatusBadgeText()">STATUS: AWAITING ADMIN APPROVAL</span>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl sm:text-3xl font-heading font-bold text-brand-slate" x-text="getStatusHeading()">
                Application Submitted
            </h1>
            <p class="text-brand-muted text-sm sm:text-base mt-2 leading-relaxed" x-text="getStatusDescription()">
                Your seller application is being reviewed. We’ll notify you when your account is approved.
            </p>
        </div>

        <!-- KEY SUMMARY INFO STRIP -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 bg-brand-subtle border border-brand-outline rounded-[14px] mb-8">
            <div>
                <span class="text-[10px] font-mono text-brand-muted uppercase block">Application ID</span>
                <span class="font-mono font-bold text-brand-slate text-xs sm:text-sm" x-text="appId">BZ-00001</span>
            </div>
            <div>
                <span class="text-[10px] font-mono text-brand-muted uppercase block">Submission Date</span>
                <span class="font-mono font-bold text-brand-slate text-xs sm:text-sm" x-text="subDate">Today</span>
            </div>
            <div>
                <span class="text-[10px] font-mono text-brand-muted uppercase block">Seller Type</span>
                <span class="font-mono font-bold text-brand-slate text-xs sm:text-sm" x-text="sellerType">Farmer</span>
            </div>
            <div>
                <span class="text-[10px] font-mono text-brand-muted uppercase block">Shop / Farm</span>
                <span class="font-mono font-bold text-brand-slate text-xs sm:text-sm truncate block" x-text="shopName">My Shop</span>
            </div>
        </div>

        <!-- REVIEW PROGRESS TIMELINE (3 Stages) -->
        <div class="mb-8">
            <h4 class="text-xs font-heading font-bold text-brand-slate uppercase tracking-wider mb-4">Review Progress Timeline</h4>
            <div class="relative flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-6 bg-white border border-brand-outline rounded-[14px]">
                
                <!-- Stage 1: Submitted -->
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center font-mono text-xs font-bold shadow">
                        ✓
                    </div>
                    <div>
                        <p class="text-xs font-bold font-heading text-brand-slate">Application Submitted</p>
                        <p class="text-[11px] text-brand-muted">Uploaded &amp; encrypted</p>
                    </div>
                </div>

                <!-- Connector Line 1 -->
                <div class="hidden sm:block flex-1 h-0.5 bg-brand-green mx-2"></div>

                <!-- Stage 2: Under Review -->
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono text-xs font-bold shadow"
                         :class="{
                             'bg-brand-amber text-brand-slate ring-4 ring-amber-100': currentState === 'pending',
                             'bg-brand-green text-white': currentState === 'approved',
                             'bg-blue-600 text-white ring-4 ring-blue-100': currentState === 'more_info',
                             'bg-red-600 text-white ring-4 ring-red-100': currentState === 'rejected',
                             'bg-slate-400 text-white': currentState === 'suspended'
                         }">
                        <span x-show="currentState === 'pending'">⏳</span>
                        <span x-show="currentState === 'approved'">✓</span>
                        <span x-show="currentState === 'more_info'">❓</span>
                        <span x-show="currentState === 'rejected'">✕</span>
                        <span x-show="currentState === 'suspended'">🔒</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold font-heading text-brand-slate">Compliance Audit</p>
                        <p class="text-[11px] text-brand-muted">Regional verification desk</p>
                    </div>
                </div>

                <!-- Connector Line 2 -->
                <div class="hidden sm:block flex-1 h-0.5 mx-2"
                     :class="currentState === 'approved' ? 'bg-brand-green' : (currentState === 'rejected' ? 'bg-red-400' : 'bg-[#EAE6DC]')"></div>

                <!-- Stage 3: Clearance / Live -->
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-mono text-xs font-bold shadow-sm"
                         :class="{
                             'bg-[#FAF8F2] border border-brand-outline text-brand-muted': currentState === 'pending' || currentState === 'more_info',
                             'bg-brand-green text-white': currentState === 'approved',
                             'bg-red-600 text-white': currentState === 'rejected',
                             'bg-slate-300 text-slate-700': currentState === 'suspended'
                         }">
                        <span x-show="currentState === 'approved'">✓</span>
                        <span x-show="currentState === 'rejected'">✕</span>
                        <span x-show="currentState !== 'approved' && currentState !== 'rejected'">03</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold font-heading"
                           :class="currentState === 'approved' ? 'text-brand-green' : (currentState === 'rejected' ? 'text-red-700' : 'text-brand-muted')">
                            Marketplace Clearance
                        </p>
                        <p class="text-[11px] text-brand-muted" x-text="currentState === 'approved' ? 'Active &amp; trading live' : (currentState === 'rejected' ? 'Application rejected' : 'Live dashboard clearance')"></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- DYNAMIC NOTICE BOX -->
        <div class="p-5 rounded-[14px] border space-y-3"
             :class="{
                 'bg-brand-subtle border-brand-outline': currentState === 'pending',
                 'bg-green-50/80 border-green-200': currentState === 'approved',
                 'bg-blue-50/80 border-blue-200': currentState === 'more_info',
                 'bg-red-50/80 border-red-200': currentState === 'rejected',
                 'bg-slate-50 border-slate-300': currentState === 'suspended'
             }">
            
            <!-- In Pending -->
            <div x-show="currentState === 'pending'" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-amber/20 text-brand-slate flex items-center justify-center shrink-0">
                    ℹ️
                </div>
                <div>
                    <h4 class="text-xs font-bold font-heading text-brand-slate">What happens next?</h4>
                    <p class="text-xs text-brand-muted mt-1 leading-relaxed">
                        Our regional compliance team is currently validating your GPS coordinates, farm plot, and trade details against local records. Review typically completes within <strong>24 to 48 hours</strong>. You will receive an SMS and email notification once approved.
                    </p>
                </div>
            </div>

            <!-- In Approved -->
            <div x-show="currentState === 'approved'" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-brand-green text-white flex items-center justify-center shrink-0">
                    ✓
                </div>
                <div>
                    <h4 class="text-xs font-bold font-heading text-green-900">Congratulations! Your account is active.</h4>
                    <p class="text-xs text-green-800 mt-1 leading-relaxed">
                        Your merchant profile is fully certified. You can now access your seller dashboard, create live wholesale auctions, add products to the catalog, and fulfill customer orders.
                    </p>
                </div>
            </div>

            <!-- In More Info -->
            <div x-show="currentState === 'more_info'" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                    ❓
                </div>
                <div>
                    <h4 class="text-xs font-bold font-heading text-blue-900">Action Required: Compliance Officer Note</h4>
                    <p class="text-xs text-blue-800 mt-1 leading-relaxed">
                        "Please update your street address and re-verify your GPS coordinates. The plot location submitted is currently ambiguous on regional maps."
                    </p>
                </div>
            </div>

            <!-- In Rejected -->
            <div x-show="currentState === 'rejected'" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-800 flex items-center justify-center shrink-0">
                    ✕
                </div>
                <div>
                    <h4 class="text-xs font-bold font-heading text-red-900">Application Not Approved</h4>
                    <p class="text-xs text-red-800 mt-1 leading-relaxed" x-text="rejectionReason"></p>
                </div>
            </div>

            <!-- In Suspended -->
            <div x-show="currentState === 'suspended'" class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center shrink-0">
                    🔒
                </div>
                <div>
                    <h4 class="text-xs font-bold font-heading text-slate-900">Merchant Account Suspended</h4>
                    <p class="text-xs text-slate-700 mt-1 leading-relaxed">
                        Access to the seller center has been locked due to an ongoing compliance review. If you believe this is in error, please reach out to <a href="mailto:support@bazaario.in" class="underline font-bold">support@bazaario.in</a>.
                    </p>
                </div>
            </div>

        </div>

        <!-- SECURITY GATE NOTICE -->
        <div class="mt-8 pt-6 border-t border-brand-outline/70 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="currentState === 'approved' ? 'bg-brand-green' : 'bg-red-500'"></span>
                <p class="text-xs font-mono text-brand-muted">
                    <span x-show="currentState !== 'approved'">Restricted Access: Live Seller Operating Dashboard locked until admin approval.</span>
                    <span x-show="currentState === 'approved'" class="text-brand-green font-bold">Clearance Granted: Live Seller Operating Dashboard accessible.</span>
                </p>
            </div>

            <!-- DYNAMIC PRIMARY CTA -->
            <div>
                <!-- When Approved -->
                <a x-show="currentState === 'approved'" href="{{ route('seller.dashboard') }}" 
                   class="px-6 py-3 bg-brand-green hover:bg-green-700 text-white font-heading font-bold text-xs rounded-[14px] shadow-sm transition inline-flex items-center gap-2">
                    <span>Enter Seller Workspace</span>
                    <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                </a>

                <!-- When Pending / More Info / Rejected -->
                <a x-show="currentState === 'pending' || currentState === 'more_info' || currentState === 'rejected'" 
                   href="{{ route('seller.onboarding') }}" 
                   class="px-5 py-2.5 border border-brand-outline bg-brand-subtle hover:bg-slate-100 text-brand-slate font-heading text-xs font-semibold rounded-[14px] transition inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">edit</span>
                    <span x-text="currentState === 'pending' ? 'Review Submission Details' : (currentState === 'more_info' ? 'Update Application Details' : 'Correct Details & Resubmit')">
                        Review Submission Details
                    </span>
                </a>

                <!-- When Suspended -->
                <a x-show="currentState === 'suspended'" href="mailto:support@bazaario.in" 
                   class="px-5 py-2.5 border border-brand-outline bg-slate-100 hover:bg-slate-200 text-slate-800 font-heading text-xs font-semibold rounded-[14px] transition inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-[16px]">mail</span>
                    <span>Contact Merchant Concierge</span>
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function pendingTerminal(config) {
    return {
        currentState: config.state || 'pending',
        appId: config.appId || 'BZ-00001',
        subDate: config.subDate || 'Oct 24, 2026',
        sellerType: config.sellerType || 'Farmer',
        shopName: config.shopName || 'Enterprise',
        rejectionReason: config.rejectionReason || 'Plot boundary not verified.',

        getStatusBadgeText() {
            switch(this.currentState) {
                case 'approved': return 'STATUS: ACCOUNT APPROVED';
                case 'more_info': return 'STATUS: MORE INFO REQUIRED';
                case 'rejected': return 'STATUS: APPLICATION REJECTED';
                case 'suspended': return 'STATUS: ACCOUNT SUSPENDED';
                default: return 'STATUS: AWAITING REVIEW / ADMIN APPROVAL';
            }
        },

        getStatusHeading() {
            switch(this.currentState) {
                case 'approved': return 'Welcome to Bazaario Seller Hub!';
                case 'more_info': return 'Document Clarification Needed';
                case 'rejected': return 'Application Not Approved';
                case 'suspended': return 'Seller Access Suspended';
                default: return 'Application Submitted';
            }
        },

        getStatusDescription() {
            switch(this.currentState) {
                case 'approved': return 'Your seller credentials have been verified. You now have full access to the live operating dashboard, catalog workstation, and wholesale auctions.';
                case 'more_info': return 'Our compliance desk needs further verification of your plot or shop credentials before proceeding.';
                case 'rejected': return 'Your seller registration did not meet compliance criteria. Please review the reasons below and resubmit.';
                case 'suspended': return 'Your seller account access is temporarily suspended due to administrative or policy review.';
                default: return 'Your seller application is being reviewed. We’ll notify you when your account is approved.';
            }
        }
    };
}
</script>
@endpush
