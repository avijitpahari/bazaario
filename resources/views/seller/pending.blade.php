@extends('layouts.seller-onboarding')

@section('title', 'Application Status Terminal — ' . \App\Models\SiteSetting::getSiteName() . ' Seller Hub')

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

        @php
            $hasGstinOrPan = !empty($profile?->gstin) || !empty($profile?->pan_number);
            $hasTradeLicense = !empty($profile?->trade_license_number);
            $hasBank = !empty($profile?->bank_account_number) || !empty($profile?->bank_ifsc);
            $hasFssai = !empty($profile?->fssai_number);
            $hasAnyCredential = $hasGstinOrPan || $hasTradeLicense || $hasBank || $hasFssai;
        @endphp

        <!-- 4 REQUIRED COMPLIANCE DOCUMENTS DOSSIER -->
        <div class="mb-8" x-data="{ uploadModalOpen: false, activeDocType: '{{ $hasGstinOrPan ? 'gstin' : ($hasTradeLicense ? 'trade_license' : ($hasBank ? 'bank' : ($hasFssai ? 'fssai' : 'gstin'))) }}' }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h4 class="text-xs font-heading font-bold text-brand-slate uppercase tracking-wider">Required Compliance Dossier & Verification Documents</h4>
                    <p class="text-[11px] text-brand-muted">Attach official tax registration, trade license, and escrow bank proofs for admin audit.</p>
                </div>
                @if($hasAnyCredential)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-mono font-bold bg-amber-50 text-amber-800 border border-amber-200 w-fit">
                        <span class="w-2 h-2 rounded-full bg-brand-amber animate-pulse"></span>
                        Document Uploads Unlocked
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-mono font-medium bg-slate-100 text-slate-600 border border-slate-200 w-fit">
                        <span class="material-symbols-outlined text-[14px]">lock</span>
                        Add Credentials to Unlock Uploads
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                
                <!-- DOCUMENT 01: GSTIN Registration Certificate & PAN -->
                <div class="bg-white border rounded-[14px] p-5 shadow-sm transition flex flex-col justify-between {{ $hasGstinOrPan ? 'border-brand-outline hover:border-brand-amber/50' : 'border-slate-200/80 bg-slate-50/40' }}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-[10px] text-brand-muted uppercase font-bold tracking-wider">DOCUMENT 01</span>
                            @if($hasGstinOrPan)
                                <button type="button" @click="activeDocType = 'gstin'; uploadModalOpen = true;" 
                                        class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-brand-slate text-[11px] font-mono font-bold flex items-center gap-1 transition shadow-xs" title="Upload GSTIN / PAN Certificate">
                                    <span class="material-symbols-outlined text-[15px] text-amber-800">upload_file</span>
                                    <span>Upload Document</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    <span>Locked</span>
                                </span>
                            @endif
                        </div>
                        <h3 class="font-heading font-bold text-sm text-brand-slate">GSTIN Registration Certificate</h3>
                        <p class="font-mono text-xs font-bold text-slate-800 mt-1">
                            {{ $profile?->gstin ? 'GSTIN: ' . $profile->gstin : 'Not Provided' }}
                        </p>

                        @if($profile?->gstin_doc_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($profile->gstin_doc_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 hover:bg-amber-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    <span>View Uploaded GST Certificate</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono text-xs">
                        <span class="text-brand-muted text-[11px]">
                            PAN: {{ $profile?->pan_number ?: 'Not Provided' }}
                        </span>
                        <div>
                            @if($profile?->gstin_doc_path)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    File Uploaded
                                </span>
                            @elseif($hasGstinOrPan)
                                <button type="button" @click="activeDocType = 'gstin'; uploadModalOpen = true;" class="group inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                                    <span>Upload Certificate</span>
                                    <span class="material-symbols-outlined text-[13px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-0.5">arrow_forward</span>
                                </button>
                            @else
                                <span class="text-slate-400 text-[11px]">Add GSTIN/PAN to unlock</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- DOCUMENT 02: Municipality Trade License -->
                <div class="bg-white border rounded-[14px] p-5 shadow-sm transition flex flex-col justify-between {{ $hasTradeLicense ? 'border-brand-outline hover:border-brand-amber/50' : 'border-slate-200/80 bg-slate-50/40' }}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-[10px] text-brand-muted uppercase font-bold tracking-wider">DOCUMENT 02</span>
                            @if($hasTradeLicense)
                                <button type="button" @click="activeDocType = 'trade_license'; uploadModalOpen = true;" 
                                        class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-brand-slate text-[11px] font-mono font-bold flex items-center gap-1 transition shadow-xs" title="Upload Trade License">
                                    <span class="material-symbols-outlined text-[15px] text-amber-800">upload_file</span>
                                    <span>Upload Document</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    <span>Locked</span>
                                </span>
                            @endif
                        </div>
                        <h3 class="font-heading font-bold text-sm text-brand-slate">Municipality Trade License</h3>
                        <p class="font-mono text-xs font-bold text-slate-800 mt-1">
                            {{ $profile?->trade_license_number ? 'No: ' . $profile->trade_license_number : 'Not Provided' }}
                        </p>

                        @if($profile?->trade_license_doc_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($profile->trade_license_doc_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 hover:bg-amber-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    <span>View Uploaded License File</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono text-xs">
                        <span class="text-brand-muted text-[11px]">Fiscal Year {{ now()->year }}</span>
                        <div>
                            @if($profile?->trade_license_doc_path)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    File Uploaded
                                </span>
                            @elseif($hasTradeLicense)
                                <button type="button" @click="activeDocType = 'trade_license'; uploadModalOpen = true;" class="group inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                                    <span>Upload License</span>
                                    <span class="material-symbols-outlined text-[13px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-0.5">arrow_forward</span>
                                </button>
                            @else
                                <span class="text-slate-400 text-[11px]">Add License No. to unlock</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- DOCUMENT 03: Escrow Bank Account Verification -->
                <div class="bg-white border rounded-[14px] p-5 shadow-sm transition flex flex-col justify-between {{ $hasBank ? 'border-brand-outline hover:border-brand-amber/50' : 'border-slate-200/80 bg-slate-50/40' }}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-[10px] text-brand-muted uppercase font-bold tracking-wider">DOCUMENT 03</span>
                            @if($hasBank)
                                <button type="button" @click="activeDocType = 'bank'; uploadModalOpen = true;" 
                                        class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-brand-slate text-[11px] font-mono font-bold flex items-center gap-1 transition shadow-xs" title="Upload Bank Proof">
                                    <span class="material-symbols-outlined text-[15px] text-amber-800">upload_file</span>
                                    <span>Upload Document</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    <span>Locked</span>
                                </span>
                            @endif
                        </div>
                        <h3 class="font-heading font-bold text-sm text-brand-slate">Escrow Bank Account Verification</h3>
                        <p class="font-mono text-xs font-bold text-slate-800 mt-1">
                            IFSC: {{ $profile?->bank_ifsc ?: 'Not Provided' }}
                        </p>

                        @if($profile?->bank_doc_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($profile->bank_doc_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 hover:bg-amber-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    <span>View Uploaded Bank Cheque / Proof</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono text-xs">
                        <span class="text-brand-muted text-[11px]">
                            A/C: {{ $profile?->bank_account_number ? (strlen($profile->bank_account_number) > 4 ? '•••• ' . substr($profile->bank_account_number, -4) : $profile->bank_account_number) : 'Not Provided' }}
                        </span>
                        <div>
                            @if($profile?->bank_doc_path)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    File Uploaded
                                </span>
                            @elseif($hasBank)
                                <button type="button" @click="activeDocType = 'bank'; uploadModalOpen = true;" class="group inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                                    <span>Upload Cheque Proof</span>
                                    <span class="material-symbols-outlined text-[13px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-0.5">arrow_forward</span>
                                </button>
                            @else
                                <span class="text-slate-400 text-[11px]">Add Bank Account to unlock</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- DOCUMENT 04: FSSAI / GI Certification -->
                <div class="bg-white border rounded-[14px] p-5 shadow-sm transition flex flex-col justify-between {{ $hasFssai ? 'border-brand-outline hover:border-brand-amber/50' : 'border-slate-200/80 bg-slate-50/40' }}">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-mono text-[10px] text-brand-muted uppercase font-bold tracking-wider">DOCUMENT 04</span>
                            @if($hasFssai)
                                <button type="button" @click="activeDocType = 'fssai'; uploadModalOpen = true;" 
                                        class="px-2.5 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-brand-slate text-[11px] font-mono font-bold flex items-center gap-1 transition shadow-xs" title="Upload FSSAI Certificate">
                                    <span class="material-symbols-outlined text-[15px] text-green-700">upload_file</span>
                                    <span>Upload Document</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                    <span class="material-symbols-outlined text-[13px]">lock</span>
                                    <span>Locked</span>
                                </span>
                            @endif
                        </div>
                        <h3 class="font-heading font-bold text-sm text-brand-slate">FSSAI / GI Certification</h3>
                        <p class="font-mono text-xs font-bold text-slate-800 mt-1">
                            {{ $profile?->fssai_number ? 'FSSAI: ' . $profile->fssai_number : 'Standard Clearance' }}
                        </p>

                        @if($profile?->fssai_doc_path)
                            <div class="mt-2">
                                <a href="{{ Storage::url($profile->fssai_doc_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-mono font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded border border-amber-200 hover:bg-amber-100 transition">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    <span>View Uploaded FSSAI Certificate</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono text-xs">
                        <span class="text-green-700 font-bold text-[11px] flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[14px]">check_circle</span> Auction Eligible
                        </span>
                        <div>
                            @if($profile?->fssai_doc_path)
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    File Uploaded
                                </span>
                            @elseif($hasFssai)
                                <button type="button" @click="activeDocType = 'fssai'; uploadModalOpen = true;" class="group inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                                    <span>Upload FSSAI Doc</span>
                                    <span class="material-symbols-outlined text-[13px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-0.5">arrow_forward</span>
                                </button>
                            @else
                                <span class="text-slate-400 text-[11px]">Add FSSAI No. to unlock</span>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- DOCUMENT UPLOAD MODAL -->
            <div x-cloak x-show="uploadModalOpen" 
                 class="fixed inset-0 z-50 bg-brand-slate/60 backdrop-blur-sm flex items-center justify-center p-4"
                 @keydown.escape.window="uploadModalOpen = false"
                 @click.self="uploadModalOpen = false">
                
                <div class="bg-white border border-brand-outline rounded-[14px] max-w-lg w-full p-6 sm:p-8 shadow-modal transform transition-all space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-brand-outline">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-amber-100 text-brand-amber flex items-center justify-center">
                                <span class="material-symbols-outlined text-xl">upload_file</span>
                            </div>
                            <div>
                                <h3 class="text-base font-heading font-bold text-brand-slate" 
                                    x-text="activeDocType === 'gstin' ? 'Upload GSTIN & PAN Certificate' : (activeDocType === 'trade_license' ? 'Upload Municipality Trade License' : (activeDocType === 'bank' ? 'Upload Bank Cheque / Passbook Proof' : 'Upload FSSAI / GI Certification'))">
                                    Upload Document
                                </h3>
                                <p class="text-[11px] text-brand-muted">Attach PDF or image file (max 5MB)</p>
                            </div>
                        </div>
                        <button type="button" @click="uploadModalOpen = false" class="text-brand-muted hover:text-brand-slate p-1 rounded-lg">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    <form action="{{ route('seller.pending.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <input type="hidden" name="doc_type" :value="activeDocType">

                        <!-- Dynamic Inputs based on activeDocType -->
                        <div x-show="activeDocType === 'gstin'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">GSTIN Registration Number</label>
                                <input type="text" name="gstin" value="{{ $profile?->gstin }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. 19AAAAA0000A1Z5" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">PAN Card Number</label>
                                <input type="text" name="pan_number" value="{{ $profile?->pan_number }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. ABCDE1234F" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'trade_license'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Trade License Registration Number</label>
                                <input type="text" name="trade_license_number" value="{{ $profile?->trade_license_number }}" maxlength="50" placeholder="e.g. TL-2026-98745" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'bank'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Bank Account Number</label>
                                <input type="text" name="bank_account_number" value="{{ $profile?->bank_account_number }}" maxlength="50" oninput="this.value = this.value.replace(/[^0-9A-Za-z]/g, '')" placeholder="e.g. 30987456123" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Bank IFSC Code</label>
                                <input type="text" name="bank_ifsc" value="{{ $profile?->bank_ifsc }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. SBIN0000058" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'fssai'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">FSSAI License / GI Registration Number</label>
                                <input type="text" name="fssai_number" value="{{ $profile?->fssai_number }}" maxlength="30" placeholder="e.g. 10020031000123" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <!-- File Input -->
                        <div>
                            <label class="block text-xs font-bold text-brand-slate mb-1">Attach Document File (PDF / PNG / JPG)</label>
                            <input type="file" name="document_file" accept=".pdf,.png,.jpg,.jpeg,.webp" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-amber file:text-brand-slate hover:file:bg-amber-400 cursor-pointer">
                        </div>

                        <div class="pt-3 flex items-center justify-end gap-2 border-t border-brand-outline">
                            <button type="button" @click="uploadModalOpen = false" class="px-4 py-2 bg-brand-subtle hover:bg-slate-200 text-brand-slate text-xs font-semibold rounded-lg transition">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2 bg-brand-amber hover:bg-amber-400 text-brand-slate text-xs font-bold rounded-lg transition flex items-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-sm">cloud_upload</span>
                                <span>Save & Upload Document</span>
                            </button>
                        </div>
                    </form>
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
                        Access to the seller center has been locked due to an ongoing compliance review. If you believe this is in error, please reach out to <a href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}" class="font-bold text-amber-700 hover:text-amber-800 transition-colors">{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}</a>.
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
                <a x-show="currentState === 'suspended'" href="mailto:{{ \App\Models\SiteSetting::get('contact_email', 'support@bazaario.in') }}" 
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
