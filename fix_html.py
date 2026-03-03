import os
import re

with open('public/restaurant.html', 'r') as f:
    rest_html = f.read()

# Extract the header from restaurant.html
header_match = re.search(r'(<header.*?</header>)', rest_html, re.DOTALL)
if not header_match:
    print("Could not find header in restaurant.html")
    exit(1)
header_content = header_match.group(1)

# Apply header to other pages
files = ['public/stays.html', 'public/car-rentals.html', 'public/bike-rentals.html', 'public/attraction.html']

for file in files:
    with open(file, 'r') as f:
        content = f.read()
    
    # Replace header
    content = re.sub(r'<header.*?</header>', header_content, content, flags=re.DOTALL)
    
    # Also fix the hero section background if needed, make sure padding is consistent
    
    with open(file, 'w') as f:
        f.write(content)
    
print("Updated headers for all pages")
