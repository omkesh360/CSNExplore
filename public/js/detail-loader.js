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
            document.title = `${data.name || data.title} - TravelHub`;
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

        // Reviews count
        const reviewsEl = document.getElementById('item-reviews');
        if (reviewsEl && data.reviews != null) reviewsEl.textContent = `${data.reviews} reviews`;

        // Price — always use ₹ (INR)
        const priceEl = document.getElementById('item-price');
        if (priceEl && data.price != null) {
            const amount = Number(data.price || data.dailyRate || data.pricePerNight || 0);
            priceEl.textContent = `₹${amount.toLocaleString('en-IN')}`;
        }

        // Image
        const mainImg = document.getElementById('item-main-image');
        if (mainImg && data.image) {
            mainImg.src = data.image;
            mainImg.alt = data.name || data.title || 'Item image';
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
