<?php
/**
 * php/image-optimizer.php — WebP conversion + lazy-loading helpers
 *
 * Usage:
 *   require_once 'php/image-optimizer.php';
 *
 *   // Convert an uploaded image to WebP and return new path:
 *   $webp = img_to_webp('images/uploads/hotel.jpg');
 *
 *   // Render a lazy-loaded <img> tag with WebP + fallback:
 *   echo img_tag('images/uploads/hotel.jpg', 'Hotel exterior', ['class' => 'w-full h-full object-cover']);
 *
 *   // Render a lazy-loaded <picture> with WebP source + fallback:
 *   echo img_picture('images/uploads/hotel.jpg', 'Hotel exterior');
 */

/**
 * Convert an image to WebP format.
 * Returns the WebP path on success, original path on failure.
 */
function img_to_webp(string $src_path, int $quality = 82): string {
    // Resolve absolute path
    $abs = (strpos($src_path, '/') === 0 || strpos($src_path, ':') !== false)
        ? $src_path
        : ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__)) . '/' . ltrim($src_path, '/');

    if (!file_exists($abs)) return $src_path;

    $ext     = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
    $webp    = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '.webp', $abs);
    $webp_rel = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '.webp', $src_path);

    // Already converted
    if (file_exists($webp)) return $webp_rel;

    // Needs GD or Imagick
    if (!function_exists('imagewebp') && !class_exists('Imagick')) return $src_path;

    try {
        if (class_exists('Imagick')) {
            $im = new Imagick($abs);
            $im->setImageFormat('webp');
            $im->setImageCompressionQuality($quality);
            $im->writeImage($webp);
            $im->destroy();
        } else {
            // GD fallback
            $img = match($ext) {
                'jpg', 'jpeg' => imagecreatefromjpeg($abs),
                'png'         => imagecreatefrompng($abs),
                'gif'         => imagecreatefromgif($abs),
                default       => null,
            };
            if (!$img) return $src_path;
            imagewebp($img, $webp, $quality);
            imagedestroy($img);
        }
        return file_exists($webp) ? $webp_rel : $src_path;
    } catch (Exception $e) {
        error_log('img_to_webp error: ' . $e->getMessage());
        return $src_path;
    }
}

/**
 * Render a lazy-loaded <img> tag.
 * Automatically tries WebP version if available.
 */
function img_tag(string $src, string $alt, array $attrs = [], bool $eager = false): string {
    $webp = _img_webp_src($src);
    $final_src = $webp ?: $src;

    $loading = $eager ? 'eager' : 'lazy';
    $decoding = $eager ? 'sync' : 'async';

    $attr_str = '';
    foreach ($attrs as $k => $v) {
        $attr_str .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
    }

    $onerror = ' onerror="this.onerror=null;this.src=\'' . htmlspecialchars($src) . '\'"';

    return '<img src="' . htmlspecialchars($final_src) . '"'
        . ' alt="' . htmlspecialchars($alt) . '"'
        . ' loading="' . $loading . '"'
        . ' decoding="' . $decoding . '"'
        . $attr_str
        . ($webp && $webp !== $src ? $onerror : '')
        . '>';
}

/**
 * Render a <picture> element with WebP source + original fallback.
 * Best for hero images and above-the-fold content.
 */
function img_picture(string $src, string $alt, array $img_attrs = [], bool $eager = false): string {
    $webp    = _img_webp_src($src);
    $loading = $eager ? 'eager' : 'lazy';

    $attr_str = '';
    foreach ($img_attrs as $k => $v) {
        $attr_str .= ' ' . htmlspecialchars($k) . '="' . htmlspecialchars($v) . '"';
    }

    $out = '<picture>';
    if ($webp && $webp !== $src) {
        $out .= '<source srcset="' . htmlspecialchars($webp) . '" type="image/webp">';
    }
    $out .= '<img src="' . htmlspecialchars($src) . '"'
        . ' alt="' . htmlspecialchars($alt) . '"'
        . ' loading="' . $loading . '"'
        . ' decoding="' . ($eager ? 'sync' : 'async') . '"'
        . $attr_str . '>';
    $out .= '</picture>';
    return $out;
}

/**
 * Batch-convert all images in a directory to WebP.
 * Returns ['converted'=>n, 'skipped'=>n, 'errors'=>n]
 */
function img_batch_convert(string $dir, int $quality = 82): array {
    $abs = (strpos($dir, '/') === 0) ? $dir : ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__)) . '/' . ltrim($dir, '/');
    if (!is_dir($abs)) return ['converted' => 0, 'skipped' => 0, 'errors' => 0];

    $stats = ['converted' => 0, 'skipped' => 0, 'errors' => 0];
    $exts  = ['jpg', 'jpeg', 'png', 'gif'];

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($abs));
    foreach ($files as $file) {
        if (!$file->isFile()) continue;
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, $exts)) continue;

        $rel  = ltrim(str_replace($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__), '', $file->getPathname()), '/');
        $webp = img_to_webp($rel, $quality);
        if ($webp !== $rel) $stats['converted']++;
        else $stats['skipped']++;
    }
    return $stats;
}

// ── Internal helper ───────────────────────────────────────────────────────────
function _img_webp_src(string $src): string {
    $ext  = strtolower(pathinfo($src, PATHINFO_EXTENSION));
    if ($ext === 'webp') return $src;
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) return $src;

    $webp_src = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '.webp', $src);
    $abs      = ($_SERVER['DOCUMENT_ROOT'] ?? dirname(__DIR__)) . '/' . ltrim($webp_src, '/');
    return file_exists($abs) ? $webp_src : $src;
}
