/**
 * CSNExplore — Global Animation System v3.0
 * Scroll reveals · Stack cards · Page transitions · Counters · Parallax
 */
(function () {
  'use strict';

  /* ─── Easing helpers ─────────────────────────────────────────────────── */
  var EASE_EXPO  = 'cubic-bezier(0.19,1,0.22,1)';
  var EASE_SPRING= 'cubic-bezier(0.175,0.885,0.32,1.15)';

  /* ─── 1. Page fade-in ────────────────────────────────────────────────── */
  function initPageFade() {
    // body starts at opacity:0 via CSS @keyframes pageFadeIn in header.php
    // Just add page-ready class once loaded so CSS animation completes cleanly
    window.addEventListener('load', function() {
      document.body.classList.add('page-ready');
    });

    // Fade out on navigation (cross-page links only)
    document.addEventListener('click', function (e) {
      var a = e.target.closest('a[href]');
      if (!a) return;
      var href = a.getAttribute('href');
      if (!href || href.startsWith('#') || href.startsWith('javascript') ||
          href.startsWith('mailto') || href.startsWith('tel') ||
          a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
      e.preventDefault();
      document.body.classList.add('page-fade-out');
      setTimeout(function () { window.location.href = href; }, 320);
    });
  }

  /* ─── 2. Scroll reveal — [data-reveal] ──────────────────────────────── */
  function initScrollReveal() {
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

  /* ─── 3. Stack card carousel (trip planner section) ─────────────────── */
  function initStackCards(wrapId, interval) {
    var wrap = document.getElementById(wrapId);
    if (!wrap) return;

    var cards = Array.prototype.slice.call(wrap.querySelectorAll('.stack-card, .img-stack-card'));
    if (!cards.length) return;

    var n = cards.length;
    var current = 0;

    // 4-layer visual states: front → mid1 → mid2 → hidden-back
    var states = [
      { z: 4, opacity: 1,    tx: 0,  ty: 0,  scale: 1,    rot: 0,    shadow: '0 28px 60px -12px rgba(0,0,0,0.55), 0 0 40px -10px rgba(236,91,19,0.18)' },
      { z: 3, opacity: 0.75, tx: 0,  ty: 12, scale: 0.93, rot: -2.5, shadow: '0 16px 36px -8px rgba(0,0,0,0.32)' },
      { z: 2, opacity: 0.45, tx: 0,  ty: 22, scale: 0.86, rot: 3,    shadow: '0 8px 18px -4px rgba(0,0,0,0.18)' },
      { z: 1, opacity: 0,    tx: 0,  ty: 32, scale: 0.80, rot: -1.5, shadow: 'none' },
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
      card.style.boxShadow  = s.shadow;
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
        'opacity 0.6s cubic-bezier(0.22,1,0.36,1),' +
        'box-shadow 0.6s cubic-bezier(0.22,1,0.36,1)';
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

  /* ─── 4. Scroll progress bar ─────────────────────────────────────────── */
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

  /* ─── 5. Counter animation ───────────────────────────────────────────── */
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

  /* ─── 6. Parallax ────────────────────────────────────────────────────── */
  function initParallax() {
    var els = document.querySelectorAll('[data-parallax]');
    if (!els.length) return;
    var ticking = false;
    window.addEventListener('scroll', function () {
      if (ticking) return;
      ticking = true;
      requestAnimationFrame(function () {
        var scrolled = window.pageYOffset;
        els.forEach(function (el) {
          var speed = parseFloat(el.getAttribute('data-parallax')) || 0.4;
          el.style.transform = 'translate3d(0,' + (-scrolled * speed) + 'px,0)';
        });
        ticking = false;
      });
    }, { passive: true });
  }

  /* ─── 7. Smooth anchor scroll ────────────────────────────────────────── */
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
    });
  }

  /* ─── 8. Image shimmer while loading ─────────────────────────────────── */
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

  /* ─── 9. Hover lift for cards that don't use Tailwind group ─────────── */
  function initHoverEffects() {
    document.querySelectorAll('.card-hover, .listing-card-anim').forEach(function (card) {
      // already handled by CSS — just ensure will-change is set
      card.style.willChange = 'transform';
    });
  }

  /* ─── INIT ───────────────────────────────────────────────────────────── */
  function init() {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', init);
      return;
    }

    initPageFade();
    initScrollReveal();
    initScrollBar();
    initCounters();
    initParallax();
    initSmoothScroll();
    initImageLoading();
    initHoverEffects();

    // Stack cards — init after a short delay so CSS transitions are registered
    setTimeout(function () {
      initStackCards('trip-stack-wrap', 1500);
      initStackCards('suggestor-stack-wrap', 1500);
    }, 120);

    document.body.classList.add('animations-loaded');
  }

  init();

  // Public API
  window.CSNAnimations = {
    init: init,
    initStackCards: initStackCards,
    animateElement: function (el) { if (el) el.classList.add('revealed'); },
    resetElement:   function (el) { if (el) el.classList.remove('revealed'); }
  };

})();
