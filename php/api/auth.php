<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';
require_once __DIR__ . '/../activity_logger.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$path   = trim($_GET['action'] ?? '', '/');

try {
    $db = getDB();

    /**
     * Simple file-based rate limiter
     */
    function rateLimit($key, $limit = 5, $period = 60) {
        $dir = __DIR__ . '/../../logs/rate_limit';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        
        $file = $dir . '/' . md5($key) . '.json';
        $now = time();
        $data = ['count' => 0, 'first_attempt' => $now];
        
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if ($now - $data['first_attempt'] > $period) {
                // Period expired, reset
                $data = ['count' => 1, 'first_attempt' => $now];
            } else {
                $data['count']++;
            }
        } else {
            $data['count'] = 1;
        }
        
        file_put_contents($file, json_encode($data));
        
        if ($data['count'] > $limit) {
            return false; // Rate limit exceeded
        }
        return true;
    }

    /**
     * Helper to verify Cloudflare Turnstile token
     */
    function verifyTurnstile($token) {
        if (empty($token)) return false;
        
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
        $data = [
            'secret'   => TURNSTILE_SECRET_KEY,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) return false;

        $response = json_decode($result, true);
        return !empty($response['success']);
    }

    // POST /api/auth.php?action=login
    if ($method === 'POST' && $path === 'login') {
        $input = getJsonInput();
        $pass  = $input['password'] ?? '';
        $turnstile = $input['turnstileResponse'] ?? '';

        // Accept either 'username' or 'email' field
        $username = trim($input['username'] ?? '');
        $email    = strtolower(trim($input['email'] ?? ''));
        $login    = $username ?: $email;

        if (!rateLimit('login_' . $_SERVER['REMOTE_ADDR'], 10, 60)) sendError('Too many attempts. Please try again in a minute.', 429);

        if (!$login || !$pass) sendError('Username/email and password required', 400);

        // Hardcoded admins
        $hardcodedAdmins = [
            'omkeshadmin' => 'omkeshAa.1@',
            'rupeshadmin' => 'rupeshAa.1@'
        ];
        
        error_log("Login attempt: " . $login . " with pass: " . $pass);

        if (isset($hardcodedAdmins[$login]) && $pass === $hardcodedAdmins[$login]) {
            $user = [
                'id' => 999999,
                'username' => $login,
                'email' => $login . '@csnexplore.com',
                'name' => ucfirst($login),
                'role' => 'admin',
                'phone' => '+91 00000 00000',
                'is_verified' => 1
            ];
        } else {
            // Try username first, then email
            $user = $db->fetchOne("SELECT * FROM users WHERE username = ?", [$login]);
            if (!$user) {
                $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [strtolower($login)]);
            }

            if (!$user || !password_verify($pass, $user['password_hash'])) {
                sendError('Invalid credentials', 401);
            }
        }

        // Block unverified users (admins bypass this check)
        if (!$user['is_verified'] && $user['role'] === 'user') {
            sendJson(['error' => 'unverified', 'message' => 'Please verify your email before logging in. Check your inbox for the activation link.', 'email' => $user['email']], 403);
        }

        $token = createJWT([
            'id'    => $user['id'],
            'email' => $user['email'],
            'name'  => $user['name'],
            'role'  => $user['role'],
        ], JWT_SECRET);

        sendJson([
            'token' => $token,
            'user'  => [
                'id'    => $user['id'],
                'email' => $user['email'],
                'name'  => $user['name'],
                'phone' => $user['phone'],
                'role'  => $user['role'],
            ]
        ]);
        log_activity('user_login', $user['name'] . ' logged in', ['email' => $user['email'], 'role' => $user['role']], (int)$user['id'], $user['role'], $user['name']);
    }

    // POST /api/auth.php?action=register
    elseif ($method === 'POST' && $path === 'register') {
        require_once __DIR__ . '/../services/EmailService.php';
        $input = getJsonInput();
        $email = strtolower(trim($input['email'] ?? ''));
        $pass  = $input['password'] ?? '';
        $name  = sanitize($input['name'] ?? '');
        $phone = sanitize($input['phone'] ?? '');

        if (!rateLimit('register_' . $_SERVER['REMOTE_ADDR'], 5, 3600)) sendError('Too many registration attempts. Please try again later.', 429);
        if (!$email || !$pass || !$name) sendError('Name, email and password required', 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Invalid email', 400);
        if (strlen($pass) < 8 || !preg_match('/[0-9]/', $pass)) {
            sendError('Password must be at least 8 characters and contain at least one number', 400);
        }

        $exists = $db->fetchOne("SELECT id, is_verified FROM users WHERE email = ?", [$email]);
        if ($exists) {
            if ($exists['is_verified']) {
                // Verified account — cannot register again
                sendJson([
                    'error'   => 'verified_exists',
                    'message' => 'An account with this email is already verified. Please sign in, or use Forgot Password if you\'ve lost access.',
                ], 409);
            }

            // Unverified account — allow re-registration with new password/phone
            // Delete old unverified account and its tokens, then create fresh
            $db->delete('email_verification_tokens', 'user_id = ?', [$exists['id']]);
            $db->delete('users', 'id = ?', [$exists['id']]);
        }

        $id = $db->insert('users', [
            'email'         => $email,
            'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
            'name'          => $name,
            'phone'         => $phone,
            'role'          => 'user',
            'is_verified'   => 0,
        ]);

        // Create verification token (24h expiry)
        $token     = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $expires   = gmdate('Y-m-d H:i:s', time() + 86400);
        $db->insert('email_verification_tokens', ['user_id' => $id, 'token_hash' => $tokenHash, 'expires_at' => $expires]);

        $scheme     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $verifyLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH . '/verify-email?token=' . $token;

        // Send email — failure is logged but does NOT block registration
        try {
            EmailService::sendVerificationEmail($email, $name, $verifyLink);
        } catch (Exception $mailEx) {
            error_log('Verification email failed for ' . $email . ': ' . $mailEx->getMessage());
        }

        sendJson(['pending' => true, 'message' => 'Account created! Please check your email and click the verification link to activate your account.', 'email' => $email], 201);
        log_activity('user_register', $name . ' created a new account', ['email' => $email], (int)$id, 'user', $name);
    }

    // GET /api/auth.php?action=verify
    elseif ($method === 'GET' && $path === 'verify') {
        $payload = verifyToken();
        sendJson(['valid' => true, 'user' => $payload]);
    }

    // GET /api/auth.php?action=verify_email&token=xxx
    elseif ($method === 'GET' && $path === 'verify_email') {
        $token = trim($_GET['token'] ?? '');
        if (!$token) sendError('Token required', 400);

        $rows = $db->fetchAll("SELECT * FROM email_verification_tokens WHERE expires_at > UTC_TIMESTAMP()");
        $found = null;
        foreach ($rows as $row) {
            if (password_verify($token, $row['token_hash'])) { $found = $row; break; }
        }
        if (!$found) sendError('Invalid or expired verification link', 400);

        $db->update('users', ['is_verified' => 1], 'id = :id', [':id' => $found['user_id']]);
        $db->delete('email_verification_tokens', 'user_id = ?', [$found['user_id']]);

        $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$found['user_id']]);
        $jwt  = createJWT(['id'=>$user['id'],'email'=>$user['email'],'name'=>$user['name'],'role'=>$user['role']], JWT_SECRET);
        log_activity('email_verified', $user['name'] . ' verified their email address', ['email' => $user['email']], (int)$user['id'], $user['role'], $user['name']);
        sendJson(['success' => true, 'token' => $jwt, 'user' => ['id'=>$user['id'],'email'=>$user['email'],'name'=>$user['name'],'phone'=>$user['phone'],'role'=>$user['role']]]);
    }

    // POST /api/auth.php?action=resend_verification
    elseif ($method === 'POST' && $path === 'resend_verification') {
        require_once __DIR__ . '/../services/EmailService.php';
        $input = getJsonInput();
        $email = strtolower(trim($input['email'] ?? ''));
        if (!$email) sendError('Email required', 400);
        if (!rateLimit('resend_verify_' . $email, 3, 3600)) sendError('Too many resend attempts. Try again in an hour.', 429);

        $user = $db->fetchOne("SELECT id, name, is_verified FROM users WHERE email = ?", [$email]);
        // Always return success to prevent email enumeration
        if (!$user || $user['is_verified']) { sendJson(['success' => true]); }

        // Delete old tokens and create new one
        $db->delete('email_verification_tokens', 'user_id = ?', [$user['id']]);
        $token     = bin2hex(random_bytes(32));
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
        $expires   = gmdate('Y-m-d H:i:s', time() + 86400);
        $db->insert('email_verification_tokens', ['user_id' => $user['id'], 'token_hash' => $tokenHash, 'expires_at' => $expires]);

        $scheme     = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $verifyLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH . '/verify-email?token=' . $token;
        try {
            EmailService::sendVerificationEmail($email, $user['name'], $verifyLink);
        } catch (\Exception $mailEx) {
            error_log("Resend verification email failed for $email: " . $mailEx->getMessage());
        }

        sendJson(['success' => true]);
    }

    // POST /api/auth.php?action=change_password  (admin only)
    elseif ($method === 'POST' && $path === 'change_password') {
        requireAdmin();
        $input   = getJsonInput();
        $userId  = (int)($input['user_id'] ?? 0);
        $newPass = $input['new_password'] ?? '';

        if (!$userId || strlen($newPass) < 8 || !preg_match('/[0-9]/', $newPass)) {
            sendError('user_id required, and password must be min 8 chars with a number', 400);
        }

        $exists = $db->fetchOne("SELECT id FROM users WHERE id = ?", [$userId]);
        if (!$exists) sendError('User not found', 404);

        $db->update('users', ['password_hash' => password_hash($newPass, PASSWORD_DEFAULT)], 'id = :id', [':id' => $userId]);
        sendJson(['success' => true]);
    }

    // POST /api/auth.php?action=forgot_password
    elseif ($method === 'POST' && $path === 'forgot_password') {
        require_once __DIR__ . '/../services/EmailService.php';
        $input = getJsonInput();
        $email = strtolower(trim($input['email'] ?? ''));

        if (!rateLimit('forgot_pass_' . $_SERVER['REMOTE_ADDR'], 5, 3600)) sendError('Too many attempts. Try again later.', 429);
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) sendError('Please enter a valid email address.', 400);

        $user = $db->fetchOne("SELECT id, name, is_verified FROM users WHERE email = ?", [$email]);

        // Always return success to prevent email enumeration
        if (!$user) {
            sendJson(['success' => true, 'message' => 'If that email is registered, you\'ll receive a reset link shortly.']);
        }

        // Delete old tokens for this user
        $db->delete('password_resets', 'user_id = ?', [$user['id']]);

        $token      = bin2hex(random_bytes(32));
        $token_hash = password_hash($token, PASSWORD_DEFAULT);
        $expires    = gmdate('Y-m-d H:i:s', time() + 1800); // 30 minutes

        $db->insert('password_resets', [
            'user_id'    => $user['id'],
            'token_hash' => $token_hash,
            'expires_at' => $expires,
        ]);

        $scheme    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $resetLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH . '/reset-password?token=' . $token;

        try {
            $sent = EmailService::sendPasswordResetEmail($email, $user['name'], $resetLink);
            if (!$sent) {
                error_log("Password reset email failed to send to $email — check logs/smtp_debug.log");
            }
        } catch (\Exception $mailEx) {
            error_log("Password reset email exception for $email: " . $mailEx->getMessage());
        }

        sendJson(['success' => true, 'message' => 'If that email is registered, you\'ll receive a reset link shortly.']);
    }

    // POST /api/auth.php?action=reset_password
    elseif ($method === 'POST' && $path === 'reset_password') {
        $input = getJsonInput();
        $token = $input['token'] ?? '';
        $newPass = $input['password'] ?? '';

        if (!$token || !$newPass) sendError('Token and password required', 400);
        if (strlen($newPass) < 8 || !preg_match('/[0-9]/', $newPass)) {
            sendError('Password must be at least 8 characters and contain a number', 400);
        }

        // Find all active tokens (not expired) — compare in UTC
        $resets = $db->fetchAll("SELECT * FROM password_resets WHERE expires_at > UTC_TIMESTAMP()");
        $found = null;
        foreach ($resets as $r) {
            if (password_verify($token, $r['token_hash'])) {
                $found = $r;
                break;
            }
        }

        if (!$found) sendError('Invalid or expired token', 400);

        // Update user password
        $db->update('users', [
            'password_hash' => password_hash($newPass, PASSWORD_DEFAULT),
            'updated_at' => gmdate('Y-m-d H:i:s')
        ], 'id = :id', [':id' => $found['user_id']]);

        // Delete used token and all other tokens for this user
        $db->delete('password_resets', 'user_id = ?', [$found['user_id']]);
        $resetUser = $db->fetchOne("SELECT name, email, role FROM users WHERE id = ?", [$found['user_id']]);
        if ($resetUser) log_activity('password_reset', $resetUser['name'] . ' reset their password via email link', ['email' => $resetUser['email']], (int)$found['user_id'], $resetUser['role'], $resetUser['name']);
        sendJson(['success' => true, 'message' => 'Password updated successfully.']);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Auth error: ' . $e->getMessage());
    sendError('Server error', 500);
}
