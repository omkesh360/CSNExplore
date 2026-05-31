<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_AUTHORIZATION'] = 'Bearer dummy';

// Mock function to bypass JWT for testing
function requireAdmin() {}
function verifyJWT() { return ['role' => 'admin']; }

$input = json_encode([
    'title' => 'API Test Blog',
    'content' => '<p>Hello world</p>',
    'author' => 'Test',
    'status' => 'published',
    'slug' => ''
]);
file_put_contents('php://input', $input); // Can't easily mock php://input in PHP CLI without a stream wrapper, but let's try something else.

// Let's just create a wrapper script that requires blogs.php and mocks the functions.
