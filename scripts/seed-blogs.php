<?php
require_once __DIR__ . '/../php/database.php';

$inputPath = __DIR__ . '/seed-data.json';
if (!file_exists($inputPath)) {
    die("seed-data.json not found. Run the node generator script first!\n");
}

$data = json_decode(file_get_contents($inputPath), true);
if (!$data) {
    die("Failed to parse seed-data.json\n");
}

$db = Database::getInstance();
$countInserted = 0;
$countUpdated = 0;

foreach ($data as $blog) {
    // Check if exists
    $existing = $db->fetchOne('SELECT id FROM blogs WHERE title = ?', [$blog['title']]);
    
    if (!$existing) {
        $db->insert('blogs', [
            'title' => $blog['title'],
            'content' => $blog['content'],
            'author' => $blog['author'],
            'status' => $blog['status'],
            'category' => $blog['category'],
            'read_time' => $blog['read_time'],
            'image' => $blog['image']
        ]);
        $countInserted++;
        echo "Inserted: {$blog['title']}\n";
    } else {
        // Update existing with new rich content
        $db->update('blogs', [
            'content' => $blog['content'],
            'image' => $blog['image']
        ], 'id = :id', [':id' => $existing['id']]);
        
        $countUpdated++;
        echo "Updated (already existed): {$blog['title']}\n";
    }
}

echo "\nDone! Inserted $countInserted new blogs and updated $countUpdated existing blogs.\n";
