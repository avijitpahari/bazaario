<!-- ── BAZAARIO CUSTOM TOAST NOTIFICATIONS & CONFIRMATION DIALOG ── -->
<div x-data="toastManager()" 
     x-init="init()"
     class="relative"
     style="font-family: inherit;">

    <!-- Floating Toast Container (Top-Right) -->
    <div class="fixed top-20 right-5 z-[999999] flex flex-col gap-3 max-w-sm sm:max-w-md w-full pointer-events-none px-4 sm:px-0">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.visible"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                 x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-x-8 scale-95"
                 class="pointer-events-auto bg-white/95 backdrop-blur-md border rounded-2xl p-4 shadow-xl flex flex-col gap-2 relative overflow-hidden transition-all"
                 :class="{
                     'border-emerald-200 text-slate-800 shadow-emerald-500/10': toast.type === 'success',
                     'border-rose-200 text-slate-800 shadow-rose-500/10': toast.type === 'error',
                     'border-amber-200 text-slate-800 shadow-amber-500/10': toast.type === 'warning',
                     'border-sky-200 text-slate-800 shadow-sky-500/10': toast.type === 'info'
                 }">
                
                <div class="flex items-start gap-3">
                    <!-- Icon Badge -->
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 border mt-0.5"
                         :class="{
                             'bg-emerald-50 text-emerald-600 border-emerald-200/60': toast.type === 'success',
                             'bg-rose-50 text-rose-600 border-rose-200/60': toast.type === 'error',
                             'bg-amber-50 text-amber-600 border-amber-200/60': toast.type === 'warning',
                             'bg-sky-50 text-sky-600 border-sky-200/60': toast.type === 'info'
                         }">
                        <span class="material-symbols-outlined text-[20px]" x-text="toast.icon"></span>
                    </div>

                    <!-- Text Content -->
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="font-heading font-bold text-xs uppercase tracking-wider" 
                           :class="{
                               'text-emerald-900': toast.type === 'success',
                               'text-rose-900': toast.type === 'error',
                               'text-amber-900': toast.type === 'warning',
                               'text-sky-900': toast.type === 'info'
                           }"
                           x-text="toast.title"></p>
                        <p class="font-sans text-xs text-slate-700 mt-0.5 leading-relaxed break-words" x-text="toast.message"></p>
                    </div>

                    <!-- Close Button -->
                    <button type="button" @click="removeToast(toast.id)"
                            class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors shrink-0 cursor-pointer">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>

                <!-- Animated Auto-Dismiss Progress Bar -->
                <div class="h-1 -mx-4 -mb-4 bg-slate-100 overflow-hidden">
                    <div class="h-full transition-all duration-100 linear"
                         :class="{
                             'bg-emerald-500': toast.type === 'success',
                             'bg-rose-500': toast.type === 'error',
                             'bg-amber-500': toast.type === 'warning',
                             'bg-sky-500': toast.type === 'info'
                         }"
                         :style="`width: ${toast.progress}%`"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Custom Confirmation Modal (Replaces Browser Default alert/confirm dialog) -->
    <div x-cloak x-show="confirmState.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999999] flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs"
         @keydown.escape.window="cancelConfirm()">
        
        <div @click.outside="cancelConfirm()"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="scale-95 translate-y-3 opacity-0"
             x-transition:enter-end="scale-100 translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="scale-100 translate-y-0 opacity-100"
             x-transition:leave-end="scale-95 translate-y-3 opacity-0"
             class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl border border-slate-200/90 flex flex-col gap-5 relative overflow-hidden">
            
            <!-- Top Colored Accent Bar -->
            <div class="absolute top-0 left-0 right-0 h-1.5"
                 :class="{
                     'bg-gradient-to-r from-rose-500 via-red-500 to-rose-600': confirmState.type === 'danger',
                     'bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600': confirmState.type === 'warning',
                     'bg-gradient-to-r from-sky-400 via-indigo-500 to-indigo-600': confirmState.type === 'info'
                 }"></div>

            <div class="flex items-start gap-4">
                <!-- Icon Badge -->
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 border"
                     :class="{
                         'bg-rose-50 text-rose-600 border-rose-200': confirmState.type === 'danger',
                         'bg-amber-50 text-amber-600 border-amber-200': confirmState.type === 'warning',
                         'bg-sky-50 text-sky-600 border-sky-200': confirmState.type === 'info'
                     }">
                    <span class="material-symbols-outlined text-[26px]"
                          x-text="confirmState.type === 'danger' ? 'delete_forever' : (confirmState.type === 'warning' ? 'warning' : 'help')"></span>
                </div>

                <!-- Text Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="font-heading font-bold text-base text-slate-900" x-text="confirmState.title"></h3>
                    <p class="font-sans text-xs text-slate-600 mt-1 leading-relaxed break-words" x-text="confirmState.message"></p>
                    <template x-if="confirmState.subtext">
                        <p class="font-sans text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            <span x-text="confirmState.subtext"></span>
                        </p>
                    </template>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                <button type="button" 
                        @click="cancelConfirm()"
                        class="h-10 px-4 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-semibold text-xs transition cursor-pointer"
                        x-text="confirmState.cancelText">
                </button>
                <button type="button" 
                        @click="acceptConfirm()"
                        class="h-10 px-5 rounded-xl font-semibold text-xs text-white transition shadow-md hover:shadow-lg flex items-center gap-1.5 cursor-pointer"
                        :class="{
                            'bg-rose-600 hover:bg-rose-700 shadow-rose-600/20': confirmState.type === 'danger',
                            'bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold shadow-amber-500/20': confirmState.type === 'warning',
                            'bg-slate-900 hover:bg-slate-800 shadow-slate-900/20': confirmState.type === 'info'
                        }">
                    <template x-if="confirmState.type === 'danger'">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </template>
                    <span x-text="confirmState.confirmText"></span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        if (window.bazaarioToastInitialized) return;
        window.bazaarioToastInitialized = true;

        window.toastManager = function() {
            return {
                toasts: [],
                confirmState: {
                    open: false,
                    title: 'Confirm Action',
                    message: '',
                    subtext: '',
                    type: 'danger',
                    confirmText: 'Confirm',
                    cancelText: 'Cancel',
                    resolve: null
                },
                init() {
                    window._activeToastManager = this;

                    // Listen for programmatic toast events
                    window.addEventListener('show-toast', (e) => {
                        this.addToast(e.detail);
                    });

                    // Capture session flash messages if present on page load
                    @if(session('success'))
                        this.addToast({ type: 'success', message: @json(session('success')), title: 'Action Completed' });
                    @endif
                    @if(session('error'))
                        this.addToast({ type: 'error', message: @json(session('error')), title: 'Error Encountered' });
                    @endif
                    @if(session('warning'))
                        this.addToast({ type: 'warning', message: @json(session('warning')), title: 'Attention' });
                    @endif
                    @if(session('info'))
                        this.addToast({ type: 'info', message: @json(session('info')), title: 'Information' });
                    @endif
                    @if(isset($errors) && $errors->any())
                        @foreach($errors->all() as $err)
                            this.addToast({ type: 'error', message: @json($err), title: 'Validation Error' });
                        @endforeach
                    @endif
                },
                addToast({ type = 'info', message = '', title = '', duration = 4500 }) {
                    if (!message) return;

                    // Deduplicate identical active toasts
                    const existing = this.toasts.find(t => t.message === message && t.type === type && t.visible);
                    if (existing) return;

                    const id = Date.now() + Math.random();
                    const icons = {
                        success: 'check_circle',
                        error: 'error',
                        warning: 'warning',
                        info: 'info'
                    };
                    const titles = {
                        success: 'Action Completed',
                        error: 'Error Encountered',
                        warning: 'Attention',
                        info: 'Information'
                    };

                    const toast = {
                        id,
                        type,
                        title: title || titles[type] || 'Notification',
                        message,
                        icon: icons[type] || 'info',
                        visible: true,
                        progress: 100
                    };

                    this.toasts.push(toast);

                    // Animate progress bar countdown
                    const startTime = Date.now();
                    const interval = setInterval(() => {
                        const elapsed = Date.now() - startTime;
                        const remaining = Math.max(0, 100 - (elapsed / duration * 100));
                        toast.progress = remaining;
                        if (remaining <= 0) {
                            clearInterval(interval);
                            this.removeToast(id);
                        }
                    }, 50);
                },
                removeToast(id) {
                    const idx = this.toasts.findIndex(t => t.id === id);
                    if (idx !== -1) {
                        this.toasts[idx].visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(t => t.id !== id);
                        }, 250);
                    }
                },
                askConfirm(options) {
                    return new Promise((resolve) => {
                        this.confirmState = {
                            open: true,
                            title: options.title || (options.type === 'danger' ? 'Confirm Deletion' : 'Please Confirm'),
                            message: options.message || 'Are you sure you want to proceed?',
                            subtext: options.subtext || (options.type === 'danger' ? 'This action cannot be undone.' : ''),
                            type: options.type || 'danger',
                            confirmText: options.confirmText || (options.type === 'danger' ? 'Yes, Delete' : 'Confirm'),
                            cancelText: options.cancelText || 'Cancel',
                            resolve: resolve
                        };
                    });
                },
                acceptConfirm() {
                    if (this.confirmState.resolve) {
                        this.confirmState.resolve(true);
                    }
                    this.confirmState.open = false;
                },
                cancelConfirm() {
                    if (this.confirmState.resolve) {
                        this.confirmState.resolve(false);
                    }
                    this.confirmState.open = false;
                }
            };
        };

        // Global helper function to trigger toast from JS
        window.showToast = function(message, type = 'info', title = '') {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: { message, type, title }
            }));
        };

        // Window toast convenience object
        window.toast = {
            success: (msg, title = 'Action Completed') => window.showToast(msg, 'success', title),
            error: (msg, title = 'Error Encountered') => window.showToast(msg, 'error', title),
            warning: (msg, title = 'Attention') => window.showToast(msg, 'warning', title),
            info: (msg, title = 'Information') => window.showToast(msg, 'info', title)
        };

        // Custom confirm helper
        window.customConfirm = function(optionsOrMessage) {
            let opts = {};
            if (typeof optionsOrMessage === 'string') {
                const isDelete = /delete|remove|cancel|suspend|destroy/i.test(optionsOrMessage);
                opts = {
                    message: optionsOrMessage,
                    type: isDelete ? 'danger' : 'warning',
                    title: isDelete ? 'Confirm Deletion' : 'Confirm Action',
                    confirmText: isDelete ? 'Yes, Delete' : 'Confirm'
                };
            } else {
                opts = optionsOrMessage || {};
            }

            if (window._activeToastManager && typeof window._activeToastManager.askConfirm === 'function') {
                return window._activeToastManager.askConfirm(opts);
            }
            return Promise.resolve(window.confirm(opts.message || 'Are you sure?'));
        };

        // Global interceptor for forms with onsubmit="return confirm('...')" or data-confirm
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form._customConfirmed) {
                if (form) form._customConfirmed = false;
                return;
            }

            const onsubmitAttr = form.getAttribute('onsubmit') || '';
            const confirmMatch = onsubmitAttr.match(/confirm\(\s*(['"])(.*?)\1\s*\)/i);
            const dataConfirm = form.dataset.confirm || (confirmMatch ? confirmMatch[2] : null);

            if (dataConfirm) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const methodInput = form.querySelector('input[name="_method"]');
                const isDelete = (methodInput && methodInput.value.toUpperCase() === 'DELETE') ||
                                 /delete|remove|destroy|cancel/i.test(dataConfirm);

                window.customConfirm({
                    title: isDelete ? 'Confirm Deletion' : 'Confirm Action',
                    message: dataConfirm,
                    type: isDelete ? 'danger' : 'warning',
                    confirmText: isDelete ? 'Yes, Delete' : 'Confirm',
                    cancelText: 'Cancel'
                }).then(confirmed => {
                    if (confirmed) {
                        form._customConfirmed = true;
                        const originalOnsubmit = form.onsubmit;
                        form.onsubmit = null;
                        form.submit();
                        form.onsubmit = originalOnsubmit;
                    }
                });
            }
        }, true);

        // Global interceptor for clickable links or buttons with onclick="return confirm('...')"
        document.addEventListener('click', function(e) {
            const target = e.target.closest('a[onclick*="confirm("], button[onclick*="confirm("]');
            if (!target || target._customConfirmed) {
                if (target) target._customConfirmed = false;
                return;
            }

            const onclickAttr = target.getAttribute('onclick') || '';
            const confirmMatch = onclickAttr.match(/confirm\(\s*(['"])(.*?)\1\s*\)/i);
            if (confirmMatch) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const message = confirmMatch[2];
                const isDelete = /delete|remove|destroy|cancel/i.test(message);

                window.customConfirm({
                    title: isDelete ? 'Confirm Deletion' : 'Confirm Action',
                    message: message,
                    type: isDelete ? 'danger' : 'warning',
                    confirmText: isDelete ? 'Yes, Proceed' : 'Confirm',
                    cancelText: 'Cancel'
                }).then(confirmed => {
                    if (confirmed) {
                        target._customConfirmed = true;
                        if (target.tagName === 'A' && target.href) {
                            window.location.href = target.href;
                        } else {
                            target.click();
                        }
                    }
                });
            }
        }, true);

        // Override standard browser alert with custom toast
        window.alert = function(message) {
            window.showToast(message, 'warning');
        };
    })();
</script>
