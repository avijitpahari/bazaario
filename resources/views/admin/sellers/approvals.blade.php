@extends('layouts.admin')

@section('title', 'Merchant KYC & Verification Queue')

@section('content')
<div x-data="{
    showRejectModal: false,
    showApproveModal: false,
    showDocViewer: false,
    previewDocUrl: '',
    previewDocTitle: '',
    previewDocIsPdf: false,
    rejectReason: '',
    copiedField: null,
    copyToClipboard(text, fieldName) {
        if (!text || text === 'N/A' || text === 'Pending' || text === 'Not Provided') return;
        navigator.clipboard.writeText(text);
        this.copiedField = fieldName;
        setTimeout(() => { this.copiedField = null; }, 2000);
    },
    openDoc(url, title) {
        if (!url) return;
        this.previewDocUrl = url;
        this.previewDocTitle = title;
        this.previewDocIsPdf = url.toLowerCase().includes('.pdf');
        this.showDocViewer = true;
    },
    setRejectPreset(preset) {
        this.rejectReason = preset;
    }
}" @keydown.escape.window="showRejectModal = false; showApproveModal = false; showDocViewer = false;" class="flex flex-col w-full gap-5">

    <!-- Unified Command Header & Toolbar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
        <!-- Top Row: Breadcrumbs, Title & Status Tabs -->
        <div class="p-5 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.sellers.index') }}" class="font-body-sm text-xs text-slate-500 hover:text-slate-800 transition-colors flex items-center gap-1 font-semibold">
                        <span class="material-symbols-outlined text-[16px]">storefront</span>
                        <span>Sellers</span>
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="font-label-sm text-xs text-amber-700 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px]">verified_user</span>
                        <span>KYC Verification Queue</span>
                    </span>
                </div>
                <h1 class="font-headline-lg text-2xl font-black text-[#0F172A] mt-1 tracking-tight flex items-center gap-2.5">
                    <span>Merchant KYC & Compliance Queue</span>
                    @if($pendingCount > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            {{ $pendingCount }} Pending Review
                        </span>
                    @endif
                </h1>
                <p class="font-body-md text-xs text-slate-500 mt-0.5">Inspect GSTIN documentation, trade licenses, bank accounts, and FSSAI certificates before granting active seller privileges.</p>
            </div>

            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-1 bg-slate-100/90 p-1.5 rounded-xl border border-slate-200/80 shrink-0">
                <a href="{{ route('admin.sellers.approvals', array_merge(request()->except('page', 'selected'), ['status' => 'pending'])) }}" 
                   class="px-3.5 py-2 rounded-lg font-bold text-xs transition-all flex items-center gap-1.5 {{ request('status', 'pending') === 'pending' ? 'bg-white text-[#0F172A] shadow-xs border border-slate-200 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <span class="material-symbols-outlined text-[16px] {{ request('status', 'pending') === 'pending' ? 'text-amber-500' : 'text-slate-400' }}">hourglass_top</span>
                    <span>Pending Review</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ request('status', 'pending') === 'pending' ? 'bg-amber-100 text-amber-900 font-bold' : 'bg-slate-200 text-slate-700' }}">{{ $pendingCount }}</span>
                </a>
                <a href="{{ route('admin.sellers.approvals', array_merge(request()->except('page', 'selected'), ['status' => 'approved'])) }}" 
                   class="px-3.5 py-2 rounded-lg font-bold text-xs transition-all flex items-center gap-1.5 {{ request('status') === 'approved' ? 'bg-white text-emerald-900 shadow-xs border border-slate-200 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <span class="material-symbols-outlined text-[16px] {{ request('status') === 'approved' ? 'text-emerald-600' : 'text-slate-400' }}">check_circle</span>
                    <span>Approved</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ request('status') === 'approved' ? 'bg-emerald-100 text-emerald-900 font-bold' : 'bg-slate-200 text-slate-700' }}">{{ $approvedCount }}</span>
                </a>
                <a href="{{ route('admin.sellers.approvals', array_merge(request()->except('page', 'selected'), ['status' => 'rejected'])) }}" 
                   class="px-3.5 py-2 rounded-lg font-bold text-xs transition-all flex items-center gap-1.5 {{ request('status') === 'rejected' ? 'bg-white text-rose-900 shadow-xs border border-slate-200 font-black' : 'text-slate-600 hover:text-slate-900 hover:bg-white/60' }}">
                    <span class="material-symbols-outlined text-[16px] {{ request('status') === 'rejected' ? 'text-rose-600' : 'text-slate-400' }}">cancel</span>
                    <span>Rejected</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] font-mono {{ request('status') === 'rejected' ? 'bg-rose-100 text-rose-900 font-bold' : 'bg-slate-200 text-slate-700' }}">{{ $rejectedCount }}</span>
                </a>
            </div>
        </div>

        <!-- Bottom Row: Search, Category Filter & Summary -->
        <div class="p-3.5 px-5 bg-slate-50/70 flex flex-col md:flex-row items-center justify-between gap-3">
            <form method="GET" action="{{ route('admin.sellers.approvals') }}" class="flex flex-col sm:flex-row items-center gap-2.5 w-full lg:w-auto flex-1">
                <input type="hidden" name="status" value="{{ request('status', 'pending') }}">
                
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[18px]">search</span>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Search shop, owner, phone, GSTIN..." 
                           class="w-full pl-9 pr-8 py-2 bg-white border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-amber-500 transition-all font-body-sm shadow-2xs">
                    @if(request('search'))
                        <a href="{{ route('admin.sellers.approvals', ['status' => request('status', 'pending')]) }}" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                            <span class="material-symbols-outlined text-[16px]">close</span>
                        </a>
                    @endif
                </div>

                <!-- Seller Classification Filter -->
                <div class="w-full sm:w-auto">
                    <select name="seller_type" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-[#0F172A] font-medium focus:outline-none focus:border-amber-500 cursor-pointer shadow-2xs">
                        <option value="">All Classifications</option>
                        <option value="Farmer / Producer" {{ request('seller_type') === 'Farmer / Producer' ? 'selected' : '' }}>🌾 Farmer / Producer</option>
                        <option value="Kirana Store" {{ request('seller_type') === 'Kirana Store' ? 'selected' : '' }}>🏪 Kirana Store</option>
                        <option value="Handloom Artisan" {{ request('seller_type') === 'Handloom Artisan' ? 'selected' : '' }}>🧵 Handloom Artisan</option>
                        <option value="Organic Producer" {{ request('seller_type') === 'Organic Producer' ? 'selected' : '' }}>🌱 Organic Producer</option>
                        <option value="D2C Brand" {{ request('seller_type') === 'D2C Brand' ? 'selected' : '' }}>🏷️ D2C Brand</option>
                        <option value="Manufacturer" {{ request('seller_type') === 'Manufacturer' ? 'selected' : '' }}>🏭 Manufacturer</option>
                        <option value="FPO / Cooperative" {{ request('seller_type') === 'FPO / Cooperative' ? 'selected' : '' }}>🤝 FPO / Cooperative</option>
                        <option value="Individual / Artisan" {{ request('seller_type') === 'Individual / Artisan' ? 'selected' : '' }}>🎨 Individual / Artisan</option>
                    </select>
                </div>

                <button type="submit" class="px-3.5 py-2 rounded-xl bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-bold transition shadow-2xs cursor-pointer flex items-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-[16px]">filter_list</span>
                    <span>Filter</span>
                </button>

                @if(request('search') || request('seller_type'))
                    <a href="{{ route('admin.sellers.approvals', ['status' => request('status', 'pending')]) }}" class="text-xs text-rose-600 hover:text-rose-800 font-semibold flex items-center gap-0.5 px-2 py-1">
                        <span class="material-symbols-outlined text-[14px]">restart_alt</span>
                        <span>Reset</span>
                    </a>
                @endif
            </form>

            <div class="text-[11px] text-slate-500 font-label-sm shrink-0 flex items-center gap-2">
                <span>Viewing: <strong class="text-[#0F172A] font-bold">{{ $pendingSellers->total() }}</strong> records</span>
                <span class="text-slate-300">•</span>
                <a href="{{ route('admin.sellers.index') }}" class="text-amber-700 hover:underline font-bold flex items-center gap-1">
                    <span>Full Directory</span>
                    <span class="material-symbols-outlined text-[13px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <span class="material-symbols-outlined text-lg text-emerald-600">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2 shadow-2xs">
            <span class="material-symbols-outlined text-lg text-rose-600">error</span>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @php
        $activeSeller = $selectedSeller ?? ($pendingSellers->firstWhere('id', request('selected')) ?? $pendingSellers->first());
    @endphp

    @if($pendingSellers->isEmpty() && !$activeSeller)
        <!-- Empty State Container -->
        <div class="p-16 rounded-2xl bg-white border border-slate-200/90 text-center flex flex-col items-center justify-center shadow-2xs">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-4 border border-amber-200/60 shadow-2xs">
                @if(request('status') === 'approved')
                    <span class="material-symbols-outlined text-3xl text-emerald-600">check_circle</span>
                @elseif(request('status') === 'rejected')
                    <span class="material-symbols-outlined text-3xl text-rose-600">cancel</span>
                @else
                    <span class="material-symbols-outlined text-3xl text-amber-600">task_alt</span>
                @endif
            </div>

            @if(request('search') || request('seller_type'))
                <h3 class="font-headline-md text-lg font-bold text-[#0F172A]">No Matching Applications Found</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">We couldn't find any merchant records matching your search query or filters.</p>
                <a href="{{ route('admin.sellers.approvals', ['status' => request('status', 'pending')]) }}" class="mt-4 px-4 py-2 rounded-xl bg-[#0F172A] text-white text-xs font-semibold hover:bg-slate-800 transition">
                    Clear Filters
                </a>
            @elseif(request('status') === 'approved')
                <h3 class="font-headline-md text-lg font-bold text-[#0F172A]">No Approved Merchants in this Queue</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">There are no approved sellers matching this view.</p>
                <a href="{{ route('admin.sellers.index') }}" class="mt-4 px-4 py-2 rounded-xl bg-[#0F172A] text-white text-xs font-semibold hover:bg-slate-800 transition">
                    Go to Sellers Directory
                </a>
            @elseif(request('status') === 'rejected')
                <h3 class="font-headline-md text-lg font-bold text-[#0F172A]">No Rejected Applications</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">No seller onboarding applications are currently marked as rejected.</p>
                <a href="{{ route('admin.sellers.approvals', ['status' => 'pending']) }}" class="mt-4 px-4 py-2 rounded-xl bg-[#0F172A] text-white text-xs font-semibold hover:bg-slate-800 transition">
                    Check Pending Queue
                </a>
            @else
                <h3 class="font-headline-md text-lg font-bold text-[#0F172A]">All Clear! No Pending Applications</h3>
                <p class="text-xs text-slate-500 max-w-md mt-1">There are no seller onboarding applications currently awaiting KYC audit in this queue.</p>
                <a href="{{ route('admin.sellers.index') }}" class="mt-4 px-4 py-2 rounded-xl bg-[#0F172A] text-white text-xs font-semibold hover:bg-slate-800 transition">
                    Return to Sellers Directory
                </a>
            @endif
        </div>
    @else
        <!-- Main Workspace Grid (4 Cols Left Queue, 8 Cols Right Dossier) -->
        <div class="grid grid-cols-12 gap-5 items-start">
            
            <!-- Left Column: Queue List (4 cols) -->
            <div class="col-span-12 lg:col-span-4 flex flex-col gap-3">
                <div class="flex items-center justify-between px-1">
                    <span class="font-label-sm text-[11px] uppercase tracking-wider text-slate-500 font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-amber-600">queue</span>
                        <span>Applications Queue</span>
                    </span>
                    <span class="font-label-sm text-[11px] text-slate-700 font-bold bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                        {{ $pendingSellers->total() }} total
                    </span>
                </div>

                <div class="flex flex-col gap-2.5">
                    @foreach($pendingSellers as $seller)
                        @php 
                            $isActive = $activeSeller && $activeSeller->id === $seller->id; 
                            $sellerLogo = $seller->logo_path ? Storage::url($seller->logo_path) : null;
                            $docCount = ($seller->gstin_doc_path ? 1 : 0) + ($seller->trade_license_doc_path ? 1 : 0) + ($seller->bank_doc_path ? 1 : 0) + ($seller->fssai_doc_path ? 1 : 0);
                        @endphp
                        <a href="{{ request()->fullUrlWithQuery(['selected' => $seller->id]) }}" 
                           class="p-4 rounded-2xl bg-white border-2 transition-all block relative {{ $isActive ? 'border-amber-500 shadow-md ring-2 ring-amber-500/20 bg-amber-50/15' : 'border-slate-200/90 hover:border-slate-300 shadow-2xs hover:bg-slate-50/60' }}">
                            
                            <div class="flex items-start gap-3">
                                <!-- Storefront Avatar -->
                                @if($sellerLogo)
                                    <img src="{{ $sellerLogo }}" alt="{{ $seller->shop_name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 shrink-0 bg-slate-50 shadow-2xs">
                                @else
                                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 text-white font-black text-sm flex items-center justify-center shrink-0 shadow-2xs border border-amber-300">
                                        {{ strtoupper(mb_substr($seller->shop_name, 0, 1)) }}
                                    </div>
                                @endif

                                <div class="flex flex-col min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-1.5">
                                        <h3 class="font-bold text-xs text-[#0F172A] truncate tracking-tight">{{ $seller->shop_name }}</h3>
                                        <span class="shrink-0 px-2 py-0.5 rounded-full font-label-sm text-[9px] font-bold uppercase tracking-wider {{ $seller->status === 'approved' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : ($seller->status === 'rejected' ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-amber-100 text-amber-900 border border-amber-200') }}">
                                            {{ $seller->status }}
                                        </span>
                                    </div>

                                    <!-- Classification & City -->
                                    <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                        <span class="inline-flex items-center text-[10px] font-bold text-amber-900 bg-amber-50 px-1.5 py-0.2 rounded border border-amber-200/80">
                                            {{ $seller->seller_type ?: 'Kirana Store' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 flex items-center gap-0.5 truncate font-medium">
                                            <span class="material-symbols-outlined text-[13px] text-slate-400">location_on</span>
                                            {{ $seller->city ?? 'West Bengal' }}
                                        </span>
                                    </div>

                                    <!-- Registered Merchant Name -->
                                    <span class="text-[11px] text-slate-600 mt-1 truncate flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[13px] text-slate-400">person</span>
                                        <span class="font-medium">{{ $seller->user?->name ?? 'Merchant' }}</span>
                                        @if($seller->user?->phone)
                                            <span class="text-slate-300">•</span>
                                            <span class="font-mono text-[10px] text-slate-500">{{ $seller->user->phone }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Document Readiness & Date Row -->
                            <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-slate-100 text-[10px] text-slate-500 font-label-sm">
                                <div class="flex items-center gap-1">
                                    @if($docCount === 4)
                                        <span class="inline-flex items-center gap-0.5 text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            <span class="material-symbols-outlined text-[12px]">verified</span>
                                            4/4 Docs Complete
                                        </span>
                                    @elseif($docCount > 0)
                                        <span class="inline-flex items-center gap-0.5 text-amber-800 font-bold bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                            <span class="material-symbols-outlined text-[12px]">upload_file</span>
                                            {{ $docCount }}/4 Docs
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 text-slate-500 font-medium bg-slate-100 px-1.5 py-0.5 rounded">
                                            <span class="material-symbols-outlined text-[12px]">pending</span>
                                            No Docs Uploaded
                                        </span>
                                    @endif
                                </div>
                                <span class="font-medium text-slate-500 flex items-center gap-0.5">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">schedule</span>
                                    {{ $seller->created_at ? $seller->created_at->format('d M, Y') : 'Recent' }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($pendingSellers->hasPages())
                    <div class="pt-2 bg-white p-3 rounded-xl border border-slate-200/90 shadow-2xs">
                        {{ $pendingSellers->links() }}
                    </div>
                @endif
            </div>

            <!-- Right Column: Verification Dossier & Audit Console (8 cols) -->
            @if($activeSeller)
                @php
                    $activeLogo = $activeSeller->logo_path ? Storage::url($activeSeller->logo_path) : null;
                    $activeBanner = $activeSeller->banner_path ? Storage::url($activeSeller->banner_path) : null;
                    $docCountActive = ($activeSeller->gstin_doc_path ? 1 : 0) + ($activeSeller->trade_license_doc_path ? 1 : 0) + ($activeSeller->bank_doc_path ? 1 : 0) + ($activeSeller->fssai_doc_path ? 1 : 0);
                @endphp
                <div class="col-span-12 lg:col-span-8 flex flex-col gap-5">
                    
                    <!-- Main Dossier Card -->
                    <div class="rounded-2xl bg-white border border-slate-200/90 shadow-2xs overflow-hidden flex flex-col">
                        
                        <!-- Top Accent Bar or Uploaded Storefront Banner -->
                        @if($activeBanner)
                            <div class="h-36 sm:h-44 w-full relative bg-slate-900 overflow-hidden cursor-pointer group" @click="openDoc('{{ $activeBanner }}', 'Storefront Banner: {{ addslashes($activeSeller->shop_name) }}')">
                                <img src="{{ $activeBanner }}" alt="{{ $activeSeller->shop_name }} Banner" class="w-full h-full object-cover opacity-90 group-hover:scale-102 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-xs text-white text-[10px] font-semibold border border-white/20 flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-[13px]">zoom_in</span>
                                        Storefront Banner Attached
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="h-2 w-full bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500"></div>
                        @endif

                        <!-- Clean Storefront Profile & Action Bar (Zero Overlap Layout) -->
                        <div class="p-6 border-b border-slate-100 bg-white">
                            <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-5">
                                
                                <!-- Left: Avatar + Titles + Badges -->
                                <div class="flex items-start gap-4 min-w-0">
                                    <!-- Store Avatar -->
                                    @if($activeLogo)
                                        <img src="{{ $activeLogo }}" 
                                             alt="{{ $activeSeller->shop_name }}" 
                                             @click="openDoc('{{ $activeLogo }}', 'Store Logo: {{ addslashes($activeSeller->shop_name) }}')"
                                             class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-2 border-slate-200 shadow-sm bg-white shrink-0 cursor-pointer hover:opacity-90 transition">
                                    @else
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-2 border-amber-300 shadow-sm bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 text-white font-black text-2xl sm:text-3xl flex items-center justify-center shrink-0">
                                            {{ strtoupper(mb_substr($activeSeller->shop_name, 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        <!-- Store Title & Badges -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h2 class="font-headline-md text-xl sm:text-2xl font-black text-[#0F172A] tracking-tight truncate">{{ $activeSeller->shop_name }}</h2>
                                            
                                            <!-- Seller Classification Pill -->
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-900 border border-amber-300 shrink-0">
                                                <span class="material-symbols-outlined text-[14px]">storefront</span>
                                                {{ $activeSeller->seller_type ?: 'Kirana Store' }}
                                            </span>

                                            <!-- Trust Score Badge -->
                                            @if((float)$activeSeller->trust_score > 0)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200/80 shrink-0">
                                                    <span class="material-symbols-outlined text-[14px]">verified</span>
                                                    Trust {{ number_format($activeSeller->trust_score, 1) }}%
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200 shrink-0">
                                                    <span class="material-symbols-outlined text-[14px]">info</span>
                                                    New Applicant · Unrated
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Metadata Row -->
                                        <div class="font-body-sm text-xs text-slate-500 mt-2 flex items-center gap-2 flex-wrap">
                                            <span>Merchant ID: <b class="font-mono text-[#0F172A] font-bold">#SLR-{{ str_pad($activeSeller->id, 4, '0', STR_PAD_LEFT) }}</b></span>
                                            <span class="text-slate-300">•</span>
                                            <span>Submitted: <b class="text-slate-700 font-semibold">{{ $activeSeller->created_at ? $activeSeller->created_at->format('d M, Y · h:i A') : 'N/A' }}</b></span>
                                            @if($activeSeller->verified_at)
                                                <span class="text-slate-300">•</span>
                                                <span class="text-emerald-700 font-bold">Verified {{ $activeSeller->verified_at->format('d M, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Interactive Action Buttons -->
                                <div class="flex items-center gap-2.5 shrink-0 self-start xl:self-center">
                                    @if($activeSeller->status === 'pending')
                                        <!-- Reject Trigger Button -->
                                        <button type="button" 
                                                @click="showRejectModal = true" 
                                                class="px-4 py-2.5 rounded-xl bg-white border border-rose-300 text-rose-700 hover:bg-rose-50 text-xs font-bold transition-all cursor-pointer flex items-center gap-1.5 shadow-2xs hover:border-rose-400">
                                            <span class="material-symbols-outlined text-[17px]">cancel</span>
                                            <span>Reject Application</span>
                                        </button>

                                        <!-- Approve Trigger Button -->
                                        <button type="button" 
                                                @click="showApproveModal = true" 
                                                class="px-4 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-[#0F172A] text-xs font-black transition-all shadow-xs flex items-center gap-1.5 cursor-pointer ring-1 ring-amber-400/50">
                                            <span class="material-symbols-outlined text-[17px]">verified</span>
                                            <span>Approve & Onboard</span>
                                        </button>
                                    @elseif($activeSeller->status === 'approved')
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-300 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                                <span class="material-symbols-outlined text-[17px] text-emerald-600">verified</span>
                                                <span>Approved & Active</span>
                                            </span>
                                            <a href="{{ route('admin.sellers.show', $activeSeller->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                                                <span class="material-symbols-outlined text-[16px]">visibility</span>
                                                <span>View Merchant</span>
                                            </a>
                                            <form method="POST" action="{{ route('admin.sellers.toggle-status', $activeSeller->id) }}" onsubmit="return confirm('Suspend this approved merchant from trading?');">
                                                @csrf
                                                <button type="submit" class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition cursor-pointer flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[15px]">block</span>
                                                    <span>Suspend</span>
                                                </button>
                                            </form>
                                        </div>
                                    @elseif($activeSeller->status === 'rejected')
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-800 border border-rose-300 text-xs font-bold flex items-center gap-1.5 shadow-2xs">
                                                <span class="material-symbols-outlined text-[17px] text-rose-600">cancel</span>
                                                <span>Application Rejected</span>
                                            </span>
                                            <button type="button" 
                                                    @click="showApproveModal = true" 
                                                    class="px-4 py-2 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-[#0F172A] text-xs font-bold transition flex items-center gap-1.5 cursor-pointer shadow-2xs">
                                                <span class="material-symbols-outlined text-[16px]">replay</span>
                                                <span>Re-evaluate & Approve</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Dossier Content Body -->
                        <div class="p-6 flex flex-col gap-5">
                            
                            <!-- Rejection Alert Callout if on record -->
                            @if($activeSeller->status === 'rejected' && $activeSeller->rejection_reason)
                                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs flex flex-col gap-1.5 shadow-2xs">
                                    <div class="font-bold flex items-center gap-1.5 text-rose-800 text-sm">
                                        <span class="material-symbols-outlined text-[18px]">report</span>
                                        <span>Official Audit Rejection Reason on Record</span>
                                    </div>
                                    <p class="font-body-sm text-slate-800 bg-white/80 p-3 rounded-lg border border-rose-200/80 leading-relaxed font-mono">
                                        "{{ $activeSeller->rejection_reason }}"
                                    </p>
                                </div>
                            @endif

                            <!-- Business Bio / Declaration Statement -->
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex flex-col gap-1.5">
                                <span class="font-label-sm text-[10px] uppercase font-bold text-slate-500 tracking-wider flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px] text-amber-600">format_quote</span>
                                    <span>Business Statement & Declaration</span>
                                </span>
                                <p class="font-body-sm text-xs text-slate-700 leading-relaxed italic">
                                    "{{ $activeSeller->bio ?: 'No additional business mission statement was provided by merchant during onboarding.' }}"
                                </p>
                            </div>

                            <!-- Merchant Profile Matrix: Identity & Location -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                
                                <!-- Block 1: Account Ownership & Identity -->
                                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 flex flex-col gap-3">
                                    <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                                        <span class="material-symbols-outlined text-amber-600 text-[18px]">badge</span>
                                        <h3 class="font-bold text-xs text-[#0F172A] uppercase tracking-wider font-label-sm">Account & Store Identity</h3>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Registered Owner</span>
                                            <span class="font-semibold text-slate-900 flex items-center gap-1">
                                                {{ $activeSeller->user?->name ?: 'N/A' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Classification</span>
                                            <span class="font-bold text-amber-900">{{ $activeSeller->seller_type ?: 'Kirana Store' }}</span>
                                        </div>
                                        <div class="col-span-2">
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Email Address</span>
                                            <div class="flex items-center gap-1 font-mono text-slate-800 text-[11px]">
                                                <span>{{ $activeSeller->user?->email ?: 'N/A' }}</span>
                                                @if($activeSeller->user?->email_verified_at)
                                                    <span class="material-symbols-outlined text-[14px] text-emerald-600" title="Email Verified">verified</span>
                                                @endif
                                                <button type="button" @click="copyToClipboard('{{ $activeSeller->user?->email }}', 'email')" class="text-slate-400 hover:text-slate-600 ml-1 p-0.5 cursor-pointer" title="Copy email">
                                                    <span class="material-symbols-outlined text-[13px]" x-text="copiedField === 'email' ? 'check' : 'content_copy'">content_copy</span>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Contact Phone</span>
                                            <div class="flex items-center gap-1 font-mono font-semibold text-slate-800">
                                                <span>{{ $activeSeller->user?->phone ?: 'Not provided' }}</span>
                                                @if($activeSeller->user?->phone)
                                                    <button type="button" @click="copyToClipboard('{{ $activeSeller->user?->phone }}', 'phone')" class="text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer" title="Copy phone">
                                                        <span class="material-symbols-outlined text-[13px]" x-text="copiedField === 'phone' ? 'check' : 'content_copy'">content_copy</span>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Store URL Slug</span>
                                            <span class="font-mono text-slate-600 text-[11px] truncate block">{{ $activeSeller->shop_slug ?: 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Block 2: Location & Logistics Corridor -->
                                <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-200/80 flex flex-col gap-3">
                                    <div class="flex items-center gap-2 pb-2 border-b border-slate-200/60">
                                        <span class="material-symbols-outlined text-amber-600 text-[18px]">local_shipping</span>
                                        <h3 class="font-bold text-xs text-[#0F172A] uppercase tracking-wider font-label-sm">Location & Logistics Corridor</h3>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-2.5 text-xs">
                                        <div class="col-span-2">
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Street Address</span>
                                            <span class="font-semibold text-slate-900 leading-snug">{{ $activeSeller->address ?: 'Not provided' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">City & State</span>
                                            <span class="font-semibold text-slate-800">{{ $activeSeller->city ?? 'N/A' }}, {{ $activeSeller->state ?? 'West Bengal' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Postal / PIN Code</span>
                                            <span class="font-mono font-bold text-slate-900">{{ $activeSeller->postal_code ?: '721401' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Operating Radius</span>
                                            <span class="font-semibold text-emerald-700 flex items-center gap-0.5">
                                                <span class="material-symbols-outlined text-[14px]">near_me</span>
                                                {{ $activeSeller->operating_radius_km ?? 25 }} km corridor
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-[10px] text-slate-400 uppercase font-bold block">GPS Coordinates</span>
                                            <div class="flex items-center gap-1">
                                                <span class="font-mono text-[10px] text-slate-600">
                                                    {{ $activeSeller->latitude ? number_format($activeSeller->latitude, 4) : '22.5726' }}, {{ $activeSeller->longitude ? number_format($activeSeller->longitude, 4) : '88.3638' }}
                                                </span>
                                                @if($activeSeller->latitude && $activeSeller->longitude)
                                                    <a href="https://www.google.com/maps?q={{ $activeSeller->latitude }},{{ $activeSeller->longitude }}" 
                                                       target="_blank" 
                                                       class="text-amber-700 hover:text-amber-900 p-0.5" 
                                                       title="View on Google Maps">
                                                        <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- The 4 KYC Compliance Regulatory Documents -->
                            <div>
                                <div class="flex items-center justify-between mb-3 px-1">
                                    <div class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-600 text-lg">folder_shared</span>
                                        <h3 class="font-headline-sm text-sm font-bold text-[#0F172A]">Statutory & Regulatory KYC Documents</h3>
                                    </div>
                                    <span class="text-[11px] font-bold {{ $docCountActive === 4 ? 'text-emerald-700' : 'text-amber-700' }} font-label-sm">
                                        {{ $docCountActive }}/4 Documents Attached
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    
                                    <!-- Document 01: GSTIN & PAN -->
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col justify-between min-h-[190px] shadow-2xs hover:border-slate-300 transition-colors">
                                        <div>
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold tracking-wider">Document 01</span>
                                                    <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">GSTIN Registration Certificate</h4>
                                                    
                                                    <div class="flex items-center gap-1.5 mt-1.5">
                                                        <span class="font-mono text-xs text-slate-900 font-bold bg-white px-2 py-0.5 rounded border border-slate-200/80 inline-block shadow-2xs">
                                                            GSTIN: {{ $activeSeller->gstin ?: 'Not Provided' }}
                                                        </span>
                                                        @if($activeSeller->gstin)
                                                            <button type="button" @click="copyToClipboard('{{ $activeSeller->gstin }}', 'gstin')" class="p-1 text-slate-400 hover:text-slate-700 rounded transition cursor-pointer" title="Copy GSTIN">
                                                                <span class="material-symbols-outlined text-[14px]" x-text="copiedField === 'gstin' ? 'check' : 'content_copy'">content_copy</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="material-symbols-outlined {{ $activeSeller->gstin_doc_path ? 'text-emerald-600' : ($activeSeller->gstin ? 'text-amber-500' : 'text-slate-400') }} text-2xl">
                                                    {{ $activeSeller->gstin_doc_path ? 'verified' : ($activeSeller->gstin ? 'edit_note' : 'pending') }}
                                                </span>
                                            </div>

                                            @if($activeSeller->gstin_doc_path)
                                                @php
                                                    $isGstinPdf = str_ends_with(strtolower($activeSeller->gstin_doc_path), '.pdf');
                                                @endphp
                                                <div class="mt-3 flex items-center gap-2 flex-wrap">
                                                    <button type="button" 
                                                            @click="openDoc('{{ Storage::url($activeSeller->gstin_doc_path) }}', 'GSTIN Certificate — {{ addslashes($activeSeller->shop_name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 text-xs font-bold transition shadow-2xs cursor-pointer">
                                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                                        <span>Inspect Uploaded Certificate</span>
                                                        <span class="text-[9px] uppercase font-mono px-1 py-0.2 bg-amber-200 rounded text-amber-950 font-bold">{{ $isGstinPdf ? 'PDF' : 'IMAGE' }}</span>
                                                    </button>
                                                    <a href="{{ Storage::url($activeSeller->gstin_doc_path) }}" target="_blank" class="text-slate-400 hover:text-slate-600 p-1" title="Open in new tab">
                                                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                                            <div class="flex items-center gap-1">
                                                <span class="{{ $activeSeller->pan_number ? 'text-slate-700 font-mono font-semibold' : 'text-slate-400' }} text-[11px]">
                                                    PAN: <b class="text-slate-900">{{ $activeSeller->pan_number ?: 'Not Provided' }}</b>
                                                </span>
                                                @if($activeSeller->pan_number)
                                                    <button type="button" @click="copyToClipboard('{{ $activeSeller->pan_number }}', 'pan')" class="text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer" title="Copy PAN">
                                                        <span class="material-symbols-outlined text-[13px]" x-text="copiedField === 'pan' ? 'check' : 'content_copy'">content_copy</span>
                                                    </button>
                                                @endif
                                            </div>

                                            @if($activeSeller->gstin_doc_path)
                                                <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[14px]">task_alt</span> File Attached
                                                </span>
                                            @elseif($activeSeller->gstin || $activeSeller->pan_number)
                                                <span class="text-amber-700 font-bold text-[11px] flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[14px]">check</span> Details Only
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Pending Upload</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Document 02: Municipality Trade License -->
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col justify-between min-h-[190px] shadow-2xs hover:border-slate-300 transition-colors">
                                        <div>
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold tracking-wider">Document 02</span>
                                                    <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">Municipality Trade License</h4>
                                                    
                                                    <div class="flex items-center gap-1.5 mt-1.5">
                                                        <span class="font-mono text-xs text-slate-900 font-bold bg-white px-2 py-0.5 rounded border border-slate-200/80 inline-block shadow-2xs">
                                                            Lic: {{ $activeSeller->trade_license_number ?: 'Not Provided' }}
                                                        </span>
                                                        @if($activeSeller->trade_license_number)
                                                            <button type="button" @click="copyToClipboard('{{ $activeSeller->trade_license_number }}', 'trade')" class="p-1 text-slate-400 hover:text-slate-700 rounded transition cursor-pointer" title="Copy Trade License">
                                                                <span class="material-symbols-outlined text-[14px]" x-text="copiedField === 'trade' ? 'check' : 'content_copy'">content_copy</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="material-symbols-outlined {{ $activeSeller->trade_license_doc_path ? 'text-emerald-600' : ($activeSeller->trade_license_number ? 'text-amber-500' : 'text-slate-400') }} text-2xl">
                                                    {{ $activeSeller->trade_license_doc_path ? 'verified' : ($activeSeller->trade_license_number ? 'edit_note' : 'pending') }}
                                                </span>
                                            </div>

                                            @if($activeSeller->trade_license_doc_path)
                                                @php
                                                    $isTradePdf = str_ends_with(strtolower($activeSeller->trade_license_doc_path), '.pdf');
                                                @endphp
                                                <div class="mt-3 flex items-center gap-2 flex-wrap">
                                                    <button type="button" 
                                                            @click="openDoc('{{ Storage::url($activeSeller->trade_license_doc_path) }}', 'Trade License — {{ addslashes($activeSeller->shop_name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 text-xs font-bold transition shadow-2xs cursor-pointer">
                                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                                        <span>Inspect Uploaded License</span>
                                                        <span class="text-[9px] uppercase font-mono px-1 py-0.2 bg-amber-200 rounded text-amber-950 font-bold">{{ $isTradePdf ? 'PDF' : 'IMAGE' }}</span>
                                                    </button>
                                                    <a href="{{ Storage::url($activeSeller->trade_license_doc_path) }}" target="_blank" class="text-slate-400 hover:text-slate-600 p-1" title="Open in new tab">
                                                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                                            <span class="text-slate-500 text-[11px]">Fiscal Year {{ now()->year }} / Local Urban Body</span>
                                            @if($activeSeller->trade_license_doc_path)
                                                <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[14px]">task_alt</span> File Attached
                                                </span>
                                            @elseif($activeSeller->trade_license_number)
                                                <span class="text-amber-700 font-bold text-[11px] flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[14px]">check</span> Details Provided
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Pending Verification</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Document 03: Escrow Bank Verification -->
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col justify-between min-h-[190px] shadow-2xs hover:border-slate-300 transition-colors">
                                        <div>
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold tracking-wider">Document 03</span>
                                                    <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">Escrow Bank Account Verification</h4>
                                                    
                                                    <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                                        <div class="flex items-center gap-1">
                                                            <span class="font-mono text-xs text-slate-900 font-bold bg-white px-2 py-0.5 rounded border border-slate-200/80 shadow-2xs">
                                                                IFSC: {{ $activeSeller->bank_ifsc ?: 'Not Provided' }}
                                                            </span>
                                                            @if($activeSeller->bank_ifsc)
                                                                <button type="button" @click="copyToClipboard('{{ $activeSeller->bank_ifsc }}', 'ifsc')" class="text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer" title="Copy IFSC">
                                                                    <span class="material-symbols-outlined text-[13px]" x-text="copiedField === 'ifsc' ? 'check' : 'content_copy'">content_copy</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <span class="font-mono text-xs text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200/80 shadow-2xs">
                                                                A/C: {{ $activeSeller->bank_account_number ?: 'Not Provided' }}
                                                            </span>
                                                            @if($activeSeller->bank_account_number)
                                                                <button type="button" @click="copyToClipboard('{{ $activeSeller->bank_account_number }}', 'acct')" class="text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer" title="Copy Account No">
                                                                    <span class="material-symbols-outlined text-[13px]" x-text="copiedField === 'acct' ? 'check' : 'content_copy'">content_copy</span>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="material-symbols-outlined {{ $activeSeller->bank_doc_path ? 'text-emerald-600' : ($activeSeller->bank_account_number ? 'text-amber-500' : 'text-slate-400') }} text-2xl">
                                                    {{ $activeSeller->bank_doc_path ? 'verified' : ($activeSeller->bank_account_number ? 'check_circle' : 'pending') }}
                                                </span>
                                            </div>

                                            @if($activeSeller->bank_doc_path)
                                                @php
                                                    $isBankPdf = str_ends_with(strtolower($activeSeller->bank_doc_path), '.pdf');
                                                @endphp
                                                <div class="mt-3 flex items-center gap-2 flex-wrap">
                                                    <button type="button" 
                                                            @click="openDoc('{{ Storage::url($activeSeller->bank_doc_path) }}', 'Bank Cheque / Passbook — {{ addslashes($activeSeller->shop_name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 text-xs font-bold transition shadow-2xs cursor-pointer">
                                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                                        <span>Inspect Cancelled Cheque / Passbook</span>
                                                        <span class="text-[9px] uppercase font-mono px-1 py-0.2 bg-amber-200 rounded text-amber-950 font-bold">{{ $isBankPdf ? 'PDF' : 'IMAGE' }}</span>
                                                    </button>
                                                    <a href="{{ Storage::url($activeSeller->bank_doc_path) }}" target="_blank" class="text-slate-400 hover:text-slate-600 p-1" title="Open in new tab">
                                                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                                            <span class="text-slate-600 text-[11px] font-label-sm flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px] text-slate-400">payments</span>
                                                NEFT / Escrow Payouts Ready
                                            </span>
                                            @if($activeSeller->bank_doc_path)
                                                <span class="text-emerald-700 font-bold text-[11px] flex items-center gap-0.5">
                                                    <span class="material-symbols-outlined text-[14px]">task_alt</span> File Attached
                                                </span>
                                            @elseif($activeSeller->bank_account_number)
                                                <span class="text-amber-700 font-semibold text-[11px]">Details Provided</span>
                                            @else
                                                <span class="text-slate-400 text-[11px]">Awaiting Bank Details</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Document 04: FSSAI / Food & Quality Certification -->
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/90 flex flex-col justify-between min-h-[190px] shadow-2xs hover:border-slate-300 transition-colors">
                                        <div>
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <span class="font-label-sm text-[10px] text-slate-400 uppercase font-bold tracking-wider">Document 04</span>
                                                    <h4 class="font-bold text-xs text-[#0F172A] mt-0.5">FSSAI / Food & Quality Certification</h4>
                                                    
                                                    <div class="flex items-center gap-1.5 mt-1.5">
                                                        <span class="font-mono text-xs text-slate-900 font-bold bg-white px-2 py-0.5 rounded border border-slate-200/80 inline-block shadow-2xs">
                                                            {{ $activeSeller->fssai_number ? 'FSSAI: ' . $activeSeller->fssai_number : 'Standard Clearance (Non-Perishable)' }}
                                                        </span>
                                                        @if($activeSeller->fssai_number)
                                                            <button type="button" @click="copyToClipboard('{{ $activeSeller->fssai_number }}', 'fssai')" class="p-1 text-slate-400 hover:text-slate-700 rounded transition cursor-pointer" title="Copy FSSAI">
                                                                <span class="material-symbols-outlined text-[14px]" x-text="copiedField === 'fssai' ? 'check' : 'content_copy'">content_copy</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                                <span class="material-symbols-outlined text-emerald-600 text-2xl">eco</span>
                                            </div>

                                            @if($activeSeller->fssai_doc_path)
                                                @php
                                                    $isFssaiPdf = str_ends_with(strtolower($activeSeller->fssai_doc_path), '.pdf');
                                                @endphp
                                                <div class="mt-3 flex items-center gap-2 flex-wrap">
                                                    <button type="button" 
                                                            @click="openDoc('{{ Storage::url($activeSeller->fssai_doc_path) }}', 'FSSAI Certificate — {{ addslashes($activeSeller->shop_name) }}')"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-950 text-xs font-bold transition shadow-2xs cursor-pointer">
                                                        <span class="material-symbols-outlined text-sm">visibility</span>
                                                        <span>Inspect FSSAI Certificate</span>
                                                        <span class="text-[9px] uppercase font-mono px-1 py-0.2 bg-amber-200 rounded text-amber-950 font-bold">{{ $isFssaiPdf ? 'PDF' : 'IMAGE' }}</span>
                                                    </button>
                                                    <a href="{{ Storage::url($activeSeller->fssai_doc_path) }}" target="_blank" class="text-slate-400 hover:text-slate-600 p-1" title="Open in new tab">
                                                        <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4 pt-2.5 border-t border-slate-200 flex items-center justify-between text-xs">
                                            <span class="text-emerald-700 font-semibold text-[11px] flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">gavel</span>
                                                Auction & Retail Eligible
                                            </span>
                                            <span class="text-slate-600 text-[11px] font-semibold">{{ $activeSeller->fssai_doc_path ? 'File Attached' : ($activeSeller->fssai_number ? 'Cleared' : 'Standard Exemption') }}</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Hub Allocation & Commission Form -->
                            <div class="p-4 rounded-xl bg-amber-50/70 border border-amber-200/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-xs text-[#0F172A] flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-amber-600 text-[16px]">percent</span>
                                        Platform Take-Rate & Regional Hub Settlement
                                    </h4>
                                    <p class="font-body-sm text-xs text-slate-600 mt-0.5">
                                        Assigned Logistics Hub: <span class="font-bold text-[#0F172A]">{{ $activeSeller->city ?? 'Regional' }} Corridor</span> • Coverage: <span class="font-bold text-[#0F172A]">{{ $activeSeller->operating_radius_km ?? 25 }} km</span>
                                    </p>
                                </div>
                                <form action="{{ route('admin.sellers.commission', $activeSeller->id) }}" method="POST" class="flex items-center gap-2 shrink-0">
                                    @csrf
                                    <label for="commission_rate" class="text-xs font-bold text-slate-700">Commission:</label>
                                    <div class="relative">
                                        <input type="number" 
                                               id="commission_rate" 
                                               step="0.1" 
                                               min="0" 
                                               max="100" 
                                               name="commission_rate" 
                                               value="{{ $activeSeller->commission_rate ?? 8.5 }}" 
                                               class="w-20 px-2 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-center text-[#0F172A] focus:outline-none focus:border-amber-500 shadow-2xs" />
                                        <span class="absolute right-2 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-500 pointer-events-none">%</span>
                                    </div>
                                    <button type="submit" class="px-3.5 py-1.5 bg-[#0F172A] hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition shadow-2xs cursor-pointer">
                                        Update Rate
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════ -->
                <!-- MODAL 1: REJECTION FEEDBACK MODAL                           -->
                <!-- ════════════════════════════════════════════════════════════ -->
                <div x-show="showRejectModal" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @click.self="showRejectModal = false">
                    <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-2xl overflow-hidden animate-fadeIn"
                         @click.stop>
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-rose-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shadow-2xs border border-rose-200">
                                    <span class="material-symbols-outlined text-xl">cancel</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-sm font-bold text-[#0F172A]">Reject Merchant Application</h3>
                                    <p class="text-[11px] text-slate-500 font-body-sm">{{ $activeSeller->shop_name }} (#SLR-{{ str_pad($activeSeller->id, 4, '0', STR_PAD_LEFT) }})</p>
                                </div>
                            </div>
                            <button type="button" @click="showRejectModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-white/80 cursor-pointer transition">
                                <span class="material-symbols-outlined text-xl">close</span>
                            </button>
                        </div>

                        <form action="{{ route('admin.sellers.reject', $activeSeller->id) }}" 
                              method="POST" 
                              class="p-5 flex flex-col gap-4" 
                              x-data="{ rejecting: false }" 
                              @submit="if(rejecting){ $event.preventDefault(); return; } rejecting = true;">
                            @csrf

                            <!-- Quick Preset Audit Reasons -->
                            <div>
                                <label class="block font-headline-sm text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">
                                    Quick Audit Presets (Click to autofill):
                                </label>
                                <div class="flex flex-wrap gap-1.5">
                                    <button type="button" 
                                            @click="setRejectPreset('GSTIN certificate is mismatched or does not match applicant legal business name.')" 
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 border border-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer">
                                        GSTIN Mismatch
                                    </button>
                                    <button type="button" 
                                            @click="setRejectPreset('Trade license document is illegible, expired, or not issued by competent authority.')" 
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 border border-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer">
                                        Expired Trade Lic.
                                    </button>
                                    <button type="button" 
                                            @click="setRejectPreset('Cancelled bank cheque / passbook is blurred or account name does not match merchant profile.')" 
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 border border-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer">
                                        Bank Passbook Illegible
                                    </button>
                                    <button type="button" 
                                            @click="setRejectPreset('Storefront photograph is unclear or does not display permanent physical business signage.')" 
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 border border-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer">
                                        Storefront Photo Missing
                                    </button>
                                    <button type="button" 
                                            @click="setRejectPreset('Valid FSSAI food hygiene registration certificate required before approval.')" 
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-rose-50 hover:text-rose-800 hover:border-rose-300 border border-slate-200 text-[11px] font-semibold text-slate-700 transition cursor-pointer">
                                        FSSAI Missing
                                    </button>
                                </div>
                            </div>

                            <!-- Reason Textarea -->
                            <div>
                                <label for="seller_rejection_reason" class="block font-headline-sm text-xs font-bold text-slate-800 mb-1.5">
                                    Official Audit Rejection Reason & Feedback *
                                </label>
                                <textarea 
                                    id="seller_rejection_reason"
                                    name="reason" 
                                    rows="4" 
                                    required 
                                    x-model="rejectReason"
                                    placeholder="Specify clear compliance defect or document error (e.g., GSTIN certificate illegible, trade license expired)..."
                                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs text-[#0F172A] focus:outline-none focus:border-rose-500 focus:bg-white transition-all font-body-sm resize-none"></textarea>
                                <p class="text-[10px] text-slate-500 mt-1 font-body-sm">This reason will be recorded in compliance audit logs and communicated directly to the merchant.</p>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                                <button type="button" 
                                        @click="showRejectModal = false" 
                                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        :disabled="rejecting || !rejectReason.trim()" 
                                        class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span x-show="!rejecting" class="material-symbols-outlined text-[17px]">cancel</span>
                                    <span x-show="rejecting" x-cloak><x-loading-spinner size="16px" class="text-white" /></span>
                                    <span x-text="rejecting ? 'Rejecting...' : 'Confirm Rejection'">Confirm Rejection</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════ -->
                <!-- MODAL 2: APPROVAL CONFIRMATION MODAL                        -->
                <!-- ════════════════════════════════════════════════════════════ -->
                <div x-show="showApproveModal" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
                     @click.self="showApproveModal = false">
                    <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-2xl overflow-hidden animate-fadeIn"
                         @click.stop>
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-amber-50/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shadow-2xs border border-amber-200">
                                    <span class="material-symbols-outlined text-xl">verified</span>
                                </div>
                                <div>
                                    <h3 class="font-headline-sm text-sm font-bold text-[#0F172A]">Approve Merchant KYC</h3>
                                    <p class="text-[11px] text-slate-500 font-body-sm">{{ $activeSeller->shop_name }}</p>
                                </div>
                            </div>
                            <button type="button" @click="showApproveModal = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-white/80 cursor-pointer transition">
                                <span class="material-symbols-outlined text-xl">close</span>
                            </button>
                        </div>

                        <form action="{{ route('admin.sellers.approve', $activeSeller->id) }}" 
                              method="POST" 
                              class="p-5 flex flex-col gap-4" 
                              x-data="{ approving: false }" 
                              @submit="if(approving){ $event.preventDefault(); return; } approving = true;">
                            @csrf

                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200/80 flex flex-col gap-2 text-xs">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Merchant Store:</span>
                                    <span class="font-bold text-[#0F172A]">{{ $activeSeller->shop_name }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">GSTIN Registration:</span>
                                    <span class="font-mono font-bold text-[#0F172A]">{{ $activeSeller->gstin ?: 'None (Composite)' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Platform Commission:</span>
                                    <span class="font-bold text-amber-900">{{ $activeSeller->commission_rate ?? 8.5 }}%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500">Documents Attached:</span>
                                    <span class="font-bold {{ $docCountActive === 4 ? 'text-emerald-700' : 'text-amber-700' }}">{{ $docCountActive }}/4 Statutory Files</span>
                                </div>
                            </div>

                            @if(!$activeSeller->hasMandatoryComplianceDocuments())
                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-900 flex items-start gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">notification_important</span>
                                <div class="leading-relaxed">
                                    <strong>Document Grace Period:</strong> Mandatory proof documents are not yet uploaded. Approving will activate this merchant immediately and display a prominent <strong>2-week compliance notice</strong> in their seller portal to upload Bank &amp; PAN documents.
                                </div>
                            </div>
                            @endif

                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-900 flex items-start gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-[18px] shrink-0 mt-0.5">info</span>
                                <div class="leading-relaxed">
                                    Upon approval, the merchant account will be activated, a KYC completion notification will be delivered, and they can publish products on Bazaario.
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                                <button type="button" 
                                        @click="showApproveModal = false" 
                                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-colors cursor-pointer">
                                    Cancel
                                </button>
                                <button type="submit" 
                                        :disabled="approving" 
                                        class="px-4 py-2.5 rounded-xl bg-[#F5A623] hover:bg-amber-400 text-[#0F172A] text-xs font-black transition-colors shadow-2xs flex items-center gap-1.5 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                    <span x-show="!approving" class="material-symbols-outlined text-[17px]">verified</span>
                                    <span x-show="approving" x-cloak><x-loading-spinner size="16px" /></span>
                                    <span x-text="approving ? 'Verifying & Onboarding...' : 'Confirm & Approve'">Confirm & Approve</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════ -->
                <!-- MODAL 3: IN-PAGE DOCUMENT VIEWER LIGHTBOX                   -->
                <!-- ════════════════════════════════════════════════════════════ -->
                <div x-show="showDocViewer" 
                     x-cloak 
                     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6"
                     @click.self="showDocViewer = false">
                    <div class="bg-white rounded-2xl max-w-4xl w-full border border-slate-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh] animate-fadeIn"
                         @click.stop>
                        
                        <!-- Modal Header -->
                        <div class="p-4 sm:px-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="material-symbols-outlined text-amber-600 text-xl shrink-0" x-text="previewDocIsPdf ? 'picture_as_pdf' : 'image'">picture_as_pdf</span>
                                <h3 class="font-headline-sm text-sm font-bold text-[#0F172A] truncate" x-text="previewDocTitle">Inspection Document</h3>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <a :href="previewDocUrl" 
                                   target="_blank" 
                                   class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold flex items-center gap-1 transition shadow-2xs">
                                    <span class="material-symbols-outlined text-[15px]">open_in_new</span>
                                    <span class="hidden sm:inline">Open in Tab</span>
                                </a>
                                <a :href="previewDocUrl" 
                                   download 
                                   class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold flex items-center gap-1 transition shadow-2xs">
                                    <span class="material-symbols-outlined text-[15px]">download</span>
                                    <span class="hidden sm:inline">Download</span>
                                </a>
                                <button type="button" 
                                        @click="showDocViewer = false" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-200 transition cursor-pointer">
                                    <span class="material-symbols-outlined text-xl">close</span>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-4 sm:p-6 bg-slate-100 flex-1 overflow-auto flex items-center justify-center min-h-[400px]">
                            <!-- PDF Renderer -->
                            <template x-if="previewDocIsPdf">
                                <iframe :src="previewDocUrl" class="w-full h-[65vh] rounded-xl border border-slate-300 shadow-sm bg-white"></iframe>
                            </template>

                            <!-- Image Renderer -->
                            <template x-if="!previewDocIsPdf">
                                <img :src="previewDocUrl" :alt="previewDocTitle" class="max-h-[65vh] w-auto max-w-full object-contain rounded-xl border border-slate-300 shadow-md bg-white">
                            </template>
                        </div>

                        <!-- Modal Footer -->
                        <div class="p-3 px-6 bg-white border-t border-slate-200 flex items-center justify-between text-xs text-slate-500 font-label-sm">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">security</span>
                                Verified Encrypted Bazaario Storage
                            </span>
                            <button type="button" @click="showDocViewer = false" class="px-4 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-bold transition">
                                Close Preview
                            </button>
                        </div>
                    </div>
                </div>

            @endif
        </div>
    @endif
</div>
@endsection
