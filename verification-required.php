<?php
require_once 'php/config.php';

// Get user email from session/token if available
$userEmail = '';
if (isset($_COOKIE['csn_token'])) {
    try {
        require_once 'php/jwt.php';
        $payload = verifyJWT($_COOKIE['csn_token'], JWT_SECRET);
        $userEmail = $payload['email'] ?? '';
    } catch (Exception $e) {
        // Invalid token
    }
}

$page_title = "Verification Required | CSNExplore";
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
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-12">
            <div class="text-center">
                <div class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-symbols-outlined text-6xl text-primary">lock</span>
                </div>
                
                <h1 class="text-4xl font-black text-gray-900 mb-4">Verification Required 🔒</h1>
                
                <p class="text-lg text-gray-600 mb-6">
                    We've sent a verification link to <strong class="text-gray-900"><?php echo $userEmail ? htmlspecialchars($userEmail) : 'your email'; ?></strong>. 
                    To keep our community secure and confirm your bookings, please click the link in that email.
                </p>
                
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-xl mb-8 text-left">
                    <p class="text-sm text-blue-900 font-medium mb-2">📧 Don't see it?</p>
                    <ul class="text-sm text-blue-800 space-y-1 ml-4">
                        <li>• Check your spam or junk folder</li>
                        <li>• Make sure you entered the correct email</li>
                        <li>• Wait a few minutes for the email to arrive</li>
                    </ul>
                </div>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded-xl mb-8 text-left">
                    <p class="text-sm text-yellow-900 font-bold mb-2">⚠️ Important</p>
                    <p class="text-sm text-yellow-800">You won't be able to finalize your booking until your email is verified.</p>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo BASE_PATH; ?>/resend-verification" class="inline-flex items-center justify-center gap-2 bg-primary text-white font-bold py-4 px-8 rounded-xl hover:bg-orange-600 transition-all shadow-lg">
                        <span class="material-symbols-outlined">forward_to_inbox</span>
                        Resend Verification Email
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/logout" class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-bold py-4 px-8 rounded-xl hover:bg-gray-200 transition-all">
                        <span class="material-symbols-outlined">logout</span>
                        Sign Out
                    </a>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-6">
            <a href="<?php echo BASE_PATH; ?>/" class="text-sm text-gray-500 hover:text-primary transition-colors">
                ← Back to Home
            </a>
        </div>
    </div>
</body>
</html>
