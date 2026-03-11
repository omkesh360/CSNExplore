import glob
import re

files = glob.glob('/Users/omkesh360/Documents/GitHub/CSNExplore/public/admin*.html')

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    header_pattern = r'(<header[^>]*bg-primary[^>]*>)(.*?)(</header>)'
    
    def fix_header_inner(m):
        header_open = m.group(1)
        inner = m.group(2)
        header_close = m.group(3)
        
        # Just replace all variations of dark text
        inner = re.sub(r'\btext-slate-800\b', 'text-white', inner)
        inner = re.sub(r'\btext-slate-900\b', 'text-white', inner)
        inner = re.sub(r'\btext-gray-800\b', 'text-white', inner)
        inner = re.sub(r'\btext-gray-900\b', 'text-white', inner)
        inner = re.sub(r'\btext-text-main\b', 'text-white', inner)
        inner = re.sub(r'\btext-primary\b', 'text-white', inner)
        
        inner = re.sub(r'\btext-slate-500\b', 'text-blue-100', inner)
        inner = re.sub(r'\btext-slate-600\b', 'text-blue-100', inner)
        inner = re.sub(r'\btext-gray-500\b', 'text-blue-100', inner)
        inner = re.sub(r'\btext-gray-600\b', 'text-blue-100', inner)
        inner = re.sub(r'\btext-text-muted\b', 'text-blue-100', inner)
        
        # fix black text on buttons with gray backgrounds
        inner = re.sub(r'\btext-slate-700\b', 'text-white', inner)
        inner = re.sub(r'\bbg-white\b', 'bg-primary-dark', inner)
        
        # Special catch for any other dark text near an svg or icon
        inner = inner.replace('text-gray-700', 'text-white')
        
        return header_open + inner + header_close

    new_content = re.sub(header_pattern, fix_header_inner, content, flags=re.DOTALL)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
        
print("Headers updated to use white text")
