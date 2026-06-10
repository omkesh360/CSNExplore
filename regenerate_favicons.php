<?php
/**
 * regenerate_favicons.php
 * Generates all standard favicon sizes from the master fevicon.png
 * Run once via: http://localhost/CSNExplore/regenerate_favicons.php
 * Or via CLI: php regenerate_favicons.php
 */

$srcFile = __DIR__ . '/images/fevicon/fevicon.png';
$outDir  = __DIR__ . '/images/fevicon/';

if (!file_exists($srcFile)) {
    die("ERROR: Source file not found: $srcFile\n");
}
if (!function_exists('imagecreatefrompng')) {
    die("ERROR: PHP GD extension is not enabled.\n");
}

$src = imagecreatefrompng($srcFile);
if (!$src) {
    die("ERROR: Could not open source image.\n");
}
imagealphablending($src, false);
imagesavealpha($src, true);

// ── All sizes to generate ──────────────────────────────────────────────────
$sizes = [
    // Standard favicons
    ['file' => 'favicon-16x16.png',         'w' => 16,  'h' => 16],
    ['file' => 'favicon-32x32.png',         'w' => 32,  'h' => 32],
    ['file' => 'favicon-48x48.png',         'w' => 48,  'h' => 48],
    ['file' => 'favicon-96x96.png',         'w' => 96,  'h' => 96],

    // Apple Touch Icons
    ['file' => 'apple-icon-57x57.png',      'w' => 57,  'h' => 57],
    ['file' => 'apple-icon-60x60.png',      'w' => 60,  'h' => 60],
    ['file' => 'apple-icon-72x72.png',      'w' => 72,  'h' => 72],
    ['file' => 'apple-icon-76x76.png',      'w' => 76,  'h' => 76],
    ['file' => 'apple-icon-114x114.png',    'w' => 114, 'h' => 114],
    ['file' => 'apple-icon-120x120.png',    'w' => 120, 'h' => 120],
    ['file' => 'apple-icon-144x144.png',    'w' => 144, 'h' => 144],
    ['file' => 'apple-icon-152x152.png',    'w' => 152, 'h' => 152],
    ['file' => 'apple-icon-180x180.png',    'w' => 180, 'h' => 180],
    ['file' => 'apple-touch-icon.png',      'w' => 180, 'h' => 180],

    // Android Chrome
    ['file' => 'android-chrome-192x192.png','w' => 192, 'h' => 192],
    ['file' => 'android-icon-192x192.png',  'w' => 192, 'h' => 192],
    ['file' => 'android-chrome-512x512.png','w' => 512, 'h' => 512],
    ['file' => 'android-icon-512x512.png',  'w' => 512, 'h' => 512],

    // Microsoft Tiles
    ['file' => 'ms-icon-144x144.png',       'w' => 144, 'h' => 144],
    ['file' => 'ms-icon-150x150.png',       'w' => 150, 'h' => 150],
    ['file' => 'ms-icon-310x310.png',       'w' => 310, 'h' => 310],
    ['file' => 'ms-icon-70x70.png',         'w' => 70,  'h' => 70],
];

$srcW = imagesx($src);
$srcH = imagesy($src);
$results = [];

foreach ($sizes as $s) {
    $dst = imagecreatetruecolor($s['w'], $s['h']);
    imagealphablending($dst, false);
    imagesavealpha($dst, true);
    $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
    imagefilledrectangle($dst, 0, 0, $s['w'], $s['h'], $transparent);

    imagecopyresampled($dst, $src, 0, 0, 0, 0, $s['w'], $s['h'], $srcW, $srcH);

    $outFile = $outDir . $s['file'];
    $ok = imagepng($dst, $outFile, 9);
    imagedestroy($dst);
    $results[] = ['file' => $s['file'], 'ok' => $ok, 'size' => $s['w'] . 'x' . $s['h']];
}
imagedestroy($src);

// ── Generate favicon.ico (multi-size ICO using 16x16 PNG bytes) ───────────
// A simple ICO generator that writes a 16x16 PNG-based ICO
function createIco($sizes, $outDir, $icoFile) {
    $images = [];
    foreach ($sizes as $size) {
        $file = $outDir . "favicon-{$size}x{$size}.png";
        if (!file_exists($file)) continue;
        $data = file_get_contents($file);
        if (!$data) continue;
        $images[] = ['data' => $data, 'w' => $size, 'h' => $size];
    }
    if (empty($images)) return false;

    // ICO header
    $count = count($images);
    $ico = pack('vvv', 0, 1, $count);

    $offset = 6 + ($count * 16);
    $dirEntries = '';
    $imageData  = '';

    foreach ($images as $img) {
        $dataLen = strlen($img['data']);
        $w = $img['w'] >= 256 ? 0 : $img['w'];
        $h = $img['h'] >= 256 ? 0 : $img['h'];
        $dirEntries .= pack('CCCCvvVV', $w, $h, 0, 0, 1, 32, $dataLen, $offset);
        $imageData  .= $img['data'];
        $offset     += $dataLen;
    }
    return file_put_contents($outDir . $icoFile, $ico . $dirEntries . $imageData) !== false;
}

$icoOk = createIco([16, 32, 48], $outDir, 'favicon.ico');
$results[] = ['file' => 'favicon.ico', 'ok' => $icoOk, 'size' => '16+32+48'];

// ── Output ─────────────────────────────────────────────────────────────────
$cli = php_sapi_name() === 'cli';
if (!$cli) { echo '<pre style="font-family:monospace;font-size:13px;background:#0f172a;color:#e2e8f0;padding:24px;border-radius:8px;">'; }
echo "CSNExplore Favicon Generator\n";
echo "============================\n";
echo "Source: $srcFile\n\n";
$ok = 0; $fail = 0;
foreach ($results as $r) {
    $status = $r['ok'] ? '✅' : '❌';
    echo "$status  {$r['file']} ({$r['size']})\n";
    $r['ok'] ? $ok++ : $fail++;
}
echo "\n";
echo "Done: $ok generated, $fail failed.\n";
if (!$cli) { echo '</pre>'; }
