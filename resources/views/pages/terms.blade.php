<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service — Bazaario Marketplace</title>
    
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
    <main class="flex-1 max-w-4xl mx-auto px-4 sm:px-6 pt-28 pb-16 w-full">
        
        <!-- Header -->
        <div class="mb-12 text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                <span class="material-symbols-outlined text-sm text-amber-600">gavel</span>
                <span>TERMS &amp; AGREEMENTS</span>
            </div>
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                Terms of Service
            </h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto">
                Last updated: October 2026 • Governing Law: Jurisdiction of Contai, West Bengal, India
            </p>
        </div>

        <!-- Document Body -->
        <article class="bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-sm space-y-8 text-sm leading-relaxed text-slate-700">
            
            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">1</span>
                    Acceptance of Terms
                </h2>
                <p>
                    By accessing, browsing, registering on, or transacting through Bazaario (the "Platform"), you enter into a legally binding agreement governed by these Terms of Service. If you do not agree to these terms, you must refrain from using the platform immediately.
                </p>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">2</span>
                    Marketplace &amp; Escrow Framework
                </h2>
                <p class="mb-3">
                    Bazaario operates as a managed multi-vendor marketplace platform. All customer transactions are processed through an automated escrow mechanism:
                </p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600">
                    <li><strong class="text-slate-800">Escrow Lockup:</strong> Customer payments remain locked in a protected escrow holding account until the order is successfully marked delivered and verified by the customer.</li>
                    <li><strong class="text-slate-800">T+2 Settlement:</strong> Once confirmed, net funds (after platform commission) are released directly to the merchant's verified bank account via automated NEFT/IMPS.</li>
                    <li><strong class="text-slate-800">Inspection Window:</strong> Buyers are granted a 48-hour inspection period for perishable farm goods and 7 days for general artisanal merchandise to raise dispute claims.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">3</span>
                    Live Auction Floor Rules
                </h2>
                <p class="mb-3">
                    Bazaario hosts wholesale and commercial live auctions. Bidding participation requires compliance with the following:
                </p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600">
                    <li><strong class="text-slate-800">Binding Bids:</strong> Every bid submitted is legally binding. Placing a bid reserves the necessary escrow guarantee.</li>
                    <li><strong class="text-slate-800">Anti-Sniping Protection:</strong> Bids placed within the final 60 seconds of a live lot automatically extend the auction clock by an additional 120 seconds.</li>
                    <li><strong class="text-slate-800">Reserve Prices:</strong> Sellers may establish confidential reserve prices. If bidding finishes below the reserve price, the seller is not obligated to conclude the sale.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">4</span>
                    Merchant Obligations &amp; Quality SLAs
                </h2>
                <p>
                    All approved sellers (Farmers, Kirana Stores, Dark Stores, and Independent Artisans) agree to uphold strict standards:
                </p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600 mt-2">
                    <li>Ensure perishable listings display genuine harvest dates and harvest-fresh expiry telemetry.</li>
                    <li>Fulfill dispatches within the assigned time slot, maintaining a minimum 95% on-time fulfillment score.</li>
                    <li>Honor platform returns and damage claims arbitrated by the Dispute Desk.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">5</span>
                    Dispute Mediation &amp; Arbitration
                </h2>
                <p>
                    In the event of an unresolvable transaction dispute between buyer and merchant, the matter is submitted to the Bazaario Mediation Desk. Platform arbitrations are final and binding, triggering automated escrow refunds or payout releases according to courier transit telemetry and photographic inspection evidence.
                </p>
            </section>

        </article>

        <!-- Navigation helper -->
        <div class="mt-8 flex justify-between items-center text-xs font-mono text-slate-500">
            <a href="{{ route('pages.privacy') }}" class="hover:text-amber-600 underline">&larr; Privacy Policy</a>
            <a href="{{ route('pages.return-policy') }}" class="hover:text-amber-600 underline">Return &amp; Refund Policy &rarr;</a>
        </div>

    </main>

    <!-- Global Footer -->
    <x-footer />

</body>
</html>
