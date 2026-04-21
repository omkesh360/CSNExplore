<?php
// admin/admin-header.php – shared admin layout
// Set $admin_page before including (e.g. 'dashboard', 'listings', 'bookings', 'blogs', 'users', 'content')
$admin_page = $admin_page ?? '';
$admin_title = $admin_title ?? 'Admin | CSNExplore';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
<link rel="apple-touch-icon" sizes="57x57" href="../images/fevicon/apple-icon-57x57.png">
<link rel="apple-touch-icon" sizes="60x60" href="../images/fevicon/apple-icon-60x60.png">
<link rel="apple-touch-icon" sizes="72x72" href="../images/fevicon/apple-icon-72x72.png">
<link rel="apple-touch-icon" sizes="76x76" href="../images/fevicon/apple-icon-76x76.png">
<link rel="apple-touch-icon" sizes="114x114" href="../images/fevicon/apple-icon-114x114.png">
<link rel="apple-touch-icon" sizes="120x120" href="../images/fevicon/apple-icon-120x120.png">
<link rel="apple-touch-icon" sizes="144x144" href="../images/fevicon/apple-icon-144x144.png">
<link rel="apple-touch-icon" sizes="152x152" href="../images/fevicon/apple-icon-152x152.png">
<link rel="apple-touch-icon" sizes="180x180" href="../images/fevicon/apple-icon-180x180.png">
<link rel="icon" type="image/png" sizes="192x192"  href="../images/fevicon/android-icon-192x192.png">
<link rel="icon" type="image/png" sizes="32x32" href="../images/fevicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="96x96" href="../images/fevicon/favicon-96x96.png">
<link rel="icon" type="image/png" sizes="16x16" href="../images/fevicon/favicon-16x16.png">
<link rel="shortcut icon" href="../images/fevicon/favicon.ico" type="image/x-icon">
<meta name="msapplication-TileColor" content="#000000">
<meta name="msapplication-TileImage" content="../images/fevicon/ms-icon-144x144.png">
<meta name="theme-color" content="#000000">
<title><?php echo htmlspecialchars($admin_title); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
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
        fontFamily: { 
            sans: ['Inter','sans-serif']
        }
    }}
}
</script>
<style>
body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }
.material-symbols-outlined { font-variation-settings:'FILL' 0,'wght' 400,'GRAD' 0,'opsz' 24; }

/* Sidebar Links - Instant transitions */
.sidebar-link { 
    transition: background 0.1s, color 0.1s; 
    color: rgba(255,255,255,0.8);
}
.sidebar-link:hover { 
    background: rgba(255,255,255,0.1); 
    color: white;
}
.sidebar-link.active { 
    background: rgba(255,255,255,0.15);
    color: white;
    border-left: 3px solid #60a5fa;
    font-weight: 600;
}
.sidebar-link.active .material-symbols-outlined { color: white; }

/* Scrollbar - Minimal */
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { 
    background: rgba(255,255,255,0.2); 
    border-radius: 10px; 
}

/* Cards - No shadow for performance */
.admin-card { 
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

/* Fast animations */
@keyframes slideIn {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-slide-in {
    animation: slideIn 0.15s ease-out;
}

/* Hardware acceleration */
#sidebar, #sidebar-overlay, .admin-card {
    will-change: transform;
    transform: translateZ(0);
}
</style>
<?php if (!empty($extra_head)) echo $extra_head; ?>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen">

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

<div class="flex h-screen overflow-hidden bg-admin-bg">
<!-- ── Mobile Overlay ──────────────────────────────────────────────────── -->
<div id="sidebar-overlay" class="hidden fixed inset-0 bg-black/50 z-40 md:hidden" onclick="toggleSidebar()"></div>

<!-- ── Sidebar ──────────────────────────────────────────────────────────── -->
<aside id="sidebar" class="fixed md:relative w-72 bg-sidebar-bg flex flex-col shrink-0 z-50 h-full -translate-x-full md:translate-x-0 transition-transform duration-150 ease-out shadow-2xl">
    <!-- Logo -->
    <div class="h-16 flex items-center gap-3 px-4 border-b border-white/10 shrink-0">
        <img src="../images/travelhub.png" alt="CSNExplore" class="h-8 object-contain shrink-0 brightness-0 invert"/>
        <button id="sidebar-close" class="ml-auto md:hidden text-white/60 hover:text-white p-2" onclick="toggleSidebar()">
            <span class="material-symbols-outlined text-xl">close</span>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 custom-scrollbar">
        <?php
        $nav = [
            ['href'=>'dashboard.php', 'icon'=>'grid_view',       'label'=>'Dashboard',    'key'=>'dashboard'],
            ['href'=>'listings.php',  'icon'=>'database',        'label'=>'Listings',     'key'=>'listings'],
            ['href'=>'bookings.php',  'icon'=>'calendar_today',  'label'=>'Bookings',     'key'=>'bookings',  'badge'=>true],
            ['href'=>'trip-requests.php','icon'=>'flight_takeoff','label'=>'Trip Planner', 'key'=>'trip-requests'],
            ['href'=>'blogs.php',     'icon'=>'article',          'label'=>'Blogs',        'key'=>'blogs'],
            ['href'=>'gallery.php',   'icon'=>'photo_library',    'label'=>'Gallery',      'key'=>'gallery'],
            ['href'=>'users.php',     'icon'=>'group',           'label'=>'Users',        'key'=>'users'],
            ['href'=>'content.php',   'icon'=>'edit_note',        'label'=>'Content',      'key'=>'content'],
        ];
        foreach ($nav as $n):
            $active = ($admin_page === $n['key']) ? 'active' : '';
        ?>
        <a href="<?php echo $n['href']; ?>"
           class="sidebar-link <?php echo $active; ?> flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium group">
            <span class="material-symbols-outlined text-[18px]"><?php echo $n['icon']; ?></span>
            <span class="flex-1"><?php echo $n['label']; ?></span>
            <?php if (!empty($n['badge'])): ?>
            <span id="sidebar-pending-badge" class="hidden bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"></span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- User Profile -->
    <div class="mx-3 mb-4 p-3 bg-white/10 border border-white/20 rounded-xl shrink-0">
        <div class="flex items-center gap-2 mb-3">
            <div class="relative">
                <div class="w-9 h-9 bg-white/20 border-2 border-white/30 rounded-xl flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-white text-lg">account_circle</span>
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-sidebar-bg rounded-full"></div>
            </div>
            <div class="min-w-0 flex-1">
                <p id="admin-name" class="text-xs font-bold text-white truncate">Admin</p>
                <p id="admin-email" class="text-[9px] text-white/60 truncate">admin@csnexplore.com</p>
            </div>
        </div>
        <button onclick="adminLogout()"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-white/10 border border-white/20 text-white hover:bg-white/20 rounded-xl transition-all font-bold text-xs">
            <span class="material-symbols-outlined text-sm">logout</span> Sign Out
        </button>
    </div>
</aside>

<!-- ── Main ─────────────────────────────────────────────────────────────── -->
<div class="flex-1 flex flex-col overflow-hidden relative w-full">
    <!-- Top bar -->
    <header class="h-14 md:h-16 bg-header-bg shadow-md flex items-center justify-between px-4 md:px-6 z-20 shrink-0">
        <div class="flex items-center gap-3">
            <button id="sidebar-toggle" class="md:hidden text-white/80 hover:text-white p-2 -ml-2 transition-colors" onclick="toggleSidebar()">
                <span class="material-symbols-outlined">menu</span>
            </button>
            <h1 class="text-sm md:text-base font-bold text-white truncate"><?php echo htmlspecialchars($admin_title); ?></h1>
        </div>
        
        <div class="flex items-center gap-2 md:gap-4">
            <div class="hidden sm:flex items-center gap-1.5 px-2 md:px-3 py-1 bg-green-500/20 text-green-300 rounded-full border border-green-400/30">
                <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-[9px] md:text-[10px] font-bold">Online</span>
            </div>

            <div class="hidden sm:block h-4 md:h-6 w-px bg-white/20"></div>

            <a href="../index.php" target="_blank"
               class="text-[10px] md:text-xs font-semibold text-white/70 hover:text-white transition-all flex items-center gap-1">
                <span class="material-symbols-outlined text-sm md:text-base">open_in_new</span> 
                <span class="hidden sm:inline">View Site</span>
            </a>
            
            <div id="pending-badge" class="hidden items-center gap-1 md:gap-1.5 bg-orange-500/20 text-orange-300 rounded-full border border-orange-400/30 text-[9px] md:text-[10px] font-bold px-2 md:px-3 py-1 md:py-1.5">
                <span class="material-symbols-outlined text-xs md:text-sm">notifications</span>
                <span id="pending-count">0</span>
            </div>
        </div>
    </header>

<script>
// API helper - FAST with minimal logging
async function api(url, options = {}) {
    try {
        options.headers = options.headers || {};
        options.headers['Content-Type'] = 'application/json';
        
        if (window._adminToken) {
            options.headers['Authorization'] = 'Bearer ' + window._adminToken;
        }
        
        var res = await fetch(url, options);
        
        if (res.status === 401 || res.status === 403) { 
            adminLogout(); 
            return null; 
        }
        
        var contentType = res.headers.get('content-type');
        
        if (contentType && contentType.includes('application/json')) {
            return await res.json();
        } else {
            console.error('[API] Invalid response type');
            return null;
        }
    } catch (error) {
        console.error('[API] Error:', error.message);
        return null;
    }
}

// Toast - FAST
function showAdminToast(msg, type) {
    var t = document.createElement('div');
    var bg = type === 'error' ? 'bg-red-600' : 'bg-slate-900';
    t.className = 'fixed bottom-4 right-4 ' + bg + ' text-white text-xs md:text-sm px-4 py-2 rounded-lg shadow-xl z-[200]';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function(){ t.remove(); }, 2500);
}

// Mobile sidebar toggle - INSTANT
function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var isOpen = !sidebar.classList.contains('-translate-x-full');
    
    if (isOpen) {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }
}

// Close sidebar on window resize to desktop
var resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (window.innerWidth >= 768) {
            document.getElementById('sidebar').classList.remove('-translate-x-full');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        }
    }, 100);
});
</script>

    <!-- Page content -->
    <main class="flex-1 overflow-y-auto p-4 md:p-6 w-full">
