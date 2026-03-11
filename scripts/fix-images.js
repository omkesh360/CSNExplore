const fs = require('fs');
const path = require('path');

const publicDir = path.join(__dirname, '../public');
const seedPath = path.join(__dirname, 'seed-data.json');

const fallbackImages = [
    'https://images.unsplash.com/photo-1585135438814-2fb402b1f8ac?auto=format&fit=crop&w=800&q=80', // Indian fort
    'https://images.unsplash.com/photo-1564507592227-0b0f5c06a338?auto=format&fit=crop&w=800&q=80', // Temple/architecture
    'https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&w=800&q=80', // Ancient ruins
    'https://images.unsplash.com/photo-1621360170068-d0e82c5058fd?auto=format&fit=crop&w=800&q=80', // Caves / rocks
    'https://images.unsplash.com/photo-1600100397608-f010f423b971?auto=format&fit=crop&w=800&q=80', // Nature / lake
    '/images/daulatabad.png',
    '/images/panchakki.jpg',
    '/images/grishneshwar-temple.jpg',
    '/images/travel-gate.jpg',
    'https://images.unsplash.com/photo-1514222134-b57cbf8cebf7?auto=format&fit=crop&w=800&q=80' // Heritage
];

function toUrlSlug(str) {
    return str.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
}

if (!fs.existsSync(seedPath)) {
    console.error("seed-data.json not found!");
    process.exit(1);
}

let seedData = JSON.parse(fs.readFileSync(seedPath, 'utf8'));
let count = 0;

seedData.forEach((row, i) => {
    if (row.image === '/images/placeholder.jpg') {
        const newImage = fallbackImages[count % fallbackImages.length];
        
        // 1. Update JSON Data
        row.image = newImage;
        count++;

        // 2. Update HTML file
        const slug = toUrlSlug(row.title);
        const htmlPath = path.join(publicDir, `blog-${slug}.html`);
        
        if (fs.existsSync(htmlPath)) {
            let content = fs.readFileSync(htmlPath, 'utf8');
            content = content.replace(/\/images\/placeholder\.jpg/g, newImage);
            fs.writeFileSync(htmlPath, content);
            console.log(`✅ Fixed image for ${row.title}`);
        }
    }
});

fs.writeFileSync(seedPath, JSON.stringify(seedData, null, 2));
console.log(`\nReplaced ${count} placeholders with vibrant images! Next, run the PHP seeder to sync DB.`);
