/**
 * listings.js — Shared dynamic listing engine for TravelHub
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
    document.addEventListener('DOMContentLoaded', async () => {
        const cfg = window.LISTING_CONFIG;
        if (!cfg) return;

        injectFilterSidebar(cfg);
        injectSortBar(cfg);
        await fetchAndRender(cfg);
        bindFilterEvents(cfg);
    });

    // ============================================================
    // FETCH
    // ============================================================
    async function fetchAndRender(cfg) {
        const container = document.getElementById(cfg.containerId);
        if (!container) return;

        showSkeleton(container);

        try {
            const res = await fetch(`/api/${cfg.category}`);
            if (!res.ok) throw new Error('Network error');
            allItems = await res.json();

            // Build filter options dynamically from data
            buildFilterOptions(cfg);
            // Initial render
            renderResults(cfg);
        } catch (err) {
            console.error('Listing fetch error:', err);
            container.innerHTML = `
                <div class="text-center py-16">
                    <span class="material-symbols-outlined text-[48px] text-gray-300 mb-3">cloud_off</span>
                    <p class="text-gray-400 text-sm">Could not load listings. Please try again.</p>
                </div>`;
        }
    }

    // ============================================================
    // FILTER + SORT LOGIC
    // ============================================================
    function getFiltered() {
        let items = [...allItems];
        const f = activeFilters;

        // Search
        if (f.search) {
            const q = f.search.toLowerCase();
            items = items.filter(item =>
                (item.name || '').toLowerCase().includes(q) ||
                (item.location || '').toLowerCase().includes(q) ||
                (item.description || '').toLowerCase().includes(q) ||
                (item.type || '').toLowerCase().includes(q) ||
                (item.cuisine || '').toLowerCase().includes(q)
            );
        }

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
                // Badges first, then by rating
                items.sort((a, b) => {
                    if (b.badge && !a.badge) return 1;
                    if (a.badge && !b.badge) return -1;
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
                    <span class="material-symbols-outlined text-[48px] text-gray-300 mb-3">search_off</span>
                    <p class="text-gray-500 font-semibold">No listings match your filters.</p>
                    <button onclick="resetFilters()" class="mt-4 text-primary text-sm font-bold hover:underline">Clear all filters</button>
                </div>`;
            return;
        }

        container.innerHTML = items.map(item => renderCard(item, cfg.category)).join('');
    }

    window.resetFilters = function () {
        activeFilters = { search: '', types: [], minRating: 0, minPrice: 0, maxPrice: Infinity, popularOnly: false };
        activeSort = 'recommended';

        // Reset UI
        document.querySelectorAll('.filter-type-cb').forEach(cb => cb.checked = false);
        const searchInput = document.getElementById('filter-search');
        if (searchInput) searchInput.value = '';
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
                <!-- Search -->
                <div class="p-4 border-b border-gray-100">
                    <label class="block text-xs font-bold text-text-muted uppercase tracking-wider mb-2">Search</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <span class="material-symbols-outlined text-[18px]">search</span>
                        </span>
                        <input id="filter-search" type="text" placeholder="Name, location…"
                            class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"/>
                    </div>
                </div>

                <!-- Popular Only -->
                <div class="p-4 border-b border-gray-100">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input id="filter-popular" type="checkbox"
                            class="w-4 h-4 rounded text-primary accent-primary cursor-pointer"/>
                        <span class="text-sm font-semibold text-text-main group-hover:text-primary transition-colors">
                            ⭐ Popular / Bestsellers only
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
        // Search (debounced)
        const searchInput = document.getElementById('filter-search');
        if (searchInput) {
            let debounce;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounce);
                debounce = setTimeout(() => {
                    activeFilters.search = searchInput.value.trim();
                    renderResults(cfg);
                }, 300);
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
        if (el) el.textContent = val > 0 ? `${val}+ ⭐` : 'Any';
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
        switch (category) {
            case 'stays': return stayCard(item);
            case 'cars': return carCard(item);
            case 'bikes': return bikeCard(item);
            case 'restaurants': return restaurantCard(item);
            case 'attractions': return attractionCard(item);
            case 'buses': return busCard(item);
            default: return '';
        }
    }

    function contactButtons() {
        return `
        <div class="flex gap-2 mt-3">
            <a href="tel:${PHONE}"
                class="flex-1 flex items-center justify-center gap-1.5 bg-primary text-white font-bold px-3 py-2 text-sm rounded-lg hover:bg-primary-hover transition-colors shadow-sm">
                <span class="material-symbols-outlined text-[18px]">call</span> Call
            </a>
            <a href="${WHATSAPP_URL}" target="_blank" rel="noopener"
                class="flex-1 flex items-center justify-center gap-1.5 bg-[#25D366] text-white font-bold px-3 py-2 text-sm rounded-lg hover:bg-[#128C7E] transition-colors shadow-sm">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" class="w-4 h-4 brightness-0 invert" alt="wa"/>
                WhatsApp
            </a>
        </div>`;
    }

    function ratingBadge(item) {
        const r = parseFloat(item.rating) || 0;
        const label = r >= 9 ? 'Superb' : r >= 8 ? 'Very Good' : r >= 7 ? 'Good' : r >= 5 ? 'Pleasant' : r > 0 ? 'New' : '—';
        return r > 0 ? `
            <div class="flex items-center gap-1.5">
                <div class="text-right">
                    <p class="text-sm font-bold text-text-main">${label}</p>
                    <p class="text-[11px] text-text-muted">${item.reviews ? item.reviews + ' reviews' : 'reviews'}</p>
                </div>
                <div class="w-9 h-9 rounded-lg bg-primary text-white flex items-center justify-center font-bold text-sm">${r}</div>
            </div>` : '';
    }

    function starIcons(rating) {
        const stars = Math.round(parseFloat(rating) || 0);
        return Array(5).fill(0).map((_, i) =>
            `<span class="material-symbols-outlined text-[15px] ${i < stars ? 'text-yellow-400' : 'text-gray-300'}"
              style="${i < stars ? 'font-variation-settings:\'FILL\' 1' : ''}">star</span>`
        ).join('');
    }

    function badgeHtml(item) {
        return item.badge ? `<span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">${item.badge}</span>` : '';
    }

    // ---- Stay Card ----
    function stayCard(item) {
        return `
        <a href="stay-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl shadow-soft p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow block group"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.price || 0}" data-category="stays">
            <div class="w-full md:w-64 h-48 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-gray-100">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                <button class="absolute top-2 right-2 w-8 h-8 rounded-full bg-white/90 backdrop-blur flex items-center justify-center hover:bg-white transition-colors shadow-sm text-gray-400 hover:text-red-500 z-10"
                    onclick="event.preventDefault()">
                    <span class="material-symbols-outlined text-[18px]">favorite</span>
                </button>
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex flex-col md:flex-row justify-between items-start gap-2 mb-2">
                    <div>
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <h3 class="text-lg font-bold text-text-main leading-tight">${item.name}</h3>
                            <div class="flex">${starIcons(item.rating)}</div>
                        </div>
                        <p class="text-sm text-primary font-bold mb-1">${item.location || ''}</p>
                        <p class="text-xs text-text-muted line-clamp-2">${item.description || ''}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1 shrink-0">
                        ${ratingBadge(item)}
                        ${badgeHtml(item)}
                    </div>
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row justify-between items-end gap-3">
                    <div class="flex items-center gap-1.5 text-xs text-green-700 font-bold">
                        <span class="material-symbols-outlined text-[15px]">check_circle</span> Free Cancellation
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        ${item.price ? `<p class="text-xs text-text-muted">from <span class="text-lg font-black text-text-main">₹${Number(item.price).toLocaleString('en-IN')}</span> / night</p>` : ''}
                        ${contactButtons()}
                    </div>
                </div>
            </div>
        </a>`;
    }

    // ---- Car Card ----
    function carCard(item) {
        const price = item.dailyRate || item.price;
        return `
        <a href="car-rental-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-6 hover:shadow-card transition-shadow block group"
            data-id="${item.id}" data-title="${item.name}" data-price="${price || 0}" data-category="cars">
            <div class="md:w-64 shrink-0 flex flex-col gap-2">
                <div class="relative aspect-[16/10] rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                    <img alt="${item.name}" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105"
                        src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                    ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
                </div>
                <div class="flex items-center justify-between px-1">
                    ${ratingBadge(item)}
                </div>
            </div>
            <div class="flex-grow flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-1">${item.name}</h3>
                        ${item.location ? `<p class="text-xs text-text-muted flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">location_on</span>${item.location}</p>` : ''}
                        ${item.provider ? `<p class="text-xs text-primary font-semibold mt-1">By ${item.provider}</p>` : ''}
                    </div>
                    <div class="bg-blue-50 text-primary text-[10px] font-bold px-2 py-1 rounded-lg shrink-0">${item.type || 'Standard'}</div>
                </div>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 mt-2">
                    <div class="flex items-center gap-1.5 text-sm text-text-main">
                        <span class="material-symbols-outlined text-[16px] text-text-muted">person</span>${item.passengers || 4} seats
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-text-main">
                        <span class="material-symbols-outlined text-[16px] text-text-muted">work</span>2 bags
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-text-main">
                        <span class="material-symbols-outlined text-[16px] text-text-muted">settings</span>${item.transmission || 'Automatic'}
                    </div>
                    <div class="flex items-center gap-1.5 text-sm text-text-main">
                        <span class="material-symbols-outlined text-[16px] text-text-muted">ac_unit</span>A/C
                    </div>
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${price ? `<p class="text-xs text-text-muted">from <span class="text-xl font-black text-text-main">₹${Number(price).toLocaleString('en-IN')}</span> / day</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }

    // ---- Bike Card ----
    function bikeCard(item) {
        const price = item.dailyRate || item.price;
        return `
        <a href="bike-rental-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-6 hover:shadow-card transition-shadow block group"
            data-id="${item.id}" data-title="${item.name}" data-price="${price || 0}" data-category="bikes">
            <div class="md:w-56 shrink-0">
                <div class="relative aspect-[4/3] rounded-lg overflow-hidden bg-gray-50 flex items-center justify-center">
                    <img alt="${item.name}" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105"
                        src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                    ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
                </div>
            </div>
            <div class="flex-grow flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-1">${item.name}</h3>
                        ${item.type ? `<span class="bg-orange-50 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded-full">${item.type}</span>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-2 line-clamp-2">${item.description}</p>` : ''}
                    </div>
                    ${ratingBadge(item)}
                </div>
                ${item.features ? `
                <div class="flex flex-wrap gap-1.5 mt-2">
                    ${item.features.split(',').map(f => `<span class="bg-gray-100 text-text-muted text-[11px] px-2 py-0.5 rounded-full">${f.trim()}</span>`).join('')}
                </div>` : ''}
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${price ? `<p class="text-xs text-text-muted">from <span class="text-xl font-black text-text-main">₹${Number(price).toLocaleString('en-IN')}</span> / day</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }

    // ---- Restaurant Card ----
    function restaurantCard(item) {
        return `
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.pricePerPerson || 0}" data-category="restaurants">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-gray-100">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        <div class="flex mb-1">${starIcons(item.rating)}</div>
                        ${item.cuisine ? `<p class="text-xs text-primary font-semibold">${item.cuisine}</p>` : ''}
                        ${item.location ? `<p class="text-xs text-text-muted flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-2 line-clamp-2">${item.description}</p>` : ''}
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${item.pricePerPerson ? `<p class="text-xs text-text-muted">~<span class="text-lg font-black text-text-main">₹${Number(item.pricePerPerson).toLocaleString('en-IN')}</span> / person</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </div>`;
    }

    // ---- Attraction Card ----
    function attractionCard(item) {
        return `
        <a href="attraction-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow block group"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.entryFee || 0}" data-category="attractions">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-gray-100">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        <div class="flex mb-1">${starIcons(item.rating)}</div>
                        ${item.location ? `<p class="text-xs text-text-muted flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.duration ? `<p class="text-xs text-text-muted flex items-center gap-1 mt-1"><span class="material-symbols-outlined text-[13px]">schedule</span>${item.duration}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-2 line-clamp-2">${item.description}</p>` : ''}
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <p class="text-xs text-text-muted">
                        Entry: <span class="text-lg font-black text-text-main">${item.entryFee > 0 ? '₹' + Number(item.entryFee).toLocaleString('en-IN') : 'Free'}</span>
                    </p>
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }

    // ---- Bus Card ----
    function busCard(item) {
        return `
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group"
            data-id="${item.id}" data-title="${item.name || item.route}" data-price="${item.price || 0}" data-category="buses">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                ${item.image
                ? `<img alt="${item.name || item.route}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        src="${item.image}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>`
                : `<span class="material-symbols-outlined text-[64px] text-primary/30">directions_bus</span>`
            }
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name || item.route || 'Bus Route'}</h3>
                        ${item.route ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">route</span>${item.route}</p>` : ''}
                        ${item.departure ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">schedule</span>Departure: ${item.departure}</p>` : ''}
                        ${item.duration ? `<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">timer</span>Duration: ${item.duration}</p>` : ''}
                        ${item.type ? `<span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">${item.type}</span>` : ''}
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${item.price ? `<p class="text-xs text-text-muted">from <span class="text-xl font-black text-text-main">₹${Number(item.price).toLocaleString('en-IN')}</span> / person</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </div>`;
    }

})();
