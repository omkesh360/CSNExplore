<?php
// Dynamic authentication API

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Remove /auth prefix from path
$path = preg_replace('#^/auth#', '', $path);
if (empty($path)) $path = '/';

try {
    $db = getDB();
    
    // Login
    if ($method === 'POST' && $path === '/login') {
        $input = getJsonInput();
        
        if (!isset($input['email']) || !isset($input['password'])) {
            sendError('Email and password are required', 400);
        }
        
        // Find user
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$input['email']]);
        
        if (!$user) {
            sendError('Invalid credentials', 401);
        }
        
        // Verify password
        if (!password_verify($input['password'], $user['password_hash'])) {
            sendError('Invalid credentials', 401);
        }
        
        // Generate JWT
        $token = createJWT([
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ], JWT_SECRET);
        
        sendJson([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ]
        ]);
    }
    
    // Register
    elseif ($method === 'POST' && $path === '/register') {
        $input = getJsonInput();
        
        if (!isset($input['email']) || !isset($input['password']) || !isset($input['name'])) {
            sendError('Email, password, and name are required', 400);
        }
        
        // Check if user exists
        $existing = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$input['email']]);
        
        if ($existing) {
            sendError('Email already registered', 400);
        }
        
        // Hash password
        $passwordHash = password_hash($input['password'], PASSWORD_DEFAULT);
        
        // Insert user
        $userId = $db->insert('users', [
            'email' => $input['email'],
            'password_hash' => $passwordHash,
            'name' => $input['name'],
            'phone' => $input['phone'] ?? null,
            'role' => 'user',
            'is_verified' => 0
        ]);
        
        // Generate JWT
        $token = createJWT([
            'id' => $userId,
            'email' => $input['email'],
            'name' => $input['name'],
            'role' => 'user'
        ], JWT_SECRET);
        
        sendJson([
            'token' => $token,
            'user' => [
                'id' => $userId,
                'email' => $input['email'],
                'name' => $input['name'],
                'role' => 'user'
            ]
        ], 201);
    }
    
    // Verify token
    elseif ($method === 'GET' && $path === '/verify') {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
        $token = str_replace('Bearer ', '', $token);
        
        if (!$token) {
            sendError('Token required', 401);
        }
        
        $decoded = verifyJWT($token, JWT_SECRET);
        
        if (!$decoded) {
            sendError('Invalid token', 401);
        }
        
        sendJson(['valid' => true, 'user' => $decoded]);
    }
    
    else {
        sendError('Not found', 404);
    }
    
} catch (Exception $e) {
    error_log('Auth API error: ' . $e->getMessage());
    sendError('Internal server error: ' . $e->getMessage(), 500);
}
