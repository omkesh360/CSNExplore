<?php
/**
 * CSS Minifier — creates style.min.css and mobile-responsive.min.css
 * Run via CLI: php minify_css.php
 */
function minifyCSS(string $css): string {
    // Remove comments (but not IE conditionals)
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove whitespace around selectors, properties, values
    $css = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $css);
    // Collapse multiple spaces
    $css = preg_replace('/\s{2,}/', ' ', $css);
    // Remove spaces around : ; { } , >
    $css = preg_replace('/\s*([:;{},>~+])\s*/', '$1', $css);
    // Remove trailing semicolons before }
    $css = str_replace(';}', '}', $css);
    // Remove leading/trailing whitespace
    $css = trim($css);
    return $css;
}

$files = [
    'style.css'               => 'style.min.css',
    'mobile-responsive.css'   => 'mobile-responsive.min.css',
    'animations.css'          => 'animations.min.css',
];

foreach ($files as $src => $dst) {
    $srcPath = __DIR__ . '/' . $src;
    $dstPath = __DIR__ . '/' . $dst;

    if (!file_exists($srcPath)) {
        echo "SKIP $src (not found)\n";
        continue;
    }

    $original = file_get_contents($srcPath);
    $minified = minifyCSS($original);

    $before = round(strlen($original) / 1024, 1);
    $after  = round(strlen($minified) / 1024, 1);
    $saving = round((1 - $after / $before) * 100, 1);

    file_put_contents($dstPath, $minified);
    echo "OK   $src → $dst: {$before}KB → {$after}KB (saved {$saving}%)\n";
}

echo "\nDone.\n";
