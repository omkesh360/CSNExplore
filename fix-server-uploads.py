import re

with open('server.js', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace upload.single('image') with upload.fields([{ name: 'image', maxCount: 1 }, { name: 'gallery', maxCount: 10 }])
content = content.replace("upload.single('image')", "upload.fields([{ name: 'image', maxCount: 1 }, { name: 'gallery', maxCount: 10 }])")

# Fix req.file -> req.files
def fix_post_file(match):
    return """  // If a file was uploaded, add its path
  if (req.files && req.files['image']) {
    newItem.image = `images/uploads/${req.files['image'][0].filename}`;
  }
  if (req.files && req.files['gallery']) {
    newItem.gallery = req.files['gallery'].map(f => `/images/uploads/${f.filename}`);
  }"""

content = re.sub(r'  // If a file was uploaded, add its path\s+if \(req\.file\) \{\s+newItem\.image = `images/uploads/\$\{req\.file\.filename\}`;\s+\}', fix_post_file, content)


def fix_put_file(match):
    return """    if (req.files && req.files['image']) {
      updatedItem.image = `images/uploads/${req.files['image'][0].filename}`;
    }
    if (req.files && req.files['gallery']) {
      // If a new gallery is uploaded, overwrite or append the gallery paths
      updatedItem.gallery = req.files['gallery'].map(f => `/images/uploads/${f.filename}`);
    }"""

content = re.sub(r'    if \(req\.file\) \{\s+updatedItem\.image = `images/uploads/\$\{req\.file\.filename\}`;\s+\}', fix_put_file, content)

with open('server.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated server.js to support gallery and multiple file uploads.")
