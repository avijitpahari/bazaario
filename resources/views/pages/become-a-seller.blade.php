<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Become a Seller — Bazaario Marketplace</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body {
            background-color: #FFFDF8;
            background-image: 
                radial-gradient(circle at 12% 10%, rgba(245, 166, 35, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 88% 18%, rgba(15, 23, 42, 0.03) 0%, transparent 40%);
            background-attachment: fixed;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col font-sans text-slate-800 antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- Include Header Component -->
    @include('components.nav-user')

    <!-- Main Content Wrapper -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 pt-28 pb-16 w-full">
        
        <!-- Hero Header -->
        <section class="text-center max-w-3xl mx-auto mb-16 pt-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>BAZAARIO SELLER PARTNER NETWORK</span>
            </div>
            
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                Grow your business on India's <span class="text-amber-600 underline decoration-amber-400/40 underline-offset-4">Verified Escrow</span> Marketplace
            </h1>
            
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-sans max-w-2xl mx-auto">
                Reach thousands of active shoppers, run sub-second live auctions, and receive guaranteed payouts protected by Bazaario Escrow.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-full font-bold text-sm transition-all shadow-lg hover:shadow-xl active:scale-95 flex items-center justify-center gap-2">
                    <span>Start Selling Today</span>
                    <span class="material-symbols-outlined text-lg text-amber-400">arrow_forward</span>
                </a>
                <a href="{{ route('docs.fees-and-commission') }}" class="w-full sm:w-auto px-6 py-3.5 bg-white border border-slate-200 text-slate-700 hover:text-slate-900 hover:border-slate-300 rounded-full font-bold text-sm transition-all shadow-xs text-center">
                    View Fee Schedule
                </a>
            </div>
        </section>

        <!-- Key Seller Advantages (3 Cards) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-card-elevated relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">100% Guaranteed Escrow</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Zero payment defaults or chargebacks. Buyer funds are locked securely in Bazaario Escrow prior to dispatch and released automatically upon delivery.
                </p>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-card-elevated relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 text-indigo-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">gavel</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">Live Sub-Second Auctions</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Clear surplus inventory or auction rare collectibles with real-time WebSocket bidding engine designed to maximize item valuation.
                </p>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-card-elevated relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center mb-5 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined text-2xl">auto_awesome</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">AI Smart Matching</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Our autonomous recommendation engine matches your products directly with high-intent buyers based on search behavior and category preferences.
                </p>
            </div>
        </section>

        <!-- 4-Step Onboarding Roadmap -->
        <section class="bg-white rounded-3xl p-6 sm:p-12 border border-slate-200/90 shadow-xl mb-20">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="font-mono text-xs font-bold uppercase tracking-wider text-amber-600">SIMPLE ONBOARDING PROCESS</span>
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 mt-1">
                    How to Become a Verified Seller in 4 Easy Steps
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
                <!-- Step 1 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-14 h-14 rounded-full bg-slate-900 text-amber-400 font-display font-extrabold text-lg flex items-center justify-center mb-4 shadow-md ring-4 ring-slate-100">
                        01
                    </div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-1.5">Register Account</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Sign up with your official mobile number & email. Select <strong>Seller Account</strong> during registration.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-14 h-14 rounded-full bg-slate-900 text-amber-400 font-display font-extrabold text-lg flex items-center justify-center mb-4 shadow-md ring-4 ring-slate-100">
                        02
                    </div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-1.5">KYC Verification</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Submit GSTIN, PAN, and business verification details in your Seller Dashboard for instant validation.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-14 h-14 rounded-full bg-slate-900 text-amber-400 font-display font-extrabold text-lg flex items-center justify-center mb-4 shadow-md ring-4 ring-slate-100">
                        03
                    </div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-1.5">Setup Store Profile</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Add your shop logo, store description, return policies, and bank account details for direct payouts.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center text-center relative">
                    <div class="w-14 h-14 rounded-full bg-amber-500 text-slate-950 font-display font-extrabold text-lg flex items-center justify-center mb-4 shadow-md ring-4 ring-amber-100">
                        04
                    </div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-1.5">List & Start Earning</h4>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Add products or launch live auctions. Fulfill orders and receive automated T+2 bank payouts.
                    </p>
                </div>
            </div>
        </section>

        <!-- Requirements Checklist -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-2xl relative overflow-hidden">
            <div>
                <span class="font-mono text-xs font-bold uppercase tracking-wider text-amber-400">WHAT YOU NEED TO START</span>
                <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-white mt-2 mb-4">
                    Seller Eligibility & Checklist
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed mb-6">
                    We maintain high marketplace trust standards. Ensure you have the following ready prior to onboarding:
                </p>

                <ul class="space-y-3 text-xs font-medium text-slate-200">
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-base">check_circle</span>
                        <span>Active GSTIN & PAN card (for GST registered businesses)</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-base">check_circle</span>
                        <span>Active Bank Account in India (matching business name for payouts)</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-base">check_circle</span>
                        <span>Verified Mobile Number & Business Email Address</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-amber-400 text-base">check_circle</span>
                        <span>High-quality product images & authentic product descriptions</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white/10 backdrop-blur-md p-6 sm:p-8 rounded-2xl border border-white/10 text-center flex flex-col items-center">
                <span class="material-symbols-outlined text-4xl text-amber-400 mb-3">storefront</span>
                <h3 class="font-display font-bold text-lg text-white mb-2">Ready to become a Bazaario Seller?</h3>
                <p class="text-xs text-slate-300 mb-6 max-w-xs">
                    Join over 4,000+ verified sellers growing their business with 0 monthly subscription fees.
                </p>
                <a href="{{ route('register') }}" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-xl shadow-lg transition-colors text-center">
                    Register as Seller Now
                </a>
            </div>
        </section>

    </main>

    <!-- Global Footer -->
    @include('components.footer')

</body>
</html>
