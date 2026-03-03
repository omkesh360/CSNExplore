import re
import sys

with open('admin-add-listing.html', 'r', encoding='utf-8') as f:
    html = f.read()

# 1. Stays Form
stays_pattern = r'(<form onsubmit="submitForm\(event, \'stays\'\)">)(.*?)(</form>)'
def stays_repl(m):
    return """<form onsubmit="submitForm(event, 'stays')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Property Name</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="location" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                        <option>Hotel</option>
                                        <option>Apartment</option>
                                        <option>Resort</option>
                                        <option>Villa</option>
                                        <option>Homestay</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating (0-5)</label>
                                    <input type="number" name="rating" step="0.1" min="0" max="5" value="4.5" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge</label>
                                    <input type="text" name="badge" placeholder="e.g. Bestseller" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price per Night (₹)</label>
                                    <input type="number" name="price" required class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                                    <input type="text" name="contact" placeholder="+91..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amenities</label>
                                    <input type="text" name="amenities" placeholder="WiFi, Pool, AC..." class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Image</label>
                                    <input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-300 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary relative image-input z-10">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                                    <input type="file" name="gallery" accept="image/*" multiple class="w-full rounded-lg border-gray-300 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary relative gallery-input z-10">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-hover">Add Property</button>
                            </div>
                        </form>"""

html = re.sub(stays_pattern, stays_repl, html, flags=re.DOTALL)

# 2. Cars Form
cars_pattern = r'(<form onsubmit="submitForm\(event, \'cars\'\)">)(.*?)(</form>)'
def cars_repl(m):
    return """<form onsubmit="submitForm(event, 'cars')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Car Model</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                        <option>Sedan</option><option>SUV</option><option>Luxury</option><option>Compact</option><option>MUV</option><option>Hatchback</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Provider</label>
                                    <input type="text" name="provider" placeholder="e.g. Hertz" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price per Day (₹)</label>
                                    <input type="number" name="price" required class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Passengers</label>
                                    <input type="number" name="passengers" value="5" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Transmission</label>
                                    <select name="transmission" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                        <option>Automatic</option><option>Manual</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fuel Type</label>
                                    <select name="fuelType" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                        <option>Petrol</option><option>Diesel</option><option>Electric</option><option>CNG</option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Features (comma list)</label>
                                    <input type="text" name="features" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 focus:border-primary"></textarea>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Cover Image</label>
                                    <input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-300 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary image-input z-10">
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                                    <input type="file" name="gallery" accept="image/*" multiple class="w-full rounded-lg border-gray-300 focus:border-primary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary gallery-input z-10">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-hover">Add Car</button>
                            </div>
                        </form>"""
html = re.sub(cars_pattern, cars_repl, html, flags=re.DOTALL)

# 3. Bikes Form
bikes_pattern = r'(<form onsubmit="submitForm\(event, \'bikes\'\)">)(.*?)(</form>)'
def bikes_repl(m):
    return """<form onsubmit="submitForm(event, 'bikes')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Bike Model</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                        <option>Cruiser</option><option>Sport</option><option>Scooter</option><option>Electric</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Price per Day (₹)</label>
                                    <input type="number" name="price" required class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Engine Capacity (cc)</label>
                                    <input type="text" name="engine" placeholder="e.g. 150cc" class="w-full rounded-lg border-gray-300 focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Cover Image</label>
                                    <input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary image-input z-10">
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                                    <input type="file" name="gallery" accept="image/*" multiple class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary gallery-input z-10 relative">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-hover">Add Bike</button>
                            </div>
                        </form>"""
html = re.sub(bikes_pattern, bikes_repl, html, flags=re.DOTALL)

# 4. Restaurants Form
rest_pattern = r'(<form onsubmit="submitForm\(event, \'restaurants\'\)">)(.*?)(</form>)'
def rest_repl(m):
    return """<form onsubmit="submitForm(event, 'restaurants')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine</label>
                                    <input type="text" name="cuisine" required placeholder="e.g. Italian, Indian" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                                    <input type="number" name="rating" step="0.1" max="5" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="location" required class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost for Two (₹)</label>
                                    <input type="number" name="price" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Operating Hours</label>
                                    <input type="text" name="hours" placeholder="e.g. 10 AM - 11 PM" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Amenities</label>
                                    <input type="text" name="amenities" placeholder="e.g. WiFi, Parking" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Cover Image</label>
                                    <input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-primary/10 file:text-primary image-input z-10 relative">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                                    <input type="file" name="gallery" accept="image/*" multiple class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-primary/10 file:text-primary gallery-input z-10 relative">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-hover">Add Restaurant</button>
                            </div>
                        </form>"""
html = re.sub(rest_pattern, rest_repl, html, flags=re.DOTALL)

# 5. Attractions Form
attr_pattern = r'(<form onsubmit="submitForm\(event, \'attractions\'\)">)(.*?)(</form>)'
def attr_repl(m):
    return """<form onsubmit="submitForm(event, 'attractions')">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Attraction Name</label>
                                    <input type="text" name="name" required class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <input type="text" name="location" required class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                    <input type="text" name="duration" placeholder="e.g. 2 hours" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Entry Fee (₹)</label>
                                    <input type="number" name="price" placeholder="0 for Free" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Operating Hours</label>
                                    <input type="text" name="hours" placeholder="e.g. 9 AM - 6 PM" class="w-full rounded-lg border-gray-300">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300"></textarea>
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Main Cover Image</label>
                                    <input type="file" name="image" accept="image/*" required class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-primary/10 file:text-primary image-input z-10">
                                </div>
                                <div class="relative">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gallery Images</label>
                                    <input type="file" name="gallery" accept="image/*" multiple class="w-full rounded-lg border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-primary/10 file:text-primary gallery-input z-10">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-hover">Add Attraction</button>
                            </div>
                        </form>"""
html = re.sub(attr_pattern, attr_repl, html, flags=re.DOTALL)

# JS Updates
js_pattern = r'(// Populate inputs.*?\})'

def js_repl(m):
    return """// Populate inputs
            for (const [key, value] of Object.entries(data)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type !== 'file') {
                        if (Array.isArray(value)) {
                            input.value = value.join(', ');
                        } else {
                            input.value = value;
                        }
                    }
                }
            }"""

html = re.sub(js_pattern, js_repl, html, flags=re.DOTALL)


# Image Prev Updates
img_preview_pattern = r'(const fileInput = form\.querySelector\(\'input\[type="file"\]\'\);.*?if \(fileInput && data\.image\) \{.*?let preview = form\.querySelector\(\'\.image-preview\'\);.*?if \(!preview\) \{.*?preview = document\.createElement\(\'div\'\);.*?preview\.className = \'image-preview mt-2\';.*?fileInput\.parentNode\.appendChild\(preview\);.*?preview\.innerHTML = `<p class="text-xs text-gray-500 mb-1">Current Image:</p><img src="\$\{data\.image\}" alt="Current listing image" class="h-20 w-auto rounded border border-gray-200">`;.*?fileInput\.removeAttribute\(\'required\'\);.*?\})'

def img_preview_repl(m):
    return """const fileInput = form.querySelector('input[name="image"]');
            if (fileInput && data.image) {
                let preview = form.querySelector('.image-preview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'image-preview mt-2 w-full';
                    fileInput.parentNode.appendChild(preview);
                }
                preview.innerHTML = `<p class="text-xs text-gray-500 mb-1">Current Image:</p><img src="${data.image}" alt="Current" class="h-20 w-auto rounded border">`;
                fileInput.removeAttribute('required');
            }
            
            const galleryInput = form.querySelector('input[name="gallery"]');
            if (galleryInput && data.gallery && data.gallery.length) {
                let gPreview = form.querySelector('.gallery-preview');
                if (!gPreview) {
                    gPreview = document.createElement('div');
                    gPreview.className = 'gallery-preview mt-2 flex gap-2 overflow-x-auto w-full';
                    galleryInput.parentNode.appendChild(gPreview);
                }
                gPreview.innerHTML = data.gallery.map(img => `<img src="${img}" alt="Gallery" class="h-12 w-auto object-cover rounded border">`).join('');
            }"""

html = re.sub(img_preview_pattern, img_preview_repl, html, flags=re.DOTALL)

with open('admin-add-listing.html', 'w', encoding='utf-8') as f:
    f.write(html)
print("Forms successfully replaced.")
