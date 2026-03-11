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
            let updated = content.replace(/travelhub\.png/g, 'csnexplore-logo.png');
            if (updated !== content) {
                fs.writeFileSync(fullPath, updated);
                console.log(`Updated logo reference in: ${fullPath.replace(__dirname + '/../', '')}`);
            }
        }
    }
}

dirsToSearch.forEach(processDirectory);
console.log("Logo references updated!");
