/**
 * detail-loader.js
 * Dynamically loads content for ALL detail pages based on ID in URL query string.
 * COMPLETELY DYNAMIC - Only shows real data from database, hides sections with no data.
 * Supports: stays, cars, bikes, restaurants, attractions, buses
 */

document.addEventListener('DOMContentLoaded', async () => {
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    // Determine category from page filename
    const pagePath = window.location.pathname;
    let category = '';

    if (pagePath.includes('stay')) category = 'stays';
    else if (pagePath.includes('car')) category = 'cars';
    else if (pagePath.includes('bike')) category = 'bikes';
    else if (pagePath.includes('restaurant')) category = 'restaurants';
    else if (pagePath.includes('attraction')) category = 'attractions';
    else if (pagePath.includes('bus')) category = 'buses';

    if (!id || !category) {
        console.warn('Missing ID or category for detail loader.');
        return;
    }

    try {
        const response = await fetch(`/api/${category}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const allData = await response.json();
        
        // Find the specific item by ID
        const data = allData.find(item => item.id == id);
        
        if (!data) {
            throw new Error('Item not found');
        }

        console.log('Loaded data for', category, ':', data);

        // Title
        const titleEl = document.getElementById('item-title');
        if (titleEl) titleEl.textContent = data.name || data.title || '';
        
        // Additional title elements (for bike detail page)
        const titleDesktopEl = document.getElementById('item-title-desktop');
        if (titleDesktopEl) titleDesktopEl.textContent = data.name || data.title || '';

        const breadcrumbTitleEl = document.getElementById('item-breadcrumb-title');
        if (breadcrumbTitleEl) breadcrumbTitleEl.textContent = data.name || data.title || '';

        // Update page <title>
        if (data.name || data.title) {
            document.title = `${data.name || data.title} - CSNExplore`;
        }

        // Location
        const locationEl = document.getElementById('item-location');
        if (locationEl && data.location) locationEl.textContent = data.location;
        
        // Additional location element (for bike detail page)
        const locationDesktopEl = document.getElementById('item-location-desktop');
        if (locationDesktopEl && data.location) locationDesktopEl.textContent = data.location;

        // Description
        const descEl = document.getElementById('item-description');
        if (descEl && data.description) descEl.textContent = data.description;

        // Rating
        const ratingEl = document.getElementById('item-rating');
        if (ratingEl && data.rating != null) ratingEl.textContent = data.rating;
        
        // Additional rating elements (for bike detail page)
        const ratingDesktopEl = document.getElementById('item-rating-desktop');
        if (ratingDesktopEl && data.rating != null) ratingDesktopEl.textContent = data.rating;
        
        const ratingBadgeEl = document.getElementById('item-rating-badge');
        if (ratingBadgeEl && data.rating != null) ratingBadgeEl.textContent = data.rating;
        const summaryRatingEl = document.getElementById('reviews-summary-rating');
        if (summaryRatingEl && data.rating != null) summaryRatingEl.textContent = Number(data.rating).toFixed(1);

        // Reviews count
        const reviewsEl = document.getElementById('item-reviews');
        if (reviewsEl && data.reviews != null) reviewsEl.textContent = `(${data.reviews} reviews)`;
        
        // Additional reviews element (for bike detail page)
        const reviewsDesktopEl = document.getElementById('item-reviews-desktop');
        if (reviewsDesktopEl && data.reviews != null) reviewsDesktopEl.textContent = `(${data.reviews})`;

        // Price — always use ₹ (INR)
        const priceEl = document.getElementById('item-price');
        if (priceEl && data.price != null) {
            const amount = Number(data.price || data.dailyRate || data.pricePerNight || data.price_per_day || data.price_per_night || 0);
            priceEl.textContent = `₹${amount.toLocaleString('en-IN')}`;
        }

        // Type/Category badge
        const typeEl = document.getElementById('item-type');
        if (typeEl && data.type) typeEl.textContent = data.type;

        // Breadcrumbs
        const breadcrumbList = document.querySelector('nav[aria-label="Breadcrumb"] ol');
        if (breadcrumbList && data.location) {
            breadcrumbList.innerHTML = `
                <li><a class="hover:text-primary hover:underline" href="index.html">Home</a></li>
                <li><span class="material-symbols-outlined text-xs">chevron_right</span></li>
                <li><a class="hover:text-primary hover:underline" href="${category}.html">${category.charAt(0).toUpperCase() + category.slice(1)}</a></li>
                <li><span class="material-symbols-outlined text-xs">chevron_right</span></li>
                <li><a class="hover:text-primary hover:underline" href="#">${data.location}</a></li>
                <li><span class="material-symbols-outlined text-xs">chevron_right</span></li>
                <li aria-current="page" class="text-text-main font-medium truncate max-w-[200px]" id="item-breadcrumb-title">${data.name || data.title || ''}</li>
            `;
        }

        // Main Image
        const mainImg = document.getElementById('item-main-image');
        if (mainImg) {
            if (data.image && data.image !== 'Array') {
                mainImg.src = data.image;
                mainImg.alt = data.name || data.title || 'Item image';
                mainImg.onerror = function() {
                    this.style.display = 'none';
                    console.log('Main image failed to load');
                };
            } else {
                mainImg.style.display = 'none';
            }
        }

        // Gallery Images - COMPLETELY DYNAMIC (hide if no images at all)
        // Combine main image + gallery images
        let galleryArray = [];
        
        // Parse gallery if exists
        if (data.gallery) {
            if (typeof data.gallery === 'string') {
                try {
                    galleryArray = JSON.parse(data.gallery);
                } catch (e) {
                    galleryArray = data.gallery.split(',').map(s => s.trim()).filter(Boolean);
                }
            } else if (Array.isArray(data.gallery)) {
                galleryArray = data.gallery.filter(Boolean);
            }
        }
        
        // Add main image to gallery if it exists and is not already in gallery
        if (data.image && data.image !== 'Array') {
            if (!galleryArray.includes(data.image)) {
                galleryArray.unshift(data.image); // Add main image at the beginning
            }
        }
        
        // Remove any invalid entries
        galleryArray = galleryArray.filter(img => img && img !== 'Array' && img.trim() !== '');

        // Find gallery container
        const galleryContainer = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-4, .gallery-grid, [class*="gallery"]');
        
        if (galleryContainer) {
            if (galleryArray.length > 0) {
                // Clear existing gallery
                galleryContainer.innerHTML = '';
                
                // Add proper grid classes if not present
                if (!galleryContainer.classList.contains('grid')) {
                    galleryContainer.className = 'grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 h-96 md:h-[500px]';
                }
                
                // Add main image (first image in array)
                const mainDiv = document.createElement('div');
                mainDiv.className = 'md:col-span-2 md:row-span-2 relative group overflow-hidden rounded-xl cursor-pointer';
                mainDiv.innerHTML = `
                    <img loading="lazy" decoding="async" alt="${data.name || data.title || 'Main image'}" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        src="${galleryArray[0]}" 
                        onerror="this.parentElement.style.display='none'" />
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-5xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">zoom_in</span>
                    </div>
                `;
                mainDiv.addEventListener('click', () => window.openGalleryViewer(galleryArray, 0));
                galleryContainer.appendChild(mainDiv);
                
                // Add remaining gallery images (up to 4 more for a total of 5 visible)
                for (let i = 1; i < Math.min(galleryArray.length, 5); i++) {
                    const div = document.createElement('div');
                    div.className = 'relative group overflow-hidden rounded-xl cursor-pointer';
                    
                    // Last image shows count if more images exist
                    if (i === 4 && galleryArray.length > 5) {
                        div.innerHTML = `
                            <img loading="lazy" decoding="async" alt="Gallery image ${i + 1}" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                src="${galleryArray[i]}" 
                                onerror="this.parentElement.style.display='none'" />
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center group-hover:bg-black/70 transition-colors duration-300 cursor-pointer">
                                <span class="text-white font-medium flex items-center gap-2">
                                    <span class="material-symbols-outlined">photo_library</span> See all ${galleryArray.length} photos
                                </span>
                            </div>
                        `;
                    } else {
                        div.innerHTML = `
                            <img loading="lazy" decoding="async" alt="Gallery image ${i + 1}" 
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                                src="${galleryArray[i]}" 
                                onerror="this.parentElement.style.display='none'" />
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300 flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300">zoom_in</span>
                            </div>
                        `;
                    }
                    div.addEventListener('click', () => window.openGalleryViewer(galleryArray, i));
                    galleryContainer.appendChild(div);
                }
                
                console.log(`✅ Gallery loaded with ${galleryArray.length} images`);
            } else {
                // No images at all - hide the entire gallery section
                galleryContainer.style.display = 'none';
                console.log('❌ No images available - gallery hidden');
            }
        }

        // Amenities / Features - COMPLETELY DYNAMIC (hide if none)
        let amenitiesArray = data.amenities || data.features;
        if (typeof amenitiesArray === 'string') {
            try {
                amenitiesArray = JSON.parse(amenitiesArray);
            } catch (e) {
                amenitiesArray = amenitiesArray.split(',').map(s => s.trim()).filter(Boolean);
            }
        }
        const amenitiesContainer = document.getElementById('item-amenities');
        const amenitiesSection = amenitiesContainer?.closest('.mb-8, section, div[class*="mb-"]');
        
        if (amenitiesContainer) {
            if (amenitiesArray && Array.isArray(amenitiesArray) && amenitiesArray.length > 0) {
                amenitiesContainer.innerHTML = ''; // clear hardcoded
                const isList = amenitiesContainer.tagName.toLowerCase() === 'ul';
                amenitiesArray.forEach(am => {
                    const el = document.createElement(isList ? 'li' : 'div');
                    if (isList) {
                        el.className = "text-text-muted mt-1";
                        el.textContent = am;
                    } else {
                        el.className = "flex items-center gap-2 text-green-700 bg-green-50 px-3 py-2 rounded-lg text-sm font-medium";
                        el.innerHTML = `<span class="material-symbols-outlined text-base">check_circle</span> ${am}`;
                    }
                    amenitiesContainer.appendChild(el);
                });
            } else {
                // No amenities - hide the entire section
                if (amenitiesSection) {
                    amenitiesSection.style.display = 'none';
                }
                console.log('No amenities available - section hidden');
            }
        }

        // --- STAY-SPECIFIC FIELDS ---
        if (category === 'stays') {
            if (data.topLocationRating) {
                const el = document.getElementById('item-top-location-rating');
                if (el) el.textContent = data.topLocationRating;
            }
            if (data.breakfastInfo) {
                const el = document.getElementById('item-breakfast-info');
                if (el) el.textContent = data.breakfastInfo;
            }

            // Rooms - COMPLETELY DYNAMIC (hide if none)
            const roomsContainer = document.getElementById('item-rooms-list');
            const roomsTable = roomsContainer?.closest('table');
            const roomsSection = roomsTable?.closest('.mt-8, section, div[class*="mt-"]');
            
            if (data.rooms && Array.isArray(data.rooms) && data.rooms.length > 0) {
                if (roomsContainer) {
                    roomsContainer.innerHTML = data.rooms.map(room => `
                        <tr>
                            <td class="p-4 align-top">
                                <a class="text-primary font-bold hover:underline text-lg block mb-1" href="javascript:void(0)">${room.name || 'Room'}</a>
                                <p class="text-sm text-text-muted mb-2">${room.beds || ''}</p>
                                ${room.availability && room.availability.toLowerCase().includes('left') ? `<span class="inline-block bg-red-100 text-red-700 text-xs px-2 py-0.5 rounded font-bold mb-2">${room.availability}</span>` : ''}
                                <div class="flex flex-wrap gap-2 text-xs text-green-600 font-medium">
                                    ${(room.features || []).map(f => `<span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">check</span> ${f}</span>`).join('')}
                                </div>
                            </td>
                            <td class="p-4 align-top text-center text-text-muted text-base">
                                ${Array(room.sleeps || 2).fill('<span class="material-symbols-outlined">person</span>').join('')}
                            </td>
                            <td class="p-4 align-top">
                                <div class="flex items-center gap-1 ${room.meals && room.meals.toLowerCase().includes('included') ? 'text-green-600' : 'text-text-main'} font-bold text-sm mb-1">
                                    <span class="material-symbols-outlined text-sm">restaurant</span> ${room.meals || 'No meals included'}
                                </div>
                                <div class="flex items-center gap-1 ${room.cancellation && room.cancellation.toLowerCase().includes('free') ? 'text-green-600' : 'text-text-main'} font-bold text-sm">
                                    <span class="material-symbols-outlined text-sm">${room.cancellation && room.cancellation.toLowerCase().includes('free') ? 'check' : 'do_not_disturb'}</span> ${room.cancellation || 'Non-refundable'}
                                </div>
                            </td>
                            <td class="p-4 align-top">
                                <a href="tel:+918600968888" class="text-primary font-bold hover:underline whitespace-nowrap">Check Rates</a>
                            </td>
                        </tr>
                    `).join('');
                }
            } else {
                // No rooms data - hide the entire availability section
                if (roomsSection) {
                    roomsSection.style.display = 'none';
                }
                console.log('No rooms data available - availability section hidden');
            }
        }

        // Guest Reviews - COMPLETELY DYNAMIC (hide if none)
        const reviewsContainer = document.getElementById('item-guest-reviews') || document.getElementById('guest-reviews-container');
        const reviewsSection = reviewsContainer?.closest('.border-t, section, div[class*="border-t"]');
        const reviewsCountEl = document.getElementById('item-reviews-count');
        
        if (data.guestReviews && Array.isArray(data.guestReviews) && data.guestReviews.length > 0) {
            if (reviewsCountEl) reviewsCountEl.textContent = data.guestReviews.length;

            if (reviewsContainer) {
                reviewsContainer.innerHTML = data.guestReviews.map(rev => `
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 ${rev.rating >= 4 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600'} rounded-full flex items-center justify-center font-bold text-sm">
                                    ${rev.name ? rev.name.substring(0, 2).toUpperCase() : 'U'}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-text-main">${rev.name || 'Anonymous'}</div>
                                    <div class="text-xs text-text-muted flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">public</span> ${rev.country || 'World'}
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-text-muted">Reviewed: ${rev.date || 'Recently'}</div>
                        </div>
                        <h4 class="font-bold text-sm text-text-main mb-2">"${rev.title || 'Good experience'}"</h4>
                        <p class="text-sm text-text-muted leading-relaxed mb-3">${rev.text || ''}</p>
                        ${(rev.tags && rev.tags.length > 0) ? `
                        <div class="flex items-center gap-1 text-xs text-green-600 font-medium">
                            <span class="material-symbols-outlined text-sm">sentiment_satisfied_alt</span> Liked: ${rev.tags.join(', ')}
                        </div>
                        ` : ''}
                    </div>
                `).join('');
            }
        } else {
            // No reviews - hide the entire reviews section
            if (reviewsSection) {
                reviewsSection.style.display = 'none';
            }
            console.log('No guest reviews available - reviews section hidden');
        }

        // Menu Highlights - COMPLETELY DYNAMIC (hide if none) - FOR RESTAURANTS
        const menuContainer = document.getElementById('menu-highlights-container');
        const menuSection = menuContainer?.closest('.mb-8, section, div[class*="mb-"]');
        
        if (data.menuHighlights && Array.isArray(data.menuHighlights) && data.menuHighlights.length > 0) {
            if (menuContainer) {
                menuContainer.innerHTML = data.menuHighlights.map(menu => `
                    <div class="flex items-start space-x-4 p-4 rounded-xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow">
                        ${menu.image && menu.image !== 'Array' ? `
                        <img loading="lazy" decoding="async" alt="${menu.name || 'Menu item'}" 
                            class="w-20 h-20 rounded-lg object-cover flex-shrink-0" 
                            src="${menu.image}" 
                            onerror="this.style.display='none'" />
                        ` : ''}
                        <div>
                            <h3 class="font-bold text-text-main">${menu.name || 'Item'}</h3>
                            <p class="text-sm text-text-muted mt-1 line-clamp-2">${menu.description || ''}</p>
                            ${menu.price ? `<p class="text-sm font-bold text-primary mt-1">₹${menu.price}</p>` : ''}
                        </div>
                    </div>
                `).join('');
            }
        } else {
            // No menu highlights - hide the section
            if (menuSection) {
                menuSection.style.display = 'none';
            }
            console.log('No menu highlights available - section hidden');
        }

        // Set data attributes on body for booking logic
        document.body.dataset.id = id;
        document.body.dataset.title = data.name || data.title || '';
        document.body.dataset.price = data.price || data.dailyRate || data.pricePerNight || data.price_per_day || data.price_per_night || 0;
        document.body.dataset.category = category;

        // Update all Book Now buttons with listing name and ID
        const listingName = data.name || data.title || '';
        document.querySelectorAll('[data-book-now]').forEach(btn => {
            if (listingName) btn.dataset.listingName = listingName;
            if (id) btn.dataset.listingId = id;
        });

        console.log('✅ Detail page loaded successfully - all sections are dynamic');

    } catch (error) {
        console.error('Error loading detail:', error);
        const titleEl = document.getElementById('item-title');
        if (titleEl) titleEl.textContent = 'Item not found';
        const descEl = document.getElementById('item-description');
        if (descEl) descEl.textContent = 'This item could not be loaded. It may have been removed or does not exist.';
        
        // Hide all optional sections on error
        const sectionsToHide = [
            '.gallery-grid',
            '[class*="gallery"]',
            '#item-amenities',
            '#item-rooms-list',
            '#item-guest-reviews',
            '#menu-highlights-container'
        ];
        
        sectionsToHide.forEach(selector => {
            const el = document.querySelector(selector);
            if (el) {
                const section = el.closest('.mb-8, section, div[class*="mb-"], div[class*="mt-"]');
                if (section) section.style.display = 'none';
            }
        });
    }
});
