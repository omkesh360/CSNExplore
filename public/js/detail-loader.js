/**
 * detail-loader.js
 * Dynamically loads content for detail pages based on ID in URL query string.
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
        const response = await fetch(`/api/${category}/${id}`);
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        const data = await response.json();

        // Title
        const titleEl = document.getElementById('item-title');
        if (titleEl) titleEl.textContent = data.name || data.title || '';

        const breadcrumbTitleEl = document.getElementById('item-breadcrumb-title');
        if (breadcrumbTitleEl) breadcrumbTitleEl.textContent = data.name || data.title || '';

        // Update page <title>
        if (data.name || data.title) {
            document.title = `${data.name || data.title} - CSNExplore`;
        }

        // Location
        const locationEl = document.getElementById('item-location');
        if (locationEl && data.location) locationEl.textContent = data.location;

        // Description
        const descEl = document.getElementById('item-description');
        if (descEl && data.description) descEl.textContent = data.description;

        // Rating
        const ratingEl = document.getElementById('item-rating');
        if (ratingEl && data.rating != null) ratingEl.textContent = data.rating;
        const ratingBadgeEl = document.getElementById('item-rating-badge');
        if (ratingBadgeEl && data.rating != null) ratingBadgeEl.textContent = data.rating;
        const summaryRatingEl = document.getElementById('reviews-summary-rating');
        if (summaryRatingEl && data.rating != null) summaryRatingEl.textContent = Number(data.rating).toFixed(1);

        // Reviews count
        const reviewsEl = document.getElementById('item-reviews');
        if (reviewsEl && data.reviews != null) reviewsEl.textContent = `${data.reviews} reviews`;

        // Price — always use ₹ (INR)
        const priceEl = document.getElementById('item-price');
        if (priceEl && data.price != null) {
            const amount = Number(data.price || data.dailyRate || data.pricePerNight || 0);
            priceEl.textContent = `₹${amount.toLocaleString('en-IN')}`;
        }

        // Breadcrumbs
        const breadcrumbList = document.querySelector('nav[aria-label="Breadcrumb"] ol');
        if (breadcrumbList && data.location) {
            // Remove hardcoded inner list except Home
            // Format: Home > [Location] > [Title]
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

        // Image
        const mainImg = document.getElementById('item-main-image');
        if (mainImg && data.image) {
            // Handle if it's "Array" text (due to a previous upload bug)
            if (data.image !== 'Array') {
                mainImg.src = data.image;
            }
            mainImg.alt = data.name || data.title || 'Item image';
        }

        // Gallery Images (replace hardcoded grid images if available)
        let galleryArray = data.gallery;
        if (typeof galleryArray === 'string') {
            try {
                galleryArray = JSON.parse(galleryArray);
            } catch (e) {
                galleryArray = data.gallery.split(',').map(s => s.trim());
            }
        }

        if (galleryArray && Array.isArray(galleryArray) && galleryArray.length > 0) {
            const allGridImgs = Array.from(document.querySelectorAll('.grid img')).filter(img => img.id !== 'item-main-image');
            for (let i = 0; i < Math.min(allGridImgs.length, galleryArray.length); i++) {
                allGridImgs[i].src = galleryArray[i];
            }
        }

        // Amenities / Features
        let amenitiesArray = data.amenities || data.features;
        if (typeof amenitiesArray === 'string') {
            try {
                amenitiesArray = JSON.parse(amenitiesArray);
            } catch (e) {
                amenitiesArray = amenitiesArray.split(',').map(s => s.trim());
            }
        }
        const amenitiesContainer = document.getElementById('item-amenities');
        if (amenitiesContainer && amenitiesArray && Array.isArray(amenitiesArray) && amenitiesArray.length > 0) {
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
        }

        // --- NEW STAY FIELDS ---
        if (data.topLocationRating) {
            const el = document.getElementById('item-top-location-rating');
            if (el) el.textContent = data.topLocationRating;
        }
        if (data.breakfastInfo) {
            const el = document.getElementById('item-breakfast-info');
            if (el) el.textContent = data.breakfastInfo;
        }

        if (data.rooms && Array.isArray(data.rooms)) {
            const roomsContainer = document.getElementById('item-rooms-list');
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
                            <div class="font-bold text-lg mb-1">₹${room.price || data.pricePerNight || 0}</div>
                            <a href="javascript:void(0)" class="text-primary font-bold hover:underline whitespace-nowrap block">Select</a>
                        </td>
                    </tr>
                `).join('');
            }
        }

        if (data.guestReviews && Array.isArray(data.guestReviews)) {
            const reviewsContainer = document.getElementById('item-guest-reviews') || document.getElementById('guest-reviews-container');
            const reviewsCountEl = document.getElementById('item-reviews-count');

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
                        <h4 class="font-bold text-sm text-text-main mb-2">"${rev.title || 'Good stay'}"</h4>
                        <p class="text-sm text-text-muted leading-relaxed mb-3">${rev.text || ''}</p>
                        ${(rev.tags && rev.tags.length > 0) ? `
                        <div class="flex items-center gap-1 text-xs text-green-600 font-medium">
                            <span class="material-symbols-outlined text-sm">sentiment_satisfied_alt</span> Liked: ${rev.tags.join(', ')}
                        </div>
                        ` : ''}
                    </div>
                `).join('');
            }
        }

        if (data.menuHighlights && Array.isArray(data.menuHighlights)) {
            const menuContainer = document.getElementById('menu-highlights-container');
            if (menuContainer) {
                menuContainer.innerHTML = data.menuHighlights.map(menu => `
                    <div class="flex items-start space-x-4 p-4 rounded-xl border border-gray-100 bg-white shadow-sm hover:shadow-md transition-shadow">
                        <img loading="lazy" decoding="async" alt="${menu.name || 'Menu item'}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0" src="${menu.image || 'https://placehold.co/100x100?text=No+Img'}" onerror="this.src='https://placehold.co/100x100?text=No+Img'" />
                        <div>
                            <h3 class="font-bold text-text-main">${menu.name || 'Item'}</h3>
                            <p class="text-sm text-text-muted mt-1 line-clamp-2">${menu.description || ''}</p>
                        </div>
                    </div>
                `).join('');
            }
        }

        // Set data attributes on body for booking logic
        document.body.dataset.id = id;
        document.body.dataset.title = data.name || data.title || '';
        document.body.dataset.price = data.price || data.dailyRate || data.pricePerNight || 0;
        document.body.dataset.category = category;

    } catch (error) {
        console.error('Error loading detail:', error);
        const titleEl = document.getElementById('item-title');
        if (titleEl) titleEl.textContent = 'Item not found';
        const descEl = document.getElementById('item-description');
        if (descEl) descEl.textContent = 'This item could not be loaded. It may have been removed.';
    }
});
