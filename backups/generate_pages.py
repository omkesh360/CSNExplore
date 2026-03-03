import os
import re
import glob

PUBLIC_DIR = '/Users/omkesh360/Documents/GitHub/CSNExplore/public'

with open(f"{PUBLIC_DIR}/index.html", 'r') as f:
    index_html = f.read()

# Extract header from index.html
header_match = re.search(r'(<header.*?</header>)', index_html, re.DOTALL)
index_header = header_match.group(1) if header_match else ''
# Remove the active state if there is any (index.html doesn't highlight contact/about by default but just in case)

# Read ref.txt
with open('/Users/omkesh360/Documents/GitHub/CSNExplore/ref.txt', 'r') as f:
    ref_txt = f.read()

# Split ref.txt into parts (since it has both HTML documents inside)
parts = re.split(r'(?=<!DOCTYPE html>)', ref_txt)

contact_html = ""
about_html = ""

for p in parts:
    if 'Contact Us' in p:
        contact_html = p
    elif 'About Us' in p:
        about_html = p

# Get footer from index.html
footer_index = index_html.find('<footer')
footer_part = index_html[footer_index:]

def build_page(raw_html, is_contact):
    head_match = re.search(r'(<head>.*?</head>)', raw_html, re.DOTALL)
    head = head_match.group(1) if head_match else ''
    
    # We want to use the main site tailwind config and css
    # Replace default tailwind script in head with the ones from index.html
    index_head_match = re.search(r'(<head.*?</head>)', index_html, re.DOTALL)
    if index_head_match:
        index_head = index_head_match.group(1)
        # Just grab the title and replace it in index.html's head
        title_match = re.search(r'<title>(.*?)</title>', head)
        title = title_match.group(1) if title_match else 'TravelHub'
        
        # We will use the main head completely, just change the title
        head = re.sub(r'<title>.*?</title>', f'<title>{title}</title>', index_head)

    main_match = re.search(r'(<main.*?</main>)', raw_html, re.DOTALL)
    main_content = main_match.group(1) if main_match else ''

    if is_contact:
        # Address and phone
        main_content = re.sub(r'\+1 \(555\) 000-TRAVEL', '+91 86009 68888', main_content)
        val1 = "123 Travel Avenue, Suite 500<br/>San Francisco, CA 94103, USA"
        val2 = "Behind State Bank Of India, plot no. 273 Samarth Nagar, Central Bus Stand, Chhatrapati Sambhajinagar, Maharashtra 431001"
        main_content = main_content.replace(val1, val2)

    return f"<!DOCTYPE html>\n<html class=\"light\" lang=\"en\">\n{head}\n<body class=\"bg-background-light dark:bg-background-dark font-display text-text-main antialiased min-h-screen flex flex-col page-fade-in overflow-x-hidden\">\n{index_header}\n{main_content}\n{footer_part}"

if contact_html:
    with open(f"{PUBLIC_DIR}/contact.html", 'w') as f:
        f.write(build_page(contact_html, True))
        
if about_html:
    with open(f"{PUBLIC_DIR}/about.html", 'w') as f:
        f.write(build_page(about_html, False))

# Remove ref.txt
os.remove('/Users/omkesh360/Documents/GitHub/CSNExplore/ref.txt')
print("Pages generated from ref.txt and ref.txt deleted.")
