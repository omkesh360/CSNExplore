import re

files_info = {
    'public/stays.html': ('Stays', 'bed', 'stays'),
    'public/car-rentals.html': ('Car Rentals', 'directions_car', 'car-rentals'),
    'public/bike-rentals.html': ('Bike Rentals', 'directions_bike', 'bike-rentals'),
    'public/attraction.html': ('Attractions', 'local_activity', 'attraction')
}

for file, (title, icon, page_id) in files_info.items():
    with open(file, 'r') as f:
        content = f.read()
    
    # 1. Update the active class in the header for desktop
    # First, reset the restaurant active class back to generic
    content = content.replace(
        '<a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-white bg-white/10 transition-colors whitespace-nowrap text-white"\n                            href="restaurant.html">',
        '<a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white group"\n                            href="restaurant.html">'
    )
    
    # Second, set the current page to active
    target_link = f'<a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white group"\n                            href="{page_id}.html">'
    active_link = f'<a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-white bg-white/10 transition-colors whitespace-nowrap text-white"\n                            href="{page_id}.html">'
    content = content.replace(target_link, active_link)

    # 2. Update the active class in the mobile drawer
    content = content.replace(
        '<a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 text-primary transition-colors font-medium border border-blue-100"\n                    href="restaurant.html">\n                    <span class="material-symbols-outlined text-[22px]">restaurant_menu</span>',
        '<a class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-primary transition-colors font-medium"\n                    href="restaurant.html">\n                    <span class="material-symbols-outlined text-[22px] text-gray-400">restaurant_menu</span>'
    )
    
    target_mobile_link = f'<a class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-blue-50 text-gray-700 hover:text-primary transition-colors font-medium"\n                    href="{page_id}.html">\n                    <span class="material-symbols-outlined text-[22px] text-gray-400">{icon}</span>'
    active_mobile_link = f'<a class="flex items-center gap-3 px-4 py-3 rounded-xl bg-blue-50 text-primary transition-colors font-medium border border-blue-100"\n                    href="{page_id}.html">\n                    <span class="material-symbols-outlined text-[22px]">{icon}</span>'
    content = content.replace(target_mobile_link, active_mobile_link)
    
    with open(file, 'w') as f:
        f.write(content)

print("Updated active states in headers based on restaurant.html structure")

