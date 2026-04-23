<?php
/**
 * verify-email.php
 * Handles email verification links: /verify-email?token=xxx
 * Uses email_verification_tokens table (matches auth.php register flow)
 */
require_once 'php/config.php';
require_once 'php/jwt.php';

$token  = trim($_GET['token'] ?? '');
$status = 'pending'; // pending | success | already_verified | expired | invalid | error
$jwt    = '';
$user   = null;

if ($token) {
    try {
        $db = getDB();

        // Fetch all non-expired tokens and find matching one
        $rows  = $db->fetchAll("SELECT * FROM email_verification_tokens WHERE expires_at > UTC_TIMESTAMP()");
        $found = null;
        foreach ($rows as $row) {
            if (password_verify($token, $row['token_hash'])) {
                $found = $row;
                break;
            }
        }

        if (!$found) {
            // Check if token exists but expired
            $allRows = $db->fetchAll("SELECT * FROM email_verification_tokens");
            $anyMatch = false;
            foreach ($allRows as $row) {
                if (password_verify($token, $row['token_hash'])) { $anyMatch = true; break; }
            }
            $status = $anyMatch ? 'expired' : 'invalid';
        } else {
            $user = $db->fetchOne("SELECT * FROM users WHERE id = ?", [$found['user_id']]);

            if (!$user) {
                $status = 'invalid';
            } elseif ($user['is_verified']) {
                // Already verified — still auto-login
                $status = 'already_verified';
                $db->delete('email_verification_tokens', 'user_id = ?', [$user['id']]);
            } else {
                // Mark verified
                $db->update('users', ['is_verified' => 1], 'id = :id', [':id' => $user['id']]);
                $db->delete('email_verification_tokens', 'user_id = ?', [$user['id']]);
                $status = 'success';
            }

            // Issue JWT for auto-login on both success and already_verified
            if ($user && in_array($status, ['success', 'already_verified'])) {
                $jwt = createJWT([
                    'id'    => $user['id'],
                    'email' => $user['email'],
                    'name'  => $user['name'],
                    'role'  => $user['role'],
                ], JWT_SECRET);
            }
        }
    } catch (Exception $e) {
        error_log('Email verification error: ' . $e->getMessage());
        $status = 'error';
    }
}

$redirect = $_GET['redirect'] ?? '';
$safeRedirect = BASE_PATH . '/';
if ($redirect && preg_match('/^[a-zA-Z0-9\-_\/\.]+$/', $redirect)) {
    $safeRedirect = BASE_PATH . '/' . ltrim($redirect, '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | CSNExplore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{primary:'#ec5b13'},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <style>
        body { font-family: Inter, sans-serif; }
        .material-symbols-outlined { font-family: 'Material Symbols Outlined'; font-style: normal; display: inline-block; line-height: 1; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">

        <?php if ($status === 'success'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-green-600">check_circle</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Email Verified! 🎉</h1>
            <p class="text-gray-500 mb-2">Your account is now active.</p>
            <p class="text-gray-400 text-sm mb-8">Signing you in automatically…</p>
            <div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
        </div>

        <?php elseif ($status === 'already_verified'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-blue-600">verified</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Already Verified</h1>
            <p class="text-gray-500 mb-8">Your email was already verified. Signing you in…</p>
            <div class="w-8 h-8 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto"></div>
        </div>

        <?php elseif ($status === 'expired'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-orange-500">schedule</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Link Expired</h1>
            <p class="text-gray-500 mb-8">This verification link has expired (valid for 24 hours). Request a new one below.</p>
            <a href="<?php echo BASE_PATH; ?>/resend-verification"
               class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Resend Verification Email
            </a>
        </div>

        <?php elseif ($status === 'invalid'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-red-500">error</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Invalid Link</h1>
            <p class="text-gray-500 mb-8">This verification link is invalid or has already been used.</p>
            <div class="flex flex-col gap-3">
                <a href="<?php echo BASE_PATH; ?>/resend-verification"
                   class="inline-block bg-primary text-white font-bold py-3 px-8 rounded-xl hover:bg-orange-600 transition-all">
                    Resend Verification Email
                </a>
                <a href="<?php echo BASE_PATH; ?>/register"
                   class="inline-block text-slate-500 font-medium py-3 px-8 rounded-xl hover:text-primary transition-all text-sm">
                    Create a new account
                </a>
            </div>
        </div>

        <?php elseif ($status === 'error'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-red-500">warning</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Something went wrong</h1>
            <p class="text-gray-500 mb-8">We couldn't process your verification. Please try again or contact support.</p>
            <a href="<?php echo BASE_PATH; ?>/resend-verification"
               class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Try Again
            </a>
        </div>

        <?php else: ?>
        <!-- No token — just show info -->
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-gray-500">mark_email_unread</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Verify Your Email</h1>
            <p class="text-gray-500 mb-8">Click the verification link in your email to activate your account.</p>
            <a href="<?php echo BASE_PATH; ?>/resend-verification"
               class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Resend Verification Email
            </a>
        </div>
        <?php endif; ?>

        <div class="text-center mt-6">
            <a href="<?php echo BASE_PATH; ?>/" class="text-sm text-gray-400 hover:text-primary transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>

    <?php if ($jwt && $user): ?>
    <script>
    // Auto-login: store token + user, then redirect
    (function(){
        var jwt  = <?php echo json_encode($jwt); ?>;
        var user = <?php echo json_encode([
            'id'    => $user['id'],
            'email' => $user['email'],
            'name'  => $user['name'],
            'phone' => $user['phone'] ?? '',
            'role'  => $user['role'],
        ]); ?>;
        localStorage.setItem('csn_token', jwt);
        localStorage.setItem('csn_user',  JSON.stringify(user));
        setTimeout(function(){
            window.location.replace(<?php echo json_encode($safeRedirect); ?>);
        }, 1200);
    })();
    </script>
    <?php endif; ?>
</body>
</html>
