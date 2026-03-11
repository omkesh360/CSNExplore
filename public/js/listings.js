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
            const res = await fetch(`/api/${cfg.category}`);
            if (!res.ok) throw new Error('Network error');
            allItems = await res.json();

            if (!allItems || allItems.length === 0) {
                allItems = getDemoData(cfg.category);
            }
        } catch (err) {
            console.error('Listing fetch error, falling back to demo data:', err);
            allItems = getDemoData(cfg.category);
        }

        // Build filter options dynamically from data
        buildFilterOptions(cfg);
        // Initial render
        renderResults(cfg);
    }

    function getDemoData(category) {
        switch (category) {
            case 'stays':
                return [
                    { id: 1, name: "Grand Taj Palace", location: "Downtown Mumbai", description: "Luxury 5-star hotel with sea views.", price: 8500, rating: 9.2, reviews: 342, badge: "Bestseller", image: "https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 2, name: "Sunset Beach Resort", location: "North Goa", description: "Private beach access and pool.", price: 4200, rating: 8.5, reviews: 128, image: "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "Mountain View Cabin", location: "Manali", description: "Cozy retreat in the hills.", price: 2100, rating: 7.8, reviews: 64, badge: "Great Value", image: "https://images.unsplash.com/photo-1542314831-c6a4d27ce6a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "City Center Inn", location: "Pune", description: "Affordable stay near the station.", price: 1200, rating: 7.0, reviews: 45, image: "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 5, name: "Desert Camp Oasis", location: "Jaisalmer", description: "Luxury tents in the sand dunes.", price: 5500, rating: 8.8, reviews: 210, badge: "Unique", image: "https://images.unsplash.com/photo-1534152011707-1c667634f509?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "Himalayan Retreat", location: "Shimla", description: "Cozy rooms with snow views.", price: 3200, rating: 8.1, reviews: 95, image: "https://images.unsplash.com/photo-1542314831-c6a4d27ce6a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            case 'cars':
                return [
                    { id: 1, name: "Hyundai Creta", provider: "Zoomcar", location: "Pune Station", dailyRate: 1800, passengers: 5, transmission: "Automatic", type: "SUV", rating: 8.9, reviews: 210, badge: 'Popular', image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 2, name: "Maruti Swift", provider: "Revv", location: "Airport", dailyRate: 1200, passengers: 4, transmission: "Manual", type: "Hatchback", rating: 7.5, reviews: 85, image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "Honda City", provider: "Avis", location: "City Center", dailyRate: 2200, passengers: 5, transmission: "Automatic", type: "Sedan", rating: 9.4, reviews: 430, badge: 'Premium', image: "https://images.unsplash.com/photo-1550355291-bbee04a92027?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "Mahindra Thar", provider: "Zoomcar", location: "North Goa", dailyRate: 3500, passengers: 4, transmission: "Manual", type: "Offroad", rating: 9.0, reviews: 320, badge: 'Adventure', image: "https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 5, name: "Toyota Innova", provider: "Savaari", location: "Mumbai Airport", dailyRate: 2800, passengers: 7, transmission: "Automatic", type: "MUV", rating: 9.1, reviews: 512, badge: 'Family Choice', image: "https://images.unsplash.com/photo-1550355291-bbee04a92027?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "Tata Nexon", provider: "Revv", location: "Pune", dailyRate: 1600, passengers: 5, transmission: "Automatic", type: "SUV", rating: 8.4, reviews: 145, image: "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            case 'bikes':
                return [
                    { id: 1, name: "Royal Enfield Classic 350", type: "Cruiser", description: "Perfect for long highway rides.", features: "2 Helmets Included, Carrier", dailyRate: 800, rating: 9.1, reviews: 312, badge: 'Top Choice', image: "https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 2, name: "Honda Activa 6G", type: "Scooter", description: "Easy and reliable city commuter.", features: "1 Helmet", dailyRate: 400, rating: 8.0, reviews: 150, image: "https://images.unsplash.com/photo-1627834575836-39149bb88ed1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "KTM Duke 200", type: "Sports", description: "Agile performance for enthusiasts.", features: "Full Gear Available", dailyRate: 1100, rating: 8.7, reviews: 92, badge: 'Sporty', image: "https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "Bajaj Avenger 220", type: "Cruiser", description: "Relaxed riding posture.", features: "2 Helmets", dailyRate: 600, rating: 8.2, reviews: 110, image: "https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 5, name: "TVS Jupiter", type: "Scooter", description: "Comfortable family scooter.", features: "1 Helmet, First Aid", dailyRate: 350, rating: 7.8, reviews: 88, image: "https://images.unsplash.com/photo-1627834575836-39149bb88ed1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "Yamaha R15", type: "Sports", description: "Track oriented performance.", features: "Helmet, Gloves", dailyRate: 900, rating: 8.9, reviews: 205, badge: 'Popular', image: "https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            case 'restaurants':
                return [
                    { id: 1, name: "Spice Route", cuisine: "Authentic Indian", location: "Connaught Place", description: "Award-winning North Indian dishes.", pricePerPerson: 1200, rating: 9.3, reviews: 450, badge: 'Recommended', image: "https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 2, name: "Ocean Grill", cuisine: "Seafood & Continental", location: "Baga Beach", description: "Fresh catch of the day by the waves.", pricePerPerson: 1800, rating: 8.8, reviews: 215, image: "https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "Cafe Mocha", cuisine: "Cafe & Desserts", location: "Koregaon Park", description: "Great coffee and relaxed outdoor seating.", pricePerPerson: 600, rating: 8.5, reviews: 330, badge: 'Cozy', image: "https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "The Steakhouse", cuisine: "Continental", location: "Bandra West", description: "Premium steaks and fine wines.", pricePerPerson: 2500, rating: 9.5, reviews: 620, badge: 'Premium', image: "https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 5, name: "Punjabi Dhaba", cuisine: "North Indian", location: "Highway 4", description: "Authentic dhaba food with outdoor seating.", pricePerPerson: 400, rating: 8.2, reviews: 180, image: "https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "Sushi Train", cuisine: "Japanese", location: "City Center Mall", description: "Conveyor belt sushi experience.", pricePerPerson: 1500, rating: 8.6, reviews: 290, image: "https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            case 'attractions':
                return [
                    { id: 1, name: "Taj Mahal Guided Tour", location: "Agra", duration: "3h 00m", description: "Skip-the-line access to the iconic monument of love.", entryFee: 1500, rating: 9.8, reviews: 2450, badge: 'Must See', image: "https://images.unsplash.com/photo-1564507592224-2fc8c61bb2b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 2, name: "Elephanta Caves Trip", location: "Gateway of India", duration: "5h 00m", description: "Ferry ride and guided exploration of ancient caves.", entryFee: 800, rating: 8.4, reviews: 520, image: "https://images.unsplash.com/photo-1627891398124-7bd08a462db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "Desert Safari", location: "Jaisalmer", duration: "6h 30m", description: "Evening dune bashing and cultural campfire dinner.", entryFee: 2500, rating: 9.1, reviews: 340, badge: 'Adventure', image: "https://images.unsplash.com/photo-1534152011707-1c667634f509?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "Amber Fort Visit", location: "Jaipur", duration: "4h 00m", description: "Explore the majestic hilltop fort.", entryFee: 500, rating: 9.0, reviews: 1200, badge: 'Historic', image: "https://images.unsplash.com/photo-1564507592224-2fc8c61bb2b1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 5, name: "Kerala Houseboat", location: "Alleppey", duration: "1 Day", description: "Overnight stay on the peaceful backwaters.", entryFee: 6000, rating: 9.5, reviews: 850, badge: 'Popular', image: "https://images.unsplash.com/photo-1627891398124-7bd08a462db9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "City Museum", location: "Delhi", duration: "2h 30m", description: "Art and history exhibits.", entryFee: 200, rating: 7.9, reviews: 410, image: "https://images.unsplash.com/photo-1534152011707-1c667634f509?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            case 'buses':
                return [
                    { id: 1, name: "Purple Travels Volvo", route: "CSN → Nashik", departure: "05:30 AM", duration: "5h 00m", type: "Volvo AC", price: 420, rating: 8.7, reviews: 88, badge: 'Comfortable', image: "/images/purple-travels-bus.jpg" },
                    { id: 2, name: "Neeta Tours", route: "CSN → Pune", departure: "08:00 PM", duration: "10h 30m", type: "Sleeper Non-AC", price: 650, rating: 7.5, reviews: 140, image: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 3, name: "VRL Travels", route: "CSN → Mumbai", departure: "10:30 PM", duration: "12h 00m", type: "Volvo Multi-Axle", price: 1100, rating: 9.0, reviews: 520, badge: 'Premium', image: "https://images.unsplash.com/photo-1570125909232-eb263c188f7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 4, name: "Konduskar Travels", route: "CSN → Kolhapur", departure: "07:00 PM", duration: "14h 00m", type: "AC Sleeper", price: 1250, rating: 8.5, reviews: 310, image: "/images/purple-travels-bus.jpg" },
                    { id: 5, name: "Prasanna Purple", route: "CSN → Aurangabad", departure: "06:00 AM", duration: "3h 30m", type: "Seater Non-AC", price: 300, rating: 8.1, reviews: 195, image: "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" },
                    { id: 6, name: "Shivneri (MSRTC)", route: "CSN → Pune", departure: "Hourly", duration: "8h 00m", type: "AC Seater", price: 750, rating: 8.8, reviews: 1450, badge: 'Reliable', image: "https://images.unsplash.com/photo-1570125909232-eb263c188f7e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" }
                ];
            default:
                return [];
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
                    <span class="material-symbols-outlined text-[48px] text-gray-300 mb-3">search_off</span>
                    <p class="text-gray-500 font-semibold">No listings match your filters.</p>
                    <button onclick="resetFilters()" class="mt-4 text-primary text-sm font-bold hover:underline">Clear all filters</button>
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
            data-id="${item.id}" data-title="${titleName}" data-price="${pVal}" data-category="${category}" onclick="window.location.href='${detailUrl}'">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                ${item.image ? `<img alt="${titleName}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="${item.image}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>` : `<span class="material-symbols-outlined text-[64px] text-primary/30">image</span>`}
                ${item.badge ? `<div class="absolute top-2 left-2"><span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider">${item.badge}</span></div>` : ''}
            </div>
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
                        <a href="${WHATSAPP_URL}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WA" class="w-4 h-4 filter brightness-0 invert"/>
                            WhatsApp
                        </a>
                        <a href="tel:${PHONE}" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-hover text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm">
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

    function contactButtons() {
        return `
        <div class="flex gap-2 w-full sm:w-auto mt-3 sm:mt-0">
            <a href="${WHATSAPP_URL}" target="_blank" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-green-500 hover:bg-green-600 text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm" onclick="event.stopPropagation()">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WA" class="w-4 h-4 filter brightness-0 invert"/>
                WhatsApp
            </a>
            <a href="tel:${PHONE}" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 bg-primary hover:bg-primary-dark text-white text-xs font-bold py-2 px-4 rounded-lg transition-colors shadow-sm" onclick="event.stopPropagation()">
                <span class="material-symbols-outlined text-[16px]">call</span>
                Call Now
            </a>
        </div>`;
    }

})();
