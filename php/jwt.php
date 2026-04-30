<?php
function b64ue($d) { return rtrim(strtr(base64_encode($d), '+/', '-_'), '='); }
function b64ud($d) { return base64_decode(strtr($d, '-_', '+/')); }

function createJWT($payload, $secret, $exp = 28800) {
    $header  = b64ue(json_encode(['typ'=>'JWT','alg'=>'HS256']));
    $payload['iat'] = time();
    $payload['exp'] = time() + $exp;
    $body    = b64ue(json_encode($payload));
    $sig     = b64ue(hash_hmac('sha256', "$header.$body", $secret, true));
    return "$header.$body.$sig";
}

function verifyJWT($jwt, $secret) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    [$h, $b, $s] = $parts;
    $expected = b64ue(hash_hmac('sha256', "$h.$b", $secret, true));
    if (!hash_equals($expected, $s)) return false;
    $payload = json_decode(b64ud($b), true);
    if (!$payload || $payload['exp'] < time()) return false;
    return $payload;
}

function getAuthToken() {
    // Try multiple methods to get the Authorization header
    $auth = null;
    
    // Method 1: getallheaders() if available
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
    }
    
    // Method 2: $_SERVER variables
    if (!$auth && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    }
    
    // Method 3: Apache mod_rewrite passthrough
    if (!$auth && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    
    // Method 4: Check for Authorization in $_SERVER with different cases
    foreach ($_SERVER as $key => $value) {
        if (strtolower($key) === 'http_authorization') {
            $auth = $value;
            break;
        }
    }
    
    if (!$auth) {
        return null;
    }
    
    // Remove 'Bearer ' prefix if present
    if (stripos($auth, 'Bearer ') === 0) {
        return substr($auth, 7);
    }
    
    return $auth;
}

function verifyToken() {
    $token = getAuthToken();
    if (!$token) sendError('Unauthorized', 401);
    
    // Ensure JWT_SECRET is defined
    if (!defined('JWT_SECRET')) {
        error_log('JWT_SECRET is not defined in verifyToken()');
        sendError('Server configuration error', 500);
    }
    
    $payload = verifyJWT($token, JWT_SECRET);
    if (!$payload) sendError('Invalid or expired token', 401);
    return $payload;
}

function isAdmin($user) {
    return isset($user['role']) && strtolower($user['role']) === 'admin';
}

function requireAdmin() {
    $user = verifyToken();
    if (!isAdmin($user)) sendError('Admin access required', 403);
    return $user;
}
