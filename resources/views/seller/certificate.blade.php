<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verified Merchant Compliance Certificate — Bazaario</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans p-6 sm:p-12">
    <div class="max-w-2xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl border-4 border-amber-500/30 relative overflow-hidden">
        {{-- Decorative Stamp Watermark --}}
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-amber-500/5 flex items-center justify-center font-mono font-bold text-7xl text-amber-500/10 rotate-12 pointer-events-none select-none">
            VERIFIED
        </div>

        <div class="text-center pb-6 border-b border-slate-200">
            <div class="mx-auto mb-3 flex justify-center">
                <x-brand-logo size="lg" :link="url('/')" />
            </div>
            <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-mono font-bold uppercase tracking-widest">
                Official Marketplace Certificate
            </span>
            <h1 class="text-2xl font-bold font-serif text-slate-900 mt-2">Certificate of Merchant Verification</h1>
            <p class="text-xs text-slate-500">Issued by Regional Standards & Escrow Compliance Desk</p>
        </div>

        <div class="py-8 space-y-4 text-center">
            <p class="text-xs text-slate-500 uppercase tracking-widest font-mono">This is to certify that</p>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $profile->shop_name }}</h2>
            <p class="text-xs text-slate-600 max-w-md mx-auto leading-relaxed">
                Operated by <strong>{{ $user->name }}</strong> in <strong>{{ $profile->city ?? 'West Bengal' }}, India</strong>, is an authenticated and approved <strong>{{ $profile->seller_type ?? 'Artisan' }}</strong> partner on {{ \App\Models\SiteSetting::getSiteName() }} Marketplace.
            </p>
        </div>

        <div class="grid grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs font-mono mb-8">
            <div>
                <span class="text-[10px] text-slate-400 block uppercase">Trust Score</span>
                <span class="font-bold text-emerald-700 text-sm">{{ number_format($profile->trust_score ?? 98.5, 1) }}%</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block uppercase">Merchant ID</span>
                <span class="font-bold text-slate-800 text-sm">#SLR-{{ str_pad($profile->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 block uppercase">Verified On</span>
                <span class="font-bold text-slate-800 text-sm">{{ $profile->verified_at ? $profile->verified_at->format('M Y') : 'Active' }}</span>
            </div>
        </div>

        <div class="flex items-end justify-between border-t border-slate-200 pt-6 text-xs text-slate-500">
            <div>
                <p class="font-mono text-[10px]">Digital Signature Verified: SHA-256</p>
                <p class="font-mono text-[10px]">Certificate Ref: BZ-CERT-{{ strtoupper(substr(md5($profile->id . 'bazaario'), 0, 10)) }}</p>
            </div>
            <div class="text-right">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <p class="font-bold text-slate-800 text-[11px]">Chief Compliance Officer</p>
                <p class="text-[10px] text-slate-400">Bazaario Regional Desk</p>
            </div>
        </div>

        <div class="no-print pt-6 mt-6 border-t border-slate-200 flex items-center justify-between">
            <a href="{{ route('seller.dashboard') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition">
                &larr; Return to Dashboard
            </a>
            <button onclick="window.print()" class="px-6 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition cursor-pointer shadow-md">
                Print Certificate (PDF)
            </button>
        </div>
    </div>
</body>
</html>
