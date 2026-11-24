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
            $providedDocs = $profile ? $profile->getProvidedDocumentsList(false) : [];
            $allDocsCatalog = $profile ? $profile->getProvidedDocumentsList(true) : [];
            if (empty($providedDocs) && !empty($allDocsCatalog)) {
                $providedDocs = [
                    'pan'  => $allDocsCatalog['pan'],
                    'bank' => $allDocsCatalog['bank'],
                ];
            }
            $totalUploaded = collect($providedDocs)->filter(fn($d) => $d['has_file'])->count();
            $totalProvided = count($providedDocs);
            $hasAnyCredential = collect($providedDocs)->contains(fn($d) => $d['has_number'] || $d['has_file']);
            $initialDocType = array_key_first($providedDocs) ?: 'pan';
        @endphp

        <!-- VERIFICATION & COMPLIANCE DOCUMENTS DOSSIER -->
        <div class="mb-8" x-data="{ uploadModalOpen: false, activeDocType: '{{ $initialDocType }}', addMenuOpen: false }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div>
                    <h4 class="text-xs font-heading font-bold text-brand-slate uppercase tracking-wider">Required Compliance Dossier & Verification Documents</h4>
                    <p class="text-[11px] text-brand-muted">Attach official PAN, escrow bank account, and trade permits for administrative audit.</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-mono font-bold {{ $totalUploaded >= 2 ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                        <span class="w-2 h-2 rounded-full {{ $totalUploaded >= 2 ? 'bg-brand-green' : 'bg-brand-amber animate-pulse' }}"></span>
                        {{ $totalUploaded }}/{{ $totalProvided }} Proofs Attached
                    </span>

                    <!-- Dropdown selector to add / update documents -->
                    <div class="relative" @click.outside="addMenuOpen = false">
                        <button type="button" @click="addMenuOpen = !addMenuOpen" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-slate text-white text-[11px] font-heading font-bold hover:bg-slate-800 transition shadow-xs">
                            <span class="material-symbols-outlined text-[15px] text-amber-400">add_circle</span>
                            <span>Choose Documents to Provide</span>
                            <span class="material-symbols-outlined text-[13px] text-slate-300 transition-transform" :class="addMenuOpen ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="addMenuOpen" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-brand-outline py-2 z-30">
                            <div class="px-3 py-1 text-[10px] font-mono font-bold text-brand-muted uppercase tracking-wider border-b border-brand-outline/60 mb-1">
                                Available Compliance Documents
                            </div>
                            @foreach($allDocsCatalog as $cKey => $cDoc)
                                <button type="button" 
                                        @click="activeDocType = '{{ $cKey }}'; uploadModalOpen = true; addMenuOpen = false;"
                                        class="w-full text-left px-3 py-2 text-xs flex items-center justify-between hover:bg-amber-50/70 transition">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px] text-brand-slate">{{ $cDoc['icon'] ?? 'description' }}</span>
                                        <div>
                                            <p class="font-heading font-bold text-brand-slate text-[11px] leading-tight">{{ $cDoc['label'] }}</p>
                                            <p class="text-[10px] text-brand-muted font-mono">{{ $cDoc['badge'] }}</p>
                                        </div>
                                    </div>
                                    @if($cDoc['has_file'])
                                        <span class="text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">Attached</span>
                                    @elseif($cDoc['has_number'])
                                        <span class="text-[10px] font-mono font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">File Needed</span>
                                    @else
                                        <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded">+ Add</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Grid of Provided Documents -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                @forelse($providedDocs as $docKey => $doc)
                    <div class="bg-white border rounded-[14px] p-5 shadow-sm transition flex flex-col justify-between {{ $doc['has_file'] ? 'border-emerald-200 hover:border-emerald-300' : ($doc['has_number'] ? 'border-amber-200 hover:border-amber-300' : 'border-slate-200/80 bg-slate-50/40') }}">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-mono text-[10px] text-brand-muted uppercase font-bold tracking-wider">DOC {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-[10px] font-mono px-2 py-0.5 rounded-full font-bold
                                        @if($doc['badge_color'] === 'emerald') bg-emerald-50 text-emerald-800 border border-emerald-200
                                        @elseif($doc['badge_color'] === 'amber') bg-amber-50 text-amber-800 border border-amber-200
                                        @elseif($doc['badge_color'] === 'blue') bg-blue-50 text-blue-800 border border-blue-200
                                        @elseif($doc['badge_color'] === 'purple') bg-purple-50 text-purple-800 border border-purple-200
                                        @elseif($doc['badge_color'] === 'indigo') bg-indigo-50 text-indigo-800 border border-indigo-200
                                        @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                        {{ $doc['badge'] }}
                                    </span>
                                    @if(!empty($doc['is_mandatory']))
                                        <span class="text-[9px] font-mono px-1.5 py-0.2 rounded bg-amber-100 text-amber-900 font-bold uppercase tracking-wide">Required</span>
                                    @endif
                                </div>
                                <button type="button" @click="activeDocType = '{{ $docKey }}'; uploadModalOpen = true;" 
                                        class="px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-brand-slate text-[11px] font-mono font-bold flex items-center gap-1 transition border border-amber-200/70 shadow-xs" title="Upload or Update {{ $doc['short_label'] }}">
                                    <span class="material-symbols-outlined text-[15px] text-amber-800">upload_file</span>
                                    <span>{{ $doc['has_file'] ? 'Update File' : 'Upload Proof' }}</span>
                                </button>
                            </div>

                            <div class="flex items-start gap-2.5 mt-2">
                                <div class="w-8 h-8 rounded-lg {{ $doc['has_file'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }} flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-base">{{ $doc['icon'] ?? 'description' }}</span>
                                </div>
                                <div>
                                    <h3 class="font-heading font-bold text-sm text-brand-slate">{{ $doc['label'] }}</h3>
                                    <p class="font-mono text-xs font-bold text-slate-800 mt-0.5">
                                        @if($doc['has_number'])
                                            {{ $doc['number_label'] }}: {{ $doc['number'] }}
                                            @if($doc['secondary'])
                                                <span class="text-brand-muted font-normal text-[11px]">({{ $doc['secondary'] }})</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 font-normal italic">Identifier not entered yet</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($doc['has_file'])
                                <div class="mt-3">
                                    <a href="{{ Storage::url($doc['file_path']) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-mono font-bold text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200 hover:bg-emerald-100 transition shadow-2xs">
                                        <span class="material-symbols-outlined text-[15px] text-emerald-600">visibility</span>
                                        <span>View Uploaded Proof {{ $doc['is_pdf'] ? '(PDF)' : '(Image)' }}</span>
                                        <span class="material-symbols-outlined text-[13px] text-emerald-500">open_in_new</span>
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between font-mono text-xs">
                            <span class="text-[11px] {{ $doc['has_file'] ? 'text-emerald-700 font-semibold' : ($doc['has_number'] ? 'text-amber-700 font-semibold' : 'text-slate-400') }}">
                                @if($doc['has_file'])
                                    ✓ Proof Attached
                                @elseif($doc['has_number'])
                                    ⏳ Awaiting Proof File
                                @else
                                    ○ Pending Input
                                @endif
                            </span>
                            <div>
                                @if($doc['has_file'])
                                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Attached
                                    </span>
                                @else
                                    <button type="button" @click="activeDocType = '{{ $docKey }}'; uploadModalOpen = true;" class="group inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 font-bold text-[11px] border border-amber-200/70 shadow-2xs hover:shadow-xs transition-all">
                                        <span>Upload Now</span>
                                        <span class="material-symbols-outlined text-[13px] text-amber-600 transition-transform duration-200 ease-out group-hover:translate-x-0.5">arrow_forward</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-8 text-center bg-slate-50 border border-dashed border-slate-300 rounded-xl">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">folder_off</span>
                        <p class="text-xs font-bold text-slate-700">No documents in packet yet</p>
                        <p class="text-[11px] text-slate-500 mt-1">Use the "Choose Documents to Provide" dropdown above to attach PAN, Bank, or License proofs.</p>
                    </div>
                @endforelse
            </div>

            <!-- DOCUMENT UPLOAD MODAL WITH DROPDOWN SELECTOR -->
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
                                <h3 class="text-base font-heading font-bold text-brand-slate">
                                    Upload / Update Verification Document
                                </h3>
                                <p class="text-[11px] text-brand-muted">Attach PDF or image proof (max 5MB)</p>
                            </div>
                        </div>
                        <button type="button" @click="uploadModalOpen = false" class="text-brand-muted hover:text-brand-slate p-1 rounded-lg">
                            <span class="material-symbols-outlined text-xl">close</span>
                        </button>
                    </div>

                    <form action="{{ route('seller.pending.documents.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <!-- Dropdown to choose which document is being uploaded/updated -->
                        <div>
                            <label class="block text-xs font-bold text-brand-slate mb-1">Select Document Type</label>
                            <select x-model="activeDocType" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-heading font-semibold text-brand-slate focus:outline-none focus:border-brand-amber">
                                <option value="pan">📌 Permanent Account Number (PAN Card) [Tax Compliance]</option>
                                <option value="bank">📌 Bank Account & Cheque / Passbook Proof [Escrow Payouts]</option>
                                <option value="gstin">📄 GST Registration Certificate [Indirect Tax]</option>
                                <option value="trade_license">📄 Municipality Trade License [Local Body]</option>
                                <option value="fssai">📄 FSSAI Food Safety / GI License [Agro & Food]</option>
                                <option value="aadhaar">📄 Aadhaar Card / Government Photo ID [Identity]</option>
                                <option value="mandi_license">📄 APMC / Mandi Market Trading License [Auctions]</option>
                                <option value="udyam">📄 Udyam / MSME Registration Certificate [Enterprise]</option>
                            </select>
                        </div>

                        <input type="hidden" name="doc_type" :value="activeDocType">

                        <!-- Dynamic Inputs based on activeDocType -->
                        <div x-show="activeDocType === 'pan'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">PAN Card Number (10 Alphanumeric characters) *</label>
                                <input type="text" name="pan_number" value="{{ $profile?->pan_number }}" maxlength="10" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. ABCDE1234F" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                                <p class="text-[10px] text-brand-muted mt-0.5">Permanent Account Number issued by Income Tax Department of India.</p>
                            </div>
                        </div>

                        <div x-show="activeDocType === 'bank'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Bank Account Number *</label>
                                <input type="text" name="bank_account_number" value="{{ $profile?->bank_account_number }}" maxlength="50" oninput="this.value = this.value.replace(/[^0-9A-Za-z]/g, '')" placeholder="e.g. 30987456123" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Bank IFSC Code (11 characters) *</label>
                                <input type="text" name="bank_ifsc" value="{{ $profile?->bank_ifsc }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. SBIN0000058" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'gstin'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">GSTIN Registration Number</label>
                                <input type="text" name="gstin" value="{{ $profile?->gstin }}" maxlength="20" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '')" placeholder="e.g. 19AAAAA0000A1Z5" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono uppercase focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'trade_license'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Trade License Registration Number</label>
                                <input type="text" name="trade_license_number" value="{{ $profile?->trade_license_number }}" maxlength="50" placeholder="e.g. TL-2026-98745" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'fssai'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">FSSAI License / GI Registration Number</label>
                                <input type="text" name="fssai_number" value="{{ $profile?->fssai_number }}" maxlength="30" placeholder="e.g. 10020031000123" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'aadhaar'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Aadhaar Card / Government Photo ID Number</label>
                                <input type="text" name="aadhaar_number" value="{{ $profile?->aadhaar_number }}" maxlength="30" placeholder="e.g. 1234 5678 9012" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'mandi_license'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">APMC / Mandi License Number</label>
                                <input type="text" name="mandi_license_number" value="{{ $profile?->mandi_license_number }}" maxlength="50" placeholder="e.g. APMC-WB-2026-4401" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <div x-show="activeDocType === 'udyam'" class="space-y-3">
                            <div>
                                <label class="block text-xs font-bold text-brand-slate mb-1">Udyam / MSME Registration Number</label>
                                <input type="text" name="udyam_number" value="{{ $profile?->udyam_number }}" maxlength="50" placeholder="e.g. UDYAM-WB-00-1234567" class="w-full px-3 py-2 bg-brand-subtle border border-brand-outline rounded-lg text-xs font-mono focus:outline-none focus:border-brand-amber">
                            </div>
                        </div>

                        <!-- File Input -->
                        <div>
                            <label class="block text-xs font-bold text-brand-slate mb-1">Attach Document Proof File (PDF / PNG / JPG / WEBP)</label>
                            <input type="file" name="document_file" accept=".pdf,.png,.jpg,.jpeg,.webp" class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-brand-amber file:text-brand-slate hover:file:bg-amber-400 cursor-pointer">
                            <p class="text-[10px] text-brand-muted mt-1">Accepts clear photo or scanned copy up to 5MB.</p>
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
