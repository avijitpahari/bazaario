<!-- ══════════════════════════════════════════════════════════════
     FULL-PAGE PRELOADER & CDN RESOURCE INTEGRITY GATE
     Displays while CDN stylesheets, fonts, external scripts, and
     document assets finish loading. Auto-fades out smoothly.
     ══════════════════════════════════════════════════════════════ -->
<div id="site-global-preloader"
     style="position: fixed; inset: 0; z-index: 999999; background: #FFFDF8; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s cubic-bezier(0.4, 0, 0.2, 1); pointer-events: auto;">
    
    <!-- Ambient Luminous Glow in Preloader -->
    <div style="position: absolute; width: 340px; height: 340px; border-radius: 50%; background: radial-gradient(circle, rgba(245, 166, 35, 0.18) 0%, rgba(245, 166, 35, 0.05) 50%, transparent 70%); filter: blur(32px); pointer-events: none;"></div>

    <div style="position: relative; display: flex; flex-direction: column; align-items: center; gap: 20px; z-index: 1;">
        
        <!-- Animated Brand Ring & Spinner -->
        <div style="position: relative; width: 88px; height: 88px; display: flex; align-items: center; justify-content: center;">
            <!-- Outer SVG Spin Ring -->
            <svg style="position: absolute; inset: 0; width: 100%; height: 100%; animation: bz-spin 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;" viewBox="0 0 88 88" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="44" cy="44" r="38" stroke="#E2DFD7" stroke-width="4" opacity="0.4" />
                <path d="M44 6 A38 38 0 0 1 82 44" stroke="#F5A623" stroke-width="4.5" stroke-linecap="round" />
            </svg>

            <!-- Inner Glowing Logo / Icon Pill -->
            <div style="width: 58px; height: 58px; border-radius: 50%; background: #ffffff; box-shadow: 0 4px 18px rgba(245, 166, 35, 0.22), 0 1px 3px rgba(15, 23, 42, 0.06); display: flex; align-items: center; justify-content: center; border: 1px solid rgba(245, 166, 35, 0.25);">
                <img src="{{ asset('images/favicon.png') }}" 
                     alt="Bazaario" 
                     onerror="this.style.display='none'; document.getElementById('bz-preloader-fallback-icon').style.display='block';"
                     style="width: 32px; height: 32px; object-fit: contain;">
                <span id="bz-preloader-fallback-icon" style="display: none; font-size: 26px; line-height: 1;">🛒</span>
            </div>
        </div>

        <!-- Brand Name & Animated Status Text -->
        <div style="text-align: center; display: flex; flex-direction: column; align-items: center; gap: 6px;">
            <div style="font-family: 'Outfit', 'Space Grotesk', system-ui, -apple-system, sans-serif; font-size: 20px; font-weight: 800; letter-spacing: -0.02em; color: #0F172A;">
                Bazaario<span style="color: #F5A623;">.</span>
            </div>
            
            <div id="bz-preloader-status" style="display: flex; align-items: center; gap: 8px; font-family: 'JetBrains Mono', monospace; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; color: #64748B;">
                <span style="width: 6px; height: 6px; border-radius: 50%; background: #F5A623; display: inline-block; animation: bz-pulse 1s ease-in-out infinite;"></span>
                <span>Initializing Resources…</span>
            </div>
        </div>

        <!-- Slim Progress Bar -->
        <div style="width: 180px; height: 3px; background: #E2DFD7; border-radius: 9999px; overflow: hidden; position: relative;">
            <div id="bz-preloader-bar" style="position: absolute; top: 0; left: 0; bottom: 0; width: 35%; background: linear-gradient(90deg, #F5A623, #D97706); border-radius: 9999px; transition: width 0.25s ease; animation: bz-shimmer 1.5s ease-in-out infinite;"></div>
        </div>

    </div>
</div>

<style>
@keyframes bz-spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
@keyframes bz-pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(0.85); }
}
@keyframes bz-shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(250%); }
}
</style>

<script>
(function() {
    const preloader = document.getElementById('site-global-preloader');
    if (!preloader) return;

    let isDismissed = false;

    function dismissPreloader() {
        if (isDismissed) return;
        isDismissed = true;

        const bar = document.getElementById('bz-preloader-bar');
        const status = document.getElementById('bz-preloader-status');
        if (bar) {
            bar.style.animation = 'none';
            bar.style.width = '100%';
        }
        if (status) {
            status.innerHTML = '<span style="color: #16A34A; font-weight: 700;">Ready ✓</span>';
        }

        setTimeout(() => {
            preloader.style.opacity = '0';
            preloader.style.pointerEvents = 'none';
            setTimeout(() => {
                preloader.style.visibility = 'hidden';
                preloader.remove();
            }, 450);
        }, 150);
    }

    // Check when all external stylesheets, fonts, images, and CDN links finish
    const readyStateCheck = () => {
        const fontPromise = (document.fonts && document.fonts.ready) 
            ? document.fonts.ready.catch(() => Promise.resolve())
            : Promise.resolve();

        fontPromise.then(() => {
            if (document.readyState === 'complete') {
                dismissPreloader();
            } else {
                window.addEventListener('load', dismissPreloader, { once: true });
            }
        });
    };

    if (document.readyState === 'complete') {
        readyStateCheck();
    } else {
        window.addEventListener('load', readyStateCheck, { once: true });
    }

    // Safety fallback: if a CDN link or font hangs, dismiss smoothly after 4 seconds
    setTimeout(dismissPreloader, 4000);
})();
</script>
