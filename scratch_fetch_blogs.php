<?php
require_once __DIR__ . '/php/config.php';

try {
    $db = getDB();
    $blogs = $db->fetchAll("SELECT id, title, category, status FROM blogs");
    echo "Current Blogs:\n";
    foreach ($blogs as $blog) {
        echo "ID: {$blog['id']} | Title: {$blog['title']} | Category: {$blog['category']} | Status: {$blog['status']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
