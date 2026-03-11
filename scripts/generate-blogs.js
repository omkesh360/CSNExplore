const fs = require('fs');
const path = require('path');
// fetch is natively available in Node 25.6.1

const inputPath = path.join(__dirname, '../public/js/new.txt');
const publicDir = path.join(__dirname, '../public');

// Template HTML
const getTemplate = (title, description, imageUrl, category = 'Heritage') => `<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>${title} — CSNExplore</title>
    <meta name="description" content="${description.replace(/"/g, '&quot;')}" />
    
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;700;900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    
    <!-- Link to our pre-compiled local Tailwind CSS -->
    <link href="css/tailwind.css" rel="stylesheet" />
    <link href="css/main.css" rel="stylesheet" />
    <style>
        .prose-article h2 { font-size: 1.45rem; font-weight: 900; color: #1a1a1a; margin-top: 2.5rem; margin-bottom: 0.75rem; line-height: 1.3; }
        .prose-article p { color: #374151; line-height: 1.85; margin-bottom: 1.2rem; font-size: 1.025rem; }
        .hero-img-overlay { background: linear-gradient(to bottom, rgba(0, 53, 128, 0.10) 0%, rgba(0, 53, 128, 0.55) 100%); }
        .back-btn { display: inline-flex; align-items: center; gap: 0.4rem; color: #fff; font-weight: 700; font-size: 0.87rem; padding: 0.5rem 1.1rem; border-radius: 9999px; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(6px); border: 1px solid rgba(255, 255, 255, 0.25); transition: background 0.2s; text-decoration: none; }
        .back-btn:hover { background: rgba(255, 255, 255, 0.28); }
    </style>
</head>
<body class="bg-background-light font-display text-text-main antialiased min-h-screen flex flex-col overflow-x-hidden">

    <!-- ============= HEADER ============= -->
    <header class="bg-primary text-white w-full sticky top-0 z-50 shadow-md">
        <!-- Assuming same header from blogs.html -->
        <div class="max-w-[1140px] mx-auto px-4 md:px-6">
            <div class="flex items-center justify-between py-1.5 h-14 md:h-16">
                <a href="index.html" class="flex items-center gap-2 cursor-pointer shrink-0">
                    <img src="/images/travelhub.png" alt="CSNExplore" class="h-8 md:h-[42px]">
                </a>
                <div class="flex items-center gap-4 overflow-hidden">
                    <nav class="hidden lg:flex items-center gap-1">
                        <a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white" href="about.html"><span class="text-[13px] font-medium">About Us</span></a>
                        <a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full hover:bg-white/10 transition-colors whitespace-nowrap text-white/90 hover:text-white" href="contact.html"><span class="text-[13px] font-medium">Contact Us</span></a>
                        <a class="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/15 text-white font-bold" href="blogs.html"><span class="text-[13px] font-medium">Blogs</span></a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- ============= HERO ============= -->
    <div class="relative w-full min-h-[360px] md:min-h-[440px] flex items-end overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('${imageUrl}');"></div>
        <div class="hero-img-overlay absolute inset-0"></div>
        <div class="relative z-10 max-w-[1140px] mx-auto px-4 md:px-6 pb-10 pt-24 w-full">
            <a href="blogs.html" class="back-btn mb-6 inline-flex">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span> All Blogs
            </a>
            <div class="flex items-center gap-2 mb-3">
                <span class="bg-accent text-white text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full">${category}</span>
                <span class="text-white/70 text-[12px] font-medium">• 3 min read</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-white leading-tight max-w-3xl drop-shadow-lg">
                ${title}
            </h1>
            <div class="flex items-center gap-3 mt-5">
                <div class="w-9 h-9 rounded-full bg-accent flex items-center justify-center text-white font-black text-base shrink-0">T</div>
                <div>
                    <p class="text-white font-bold text-sm">CSNExplore Team</p>
                    <p class="text-white/60 text-xs">March 7, 2026</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============= ARTICLE BODY ============= -->
    <main class="flex-1 py-12">
        <div class="max-w-[1140px] mx-auto px-4 md:px-6">
            <article class="prose-article max-w-3xl mx-auto bg-white p-5 md:p-8 rounded-2xl shadow-soft">
                <p class="text-xl font-medium text-slate-800 border-l-4 border-primary pl-4 mb-8">
                    ${description}
                </p>
                <p>
                    This is a comprehensive overview of <strong>${title}</strong>, a notable landmark. Reading through its historical significance and unique architecture offers an unparalleled glimpse into the region's rich heritage.
                </p>
                <h2>A Deep Dive into History</h2>
                <p>
                    The preservation and cultural importance of such sites cannot be understated. Visitors are consistently captivated by the intricate details and the stories that these walls tell. Exploring such destinations provides both educational value and an unforgettable atmospheric experience.
                </p>
            </article>
        </div>
    </main>

    <!-- ============= FOOTER ============= -->
    <footer class="bg-primary text-white pt-16 pb-8 mt-auto">
        <div class="max-w-[1140px] mx-auto px-4 md:px-6">
            <div class="border-t border-white/20 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <img src="/images/travelhub.png" alt="CSNExplore" class="filter brightness-0 invert h-6 md:h-8">
                </div>
                <div class="flex flex-col md:flex-row items-center gap-2 text-xs text-white/60">
                    <div class="text-center md:text-right">Copyright &copy; 1996&ndash;2026 CSNExplore. All rights reserved.</div>
                    <span class="hidden md:inline">&bull;</span>
                    <a href="admin.html" class="text-white/60 hover:text-white transition-colors flex items-center gap-1 relative z-[100] cursor-pointer" target="_blank">
                        <span class="material-symbols-outlined text-[14px] pointer-events-none">admin_panel_settings</span>
                        <span class="pointer-events-none">Admin Login</span>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>`;

function toUrlSlug(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
}

async function fetchWikipediaData(title) {
    try {
        const query = encodeURIComponent(title.replace(/ /g, '_'));
        const response = await fetch(`https://en.wikipedia.org/api/rest_v1/page/summary/${query}`);
        if (!response.ok) return null;
        const data = await response.json();

        return {
            extract: data.extract,
            thumbnail: data.thumbnail ? data.thumbnail.source : null
        };
    } catch (e) {
        return null;
    }
}

async function run() {
    try {
        const data = fs.readFileSync(inputPath, 'utf8');
        const lines = data.split('\n').filter(l => l.trim() !== '');

        // Output JSON file containing the extracted data to be used by the PHP seeder!
        const databaseSeedData = [];

        console.log(`Starting to fetch Wikipedia data for ${lines.length} locations...`);

        for (const line of lines) {
            let [titlePart, descPart] = line.split(' — ');
            if (!titlePart) continue;

            titlePart = titlePart.trim();
            if (titlePart.endsWith(':')) continue;

            const title = titlePart;
            const originalDesc = descPart ? descPart.trim() : 'Explore this incredible location in Chhatrapati Sambhajinagar.';
            const slug = toUrlSlug(title);
            const fileName = `blog-${slug}.html`;

            // Fetch Real Data from Wikipedia
            const wikiData = await fetchWikipediaData(title);

            // Fallbacks: Use Wikipedia if available, otherwise original description
            const finalDesc = wikiData && wikiData.extract ? wikiData.extract : originalDesc;
            const finalImage = wikiData && wikiData.thumbnail ? wikiData.thumbnail : '/images/placeholder.jpg';

            // 1. Generate Static HTML
            const htmlContent = getTemplate(title, finalDesc, finalImage);
            fs.writeFileSync(path.join(publicDir, fileName), htmlContent);
            console.log(`✅ Generated ${fileName} (Wiki Found: ${!!wikiData})`);

            // Store for seeder
            databaseSeedData.push({
                title: title,
                content: finalDesc,
                author: 'System Gen',
                status: 'published',
                category: 'Heritage',
                read_time: '3 min',
                image: finalImage
            });

            // Slight delay so we don't spam Wikipedia API and get blocked
            await new Promise(r => setTimeout(r, 100));
        }

        // Write the fetched data to a JSON file so the PHP seeder can use it directly
        fs.writeFileSync(path.join(__dirname, 'seed-data.json'), JSON.stringify(databaseSeedData, null, 2));
        console.log('✅ Generation complete. Data written to seed-data.json');

    } catch (e) {
        console.error("Error generating blogs:", e);
    }
}

run();
