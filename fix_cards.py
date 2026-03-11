import re

with open('public/js/listings.js', 'r') as file:
    content = file.read()

# Replace restaurantCard
restaurant_target = re.compile(r'    // ---- Restaurant Card ----.*?^\s*\}\n', re.MULTILINE | re.DOTALL)
restaurant_replacement = """    // ---- Restaurant Card ----
    function restaurantCard(item) {
        return `
        <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row gap-4 hover:shadow-card transition-shadow group"
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
                        <div class="flex mb-1">${starIcons(item.rating)}</div>
                        ${item.cuisine ? `<p class="text-xs text-primary font-semibold">${item.cuisine}</p>` : ''}
                        ${item.location ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
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
"""

content = restaurant_target.sub(restaurant_replacement, content, count=1)

# Replace attractionCard
attraction_target = re.compile(r'    // ---- Attraction Card ----.*?^\s*\}\n', re.MULTILINE | re.DOTALL)
attraction_replacement = """    // ---- Attraction Card ----
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
                        <div class="flex mb-1">${starIcons(item.rating)}</div>
                        ${item.location ? `<p class="text-xs text-text-muted flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">location_on</span>${item.location}</p>` : ''}
                        ${item.duration ? `<p class="text-xs text-text-muted mt-1 flex items-center gap-1"><span class="material-symbols-outlined text-[13px]">schedule</span>${item.duration}</p>` : ''}
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
"""

content = attraction_target.sub(attraction_replacement, content, count=1)

with open('public/js/listings.js', 'w') as file:
    file.write(content)

print("Updated listings.js cards successfully.")
