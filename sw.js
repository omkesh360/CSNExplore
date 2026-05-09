/**
 * CSNExplore Service Worker v2.0 - OPTIMIZED
 * Aggressive caching for maximum performance
 */

const CACHE_VERSION = 'csnexplore-v2';
const STATIC_CACHE = CACHE_VERSION + '-static';
const DYNAMIC_CACHE = CACHE_VERSION + '-dynamic';
const MAX_DYNAMIC_ITEMS = 50;

// Critical assets to cache immediately
const STATIC_ASSETS = [
  '/',
  '/style.css',
  '/animations.min.css',
  '/animations.js',
  '/images/travelhub.png',
  '/images/fevicon/favicon-32x32.png',
  '/images/fevicon/favicon-16x16.png',
  '/manifest.json'
];

// Install event - cache static assets aggressively
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(STATIC_CACHE)
      .then(cache => {
        return cache.addAll(STATIC_ASSETS).catch(err => {
          console.warn('SW: Failed to cache some assets', err);
        });
      })
      .then(() => self.skipWaiting())
  );
});

// Activate event - clean old caches
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys()
      .then(keys => Promise.all(
        keys.filter(key => key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
          .map(key => caches.delete(key))
      ))
      .then(() => self.clients.claim())
  );
});

// Limit dynamic cache size
function limitCacheSize(cacheName, maxItems) {
  caches.open(cacheName).then(cache => {
    cache.keys().then(keys => {
      if (keys.length > maxItems) {
        cache.delete(keys[0]).then(() => limitCacheSize(cacheName, maxItems));
      }
    });
  });
}

// Fetch event - Cache-first strategy for static, Network-first for dynamic
self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);
  
  // Skip non-GET requests
  if (request.method !== 'GET') return;
  
  // Skip API calls and admin pages (always fresh)
  if (url.pathname.includes('/php/api/') || 
      url.pathname.includes('/admin/') ||
      url.pathname.includes('/vendor/')) {
    return;
  }

  // Cache-first strategy for static assets
  if (request.url.match(/\.(css|js|png|jpg|jpeg|webp|svg|woff|woff2|ico)$/)) {
    event.respondWith(
      caches.match(request).then(response => {
        return response || fetch(request).then(fetchResponse => {
          if (!fetchResponse || fetchResponse.status !== 200 || fetchResponse.type !== 'basic') {
            return fetchResponse;
          }
          
          const responseClone = fetchResponse.clone();
          caches.open(STATIC_CACHE).then(cache => {
            cache.put(request, responseClone);
          });
          
          return fetchResponse;
        });
      })
    );
    return;
  }

  // Network-first strategy for HTML pages (with cache fallback)
  event.respondWith(
    fetch(request)
      .then(response => {
        if (!response || response.status !== 200) {
          return response;
        }
        
        const responseClone = response.clone();
        caches.open(DYNAMIC_CACHE).then(cache => {
          cache.put(request, responseClone);
          limitCacheSize(DYNAMIC_CACHE, MAX_DYNAMIC_ITEMS);
        });
        
        return response;
      })
      .catch(() => {
        return caches.match(request).then(response => {
          return response || caches.match('/');
        });
      })
  );
});
