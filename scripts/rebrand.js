const fs = require('fs');
const path = require('path');

const dirsToSearch = [
    path.join(__dirname, '../public'),
    path.join(__dirname, '../php'),
    path.join(__dirname, '../scripts')
];

function processDirectory(dir) {
    const files = fs.readdirSync(dir);
    for (const file of files) {
        const fullPath = path.join(dir, file);
        const stat = fs.statSync(fullPath);
        
        if (stat.isDirectory()) {
            if (file !== 'images' && file !== 'uploads') {
                processDirectory(fullPath);
            }
        } else if (file.match(/\.(html|js|php|json)$/)) {
            let content = fs.readFileSync(fullPath, 'utf8');
            let updated = content.replace(/CSNExplore/g, 'CSNExplore');
            updated = updated.replace(/CSNExplore/g, 'CSNExplore');
            updated = updated.replace(/travelhub\.db/g, 'csnexplore.db'); // DB name change? Maybe just text first
            // Don't change actual image filenames like travelhub.png to avoid 404s unless we rename the file
            if (updated !== content) {
                fs.writeFileSync(fullPath, updated);
                console.log(`Rebranded: ${fullPath.replace(__dirname + '/../', '')}`);
            }
        }
    }
}

dirsToSearch.forEach(processDirectory);
console.log("Rebranding text complete!");
