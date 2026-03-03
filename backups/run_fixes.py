import os
import glob
import shutil
import re

PUBLIC_DIR = '/Users/omkesh360/Documents/GitHub/CSNExplore/public'
BACKUP_DIR = '/Users/omkesh360/Documents/GitHub/CSNExplore/backups'

# 1. Update index.html
index_path = os.path.join(PUBLIC_DIR, 'index.html')
if os.path.exists(index_path):
    with open(index_path, 'r') as f:
        content = f.read()
    
    # Remove Desktop Admin line block
    content = re.sub(r'<a[^>]*href="admin\.html"[^>]*>\s*<span[^>]*>admin_panel_settings</span>\s*<span[^>]*>Admin</span>\s*</a>', '', content, flags=re.DOTALL)
    
    # Remove Mobile Admin line block
    content = re.sub(r'<a[^>]*href="admin\.html"[^>]*>\s*<span[^>]*>admin_panel_settings.*?</span>\s*Admin\s*</a>', '', content, flags=re.DOTALL)
    
    with open(index_path, 'w') as f:
        f.write(content)

# 2. Replace admin.html with admin-dashboard.html across all HTML files
for filepath in glob.glob(os.path.join(PUBLIC_DIR, '*.html')):
    with open(filepath, 'r') as f:
        file_content = f.read()
    
    if 'href="admin.html"' in file_content:
        file_content = file_content.replace('href="admin.html"', 'href="admin-dashboard.html"')
        with open(filepath, 'w') as f:
            f.write(file_content)

# 3. Handle admin.html
admin_html_path = os.path.join(PUBLIC_DIR, 'admin.html')
if os.path.exists(admin_html_path):
    os.remove(admin_html_path)

# 4. Cleanup unwated/backup files
if not os.path.exists(BACKUP_DIR):
    os.makedirs(BACKUP_DIR)

patterns = [
    '*.bak', 
    '*backup*',
    '*.tmp',
    'fix-header-*.py',
    'inject-*.py',
    'update-forms.py'
]

count = 0
for pattern in patterns:
    for f in glob.glob(os.path.join(PUBLIC_DIR, pattern)) + glob.glob(os.path.join(PUBLIC_DIR, '**', pattern), recursive=True):
        if os.path.isfile(f):
            basename = os.path.basename(f)
            dest = os.path.join(BACKUP_DIR, basename)
            counter = 1
            while os.path.exists(dest):
                dest = os.path.join(BACKUP_DIR, f"{counter}_{basename}")
                counter += 1
            shutil.move(f, dest)
            count += 1
            
print(f"Cleanup complete! Moved {count} files to backups/")

