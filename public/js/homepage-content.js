/**
 * homepage-content.js
 * Handles fetching dynamic homepage content from the API
 * and rendering it into the placeholders on index.html
 */

document.addEventListener('DOMContentLoaded', async () => {
    try {
        const res = await fetch('/api/homepage-content');
        if (!res.ok) throw new Error('Failed to fetch homepage data');
        const hpData = await res.json();

        renderHero(hpData.hero);
        renderTrendingTransport(hpData.trendingTransport);
        renderRestaurantCircles(hpData.restaurantCircles);
        renderBusRoutes(hpData.busRoutes);
        renderAttractions(hpData.attractions);
        renderBikeRentals(hpData.bikeRentals);
        renderFeaturedRestaurants(hpData.featuredRestaurants);
        renderTravelInsights(hpData.travelInsights);

    } catch (err) {
        console.error('Error loading homepage content:', err);
        // Could show a fallback or error state here if needed
    }
});

function esc(str) {
    return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

// 1. HERO SECTION & CONTACT BAR
function renderHero(hero) {
    if (!hero) return;

    // Contact Bar
    if (hero.phone) {
        const phoneLink = document.getElementById('hp-phone-link');
        const phoneText = document.getElementById('hp-phone-text');
        if (phoneLink) phoneLink.href = `tel:${esc(hero.phone).replace(/\s+/g, '')}`;
        if (phoneText) phoneText.textContent = hero.phone;
    }
    if (hero.whatsapp) {
        const waLink = document.getElementById('hp-wa-link');
        if (waLink) waLink.href = `https://wa.me/${esc(hero.whatsapp).replace(/\s+/g, '')}`;
    }

    // Marquee
    const marquee = document.getElementById('hp-marquee');
    if (marquee && hero.marqueeItems && hero.marqueeItems.length > 0) {
        // Double it for seamless loop
        const itemsHtml = hero.marqueeItems.map(item => `<span class="mx-4">${esc(item)}</span>`).join('');
        const hiddenHtml = hero.marqueeItems.map(item => `<span class="mx-4" aria-hidden="true">${esc(item)}</span>`).join('');
        marquee.innerHTML = itemsHtml + hiddenHtml;
    }

    // Hero Title & Description
    if (window.updateHeroDynamicContent) {
        window.updateHeroDynamicContent(hero);
    } else {
        const heroTitle = document.getElementById('hero-title');
        const heroDesc = document.getElementById('hero-desc');
        if (heroTitle && hero.title) heroTitle.innerHTML = hero.title;
        if (heroDesc && hero.description) heroDesc.textContent = hero.description;
    }

    // Carousel Backgrounds — slides are divs with background-image (not <img>)
    if (hero.carousel) {
        ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'].forEach(key => {
            const slide = document.getElementById(`carousel-${key}`);
            if (slide && hero.carousel[key]) {
                slide.style.backgroundImage = `linear-gradient(to bottom, rgba(0, 53, 128, 0.8), rgba(0, 53, 128, 0.4)), url('${hero.carousel[key]}')`;
            }
        });
    }
}

// 2. TRENDING TRANSPORT
function renderTrendingTransport(items) {
    const container = document.getElementById('trending-transport-grid');
    if (!container || !items) return;

    container.innerHTML = items.map((item, i) => `
        <div class="bg-white rounded-lg shadow-soft overflow-hidden flex flex-row h-full min-h-[200px] cursor-pointer group hover:shadow-card transition-shadow">
            <div class="p-6 flex flex-col justify-between flex-1 z-10 relative bg-white/90 backdrop-blur-sm sm:bg-transparent sm:backdrop-blur-none">
                <div>
                    <h3 class="text-xl font-bold text-text-main mb-1 line-clamp-2">${esc(item.title)}</h3>
                    <p class="text-sm text-text-muted line-clamp-2">${esc(item.description)}</p>
                </div>
                <div class="mt-auto pt-4">
                    <p class="text-xs text-text-muted mb-1">${esc(item.subtext)}</p>
                    <a href="${esc(item.link)}" class="mt-1 text-sm font-bold text-primary group-hover:underline flex items-center gap-1">
                        ${esc(item.linkText)} <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </a>
                </div>
            </div>
            <div class="w-1/2 relative shrink-0">
                <img class="absolute inset-0 w-full h-full object-cover" alt="${esc(item.title)}" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                <div class="absolute inset-0 bg-gradient-to-r from-white via-white/40 to-transparent sm:via-transparent"></div>
            </div>
        </div>
    `).join('');
}

// 3. TASTE THE CITY (RESTAURANT CIRCLES)
function renderRestaurantCircles(items) {
    const container = document.getElementById('restaurant-circles-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => `
        <a href="${esc(item.link)}" class="flex flex-col items-center gap-2 min-w-[120px] snap-center snap-item cursor-pointer group">
            <div class="relative w-28 h-28 rounded-full shadow-md overflow-hidden ring-2 ring-white group-hover:ring-primary transition-all">
                <img class="w-full h-full object-cover" alt="${esc(item.name)}" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                <div class="absolute bottom-0 inset-x-0 h-1/2 bg-gradient-to-t from-black/60 to-transparent"></div>
                ${item.rating ? `<div class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">${esc(item.rating)}</div>` : ''}
            </div>
            <div class="text-center">
                <h3 class="text-sm font-bold text-text-main group-hover:text-primary">${esc(item.name)}</h3>
                <p class="text-xs text-text-muted">${esc(item.type)}</p>
            </div>
        </a>
    `).join('');
}

// 4. TRAVEL YOUR WAY (BUS ROUTES)
function renderBusRoutes(items) {
    const container = document.getElementById('bus-routes-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => `
        <a href="${esc(item.link)}" class="bg-white p-4 rounded-lg shadow-soft border border-gray-100 hover:shadow-card transition-all cursor-pointer group flex flex-col h-full">
            <div class="flex justify-between items-start mb-4">
                <div class="flex gap-3 items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-primary shrink-0">
                        <span class="material-symbols-outlined">directions_bus</span>
                    </div>
                    <div>
                        <h3 class="font-bold text-text-main">${esc(item.from)} <span class="text-gray-400 mx-1">→</span> ${esc(item.to)}</h3>
                        <p class="text-xs text-text-muted whitespace-nowrap overflow-hidden text-ellipsis w-[180px] max-w-full">Direct • ${esc(item.provider)}</p>
                    </div>
                </div>
                ${item.badge ? `<span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider shrink-0">${esc(item.badge)}</span>` : ''}
            </div>
            <div class="flex justify-between items-end mt-auto pt-2 border-t border-gray-50">
                <div>
                    <p class="text-sm font-medium text-text-main">${esc(item.duration)}</p>
                    <p class="text-xs text-text-muted">Departs ${esc(item.departure)}</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-bold text-text-main group-hover:text-primary transition-colors">${esc(item.price)}</p>
                </div>
            </div>
        </a>
    `).join('');
}

// 5. EXPLORE ATTRACTIONS
function renderAttractions(items) {
    const container = document.getElementById('attractions-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => `
        <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft border border-gray-100 overflow-hidden cursor-pointer group hover:shadow-card transition-all flex flex-col h-full">
            <div class="h-48 shrink-0 overflow-hidden relative">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${esc(item.name)}" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                ${item.rating ? `
                <div class="absolute top-2 right-2 bg-white/90 backdrop-blur text-xs font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-yellow-500 text-[14px] fill-current">star</span>
                    ${esc(item.rating)}
                </div>` : ''}
            </div>
            <div class="p-4 flex flex-col flex-1">
                <h3 class="font-bold text-text-main mb-1 line-clamp-2">${esc(item.name)}</h3>
                <p class="text-xs text-text-muted flex items-center gap-1 mt-auto pt-2">
                    <span class="material-symbols-outlined text-[14px] shrink-0">location_on</span> <span class="line-clamp-1">${esc(item.location)}</span>
                </p>
            </div>
        </a>
    `).join('');
}

// 6. QUICK BIKE RENTALS
function renderBikeRentals(items) {
    const container = document.getElementById('bike-rentals-grid');
    if (!container || !items) return;

    // Using global static hero whatsapp/phone for these CTAs 
    // This allows the hero section edits to globally update contact numbers
    const contactPhone = document.getElementById('hp-phone-text')?.textContent || '+91 86009 68888';

    container.innerHTML = items.map(item => `
        <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft border border-gray-100 p-4 flex flex-col hover:shadow-card transition-all h-full text-center">
            <div class="w-full h-32 shrink-0 bg-gray-50 rounded mb-4 flex items-center justify-center overflow-hidden">
                <img alt="${esc(item.name)}" class="w-full h-full object-cover" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
            </div>
            <h3 class="text-lg font-bold text-text-main mb-1 line-clamp-1">${esc(item.name)}</h3>
            <p class="text-sm text-text-muted mb-3 line-clamp-2">${esc(item.description)}</p>
            <div class="mt-auto w-full flex items-center justify-between border-t border-gray-100 pt-3">
                <div class="flex flex-row gap-2 w-full mt-1">
                    <object class="flex-1 min-w-0"><a href="tel:${esc(contactPhone).replace(/\s+/g, '')}" class="w-full flex items-center justify-center gap-1 bg-primary text-white text-[11px] sm:text-xs font-bold px-2 py-1.5 rounded hover:bg-primary-hover transition-colors shadow-sm whitespace-nowrap">
                        <span class="material-symbols-outlined text-[14px] sm:text-[16px]">call</span> Call
                    </a></object>
                    <object class="flex-1 min-w-0"><a href="https://wa.me/${esc(contactPhone).replace(/\D/g, '')}" target="_blank" class="w-full flex items-center justify-center gap-1 bg-[#25D366] text-white text-[11px] sm:text-xs font-bold px-2 py-1.5 rounded hover:bg-[#128C7E] transition-colors shadow-sm whitespace-nowrap">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="w-3 h-3 sm:w-4 sm:h-4 brightness-0 invert"> Chat
                    </a></object>
                </div>
            </div>
        </a>
    `).join('');
}

// 7. FEATURED RESTAURANTS
function renderFeaturedRestaurants(items) {
    const container = document.getElementById('featured-restaurants-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => {
        const tagsHtml = (item.tags || []).map(t => `<span class="bg-blue-50 text-primary text-[10px] font-bold px-2 py-1 rounded whitespace-nowrap">${esc(t)}</span>`).join('');
        return `
        <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft overflow-hidden border border-gray-100 flex flex-col group hover:shadow-card transition-all h-full">
            <div class="h-48 shrink-0 relative overflow-hidden">
                <img alt="${esc(item.name)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                ${item.rating ? `<div class="absolute top-3 right-3 bg-primary text-white text-xs font-bold px-2 py-1 rounded">${esc(item.rating)}</div>` : ''}
            </div>
            <div class="p-5 flex flex-col flex-1">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main line-clamp-1">${esc(item.name)}</h3>
                        <p class="text-sm text-text-muted line-clamp-1">${esc(item.type)}</p>
                    </div>
                </div>
                <p class="text-sm text-text-muted line-clamp-2 mb-4">${esc(item.description)}</p>
                <div class="flex flex-wrap gap-2 mt-auto pt-2 border-t border-gray-50">
                    ${tagsHtml}
                </div>
            </div>
        </a>
        `;
    }).join('');
}

// 8. TRAVEL INSIGHTS
function renderTravelInsights(items) {
    const container = document.getElementById('travel-insights-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => `
        <div class="group cursor-pointer flex flex-col h-full bg-white rounded-lg shadow-sm border border-gray-100 p-3 hover:shadow-md transition-shadow">
            <div class="rounded-lg shrink-0 overflow-hidden h-[180px] mb-3 relative">
                <img alt="${esc(item.title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
            </div>
            <div class="flex items-center gap-2 mb-2 text-xs text-text-muted font-medium">
                <span class="text-primary font-bold">${esc(item.category)}</span>
                <span>•</span>
                <span>${esc(item.readTime)}</span>
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2 leading-snug group-hover:text-primary transition-colors line-clamp-2">${esc(item.title)}</h3>
            <p class="text-sm text-text-muted line-clamp-2 mt-auto">${esc(item.description)}</p>
        </div>
    `).join('');
}
