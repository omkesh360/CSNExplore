<?php
$file = __DIR__ . '/../listing-detail/stays-1-hotel-renaissance-aurangabad.html';
$content = file_get_contents($file);
echo 'body open/close: ' . substr_count($content, '<body') . '/' . substr_count($content, '</body>') . PHP_EOL;
echo 'html open/close: ' . substr_count($content, '<html') . '/' . substr_count($content, '</html>') . PHP_EOL;
echo 'main open/close: ' . substr_count($content, '<main') . '/' . substr_count($content, '</main>') . PHP_EOL;
echo 'File size: ' . strlen($content) . ' bytes' . PHP_EOL;
if (strpos($content, 'Fatal error') !== false) echo 'FATAL ERROR IN HTML!' . PHP_EOL;
if (strpos($content, 'Parse error') !== false) echo 'PARSE ERROR IN HTML!' . PHP_EOL;
echo PHP_EOL . 'First 300 chars:' . PHP_EOL . substr($content, 0, 300) . PHP_EOL;
