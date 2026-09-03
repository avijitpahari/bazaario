@extends('layouts.seller')

@section('title', 'Payouts & Stall Settings — Bazaario')
@section('page-title', 'Stall Financials & Settings')

@section('content')
<div class="space-y-8" x-data="{
    tab: 'payouts',
    shopName: 'Artisan & Timber Workshop',
    shopBio: 'Handcrafting bespoke vegetable-tanned leather goods & solid American walnut furniture in Stockholm since 2018.',
    payoutMethod: 'bank',
    savedToast: false
}">

    <!-- Toast -->
    <div x-show="savedToast" x-transition.duration.300ms x-cloak class="fixed bottom-6 right-6 z-50 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-slate-700 flex items-center gap-3 text-xs font-bold">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i> Settings updated successfully!
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-slate-200">
        <button @click="tab = 'payouts'" :class="tab === 'payouts' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-wallet"></i> Payouts & Commission Rate
        </button>
        <button @click="tab = 'settings'" :class="tab === 'settings' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'" class="py-3 px-6 border-b-2 text-sm transition flex items-center gap-2">
            <i class="fa-solid fa-sliders"></i> Stall Profile & Storefront Branding
        </button>
    </div>

    <!-- TAB 1: PAYOUTS & COMMISSIONS -->
    <div x-show="tab === 'payouts'" class="space-y-6">
        
        <!-- Transparent Commission Rate Banner (Section 3.6) -->
        <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-xl border border-slate-800">
            <div class="space-y-1">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-300 bg-amber-500/20 px-3 py-1 rounded-full border border-amber-500/30">
                    <i class="fa-solid fa-percent"></i> Transparent Marketplace Fee
                </span>
                <h3 class="text-xl font-bold font-heading text-white mt-2">Platform Commission Rate: 8.5%</h3>
                <p class="text-xs text-slate-300 max-w-xl">
                    Bazaario retains an 8.5% fee on completed sales to power customer acquisition, secure buyer escrow, and 24/7 server infrastructure. You retain 91.5% of every order.
                </p>
            </div>

            <div class="bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/20 text-center shrink-0 min-w-[200px]">
                <span class="text-xs text-slate-300 block">Available for Payout</span>
                <div class="text-2xl font-black font-heading text-emerald-400 mt-0.5">$3,480.00</div>
                <button class="mt-2 w-full py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition shadow">
                    Request Payout
                </button>
            </div>
        </div>

        <!-- Payout History Table -->
        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
            <h3 class="text-base font-bold font-heading text-slate-900">Historical Payout Records</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                        <tr>
                            <th class="py-3 px-4">Payout ID</th>
                            <th class="py-3 px-4">Period</th>
                            <th class="py-3 px-4">Gross Sales</th>
                            <th class="py-3 px-4">Fee (8.5%)</th>
                            <th class="py-3 px-4">Net Payout</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <tr>
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">PAY-8842</td>
                            <td class="py-3.5 px-4">Aug 15 - Aug 31, 2026</td>
                            <td class="py-3.5 px-4">$8,420.00</td>
                            <td class="py-3.5 px-4 text-rose-600">-$715.70</td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">$7,704.30</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Settled / Paid
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button class="p-1 text-indigo-600 hover:text-indigo-800 font-bold"><i class="fa-solid fa-download"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-4 font-bold text-slate-900 font-mono">PAY-8801</td>
                            <td class="py-3.5 px-4">Aug 01 - Aug 14, 2026</td>
                            <td class="py-3.5 px-4">$6,510.00</td>
                            <td class="py-3.5 px-4 text-rose-600">-$553.35</td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">$5,956.65</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Settled / Paid
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <button class="p-1 text-indigo-600 hover:text-indigo-800 font-bold"><i class="fa-solid fa-download"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- TAB 2: STALL PROFILE & SETTINGS -->
    <div x-show="tab === 'settings'" x-cloak class="max-w-3xl bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <h3 class="text-lg font-bold font-heading text-slate-900 border-b border-slate-100 pb-3">Stall Public Profile & Brand Settings</h3>

        <form @submit.prevent="savedToast = true; setTimeout(() => savedToast = false, 3000)" class="space-y-4 text-xs">
            
            <div class="space-y-1.5">
                <label class="font-bold text-slate-700">Storefront Display Name</label>
                <input type="text" x-model="shopName" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 font-semibold">
            </div>

            <div class="space-y-1.5">
                <label class="font-bold text-slate-700">Stall Bio / Creator Manifesto</label>
                <textarea rows="3" x-model="shopBio" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 focus:outline-none focus:border-indigo-600 leading-relaxed"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Contact / Support Email</label>
                    <input type="email" value="support@artisantimber.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                </div>
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-700">Workshop Location / Dispatch Origin</label>
                    <input type="text" value="Stockholm, Sweden" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <h4 class="font-bold uppercase tracking-wider text-slate-400 mb-2">Payout Destination (Bank / Direct Deposit)</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <input type="text" placeholder="IBAN / Account Number" value="SE49 5000 0000 0549 1042 8821" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-mono">
                    <input type="text" placeholder="Bank Name / SWIFT" value="Nordea Bank (NMEASESS)" class="px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50">
                </div>
            </div>

            <div class="pt-6 flex justify-end">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-md transition">
                    Save Stall Settings
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
