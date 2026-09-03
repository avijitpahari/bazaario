@extends('layouts.admin')

@section('title', 'Seller Approvals & Compliance Queue — Bazaario HQ')
@section('page-title', 'Stall Approval & Merchant Verification Queue')

@section('content')
<div class="space-y-6" x-data="{
    search: '',
    categoryFilter: 'all',
    docModal: false,
    rejectModal: false,
    selectedApp: null,
    rejectReason: 'Incomplete business registration documentation',
    toastMsg: '',
    showToast: false,
    applications: [
        { 
            id: 1, 
            shop: 'Heritage Wood & Iron', 
            owner: 'Marcus Vance', 
            email: 'marcus@heritagewood.com', 
            phone: '+1 (555) 890-1234',
            category: 'Artisan & Crafts', 
            date: 'Today, 09:30 AM', 
            taxId: 'US-EIN-99482104',
            risk: 'low',
            origin: 'Stockholm, Sweden',
            bio: 'Handcrafted rustic wood tables, live-edge shelving and custom forged hardware.',
            sampleProducts: 12,
            status: 'pending' 
        },
        { 
            id: 2, 
            shop: 'Aura Botanical Perfumes', 
            owner: 'Claire Delacroix', 
            email: 'claire@aurabotanicals.com', 
            phone: '+33 6 12 34 56 78',
            category: 'Gourmet & Organics', 
            date: 'Yesterday, 04:12 PM', 
            taxId: 'FR-TVA-44829104',
            risk: 'low',
            origin: 'Grasse, France',
            bio: 'Organic small-batch botanical fragrances and artisan essential oil distillations.',
            sampleProducts: 8,
            status: 'pending' 
        },
        { 
            id: 3, 
            shop: 'Zenith Audio Gear', 
            owner: 'Alexandre Meyer', 
            email: 'alex@zenithaudio.io', 
            phone: '+49 30 9948201',
            category: 'Electronics & Audio', 
            date: 'Sep 01, 2026', 
            taxId: 'DE-UST-88492019',
            risk: 'medium',
            origin: 'Berlin, Germany',
            bio: 'Custom braided audiophile headphone cables and balanced DAC amplifiers.',
            sampleProducts: 15,
            status: 'pending' 
        },
        { 
            id: 4, 
            shop: 'Ceramic Sanctuary', 
            owner: 'Kaito Tanaka', 
            email: 'kaito@ceramicsanctuary.jp', 
            phone: '+81 3 5555 0192',
            category: 'Home & Living', 
            date: 'Aug 30, 2026', 
            taxId: 'JP-CORP-0049281',
            risk: 'low',
            origin: 'Kyoto, Japan',
            bio: 'Hand-thrown stoneware ceramics, matcha bowls, and wabi-sabi home decor.',
            sampleProducts: 24,
            status: 'pending' 
        }
    ],
    approve(app) {
        app.status = 'approved';
        this.docModal = false;
        this.toastMsg = 'Stall ' + app.shop + ' has been APPROVED! Merchant notified and dashboard unlocked.';
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
    },
    confirmReject() {
        if (!this.selectedApp) return;
        this.selectedApp.status = 'rejected';
        this.rejectModal = false;
        this.docModal = false;
        this.toastMsg = 'Application for ' + this.selectedApp.shop + ' was REJECTED. Reason: ' + this.rejectReason;
        this.showToast = true;
        setTimeout(() => this.showToast = false, 3500);
    },
    openInspector(app) {
        this.selectedApp = app;
        this.docModal = true;
    },
    openReject(app) {
        this.selectedApp = app;
        this.rejectModal = true;
    }
}">

    <!-- Toast Notification -->
    <div x-show="showToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 text-xs font-bold">
        <i class="fa-solid fa-shield-check text-emerald-400 text-base"></i>
        <span x-text="toastMsg"></span>
    </div>

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold font-heading text-white">Merchant Application Queue</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold font-mono bg-amber-500/20 text-amber-300 border border-amber-500/30" x-text="applications.filter(a => a.status === 'pending').length + ' Pending'"></span>
            </div>
            <p class="text-xs text-slate-400 mt-1">Review business tax credentials, verify artisan authenticity, and gate marketplace access.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3 py-1.5 rounded-xl font-mono">
                <i class="fa-solid fa-bolt"></i> SLA: 4 Hours
            </span>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="categoryFilter = 'all'" :class="categoryFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">All Taxonomies</button>
            <button @click="categoryFilter = 'Artisan & Crafts'" :class="categoryFilter === 'Artisan & Crafts' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Artisan & Crafts</button>
            <button @click="categoryFilter = 'Electronics & Audio'" :class="categoryFilter === 'Electronics & Audio' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Electronics</button>
            <button @click="categoryFilter = 'Gourmet & Organics'" :class="categoryFilter === 'Gourmet & Organics' ? 'bg-indigo-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white'" class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition">Organics</button>
        </div>

        <div class="relative w-full sm:w-64">
            <i class="fa-solid fa-magnifying-glass text-slate-500 absolute left-3 top-3 text-xs"></i>
            <input type="text" x-model="search" placeholder="Search applicant, stall, tax ID..." class="w-full py-2 pl-8 pr-3 text-xs rounded-xl border border-slate-800 bg-slate-950 text-white focus:outline-none focus:border-indigo-500">
        </div>
    </div>

    <!-- Application Queue Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4">Stall / Merchant Profile</th>
                        <th class="py-3.5 px-4">Applicant & Contact</th>
                        <th class="py-3.5 px-4">Taxonomy Category</th>
                        <th class="py-3.5 px-4">Business Tax ID</th>
                        <th class="py-3.5 px-4">Risk Assessment</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Governance Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-medium">
                    <template x-for="app in applications" :key="app.id">
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center font-bold text-sm">
                                        <i class="fa-solid fa-store"></i>
                                    </div>
                                    <div>
                                        <span class="font-bold text-white text-sm block" x-text="app.shop"></span>
                                        <span class="text-[10px] text-slate-400" x-text="app.origin + ' • ' + app.sampleProducts + ' initial listings'"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-200 block" x-text="app.owner"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="app.email"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="app.category"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-mono text-indigo-400 font-bold" x-text="app.taxId"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span x-show="app.risk === 'low'" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                    <i class="fa-solid fa-shield-check"></i> Low Risk
                                </span>
                                <span x-show="app.risk === 'medium'" class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                                    <i class="fa-solid fa-circle-exclamation"></i> Verify Docs
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span x-show="app.status === 'pending'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Pending Review</span>
                                <span x-show="app.status === 'approved'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Approved</span>
                                <span x-show="app.status === 'rejected'" class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Rejected</span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div x-show="app.status === 'pending'" class="flex items-center justify-end gap-2">
                                    <button @click="openInspector(app)" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs transition flex items-center gap-1 border border-slate-700">
                                        <i class="fa-regular fa-id-card"></i> Inspect
                                    </button>
                                    <button @click="approve(app)" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition flex items-center gap-1">
                                        <i class="fa-solid fa-check"></i> Approve
                                    </button>
                                    <button @click="openReject(app)" class="p-1.5 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white transition" title="Reject Application">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                                <span x-show="app.status !== 'pending'" class="text-[11px] text-slate-500 italic">Decision Recorded</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DOCUMENT & STALL INSPECTOR MODAL -->
    <div x-show="docModal" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="docModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-2xl w-full shadow-2xl space-y-6 text-xs text-slate-300" x-show="selectedApp">
            
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold font-heading text-white" x-text="selectedApp?.shop"></h3>
                        <p class="text-slate-400 text-[11px]">Submitted by <strong class="text-white" x-text="selectedApp?.owner"></strong> (<span x-text="selectedApp?.email"></span>)</p>
                    </div>
                </div>
                <button @click="docModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <!-- Detailed Specs Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block">Business Tax ID / EIN</span>
                    <span class="font-mono text-sm font-bold text-indigo-400" x-text="selectedApp?.taxId"></span>
                    <span class="text-[10px] text-emerald-400 block font-semibold"><i class="fa-solid fa-circle-check"></i> Formatted & Validated</span>
                </div>

                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block">Physical Workshop Location</span>
                    <span class="text-sm font-bold text-white" x-text="selectedApp?.origin"></span>
                    <span class="text-[10px] text-slate-400 block">Phone: <span x-text="selectedApp?.phone"></span></span>
                </div>

                <div class="sm:col-span-2 p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px] block">Stall Bio & Artisan Manifesto</span>
                    <p class="text-xs text-slate-200 leading-relaxed" x-text="selectedApp?.bio"></p>
                </div>
            </div>

            <!-- Documents Preview Block -->
            <div class="p-4 rounded-2xl bg-indigo-950/30 border border-indigo-800/40 space-y-2">
                <div class="flex items-center justify-between text-indigo-300 font-bold">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-file-pdf"></i> Business_Registration_Cert.pdf</span>
                    <span class="text-[10px] text-emerald-400"><i class="fa-solid fa-check"></i> Scanned Clean (0 flags)</span>
                </div>
                <div class="flex items-center justify-between text-indigo-300 font-bold pt-1 border-t border-indigo-900/50">
                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-image"></i> Workshop_Proof_Photos.zip</span>
                    <span class="text-[10px] text-slate-400">4 photos attached</span>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="pt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-800">
                <button @click="openReject(selectedApp)" class="px-4 py-2.5 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-bold transition">
                    Reject Application...
                </button>
                <div class="flex gap-2">
                    <button @click="docModal = false" class="px-4 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 font-bold">Close</button>
                    <button @click="approve(selectedApp)" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-lg shadow-emerald-600/25 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> Approve Stall & Unlock Dashboard
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- REJECTION REASON MODAL -->
    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.outside="rejectModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5 text-xs text-slate-300">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <h3 class="text-base font-bold font-heading text-rose-400">Reject Stall Application</h3>
                <button @click="rejectModal = false" class="text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <p>Please specify why the application for <strong class="text-white" x-text="selectedApp?.shop"></strong> cannot be approved:</p>

            <div class="space-y-2">
                <label class="block p-3 rounded-xl border border-slate-800 bg-slate-950 cursor-pointer flex items-center gap-2">
                    <input type="radio" name="reason" value="Incomplete business registration documentation" x-model="rejectReason">
                    <span>Incomplete business registration documentation</span>
                </label>
                <label class="block p-3 rounded-xl border border-slate-800 bg-slate-950 cursor-pointer flex items-center gap-2">
                    <input type="radio" name="reason" value="Prohibited or copyrighted catalog items" x-model="rejectReason">
                    <span>Prohibited or copyrighted catalog items</span>
                </label>
                <label class="block p-3 rounded-xl border border-slate-800 bg-slate-950 cursor-pointer flex items-center gap-2">
                    <input type="radio" name="reason" value="Invalid Tax ID / Corporate Registration number" x-model="rejectReason">
                    <span>Invalid Tax ID / Corporate Registration number</span>
                </label>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-slate-800">
                <button @click="rejectModal = false" class="px-4 py-2 rounded-xl text-slate-400 hover:bg-slate-800">Cancel</button>
                <button @click="confirmReject()" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow">Send Rejection & Notify</button>
            </div>
        </div>
    </div>

</div>
@endsection
