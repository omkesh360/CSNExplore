import os
import re
import glob

PUBLIC_DIR = '/Users/omkesh360/Documents/GitHub/CSNExplore/public'

with open(f"{PUBLIC_DIR}/stays.html", 'r') as f:
    stays_html = f.read()

# Split stays_html into header and footer parts
header_part = stays_html.split('</header>')[0] + '</header>\n'
# Find footer start
footer_index = stays_html.find('<footer')
footer_part = stays_html[footer_index:]

# Read ref1
with open(f"{PUBLIC_DIR}/ref1.html", 'r') as f:
    ref1_html = f.read()
    
# Extract <main> from ref1
main1_match = re.search(r'(<main.*?</main>)', ref1_html, re.DOTALL)
main1 = main1_match.group(1) if main1_match else ""

# Fix title for about.html
about_header = header_part.replace('TravelHub - Stays in Chhatrapati Sambhajinagar', 'About Us | TravelHub')
# Also remove active state from Stays button if we copied stays.html nav
about_header = about_header.replace('bg-blue-50 text-primary transition-colors font-medium border border-blue-100', 'hover:bg-blue-50 text-gray-700 hover:text-primary transition-colors font-medium')
about_header = about_header.replace('border border-white bg-white/10 transition-colors whitespace-nowrap text-white', 'hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white group')


with open(f"{PUBLIC_DIR}/about.html", 'w') as f:
    f.write(about_header + main1 + '\n' + footer_part)

# Read ref2
with open(f"{PUBLIC_DIR}/ref2.html", 'r') as f:
    ref2_html = f.read()
    
# Extract <main> from ref2
main2_match = re.search(r'(<main.*?</main>)', ref2_html, re.DOTALL)
main2 = main2_match.group(1) if main2_match else ""

# Replace address
old_address = "123 Travel Avenue, Suite 500<br/>San Francisco, CA 94103, USA"
new_address = "Behind State Bank Of India, plot no. 273 Samarth Nagar, Central Bus Stand, Chhatrapati Sambhajinagar, Maharashtra 431001"
main2 = main2.replace(old_address, new_address)

# Fix title for contact.html
contact_header = header_part.replace('TravelHub - Stays in Chhatrapati Sambhajinagar', 'Contact Us | TravelHub')
# Remove active state
contact_header = contact_header.replace('bg-blue-50 text-primary transition-colors font-medium border border-blue-100', 'hover:bg-blue-50 text-gray-700 hover:text-primary transition-colors font-medium')
contact_header = contact_header.replace('border border-white bg-white/10 transition-colors whitespace-nowrap text-white', 'hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white group')


with open(f"{PUBLIC_DIR}/contact.html", 'w') as f:
    f.write(contact_header + main2 + '\n' + footer_part)

# Update Copyrights everywhere
for filepath in glob.glob(f"{PUBLIC_DIR}/**/*.html", recursive=True):
    with open(filepath, 'r') as f:
        content = f.read()
    
    # regex to replace copyright year
    content = re.sub(r'Copyright &copy; 1996&ndash;2024', 'Copyright &copy; 1996&ndash;2026', content)
    content = re.sub(r'© 2024 TravelHub', '© 2026 TravelHub', content)
    content = re.sub(r'2024 TravelHub', '2026 TravelHub', content)
    
    with open(filepath, 'w') as f:
        f.write(content)

# Delete ref files
if os.path.exists(f"{PUBLIC_DIR}/ref1.html"): os.remove(f"{PUBLIC_DIR}/ref1.html")
if os.path.exists(f"{PUBLIC_DIR}/ref2.html"): os.remove(f"{PUBLIC_DIR}/ref2.html")
print("Pages generated, texts updated, refs deleted.")

