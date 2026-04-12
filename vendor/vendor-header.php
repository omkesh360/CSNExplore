<?php
$vendor_page  = $vendor_page  ?? '';
$vendor_title = $vendor_title ?? 'Vendor Portal';

if (!defined('BASE_PATH'))      require_once __DIR__ . '/../php/config.php';

if (!defined('VENDOR_API_BASE')) {
    $_vs  = $_SERVER['SCRIPT_NAME'] ?? '';
    $_sb  = dirname(dirname($_vs));
    if ($_sb === '/' || $_sb === '\\') $_sb = '';
    define('VENDOR_API_BASE', rtrim($_sb, '/'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1"/>
<title><?php echo htmlspecialchars($vendor_title); ?> — CSNExplore Vendor</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'Roboto', Arial, sans-serif; background: #f5f5f5; color: #333; }
.material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; line-height: 1; vertical-align: middle; }

/* Header */
header { background: white; border-bottom: 1px solid #ddd; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.logo { font-size: 20px; font-weight: bold; color: #ec5b13; text-decoration: none; display: flex; align-items: center; gap: 8px; }
.logo span { font-size: 24px; }
.header-right { display: flex; align-items: center; gap: 20px; }
.vendor-info { display: flex; align-items: center; gap: 10px; }
.vendor-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #ec5b13, #ff8c42); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 16px; }
.vendor-name { font-size: 14px; font-weight: 500; }
.vendor-status { font-size: 12px; color: #666; }

/* Sidebar Navigation */
.sidebar { background: white; width: 250px; min-height: calc(100vh - 60px); border-right: 1px solid #ddd; padding: 20px 0; position: fixed; left: 0; top: 60px; overflow-y: auto; }
.nav-section { margin-bottom: 20px; }
.nav-section-title { font-size: 12px; font-weight: bold; color: #999; text-transform: uppercase; padding: 0 20px; margin-bottom: 10px; letter-spacing: 0.5px; }
.nav-link { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: #666; text-decoration: none; font-size: 14px; transition: all 0.2s; border-left: 3px solid transparent; }
.nav-link:hover { background: #f5f5f5; color: #ec5b13; }
.nav-link.active { background: #fff3e0; color: #ec5b13; border-left-color: #ec5b13; font-weight: 500; }
.nav-link span { font-size: 20px; }

/* Main Content */
.main-content { margin-left: 250px; margin-top: 60px; padding: 30px; }
.page-header { background: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.page-header h1 { font-size: 28px; margin-bottom: 5px; color: #333; }
.page-header p { font-size: 14px; color: #666; }

/* Responsive */
@media (max-width: 768px) {
    .sidebar { width: 100%; position: relative; top: 0; min-height: auto; border-right: none; border-bottom: 1px solid #ddd; display: flex; overflow-x: auto; padding: 0; }
    .nav-section { margin-bottom: 0; display: flex; gap: 0; }
    .nav-section-title { display: none; }
    .nav-link { padding: 15px 15px; border-left: none; border-bottom: 3px solid transparent; white-space: nowrap; }
    .nav-link.active { border-bottom-color: #ec5b13; border-left: none; }
    .main-content { margin-left: 0; margin-top: 0; padding: 15px; }
    header { flex-direction: column; gap: 10px; }
    .header-right { width: 100%; justify-content: space-between; }
}

/* Toast Notifications */
.toast { position: fixed; bottom: 20px; right: 20px; background: #333; color: white; padding: 15px 20px; border-radius: 4px; z-index: 2000; font-size: 14px; }
.toast.success { background: #28a745; }
.toast.error { background: #dc3545; }
.toast.info { background: #17a2b8; }
</style>
</head>
<body>

<!-- Header -->
<header>
    <a href="<?php echo VENDOR_API_BASE; ?>/vendor/dashboard.php" class="logo">
        <span class="material-symbols-outlined">storefront</span>
        <span>CSNExplore Vendor</span>
    </a>
    <div class="header-right">
        <div class="vendor-info">
            <div class="vendor-avatar" id="vendor-avatar">V</div>
            <div>
                <div class="vendor-name" id="vendor-name">Vendor</div>
                <div class="vendor-status">Active Vendor</div>
            </div>
        </div>
        <button onclick="vendorLogout()" style="background: none; border: none; cursor: pointer; color: #666; font-size: 20px; display: flex; align-items: center; gap: 5px;">
            <span class="material-symbols-outlined">logout</span>
            <span style="font-size: 14px;">Sign Out</span>
        </button>
    </div>
</header>

<!-- Sidebar Navigation -->
<aside class="sidebar">
    <div class="nav-section">
        <div class="nav-section-title">Overview</div>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/dashboard.php" class="nav-link <?php echo $vendor_page === 'dashboard' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">dashboard</span>
            <span>Dashboard</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Listings</div>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/stays.php" class="nav-link <?php echo $vendor_page === 'stays' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">apartment</span>
            <span>Hotel & Stays</span>
        </a>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/rooms.php" class="nav-link <?php echo $vendor_page === 'rooms' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">bed</span>
            <span>Rooms</span>
        </a>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/cars.php" class="nav-link <?php echo $vendor_page === 'cars' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">directions_car</span>
            <span>Cars</span>
        </a>
    </div>

    <div class="nav-section">
        <div class="nav-section-title">Business</div>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/bookings.php" class="nav-link <?php echo $vendor_page === 'bookings' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">calendar_month</span>
            <span>Bookings</span>
        </a>
        <a href="<?php echo VENDOR_API_BASE; ?>/vendor/profile.php" class="nav-link <?php echo $vendor_page === 'profile' ? 'active' : ''; ?>">
            <span class="material-symbols-outlined">manage_accounts</span>
            <span>Profile & Security</span>
        </a>
    </div>
</aside>

<!-- Main Content Area -->
<div class="main-content">

<script>
// Initialize vendor info
(function(){
    try {
        var user = JSON.parse(localStorage.getItem('csn_vendor_user') || '{}');
        var token = localStorage.getItem('csn_vendor_token');
        
        if (!token || !user.id) {
            window.location.href = '<?php echo VENDOR_API_BASE; ?>/vendor/vendorlogin.php';
            return;
        }
        
        window._vendorToken = token;
        window._vendorUser = user;
        
        var name = user.name || user.username || 'Vendor';
        var initial = name.charAt(0).toUpperCase();
        
        document.getElementById('vendor-avatar').textContent = initial;
        document.getElementById('vendor-name').textContent = name;
    } catch(e) {
        console.error('Error loading vendor info:', e);
    }
})();

// API Helper
async function vendorApi(url, options = {}) {
    try {
        if (!(options.body instanceof FormData)) {
            options.headers = Object.assign({'Content-Type':'application/json'}, options.headers||{});
        }
        if (window._vendorToken) {
            options.headers = options.headers || {};
            options.headers['Authorization'] = 'Bearer ' + window._vendorToken;
        }
        var res = await fetch(url, options);
        if (res.status === 401 || res.status === 403) {
            vendorLogout();
            return null;
        }
        var ct = res.headers.get('content-type');
        return (ct && ct.includes('application/json')) ? await res.json() : null;
    } catch(e) {
        console.error('[VendorAPI]', e);
        return null;
    }
}

// Toast Notification
function showToast(message, type = 'success') {
    var toast = document.createElement('div');
    toast.className = 'toast ' + type;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

// Logout
function vendorLogout() {
    localStorage.removeItem('csn_vendor_token');
    localStorage.removeItem('csn_vendor_user');
    window.location.href = '<?php echo VENDOR_API_BASE; ?>/vendor/vendorlogin.php';
}

// Format currency
function fmt(n, d=0) {
    var v = parseFloat(n||0);
    return '₹' + v.toLocaleString('en-IN', {minimumFractionDigits:d, maximumFractionDigits:d});
}
</script>
