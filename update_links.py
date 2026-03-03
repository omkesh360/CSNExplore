import os
import glob
import re

PUBLIC_DIR = '/Users/omkesh360/Documents/GitHub/CSNExplore/public'

for filepath in glob.glob(os.path.join(PUBLIC_DIR, '*.html')):
    with open(filepath, 'r') as f:
        content = f.read()
    
    # Update `#about` and `#contact` to `about.html` and `contact.html`
    content = re.sub(r'href="#about"', 'href="about.html"', content)
    content = re.sub(r'href="#contact"', 'href="contact.html"', content)
    
    with open(filepath, 'w') as f:
        f.write(content)

print("Links updated")

