<?php
$page_title = "Create Account | CSNExplore";
$current_page = "register.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Create a free account on CSNExplore to unlock exclusive travel deals, save itineraries, and book hotels and cars in Chhatrapati Sambhajinagar.",
    'canonical'   => BASE_PATH . '/register',
    'type'        => 'website',
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "name": "Create Account | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/register"
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
                var p = JSON.parse(atob(parts[1].replace(/-/g,'+').replace(/_/g,'/')));
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

    <!-- Left: Register Form -->
    <div class="w-full lg:w-1/2 flex flex-col items-center px-6 md:px-12 pt-6 pb-12 lg:py-12 bg-slate-50 relative overflow-y-auto min-h-screen lg:min-h-0">

        <div class="w-full max-w-md space-y-8 lg:my-auto mt-4">
            <div class="text-center lg:text-left">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight md:text-4xl">Start your journey</h2>
                <p class="mt-3 text-slate-500 font-medium">
                    Already have an account?
                    <a href="login" class="text-primary hover:text-orange-600 font-bold transition-all border-b-2 border-primary/20 hover:border-primary">Sign in here</a>
                </p>
            </div>

            <!-- Error Container -->
            <div id="reg-error" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 shrink-0">error</span>
                    <p class="text-sm text-red-700 font-medium" id="reg-error-text">Something went wrong.</p>
                </div>
            </div>

            <!-- Success: Check Email Screen -->
            <div id="reg-success" class="hidden text-center py-6 space-y-4">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                    <span class="material-symbols-outlined text-green-600 text-4xl">mark_email_read</span>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Check your inbox!</h3>
                <p class="text-slate-500 text-sm">We've sent a verification link to <strong id="reg-email-sent"></strong>. Click the link to activate your account.</p>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800 text-left">
                    <span class="material-symbols-outlined text-blue-600 text-base align-middle mr-1">info</span>
                    Please click the verification link we just sent you to complete your account creation. Be sure to check your spam folder!
                </div>
                <button id="resend-btn" onclick="resendVerification()" class="text-primary font-bold text-sm hover:underline">Didn't receive it? Resend email</button>
                <p id="resend-msg" class="hidden text-green-600 text-sm font-medium">Verification email resent!</p>
            </div>

            <form id="registration-form" class="space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <label for="first-name" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">First Name</label>
                        <input id="first-name" name="first_name" type="text" required
                               class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="John"/>
                    </div>
                    <div class="space-y-1">
                        <label for="last-name" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Last Name</label>
                        <input id="last-name" name="last_name" type="text" required
                               class="block w-full px-4 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Doe"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="email" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Email Address</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">alternate_email</span>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                               class="block w-full pl-12 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="john@example.com"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="phone" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Phone Number</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">phone</span>
                        </span>
                        <input id="phone" name="phone" type="tel" autocomplete="tel"
                               class="block w-full pl-12 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="+91 XXXXX XXXXX"/>
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-xs font-bold text-slate-700 ml-1 uppercase tracking-wider">Create Password</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400 group-focus-within:text-primary transition-colors">lock</span>
                        </span>
                        <input id="password" name="password" type="password" required minlength="8"
                               class="block w-full pl-12 pr-10 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition-all shadow-sm"
                               placeholder="Min. 8 characters"/>
                        <button type="button" id="toggle-password"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 transition-colors">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-start mt-2">
                    <input id="terms" name="terms" type="checkbox" required
                           class="h-4 w-4 mt-0.5 text-primary focus:ring-primary border-slate-300 rounded"/>
                    <label for="terms" class="ml-3 text-sm text-slate-500">
                        I agree to the <a href="terms" class="text-primary font-bold hover:underline">Terms of Service</a> and <a href="privacy" class="text-primary font-bold hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit" id="reg-btn"
                            class="w-full flex justify-center items-center gap-2 py-3.5 px-4 rounded-xl shadow-lg text-sm font-bold text-white bg-primary hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all active:scale-[0.98] hover:shadow-primary/30">
                        <span id="reg-btn-text">Create Account</span>
                        <span id="reg-spinner" class="hidden material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                    </button>
                </div>
            </form>

            <div class="text-center text-xs text-slate-400 pt-4">
                <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Right: Image & Branding -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-primary">
        <img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80" alt="Travel Background"
             class="absolute inset-0 w-full h-full object-cover mix-blend-overlay opacity-80 animate-slow-zoom"/>
        <div class="absolute inset-0 bg-gradient-to-tl from-primary/70 to-transparent"></div>
        <div class="relative z-10 w-full p-12 flex flex-col justify-between items-end text-right">
            <div></div>
            <div class="max-w-md">
                <h1 class="text-5xl font-serif font-black text-white leading-tight mb-6 tracking-tight">Expand your horizons.</h1>
                <p class="text-xl text-white/80 font-medium">Unlock exclusive deals, personalized itineraries, and 24/7 travel support when you join our community.</p>
            </div>
            <div class="flex items-center gap-6 text-white/60 text-sm font-medium">
                <span class="flex items-center gap-2">Join 10,000+ Travelers <span class="material-symbols-outlined text-[18px]">public</span></span>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('toggle-password').addEventListener('click', function() {
        const pwd  = document.getElementById('password');
        const icon = this.querySelector('.material-symbols-outlined');
        pwd.type   = pwd.type === 'password' ? 'text' : 'password';
        icon.textContent = pwd.type === 'password' ? 'visibility' : 'visibility_off';
    });

    document.getElementById('registration-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const errBox  = document.getElementById('reg-error');
        const errText = document.getElementById('reg-error-text');
        const btn     = document.getElementById('reg-btn');
        const btnText = document.getElementById('reg-btn-text');
        const spinner = document.getElementById('reg-spinner');

        errBox.classList.add('hidden');
        btn.disabled = true;
        btnText.textContent = 'Creating account…';
        spinner.classList.remove('hidden');

        const firstName = document.getElementById('first-name').value.trim();
        const lastName  = document.getElementById('last-name').value.trim();
        const email     = document.getElementById('email').value.trim();
        const phone     = document.getElementById('phone').value.trim();
        const password  = document.getElementById('password').value;
        const terms     = document.getElementById('terms').checked;

        if (!terms) {
            errText.textContent = 'You must agree to the Terms of Service and Privacy Policy.';
            errBox.classList.remove('hidden');
            btn.disabled = false; btnText.textContent = 'Create Account'; spinner.classList.add('hidden');
            return;
        }

        try {
            const res  = await fetch('<?php echo BASE_PATH; ?>/php/api/auth.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: `${firstName} ${lastName}`.trim(), email, phone, password })
            });
            const data = await res.json();

            if (data.pending) {
                // Show check-email screen
                document.getElementById('registration-form').classList.add('hidden');
                document.getElementById('reg-email-sent').textContent = email;
                document.getElementById('reg-success').classList.remove('hidden');
                window._regEmail = email;
                return;
            }

            if (data.error === 'unverified') {
                document.getElementById('registration-form').classList.add('hidden');
                document.getElementById('reg-email-sent').textContent = data.email || email;
                document.getElementById('reg-success').classList.remove('hidden');
                window._regEmail = data.email || email;
                return;
            }

            if (!res.ok) {
                errText.textContent = data.error || 'Registration failed. Please try again.';
                errBox.classList.remove('hidden');
                return;
            }
        } catch (err) {
            errText.textContent = 'Network error. Please check your connection.';
            errBox.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Create Account';
            spinner.classList.add('hidden');
        }
    });

    async function resendVerification() {
        const email = window._regEmail;
        if (!email) return;
        document.getElementById('resend-btn').disabled = true;
        try {
            await fetch('<?php echo BASE_PATH; ?>/php/api/auth.php?action=resend_verification', {
                method: 'POST', headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ email })
            });
            document.getElementById('resend-msg').classList.remove('hidden');
            setTimeout(() => document.getElementById('resend-msg').classList.add('hidden'), 4000);
        } catch(e) {}
        setTimeout(() => document.getElementById('resend-btn').disabled = false, 30000);
    }
</script>
<?php require 'footer.php'; ?>
