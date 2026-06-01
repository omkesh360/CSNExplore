/**
 * CSNExplore Service Worker v4.0 – OPTIMIZED
 * Strategy:
 *   - Static assets (CSS/JS/fonts/images): Cache-First, 1-year TTL
 *   - HTML pages: Stale-While-Revalidate (serve from cache instantly, update in bg)
 *   - API/Admin: Network-only, never cached
 *   - Hero image: Precached at install time for instant LCP
 */

const CACHE_VERSION  = 'csn-v6';
const STATIC_CACHE   = CACHE_VERSION + '-static';
const PAGES_CACHE    = CACHE_VERSION + '-pages';
const MAX_PAGE_ITEMS = 20;
const STATIC_TTL_MS  = 365 * 24 * 60 * 60 * 1000; // 1 year

// Critical assets to precache at install time (LCP + above-the-fold)
const PRECACHE_ASSETS = [
  './style.min.css',
  './animations.min.css',
  './animations.min.js',
  './manifest.json',
  // Hero image: precached so LCP is instant on repeat visits
  './images/hotel-hero-section-mobile.webp',
];

// ── Install: precache static shell ─────────────────────────────────────────
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then(cache =>
      cache.addAll(PRECACHE_ASSETS).catch(err =>
        console.warn('[SW] Precache partial failure:', err)
      )
    ).then(() => self.skipWaiting())
  );
});

// ── Activate: nuke all old caches ──────────────────────────────────────────
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys =>
      Promise.all(
        keys.filter(k => k !== STATIC_CACHE && k !== PAGES_CACHE)
            .map(k => caches.delete(k))
      )
    ).then(() => self.clients.claim())
  );
});

// ── Fetch routing ──────────────────────────────────────────────────────────
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET and cross-origin requests
  if (request.method !== 'GET') return;
  if (url.origin !== self.location.origin) return;

  // Never cache admin / API / vendor routes
  if (/\/(admin|php\/api|vendor)\//i.test(url.pathname)) return;

  // ── Strategy A: Cache-First for static assets (CSS/JS/fonts/images) ───
  if (/\.(css|js|woff2?|ttf|otf|webp|avif|png|jpg|jpeg|gif|svg|ico)(\?.*)?$/.test(url.pathname)) {
    event.respondWith(cacheFirst(request, STATIC_CACHE));
    return;
  }

  // ── Strategy B: Stale-While-Revalidate for HTML pages ─────────────────
  event.respondWith(staleWhileRevalidate(request, PAGES_CACHE));
});

// ── Cache-First helper ─────────────────────────────────────────────────────
async function cacheFirst(request, cacheName) {
  const cache    = await caches.open(cacheName);
  const cached   = await cache.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response.ok) {
      cache.put(request, response.clone()).catch(() => {});
    }
    return response;
  } catch {
    return new Response('', { status: 503, statusText: 'Offline' });
  }
}

// ── Stale-While-Revalidate helper ─────────────────────────────────────────
async function staleWhileRevalidate(request, cacheName) {
  const cache  = await caches.open(cacheName);
  const cached = await cache.match(request);

  // Always revalidate in the background
  const networkFetch = fetch(request).then(response => {
    if (response && response.status === 200) {
      cache.put(request, response.clone()).then(() => limitCache(cache, MAX_PAGE_ITEMS));
    }
    return response;
  }).catch(() => null);

  // Return cached immediately if available; otherwise wait for network
  return cached || networkFetch || new Response('', { status: 503 });
}

// ── Trim old entries from cache ────────────────────────────────────────────
async function limitCache(cache, max) {
  const keys = await cache.keys();
  if (keys.length > max) {
    await cache.delete(keys[0]);
    await limitCache(cache, max);
  }
}
