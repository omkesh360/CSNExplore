// CSNExplore Preloader Script
(function() {
    'use strict';
    
    // Minimum display time (in milliseconds)
    const MIN_DISPLAY_TIME = 1000;
    
    // Track when preloader started
    const startTime = Date.now();
    
    // Hide preloader function
    function hidePreloader() {
        const preloader = document.getElementById('preloader');
        if (!preloader) return;
        
        const elapsed = Date.now() - startTime;
        const remainingTime = Math.max(0, MIN_DISPLAY_TIME - elapsed);
        
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
            }, 500);
        }, remainingTime);
    }
    
    // Wait for the page to fully load all resources
    window.addEventListener('load', hidePreloader);
    
    // If already loaded, still call it (it will wait remaining time)
    if (document.readyState === 'complete') {
        hidePreloader();
    }
})();
