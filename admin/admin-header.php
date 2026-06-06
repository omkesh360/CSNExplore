<?php
// admin/admin-header.php – shared admin layout
// Set $admin_page before including (e.g. 'dashboard', 'listings', 'bookings', 'blogs', 'users', 'content')
require_once __DIR__ . '/../php/config.php';
require_once __DIR__ . '/../php/jwt.php';

$admin_token_cookie = $_COOKIE['admin_token'] ?? null;
if (!$admin_token_cookie) {
    header('Location: ../adminexplorer.php');
    exit;
}

$payload = verifyJWT($admin_token_cookie, JWT_SECRET);
if (!$payload || !isset($payload['role']) || $payload['role'] !== 'admin') {
    header('Location: ../adminexplorer.php');
    exit;
}

$admin_page  = $admin_page  ?? '';
$admin_title = $admin_title ?? 'Admin | CSNExplore';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<link rel="apple-touch-icon" sizes="57x57"   href="../images/fevicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60"   href="../images/fevicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72"   href="../images/fevicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76"   href="../images/fevicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../images/fevicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../images/fevicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../images/fevicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../images/fevicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../images/fevicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192" href="../images/fevicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32"   href="../images/fevicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96"   href="../images/fevicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16"   href="../images/fevicon/favicon-16x16.png">
<link rel="shortcut icon" href="../images/fevicon/favicon.ico" type="image/x-icon">
<meta name="msapplication-TileColor" content="#000000">
<meta name="msapplication-TileImage" content="../images/fevicon/ms-icon-144x144.png">
<meta name="theme-color" content="#000000">
<title><?php echo htmlspecialchars($admin_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin/admin-mobile.css"/>
<script>
tailwind.config = {
    theme: { extend: {
        colors: {
            primary: '#ec5b13',
            'primary-dark': '#c94d0e',
            'admin-bg': '#f8fafc',
            'sidebar-bg': '#0f172a',
            'header-bg': '#1e293b'
        },
        fontFamily: { sans: ['Inter','sans-serif'] }
    }}
}
</script>
<style>
/* ─── Reset & base ─────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background-color: #f8fafc;
    color: #1e293b;
    margin: 0;
    padding: 0;
}
.material-symbols-outlined {
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}

/* ─── Admin Layout Shell ───────────────────────── */
#admin-shell {
    display: flex;
    height: 100vh;
    overflow: hidden;
    background: #f8fafc;
    position: relative;
}

/* ─── Sidebar ──────────────────────────────────── */
#sidebar {
    width: 272px;
    min-width: 272px;
    background: #0f172a;
    display: flex;
    flex-direction: column;
    height: 100%;
    flex-shrink: 0;
    z-index: 50;
    transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
    /* NO will-change / translateZ overrides here */
}

/* ─── Mobile: sidebar slides off-screen left ───── */
@media (max-width: 767px) {
    #sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        transform: translateX(-100%);
    }
    #sidebar.is-open {
        transform: translateX(0);
        box-shadow: 4px 0 32px rgba(0,0,0,0.5);
    }
}

/* ─── Overlay ──────────────────────────────────── */
#sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.52);
    z-index: 40;
    backdrop-filter: blur(2px);
    -webkit-backdrop-filter: blur(2px);
}
#sidebar-overlay.is-open { display: block; }

/* ─── Main area ────────────────────────────────── */
#admin-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-width: 0;
    width: 100%;
}

/* ─── Top bar ──────────────────────────────────── */
#admin-topbar {
    height: 56px;
    background: #1e293b;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 16px;
    flex-shrink: 0;
    z-index: 20;
}
@media (min-width: 768px) {
    #admin-topbar { height: 64px; padding: 0 24px; }
}

/* ─── Hamburger button ─────────────────────────── */
#sidebar-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 10px;
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    flex-shrink: 0;
    /* Show on mobile only */
}
#sidebar-toggle:hover { background: rgba(255,255,255,0.18); color: #fff; }
@media (min-width: 768px) {
    #sidebar-toggle { display: none; }
}

/* ─── Sidebar nav links ────────────────────────── */
.sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    transition: background 0.12s, color 0.12s;
    position: relative;
}
.sidebar-link:hover {
    background: rgba(255,255,255,0.09);
    color: #fff;
}
.sidebar-link.active {
    background: rgba(255,255,255,0.13);
    color: #fff;
    font-weight: 600;
    border-left: 3px solid #60a5fa;
    padding-left: 9px;
}

/* ─── Scrollbar ────────────────────────────────── */
.custom-scrollbar::-webkit-scrollbar        { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track  { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb  { background: rgba(255,255,255,0.18); border-radius: 10px; }

/* ─── Admin card ───────────────────────────────── */
.admin-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
}

/* ─── Slide-in animation ───────────────────────── */
@keyframes adminSlideIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.animate-slide-in { animation: adminSlideIn 0.18s ease-out; }

/* ─── Topbar right badges ──────────────────────── */
.topbar-badge {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 700;
}
</style>
<?php if (!empty($extra_head)) echo $extra_head; ?>

<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-58P4JE1SYS"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'G-58P4JE1SYS');
</script>
</head>
<body>

<!-- Auth guard -->
<script>
(function(){
    var token = localStorage.getItem('csn_admin_token');
    var user  = JSON.parse(localStorage.getItem('csn_admin_user') || 'null');
    if (!token || !user || user.role !== 'admin') {
        window.location.href = '../adminexplorer.php';
    }
    window._adminToken = token;
    window._adminUser  = user;
})();
</script>

<div id="admin-shell">

<!-- ── Mobile overlay ──────────────────────────────────────────────── -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- ── Sidebar ─────────────────────────────────────────────────────── -->
<aside id="sidebar">

    <!-- Logo row -->
    <div style="height:64px;display:flex;align-items:center;gap:12px;padding:0 16px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0;">
        <img width="180" height="40" src="../images/Logo-light-optimized.webp" alt="CSNExplore" style="height:32px;object-fit:contain;flex-shrink:0;"/>
        <!-- Close button — mobile only -->
        <button onclick="closeSidebar()" id="sidebar-close-btn"
                style="margin-left:auto;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:8px;color:rgba(255,255,255,0.7);width:34px;height:34px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .15s;">
            <span class="material-symbols-outlined" style="font-size:18px;">close</span>
        </button>
    </div>

    <!-- Nav links -->
    <nav class="custom-scrollbar" style="flex:1;overflow-y:auto;padding:16px 12px;display:flex;flex-direction:column;gap:2px;">
        <?php
        $nav = [
            // ── Core ──────────────────────────────────────────────────────
            ['divider'=>'Core'],
            ['href'=>'dashboard.php',      'icon'=>'grid_view',        'label'=>'Dashboard',        'key'=>'dashboard'],
            ['href'=>'listings.php',       'icon'=>'database',          'label'=>'Listings',         'key'=>'listings'],
            ['href'=>'bookings.php',       'icon'=>'calendar_today',    'label'=>'Bookings',         'key'=>'bookings',  'badge'=>true],
            ['href'=>'trip-requests.php',  'icon'=>'flight_takeoff',    'label'=>'Trip Planner',     'key'=>'trip-requests'],
            // ── Content ───────────────────────────────────────────────────
            ['divider'=>'Content'],
            ['href'=>'blogs.php',          'icon'=>'article',           'label'=>'Blogs',            'key'=>'blogs'],
            ['href'=>'gallery.php',        'icon'=>'photo_library',     'label'=>'Gallery',          'key'=>'gallery'],
            ['href'=>'content.php',        'icon'=>'edit_note',         'label'=>'Content',          'key'=>'content'],
            // ── SEO & Pages ───────────────────────────────────────────────
            ['divider'=>'SEO & Pages'],
            ['href'=>'seo-manager.php',    'icon'=>'search',            'label'=>'SEO Manager',      'key'=>'seo'],
            ['href'=>'map-embeds.php',     'icon'=>'map',               'label'=>'Map Embeds',       'key'=>'map-embeds'],
            ['href'=>'regenerate.php',     'icon'=>'autorenew',         'label'=>'Regenerate Pages', 'key'=>'regenerate'],
            // ── People ────────────────────────────────────────────────────
            ['divider'=>'People'],
            ['href'=>'users.php',          'icon'=>'group',             'label'=>'Users',            'key'=>'users'],
            ['href'=>'subscribers.php',    'icon'=>'mark_email_read',   'label'=>'Subscribers',      'key'=>'subscribers'],
            // ── System ────────────────────────────────────────────────────
            ['divider'=>'System'],
            ['href'=>'activity-logs.php',  'icon'=>'history',           'label'=>'Activity Logs',    'key'=>'activity-logs'],
            ['href'=>'caching.php',        'icon'=>'memory',            'label'=>'Caching',          'key'=>'caching'],
        ];
        foreach ($nav as $n):
            // Section divider — render label and skip to next item
            if (!empty($n['divider'])): ?>
        <div style="margin:10px 4px 6px;font-size:9px;font-weight:700;color:rgba(255,255,255,0.3);letter-spacing:.1em;text-transform:uppercase;padding-left:4px;"><?php echo $n['divider']; ?></div>
        <?php continue; endif;
            $active = ($admin_page === ($n['key'] ?? '')) ? 'active' : '';
        ?>
        <a href="<?php echo $n['href']; ?>"
           class="sidebar-link <?php echo $active; ?>"
           onclick="if(window.innerWidth<768){closeSidebar();}">
            <span class="material-symbols-outlined" style="font-size:20px;flex-shrink:0;"><?php echo $n['icon']; ?></span>
            <span style="flex:1;"><?php echo $n['label']; ?></span>
            <?php if (!empty($n['badge'])): ?>
            <span id="sidebar-pending-badge" style="display:none;background:#ec5b13;color:#fff;font-size:9px;font-weight:700;padding:2px 6px;border-radius:999px;"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- User profile card -->
    <div style="margin:0 12px 16px;padding:12px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);border-radius:14px;flex-shrink:0;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
            <div style="position:relative;">
                <div style="width:38px;height:38px;background:rgba(255,255,255,0.18);border:2px solid rgba(255,255,255,0.25);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <span class="material-symbols-outlined" style="color:#fff;font-size:20px;">account_circle</span>
                </div>
                <div style="position:absolute;bottom:-2px;right:-2px;width:10px;height:10px;background:#4ade80;border:2px solid #0f172a;border-radius:50%;"></div>
            </div>
            <div style="min-width:0;flex:1;">
                <p id="admin-name"  style="font-size:12px;font-weight:700;color:#fff;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Admin</p>
                <p id="admin-email" style="font-size:10px;color:rgba(255,255,255,0.55);margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">admin@csnexplore.com</p>
            </div>
        </div>
        <button onclick="adminLogout()"
                style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:8px 12px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.18);color:#fff;border-radius:10px;cursor:pointer;font-weight:700;font-size:12px;transition:background .15s;font-family:inherit;">
            <span class="material-symbols-outlined" style="font-size:16px;">logout</span> Sign Out
        </button>
    </div>
</aside>

<!-- ── Main column ──────────────────────────────────────────────────── -->
<div id="admin-main">

    <!-- Top bar -->
    <header id="admin-topbar">
        <div style="display:flex;align-items:center;gap:12px;min-width:0;">
            <!-- Hamburger — only on mobile -->
            <button id="sidebar-toggle" onclick="openSidebar()" title="Open menu">
                <span class="material-symbols-outlined" style="font-size:22px;">menu</span>
            </button>
            <h1 style="font-size:15px;font-weight:700;color:#fff;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                <?php echo htmlspecialchars($admin_title); ?>
            </h1>
        </div>

        <div style="display:flex;align-items:center;gap:10px;">
            <!-- Online badge -->
            <div class="topbar-badge" style="background:rgba(74,222,128,0.15);color:#86efac;border:1px solid rgba(74,222,128,0.3);display:none;" id="online-badge">
                <div style="width:6px;height:6px;background:#4ade80;border-radius:50%;animation:pulse 2s infinite;"></div>
                <span>Online</span>
            </div>

            <div style="width:1px;height:20px;background:rgba(255,255,255,0.15);display:none;" id="topbar-sep"></div>

            <a href="../index.php" target="_blank"
               style="display:flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:rgba(255,255,255,0.65);text-decoration:none;transition:color .15s;"
               onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.65)'">
                <span class="material-symbols-outlined" style="font-size:16px;">open_in_new</span>
                <span id="view-site-label">View Site</span>
            </a>

            <div id="pending-badge"
                 style="display:none;align-items:center;gap:5px;padding:4px 10px;border-radius:999px;background:rgba(249,115,22,0.15);color:#fdba74;border:1px solid rgba(249,115,22,0.3);font-size:9px;font-weight:700;">
                <span class="material-symbols-outlined" style="font-size:14px;">notifications</span>
                <span id="pending-count">0</span>
            </div>
        </div>
    </header>

<script>
/* ── Sidebar toggle (mobile) ───────────────────── */
function openSidebar() {
    document.getElementById('sidebar').classList.add('is-open');
    document.getElementById('sidebar-overlay').classList.add('is-open');
    document.body.style.overflow = 'hidden'; // prevent background scroll
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('is-open');
    document.getElementById('sidebar-overlay').classList.remove('is-open');
    document.body.style.overflow = '';
}

/* Hide close button on desktop */
(function() {
    var mq = window.matchMedia('(min-width: 768px)');
    function applyMQ(e) {
        var btn = document.getElementById('sidebar-close-btn');
        var ol  = document.getElementById('online-badge');
        var sep = document.getElementById('topbar-sep');
        var lbl = document.getElementById('view-site-label');
        if (e.matches) {
            // Desktop: always show sidebar, hide mobile-specific UI
            if (btn) btn.style.display = 'none';
            document.getElementById('sidebar').classList.remove('is-open');
            document.getElementById('sidebar-overlay').classList.remove('is-open');
            document.body.style.overflow = '';
            if (ol)  ol.style.display  = 'flex';
            if (sep) sep.style.display = 'block';
            if (lbl) lbl.style.display = 'inline';
        } else {
            // Mobile: show close button
            if (btn) btn.style.display = 'flex';
            if (ol)  ol.style.display  = 'none';
            if (sep) sep.style.display = 'none';
            if (lbl) lbl.style.display = 'none';
        }
    }
    mq.addEventListener('change', applyMQ);
    applyMQ(mq);
})();

/* ── API helper ────────────────────────────────── */
async function api(url, options = {}) {
    try {
        options.headers = options.headers || {};
        options.headers['Content-Type'] = 'application/json';
        if (window._adminToken) {
            options.headers['Authorization'] = 'Bearer ' + window._adminToken;
        }
        var res = await fetch(url, options);
        if (res.status === 401 || res.status === 403) { adminLogout(); return null; }
        var ct = res.headers.get('content-type');
        if (ct && ct.includes('application/json')) return await res.json();
        console.error('[API] Non-JSON response');
        return null;
    } catch (err) {
        console.error('[API]', err.message);
        return null;
    }
}

/* ── Toast ─────────────────────────────────────── */
function showAdminToast(msg, type) {
    var t  = document.createElement('div');
    var bg = type === 'error' ? '#dc2626' : '#0f172a';
    t.style.cssText = 'position:fixed;bottom:20px;right:20px;background:' + bg + ';color:#fff;font-size:13px;padding:10px 18px;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:9999;font-family:Inter,sans-serif;max-width:320px;word-break:break-word;';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2800);
}
</script>

    <!-- Page content starts -->
    <main style="flex:1;overflow-y:auto;padding:16px;" id="admin-content">
<style>
@media (min-width: 768px) {
    #admin-content { padding: 24px !important; }
}
</style>
