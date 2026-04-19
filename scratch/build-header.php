<?php
require_once dirname(__DIR__) . '/php/config.php';
$current_page = 'listing-detail';
ob_start();
include dirname(__DIR__) . '/header.php';
$content = ob_get_clean();

preg_match('/<!-- ── Scroll Progress Bar ───────────────────────────────── -->.*<\/script>\s*$/s', $content, $matches);
if ($matches) {
    $headerHtml = $matches[0];
    
    // Remove all <script> blocks to let generate_html.php handle them centrally
    $headerHtml = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $headerHtml);
    
    // Remove redundant scroll bar div
    $headerHtml = str_replace('<div id="csn-scroll-bar"></div>', '', $headerHtml);
    
    // Cleanup BASE_PATH
    $headerHtml = str_replace(BASE_PATH, '{{BASE}}', $headerHtml);
    
    file_put_contents(dirname(__DIR__) . '/header-html.html', $headerHtml);
    echo "Done";
} else {
    echo "No match";
}
