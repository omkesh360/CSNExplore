<?php
require 'php/config.php';
$db = getDB();

$tables = ['bikes', 'stays', 'cars', 'restaurants', 'attractions', 'buses'];
foreach ($tables as $table) {
    $nameCol = ($table === 'buses') ? 'operator' : 'name';
    $rows = $db->fetchAll("SELECT id, $nameCol as name, image FROM $table ORDER BY id");
    $bad = 0;
    foreach ($rows as $r) {
        $img = $r['image'] ?? '';
        // Bad if: hotel-renaissance, empty, or non-existent local path
        $isBad = (
            strpos($img, 'hotel-renaissance') !== false ||
            empty($img) ||
            (strpos($img, 'http') !== 0 && !file_exists(__DIR__ . '/' . ltrim($img, '/')))
        );
        if ($isBad) {
            echo "[$table] ID={$r['id']} name=" . substr($r['name'],0,25) . " | BAD IMG: " . substr($img,0,60) . "\n";
            $bad++;
        }
    }
    $total = count($rows);
    echo "[$table] $bad/$total have bad images\n\n";
}
?>
