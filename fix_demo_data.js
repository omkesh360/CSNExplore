const fs = require('fs');
let code = fs.readFileSync('public/js/listings.js', 'utf8');
code = code.replace(/function getDemoData\(category\) \{([\s\S]*?)return data\[category\] \|\| \[\];\n    }/, function(match) {
    return Array.from(match.matchAll(/case '(\w+)':\n\s*return (\[[\s\S]*?\]);/g)).reduce((acc, curr) => {
        const cat = curr[1];
        const arrStr = curr[2];
        acc[cat] = JSON.parse(arrStr.replace(/(\w+):/g, '"$1":').replace(/'/g, '"'));
        return acc;
    }, {});
});
fs.writeFileSync('public/js/listings.js', code);
