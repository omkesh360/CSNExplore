const fs = require('fs');
const path = require('path');

const publicDir = path.join(__dirname, '../public');

function cleanHTMLFiles(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        if (fs.statSync(fullPath).isDirectory()) {
            cleanHTMLFiles(fullPath);
        } else if (file.endsWith('.html')) {
            let content = fs.readFileSync(fullPath, 'utf8');

            // Remove the tailwind CDN script tag completely
            let cleaned = content.replace(/<script src="https:\/\/cdn\.tailwindcss\.com[^>]*><\/script>\n?/g, '');

            // Remove the config block
            cleaned = cleaned.replace(/<script>\s*tailwind\.config =[\s\S]*?<\/script>\n?/g, '');

            // Ensure the local CSS tag is present somewhere in the head
            if (!cleaned.includes('css/tailwind.css') && !cleaned.includes('css/tailwind.css"')) {
                // for those files in subfolders, we want to make sure the relative path is ok, but they are all in public/ right now except admin pages. There are no subfolders containing HTML.
                cleaned = cleaned.replace('</head>', '    <!-- Link to our pre-compiled local Tailwind CSS -->\n    <link href="css/tailwind.css" rel="stylesheet" />\n</head>');
            }

            // Add loading="lazy" and decoding="async" to img tags
            cleaned = cleaned.replace(/<img([^>]+)>/g, (match, p1) => {
                let newMatch = match;
                if (!match.includes('loading="lazy"')) {
                    newMatch = `<img loading="lazy" decoding="async"${p1}>`;
                }
                return newMatch;
            });

            if (cleaned !== content) {
                fs.writeFileSync(fullPath, cleaned);
                console.log(`✅ Cleaned up CDN, added local CSS, and optimized images in ${file}`);
            }
        }
    }
}

cleanHTMLFiles(publicDir);
