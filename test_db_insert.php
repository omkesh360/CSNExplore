<?php
require 'c:/xampp/htdocs/CSNExplore/php/config.php';
$db = getDB();
try {
    $newId = $db->insert('blogs', [
        'title' => 'Test Blog '.time(),
        'content' => '<p>Test content</p>',
        'author' => 'Admin',
        'status' => 'published',
        'slug' => 'test-blog-'.time()
    ]);
    echo 'Inserted ID: ' . $newId;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
