// CSNExplore Preloader Script - FIXED 0.5 SECONDS
(function() {
    'use strict';
    
    // FIXED: Exactly 0.5 seconds (500ms) display time
    const FIXED_DISPLAY_TIME = 500;
    
    // Track when preloader started
    const startTime = Date.now();
    
    // Hide preloader function
    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        if (!preloader) return;
        
        const elapsed = Date.now() - startTime;
        const remainingTime = Math.max(0, FIXED_DISPLAY_TIME - elapsed);
        
        setTimeout(function() {
            preloader.classList.add('fade-out');
            
            // Remove from DOM after fade animation
            setTimeout(function() {
                if (preloader.parentNode) {
                    preloader.parentNode.removeChild(preloader);
                }
                
                // Trigger marquee bar initialization after preloader is removed
                const marqueeBar = document.getElementById('marquee-bar');
                if (marqueeBar) {
                    // Force reflow to ensure animations start
                    marqueeBar.style.display = 'none';
                    marqueeBar.offsetHeight; // Force reflow
                    marqueeBar.style.display = '';
                }
            }, 250);
        }, remainingTime);
    }
    
    // Start hiding preloader immediately when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hidePreloader);
    } else {
        // DOM already loaded
        hidePreloader();
    }
})();
