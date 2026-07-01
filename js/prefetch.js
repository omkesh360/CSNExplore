/**
 * CSNExplore Predictive UI Prefetching
 * Injects <link rel="prefetch"> into the head when a user hovers over an internal link for >65ms.
 */
(function() {
    let prefetchCache = new Set();
    let hoverTimer;

    document.addEventListener('mouseover', function(e) {
        let a = e.target.closest('a');
        if (!a) return;

        let href = a.getAttribute('href');
        if (!href || href === '#' || href.startsWith('javascript') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        try {
            let url = new URL(href, window.location.href);
            // Only prefetch internal GET requests
            if (url.origin !== window.location.origin) return;
            // Ignore API calls and admin
            if (url.pathname.startsWith('/php/api') || url.pathname.startsWith('/admin')) return;
            
            // Clean URL for cache
            let cleanUrl = url.href.split('#')[0];
            if (prefetchCache.has(cleanUrl)) return;

            // Wait 400ms before prefetching to avoid accidental hovers and server overload
            hoverTimer = setTimeout(function() {
                prefetchCache.add(cleanUrl);
                let link = document.createElement('link');
                link.rel = 'prefetch';
                link.href = cleanUrl;
                link.as = 'document';
                document.head.appendChild(link);
            }, 400);

        } catch (err) {}
    });

    document.addEventListener('mouseout', function(e) {
        let a = e.target.closest('a');
        if (a && hoverTimer) {
            clearTimeout(hoverTimer);
        }
    });
})();
