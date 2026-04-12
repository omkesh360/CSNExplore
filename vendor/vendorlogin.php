<?php
require_once __DIR__ . '/../php/config.php';

if (!defined('VENDOR_API_BASE')) {
    $_vs = $_SERVER['SCRIPT_NAME'] ?? '';
    $_sb = dirname(dirname($_vs));
    if ($_sb === '/' || $_sb === '\\') $_sb = '';
    define('VENDOR_API_BASE', rtrim($_sb, '/'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title>Vendor Portal — CSNExplore</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;min-height:100vh;overflow:hidden}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;line-height:1}

/* Split layout */
.split{display:flex;min-height:100vh}
.split-left{
    flex:0 0 55%;
    background:linear-gradient(135deg,#1a0a00 0%,#2d1000 30%,#ec5b13 70%,#ff8c42 100%);
    position:relative;overflow:hidden;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 48px;
}
.split-right{
    flex:1;background:#fafafa;display:flex;align-items:center;justify-content:center;padding:40px 48px;position:relative;
}

/* Animated blobs */
.blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:.18;pointer-events:none}
.blob1{width:400px;height:400px;background:#ff6b1a;top:-100px;right:-80px;animation:drift 8s ease-in-out infinite alternate}
.blob2{width:300px;height:300px;background:#ffb347;bottom:-60px;left:-60px;animation:drift 11s ease-in-out infinite alternate-reverse}
.blob3{width:200px;height:200px;background:#fff;top:40%;left:30%;animation:drift 14s ease-in-out infinite alternate}
@keyframes drift{from{transform:translate(0,0) scale(1)}to{transform:translate(30px,20px) scale(1.08)}}

/* Left content */
.brand-tag{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:40px;padding:6px 14px 6px 8px;margin-bottom:32px;backdrop-filter:blur(8px)}
.brand-tag span{font-size:.7rem;font-weight:700;color:rgba(255,255,255,.9);letter-spacing:.05em;text-transform:uppercase}
.brand-icon{width:28px;height:28px;background:#ec5b13;border-radius:50%;display:flex;align-items:center;justify-content:center}

.left-heading{font-size:2.6rem;font-weight:900;color:#fff;line-height:1.1;margin-bottom:16px;letter-spacing:-.03em}
.left-heading em{font-style:normal;color:#ffb347}
.left-sub{font-size:.9rem;color:rgba(255,255,255,.7);line-height:1.6;max-width:380px;margin-bottom:40px}

/* Feature pills */
.feature-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;width:100%;max-width:420px}
.feature-pill{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:14px;padding:12px 14px;backdrop-filter:blur(8px)}
.feature-pill .material-symbols-outlined{font-size:18px;color:#ffb347}
.feature-pill p{font-size:.72rem;font-weight:600;color:rgba(255,255,255,.9)}
.feature-pill small{display:block;font-size:.65rem;color:rgba(255,255,255,.5);margin-top:1px}

/* Right side - login form */
.form-card{width:100%;max-width:400px}
.form-logo{width:44px;height:44px;background:linear-gradient(135deg,#ec5b13,#ff8c42);border-radius:14px;display:flex;align-items:center;justify-content:center;margin-bottom:24px;box-shadow:0 8px 20px rgba(236,91,19,.35)}
.form-logo .material-symbols-outlined{color:#fff;font-size:24px}
.form-card h1{font-size:1.5rem;font-weight:800;color:#0f172a;letter-spacing:-.03em;margin-bottom:4px}
.form-card .sub-tag{font-size:.8rem;color:#64748b;margin-bottom:32px}

/* Inputs */
.field{margin-bottom:18px}
.field label{display:block;font-size:.75rem;font-weight:700;color:#374151;margin-bottom:6px;letter-spacing:.01em}
.input-wrap{position:relative}
.input-wrap .material-symbols-outlined{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:18px;color:#94a3b8;pointer-events:none}
.input-wrap input{
    width:100%;padding:11px 12px 11px 40px;
    border:1.5px solid #e2e8f0;border-radius:12px;
    font-size:.85rem;font-family:'Inter',sans-serif;color:#0f172a;background:#fff;
    transition:border .15s,box-shadow .15s;outline:none
}
.input-wrap input:focus{border-color:#ec5b13;box-shadow:0 0 0 3px rgba(236,91,19,.12)}
.input-wrap .pw-toggle{position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;display:flex;padding:2px}
.input-wrap .pw-toggle:hover{color:#64748b}

.error-box{display:none;align-items:center;gap:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:.78rem;color:#dc2626;font-weight:500}
.error-box .material-symbols-outlined{font-size:16px}

.btn-primary{
    width:100%;padding:13px;background:linear-gradient(135deg,#ec5b13,#ff7a35);
    border:none;border-radius:12px;color:#fff;font-size:.88rem;font-weight:700;
    cursor:pointer;font-family:'Inter',sans-serif;
    display:flex;align-items:center;justify-content:center;gap:8px;
    transition:transform .15s,box-shadow .15s;
    box-shadow:0 4px 14px rgba(236,91,19,.35);margin-bottom:24px
}
.btn-primary:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 6px 20px rgba(236,91,19,.45)}
.btn-primary:disabled{opacity:.7;cursor:not-allowed}

.divider{border:none;border-top:1px solid #f1f5f9;margin-bottom:20px}
.hint-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;text-align:center}
.hint-box p{font-size:.75rem;color:#64748b;line-height:1.5}
.hint-box strong{color:#0f172a}

.back-link{display:inline-flex;align-items:center;gap:6px;margin-top:20px;font-size:.75rem;color:#94a3b8;text-decoration:none;transition:color .15s}
.back-link:hover{color:#ec5b13}
.back-link .material-symbols-outlined{font-size:16px}

/* Responsive */
@media(max-width:768px){
    .split{flex-direction:column}
    .split-left{flex:0 0 auto;padding:40px 24px;min-height:auto}
    .left-heading{font-size:1.8rem}
    .feature-grid{grid-template-columns:1fr}
    .split-right{padding:32px 24px}
    .form-card{max-width:100%}
}

/* Spinner */
@keyframes spin{to{transform:rotate(360deg)}}
.spin{animation:spin .7s linear infinite}
</style>
</head>
<body>

<!-- Redirect if already logged in -->
<script>
(function(){
    try {
        var t = localStorage.getItem('csn_vendor_token');
        var v = JSON.parse(localStorage.getItem('csn_vendor_user') || 'null');
        if (t && v) {
            // verify token not expired
            var p = JSON.parse(atob(t.split('.')[1].replace(/-/g,'+').replace(/_/g,'/')));
            if (!p.exp || p.exp > Math.floor(Date.now()/1000)) {
                window.location.href = '<?php echo VENDOR_API_BASE; ?>/vendor/dashboard.php';
            }
        }
    } catch(e) {}
})();
</script>

<div class="split">

    <!-- ════ LEFT ════ -->
    <div class="split-left">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="blob blob3"></div>

        <div style="position:relative;z-index:1;width:100%;max-width:440px">
            <div class="brand-tag">
                <div class="brand-icon"><span class="material-symbols-outlined" style="font-size:14px;color:#fff">storefront</span></div>
                <span>CSNExplore Vendor Portal</span>
            </div>

            <h1 class="left-heading">Manage your<br/>listings with <em>ease</em></h1>
            <p class="left-sub">Your all-in-one dashboard to manage hotels, rooms, cars and track bookings across Chhatrapati Sambhajinagar.</p>

            <div class="feature-grid">
                <div class="feature-pill">
                    <span class="material-symbols-outlined">apartment</span>
                    <div><p>Hotel Listings</p><small>Add & manage stays</small></div>
                </div>
                <div class="feature-pill">
                    <span class="material-symbols-outlined">bed</span>
                    <div><p>Room Types</p><small>Manage inventory</small></div>
                </div>
                <div class="feature-pill">
                    <span class="material-symbols-outlined">directions_car</span>
                    <div><p>Car Rentals</p><small>Fleet management</small></div>
                </div>
                <div class="feature-pill">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <div><p>Bookings</p><small>Track reservations</small></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ════ RIGHT ════ -->
    <div class="split-right">
        <div class="form-card">
            <div class="form-logo"><span class="material-symbols-outlined">storefront</span></div>
            <h1>Welcome back</h1>
            <p class="sub-tag">Sign in to your vendor account</p>

            <div class="error-box" id="err">
                <span class="material-symbols-outlined">error</span>
                <span id="err-text">Invalid credentials</span>
            </div>

            <form id="login-form" autocomplete="on">
                <div class="field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="material-symbols-outlined">person</span>
                        <input id="username" type="text" placeholder="Enter your username" required autocomplete="username"/>
                    </div>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="material-symbols-outlined">lock</span>
                        <input id="password" type="password" placeholder="••••••••" required autocomplete="current-password"/>
                        <button type="button" class="pw-toggle" id="pw-toggle" title="Show/hide password">
                            <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-primary" id="submit-btn">
                    <span id="btn-text">Sign In</span>
                    <span class="material-symbols-outlined spin" id="btn-spinner" style="display:none;font-size:18px">progress_activity</span>
                </button>
            </form>

            <hr class="divider"/>
            <div class="hint-box">
                <p>Don't have an account?<br/><strong>Contact your CSNExplore admin</strong><br/>to set up your vendor account.</p>
            </div>

            <div style="text-align:center">
                <a href="<?php echo VENDOR_API_BASE; ?>/index.php" class="back-link">
                    <span class="material-symbols-outlined">arrow_back</span>
                    Back to Website
                </a>
            </div>
        </div>
    </div>

</div>

<script>
const VENDOR_API = '<?php echo VENDOR_API_BASE; ?>';

// Password toggle
document.getElementById('pw-toggle').addEventListener('click', function() {
    var i = document.getElementById('password');
    var ic = this.querySelector('.material-symbols-outlined');
    i.type = i.type === 'password' ? 'text' : 'password';
    ic.textContent = i.type === 'password' ? 'visibility' : 'visibility_off';
});

// Login
document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    var btn     = document.getElementById('submit-btn');
    var txt     = document.getElementById('btn-text');
    var spin    = document.getElementById('btn-spinner');
    var errBox  = document.getElementById('err');
    var errTxt  = document.getElementById('err-text');

    btn.disabled = true;
    txt.textContent = 'Signing in…';
    spin.style.display = 'inline-block';
    errBox.style.display = 'none';

    try {
        var res  = await fetch(VENDOR_API + '/php/api/vendor-auth-simple.php?action=login', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value
            })
        });
        var data = await res.json();

        if (!res.ok || !data.token) throw new Error(data.error || 'Invalid credentials');

        localStorage.setItem('csn_vendor_token', data.token);
        localStorage.setItem('csn_vendor_user',  JSON.stringify(data.vendor));
        txt.textContent = 'Redirecting…';
        window.location.href = VENDOR_API + '/vendor/dashboard.php';

    } catch(ex) {
        errTxt.textContent = ex.message;
        errBox.style.display = 'flex';
        btn.disabled = false;
        txt.textContent = 'Sign In';
        spin.style.display = 'none';
    }
});
</script>
</body>
</html>
