<!-- ── GLOBAL BAZAARIO FOOTER COMPONENT ── -->
<footer class="w-full bg-white/70 backdrop-blur-md border-t border-slate-authority/10 pt-14 pb-8 text-xs text-slate-authority/70 relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-authority/10">
            <!-- Brand & Description -->
            <div class="md:col-span-4 flex flex-col items-start space-y-3">
                <x-brand-logo size="md" :link="url('/')" />
                <p class="text-xs text-slate-authority/60 leading-relaxed max-w-xs">
                    {{ \App\Models\SiteSetting::getTagline() }}
                </p>
                <!-- Social Badges -->
                <div class="flex items-center space-x-2 pt-2">
                    <a aria-label="Instagram"
                        class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                        href="https://instagram.com" target="_blank" rel="noopener noreferrer">ig</a>
                    <a aria-label="X Twitter"
                        class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                        href="https://x.com" target="_blank" rel="noopener noreferrer">x</a>
                    <a aria-label="YouTube"
                        class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                        href="https://youtube.com" target="_blank" rel="noopener noreferrer">yt</a>
                    <a aria-label="GitHub"
                        class="w-7 h-7 rounded-lg bg-surface-container-low border border-slate-authority/10 flex items-center justify-center text-slate-authority hover:bg-amber-action transition-colors text-[11px] font-mono font-bold"
                        href="https://github.com" target="_blank" rel="noopener noreferrer">gh</a>
                </div>
            </div>
            <!-- Links: Shop -->
            <div class="md:col-span-2">
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Shop</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">All Products</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('auctions.index') }}">Live Auctions</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">Categories</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('user.compare') }}">AI Compare</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index', ['filter' => 'deals']) }}">Deals</a></li>
                </ul>
            </div>
            <!-- Links: Sell -->
            <div class="md:col-span-2">
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Sell</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('docs.become-a-seller') }}">Become a Seller</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('docs.fees-and-commission') }}">Fees &amp; Commission</a></li>
                </ul>
            </div>
            <!-- Links: Platform -->
            <div class="md:col-span-2">
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Company</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.how-it-works') }}">How It Works / About</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.how-it-works') }}">About Us</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.return-policy') }}">Return Policy</a></li>
                </ul>
            </div>
            <!-- Links: Help & Escrow -->
            <div class="md:col-span-2">
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Security</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.how-it-works') }}">Escrow Guarantee</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.how-it-works') }}">Dispute Center</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.privacy') }}">Privacy Policy</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.terms') }}">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        <!-- Bottom Bar -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-[11px] font-mono text-slate-authority/60 gap-4">
            <div>
                © {{ date('Y') }} {{ strtoupper(\App\Models\SiteSetting::getSiteName()) }} MARKETPLACE. ALL RIGHTS RESERVED.
            </div>

            <div class="flex items-center space-x-4 relative" x-data="bazaarioCurrency()">
                <span class="flex items-center gap-1 hidden sm:inline-flex text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> All Systems Operational
                </span>

                <!-- Currency Selector (P37) -->
                <div class="relative">
                    <button @click="currOpen = !currOpen" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:border-amber-500 text-slate-800 transition-colors shadow-xs active:scale-95"
                            aria-label="Select Currency">
                        <span class="font-mono font-bold text-amber-600" x-text="currentCurr.symbol"></span>
                        <span class="font-semibold text-xs" x-text="currentCurr.code"></span>
                        <span class="material-symbols-outlined text-[15px] text-slate-400 transition-transform duration-200" :class="{'rotate-180': currOpen}">expand_more</span>
                    </button>

                    <div x-cloak x-show="currOpen" @click.outside="currOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute top-full mt-2 right-0 w-36 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 overflow-hidden">
                        <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Select Currency</div>
                        <template x-for="curr in currencies" :key="curr.code">
                            <button @click="setCurrency(curr)"
                                    class="w-full text-left px-3 py-2 hover:bg-amber-50/80 flex items-center justify-between text-xs font-medium text-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-amber-600" x-text="curr.symbol"></span>
                                    <span x-text="curr.code"></span>
                                </span>
                                <span x-show="currentCurr.code === curr.code" class="material-symbols-outlined text-xs text-amber-600 font-bold">check</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- ── CURRENCY ENGINE SCRIPT ── -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bazaarioCurrency', () => ({
            currOpen: false,
            currencies: [
                { code: 'INR', symbol: '₹', rate: 1.0, decimals: 0 },
                { code: 'USD', symbol: '$', rate: 0.012, decimals: 2 },
                { code: 'EUR', symbol: '€', rate: 0.011, decimals: 2 }
            ],
            currentCurr: { code: 'INR', symbol: '₹', rate: 1.0, decimals: 0 },

            init() {
                const savedCurr = localStorage.getItem('bazaario_curr');
                if (savedCurr) {
                    const found = this.currencies.find(c => c.code === savedCurr);
                    if (found) this.currentCurr = found;
                }
                this.applyCurrency();
            },

            setCurrency(curr) {
                this.currentCurr = curr;
                this.currOpen = false;
                localStorage.setItem('bazaario_curr', curr.code);
                this.applyCurrency();
            },

            applyCurrency() {
                const rate = this.currentCurr.rate;
                const symbol = this.currentCurr.symbol;

                document.querySelectorAll('[data-price-inr]').forEach(el => {
                    const inrVal = parseFloat(el.getAttribute('data-price-inr'));
                    if (!isNaN(inrVal)) {
                        const converted = inrVal * rate;
                        const formatted = this.currentCurr.decimals > 0 ? converted.toFixed(this.currentCurr.decimals) : Math.round(converted).toLocaleString('en-IN');
                        el.textContent = `${symbol} ${formatted}`;
                    }
                });
            }
        }));
    });
</script>

