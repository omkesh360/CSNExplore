<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../rate-limiter.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['PATH_INFO'] ?? '/';

// Strip /auth prefix so routes match /login, /register, /me
$path = preg_replace('#^/auth#', '', $path) ?: '/';

// Route handling
if ($method === 'POST' && $path === '/register') {
    register();
} elseif ($method === 'POST' && $path === '/login') {
    login();
} elseif ($method === 'GET' && $path === '/me') {
    verifyMe();
} else {
    sendError('Not found', 404);
}

function register() {
    $input = getJsonInput();
    
    $name = trim($input['name'] ?? '');
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    if (!$name || !$email || !$password) {
        sendError('Name, email and password are required');
    }
    
    if (strlen($password) < 6) {
        sendError('Password must be at least 6 characters');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendError('Invalid email format');
    }
    
    $users = readJsonFile('users.json');
    
    // Check if email already exists
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            sendError('An account with this email already exists', 409);
        }
    }
    
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    $newUser = [
        'id' => (string)(time() * 1000),
        'name' => $name,
        'email' => $email,
        'role' => 'user',
        'password' => $hashedPassword,
        'createdAt' => date('c')
    ];
    
    $users[] = $newUser;
    writeJsonFile('users.json', $users);
    
    $token = createJWT([
        'id' => $newUser['id'],
        'email' => $newUser['email'],
        'role' => $newUser['role'],
        'name' => $newUser['name']
    ], JWT_SECRET, 7200);
    
    sendJson([
        'success' => true,
        'user' => [
            'id' => $newUser['id'],
            'email' => $newUser['email'],
            'name' => $newUser['name'],
            'role' => $newUser['role']
        ],
        'token' => $token
    ], 201);
}

function login() {
    $input = getJsonInput();
    
    $email = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    if (!$email || !$password) {
        sendError('Email and password are required');
    }
    
    $users = readJsonFile('users.json');
    
    $user = null;
    foreach ($users as $u) {
        if (strtolower($u['email']) === strtolower($email)) {
            $user = $u;
            break;
        }
    }
    
    if (!$user || !isset($user['password'])) {
        sendError('Invalid credentials', 401);
    }
    
    if (!password_verify($password, $user['password'])) {
        sendError('Invalid credentials', 401);
    }
    
    $token = createJWT([
        'id' => $user['id'],
        'email' => $user['email'],
        'role' => $user['role'],
        'name' => $user['name']
    ], JWT_SECRET, 28800);
    
    sendJson([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role' => $user['role']
        ],
        'token' => $token
    ]);
}

function verifyMe() {
    $user = verifyToken();
    sendJson(['success' => true, 'user' => $user]);
}
