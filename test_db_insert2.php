<?php
require 'c:/xampp/htdocs/CSNExplore/php/config.php';
$db = getDB();
try {
    $newId = $db->insert('blogs', [
        'title' => 'Test Blog 2 '.time(),
        'content' => '<p>Test content 2</p>',
        'author' => 'Admin',
        'status' => 'published',
        'slug' => 'test-blog-2-'.time(),
        'seo_score' => 50,
        'linked_listings' => '[]'
    ]);
    echo 'Inserted ID: ' . $newId;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
