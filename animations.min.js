/**
 * CSNExplore â€” Global Animation System v3.1 OPTIMIZED
 * Scroll reveals Â· Stack cards Â· Page transitions Â· Counters Â· Parallax
 * PERFORMANCE: Deferred initialization for better FCP/LCP
 */
(function () {
  'use strict';

  /* â”€â”€â”€ Easing helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  var EASE_EXPO  = 'cubic-bezier(0.19,1,0.22,1)';
  var EASE_SPRING= 'cubic-bezier(0.175,0.885,0.32,1.15)';

  /* â”€â”€â”€ 1. Page fade-in â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initPageFade() {
    // body starts at opacity:0 via CSS @keyframes pageFadeIn in header.php
    // Just add page-ready class once loaded so CSS animation completes cleanly
    window.addEventListener('load', function() {
      document.body.classList.add('page-ready');
    }, { passive: true });
  }

  /* â”€â”€â”€ 2. Scroll reveal â€” [data-reveal] â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initScrollReveal() {
    // Mark body so animations.css knows JS is active (prevents flash of invisible elements)
    document.body.classList.add('csn-anim-init');

    if (!('IntersectionObserver' in window)) {
      document.querySelectorAll('[data-reveal]').forEach(function (el) {
        el.style.opacity = '1';
        el.style.transform = 'none';
      });
      return;
    }

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      observer.observe(el);
    });

    // Also handle [data-reveal-children]
    var childObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          childObserver.unobserve(entry.target);
        }
      });
    }, { threshold: 0.06, rootMargin: '0px 0px -20px 0px' });

    document.querySelectorAll('[data-reveal-children]').forEach(function (el) {
      childObserver.observe(el);
    });
  }

  /* â”€â”€â”€ 3. Stack card carousel (trip planner section) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initStackCards(wrapId, interval) {
    var wrap = document.getElementById(wrapId);
    if (!wrap) return;

    var cards = Array.prototype.slice.call(wrap.querySelectorAll('.stack-card, .img-stack-card'));
    if (!cards.length) return;

    var n = cards.length;
    var current = 0;

    // 4-layer visual states: front â†’ mid1 â†’ mid2 â†’ hidden-back
    var states = [
      { z: 4, opacity: 1,    tx: 0,  ty: 0,  scale: 1,    rot: 0,    filter: 'drop-shadow(0 28px 30px rgba(0,0,0,0.5)) drop-shadow(0 0 20px rgba(236,91,19,0.15))' },
      { z: 3, opacity: 0.75, tx: 0,  ty: 12, scale: 0.93, rot: -2.5, filter: 'drop-shadow(0 10px 18px rgba(0,0,0,0.28))' },
      { z: 2, opacity: 0.45, tx: 0,  ty: 22, scale: 0.86, rot: 3,    filter: 'drop-shadow(0 4px 8px rgba(0,0,0,0.15))' },
      { z: 1, opacity: 0,    tx: 0,  ty: 32, scale: 0.80, rot: -1.5, filter: 'none' },
    ];

    function buildTransform(s) {
      return 'translateX(' + s.tx + 'px) translateY(' + s.ty + 'px) scale(' + s.scale + ') rotate(' + s.rot + 'deg)';
    }

    function applyState(card, s, animate) {
      if (!animate) {
        card.style.transition = 'none';
      }
      card.style.zIndex     = s.z;
      card.style.opacity    = s.opacity;
      card.style.transform  = buildTransform(s);
      card.style.filter     = s.filter;
    }

    // Init without transition so cards snap to position silently
    cards.forEach(function (card, i) {
      applyState(card, states[i % n], false);
    });

    // Single reflow to flush the no-transition state
    void wrap.offsetWidth;

    // Re-enable transitions on all cards
    cards.forEach(function (card) {
      card.style.transition =
        'transform 0.6s cubic-bezier(0.22,1,0.36,1),' +
        'opacity 0.6s cubic-bezier(0.22,1,0.36,1)';
    });

    // Advance every `interval` ms
    setInterval(function () {
      current = (current + 1) % n;
      cards.forEach(function (card, i) {
        var si = (i - current + n) % n;
        applyState(card, states[si], true);
      });
    }, interval || 3200);
  }

  /* â”€â”€â”€ 4. Scroll progress bar â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initScrollBar() {
    var bar = document.getElementById('csn-scroll-bar');
    if (!bar) return;
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var doc   = document.documentElement;
        var total = doc.scrollHeight - doc.clientHeight;
        bar.style.width = total > 0 ? (doc.scrollTop / total * 100) + '%' : '0%';
        ticking = false;
      });
    }, { passive: true });
  }

  /* â”€â”€â”€ 5. Counter animation â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function animateCounter(el) {
    var raw    = el.getAttribute('data-count') || el.textContent.replace(/\D/g, '');
    var target = parseInt(raw, 10);
    if (!target) return;
    var start    = performance.now();
    var duration = 1800;
    function step(now) {
      var p = Math.min((now - start) / duration, 1);
      // ease-out cubic
      var eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.floor(eased * target).toLocaleString();
      if (p < 1) requestAnimationFrame(step);
      else el.textContent = target.toLocaleString();
    }
    requestAnimationFrame(step);
  }

  function initCounters() {
    if (!('IntersectionObserver' in window)) return;
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) { animateCounter(e.target); obs.unobserve(e.target); }
      });
    }, { threshold: 0.5 });
    document.querySelectorAll('[data-counter], .stat-num').forEach(function (el) {
      obs.observe(el);
    });
  }



  /* â”€â”€â”€ 7. Smooth anchor scroll â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initSmoothScroll() {
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href^="#"]');
      if (!a) return;
      var href = a.getAttribute('href');
      if (href === '#' || href === '#!') return;
      var target = document.querySelector(href);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    }, { passive: true });
  }

  /* â”€â”€â”€ 8. Image shimmer while loading â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initImageLoading() {
    document.querySelectorAll('img[loading="lazy"]').forEach(function (img) {
      if (img.complete) return;
      img.style.transition = 'opacity 0.5s ease';
      img.style.opacity = '0';
      img.addEventListener('load', function () {
        img.style.opacity = '1';
      }, { once: true });
    });
  }

  /* â”€â”€â”€ 9. Hover lift for cards that don't use Tailwind group â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function initHoverEffects() {
    document.querySelectorAll('.card-hover, .listing-card-anim').forEach(function (card) {
      // already handled by CSS â€” just ensure will-change is set
      card.style.willChange = 'transform';
    });
  }

  /* â”€â”€â”€ INIT â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ */
  function init() {
    initPageFade();
    initScrollReveal();
    initScrollBar();
    initCounters();
    initSmoothScroll();
    initImageLoading();
    initHoverEffects();

    // Stack cards â€” init after a short delay so CSS transitions are registered
    setTimeout(function () {
      initStackCards('trip-stack-wrap', 1500);
      initStackCards('suggestor-stack-wrap', 1500);
    }, 120);

    document.body.classList.add('animations-loaded');
  }

  // PERFORMANCE: Defer initialization until DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { passive: true });
  } else {
    // DOM already loaded, defer to next tick
    setTimeout(init, 0);
  }

  // Public API
  window.CSNAnimations = {
    init: init,
    initStackCards: initStackCards,
    animateElement: function (el) { if (el) el.classList.add('revealed'); },
    resetElement:   function (el) { if (el) el.classList.remove('revealed'); }
  };

})();
