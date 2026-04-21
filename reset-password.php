<?php
require_once 'php/config.php';
$token = $_GET['token'] ?? '';
$page_title = "Reset Password | CSNExplore";
$current_page = 'reset-password.php';

$page_meta = [
    'description' => "Create a new password for your CSNExplore account to continue managing your travel bookings.",
    'canonical'   => BASE_PATH . '/reset-password',
    'type'        => 'website'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Reset Password | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/reset-password"
}
</script>';

include 'header.php';
?>

<main class="min-h-screen bg-[#f4f5f7] flex items-center justify-center p-6 pt-24">
    <div class="glassy max-w-md w-full p-8 rounded-3xl shadow-2xl relative overflow-hidden group">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all duration-700"></div>
        
        <div class="relative z-10">
            <?php if (!$token): ?>
                <div class="text-center py-12">
                    <span class="material-symbols-outlined text-5xl text-red-400 mb-4 block">error</span>
                    <h2 class="text-2xl font-serif font-black text-slate-900 mb-2">Invalid Token</h2>
                    <p class="text-slate-500 mb-6">This password reset link is invalid or has expired.</p>
                    <a href="forgot-password" class="bg-primary text-white px-8 py-3 rounded-full font-bold hover:bg-orange-600 transition-all shadow-lg">Request New Link</a>
                </div>
            <?php else: ?>
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-serif font-black text-slate-900 mb-2">Create New Password</h1>
                    <p class="text-slate-500">Security first! Choose a strong password you'll remember.</p>
                </div>

                <form id="reset-form" class="space-y-6">
                    <input type="hidden" id="token" value="<?php echo htmlspecialchars($token); ?>"/>
                    
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-widest px-1">New Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">lock</span>
                            <input type="password" id="password" required placeholder="" autocomplete="off"
                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all"/>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-widest px-1">Confirm New Password</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">verified_user</span>
                            <input type="password" id="confirm-password" required placeholder="" autocomplete="off"
                                   class="w-full bg-white border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all"/>
                        </div>
                    </div>

                    <div id="msg" class="hidden text-sm p-4 rounded-xl"></div>

                    <button type="submit" id="submit-btn" class="w-full bg-primary text-white font-black py-4 rounded-2xl hover:bg-orange-600 active:scale-95 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                        <span>Save Password</span>
                        <span class="material-symbols-outlined">check_circle</span>
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Back to 
                    <a href="login" class="text-primary font-bold hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
const form = document.getElementById('reset-form');
if (form) {
    form.onsubmit = async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submit-btn');
        const msg = document.getElementById('msg');
        const token = document.getElementById('token').value;
        const password = document.getElementById('password').value;
        const confirm = document.getElementById('confirm-password').value;

        if (password !== confirm) {
            msg.className = 'text-red-800 bg-red-50 p-4 rounded-xl text-sm font-medium block border border-red-100';
            msg.textContent = 'Passwords do not match.';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Saving...';
        msg.className = 'hidden';

        try {
            const res = await fetch('php/api/auth.php?action=reset_password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token, password })
            });
            const data = await res.json();
            
            btn.disabled = false;
            btn.innerHTML = '<span>Save Password</span><span class="material-symbols-outlined">check_circle</span>';

            if (res.ok) {
                msg.className = 'text-green-800 bg-green-50 p-4 rounded-xl text-sm font-medium block border border-green-100';
                msg.textContent = data.message + ' Redirecting to login...';
                setTimeout(() => window.location.href = 'login', 2500);
            } else {
                msg.className = 'text-red-800 bg-red-50 p-4 rounded-xl text-sm font-medium block border border-red-100';
                msg.textContent = data.error || 'Password reset failed.';
            }
        } catch (e) {
            btn.disabled = false;
            btn.innerHTML = '<span>Save Password</span><span class="material-symbols-outlined">check_circle</span>';
            msg.className = 'text-red-800 bg-red-50 p-4 rounded-xl text-sm font-medium block border border-red-100';
            msg.textContent = 'Network error. Please try again.';
        }
    };
}
</script>

<?php include 'footer.php'; ?>
