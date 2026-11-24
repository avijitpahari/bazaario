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
            <div class="md:col-span-2" x-data="{ aiModalOpen: false }">
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Shop</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">All Products</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('auctions.index') }}">Live Auctions</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index') }}">Categories</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('user.compare') }}">AI Compare</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('products.index', ['filter' => 'deals']) }}">Deals</a></li>
                    <li><button type="button" @click="aiModalOpen = true" class="hover:text-amber-action transition-colors text-left text-amber-600 font-semibold cursor-pointer">✦ Ask Bazaario AI</button></li>
                </ul>

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
                                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-xl text-amber-600 font-bold">
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
                <h5 class="font-mono font-bold text-slate-authority text-[11px] uppercase tracking-wider mb-3">Security & Support</h5>
                <ul class="space-y-2 text-xs font-medium text-slate-authority/70">
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('pages.how-it-works') }}">Escrow Guarantee</a></li>
                    <li><a class="hover:text-amber-action transition-colors" href="{{ route('support.hub') }}">Support &amp; Dispute Desk</a></li>
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

            <div class="flex items-center space-x-4">
                <span class="flex items-center gap-1 hidden sm:inline-flex text-slate-500">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> All Systems Operational
                </span>
            </div>
        </div>
    </div>
</footer>

<!-- ── RUPEE CURRENCY LOCK SCRIPT ── -->
<script>
    try {
        localStorage.removeItem('bazaario_curr');
    } catch (e) {}
</script>

