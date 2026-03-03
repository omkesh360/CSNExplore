import os
import glob
import re

public_dir = '/Users/omkesh360/Documents/GitHub/CSNExplore/public'
html_files = glob.glob(os.path.join(public_dir, '*.html'))

pattern = re.compile(
    r'<a href="login\.html"\s*'
    r'class="(text-white/60 hover:text-white transition-colors flex items-center gap-1)[^"]*">\s*'
    r'<span class="material-symbols-outlined text-\[14px\](?: pointer-events-none)*">admin_panel_settings</span>\s*'
    r'(?:<span class="pointer-events-none">)*Admin Login(?:</span>)*\s*'
    r'</a>', re.IGNORECASE
)

replacement = """<a href="login.html"
                        class="\\1 relative z-[100] cursor-pointer"
                        onclick="window.location.href='login.html'; return true;">
                        <span class="material-symbols-outlined text-[14px] pointer-events-none">admin_panel_settings</span>
                        <span class="pointer-events-none">Admin Login</span>
                    </a>"""

for filepath in html_files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    new_content, count = pattern.subn(replacement, content)
    
    if count > 0:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Updated {filepath}")

print("Done fixing admin footer links.")
