import glob
import re

files = glob.glob('/Users/omkesh360/Documents/GitHub/CSNExplore/public/admin*.html')

new_link = """
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-slate-500 font-medium transition-all hover:bg-slate-50 hover:text-slate-900"
                        href="admin-cards-editor.html">
                        <span class="material-symbols-outlined text-[20px]">view_list</span>
                        <span class="text-[13px]">Cards Editor</span>
                    </a>"""

for filepath in files:
    if 'admin-cards-editor.html' in filepath:
        continue # Already has the link I want
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the manage listings link block and append the cards editor link
    pattern = r'(<a[^>]*href="admin-manage-listings.html"[^>]*>.*?</a>)'
    
    if not 'href="admin-cards-editor.html"' in content:
        content, count = re.subn(pattern, r'\1' + new_link, content, flags=re.DOTALL)
        if count > 0:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Updated {filepath}")

print("Done updating sidebars.")
