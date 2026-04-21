<?php
require_once 'php/config.php';

$page_title = "Verify Your Email | CSNExplore";
$token = $_GET['token'] ?? '';
$status = 'pending'; // pending, success, expired, invalid

if ($token) {
    try {
        $db = getDB();
        
        // Find user with this token
        $user = $db->fetchOne("SELECT * FROM users WHERE verification_token = ?", [$token]);
        
        if (!$user) {
            $status = 'invalid';
        } elseif ($user['is_verified']) {
            $status = 'already_verified';
        } elseif ($user['token_expires_at'] && strtotime($user['token_expires_at']) < time()) {
            $status = 'expired';
        } else {
            // Verify the user
            $db->query("UPDATE users SET is_verified = 1, verification_token = NULL, token_expires_at = NULL WHERE id = ?", [$user['id']]);
            $status = 'success';
        }
    } catch (Exception $e) {
        error_log('Email verification error: ' . $e->getMessage());
        $status = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{colors:{primary:'#ec5b13'},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
</head>
<body class="bg-gradient-to-br from-slate-50 to-orange-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <?php if ($status === 'success'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-green-600">check_circle</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Email Verified! 🎉</h1>
            <p class="text-gray-600 mb-8">Your email has been successfully verified. You can now access all features and start booking!</p>
            <a href="<?php echo BASE_PATH; ?>/login" class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Sign In Now
            </a>
        </div>
        
        <?php elseif ($status === 'already_verified'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-blue-600">verified</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Already Verified</h1>
            <p class="text-gray-600 mb-8">This email has already been verified. You can sign in to your account.</p>
            <a href="<?php echo BASE_PATH; ?>/login" class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Sign In
            </a>
        </div>
        
        <?php elseif ($status === 'expired'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-orange-600">schedule</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Link Expired</h1>
            <p class="text-gray-600 mb-8">This verification link has expired. Please request a new one.</p>
            <a href="<?php echo BASE_PATH; ?>/resend-verification" class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Resend Verification Email
            </a>
        </div>
        
        <?php elseif ($status === 'invalid'): ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-red-600">error</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Invalid Link</h1>
            <p class="text-gray-600 mb-8">This verification link is invalid or has already been used.</p>
            <a href="<?php echo BASE_PATH; ?>/register" class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                Create Account
            </a>
        </div>
        
        <?php else: ?>
        <div class="bg-white rounded-3xl shadow-2xl p-8 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-5xl text-gray-600">mail</span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 mb-3">Verify Your Email</h1>
            <p class="text-gray-600 mb-8">Please click the verification link in your email to activate your account.</p>
        </div>
        <?php endif; ?>
        
        <div class="text-center mt-6">
            <a href="<?php echo BASE_PATH; ?>/" class="text-sm text-gray-500 hover:text-primary transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>
</body>
</html>
