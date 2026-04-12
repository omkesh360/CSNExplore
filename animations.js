/**
 * CSNExplore - Global Animation System
 * Handles scroll-based animations across all pages
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        threshold: 0.1,        // Percentage of element visible before animating
        rootMargin: '0px 0px -50px 0px',  // Trigger slightly before element enters viewport
        once: true             // Animate only once
    };

    // Track animated elements
    const animatedElements = new Set();

    /**
     * Initialize Intersection Observer
     */
    function initAnimationObserver() {
        // Check if IntersectionObserver is supported
        if (!('IntersectionObserver' in window)) {
            // Fallback: show all elements immediately
            document.querySelectorAll('[data-animate]').forEach(el => {
                el.style.opacity = '1';
                el.style.transform = 'none';
            });
            return;
        }

        // Create observer
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    
                    // Add visible class to trigger animation
                    element.classList.add('animate-visible');
                    
                    // Track animated element
                    animatedElements.add(element);
                    
                    // Stop observing if once is true
                    if (config.once) {
                        observer.unobserve(element);
                    }
                } else if (!config.once) {
                    // Remove animation class if element leaves viewport (when once is false)
                    entry.target.classList.remove('animate-visible');
                }
            });
        }, {
            threshold: config.threshold,
            rootMargin: config.rootMargin
        });

        // Observe all elements with data-animate attribute
        document.querySelectorAll('[data-animate]').forEach(element => {
            observer.observe(element);
        });

        return observer;
    }

    /**
     * Add animations to dynamically loaded content
     */
    function observeNewElements(observer) {
        // Watch for new elements added to the DOM
        const mutationObserver = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) { // Element node
                        // Check if the node itself has data-animate
                        if (node.hasAttribute('data-animate')) {
                            observer.observe(node);
                        }
                        // Check children
                        node.querySelectorAll('[data-animate]').forEach(el => {
                            observer.observe(el);
                        });
                    }
                });
            });
        });

        mutationObserver.observe(document.body, {
            childList: true,
            subtree: true
        });

        return mutationObserver;
    }

    /**
     * Add stagger animation to children
     */
    function initStaggerAnimations() {
        document.querySelectorAll('.stagger-children').forEach(container => {
            const children = container.children;
            Array.from(children).forEach((child, index) => {
                child.style.animationDelay = `${(index + 1) * 0.1}s`;
            });
        });
    }

    /**
     * Add hover effects to cards
     */
    function initHoverEffects() {
        // Add hover-lift to all cards
        document.querySelectorAll('.card, .admin-card, [class*="card-"]').forEach(card => {
            if (!card.classList.contains('hover-lift') && !card.classList.contains('no-hover')) {
                card.classList.add('hover-lift');
            }
        });

        // Add img-zoom to images in containers
        document.querySelectorAll('.gallery-thumb, .listing-card, [class*="image-container"]').forEach(container => {
            const img = container.querySelector('img');
            if (img && !container.classList.contains('img-zoom-container')) {
                container.classList.add('img-zoom-container');
                img.classList.add('img-zoom');
            }
        });
    }

    /**
     * Animate counters (for stats)
     */
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count') || element.textContent.replace(/\D/g, ''));
        const duration = 2000; // 2 seconds
        const increment = target / (duration / 16); // 60fps
        let current = 0;

        const updateCounter = () => {
            current += increment;
            if (current < target) {
                element.textContent = Math.floor(current).toLocaleString();
                requestAnimationFrame(updateCounter);
            } else {
                element.textContent = target.toLocaleString();
            }
        };

        updateCounter();
    }

    /**
     * Initialize counter animations
     */
    function initCounterAnimations() {
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    counterObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('[data-counter], .stat-num').forEach(counter => {
            counterObserver.observe(counter);
        });
    }

    /**
     * Add parallax effect to hero sections
     */
    function initParallax() {
        const parallaxElements = document.querySelectorAll('[data-parallax]');
        
        if (parallaxElements.length === 0) return;

        let ticking = false;

        function updateParallax() {
            const scrolled = window.pageYOffset;

            parallaxElements.forEach(element => {
                const speed = parseFloat(element.getAttribute('data-parallax')) || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translate3d(0, ${yPos}px, 0)`;
            });

            ticking = false;
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }, { passive: true });
    }

    /**
     * Add smooth scroll to anchor links
     */
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href === '#' || href === '#!') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Add loading animation to images
     */
    function initImageLoading() {
        const images = document.querySelectorAll('img[data-src], img[loading="lazy"]');
        
        images.forEach(img => {
            // Add shimmer effect while loading
            if (!img.complete) {
                img.classList.add('shimmer');
                img.addEventListener('load', () => {
                    img.classList.remove('shimmer');
                    img.classList.add('animate-visible');
                }, { once: true });
            }
        });
    }

    /**
     * Initialize all animations
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        // Initialize animation observer
        const observer = initAnimationObserver();

        // Initialize other features
        if (observer) {
            observeNewElements(observer);
        }
        
        initStaggerAnimations();
        initHoverEffects();
        initCounterAnimations();
        initParallax();
        initSmoothScroll();
        initImageLoading();

        // Add loaded class to body
        document.body.classList.add('animations-loaded');

        console.log('✨ CSNExplore animations initialized');
    }

    // Auto-initialize
    init();

    // Expose API for manual control
    window.CSNAnimations = {
        init: init,
        animateElement: function(element) {
            if (element && element.hasAttribute('data-animate')) {
                element.classList.add('animate-visible');
            }
        },
        resetElement: function(element) {
            if (element) {
                element.classList.remove('animate-visible');
            }
        }
    };

})();
