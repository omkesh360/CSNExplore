<?php
/**
 * apply-blog-header-update.php
 * Replaces the old simple header in all blogs/*.html files with the
 * full pill-header template from header-html.html.
 * Also fixes the <head> section to include all required CSS/JS.
 * Run once from browser or CLI: php apply-blog-header-update.php
 */

$files   = glob(__DIR__ . '/blogs/*.html');
$updated = 0;
$skipped = 0;

// Load the shared header template
$headerTpl = file_get_contents(__DIR__ . '/header-html.html');
// For blogs/ the base path is one level up
$headerHtml = str_replace('{{BASE}}', '../', $headerTpl);

// The CSS/JS block to inject into <head> (replaces the minimal head in old blogs)
$headInject = <<<'HEAD'
<meta name="mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-capable" content="yes"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="format-detection" content="telephone=no"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="../mobile-responsive.css"/>
<link rel="stylesheet" href="../animations.css"/>
<style>
body{background:#fff;color:#0f172a;font-family:Inter,sans-serif;}
body.page-ready{animation:pageFadeIn 0.2s ease forwards;}
@keyframes pageFadeIn{from{opacity:0;}to{opacity:1;}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}
.blog-content{max-width:720px;margin:0 auto;}
.blog-content h2{font-size:1.875rem;font-weight:700;margin:2rem 0 1rem;color:#1e293b;}
.blog-content h3{font-size:1.5rem;font-weight:700;margin:1.5rem 0 0.75rem;color:#334155;}
.blog-content p{margin:1rem 0;line-height:1.75;color:#475569;}
.blog-content ul,.blog-content ol{margin:1rem 0;padding-left:2rem;color:#475569;}
.blog-content li{margin:0.5rem 0;}
.blog-content img{border-radius:.75rem;margin:2rem 0;width:100%;height:auto;}
.blog-content a{color:#ec5b13;text-decoration:underline;}
.blog-content blockquote{border-left:4px solid #ec5b13;padding-left:1.5rem;margin:2rem 0;font-style:italic;color:#64748b;}
</style>
HEAD;

foreach ($files as $file) {
    $content = file_get_contents($file);

    // ── 1. Inject missing head tags if not already present ──
    if (strpos($content, 'Material+Symbols+Outlined') === false) {
        // Insert after <meta name="viewport"...>
        $content = preg_replace(
            '/(<meta name="viewport"[^>]+>)/i',
            '$1' . "\n" . $headInject,
            $content,
            1
        );
    }

    // ── 2. Replace header block ──
    // Matches both old simple fixed header AND any version of the new header block
    // Anchors on the marquee-bar div id which is always present
    $blockPattern = '/(?:<!-- [^\n]*(?:Marquee|Simple Header)[^\n]*-->\s*)?<div id="marquee-bar".*?<\/script>\s*/is';

    if (preg_match($blockPattern, $content)) {
        $content = preg_replace($blockPattern, $headerHtml . "\n", $content, 1);
        // Remove old mt-16 spacer on hero if present
        $content = str_replace('class="relative h-[60vh] min-h-[400px] mt-16', 'class="relative h-[60vh] min-h-[400px]', $content);
        file_put_contents($file, $content);
        $updated++;
    } else {
        $skipped++;
    }
}

echo "Blog pages updated : $updated\n";
echo "Blog pages skipped : $skipped\n";
echo "Total              : " . count($files) . "\n";
?>
