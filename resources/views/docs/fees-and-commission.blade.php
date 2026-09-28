<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Fees & Commission Schedule — Bazaario Marketplace</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&family=Space+Grotesk:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-900 font-mono text-[11px] font-bold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>TRANSPARENT MARKETPLACE PRICING</span>
            </div>
            
            <h1 class="font-display font-extrabold text-3xl sm:text-5xl text-slate-900 tracking-tight leading-tight mb-4">
                Simple, transparent fees with <span class="text-amber-600 underline decoration-amber-400/40 underline-offset-4">zero hidden charges</span>
            </h1>
            
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-sans max-w-2xl mx-auto">
                No monthly store subscription fees. No upfront listing costs. You only pay a competitive percentage commission when you make a successful sale.
            </p>
        </section>

        <!-- Zero Fees Highlights (3 Stat Pills) -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card-elevated text-center">
                <div class="font-display font-extrabold text-3xl text-emerald-600 mb-1">₹0</div>
                <div class="font-display font-bold text-slate-900 text-sm mb-1">Listing Fees</div>
                <p class="text-xs text-slate-500">List unlimited items across all categories for free.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card-elevated text-center">
                <div class="font-display font-extrabold text-3xl text-emerald-600 mb-1">₹0 / mo</div>
                <div class="font-display font-bold text-slate-900 text-sm mb-1">Store Subscriptions</div>
                <p class="text-xs text-slate-500">No monthly maintenance or account maintenance fees.</p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-card-elevated text-center">
                <div class="font-display font-extrabold text-3xl text-amber-500 mb-1">T+2</div>
                <div class="font-display font-bold text-slate-900 text-sm mb-1">Fast Payouts</div>
                <p class="text-xs text-slate-500">Automatic direct bank payout after order delivery.</p>
            </div>
        </section>

        <!-- Interactive Payout Calculator & Category Rate Table -->
        <section x-data="feeCalculator()" class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-20 items-start">
            
            <!-- Left Column: Interactive Calculator -->
            <div class="lg:col-span-5 bg-slate-900 text-white p-6 sm:p-8 rounded-3xl shadow-2xl relative overflow-hidden">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-amber-400">calculate</span>
                    <h3 class="font-display font-bold text-lg text-white">Interactive Payout Calculator</h3>
                </div>
                <p class="text-xs text-slate-300 mb-6">Estimate your exact net payout earnings per sale:</p>

                <!-- Input Selling Price -->
                <div class="mb-5">
                    <label class="block text-xs font-mono font-bold uppercase text-slate-400 mb-2">Item Selling Price (₹)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 font-mono font-bold text-slate-400">₹</span>
                        <input type="number" x-model.number="price" min="0" step="50" 
                               class="w-full pl-8 pr-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white font-mono text-base font-bold focus:outline-none focus:border-amber-500 transition-colors" />
                    </div>
                </div>

                <!-- Select Category -->
                <div class="mb-6">
                    <label class="block text-xs font-mono font-bold uppercase text-slate-400 mb-2">Product Category</label>
                    <select x-model="selectedCategory" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-white font-sans text-xs font-semibold focus:outline-none focus:border-amber-500 transition-colors">
                        <template x-for="(rate, cat) in rates" :key="cat">
                            <option :value="cat" x-text="cat + ' (' + rate + '% commission)'"></option>
                        </template>
                    </select>
                </div>

                <!-- Calculation Breakdown Output -->
                <div class="bg-slate-800/80 rounded-2xl p-4 border border-slate-700 space-y-3 font-mono text-xs">
                    <div class="flex justify-between text-slate-300">
                        <span>Selling Price:</span>
                        <span class="font-bold text-white" x-text="'₹' + Number(price || 0).toLocaleString('en-IN')"></span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Commission Fee (<span x-text="rates[selectedCategory] + '%'"></span>):</span>
                        <span class="font-bold text-amber-400" x-text="'- ₹' + Number(commissionAmount).toLocaleString('en-IN')"></span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Payment Processing (2%):</span>
                        <span class="font-bold text-amber-400" x-text="'- ₹' + Number(pgFee).toLocaleString('en-IN')"></span>
                    </div>
                    <div class="pt-3 border-t border-slate-700 flex justify-between items-center text-sm">
                        <span class="font-bold text-white font-sans">Your Net Payout:</span>
                        <span class="font-extrabold text-emerald-400 text-lg" x-text="'₹' + Number(netPayout).toLocaleString('en-IN')"></span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Category Fee Table -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-card-elevated">
                <h3 class="font-display font-bold text-xl text-slate-900 mb-1">Category Commission Rate Schedule</h3>
                <p class="text-xs text-slate-500 mb-6">Commission is calculated as a flat percentage of the final item sales price:</p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-400 font-mono uppercase text-[10px] tracking-wider">
                                <th class="pb-3 font-bold">Category</th>
                                <th class="pb-3 font-bold">Commission Rate</th>
                                <th class="pb-3 font-bold">Listing Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Electronics & Gadgets</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">5.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Fashion & Apparel</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">8.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Artisan Crafts & Handloom</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">6.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Home Living & Furnishings</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">6.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Collectibles & Rare Antiques</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">7.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                            <tr>
                                <td class="py-3 font-semibold text-slate-900">Books & Fine Stationery</td>
                                <td class="py-3 text-amber-600 font-mono font-bold">5.0%</td>
                                <td class="py-3 text-emerald-600 font-mono font-bold">FREE</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </section>

    </main>

    <!-- Global Footer -->
    @include('components.footer')

    <script>
        function feeCalculator() {
            return {
                price: 2500,
                selectedCategory: 'Artisan Crafts & Handloom',
                rates: {
                    'Electronics & Gadgets': 5.0,
                    'Fashion & Apparel': 8.0,
                    'Artisan Crafts & Handloom': 6.0,
                    'Home Living & Furnishings': 6.0,
                    'Collectibles & Rare Antiques': 7.0,
                    'Books & Fine Stationery': 5.0
                },
                get commissionAmount() {
                    const rate = this.rates[this.selectedCategory] || 5.0;
                    return Math.round(((this.price || 0) * rate) / 100);
                },
                get pgFee() {
                    return Math.round(((this.price || 0) * 2.0) / 100);
                },
                get netPayout() {
                    const val = (this.price || 0) - this.commissionAmount - this.pgFee;
                    return Math.max(0, val);
                }
            }
        }
    </script>

</body>
</html>
