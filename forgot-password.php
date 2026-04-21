<?php
require_once 'php/config.php';
$page_title = "Forgot Password | CSNExplore";
$current_page = "forgot-password.php";

$page_meta = [
    'description' => "Reset your CSNExplore account password to regain access to your bookings and itineraries.",
    'canonical'   => BASE_PATH . '/forgot-password',
    'type'        => 'website'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Forgot Password | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/forgot-password"
}
</script>';

include 'header.php';
?>

<main class="min-h-screen bg-[#f4f5f7] flex items-center justify-center p-6 pt-24">
    <div class="glassy max-w-md w-full p-8 rounded-3xl shadow-2xl relative overflow-hidden group">
        <div class="absolute -top-24 -right-24 w-48 h-48 bg-primary/10 rounded-full blur-3xl group-hover:bg-primary/20 transition-all duration-700"></div>
        
        <div class="relative z-10">
            <div class="mb-8">
                <h1 class="text-3xl font-serif font-black text-slate-900 mb-2">Forgot Password?</h1>
                <p class="text-slate-500">No worries, it happens. Enter your email and we'll send you a reset link.</p>
            </div>

            <form id="forgot-form" class="space-y-6">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-widest px-1">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">mail</span>
                        <input type="email" id="email" required placeholder="" autocomplete="off"
                               class="w-full bg-white border-2 border-slate-100 rounded-2xl pl-12 pr-4 py-4 text-sm focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all"/>
                    </div>
                </div>

                <div id="msg" class="hidden text-sm p-4 rounded-xl"></div>

                <button type="submit" id="submit-btn" class="w-full bg-primary text-white font-black py-4 rounded-2xl hover:bg-orange-600 active:scale-95 transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <span>Send Reset Link</span>
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Remembered your password? 
                    <a href="login" class="text-primary font-bold hover:underline">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</main>

<script>
document.getElementById('forgot-form').onsubmit = async (e) => {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const msg = document.getElementById('msg');
    const email = document.getElementById('email').value;

    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin">sync</span> Sending...';
    msg.className = 'hidden';

    try {
        const res = await fetch('php/api/auth.php?action=forgot_password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        const data = await res.json();
        
        btn.disabled = false;
        btn.innerHTML = '<span>Send Reset Link</span><span class="material-symbols-outlined">arrow_forward</span>';

        if (res.ok) {
            msg.className = 'text-green-800 bg-green-50 p-4 rounded-xl text-sm font-medium block border border-green-100';
            msg.textContent = data.message;
            document.getElementById('forgot-form').reset();
        } else {
            msg.className = 'text-red-800 bg-red-50 p-4 rounded-xl text-sm font-medium block border border-red-100';
            msg.textContent = data.error || 'Failed to send reset link.';
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = '<span>Send Reset Link</span><span class="material-symbols-outlined">arrow_forward</span>';
        msg.className = 'text-red-800 bg-red-50 p-4 rounded-xl text-sm font-medium block border border-red-100';
        msg.textContent = 'Network error. Please try again.';
    }
};
</script>

<?php include 'footer.php'; ?>
