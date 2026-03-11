import glob
import re
import os

files = glob.glob('/Users/omkesh360/Documents/GitHub/CSNExplore/public/admin*.html')

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Fix the logo section background and invert the logo
    # Match: <div class="flex h-20 items-center px-6 border-b border-slate-100 shrink-0">
    # Replace with bg-primary and change the img
    
    # Let's do a more robust replacement for the sidebar logo area
    # Find the aside then fix the first div
    aside_pattern = r'(<aside[^>]*>[\s\n]*)<div class="flex h-20 items-center px-6 border-b[^"]*"([^>]*)>'
    content = re.sub(aside_pattern, r'\1<div class="flex h-20 items-center px-6 border-b border-primary-dark bg-primary shrink-0"\2>', content)

    # Invert the logo image so it's visible on blue background
    logo_pattern = r'(<img src="/images/travelhub\.png" alt="TravelHub" class=")([^"]*)(")'
    def replace_logo(m):
        cls = m.group(2)
        if 'brightness-0' not in cls:
            cls = cls + ' filter brightness-0 invert'
        return m.group(1) + cls + m.group(3)
    content = re.sub(logo_pattern, replace_logo, content)

    # 2. Fix the header text colors
    # We want to target the <header> tag section
    header_pattern = r'(<header[^>]*bg-primary[^>]*>)(.*?)(</header>)'
    
    def fix_header_inner(m):
        header_open = m.group(1)
        inner = m.group(2)
        header_close = m.group(3)
        
        # h1 text-primary -> text-white
        inner = re.sub(r'(<h1[^>]*class="[^"]*)text-primary([^"]*">)', r'\1text-white\2', inner)
        
        # p text-text-muted -> text-blue-100
        inner = re.sub(r'(<p[^>]*class="[^"]*)text-text-muted([^"]*">)', r'\1text-blue-100\2', inner)
        
        # p text-text-main -> text-white
        inner = re.sub(r'(<p[^>]*class="[^"]*)text-text-main([^"]*">)', r'\1text-white\2', inner)
        
        # button text-gray-500 hover:bg-gray-50 -> text-white hover:bg-primary-dark
        inner = re.sub(r'text-gray-500(\s+)hover:bg-gray-50', r'text-white\1hover:bg-primary-hover', inner)
        
        # remove border-slate-200 if any, ensure border-primary-dark
        inner = inner.replace('border-slate-200', 'border-primary-dark')

        return header_open + inner + header_close

    content = re.sub(header_pattern, fix_header_inner, content, flags=re.DOTALL)

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

print(f"Updated {len(files)} files.")
