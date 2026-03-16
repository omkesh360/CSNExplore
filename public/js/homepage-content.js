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

        const v = hpData.visibility || {};
        const t = hpData.sectionTitles || {};

        if (v.trendingTransport === false) document.getElementById('section-trending-transport')?.remove();
        else {
            if (t.trendingTransport && document.getElementById('title-trending-transport')) document.getElementById('title-trending-transport').textContent = t.trendingTransport;
            renderTrendingTransport(hpData.trendingTransport);
        }

        if (v.restaurantCircles === false) document.getElementById('section-taste-city')?.remove();
        else {
            if (t.restaurantCircles && document.getElementById('title-restaurant-circles')) document.getElementById('title-restaurant-circles').textContent = t.restaurantCircles;
            renderRestaurantCircles(hpData.restaurantCircles);
        }

        if (v.busRoutes === false) document.getElementById('section-bus-routes')?.remove();
        else {
            if (t.busRoutes && document.getElementById('title-bus-routes')) document.getElementById('title-bus-routes').textContent = t.busRoutes;
            renderBusRoutes(hpData.busRoutes);
        }

        if (v.attractions === false) document.getElementById('section-attractions')?.remove();
        else {
            if (t.attractions && document.getElementById('title-attractions')) document.getElementById('title-attractions').textContent = t.attractions;
            renderAttractions(hpData.attractions);
        }

        if (v.bikeRentals === false) document.getElementById('section-bike-rentals')?.remove();
        else {
            if (t.bikeRentals && document.getElementById('title-bike-rentals')) document.getElementById('title-bike-rentals').textContent = t.bikeRentals;
            renderBikeRentals(hpData.bikeRentals);
        }

        if (v.featuredRestaurants === false) document.getElementById('section-featured-restaurants')?.remove();
        else {
            if (t.featuredRestaurants && document.getElementById('title-featured-restaurants')) document.getElementById('title-featured-restaurants').textContent = t.featuredRestaurants;
            renderFeaturedRestaurants(hpData.featuredRestaurants);
        }

        if (v.travelInsights === false) document.getElementById('section-travel-insights')?.remove();
        else {
            if (t.travelInsights && document.getElementById('title-travel-insights')) document.getElementById('title-travel-insights').textContent = t.travelInsights;
            // Fetch live blogs from the blogs API, fall back to static data
            try {
                const blogsRes = await fetch('/api/blogs');
                if (blogsRes.ok) {
                    const allBlogs = await blogsRes.json();
                    const publishedBlogs = allBlogs.filter(b => b.status === 'published').slice(0, 3);
                    renderTravelInsights(publishedBlogs.length > 0 ? publishedBlogs : hpData.travelInsights, true);
                } else {
                    renderTravelInsights(hpData.travelInsights, false);
                }
            } catch (blogsErr) {
                renderTravelInsights(hpData.travelInsights, false);
            }
        }

        // Apply section order from admin drag-drop
        if (hpData.sectionOrder && hpData.sectionOrder.length > 0) {
            const container = document.getElementById('homepage-main');
            if (container) {
                // Map admin section IDs to homepage DOM section IDs
                const sectionMap = {
                    trendingTransport: 'section-trending-transport',
                    restaurantCircles: 'section-taste-city',
                    busRoutes: 'section-bus-routes',
                    attractions: 'section-attractions',
                    bikeRentals: 'section-bike-rentals',
                    featuredRestaurants: 'section-featured-restaurants',
                    travelInsights: 'section-travel-insights',
                };
                hpData.sectionOrder.forEach(sid => {
                    const domId = sectionMap[sid];
                    if (domId) {
                        const el = document.getElementById(domId);
                        if (el) container.appendChild(el);
                    }
                });
            }
        }

    } catch (err) {
        console.error('Error loading homepage content:', err);
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

    // Separate cars and bikes
    const cars = items.filter(item => item.category === 'cars' || item.title.toLowerCase().includes('car'));
    const bikes = items.filter(item => item.category === 'bikes' || item.title.toLowerCase().includes('bike'));
    
    // Use first car and first bike, or create defaults
    const carItem = cars.length > 0 ? cars[0] : {
        title: 'Cars',
        description: 'Explore the city in comfort and style.',
        subtext: 'Explore Deals',
        linkText: 'Search cars',
        link: '/car-rentals.html',
        image: '/images/placeholder.jpg'
    };
    
    const bikeItem = bikes.length > 0 ? bikes[0] : {
        title: 'Bikes',
        description: 'Swift travel, zero emissions.',
        subtext: 'Explore Rentals',
        linkText: 'Find bikes',
        link: '/bike-rentals.html',
        image: '/images/placeholder.jpg'
    };
    
    const displayItems = [carItem, bikeItem];

    container.innerHTML = displayItems.map((item, i) => `
        <div class="bg-white rounded-lg shadow-soft overflow-hidden flex flex-row h-full min-h-[200px] cursor-pointer group hover:shadow-card transition-shadow box-hover border border-gray-100">
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
    if (!container || !items || items.length === 0) return;

    const itemsPerView = 7;
    
    // Add carousel CSS if not already added
    if (!document.getElementById('carousel-styles')) {
        const style = document.createElement('style');
        style.id = 'carousel-styles';
        style.textContent = `
            .carousel-wrapper {
                overflow: hidden;
                width: 100%;
            }
            
            .carousel-track {
                display: flex;
                gap: 1rem;
                transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            .carousel-item {
                flex: 0 0 calc((100% - 6rem) / 7);
                min-width: 0;
            }
            
            @media (max-width: 768px) {
                .carousel-item {
                    flex: 0 0 calc((100% - 2rem) / 3);
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Create wrapper and track
    container.className = 'carousel-wrapper';
    const track = document.createElement('div');
    track.className = 'carousel-track';
    
    // Duplicate items for infinite scroll effect
    const allItems = [...items, ...items];
    
    track.innerHTML = allItems.map((item) => `
        <div class="carousel-item">
            <a href="${esc(item.link)}" class="flex flex-col items-center gap-2 cursor-pointer group transition-all duration-300">
                <div class="relative w-full aspect-square rounded-full shadow-md overflow-hidden ring-2 ring-white group-hover:ring-primary transition-all duration-300">
                    <img class="w-full h-full object-cover" alt="${esc(item.name)}" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                    <div class="absolute bottom-0 inset-x-0 h-1/2 bg-gradient-to-t from-black/60 to-transparent"></div>
                    ${item.rating ? `<div class="absolute bottom-2 left-1/2 -translate-x-1/2 bg-primary text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm">${esc(item.rating)}</div>` : ''}
                </div>
                <div class="text-center w-full">
                    <h3 class="text-sm font-bold text-text-main group-hover:text-primary line-clamp-2 transition-colors">${esc(item.name)}</h3>
                    <p class="text-xs text-text-muted line-clamp-1">${esc(item.type)}</p>
                </div>
            </a>
        </div>
    `).join('');
    
    container.innerHTML = '';
    container.appendChild(track);
    
    // Smooth carousel animation
    if (items.length > itemsPerView) {
        let currentPosition = 0;
        const itemWidth = 100 / itemsPerView;
        
        setInterval(() => {
            currentPosition += itemWidth;
            track.style.transform = `translateX(-${currentPosition}%)`;
            
            // Reset to beginning when we've scrolled through one set
            if (currentPosition >= itemWidth * items.length) {
                setTimeout(() => {
                    track.style.transition = 'none';
                    currentPosition = 0;
                    track.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        track.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    }, 50);
                }, 800);
            }
        }, 3000);
    }
}

// 4. TRAVEL YOUR WAY (BUS ROUTES)
function renderBusRoutes(items) {
    const container = document.getElementById('bus-routes-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => `
        <a href="${esc(item.link)}" class="bg-white p-4 rounded-lg shadow-soft border border-gray-100 hover:shadow-card transition-all cursor-pointer group flex flex-col h-full box-hover">
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
    if (!container || !items || items.length === 0) return;

    const itemsPerView = 4;
    
    // Create wrapper and track
    container.className = 'carousel-wrapper';
    const track = document.createElement('div');
    track.className = 'carousel-track';
    track.style.gap = '1rem';
    
    // Duplicate items for infinite scroll effect
    const allItems = [...items, ...items];
    
    track.innerHTML = allItems.map((item) => `
        <div style="flex: 0 0 calc((100% - 3rem) / 4); min-width: 0;">
            <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft border border-gray-100 overflow-hidden cursor-pointer group hover:shadow-card transition-all flex flex-col h-full box-hover">
                <div class="h-48 shrink-0 overflow-hidden relative">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="${esc(item.name)}" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                    ${item.rating ? `
                    <div class="absolute top-2 right-2 bg-white/90 backdrop-blur text-xs font-bold px-2 py-0.5 rounded shadow-sm flex items-center gap-1">
                        <span class="material-symbols-outlined text-yellow-500 text-[12px] fill-current">star</span>
                        ${esc(item.rating)}
                    </div>` : ''}
                </div>
                <div class="p-3 flex flex-col flex-1">
                    <h3 class="font-bold text-text-main mb-1 line-clamp-2 text-sm">${esc(item.name)}</h3>
                    <p class="text-xs text-text-muted flex items-center gap-1 mt-auto">
                        <span class="material-symbols-outlined text-[12px] shrink-0">location_on</span> <span class="line-clamp-1">${esc(item.location)}</span>
                    </p>
                </div>
            </a>
        </div>
    `).join('');
    
    container.innerHTML = '';
    container.appendChild(track);
    
    // Smooth carousel animation with infinite loop
    if (items.length > itemsPerView) {
        let currentPosition = 0;
        const itemWidth = 100 / itemsPerView;
        
        setInterval(() => {
            currentPosition += itemWidth;
            track.style.transform = `translateX(-${currentPosition}%)`;
            
            // Reset to beginning when we've scrolled through one set
            if (currentPosition >= itemWidth * items.length) {
                setTimeout(() => {
                    track.style.transition = 'none';
                    currentPosition = 0;
                    track.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        track.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    }, 50);
                }, 800);
            }
        }, 3000);
    }
}

// 6. QUICK BIKE RENTALS
function renderBikeRentals(items) {
    const container = document.getElementById('bike-rentals-grid');
    if (!container || !items || items.length === 0) return;

    const itemsPerView = 4;

    // Create wrapper and track
    container.className = 'carousel-wrapper';
    const track = document.createElement('div');
    track.className = 'carousel-track';
    track.style.gap = '1rem';
    
    // Duplicate items for infinite scroll effect
    const allItems = [...items, ...items];
    
    track.innerHTML = allItems.map((item) => `
        <div style="flex: 0 0 calc((100% - 3rem) / 4); min-width: 0;">
            <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft border border-gray-100 p-3 flex flex-col hover:shadow-card transition-all h-full text-center box-hover">
                <div class="w-full h-40 shrink-0 bg-gray-50 rounded mb-2 flex items-center justify-center overflow-hidden">
                    <img alt="${esc(item.name)}" class="w-full h-full object-cover" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                </div>
                <h3 class="text-sm font-bold text-text-main mb-1 line-clamp-1">${esc(item.name)}</h3>
                <p class="text-xs text-text-muted line-clamp-3">${esc(item.description)}</p>
            </a>
        </div>
    `).join('');
    
    container.innerHTML = '';
    container.appendChild(track);
    
    // Smooth carousel animation with infinite loop
    if (items.length > itemsPerView) {
        let currentPosition = 0;
        const itemWidth = 100 / itemsPerView;
        
        setInterval(() => {
            currentPosition += itemWidth;
            track.style.transform = `translateX(-${currentPosition}%)`;
            
            // Reset to beginning when we've scrolled through one set
            if (currentPosition >= itemWidth * items.length) {
                setTimeout(() => {
                    track.style.transition = 'none';
                    currentPosition = 0;
                    track.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        track.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    }, 50);
                }, 800);
            }
        }, 3000);
    }
}

// 7. FEATURED RESTAURANTS
function renderFeaturedRestaurants(items) {
    const container = document.getElementById('featured-restaurants-grid');
    if (!container || !items || items.length === 0) return;

    const itemsPerView = 3;

    // Create wrapper and track
    container.className = 'carousel-wrapper';
    const track = document.createElement('div');
    track.className = 'carousel-track';
    track.style.gap = '1rem';
    
    // Duplicate items for infinite scroll effect
    const allItems = [...items, ...items];
    
    track.innerHTML = allItems.map((item) => {
        const tagsHtml = (item.tags || []).map(t => `<span class="bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded whitespace-nowrap">${esc(t)}</span>`).join('');
        return `
        <div style="flex: 0 0 calc((100% - 2rem) / 3); min-width: 0;">
            <a href="${esc(item.link)}" class="bg-white rounded-lg shadow-soft overflow-hidden border border-gray-100 flex flex-col group hover:shadow-card transition-all h-full box-hover">
                <div class="h-48 shrink-0 relative overflow-hidden">
                    <img alt="${esc(item.name)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="${esc(item.image)}" onerror="this.src='/images/placeholder.jpg'" />
                    ${item.rating ? `<div class="absolute top-2 right-2 bg-primary text-white text-xs font-bold px-2 py-0.5 rounded">${esc(item.rating)}</div>` : ''}
                </div>
                <div class="p-3 flex flex-col flex-1">
                    <div class="flex items-start justify-between mb-1">
                        <div>
                            <h3 class="text-sm font-bold text-text-main line-clamp-1">${esc(item.name)}</h3>
                            <p class="text-xs text-text-muted line-clamp-1">${esc(item.type)}</p>
                        </div>
                    </div>
                    <p class="text-xs text-text-muted line-clamp-2 mb-2">${esc(item.description)}</p>
                    <div class="flex flex-wrap gap-1 mt-auto pt-2 border-t border-gray-50">
                        ${tagsHtml}
                    </div>
                </div>
            </a>
        </div>
        `;
    }).join('');
    
    container.innerHTML = '';
    container.appendChild(track);
    
    // Smooth carousel animation with infinite loop
    if (items.length > itemsPerView) {
        let currentPosition = 0;
        const itemWidth = 100 / itemsPerView;
        
        setInterval(() => {
            currentPosition += itemWidth;
            track.style.transform = `translateX(-${currentPosition}%)`;
            
            // Reset to beginning when we've scrolled through one set
            if (currentPosition >= itemWidth * items.length) {
                setTimeout(() => {
                    track.style.transition = 'none';
                    currentPosition = 0;
                    track.style.transform = `translateX(0)`;
                    setTimeout(() => {
                        track.style.transition = 'transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                    }, 50);
                }, 800);
            }
        }, 3000);
    }
}

// 8. TRAVEL INSIGHTS
// Static blog slug map — links homepage cards to dedicated blog pages
const STATIC_BLOG_LINKS = {
    'street food': 'blog-street-food.html',
    'ajanta': 'blog-ajanta-caves.html',
    'bibi ka maqbara': 'blog-bibi-ka-maqbara.html',
};

function getBlogLink(item, fromApi) {
    // For static/admin-curated items: use item.link if available
    if (!fromApi && item.link) return item.link;
    // Match by title keywords against known static blog pages
    const title = (item.title || '').toLowerCase();
    for (const [keyword, url] of Object.entries(STATIC_BLOG_LINKS)) {
        if (title.includes(keyword)) return url;
    }
    // Fallback: API blogs open on the blogs listing page
    return 'blogs.html';
}

function renderTravelInsights(items, fromApi = false) {
    const container = document.getElementById('travel-insights-grid');
    if (!container || !items) return;

    container.innerHTML = items.map(item => {
        // Support both DB blog format and legacy JSON format
        const category = fromApi ? (item.category || 'Insights') : (item.category || 'Insights');
        const readTime = fromApi ? (item.read_time || '') : (item.readTime || '');
        const image = item.image || '/images/placeholder.jpg';
        const description = fromApi ? (item.content || '').substring(0, 160) + '...' : (item.description || '');
        const blogHref = getBlogLink(item, fromApi);
        return `
        <a href="${esc(blogHref)}" target="_blank" class="group cursor-pointer flex flex-col h-full bg-white rounded-lg shadow-sm border border-gray-100 p-3 hover:shadow-md transition-shadow no-underline box-hover">
            <div class="rounded-lg shrink-0 overflow-hidden h-[180px] mb-3 relative">
                <img alt="${esc(item.title)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="${esc(image)}" onerror="this.src='/images/placeholder.jpg'" />
                <div class="absolute inset-0 bg-black/10 group-hover:bg-transparent transition-colors"></div>
            </div>
            <div class="flex items-center gap-2 mb-2 text-xs text-text-muted font-medium">
                <span class="text-primary font-bold">${esc(category)}</span>
                ${readTime ? `<span>•</span><span>${esc(readTime)}</span>` : ''}
            </div>
            <h3 class="text-lg font-bold text-text-main mb-2 leading-snug group-hover:text-primary transition-colors line-clamp-2">${esc(item.title)}</h3>
            <p class="text-sm text-text-muted line-clamp-2 mt-auto">${esc(description)}</p>
        </a>
        `;
    }).join('');
}
