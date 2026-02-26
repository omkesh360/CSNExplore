import glob, re

files = glob.glob('public/*.html')

for f in files:
    if f == 'public/index.html':
        continue # Already dynamic
        
    with open(f, 'r') as file:
        content = file.read()
    
    # Add IDs to phone link
    content = re.sub(
        r'<a href="tel:\+918600968888" class="flex items-center gap-1 hover:text-white transition-colors">\s*<span class="material-symbols-outlined text-\[16px\]">call</span>\s*\+91 86009 68888\s*</a>',
        '<a href="tel:+918600968888" id="hp-phone-link" class="flex items-center gap-1 hover:text-white transition-colors">\n                        <span class="material-symbols-outlined text-[16px]">call</span>\n                        <span id="hp-phone-text">+91 86009 68888</span>\n                    </a>',
        content
    )
    
    # Add ID to marquee
    content = content.replace(
        '<div class="animate-marquee whitespace-nowrap text-white/90 text-[12px]">',
        '<div id="hp-marquee" class="animate-marquee whitespace-nowrap text-white/90 text-[12px]">'
    )
    
    # Add ID to WhatsApp link
    content = re.sub(
        r'<a href="https://wa\.me/918600968888" target="_blank"\s*class="flex items-center gap-1 hover:text-white transition-colors ml-2">',
        '<a href="https://wa.me/918600968888" id="hp-wa-link" target="_blank"\n                        class="flex items-center gap-1 hover:text-white transition-colors ml-2">',
        content
    )
    
    with open(f, 'w') as file:
        file.write(content)
        
print("HTML files updated.")
