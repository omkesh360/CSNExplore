<?php
/**
 * WebP Helper — outputs <picture> tag with WebP source + original fallback
 * Usage: webp_img('/images/foo.png', 'Alt text', 'class="..."', 'width="400" height="300"')
 *
 * Also provides webp_src() to just get the best available src string.
 */

/**
 * Returns the best available image src (WebP if exists, else original).
 * Useful for background-image inline styles.
 */
function webp_src(string $src): string {
    // Strip query string for file check
    $cleanSrc = strtok($src, '?');
    $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $cleanSrc);
    $absWebp  = $_SERVER['DOCUMENT_ROOT'] . $webpPath;

    if (file_exists($absWebp)) {
        return $webpPath;
    }
    return $src;
}

/**
 * Outputs a <picture> element with WebP source and original fallback.
 *
 * @param string $src       Image src (relative to root, e.g. /images/foo.png)
 * @param string $alt       Alt text
 * @param string $imgAttrs  Extra attributes for <img> tag (class, id, style, etc.)
 * @param string $sizeAttrs Width/height/loading/fetchpriority attributes
 */
function webp_img(string $src, string $alt = '', string $imgAttrs = '', string $sizeAttrs = ''): void {
    $cleanSrc = strtok($src, '?');
    $webpPath = preg_replace('/\.(png|jpe?g)$/i', '.webp', $cleanSrc);
    $absWebp  = $_SERVER['DOCUMENT_ROOT'] . $webpPath;

    $altEsc = htmlspecialchars($alt, ENT_QUOTES, 'UTF-8');

    if (file_exists($absWebp)) {
        echo '<picture>';
        echo '<source srcset="' . htmlspecialchars($webpPath, ENT_QUOTES) . '" type="image/webp">';
        echo '<img src="' . htmlspecialchars($src, ENT_QUOTES) . '" alt="' . $altEsc . '" ' . $sizeAttrs . ' ' . $imgAttrs . '>';
        echo '</picture>';
    } else {
        echo '<img src="' . htmlspecialchars($src, ENT_QUOTES) . '" alt="' . $altEsc . '" ' . $sizeAttrs . ' ' . $imgAttrs . '>';
    }
}
