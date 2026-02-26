import re

with open('public/js/listings.js', 'r') as f:
    text = f.read()

# The perfect consistent layout logic
cards_logic = """// ---- Stay Card ----
    function stayCard(item) {
        return `
        <a href="stay-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group block"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.price || 0}" data-category="stays">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        ${item.location ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1 line-clamp-1"><span class="material-symbols-outlined text-[13px]">info</span>${item.description}</p>` : ''}
                        <p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">check_circle</span>Free Cancellation</p>
                        <span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">Hotel Stay</span>
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${item.price ? `<p class="text-xs text-text-muted">from <span class="text-xl font-black text-text-main">₹${Number(item.price).toLocaleString('en-IN')}</span> / night</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }

    // ---- Car Card ----
    function carCard(item) {
        const price = item.dailyRate || item.price;
        return `
        <a href="car-rental-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group block"
            data-id="${item.id}" data-title="${item.name}" data-price="${price || 0}" data-category="cars">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        ${item.provider ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">directions_car</span>By ${item.provider}</p>` : ''}
                        ${item.location ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        <p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">group</span>${item.passengers || 4} seats • ${item.transmission || 'Auto'}</p>
                        ${item.type ? `<span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">${item.type}</span>` : ''}
                    </div>
                    ${ratingBadge(item)}
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
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group block"
            data-id="${item.id}" data-title="${item.name}" data-price="${price || 0}" data-category="bikes">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        ${item.type ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">two_wheeler</span>${item.type}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1 line-clamp-1"><span class="material-symbols-outlined text-[13px]">info</span>${item.description}</p>` : ''}
                        ${item.features ? `<p class="text-xs text-text-muted flex items-center gap-1 mt-0.5"><span class="material-symbols-outlined text-[13px]">build</span>${item.features.split(',')[0]}</p>` : ''}
                        <span class="mt-1 inline-block bg-orange-50 text-orange-600 text-[10px] font-bold px-2 py-0.5 rounded-full">Bike Rental</span>
                    </div>
                    ${ratingBadge(item)}
                </div>
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
        <a href="restaurant-detail.html?id=${item.id}" class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group block"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.pricePerPerson || 0}" data-category="restaurants">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        ${item.cuisine ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">restaurant_menu</span>${item.cuisine}</p>` : ''}
                        ${item.location ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-0.5 flex items-center gap-1 line-clamp-1"><span class="material-symbols-outlined text-[13px]">info</span>${item.description}</p>` : ''}
                        <span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">Dining</span>
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    ${item.pricePerPerson ? `<p class="text-xs text-text-muted">~<span class="text-xl font-black text-text-main">₹${Number(item.pricePerPerson).toLocaleString('en-IN')}</span> / person</p>` : '<div></div>'}
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }

    // ---- Attraction Card ----
    function attractionCard(item) {
        return `
        <a href="attraction-detail.html?id=${item.id}"
            class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow block group"
            data-id="${item.id}" data-title="${item.name}" data-price="${item.entryFee || 0}" data-category="attractions">
            <div class="w-full md:w-56 h-44 md:h-auto shrink-0 relative rounded-lg overflow-hidden bg-primary/5 flex items-center justify-center">
                <img alt="${item.name}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    src="${item.image || ''}" onerror="this.src='https://placehold.co/600x400?text=No+Image'"/>
                ${badgeHtml(item) ? `<div class="absolute top-2 left-2">${badgeHtml(item)}</div>` : ''}
            </div>
            <div class="flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <h3 class="text-lg font-bold text-text-main mb-0.5">${item.name}</h3>
                        ${item.location ? `<p class="text-xs text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.duration ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">schedule</span>Duration: ${item.duration}</p>` : ''}
                        ${item.description ? `<p class="text-xs text-text-muted mt-0.5 flex items-center gap-1 line-clamp-1"><span class="material-symbols-outlined text-[13px]">info</span>${item.description}</p>` : ''}
                        <span class="mt-1 inline-block bg-blue-50 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">Tourist Spot</span>
                    </div>
                    ${ratingBadge(item)}
                </div>
                <div class="mt-auto border-t border-gray-100 pt-3 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <p class="text-xs text-text-muted">
                        Entry: <span class="text-xl font-black text-text-main">${item.entryFee > 0 ? '₹' + Number(item.entryFee).toLocaleString('en-IN') : 'Free'}</span>
                    </p>
                    ${contactButtons()}
                </div>
            </div>
        </a>`;
    }
"""

parts = re.split(r'// ---- Stay Card ----', text)
pre = parts[0]
post = '// ---- Bus Card ----' + parts[1].split('// ---- Bus Card ----')[1]

with open('public/js/listings.js', 'w') as f:
    f.write(pre + cards_logic + post)

print("Updated vertically stacked layout for all cards perfectly aligned with bus style.")
