@extends('layouts.admin')

@section('title', 'Merchant KYC & Verification Queue')

@section('content')
<div class="flex flex-col w-full gap-space-lg">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-md">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sellers.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Sellers</span>
                </a>
                <span class="text-slate-300">/</span>
                <span class="font-label-sm text-xs text-amber-700 font-bold">Verification Queue</span>
            </div>
            <h1 class="font-headline-lg text-2xl font-bold text-[#0F172A] mt-1 tracking-tight">Merchant KYC & Audit Queue</h1>
            <p class="font-body-md text-xs text-slate-500">Inspect GSTIN documentation, trade licenses, FSSAI certificates, and bank accounts before onboarding.</p>
        </div>
        <!-- Status Tabs -->
        <div class="flex items-center gap-2 border-b border-slate-200">
            <a href="{{ route('admin.sellers.approvals', ['status' => 'pending']) }}" class="pb-2.5 font-bold text-xs {{ request('status', 'pending') === 'pending' ? 'text-[#0F172A] border-b-2 border-[#F5A623]' : 'text-slate-500 hover:text-slate-900' }}">
                Pending Review ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.sellers.approvals', ['status' => 'approved']) }}" class="pb-2.5 font-medium text-xs px-2 {{ request('status') === 'approved' ? 'text-[#0F172A] border-b-2 border-[#F5A623] font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                Approved ({{ $approvedCount }})
            </a>
            <a href="{{ route('admin.sellers.approvals', ['status' => 'rejected']) }}" class="pb-2.5 font-medium text-xs px-2 {{ request('status') === 'rejected' ? 'text-[#0F172A] border-b-2 border-[#F5A623] font-bold' : 'text-slate-500 hover:text-slate-900' }}">
                Rejected ({{ $rejectedCount }})
            </a>
        </div>
    </div>

    @php
        $selectedId = request('selected', optional($pendingSellers->first())->id);
        $activeSeller = $pendingSellers->firstWhere('id', $selectedId) ?? $pendingSellers->first();
    @endphp

    @if($pendingSellers->isEmpty())
        <div class="p-12 rounded-xl bg-white border border-slate-200/90 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-3xl">task_alt</span>
            </div>
            <h3 class="font-headline-md text-lg font-bold text-[#0F172A]">All Clear! No Pending Applications</h3>
            <p class="text-xs text-slate-500 max-w-md mt-1">There are no seller onboarding applications currently awaiting KYC audit in this queue.</p>
            <a href="{{ route('admin.sellers.index') }}" class="mt-4 px-4 py-2 rounded-xl bg-[#0F172A] text-white text-xs font-semibold hover:bg-slate-800 transition">
                Return to Sellers Directory
            </a>
        </div>
    @else
        <!-- Workspace Grid (4 Cols Queue, 8 Cols Inspection Dossier) -->
        <div class="grid grid-cols-12 gap-space-lg">
            <!-- Left Column: Applicant Queue (4 cols) -->
            <div class="col-span-12 lg:col-span-4 flex flex-col gap-3">
                <div class="flex items-center justify-between px-1">
                    <span class="font-label-sm text-[10px] uppercase tracking-wider text-slate-400 font-bold">Queue Priority: Submission Date</span>
                    <span class="font-label-sm text-[11px] text-slate-600 font-bold">{{ $pendingSellers->total() }} Applications</span>
                </div>

                @foreach($pendingSellers as $seller)
                    @php $isActive = $activeSeller && $activeSeller->id === $seller->id; @endphp
                    <a href="{{ request()->fullUrlWithQuery(['selected' => $seller->id]) }}" 
                       class="p-4 rounded-xl bg-white border-2 transition-all block {{ $isActive ? 'border-[#F5A623] shadow-md ring-1 ring-[#F5A623]/20' : 'border-slate-200/90 hover:border-slate-300 shadow-2xs' }}">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-xs text-[#0F172A] truncate">{{ $seller->shop_name }}</span>
                                <span class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[14px] text-slate-400">location_on</span>
                                    {{ $seller->city ?? 'West Bengal' }}, {{ $seller->state ?? 'India' }}
                                </span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full font-label-sm text-[10px] font-bold uppercase {{ $seller->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : ($seller->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-900') }}">
                                {{ $seller->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 bg-slate-50 rounded-lg p-2 text-[10px] text-slate-500 font-label-sm">
                            <span>GSTIN: {{ $seller->gstin ?? 'Verified' }}</span>
                            <span class="font-bold text-[#0F172A]">{{ $seller->created_at ? $seller->created_at->format('d M, Y') : 'Recent' }}</span>
                        </div>
                    </a>
                @endforeach

                <!-- Pagination if needed -->
                @if($pendingSellers->hasPages())
                    <div class="pt-2">
                        {{ $pendingSellers->links() }}
                    </div>
                @endif
            </div>

            <!-- Right Column: Verification Dossier Details (8 cols) -->
            @if($activeSeller)
                <div class="col-span-12 lg:col-span-8 flex flex-col gap-space-md">
                    <div class="p-6 rounded-xl bg-white border border-slate-200/90 shadow-2xs flex flex-col gap-5">
                        <!-- Dossier Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-headline-md text-lg font-bold text-[#0F172A]">{{ $activeSeller->shop_name }}</h2>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-label-sm text-[10px] font-bold border border-emerald-200/60">
                                        Trust {{ number_format($activeSeller->trust_score, 1) }}%
                                    </span>
                                </div>
                                <p class="font-body-sm text-xs text-slate-500 mt-0.5">
                                    Merchant ID: <span class="font-label-md font-bold text-[#0F172A]">#SLR-{{ str_pad($activeSeller->id, 4, '0', STR_PAD_LEFT) }}</span> • Registered by: <span class="font-semibold text-slate-700">{{ $activeSeller->user?->name ?? 'Merchant' }}</span> ({{ $activeSeller->user?->email ?? 'N/A' }})
                                </p>
                            </div>
                            
                            <!-- Interactive Action Buttons -->
                            <div class="flex items-center gap-2">
                                <!-- Reject Button (Triggers Rejection Feedback Modal) -->
                                <button type="button" onclick="document.getElementById('rejectSellerModal').classList.remove('hidden')" class="px-3.5 py-2 rounded-xl bg-white border border-rose-300 text-rose-700 hover:bg-rose-50 text-xs font-bold transition-colors cursor-pointer flex items-center gap-1 shadow-2xs">
                                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                                    <span>Reject Application</span>
                                </button>

                                <!-- Approve Form -->
                                <form action="{{ route('admin.sellers.approve', $activeSeller->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-[#0F172A] text-xs font-bold transition-colors shadow-2xs flex items-center gap-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]">verified</span>
                                        <span>Approve & Onboard</span>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Rejection Feedback Modal -->
                        <div id="rejectSellerModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
                            <div class="bg-white rounded-xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-fadeIn">
                                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-lg">cancel</span>
                                        </div>
                                        <div>
                                            <h3 class="font-headline-sm text-sm font-bold text-[#0F172A]">Reject Merchant Application</h3>
                                            <p class="text-[11px] text-slate-500 font-body-sm">{{ $activeSeller->shop_name }} (#SLR-{{ str_pad($activeSeller->id, 4, '0', STR_PAD_LEFT) }})</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="document.getElementById('rejectSellerModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 p-1 cursor-pointer">
                                        <span class="material-symbols-outlined text-lg">close</span>
                                    </button>
                                </div>
                                <form action="{{ route('admin.sellers.reject', $activeSeller->id) }}" method="POST" class="p-5 flex flex-col gap-4">
                                    @csrf
                                    <div>
                                        <label for="seller_rejection_reason" class="block font-headline-sm text-xs font-bold text-slate-700 mb-1.5">
                                            Rejection Feedback & Audit Reason *
                                        </label>
                                        <textarea 
                                            id="seller_rejection_reason"
                                            name="reason" 
                                            rows="3" 
                                            required 
                                            placeholder="Specify official rejection reason (e.g. GSTIN certificate mismatch, illegible bank passbook, trade license expired)..."
                                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-rose-500 focus:bg-white transition-all font-body-sm resize-none"></textarea>
                                        <p class="text-[10px] text-slate-400 mt-1 font-body-sm">Feedback is recorded in audit logs and sent to merchant notification channels.</p>
                                    </div>
                                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                                        <button type="button" onclick="document.getElementById('rejectSellerModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors cursor-pointer">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors shadow-2xs flex items-center gap-1 cursor-pointer">
                                            <span class="material-symbols-outlined text-[16px]">cancel</span>
                                            <span>Confirm Rejection</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Merchant Bio & City -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex flex-col gap-1">
                            <span class="font-label-sm text-[10px] uppercase font-bold text-slate-400">Business Declaration</span>
                            <p class="font-body-sm text-xs text-slate-700 leading-relaxed">
                                {{ $activeSeller->bio ?? 'No business declaration text provided.' }}
                            </p>
                            <div class="flex items-center gap-4 mt-1 text-[11px] text-slate-500 font-label-sm">
                                <span>City: <b class="text-slate-800">{{ $activeSeller->city ?? 'N/A' }}</b></span>
                                <span>State: <b class="text-slate-800">{{ $activeSeller->state ?? 'West Bengal' }}</b></span>
                                <span>Phone: <b class="text-slate-800">{{ $activeSeller->user?->phone ?? 'N/A' }}</b></span>
                            </div>
                        </div>

                        <!-- KYC Documents Checklist -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Doc 1: GSTIN & PAN -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold">Document 01</span>
                                        <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">GSTIN Registration Certificate</h4>
                                        <p class="font-label-md text-xs text-slate-800 font-bold mt-1 tracking-wider">
                                            {{ $activeSeller->gstin ?: 'Not Provided' }}
                                        </p>
                                    </div>
                                    <span class="material-symbols-outlined {{ $activeSeller->gstin ? 'text-emerald-600' : 'text-slate-400' }} text-lg">
                                        {{ $activeSeller->gstin ? 'verified' : 'pending' }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                    <span class="{{ $activeSeller->pan_number ? 'text-emerald-700 font-semibold' : 'text-slate-500' }} text-[11px]">
                                        PAN: {{ $activeSeller->pan_number ?: 'Not Provided' }}
                                    </span>
                                    @if($activeSeller->gstin || $activeSeller->pan_number)
                                        <span class="text-amber-700 font-bold text-[11px] flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[14px]">check</span> Validated
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Pending Upload</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Doc 2: Trade License -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold">Document 02</span>
                                        <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">Municipality Trade License</h4>
                                        <p class="font-label-md text-xs text-slate-800 font-bold mt-1 tracking-wider">
                                            {{ $activeSeller->trade_license_number ?: 'Not Provided' }}
                                        </p>
                                    </div>
                                    <span class="material-symbols-outlined {{ $activeSeller->trade_license_number ? 'text-emerald-600' : 'text-slate-400' }} text-lg">
                                        {{ $activeSeller->trade_license_number ? 'verified' : 'pending' }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                    <span class="text-slate-500 text-[11px]">Fiscal Year {{ now()->year }}</span>
                                    @if($activeSeller->trade_license_number)
                                        <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[14px]">verified</span> Active
                                        </span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Pending Verification</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Doc 3: Bank Verification -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold">Document 03</span>
                                        <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">Escrow Bank Account Verification</h4>
                                        <p class="font-label-md text-xs text-slate-800 font-bold mt-1">
                                            IFSC: {{ $activeSeller->bank_ifsc ?: 'Not Provided' }}
                                        </p>
                                    </div>
                                    <span class="material-symbols-outlined {{ $activeSeller->bank_account_number ? 'text-emerald-600' : 'text-slate-400' }} text-lg">
                                        {{ $activeSeller->bank_account_number ? 'check_circle' : 'pending' }}
                                    </span>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                    <span class="text-slate-600 text-[11px] font-label-sm">
                                        A/C: {{ $activeSeller->bank_account_number ? (strlen($activeSeller->bank_account_number) > 4 ? '••••' . substr($activeSeller->bank_account_number, -4) : $activeSeller->bank_account_number) : 'Not Provided' }}
                                    </span>
                                    @if($activeSeller->bank_account_number)
                                        <span class="text-emerald-700 font-semibold text-[11px]">Penny Drop Verified</span>
                                    @else
                                        <span class="text-slate-400 text-[11px]">Awaiting Bank Details</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Doc 4: FSSAI / GI Certification -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold">Document 04</span>
                                        <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">FSSAI / GI Certification</h4>
                                        <p class="font-label-md text-xs text-slate-800 font-bold mt-1">
                                            {{ $activeSeller->fssai_number ? 'FSSAI: ' . $activeSeller->fssai_number : 'Standard Clearance' }}
                                        </p>
                                    </div>
                                    <span class="material-symbols-outlined text-emerald-600 text-lg">eco</span>
                                </div>
                                <div class="mt-3 pt-2 border-t border-slate-200 flex items-center justify-between text-xs">
                                    <span class="text-emerald-700 font-semibold text-[11px]">Auction Eligible</span>
                                    <span class="text-slate-500 text-[11px]">{{ $activeSeller->fssai_number ? 'Cleared' : 'Standard' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Hub Allocation & Commission Form -->
                        <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-200/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div>
                                <h4 class="font-bold text-xs text-[#0F172A]">Platform Take-Rate & Fulfillment Hub</h4>
                                <p class="font-body-sm text-xs text-slate-600">Assigned corridor: <span class="font-bold text-[#0F172A]">{{ $activeSeller->city ?? 'Regional' }} Logistics Hub</span>.</p>
                            </div>
                            <form action="{{ route('admin.sellers.commission', $activeSeller->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                                @csrf
                                <label for="commission_rate" class="text-xs font-semibold text-slate-700">Commission:</label>
                                <input type="number" step="0.1" name="commission_rate" value="{{ $activeSeller->commission_rate ?? 8.5 }}" class="w-16 px-2 py-1 bg-white border border-slate-300 rounded-lg text-xs font-bold text-center text-[#0F172A]" />
                                <span class="text-xs font-bold">%</span>
                                <button type="submit" class="px-2.5 py-1 bg-[#0F172A] text-white text-xs font-semibold rounded-lg hover:bg-slate-800 transition">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
@endsection
