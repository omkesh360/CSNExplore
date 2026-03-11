const fs = require('fs');
const jsdom = require('jsdom');
const { JSDOM } = jsdom;

const html = fs.readFileSync('public/admin-blogs-generator.html', 'utf8');

const virtualConsole = new jsdom.VirtualConsole();
virtualConsole.on("error", (err) => { console.error("DOM ERROR:", err); });
virtualConsole.on("warn", (warn) => { console.warn("DOM WARN:", warn); });
virtualConsole.on("info", (info) => { console.info("DOM INFO:", info); });
virtualConsole.on("log", (log) => { console.log("DOM LOG:", log); });
virtualConsole.on("jsdomError", (err) => { console.error("JSDOM ERROR:", err.message, err.detail); });

const dom = new JSDOM(html, { 
    virtualConsole, 
    runScripts: "dangerously", 
    resources: "usable",
    url: "http://localhost:8000/admin-blogs-generator.html"
});

setTimeout(() => {
    console.log("Done waiting");
    process.exit(0);
}, 3000);
