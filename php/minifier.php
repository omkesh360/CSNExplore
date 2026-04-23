<?php
/**
 * php/minifier.php — CSS & JS minifier (no caching, pure string processing)
 *
 * Usage:
 *   require_once 'php/minifier.php';
 *
 *   // Minify a CSS string:
 *   $min_css = minify_css($css_string);
 *
 *   // Minify a JS string:
 *   $min_js = minify_js($js_string);
 *
 *   // Serve a minified CSS file inline:
 *   echo '<style>' . minify_css_file('animations.css') . '</style>';
 *
 *   // Output a minified JS file with correct headers:
 *   minify_serve_js('animations.js');
 */

/**
 * Minify CSS: removes comments, excess whitespace, and redundant semicolons.
 */
function minify_css(string $css): string {
    // Remove block comments /* ... */
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    // Remove line comments (rare in CSS but possible)
    $css = preg_replace('!//[^\n]*\n!', "\n", $css);
    // Collapse whitespace
    $css = preg_replace('/\s+/', ' ', $css);
    // Remove spaces around structural characters
    $css = preg_replace('/\s*([:;{},>~+])\s*/', '$1', $css);
    // Remove trailing semicolons before }
    $css = str_replace(';}', '}', $css);
    // Remove leading/trailing whitespace
    return trim($css);
}

/**
 * Minify JS: removes single-line comments, block comments, and excess whitespace.
 * NOTE: This is a lightweight minifier — not a full AST parser.
 * It preserves string literals and regex patterns.
 */
function minify_js(string $js): string {
    $out    = '';
    $len    = strlen($js);
    $i      = 0;
    $in_str = false;
    $str_ch = '';

    while ($i < $len) {
        $ch   = $js[$i];
        $next = $js[$i + 1] ?? '';

        // Inside a string literal — pass through verbatim
        if ($in_str) {
            $out .= $ch;
            if ($ch === '\\') { $out .= $next; $i += 2; continue; }
            if ($ch === $str_ch) $in_str = false;
            $i++;
            continue;
        }

        // Start of string
        if ($ch === '"' || $ch === "'" || $ch === '`') {
            $in_str = true;
            $str_ch = $ch;
            $out   .= $ch;
            $i++;
            continue;
        }

        // Block comment /* ... */
        if ($ch === '/' && $next === '*') {
            $end = strpos($js, '*/', $i + 2);
            if ($end === false) break;
            $i = $end + 2;
            $out .= ' '; // preserve space to avoid token merging
            continue;
        }

        // Line comment // ...
        if ($ch === '/' && $next === '/') {
            $end = strpos($js, "\n", $i + 2);
            $i   = $end === false ? $len : $end + 1;
            $out .= "\n";
            continue;
        }

        // Collapse whitespace sequences (but keep single space/newline)
        if ($ch === ' ' || $ch === "\t" || $ch === "\r" || $ch === "\n") {
            // Only emit a single space if needed between tokens
            $prev = $out ? $out[strlen($out) - 1] : '';
            if (!in_array($prev, [' ', "\n", '{', '}', '(', ')', ';', ',', '=', '+', '-', '*', '/', '!', '&', '|', '<', '>', '?', ':'])) {
                $out .= ' ';
            }
            // Skip all consecutive whitespace
            while ($i < $len && in_array($js[$i], [' ', "\t", "\r", "\n"])) $i++;
            continue;
        }

        $out .= $ch;
        $i++;
    }

    return trim($out);
}

/**
 * Read and minify a CSS file. Returns empty string if file not found.
 */
function minify_css_file(string $path): string {
    $abs = _minifier_abs($path);
    if (!$abs || !file_exists($abs)) return '';
    return minify_css(file_get_contents($abs));
}

/**
 * Read and minify a JS file. Returns empty string if file not found.
 */
function minify_js_file(string $path): string {
    $abs = _minifier_abs($path);
    if (!$abs || !file_exists($abs)) return '';
    return minify_js(file_get_contents($abs));
}

/**
 * Serve a minified CSS file with correct headers.
 */
function minify_serve_css(string $path): void {
    $content = minify_css_file($path);
    header('Content-Type: text/css; charset=utf-8');
    header('Content-Length: ' . strlen($content));
    echo $content;
    exit;
}

/**
 * Serve a minified JS file with correct headers.
 */
function minify_serve_js(string $path): void {
    $content = minify_js_file($path);
    header('Content-Type: application/javascript; charset=utf-8');
    header('Content-Length: ' . strlen($content));
    echo $content;
    exit;
}

// ── Internal helper ───────────────────────────────────────────────────────────
function _minifier_abs(string $path): ?string {
    if (file_exists($path)) return realpath($path);
    $root = $_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__);
    $abs  = rtrim($root, '/') . '/' . ltrim($path, '/');
    return file_exists($abs) ? $abs : null;
}
