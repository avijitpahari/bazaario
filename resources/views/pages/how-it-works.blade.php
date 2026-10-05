<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works — Bazaario Marketplace Documentation</title>
    
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
    @include('components.nav')

    <!-- Main Content Wrapper -->
    <main class="flex-1 max-w-6xl mx-auto px-4 sm:px-6 pt-28 pb-16 w-full">
        
        <!-- Hero Header -->
        <section class="text-center max-w-3xl mx-auto mb-16 pt-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>PLATFORM ARCHITECTURE &amp; DOCUMENTATION</span>
            </div>
            
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                How Bazaario Works: <span class="text-amber-600 underline decoration-amber-400/40 underline-offset-4">Fair, Hyperlocal, &amp; Escrow-Secured</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-sans max-w-2xl mx-auto">
                Discover authentic local artisans and merchants, participate in sub-second live auctions, and shop with complete confidence backed by 100% smart escrow protection.
            </p>
        </section>

        <!-- 3 Core Pillars -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:border-amber-500/50 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-2xl">near_me</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">Hyperlocal Discovery</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Filter independent stalls within 5 to 100 km using real-time spatial calculations. Support nearby farmers, kirana shops, and master creators in your neighborhood.
                </p>
            </div>

            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:border-emerald-500/50 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">100% Escrow Guarantee</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Your payments are held in verified escrow until you receive and inspect your items. Sellers receive fast automated payouts only when trade conditions are met.
                </p>
            </div>

            <div class="bg-white p-7 rounded-3xl border border-slate-200/80 shadow-xs hover:border-indigo-500/50 transition-all duration-300">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 text-indigo-600 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-2xl">gavel</span>
                </div>
                <h3 class="font-display font-bold text-slate-900 text-lg mb-2">Sub-Second Live Auctions</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Bid in real time on rare collectibles, electronics, and craft masterpieces with 0.2s latency, anti-sniping protection, and transparent hammer-down mechanics.
                </p>
            </div>
        </section>

        <!-- The Buyer Journey: 4 Steps -->
        <section class="mb-16">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <div class="font-mono text-[11px] uppercase tracking-wider text-amber-600 font-bold mb-1">
                    SEAMLESS SHOPPING EXPERIENCE
                </div>
                <h2 class="font-display font-bold text-2xl sm:text-3xl text-slate-900 tracking-tight">The Buyer Journey in 4 Steps</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs relative">
                    <div class="font-mono text-2xl font-extrabold text-amber-500/40 mb-3">01</div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-2">Browse &amp; Filter</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Search by keyword, filter by category, price bounds, seller ratings, and distance radius to discover the exact items you need.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs relative">
                    <div class="font-mono text-2xl font-extrabold text-amber-500/40 mb-3">02</div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-2">Multi-Seller Cart</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Add items from multiple independent merchants to a single cart. Items are automatically grouped by seller with itemized subtotals.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs relative">
                    <div class="font-mono text-2xl font-extrabold text-amber-500/40 mb-3">03</div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-2">Secure Checkout</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Select your verified delivery address, choose your delivery time window, and pay with Cash on Delivery (COD) or escrow-backed instant payment.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs relative">
                    <div class="font-mono text-2xl font-extrabold text-amber-500/40 mb-3">04</div>
                    <h4 class="font-display font-bold text-slate-900 text-base mb-2">Track &amp; Inspect</h4>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Track delivery telemetry per merchant. Review your order upon receipt with a 7-day buyer protection window before merchant funds release.
                    </p>
                </div>
            </div>
        </section>

        <!-- The Seller Advantage -->
        <section class="bg-slate-900 text-white rounded-3xl p-8 sm:p-12 mb-16 relative overflow-hidden">
            <div class="relative z-10 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-400 font-mono text-[10px] font-bold uppercase tracking-wider mb-4 border border-amber-400/30">
                    FOR MERCHANTS &amp; CREATORS
                </div>
                <h2 class="font-display font-extrabold text-2xl sm:text-4xl text-white tracking-tight mb-4">
                    Empowering Local Stalls with ₹0 Listing Fees
                </h2>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed mb-6">
                    Bazaario eliminates upfront store fees and restrictive contracts. List unlimited products for free, access high-converting buyers in your hyperlocal radius, and enjoy guaranteed automated bank payouts on a T+2 schedule.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('docs.become-a-seller') }}" class="px-6 py-3 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm transition-all duration-200 shadow-md">
                        Become a Verified Seller
                    </a>
                    <a href="{{ route('docs.fees-and-commission') }}" class="px-6 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm border border-white/20 transition-all duration-200">
                        View Transparent Fee Schedule
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="text-center py-6">
            <h3 class="font-display font-bold text-xl sm:text-2xl text-slate-900 mb-3">Ready to experience Bazaario?</h3>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-6">Explore thousands of curated items, support local businesses, and bid on unique finds.</p>
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-slate-900 hover:bg-amber-500 hover:text-slate-950 text-white font-bold text-xs sm:text-sm transition-all duration-200 shadow-md group">
                <span>Explore the Catalog</span>
                <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </a>
        </section>

    </main>

    <!-- Global Footer -->
    <x-footer />

</body>
</html>
