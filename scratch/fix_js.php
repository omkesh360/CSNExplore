<?php
$file = 'c:/xampp/htdocs/CSNexplore/CSNExplore/php/api/generate_html.php';
$content = file_get_contents($file);

// Let's replace the block with safely escaped single quotes
$js = <<<'JS'
// ── Sticky Header & Scroll Logic ──
(function(){
    var h  = document.getElementById('site-header');
    var ph = document.getElementById('site-header-placeholder');
    var mb = document.getElementById('marquee-bar');
    var scrollBar = document.getElementById('csn-scroll-bar');
    var ticking = false;
    var MH = 0;

    function measureMarquee(){
        if (!mb) return 0;
        return mb.offsetHeight || 0;
    }

    function setNormal(){
        if (mb) mb.classList.remove('hidden-bar');
        if (h) {
            h.classList.remove('pill-mode');
            h.style.setProperty('position', 'fixed', 'important');
            h.style.setProperty('top', MH + 'px', 'important');
            h.style.setProperty('left', '50%', 'important');
            h.style.setProperty('transform', 'translateX(-50%)', 'important');
            h.style.setProperty('width', '100%', 'important');
            h.style.setProperty('max-width', '100%', 'important');
            h.style.setProperty('border-radius', '0', 'important');
        }
        if (ph) ph.style.height = (MH + 64) + 'px';
    }

    function setPill(){
        if (mb) mb.classList.add('hidden-bar');
        if (h) {
            h.classList.add('pill-mode');
            h.style.setProperty('position', 'fixed', 'important');
            h.style.setProperty('top', '14px', 'important');
            h.style.setProperty('left', '50%', 'important');
            h.style.setProperty('transform', 'translateX(-50%)', 'important');
            h.style.setProperty('width', 'calc(100% - 32px)', 'important');
            h.style.setProperty('max-width', '1120px', 'important');
            h.style.setProperty('border-radius', '9999px', 'important');
        }
    }

    function update(){
        var scrolled = window.scrollY;
        
        // Progress Bar
        var total = document.documentElement.scrollHeight - window.innerHeight;
        if(scrollBar) scrollBar.style.width = total > 0 ? (scrolled/total*100)+'%\' : \'0%';
        
        if (scrolled > 40) {
            setPill();
        } else {
            setNormal();
        }
        ticking = false;
    }
    
    window.addEventListener('scroll', function(){
        if(!ticking){ requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    window.addEventListener('load', function() {
        document.body.classList.add('page-ready');
        MH = measureMarquee();
        update();
    });
    window.addEventListener('resize', function(){
        MH = measureMarquee();
        update();
    });
})();
JS;

// Escape the JS string for PHP execution
$escaped_js = str_replace("'", "\\'", $js);

$pattern = '/\/\/ ── Sticky Header & Scroll Logic ──.*?}\)\(\);/s';
if(preg_match($pattern, $content)) {
    $content = preg_replace($pattern, $escaped_js, $content);
    file_put_contents($file, $content);
    echo "Successfully replaced the header logic.\n";
} else {
    echo "Could not find the target code block.\n";
}
