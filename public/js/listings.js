/**
 * listings.js — Shared dynamic listing engine for CSNExplore
 * Powers: stays, car-rentals, bike-rentals, restaurant, attraction, bus
 *
 * Usage: Each page sets window.LISTING_CONFIG = { category, containerId, ... }
 * before loading this script.
 */

(function () {
    // ============================================================
    // CONFIG & STATE
    // ============================================================
    const PHONE = '+918600968888';
    const WHATSAPP_URL = 'https://wa.me/918600968888';

    let allItems = [];  // raw data from API
    let activeSort = 'recommended';
    let visibleLimit = 5;
    let activeFilters = {
        search: '',
        types: [],
        minRating: 0,
        minPrice: 0,
        maxPrice: Infinity,
        popularOnly: false
    };

    // ============================================================
    // BOOTSTRAP — runs after DOM + config ready
    // ============================================================
    async function initListings() {
        const cfg = window.LISTING_CONFIG;
        if (!cfg) return;

        injectFilterSidebar(cfg);
        injectSortBar(cfg);
        await fetchAndRender(cfg);
        bindFilterEvents(cfg);
    }

    // Delegated card navigation — skip if click is on a button or anchor
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-book-now]')) return; // handled by booking-popup.js
        if (e.target.closest('a') || e.target.closest('button')) return;
        const card = e.target.closest('[data-detail-url]');
        if (card) {
            window.location.href = card.dataset.detailUrl;
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initListings);
    } else {
        initListings();
    }

    // ============================================================
    // FETCH
    // ============================================================
    async function fetchAndRender(cfg) {
        const container = document.getElementById(cfg.containerId);
        if (!container) return;

        showSkeleton(container);

        try {
            // Fetch from /api/{category} endpoint (e.g., /api/stays, /api/cars)
            const res = await fetch(`/api/${cfg.category}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            allItems = await res.json();

            // Only show real data - NO FALLBACK to demo data
            if (!allItems || allItems.length === 0) {
                allItems = [];
                console.log('No listings found in database for category:', cfg.category);
            }
        } catch (err) {
            console.error('Listing fetch error:', err);
            allItems = [];
            container.innerHTML = `
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-[64px] text-gray-300 mb-4">cloud_off</span>
                    <p class="text-gray-600 font-semibold text-lg mb-2">Unable to load listings</p>
                    <p class="text-gray-500 text-sm">Please check your connection and try again.</p>
                    <button onclick="window.location.reload()" class="mt-4 bg-primary text-white font-bold py-2 px-6 rounded-lg hover:bg-primary-hover transition-colors">
                        Retry
                    </button>
                </div>`;
            return;
        }

        // Pre-fill search from URL ?search= param (e.g. passed from homepage)
        const urlSearch = new URLSearchParams(window.location.search).get('search');
        if (urlSearch) {
            const searchInput = document.getElementById('search-location');
            if (searchInput) {
                // Show the input if it's locked (reveal it for URL-based search)
                searchInput.style.display = 'block';
                const lockedLabel = document.getElementById('dest-locked-label');
                const changeBtn   = document.getElementById('dest-change-btn');
                if (lockedLabel) lockedLabel.style.display = 'none';
                if (changeBtn)   changeBtn.style.display   = 'none';
                searchInput.value = urlSearch;
            }
            activeFilters.search = urlSearch;
        }

        // Build filter options dynamically from data
        buildFilterOptions(cfg);
        // Initial render
        renderResults(cfg);
    }

    // ============================================================
    // FILTER + SORT LOGIC
    // ============================================================
    function getFiltered() {
        let items = [...allItems];
        const f = activeFilters;

        // Filter out hidden items (is_active = 0)
        items = items.filter(item => parseInt(item.is_active) !== 0);

        // NOTE: Location/name search does not filter listings — all listings are shown regardless

        // Type filter
        if (f.types.length > 0) {
            items = items.filter(item =>
                f.types.includes((item.type || '').trim())
            );
        }

        // Popular only
        if (f.popularOnly) {
            items = items.filter(item => item.badge);
        }

        // Rating
        if (f.minRating > 0) {
            items = items.filter(item => (parseFloat(item.rating) || 0) >= f.minRating);
        }

        // Price
        const priceKey = getPriceKey(window.LISTING_CONFIG?.category);
        if (f.minPrice > 0) {
            items = items.filter(item => (parseFloat(item[priceKey]) || 0) >= f.minPrice);
        }
        if (f.maxPrice < Infinity) {
            items = items.filter(item => (parseFloat(item[priceKey]) || 0) <= f.maxPrice);
        }

        // Sort
        switch (activeSort) {
            case 'rating':
                items.sort((a, b) => (parseFloat(b.rating) || 0) - (parseFloat(a.rating) || 0));
                break;
            case 'price_asc':
                items.sort((a, b) => (parseFloat(a[priceKey]) || 0) - (parseFloat(b[priceKey]) || 0));
                break;
            case 'price_desc':
                items.sort((a, b) => (parseFloat(b[priceKey]) || 0) - (parseFloat(a[priceKey]) || 0));
                break;
            case 'recommended':
            default:
                // Sort by display_order first, then badges, then by rating
                items.sort((a, b) => {
                    // Primary: display_order (lower number = higher priority)
                    const orderA = parseInt(a.display_order) || 999999;
                    const orderB = parseInt(b.display_order) || 999999;
                    if (orderA !== orderB) return orderA - orderB;
                    
                    // Secondary: badges first
                    if (b.badge && !a.badge) return 1;
                    if (a.badge && !b.badge) return -1;
                    
                    // Tertiary: by rating
                    return (parseFloat(b.rating) || 0) - (parseFloat(a.rating) || 0);
                });
        }

        return items;
    }

    function getPriceKey(category) {
        const map = {
            stays: 'price',
            cars: 'dailyRate',
            bikes: 'dailyRate',
            restaurants: 'pricePerPerson',
            attractions: 'entryFee',
            buses: 'price',
        };
        return (map[category] || 'price');
    }

    // Expose load more globally so the button can call it
    window.loadMoreListings = function () {
        visibleLimit += 5;
        renderResults(window.LISTING_CONFIG);
    };

    function renderResults(cfg) {
        const container = document.getElementById(cfg.containerId);
        if (!container) return;
        const items = getFiltered();

        // Update count
        const countEl = document.getElementById('results-count');
        if (countEl) {
            countEl.textContent = `${items.length} result${items.length !== 1 ? 's' : ''} found`;
        }

        if (items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-[64px] text-gray-300 mb-4">inventory_2</span>
                    <p class="text-gray-600 font-semibold text-lg mb-2">No listings available</p>
                    <p class="text-gray-500 text-sm mb-4">There are currently no ${cfg.category} in the database.</p>
                    ${activeFilters.search || activeFilters.types.length > 0 || activeFilters.minRating > 0 || activeFilters.minPrice > 0 || activeFilters.maxPrice < Infinity || activeFilters.popularOnly ? 
                        '<button onclick="resetFilters()" class="mt-2 bg-primary text-white font-bold py-2 px-6 rounded-lg hover:bg-primary-hover transition-colors">Clear Filters</button>' : 
                        '<p class="text-gray-400 text-xs mt-2">Add listings via the Admin Panel</p>'}
                </div>`;
            return;
        }

        // Slice items based on visibleLimit
        const visibleItems = items.slice(0, visibleLimit);

        let html = visibleItems.map(item => renderCard(item, cfg.category)).join('');

        // Add Load More button if there are more items to show
        if (items.length > visibleLimit) {
            html += `
                <div class="text-center mt-8">
                    <button onclick="window.loadMoreListings()" class="bg-primary/10 text-primary font-bold py-3 px-8 rounded-lg hover:bg-primary hover:text-white transition-colors duration-300 shadow-sm">
                        Load More Results
                    </button>
                    <p class="text-xs text-text-muted mt-2">Showing ${visibleLimit} of ${items.length} listings</p>
                </div>
            `;
        } else if (items.length > 5) {
            html += `
                <div class="text-center mt-8 text-sm text-text-muted">
                    End of results (Showing all ${items.length} listings)
                </div>
            `;
        }

        container.innerHTML = html;
    }

    window.resetFilters = function () {
        activeFilters = { search: '', types: [], minRating: 0, minPrice: 0, maxPrice: Infinity, popularOnly: false };
        activeSort = 'recommended';
        visibleLimit = 5; // Reset limit on filter reset

        // Reset UI
        document.querySelectorAll('.filter-type-cb').forEach(cb => cb.checked = false);
        const ratingInput = document.getElementById('filter-rating');
        if (ratingInput) { ratingInput.value = 0; updateRatingDisplay(0); }
        const minPrice = document.getElementById('filter-min-price');
        const maxPrice = document.getElementById('filter-max-price');
        if (minPrice) minPrice.value = '';
        if (maxPrice) maxPrice.value = '';
        const popularCb = document.getElementById('filter-popular');
        if (popularCb) popularCb.checked = false;

        // Reset sort buttons
        document.querySelectorAll('.sort-btn').forEach(b => {
            b.classList.remove('bg-primary', 'text-white');
            b.classList.add('bg-white', 'text-text-main');
        });
        const recBtn = document.querySelector('.sort-btn[data-sort="recommended"]');
        if (recBtn) { recBtn.classList.add('bg-primary', 'text-white'); recBtn.classList.remove('text-text-main'); }

        renderResults(window.LISTING_CONFIG);
    };

    // ============================================================
    // INJECT FILTER SIDEBAR
    // ============================================================
    function injectFilterSidebar(cfg) {
        const target = document.getElementById('filter-sidebar-placeholder');
        if (!target) return;

        target.innerHTML = `
        <aside id="listing-sidebar"
            class="w-full lg:w-72 shrink-0 lg:sticky lg:top-4 self-start">
            <!-- Mobile header -->
            <div class="flex items-center justify-between mb-4 lg:hidden">
                <h2 class="font-bold text-lg text-text-main">Filters</h2>
                <button id="close-sidebar" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-soft overflow-hidden">
                <!-- Popular Only -->
                <div class="p-4 border-b border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input id="filter-popular" type="checkbox"
                            class="w-4 h-4 rounded text-primary accent-primary cursor-pointer"/>
                        <span class="text-sm font-semibold text-text-main group-hover:text-primary transition-colors flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-primary text-[16px] leading-none">workspace_premium</span>
                            Popular / Bestsellers only
                        </span>
                    </label>
                </div>

                <!-- Star Rating -->
                <div class="p-4 border-b border-gray-100">
                    <label class="block text-xs font-bold text-text-muted uppercase tracking-wider mb-3">Min. Star Rating</label>
                    <input id="filter-rating" type="range" min="0" max="5" step="0.5" value="0"
                        class="w-full accent-primary cursor-pointer"/>
                    <div class="flex justify-between items-center mt-1">
                        <span class="text-xs text-text-muted">Any</span>
                        <span id="filter-rating-display" class="text-sm font-bold text-primary">Any</span>
                    </div>
                </div>

                <!-- Type filter (dynamic) -->
                <div id="type-filter-section" class="p-4 border-b border-gray-100 hidden">
                    <label class="block text-xs font-bold text-text-muted uppercase tracking-wider mb-3">Type</label>
                    <div id="type-filter-options" class="space-y-2"></div>
                </div>

                <!-- Price Range -->
                <div class="p-4 border-b border-gray-100">
                    <label class="block text-xs font-bold text-text-muted uppercase tracking-wider mb-3">Price Range (₹)</label>
                    <div class="flex gap-2 items-center">
                        <input id="filter-min-price" type="number" min="0" placeholder="Min"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"/>
                        <span class="text-gray-400 shrink-0">–</span>
                        <input id="filter-max-price" type="number" min="0" placeholder="Max"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none"/>
                    </div>
                </div>

                <!-- Reset -->
                <div class="p-4">
                    <button onclick="resetFilters()"
                        class="w-full py-2 border border-gray-200 rounded-lg text-sm font-bold text-text-muted hover:bg-gray-50 hover:text-primary transition-colors">
                        Reset All Filters
                    </button>
                </div>
            </div>
        </aside>`;
    }

    // ============================================================
    // INJECT SORT BAR
    // ============================================================
    function injectSortBar(cfg) {
        const target = document.getElementById('sort-bar-placeholder');
        if (!target) return;

        target.innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div class="flex items-center gap-2">
                <!-- Mobile filter toggle -->
                <button id="mobile-filter-btn"
                    class="lg:hidden flex items-center gap-2 px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-text-main bg-white hover:border-primary hover:text-primary transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">tune</span> Filters
                </button>
                <p id="results-count" class="text-sm text-text-muted font-medium"></p>
            </div>
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0">
                <span class="text-xs text-text-muted font-bold uppercase tracking-wider shrink-0">Sort:</span>
                <button class="sort-btn bg-primary text-white shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition-all shadow-sm" data-sort="recommended">Recommended</button>
                <button class="sort-btn bg-white text-text-main border border-gray-200 shrink-0 px-3 py-1.5 rounded-full text-xs font-bold hover:border-primary hover:text-primary transition-all" data-sort="rating">Top Rated</button>
                <button class="sort-btn bg-white text-text-main border border-gray-200 shrink-0 px-3 py-1.5 rounded-full text-xs font-bold hover:border-primary hover:text-primary transition-all" data-sort="price_asc">Price ↑</button>
                <button class="sort-btn bg-white text-text-main border border-gray-200 shrink-0 px-3 py-1.5 rounded-full text-xs font-bold hover:border-primary hover:text-primary transition-all" data-sort="price_desc">Price ↓</button>
            </div>
        </div>`;
    }

    // ============================================================
    // BUILD DYNAMIC FILTER OPTIONS FROM DATA
    // ============================================================
    function buildFilterOptions(cfg) {
        const types = [...new Set(allItems.map(i => i.type).filter(Boolean))];
        const priceKey = getPriceKey(cfg.category);
        const prices = allItems.map(i => parseFloat(i[priceKey])).filter(p => !isNaN(p) && p > 0);

        // Type checkboxes
        const section = document.getElementById('type-filter-section');
        const optionsEl = document.getElementById('type-filter-options');
        if (section && optionsEl && types.length > 0) {
            section.classList.remove('hidden');
            optionsEl.innerHTML = types.map(type => `
                <label class="flex items-center gap-2 cursor-pointer group">
                    <input type="checkbox" class="filter-type-cb w-4 h-4 rounded text-primary accent-primary cursor-pointer" value="${type}"/>
                    <span class="text-sm text-text-main group-hover:text-primary transition-colors">${type}</span>
                </label>
            `).join('');
        }

        // Auto-fill max price hint
        if (prices.length > 0) {
            const maxP = Math.ceil(Math.max(...prices));
            const maxInput = document.getElementById('filter-max-price');
            if (maxInput && maxInput.placeholder === 'Max') {
                maxInput.placeholder = `Max (₹${maxP.toLocaleString('en-IN')})`;
            }
        }
    }

    // ============================================================
    // BIND FILTER EVENTS
    // ============================================================
    function bindFilterEvents(cfg) {
        // ── Location / Name search input ──────────────────────────
        const searchInput = document.getElementById('search-location');
        if (searchInput) {
            // Live filter on every keystroke
            searchInput.addEventListener('input', () => {
                visibleLimit = 5; // reset to first page on new search
                activeFilters.search = searchInput.value.trim();
                renderResults(cfg);
            });
            // Also trigger on Enter key
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    visibleLimit = 5;
                    activeFilters.search = searchInput.value.trim();
                    renderResults(cfg);
                }
            });
        }

        // Hook the hero SEARCH button to also apply the text filter
        const heroSearchBtn = document.querySelector('.search-btn-modern');
        if (heroSearchBtn && searchInput) {
            heroSearchBtn.addEventListener('click', () => {
                visibleLimit = 5;
                activeFilters.search = searchInput.value.trim();
                renderResults(cfg);
            });
        }

        // Popular
        const popularCb = document.getElementById('filter-popular');
        if (popularCb) {
            popularCb.addEventListener('change', () => {
                activeFilters.popularOnly = popularCb.checked;
                renderResults(cfg);
            });
        }

        // Rating slider
        const ratingInput = document.getElementById('filter-rating');
        if (ratingInput) {
            ratingInput.addEventListener('input', () => {
                const val = parseFloat(ratingInput.value);
                activeFilters.minRating = val;
                updateRatingDisplay(val);
                renderResults(cfg);
            });
        }

        // Type checkboxes (delegated — options added dynamically)
        document.addEventListener('change', e => {
            if (e.target.classList.contains('filter-type-cb')) {
                activeFilters.types = [...document.querySelectorAll('.filter-type-cb:checked')].map(cb => cb.value);
                renderResults(cfg);
            }
        });

        // Price range
        const minPrice = document.getElementById('filter-min-price');
        const maxPrice = document.getElementById('filter-max-price');
        function applyPriceFilter() {
            activeFilters.minPrice = parseFloat(minPrice?.value) || 0;
            activeFilters.maxPrice = parseFloat(maxPrice?.value) || Infinity;
            renderResults(cfg);
        }
        if (minPrice) minPrice.addEventListener('change', applyPriceFilter);
        if (maxPrice) maxPrice.addEventListener('change', applyPriceFilter);

        // Sort buttons
        document.querySelectorAll('.sort-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                activeSort = btn.dataset.sort;
                document.querySelectorAll('.sort-btn').forEach(b => {
                    b.classList.remove('bg-primary', 'text-white', 'shadow-sm');
                    b.classList.add('bg-white', 'text-text-main', 'border', 'border-gray-200');
                });
                btn.classList.add('bg-primary', 'text-white', 'shadow-sm');
                btn.classList.remove('bg-white', 'text-text-main', 'border', 'border-gray-200');
                renderResults(cfg);
            });
        });

        // Mobile filter toggle
        const mobileFilterBtn = document.getElementById('mobile-filter-btn');
        const sidebar = document.getElementById('listing-sidebar');
        const closeBtn = document.getElementById('close-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('hidden', '-translate-x-full');
            if (overlay) { overlay.classList.remove('hidden'); overlay.classList.add('opacity-100'); }
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('hidden');
            if (overlay) { overlay.classList.add('hidden'); overlay.classList.remove('opacity-100'); }
            document.body.style.overflow = '';
        }

        if (mobileFilterBtn) mobileFilterBtn.addEventListener('click', openSidebar);
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);
    }

    function updateRatingDisplay(val) {
        const el = document.getElementById('filter-rating-display');
        if (el) el.textContent = val > 0 ? `${val}+ stars` : 'Any';
    }

    // ============================================================
    // SKELETON LOADER
    // ============================================================
    function showSkeleton(container) {
        container.innerHTML = Array(3).fill(0).map(() => `
            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 animate-pulse">
                <div class="w-full md:w-64 h-44 bg-gray-200 rounded-lg shrink-0"></div>
                <div class="flex-1 space-y-3 py-2">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                    <div class="h-3 bg-gray-100 rounded w-2/3"></div>
                    <div class="h-8 bg-gray-200 rounded w-40 mt-4"></div>
                </div>
            </div>`).join('');
    }

    // ============================================================
    // CARD RENDERERS
    // ============================================================
    function renderCard(item, category) {
        const getDetailUrl = (cat) => {
            const m = {
                stays: 'stay-detail.html',
                cars: 'car-rental-detail.html',
                bikes: 'bike-rental-detail.html',
                restaurants: 'restaurant-detail.html',
                attractions: 'attraction-detail.html',
                buses: 'bus-detail.html'
            };
            return m[cat] ? `${m[cat]}?id=${item.id}` : '#';
        };

        const pVal = item.price || item.dailyRate || item.pricePerPerson || item.entryFee || 0;
        const priceStr = pVal > 0 ? `₹${Number(pVal).toLocaleString('en-IN')}` : 'Free';
        const perUnit = category === 'stays' ? 'night' : (category === 'cars' || category === 'bikes') ? 'day' : 'person';

        // Define lines dynamically
        let lines = [];
        let tag = '';

        if (category === 'stays') {
            if (item.location) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>`);
            if (item.description) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span><span class="line-clamp-1">${item.description}</span></p>`);
            lines.push(`<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">check_circle</span>Free Cancellation</p>`);
            tag = 'Hotel Stay';
        } else if (category === 'cars') {
            if (item.provider) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">directions_car</span>By ${item.provider}</p>`);
            if (item.location) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>`);
            lines.push(`<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">group</span>${item.passengers || 4} seats • ${item.transmission || 'Auto'}</p>`);
            tag = item.type || 'Standard';
        } else if (category === 'bikes') {
            if (item.type) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">two_wheeler</span>${item.type}</p>`);
            if (item.description) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span><span class="line-clamp-1">${item.description}</span></p>`);
            if (item.features) lines.push(`<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">build</span>${item.features.split(',')[0]}</p>`);
            tag = 'Bike Rental';
        } else if (category === 'restaurants') {
            if (item.cuisine) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">restaurant_menu</span>${item.cuisine}</p>`);
            if (item.location) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>`);
            if (item.description) lines.push(`<p class="text-xs text-text-muted mt-0.5 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span><span class="line-clamp-1">${item.description}</span></p>`);
            tag = 'Dining';
        } else if (category === 'attractions') {
            if (item.location) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>`);
            if (item.duration) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">schedule</span>Duration: ${item.duration}</p>`);
            if (item.description) lines.push(`<p class="text-xs text-text-muted mt-0.5 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">info</span><span class="line-clamp-1">${item.description}</span></p>`);
            tag = 'Tourist Spot';
        } else if (category === 'buses') {
            if (item.route) lines.push(`<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">route</span>${item.route}</p>`);
            if (item.departure) lines.push(`<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">schedule</span>Departure: ${item.departure}</p>`);
            if (item.duration) lines.push(`<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">timer</span>Duration: ${item.duration}</p>`);
            tag = item.type || 'Volvo AC';
        }

        const tagHtml = tag ? `<span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">${tag}</span>` : '';
        const titleName = item.name || item.route || 'Listing';
        const detailUrl = getDetailUrl(category);

        return `
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group cursor-pointer"
            data-id="${item.id}" data-title="${titleName}" data-price="${pVal}" data-category="${category}" data-detail-url="${detailUrl}">
            ${item.image && item.image !== 'Array' && item.image.trim() !== '' ? `
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5">
                <img alt="${titleName}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="${item.image}" onerror="this.parentElement.style.display='none'"/>
                ${item.badge ? `<div class="absolute top-2 left-2"><span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">${item.badge}</span></div>` : ''}
            </div>
            ` : ''}
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${titleName}</h3>
                        ${lines.join('\n                        ')}
                        ${tagHtml}
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <p class="text-xs text-text-muted">from <span class="text-xl font-black text-text-main">${priceStr}</span> / ${perUnit}</p>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <button data-book-now data-service-page="${getDetailUrl(category).split('?')[0]}" data-listing-id="${item.id}" data-listing-name="${titleName}" data-listing-image="${item.image || ''}" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">event</span>
                            Book Now
                        </button>
                        <a href="tel:${PHONE}" onclick="event.stopPropagation()" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">call</span>
                            Call Now
                        </a>
                    </div>
                </div>
            </div>
        </div>`;
    }


    // ============================================================
    // UTILITY COMPONENTS
    // ============================================================
    function ratingBadge(item) {
        if (!item.rating) return '<div class="text-right shrink-0"></div>';
        const ratingLabel = parseFloat(item.rating) >= 9 ? 'Exceptional' : (parseFloat(item.rating) >= 8 ? 'Excellent' : 'Good');
        const reviewsHtml = item.reviews ? '<p class="text-[10px] text-text-muted">' + item.reviews + ' reviews</p>' : '';
        return `
        <div class="text-right shrink-0">
            <div class="flex items-center justify-end gap-1 mb-1">
                <span class="bg-primary text-white text-xs font-bold px-1.5 py-0.5 rounded">${item.rating}</span>
                <span class="text-xs font-bold text-text-main">${ratingLabel}</span>
            </div>
            ${reviewsHtml}
        </div>`;
    }

    function contactButtons(item, category, detailPage) {
        const titleName = item.name || item.route || 'Listing';
        return `
        <div class="flex gap-2 w-full sm:w-auto mt-3 sm:mt-0">
            <button data-book-now data-service-page="${detailPage}" data-listing-id="${item.id}" data-listing-name="${titleName}" data-listing-image="${item.image || ''}" onclick="event.stopPropagation()" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[16px]">event</span>
                Book Now
            </button>
            <a href="tel:${PHONE}" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm" onclick="event.stopPropagation()">
                <span class="material-symbols-outlined text-[16px]">call</span>
                Call Now
            </a>
        </div>`;
    }

})();
