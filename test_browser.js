const puppeteer = require('puppeteer-core');
(async () => {
    try {
        await fetch('http://localhost/CSNExplore/clear_cache.php');
        const browser = await puppeteer.launch({
            executablePath: 'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
            headless: 'new'
        });
        const page = await browser.newPage();
        
        const errors = [];
        page.on('pageerror', err => {
            errors.push('Page Error: ' + err.toString());
        });
        page.on('console', msg => {
            if (msg.type() === 'error') {
                errors.push('Console Error: ' + msg.text());
            }
        });

        console.log('Navigating to editor...');
        await page.goto('http://localhost/CSNExplore/admin/blog-editor-new.php?id=19', { waitUntil: 'networkidle2' });
        
        console.log('Waiting for elements to load...');
        await new Promise(r => setTimeout(r, 2000));
        
        const titleValue = await page.$eval('#post-title', el => el.value).catch(e => 'Not found');
        console.log('Title value in input:', titleValue);
        
        const excerptValue = await page.$eval('#post-excerpt', el => el.value).catch(e => 'Not found');
        console.log('Excerpt value in input:', excerptValue);
        
        const tagsValue = await page.$eval('#post-tags', el => el.value).catch(e => 'Not found');
        console.log('Tags value in input:', tagsValue);
        
        console.log('Any JS errors encountered:', errors.length > 0 ? errors : 'None');
        
        await browser.close();
    } catch(err) {
        console.error('Puppeteer Error:', err);
    }
})();
