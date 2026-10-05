<!-- ── GLOBAL BAZAARIO FOOTER COMPONENT ── -->
<footer class="w-full bg-white/70 backdrop-blur-md border-t border-slate-authority/10 pt-14 pb-8 text-xs text-slate-authority/70 relative z-10">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pb-10 border-b border-slate-authority/10">
            <!-- Brand & Description -->
            <div class="md:col-span-4 flex flex-col items-start space-y-3">
                <a class="flex items-center gap-2" href="{{ url('/') }}">
                    <img alt="Bazaario" class="h-7 w-auto object-contain" src="{{ asset('images/bazaario-logo.png') }}">
                </a>
                <p class="text-xs text-slate-authority/60 leading-relaxed max-w-xs">
                    The next-generation marketplace platform with sub-second live auctions, verified escrow, and autonomous AI recommendations.
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
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index', ['filter' => 'auctions']) }}">Live Auctions</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">Categories</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">AI Compare</a></li>
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
                © {{ date('Y') }} BAZAARIO MARKETPLACE. ALL RIGHTS RESERVED.
            </div>

            <div class="flex items-center space-x-4 relative" x-data="bazaarioLocalization()">
                <span class="flex items-center gap-1 hidden sm:inline-flex text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> All Systems Operational
                </span>

                <!-- Language Dropdown -->
                <div class="relative">
                    <button @click="langOpen = !langOpen" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:border-amber-500 text-slate-800 transition-colors shadow-xs active:scale-95">
                        <span x-text="currentLang.flag"></span>
                        <span class="font-semibold" x-text="currentLang.label"></span>
                        <span class="material-symbols-outlined text-[15px] text-slate-400 transition-transform duration-200" :class="{'rotate-180': langOpen}">expand_more</span>
                    </button>

                    <div x-cloak x-show="langOpen" @click.outside="langOpen = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                         class="absolute top-full mt-2 right-0 w-40 bg-white rounded-2xl shadow-2xl border border-slate-200/90 py-2 z-50 overflow-hidden">
                        <div class="px-3 py-1 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 mb-1">Select Language</div>
                        <template x-for="lang in languages" :key="lang.code">
                            <button @click="setLanguage(lang)"
                                    class="w-full text-left px-3 py-2 hover:bg-amber-50/80 flex items-center justify-between text-xs font-medium text-slate-800 transition-colors">
                                <span class="flex items-center gap-2">
                                    <span x-text="lang.flag"></span>
                                    <span x-text="lang.name"></span>
                                </span>
                                <span x-show="currentLang.code === lang.code" class="material-symbols-outlined text-xs text-amber-600 font-bold">check</span>
                            </button>
                        </template>
                    </div>
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

<!-- ── GLOBAL LOCALIZATION & TRANSLATION ENGINE SCRIPT ── -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('bazaarioLocalization', () => ({
            langOpen: false,
            currOpen: false,
            textCache: new WeakMap(),
            languages: [
                { code: 'en', label: 'English (IN)', name: 'English', flag: '🇮🇳' },
                { code: 'bn', label: 'বাংলা (BN)', name: 'বাংলা', flag: '🇮🇳' },
                { code: 'hi', label: 'हिंदी (HI)', name: 'हिंदी', flag: '🇮🇳' }
            ],
            currencies: [
                { code: 'INR', symbol: '₹', rate: 1.0, decimals: 0 },
                { code: 'USD', symbol: '$', rate: 0.012, decimals: 2 },
                { code: 'EUR', symbol: '€', rate: 0.011, decimals: 2 }
            ],
            currentLang: { code: 'en', label: 'English (IN)', name: 'English', flag: '🇮🇳' },
            currentCurr: { code: 'INR', symbol: '₹', rate: 1.0, decimals: 0 },

            init() {
                const savedLang = localStorage.getItem('bazaario_lang');
                if (savedLang) {
                    const found = this.languages.find(l => l.code === savedLang);
                    if (found) this.currentLang = found;
                }
                const savedCurr = localStorage.getItem('bazaario_curr');
                if (savedCurr) {
                    const found = this.currencies.find(c => c.code === savedCurr);
                    if (found) this.currentCurr = found;
                }
                this.applyCurrency();
                if (this.currentLang.code !== 'en') {
                    this.applyTranslation(this.currentLang.code);
                }
            },

            setLanguage(lang) {
                this.currentLang = lang;
                this.langOpen = false;
                localStorage.setItem('bazaario_lang', lang.code);
                this.applyTranslation(lang.code);
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
            },

            applyTranslation(langCode) {
                const dictionary = {
                    bn: {
                        "Shop": "কেনাকাটা",
                        "Auctions": "নিলাম",
                        "LIVE": "লাইভ",
                        "Sign in": "সাইন ইন",
                        "Sign up": "সাইন আপ",
                        "Featured Sellers": "সেরা বিক্রেতা",
                        "Why Choose Bazaario?": "কেন বাজারিয়ো বেছে নেবেন?",
                        "Search": "অনুসন্ধান",
                        "Add to Cart": "কার্টে যোগ করুন",
                        "Buy Now": "এখনই কিনুন",
                        "Categories": "বিভাগসমূহ",
                        "All Categories": "সকল বিভাগ",
                        "My Dashboard": "আমার ড্যাশবোর্ড",
                        "My Orders": "আমার অর্ডার",
                        "Wishlist": "পছন্দের তালিকা",
                        "Cart": "কার্ট",
                        "Logout": "লগআউট",
                        "Verified Stall": "যাচাইকৃত স্টল",
                        "Products": "পণ্যসমূহ",
                        "Shop Smarter. Sell Bigger.": "স্মার্ট কেনাকাটা করুন। বড় ব্যবসা করুন।",
                        "Explore Marketplace Sellers": "মার্কেটপ্লেস বিক্রেতাদের দেখুন"
                    },
                    hi: {
                        "Shop": "दुकान",
                        "Auctions": "नीलामी",
                        "LIVE": "लाइव",
                        "Sign in": "साइन इन",
                        "Sign up": "साइन अप",
                        "Featured Sellers": "प्रमुख विक्रेता",
                        "Why Choose Bazaario?": "बाज़ारियो क्यों चुनें?",
                        "Search": "खोजें",
                        "Add to Cart": "कार्ट में जोड़ें",
                        "Buy Now": "अभी खरीदें",
                        "Categories": "श्रेणियां",
                        "All Categories": "सभी श्रेणियां",
                        "My Dashboard": "मेरा डैशबोर्ड",
                        "My Orders": "मेरे ऑर्डर",
                        "Wishlist": "विशलिस्ट",
                        "Cart": "कार्ट",
                        "Logout": "लॉगआउट",
                        "Verified Stall": "सत्यापित स्टॉल",
                        "Products": "उत्पाद",
                        "Shop Smarter. Sell Bigger.": "स्मार्ट खरीदारी करें। बड़ा बेचें।",
                        "Explore Marketplace Sellers": "मार्केटप्लेस विक्रेताओं को देखें"
                    }
                };

                const dict = dictionary[langCode] || null;

                const walkNodes = (node) => {
                    if (node.nodeType === Node.TEXT_NODE) {
                        let original = this.textCache.get(node);
                        if (original === undefined) {
                            original = node.nodeValue;
                            this.textCache.set(node, original);
                        }

                        if (langCode === 'en' || !dict) {
                            node.nodeValue = original;
                            return;
                        }

                        let text = original.trim();
                        if (dict[text]) {
                            node.nodeValue = original.replace(text, dict[text]);
                        } else {
                            node.nodeValue = original;
                        }
                    } else if (node.nodeType === Node.ELEMENT_NODE && !['SCRIPT', 'STYLE', 'INPUT', 'TEXTAREA'].includes(node.tagName)) {
                        node.childNodes.forEach(walkNodes);
                    }
                };

                walkNodes(document.body);
            }
        }));
    });
</script>

<!-- ── FLOATING AI DOCKED BUTTON & COMING SOON MODAL ── -->
<div x-data="{ aiModalOpen: false }" class="relative">
    <div class="fixed bottom-6 right-6 z-40">
        <button
            @click="aiModalOpen = true"
            class="bg-slate-900 text-white pl-4 pr-5 py-2.5 rounded-full font-semibold text-xs shadow-xl hover:bg-slate-800 transition-all flex items-center gap-2 ring-1 ring-white/20 active:scale-95 group cursor-pointer"
            type="button">
            <span
                class="material-symbols-outlined text-amber-400 text-[19px] group-hover:rotate-12 transition-transform">auto_awesome</span>
            <span>✦ Ask Bazaario AI</span>
        </button>
    </div>

    <!-- AI Assistant Informative Modal -->
    <div x-cloak x-show="aiModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.outside="aiModalOpen = false"
             class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 relative overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 translate-y-2"
             x-transition:enter-end="scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="scale-100 translate-y-0"
             x-transition:leave-end="scale-95 translate-y-2">
            
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-xl">
                        ✦
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-base text-slate-900">Bazaario AI Assistant</h3>
                        <p class="font-mono text-[11px] text-amber-600 font-semibold uppercase tracking-wider">Autonomous Shopping Agent • Coming Soon</p>
                    </div>
                </div>
                <button type="button" @click="aiModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed mb-4">
                Our semantic marketplace agent is currently integrating multi-lingual catalog comparison, live mandi auction price predictions, and automated escrow dispute arbitration.
            </p>

            <div class="space-y-2.5 mb-5 text-xs text-slate-700 font-medium">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-sm">compare_arrows</span>
                    <span>Smart Multi-Seller Specification Comparison</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-sm">mic</span>
                    <span>Vernacular Voice Search (Bengali, Hindi, English)</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-amber-600 text-sm">trending_up</span>
                    <span>Real-Time Wholesale Auction Bid Advisor</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-sans text-xs font-semibold hover:bg-slate-800 transition">
                    Browse Catalog Now
                </a>
                <button type="button" @click="aiModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-sans text-xs font-medium hover:bg-slate-200 transition">
                    Got It
                </button>
            </div>
        </div>
    </div>
</div>

