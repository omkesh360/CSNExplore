/**
 * CSNExplore Web Worker for Filtering & Sorting Listings
 * Offloads heavy array manipulations from the main thread.
 */

self.onmessage = function(e) {
    const { listings, checkedTypes, minRating, priceMin, priceMax, sortBy } = e.data;

    let filtered = [];

    for (let i = 0; i < listings.length; i++) {
        let item = listings[i];
        
        let typeOk = checkedTypes.length === 0 || checkedTypes.includes(item.type);
        let priceOk = item.price === 0 || (item.price >= priceMin && item.price <= priceMax);
        let ratingOk = item.rating >= minRating;

        if (typeOk && priceOk && ratingOk) {
            filtered.push(item);
        }
    }

    if (sortBy === 'price_low') {
        filtered.sort((a, b) => (a.price || 999999) - (b.price || 999999));
    } else if (sortBy === 'price_high') {
        filtered.sort((a, b) => b.price - a.price);
    } else if (sortBy === 'rating') {
        filtered.sort((a, b) => b.rating - a.rating);
    } else {
        // default sorting (preserve DOM order logic by id)
        filtered.sort((a, b) => a.order - b.order);
    }

    // Return an array of IDs of the visible listings
    const visibleIds = filtered.map(item => item.id);
    
    self.postMessage({ visibleIds: visibleIds });
};
