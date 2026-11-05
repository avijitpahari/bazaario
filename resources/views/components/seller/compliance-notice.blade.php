@props(['profile' => null])

@php
    $profile = $profile ?? (Auth::guard('seller')->user() ?? Auth::user())?->sellerProfile;
@endphp

@if($profile && $profile->status === 'approved' && !$profile->hasMandatoryComplianceDocuments())
@php
    $isExpired = $profile->isComplianceGraceExpired();
    $daysRemaining = $profile->compliance_days_remaining;
    $hasBank = $profile->hasUploadedBank();
    $hasPan = $profile->hasUploadedPan();
@endphp

<div x-data="{ openUploadModal: false }" class="mb-6 w-full">
    <!-- Notice Banner -->
    <div class="relative overflow-hidden rounded-2xl border {{ $isExpired ? 'border-rose-400 bg-gradient-to-r from-rose-50 via-rose-100/60 to-amber-50 text-rose-950' : 'border-amber-400/90 bg-gradient-to-r from-amber-50 via-amber-100/50 to-orange-50 text-amber-950' }} p-4 sm:p-5 shadow-sm transition-all">
        <!-- Accent Glow Line -->
        <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $isExpired ? 'bg-rose-600' : 'bg-amber-500' }}"></div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pl-2 sm:pl-3">
            <!-- Left Info Block -->
            <div class="flex items-start gap-3.5">
                <div class="mt-0.5 w-10 h-10 rounded-xl {{ $isExpired ? 'bg-rose-200/80 text-rose-700 border border-rose-300' : 'bg-amber-200/80 text-amber-800 border border-amber-300' }} flex items-center justify-center shrink-0 shadow-2xs">
                    <span class="material-symbols-outlined text-[24px]">{{ $isExpired ? 'emergency_home' : 'warning' }}</span>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="font-heading font-black text-sm sm:text-base tracking-tight {{ $isExpired ? 'text-rose-950' : 'text-amber-950' }}">
                            {{ $isExpired ? 'Action Required: Mandatory Compliance Grace Period Expired' : 'Mandatory Action: Upload Bank & PAN Details (Within 2 Weeks)' }}
                        </h4>
                        
                        <!-- Countdown Badge -->
                        @if($isExpired)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-rose-600 text-white shadow-2xs animate-pulse">
                                <span class="material-symbols-outlined text-[13px]">alarm_off</span>
                                Overdue (2-Week Grace Ended)
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-mono font-bold bg-amber-200 text-amber-900 border border-amber-300">
                                <span class="material-symbols-outlined text-[13px]">schedule</span>
                                {{ $daysRemaining }} {{ \Illuminate\Support\Str::plural('day', $daysRemaining) }} remaining
                                @if($profile->compliance_deadline)
                                    (Deadline: {{ $profile->compliance_deadline->format('M d, Y') }})
                                @endif
                            </span>
                        @endif
                    </div>

                    <p class="mt-1 text-xs sm:text-sm text-slate-700 leading-relaxed max-w-3xl">
                        Your merchant account is approved, but government and banking regulations require you to submit your 
                        <strong class="font-bold text-slate-900">Bank Details (with Passbook / Cancelled Cheque proof)</strong> and 
                        <strong class="font-bold text-slate-900">PAN Card (Number & Document proof)</strong> within 2 weeks of approval. Both documents are mandatory to ensure uninterrupted payouts and compliance.
                    </p>

                    <!-- Document Status Checklist Chips -->
                    <div class="flex flex-wrap items-center gap-2 mt-3 pt-2 border-t {{ $isExpired ? 'border-rose-200/80' : 'border-amber-200/70' }}">
                        <!-- Bank Status Chip -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $hasBank ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-white/80 text-rose-900 border border-rose-300 shadow-2xs' }}">
                            <span class="material-symbols-outlined text-[16px] {{ $hasBank ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $hasBank ? 'check_circle' : 'cancel' }}
                            </span>
                            <span>Bank Account & Proof:</span>
                            <span class="font-bold">{{ $hasBank ? 'Uploaded & Verified' : 'Missing (Action Needed)' }}</span>
                        </div>

                        <!-- PAN Status Chip -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold {{ $hasPan ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-white/80 text-rose-900 border border-rose-300 shadow-2xs' }}">
                            <span class="material-symbols-outlined text-[16px] {{ $hasPan ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ $hasPan ? 'check_circle' : 'cancel' }}
                            </span>
                            <span>PAN Number & Proof:</span>
                            <span class="font-bold">{{ $hasPan ? 'Uploaded & Verified' : 'Missing (Action Needed)' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Action Trigger Button -->
            <div class="shrink-0 flex items-center lg:self-center pl-2 sm:pl-0">
                <button type="button" 
                        @click="openUploadModal = true"
                        class="w-full sm:w-auto px-5 py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm tracking-wide transition-all flex items-center justify-center gap-2 shadow-sm hover:scale-102 active:scale-98 cursor-pointer {{ $isExpired ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-600/30' : 'bg-amber-600 hover:bg-amber-700 text-white shadow-amber-600/30' }}">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    <span>Upload Bank & PAN Details</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Upload Modal Dialog -->
    <div x-cloak 
         x-show="openUploadModal" 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-4 overflow-y-auto"
         @keydown.escape.window="openUploadModal = false">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
             x-show="openUploadModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openUploadModal = false"></div>

        <!-- Modal Card -->
        <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 w-full max-w-2xl overflow-hidden z-10 transition-all max-h-[90vh] flex flex-col"
             x-show="openUploadModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
            
            <!-- Modal Header -->
            <div class="px-6 py-4.5 bg-gradient-to-r from-slate-900 to-slate-800 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-base text-white tracking-tight">Upload Mandatory Compliance Documents</h3>
                        <p class="text-[11px] text-slate-300">Submit Bank details and PAN card document within your 2-week grace period</p>
                    </div>
                </div>
                <button type="button" @click="openUploadModal = false" class="text-slate-400 hover:text-white transition-colors p-1 rounded-lg">
                    <span class="material-symbols-outlined text-[22px]">close</span>
                </button>
            </div>

            <!-- Modal Form Body -->
            <form action="{{ route('seller.account.compliance.upload') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-6">
                @csrf

                <!-- Section 1: Escrow Bank Account Details -->
                <div class="rounded-2xl border {{ $hasBank ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50/50' }} p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] {{ $hasBank ? 'text-emerald-600' : 'text-blue-600' }}">account_balance</span>
                            <h4 class="font-heading font-bold text-sm text-slate-900">1. Bank Account Details (Mandatory)</h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="
                                        $el.closest('form').querySelector('input[name=bank_account_number]').value = '918010023456789';
                                        $el.closest('form').querySelector('input[name=bank_ifsc]').value = 'HDFC0001234';
                                    "
                                    class="text-[10px] font-mono px-2 py-0.5 rounded font-bold uppercase bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 transition-colors flex items-center gap-1 cursor-pointer">
                                <span class="material-symbols-outlined text-[13px]">science</span>
                                <span>Auto-Fill Test Bank</span>
                            </button>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold uppercase {{ $hasBank ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $hasBank ? 'Verified / Complete' : 'Pending Upload' }}
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Required for automatic bi-weekly payout settlements and auction payouts.</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Account Number -->
                        <div>
                            <label class="block font-label-sm text-xs font-semibold text-slate-700 mb-1">Bank Account Number *</label>
                            <input type="text" 
                                   name="bank_account_number" 
                                   value="{{ old('bank_account_number', $profile->bank_account_number) }}" 
                                   placeholder="e.g. 123456789012" 
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <!-- Bank IFSC -->
                        <div>
                            <label class="block font-label-sm text-xs font-semibold text-slate-700 mb-1">Bank IFSC Code *</label>
                            <input type="text" 
                                   name="bank_ifsc" 
                                   value="{{ old('bank_ifsc', $profile->bank_ifsc) }}" 
                                   placeholder="e.g. SBIN0001234" 
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono uppercase focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <!-- Bank Proof Document -->
                        <div class="sm:col-span-2">
                            <label class="block font-label-sm text-xs font-semibold text-slate-700 mb-1">Cancelled Cheque or Bank Passbook Proof *</label>
                            <input type="file" 
                                   name="bank_doc" 
                                   accept=".pdf,.png,.jpg,.jpeg,.webp" 
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all border border-slate-200 rounded-xl bg-white p-1.5 cursor-pointer">
                            <span class="text-[11px] text-slate-400 mt-1 block">Accepted: PDF, PNG, JPG, WEBP (Max 5MB).</span>
                            @if($profile->bank_doc_path)
                                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                                    <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                    <span>Current document on file: <a href="{{ asset('storage/' . $profile->bank_doc_path) }}" target="_blank" class="underline font-bold hover:text-emerald-800">View Bank Proof</a> (Uploading a new file will replace this)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Section 2: PAN Card Details -->
                <div class="rounded-2xl border {{ $hasPan ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50/50' }} p-5">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] {{ $hasPan ? 'text-emerald-600' : 'text-blue-600' }}">badge</span>
                            <h4 class="font-heading font-bold text-sm text-slate-900">2. PAN Card Details (Mandatory)</h4>
                        </div>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded font-bold uppercase {{ $hasPan ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $hasPan ? 'Verified / Complete' : 'Pending Upload' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 mb-4">Required by Tax Authorities (IT Act / TDS deductions on marketplace sales).</p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- PAN Number -->
                        <div>
                            <label class="block font-label-sm text-xs font-semibold text-slate-700 mb-1">PAN Card Number (10 Alphanumeric) *</label>
                            <input type="text" 
                                   name="pan_number" 
                                   value="{{ old('pan_number', $profile->pan_number) }}" 
                                   placeholder="e.g. ABCDE1234F" 
                                   maxlength="10"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-mono uppercase focus:border-blue-500 focus:outline-none transition-all">
                        </div>

                        <!-- PAN Document -->
                        <div>
                            <label class="block font-label-sm text-xs font-semibold text-slate-700 mb-1">PAN Card Image or PDF *</label>
                            <input type="file" 
                                   name="pan_doc" 
                                   accept=".pdf,.png,.jpg,.jpeg,.webp" 
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all border border-slate-200 rounded-xl bg-white p-1.5 cursor-pointer">
                            <span class="text-[11px] text-slate-400 mt-1 block">Accepted: PDF, PNG, JPG, WEBP (Max 5MB).</span>
                        </div>

                        @if($profile->pan_doc_path)
                            <div class="sm:col-span-2 flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span>
                                <span>Current document on file: <a href="{{ asset('storage/' . $profile->pan_doc_path) }}" target="_blank" class="underline font-bold hover:text-emerald-800">View PAN Proof</a> (Uploading a new file will replace this)</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Form Action Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100">
                    <button type="submit" 
                            formaction="{{ route('seller.account.bank.bypass') }}"
                            formmethod="POST"
                            class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-sans text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer shadow-2xs hover:shadow-xs"
                            title="Auto-verify sandbox bank & compliance documents for testing">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">flash_on</span>
                        <span>⚡ Instant Test Bypass</span>
                    </button>

                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" 
                                @click="openUploadModal = false" 
                                class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-900 font-heading font-black text-xs tracking-wider shadow-md shadow-amber-500/20 transition-all flex items-center gap-2 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">cloud_upload</span>
                            <span>Submit Mandatory Documents</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
