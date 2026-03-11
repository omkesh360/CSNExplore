import re
import os

with open('public/admin-manage-listings.html', 'r', encoding='utf-8') as f:
    template = f.read()

# I will modify the template heavily to build the Cards Editor.
# Let's write the HTML directly to a new file instead of replacing.
