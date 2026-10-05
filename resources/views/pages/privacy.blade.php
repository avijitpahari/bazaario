<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy — Bazaario Marketplace</title>
    
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
                <span class="material-symbols-outlined text-sm text-amber-600">shield</span>
                <span>LEGAL &amp; COMPLIANCE</span>
            </div>
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                Privacy Policy
            </h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto">
                Last updated: October 2026 • Effective Date: January 1, 2026
            </p>
        </div>

        <!-- Document Body -->
        <article class="bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-sm space-y-8 text-sm leading-relaxed text-slate-700">
            
            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">1</span>
                    Overview &amp; Scope
                </h2>
                <p>
                    Bazaario ("we", "our", or "us") operates the hyperlocal, AI-powered e-commerce marketplace platform connecting verified local merchants, farmers, and artisans with retail and commercial buyers. We are deeply committed to protecting your personal data, privacy, and transactional confidentiality in strict compliance with the Information Technology Act, 2000, Digital Personal Data Protection Act (DPDPA), and global data protection standards.
                </p>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">2</span>
                    Information We Collect
                </h2>
                <p class="mb-3">To facilitate secure transactions, order fulfillment, escrow protection, and hyperlocal discovery, we collect the following categories of data:</p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600">
                    <li><strong class="text-slate-800">Account Credentials:</strong> Name, verified mobile phone number (OTP-authenticated), email address, and encrypted passwords.</li>
                    <li><strong class="text-slate-800">Merchant Telemetry:</strong> Business name, GSTIN, PAN, shop coordinates (latitude/longitude), and bank account payout details.</li>
                    <li><strong class="text-slate-800">Delivery Telemetry:</strong> Delivery addresses, geographical coordinates, chosen delivery slots, and courier handoff signatures.</li>
                    <li><strong class="text-slate-800">Transaction &amp; Escrow Records:</strong> Payment gateway transaction IDs, order breakdowns, auction bids, and escrow settlement status.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">3</span>
                    How We Use Your Data
                </h2>
                <p class="mb-3">We process your information exclusively for legitimate marketplace operations:</p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600">
                    <li>Processing, routing, and fulfilling multi-seller consignment orders.</li>
                    <li>Calculating spatial proximity for Hyperlocal Stalls within customizable radius filters.</li>
                    <li>Managing sub-second auction bidding ledgers and anti-sniping protection timers.</li>
                    <li>Executing escrow settlements and batch NEFT merchant payouts.</li>
                    <li>Arbitrating disputes via our AI-assisted mediation triage desk.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">4</span>
                    Data Sharing &amp; Third Parties
                </h2>
                <p>
                    We never sell, lease, or monetize your personal identity. Data is shared strictly on a need-to-know basis with:
                </p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600 mt-2">
                    <li><strong class="text-slate-800">Fulfillment Partners:</strong> Assigned merchants and verified couriers receive only delivery names, addresses, and contact numbers.</li>
                    <li><strong class="text-slate-800">Financial Gateways:</strong> RBI-licensed payment aggregators and escrow banking partners receive encrypted payload signatures.</li>
                    <li><strong class="text-slate-800">Statutory Authorities:</strong> Disclosed only when strictly mandated under lawful judicial process.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-700 font-mono text-xs flex items-center justify-center font-bold">5</span>
                    Your Rights &amp; Data Control
                </h2>
                <p>
                    You retain complete autonomy over your data on Bazaario. You may at any time inspect, correct, export, or request deletion of your account and stored delivery addresses directly from your User Account Dashboard or by contacting our Data Protection Officer at <code class="font-mono bg-slate-100 px-1.5 py-0.5 rounded text-amber-700">privacy@bazaario.in</code>, Bazaario Technologies, Contai, West Bengal, India.
                </p>
            </section>

        </article>

        <!-- Navigation helper -->
        <div class="mt-8 flex justify-between items-center text-xs font-mono text-slate-500">
            <a href="{{ route('pages.terms') }}" class="hover:text-amber-600 underline">Terms of Service &rarr;</a>
            <a href="{{ route('pages.return-policy') }}" class="hover:text-amber-600 underline">Return &amp; Refund Policy &rarr;</a>
        </div>

    </main>

    <!-- Global Footer -->
    <x-footer />

</body>
</html>
