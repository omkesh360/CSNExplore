import re

filepath = "/Users/omkesh360/Documents/GitHub/CSNExplore/public/admin-homepage.html"
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Inject the Add Existing buttons
mappings = [
    # trending transport (doesn't have an add btn right now, wait, let me check)
    # restaurant circles
    (r'(<button class="add-btn[^>]*onclick="addRestaurantCircle\(\)"[^>]*>.*?<\/button>)', 
     r'\1\n                                <button class="add-btn !w-auto !py-2" onclick="openItemBrowser(\'restaurants\', \'restaurantCircles\')" style="border-color:#bfdbfe; color:#003580; background:#eff6ff;">\n                                    <span class="material-symbols-outlined text-[18px]">list</span> <span class="hidden sm:inline">Existing</span>\n                                </button>'),
    # bus routes
    (r'(<button class="add-btn[^>]*onclick="addBusRoute\(\)"[^>]*>.*?<\/button>)', 
     r'\1\n                                <button class="add-btn !w-auto !py-2" onclick="openItemBrowser(\'buses\', \'busRoutes\')" style="border-color:#bfdbfe; color:#003580; background:#eff6ff;">\n                                    <span class="material-symbols-outlined text-[18px]">list</span> <span class="hidden sm:inline">Existing</span>\n                                </button>'),
    # attractions
    (r'(<button class="add-btn[^>]*onclick="addAttraction\(\)"[^>]*>.*?<\/button>)', 
     r'\1\n                                <button class="add-btn !w-auto !py-2" onclick="openItemBrowser(\'attractions\', \'attractions\')" style="border-color:#bfdbfe; color:#003580; background:#eff6ff;">\n                                    <span class="material-symbols-outlined text-[18px]">list</span> <span class="hidden sm:inline">Existing</span>\n                                </button>'),
    # bike rentals
    (r'(<button class="add-btn[^>]*onclick="addBikeRental\(\)"[^>]*>.*?<\/button>)', 
     r'\1\n                                <button class="add-btn !w-auto !py-2" onclick="openItemBrowser(\'bikes\', \'bikeRentals\')" style="border-color:#bfdbfe; color:#003580; background:#eff6ff;">\n                                    <span class="material-symbols-outlined text-[18px]">list</span> <span class="hidden sm:inline">Existing</span>\n                                </button>'),
    # featured restaurants
    (r'(<button class="add-btn[^>]*onclick="addFeaturedRestaurant\(\)"[^>]*>.*?<\/button>)', 
     r'\1\n                                <button class="add-btn !w-auto !py-2" onclick="openItemBrowser(\'restaurants\', \'featuredRestaurants\')" style="border-color:#bfdbfe; color:#003580; background:#eff6ff;">\n                                    <span class="material-symbols-outlined text-[18px]">list</span> <span class="hidden sm:inline">Existing</span>\n                                </button>'),
]

for pattern, repl in mappings:
    content = re.sub(pattern, repl, content, flags=re.DOTALL)

# Modal HTML
modal_html = """
    <!-- Item Browser Modal -->
    <div id="item-browser-modal"
        style="display:none;position:fixed;inset:0;z-index:10000;background:rgba(15,23,42,0.6);backdrop-filter:blur(4px);"
        onclick="if(event.target===this)closeItemBrowser()">
        <div
            style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;border-radius:1.25rem;box-shadow:0 24px 80px rgba(0,0,0,0.18);width:min(90vw,860px);max-height:85vh;display:flex;flex-direction:column;overflow:hidden;">
            <!-- Modal Header -->
            <div
                style="display:flex;align-items:center;justify-content:space-between;padding:1.25rem 1.5rem;border-bottom:1px solid #f1f5f9;">
                <div>
                    <h2 style="font-size:17px;font-weight:800;color:#1e293b;margin:0;">Add Existing Property</h2>
                    <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Select a listed property to add to the homepage</p>
                </div>
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <button onclick="closeItemBrowser()"
                        style="width:2.25rem;height:2.25rem;border-radius:50%;background:#f1f5f9;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:18px;">
                        <span class="material-symbols-outlined" style="font-size:20px;">close</span>
                    </button>
                </div>
            </div>
            <!-- Search -->
            <div style="padding:0.75rem 1.5rem;border-bottom:1px solid #f8fafc;">
                <div style="position:relative;">
                    <span class="material-symbols-outlined"
                        style="position:absolute;left:0.625rem;top:50%;transform:translateY(-50%);font-size:18px;color:#94a3b8;">search</span>
                    <input type="text" id="item-browser-search" placeholder="Search by name..."
                        oninput="filterItemBrowser(this.value)"
                        style="width:100%;padding:0.5rem 0.75rem 0.5rem 2.25rem;border:1px solid #e2e8f0;border-radius:0.625rem;font-size:13px;outline:none;background:#f8fafc;box-sizing:border-box;font-family:inherit;">
                </div>
            </div>
            <!-- List -->
            <div id="item-browser-list"
                style="flex:1;overflow-y:auto;padding:1rem 1.5rem;display:grid;grid-template-columns:1fr;gap:0.625rem;">
                <div style="text-align:center;color:#94a3b8;padding:2rem;">Loading properties...</div>
            </div>
        </div>
    </div>
"""

content = content.replace('<!-- Image Browser Modal -->', modal_html + '\n    <!-- Image Browser Modal -->')

# Logic scripts
logic_script = """
        // ============================================================
        // ITEM BROWSER (existing properties)
        // ============================================================
        let currentItemCategory = '';
        let currentItemTargetArray = '';
        let loadedItems = [];

        async function openItemBrowser(category, targetArray) {
            currentItemCategory = category;
            currentItemTargetArray = targetArray;
            document.getElementById('item-browser-modal').style.display = 'block';
            document.getElementById('item-browser-list').innerHTML = '<div style="text-align:center;color:#94a3b8;padding:2rem;">Loading properties...</div>';
            
            try {
                const res = await fetch(`/api/${category}`);
                if (!res.ok) throw new Error('Failed');
                loadedItems = await res.json();
                renderItemBrowserList(loadedItems);
            } catch (err) {
                document.getElementById('item-browser-list').innerHTML = '<div style="text-align:center;color:#ef4444;padding:2rem;">Error loading items</div>';
            }
        }

        function closeItemBrowser() {
            document.getElementById('item-browser-modal').style.display = 'none';
        }

        function filterItemBrowser(query) {
            if(!loadedItems) return;
            const q = query.toLowerCase();
            const filtered = loadedItems.filter(item => 
                (item.name && item.name.toLowerCase().includes(q)) || 
                (item.title && item.title.toLowerCase().includes(q))
            );
            renderItemBrowserList(filtered);
        }

        function renderItemBrowserList(items) {
            const list = document.getElementById('item-browser-list');
            if (items.length === 0) {
                list.innerHTML = '<div style="text-align:center;color:#94a3b8;padding:2rem;">No items found.</div>';
                return;
            }
            list.innerHTML = items.map(item => {
                const title = escHtml(item.name || item.title || 'Unnamed');
                const img = escHtml(item.image || item.image_url || 'images/placeholder.jpg');
                const rawJson = escHtml(JSON.stringify(item));
                
                return `
                <div style="display:flex;align-items:center;gap:1rem;padding:0.75rem;border:1px solid #e2e8f0;border-radius:0.75rem;cursor:pointer;transition:background 0.2s;" class="hover:bg-slate-50" onclick="selectItemBrowser('${btoa(unescape(encodeURIComponent(JSON.stringify(item))))}')">
                    <img src="${img}" style="width:3rem;height:3rem;object-fit:cover;border-radius:0.5rem;" onerror="this.src='/images/placeholder.jpg'">
                    <div style="flex:1;">
                        <div style="font-weight:700;font-size:14px;color:#1e293b;">${title}</div>
                        <div style="font-size:12px;color:#64748b;text-transform:capitalize;">ID: ${item.id || '-'} | Category: ${currentItemCategory}</div>
                    </div>
                    <span class="material-symbols-outlined" style="color:#003580;">add_circle</span>
                </div>`;
            }).join('');
        }

        function selectItemBrowser(base64item) {
            const item = JSON.parse(decodeURIComponent(escape(atob(base64item))));
            
            if (!hpData[currentItemTargetArray]) {
                hpData[currentItemTargetArray] = [];
            }
            
            // Map common properties
            let newItem = {
                id: item.id || Date.now().toString(),
                name: item.name || item.title || '',
                image: item.image || item.image_url || ''
            };
            
            // Map array specific properties
            if (currentItemTargetArray === 'restaurantCircles') {
                newItem.type = item.type || item.cuisine || '';
                newItem.rating = item.rating || '';
                newItem.link = `restaurant-detail.html?id=${item.id}`;
            } else if (currentItemTargetArray === 'busRoutes') {
                newItem.from = item.from || '';
                newItem.to = item.to || '';
                newItem.provider = item.provider || item.operator || '';
                newItem.duration = item.duration || '';
                newItem.departure = item.departure || item.departureTime || '';
                newItem.price = item.price ? `₹${item.price}` : '';
                newItem.link = `bus-detail.html?id=${item.id}`;
            } else if (currentItemTargetArray === 'attractions') {
                newItem.location = item.location || '';
                newItem.rating = item.rating || '';
                newItem.link = `attraction-detail.html?id=${item.id}`;
            } else if (currentItemTargetArray === 'bikeRentals') {
                newItem.description = item.description ? item.description.substring(0, 50) + '...' : '';
                newItem.link = `bike-rental-detail.html?id=${item.id}`;
            } else if (currentItemTargetArray === 'featuredRestaurants') {
                newItem.type = item.type || item.cuisine || '';
                newItem.rating = item.rating || '';
                newItem.reviews = item.reviews || '0 reviews';
                newItem.price = item.price ? `₹${item.price}` : '';
                newItem.badge = 'Popular';
                newItem.link = `restaurant-detail.html?id=${item.id}`;
            }
            
            hpData[currentItemTargetArray].push(newItem);
            
            // re-render the appropriate section
            if (currentItemTargetArray === 'restaurantCircles') renderRestaurantCircles();
            if (currentItemTargetArray === 'busRoutes') renderBusRoutes();
            if (currentItemTargetArray === 'attractions') renderAttractions();
            if (currentItemTargetArray === 'bikeRentals') renderBikeRentals();
            if (currentItemTargetArray === 'featuredRestaurants') renderFeaturedRestaurants();
            
            closeItemBrowser();
            showToast('Item added to section!');
        }

"""

if 'function openItemBrowser' not in content:
    content = content.replace('// STATE', logic_script + '\n        // STATE')

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated homepage editor html logic.")
