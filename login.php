<?php
$page_title = "Login | CSNExplore";
$current_page = "login.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Login to your CSNExplore account to manage your bookings, save your favorite travel spots, and connect with local trip experts in Chhatrapati Sambhajinagar.",
    'canonical'   => BASE_PATH . '/login',
    'type'        => 'website',
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "name": "Login | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/login"
}
</script>';
$extra_styles = "
    @keyframes slow-zoom { 0%{transform:scale(1)} 100%{transform:scale(1.1)} }
    .animate-slow-zoom { animation: slow-zoom 20s linear infinite alternate; }
";
require 'header.php';
?>
<script>
// If already logged in, redirect away immediately
(function(){
    var token = localStorage.getItem('csn_token');
    var user  = JSON.parse(localStorage.getItem('csn_user') || 'null');
    if (token && user) {
        try {
            var parts = token.split('.');
            if (parts.length === 3) {
                var b64 = parts[1].replace(/-/g,'+').replace(/_/g,'/'); while(b64.length%4) b64+='=';
                var p = JSON.parse(atob(b64));
                if (!p.exp || p.exp > Math.floor(Date.now()/1000)) {
                    var redirect = new URLSearchParams(window.location.search).get('redirect') || '';
                    window.location.replace(redirect || 'index');
                }
            }
        } catch(e) {}
    }
})();
</script>
<div class="flex min-h-screen">

    <!-- Left: Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-primary">
        <img src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1200&q=80" alt="Travel Background"
             class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80 animate-slow-zoom"/>
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/70 to-transparent"></div>
        <div class="relative z-10 w-full p-12 flex flex-col justify-between">
            <div></div>
            <div class="max-w-md">
                <h1 class="text-5xl font-serif font-black text-white leading-tight mb-6 tracking-tight">Your next adventure starts here.</h1>
                <p class="text-xl text-white/80 font-medium">Join thousands of travelers exploring the hidden gems of Chhatrapati Sambhajinagar and beyond.</p>
            </div>
            <div class="flex items-center gap-6 text-white/60 text-sm font-medium">
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">verified</span> 100% Secure</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">support_agent</span> 24/7 Support</span>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="w-full lg:w-1/2 flex flex-col items-center px-6 md:px-12 pt-6 pb-12 lg:py-12 bg-slate-50 relative overflow-y-auto min-h-screen lg:min-h-0">

        <div class="w-full max-w-md space-y-8 lg:my-auto mt-4">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight md:text-4xl">Welcome back</h2>
                <p class="mt-3 text-slate-500 font-medium">
                    Don't have an account?
                    <a href="register" class="text-primary hover:text-orange-600 font-bold transition-all border-b-2 border-primary/20 hover:border-primary">Create one for free</a>
                </p>
            </div>

            <!-- Error Container -->
            <div id="login-error" class="hidden mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 shrink-0">error</span>
                    <p class="text-sm text-red-700 font-medium" id="login-error-text">Something went wrong.</p>
                </div>
            </div>

            <!-- Unverified email notice -->
            <div id="unverified-notice" class="hidden bg-amber-50 border border-amber-300 rounded-xl p-4 space-y-2">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 shrink-0">mark_email_unread</span>
                    <div>
                        <p class="text-sm font-bold text-amber-900">Email not verified</p>
                        <p class="text-sm text-amber-800 mt-0.5">Please click the link we sent to <strong id="unverified-email"></strong> to activate your account.</p>
                    </div>
                </div>
                <button onclick="resendFromLogin()" id="resend-login-btn" class="text-sm font-bold text-primary hover:underline ml-9">Resend verification email</button>
                <p id="resend-login-msg" class="hidden text-green-600 text-sm font-medium ml-9">Verification email sent!</p>
            </div>

            <form id="login-form" class="space-y-5">

                <div class="space-y-1">
                    <label for="email" class="text-sm font-bold text-slate-800 ml-1">Email address</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">alternate_email</span>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Enter your email"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-sm font-bold text-slate-800 ml-1">Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                               class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="••••••••"/>
                        <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded"/>
                        <span class="text-sm font-medium text-slate-700">Stay signed in</span>
                    </label>
                    <a href="forgot-password" class="text-sm font-bold text-primary hover:text-orange-600 transition-colors">Forgot password?</a>
                </div>

                <button type="submit" id="login-btn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-primary hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98] hover:shadow-primary/30">
                    <span id="login-btn-text">Sign in to CSNExplore</span>
                    <span id="login-spinner" class="hidden material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                </button>
            </form>
        </div>

        <div class="mt-auto pt-8 text-center text-xs text-slate-400">
            <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            <div class="mt-2 flex justify-center gap-4">
                <a href="privacy" class="hover:text-primary transition-colors">Privacy Policy</a>
                <a href="terms" class="hover:text-primary transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</div>

<script>
    async function resendFromLogin() {
        const email = window._loginEmail || document.getElementById('email').value.trim();
        if (!email) return;
        document.getElementById('resend-login-btn').disabled = true;
        try {
            await fetch('<?php echo BASE_PATH; ?>/php/api/auth.php?action=resend_verification', {
                method: 'POST', headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ email })
            });
            document.getElementById('resend-login-msg').classList.remove('hidden');
            setTimeout(() => document.getElementById('resend-login-msg').classList.add('hidden'), 4000);
        } catch(e) {}
        setTimeout(() => document.getElementById('resend-login-btn').disabled = false, 30000);
    }

    // Password toggle
    document.getElementById('toggle-password').addEventListener('click', function() {
        const pwd = document.getElementById('password');
        const icon = this.querySelector('.material-symbols-outlined');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            pwd.type = 'password';
            icon.textContent = 'visibility';
        }
    });

    // Login via fetch
    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const errBox  = document.getElementById('login-error');
        const errText = document.getElementById('login-error-text');
        const btn     = document.getElementById('login-btn');
        const btnText = document.getElementById('login-btn-text');
        const spinner = document.getElementById('login-spinner');

        errBox.classList.add('hidden');
        btn.disabled = true;
        btnText.textContent = 'Signing in…';
        spinner.classList.remove('hidden');

        const email    = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const turnstileResponse = document.querySelector('[name="cf-turnstile-response"]')?.value || "";

        try {
            const res  = await fetch('<?php echo BASE_PATH; ?>/php/api/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, turnstileResponse })
            });
            const data = await res.json();

            if (!res.ok) {
                if (data.error === 'unverified') {
                    document.getElementById('unverified-email').textContent = data.email || email;
                    document.getElementById('unverified-notice').classList.remove('hidden');
                    window._loginEmail = data.email || email;
                } else {
                    errText.textContent = data.error || 'Login failed. Please try again.';
                    errBox.classList.remove('hidden');
                }
                return;
            }

            localStorage.setItem('csn_token', data.token);
            localStorage.setItem('csn_user',  JSON.stringify(data.user));

            if (data.user.role === 'admin') {
                localStorage.setItem('csn_admin_token', data.token);
                localStorage.setItem('csn_admin_user',  JSON.stringify(data.user));
            }

            const redirect = new URLSearchParams(window.location.search).get('redirect') || '';
            if (redirect) {
                window.location.href = redirect;
            } else if (data.user.role === 'admin') {
                window.location.href = 'admin/dashboard.php';
            } else {
                window.location.href = 'index';
            }
        } catch (err) {
            errText.textContent = 'Network error. Please check your connection.';
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Sign in to CSNExplore';
            spinner.classList.add('hidden');
        }
    });
</script>
<?php require 'footer.php'; ?>
