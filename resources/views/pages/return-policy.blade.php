<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return &amp; Refund Policy — Bazaario Marketplace</title>
    
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
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                <span class="material-symbols-outlined text-sm text-emerald-600">published_with_changes</span>
                <span>BUYER ESCROW PROTECTION</span>
            </div>
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                Return &amp; Refund Policy
            </h1>
            <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto">
                Guaranteed zero-hassle returns backed by smart escrow safeguards and hyperlocal courier pickup.
            </p>
        </div>

        <!-- Document Body -->
        <article class="bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 p-6 sm:p-10 shadow-sm space-y-8 text-sm leading-relaxed text-slate-700">
            
            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-700 font-mono text-xs flex items-center justify-center font-bold">1</span>
                    Escrow-Backed Buyer Protection
                </h2>
                <p>
                    Every order on Bazaario is covered by our Smart Escrow Guarantee. When you complete checkout, your payment is held securely in escrow and is never released to the seller until you have received the order in satisfactory condition or your inspection window elapses.
                </p>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-700 font-mono text-xs flex items-center justify-center font-bold">2</span>
                    Return Windows &amp; Eligibility
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-4">
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200/60">
                        <div class="flex items-center gap-2 font-display font-bold text-amber-950 mb-1">
                            <span class="material-symbols-outlined text-amber-600">agriculture</span>
                            <span>Perishable &amp; Farm Goods</span>
                        </div>
                        <p class="text-xs text-amber-900/80 leading-normal">
                            <strong>48 Hours:</strong> Fruits, vegetables, dairy, grains, and perishables damaged in transit or failing freshness standards may be reported within 48 hours of delivery.
                        </p>
                    </div>
                    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200/60">
                        <div class="flex items-center gap-2 font-display font-bold text-blue-950 mb-1">
                            <span class="material-symbols-outlined text-blue-600">inventory_2</span>
                            <span>Handicrafts &amp; Non-Perishables</span>
                        </div>
                        <p class="text-xs text-blue-900/80 leading-normal">
                            <strong>7 Days:</strong> Handcrafted items, leather goods, textiles, and packaged products qualify for a 7-day return period if defective, wrong item, or damaged.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-700 font-mono text-xs flex items-center justify-center font-bold">3</span>
                    How to File a Return Claim
                </h2>
                <ol class="list-decimal pl-6 space-y-2 text-slate-600">
                    <li>Navigate to your Customer Account &rarr; <a href="{{ route('user.returns') }}" class="text-amber-600 font-medium underline">Returns &amp; Refunds</a> section.</li>
                    <li>Select the eligible order item and choose the return reason (e.g., Transit Damage, Freshness Issue, Wrong SKU).</li>
                    <li>Upload a clear photograph of the damaged or delivered merchandise.</li>
                    <li>Our automated dispute engine cross-checks courier delivery telemetry and issues an immediate return label or instant replacement dispatch.</li>
                </ol>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-700 font-mono text-xs flex items-center justify-center font-bold">4</span>
                    Refund Timelines &amp; Settlement
                </h2>
                <p>
                    Once a claim is approved or return pickup is verified by our courier partner:
                </p>
                <ul class="list-disc pl-6 space-y-1.5 text-slate-600 mt-2">
                    <li><strong class="text-slate-800">Online Payments (UPI / Cards / Net Banking):</strong> Automated escrow refund is released within <strong>24–48 hours</strong> back to your original source account.</li>
                    <li><strong class="text-slate-800">Cash on Delivery (COD):</strong> Credit voucher issued instantly or direct bank transfer to your linked UPI VPA within 3 business days.</li>
                </ul>
            </section>

            <section>
                <h2 class="font-display text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-500/10 text-emerald-700 font-mono text-xs flex items-center justify-center font-bold">5</span>
                    Non-Returnable Items
                </h2>
                <p>
                    Customized personalized lots, auction lots flagged as "As-Is / Clearance Lot", and products consumed or opened without defect verification cannot be returned.
                </p>
            </section>

        </article>

        <!-- Navigation helper -->
        <div class="mt-8 flex justify-between items-center text-xs font-mono text-slate-500">
            <a href="{{ route('pages.privacy') }}" class="hover:text-amber-600 underline">&larr; Privacy Policy</a>
            <a href="{{ route('pages.terms') }}" class="hover:text-amber-600 underline">Terms of Service &rarr;</a>
        </div>

    </main>

    <!-- Global Footer -->
    <x-footer />

</body>
</html>
