import os
import re

directory = "/Users/omkesh360/Documents/GitHub/CSNExplore/public"
header_pattern = re.compile(r'(<header[^>]*?bg-)white([^>]*?>.*?)(</header>)', re.DOTALL)

for filename in os.listdir(directory):
    if filename.startswith("admin") and filename.endswith(".html"):
        filepath = os.path.join(directory, filename)
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # Simple string replacements to change standard header styling
        # Find the header block
        header_match = re.search(r'<header[^>]*class="[^"]*?(?:bg-white|border-b)[^"]*?"[^>]*>.*?</header>', content, re.DOTALL)
        
        if header_match:
            original_header = header_match.group(0)
            
            # modify classes for blue theme
            new_header = original_header.replace('bg-white', 'bg-primary')
            new_header = new_header.replace('border-slate-200', 'border-primary-dark').replace('border-gray-200', 'border-primary-dark')
            
            # menu button
            new_header = new_header.replace('text-slate-500 hover:text-slate-800', 'text-white/80 hover:text-white')
            
            # search icon
            new_header = new_header.replace('text-slate-400 text-[20px]', 'text-white/70 text-[20px]')
            
            # search input
            new_header = new_header.replace('border border-slate-200 bg-slate-50/50', 'border border-white/20 bg-white/10')
            new_header = new_header.replace('text-slate-800 placeholder-slate-400', 'text-white placeholder-white/60')
            new_header = new_header.replace('focus:border-primary focus:ring-primary/10', 'focus:border-white focus:ring-white/20')
            
            # notification button
            # Note: handle both instances of text-slate-500
            new_header = new_header.replace('text-slate-500 transition-colors hover:bg-slate-50 hover:text-slate-800 border border-transparent hover:border-slate-200', 'text-white/80 transition-colors hover:bg-white/10 hover:text-white border border-transparent hover:border-white/20')
            new_header = new_header.replace('border-white box-content', 'border-primary box-content')
            
            # view site button
            new_header = new_header.replace('bg-white text-slate-700', 'bg-white text-primary')
            
            # in admin.html edge cases
            new_header = new_header.replace('text-slate-600', 'text-white/80').replace('hover:text-slate-900', 'hover:text-white')

            content = content.replace(original_header, new_header)

            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated {filename}")
        else:
            print(f"Header not found or pattern didn't match in {filename}")

