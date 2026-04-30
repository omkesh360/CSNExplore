<?php
$page_title = "Admin Login | CSNExplore";
require_once 'php/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?php echo htmlspecialchars($page_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<script>tailwind.config={theme:{extend:{colors:{primary:'#ec5b13'},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
<style>
body{font-family:'Inter',sans-serif;}
.material-symbols-outlined{font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24;}
@keyframes slow-zoom{0%{transform:scale(1)}100%{transform:scale(1.1)}}
.animate-slow-zoom{animation:slow-zoom 20s linear infinite alternate;}
@keyframes spin{to{transform:rotate(360deg)}}
</style>
<!-- Google Analytics - G-58P4JE1SYS -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-58P4JE1SYS"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-58P4JE1SYS');
</script>
</head>
<body class="bg-white min-h-screen">
<script>
(function(){
    var token=localStorage.getItem('csn_admin_token');
    var user=JSON.parse(localStorage.getItem('csn_admin_user')||'null');
    if(token&&user&&user.role==='admin'){
        try{var p=JSON.parse(atob(token.split('.')[1].replace(/-/g,'+').replace(/_/g,'/')));if(!p.exp||p.exp>Math.floor(Date.now()/1000)){window.location.replace('admin/dashboard.php');}}catch(e){}
    }
})();
</script>
<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-slate-900">
        <img src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1200&q=80" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40 animate-slow-zoom"/>
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-900/90 via-slate-900/60 to-orange-600/20"></div>
        <div class="relative z-10 w-full p-12 flex flex-col justify-between">
            <div class="flex items-center gap-3">
                <img src="images/travelhub.png" alt="CSNExplore" class="h-10 object-contain"/>
            </div>
            <div class="max-w-md">
                <h1 class="text-4xl font-black text-white leading-tight mb-4 mt-6">Admin Control Center</h1>
                <p class="text-lg text-white/60">Manage listings, bookings, users and content from one place.</p>
            </div>
            <div class="flex items-center gap-6 text-white/40 text-sm">
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-base">shield</span> Secure Access</span>
                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-base">lock</span> Encrypted</span>
            </div>
        </div>
    </div>
    <div class="w-full lg:w-1/2 flex flex-col items-center px-6 md:px-12 pt-8 pb-12 lg:py-12 bg-slate-50 min-h-screen overflow-y-auto">
        <div class="lg:hidden flex items-center gap-2 mb-8 self-start">
            <img src="images/travelhub.png" alt="CSNExplore" class="h-8 object-contain"/>
        </div>
        <div class="w-full max-w-md lg:my-auto">
            <h2 class="text-3xl font-extrabold text-slate-900 mb-1">Admin Sign In</h2>
            <p class="text-slate-500 text-sm mb-7">Access restricted to authorized administrators only.</p>
            <div id="login-error" class="hidden bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-5">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-red-500 text-lg shrink-0">error</span>
                    <p class="text-sm text-red-700 font-medium" id="login-error-text"></p>
                </div>
            </div>
            <form id="admin-login-form" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-bold text-slate-800 mb-1.5">Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400">person</span>
                        </span>
                        <input id="username" name="username" type="text" autocomplete="off" required
                               class="block w-full pl-12 pr-3 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-primary text-sm shadow-sm transition-all"
                               placeholder=""/>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-800 mb-1.5">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="material-symbols-outlined text-[20px] text-slate-400">lock</span>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="off" required
                               class="block w-full pl-12 pr-10 py-3 border border-slate-200 rounded-xl bg-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-primary text-sm shadow-sm transition-all"
                               placeholder=""/>
                        <button type="button" id="toggle-pw" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>
                <button type="submit" id="login-btn"
                        class="w-full flex justify-center items-center gap-2 py-3.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-orange-600 transition-all shadow-lg active:scale-[0.98]">
                    <span id="btn-text">Sign in to Admin Panel</span>
                    <span id="btn-spin" class="hidden material-symbols-outlined text-[18px]" style="animation:spin 1s linear infinite">progress_activity</span>
                </button>
            </form>
            <div class="mt-6 text-center">
                <a href="/" class="text-sm text-slate-400 hover:text-primary flex items-center justify-center gap-1 transition-colors">
                    <span class="material-symbols-outlined text-base">arrow_back</span> Back to Website
                </a>
            </div>
        </div>
        <p class="mt-auto pt-8 text-xs text-slate-400">&copy; <?php echo date('Y'); ?> CSNExplore. Admin access only.</p>
    </div>
</div>
<script>
document.getElementById('toggle-pw').addEventListener('click',function(){
    var p=document.getElementById('password'),i=this.querySelector('.material-symbols-outlined');
    p.type=p.type==='password'?'text':'password';
    i.textContent=p.type==='password'?'visibility':'visibility_off';
});
document.getElementById('admin-login-form').addEventListener('submit',async function(e){
    e.preventDefault();
    var errBox=document.getElementById('login-error'),errTxt=document.getElementById('login-error-text');
    var btn=document.getElementById('login-btn'),btnTxt=document.getElementById('btn-text'),spin=document.getElementById('btn-spin');
    errBox.classList.add('hidden');
    btn.disabled=true; btnTxt.textContent='Signing in…'; spin.classList.remove('hidden');
    var username=document.getElementById('username').value.trim();
    var password=document.getElementById('password').value;
    try{
        var res=await fetch('<?php echo BASE_PATH; ?>/php/api/auth.php?action=login',{
            method:'POST',headers:{'Content-Type':'application/json'},
            body:JSON.stringify({username:username,password:password})
        });
        var data=await res.json();
        if(!res.ok||!data.token){errTxt.textContent=data.error||'Invalid credentials.';errBox.classList.remove('hidden');return;}
        if(data.user.role!=='admin'){errTxt.textContent='Access denied. Admin privileges required.';errBox.classList.remove('hidden');return;}
        localStorage.setItem('csn_admin_token',data.token);
        localStorage.setItem('csn_admin_user',JSON.stringify(data.user));
        localStorage.setItem('csn_token',data.token);
        localStorage.setItem('csn_user',JSON.stringify(data.user));
        window.location.href='<?php echo BASE_PATH; ?>/admin/dashboard.php';
    }catch(err){
        errTxt.textContent='Network error. Please check your connection.';
        errBox.classList.remove('hidden');
    }finally{
        btn.disabled=false; btnTxt.textContent='Sign in to Admin Panel'; spin.classList.add('hidden');
    }
});
</script>
</body>
</html>
