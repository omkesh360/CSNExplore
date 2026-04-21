<?php
require_once 'php/config.php';

$page_title = "Resend Verification Email | CSNExplore";
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $db = getDB();
            $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
            
            if (!$user) {
                $error = 'No account found with this email address.';
            } elseif ($user['is_verified']) {
                $error = 'This email is already verified. You can sign in.';
            } else {
                // Generate new token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
                
                $db->query("UPDATE users SET verification_token = ?, token_expires_at = ? WHERE id = ?", 
                    [$token, $expires, $user['id']]);
                
                // Send verification email
                require_once 'php/services/EmailService.php';
                $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
                $verifyLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . BASE_PATH . '/verify-email.php?token=' . $token;
                
                EmailService::sendVerificationEmail($email, $user['name'], $verifyLink);
                
                $success = true;
            }
        } catch (Exception $e) {
            error_log('Resend verification error: ' . $e->getMessage());
            $error = 'Something went wrong. Please try again.';
        }
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
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <?php if ($success): ?>
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-5xl text-green-600">mark_email_read</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 mb-3">Email Sent! 📧</h1>
                <p class="text-gray-600 mb-8">We've sent a new verification link to <strong><?php echo htmlspecialchars($email); ?></strong>. Please check your inbox and spam folder.</p>
                <a href="<?php echo BASE_PATH; ?>/login" class="inline-block bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                    Go to Login
                </a>
            </div>
            <?php else: ?>
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-5xl text-primary">forward_to_inbox</span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 mb-3">Resend Verification</h1>
                <p class="text-gray-600">Enter your email address and we'll send you a new verification link.</p>
            </div>
            
            <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
                <p class="text-sm text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-800 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required autocomplete="off"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                           placeholder="">
                </div>
                <button type="submit" class="w-full bg-primary text-white font-bold py-4 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                    Send Verification Email
                </button>
            </form>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-6">
            <a href="<?php echo BASE_PATH; ?>/login" class="text-sm text-gray-500 hover:text-primary transition-colors">
                ← Back to Login
            </a>
        </div>
    </div>
</body>
</html>
