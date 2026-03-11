<?php
/**
 * TravelHub — One-Click Installer
 * ===================================
 * Run this ONCE after uploading to your PHP server:
 *   https://yourdomain.com/install.php
 *
 * DELETE THIS FILE after installation is complete!
 */

// ---- Basic Security ---- //
define('INSTALL_PASSWORD', 'install1234'); // Change before uploading!

// Password gate
if (!isset($_GET['key']) || $_GET['key'] !== INSTALL_PASSWORD) {
    die('<h2>🔒 Access Denied</h2><p>Add <code>?key=install1234</code> to the URL to run setup.</p>');
}

set_time_limit(120);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$log = [];
$errors = [];

function logMsg($msg) { global $log; $log[] = $msg; echo "<p style='color:#16a34a'>✅ $msg</p>"; flush(); }
function logErr($msg) { global $errors; $errors[] = $msg; echo "<p style='color:#dc2626'>❌ $msg</p>"; flush(); }
function logInfo($msg) { echo "<p style='color:#2563eb'>ℹ️ $msg</p>"; flush(); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>TravelHub Installer</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; max-width: 720px; margin: 40px auto; padding: 20px; line-height: 1.6; color: #1e293b; }
  h1 { color: #003580; }
  h2 { color: #1e40af; margin-top: 2em; }
  pre { background: #f1f5f9; padding: 12px; border-radius: 8px; overflow-x: auto; font-size: 13px; }
  .box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px; margin-top: 24px; }
  .warn { background: #fef9c3; border: 1px solid #fde047; border-radius: 12px; padding: 16px; margin-top: 16px; }
  .success { background: #dcfce7; border: 1px solid #86efac; border-radius: 12px; padding: 16px; margin-top: 16px; }
  .error { background: #fee2e2; border: 1px solid #fca5a5; border-radius: 12px; padding: 16px; margin-top: 16px; }
</style>
</head>
<body>
<h1>🚀 TravelHub Installer</h1>
<p>Setting up your TravelHub PHP server...</p>
<hr>

<?php

// ============================
// 1. CHECK PHP REQUIREMENTS
// ============================
logInfo("Checking PHP requirements...");

$phpVersion = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
if (version_compare(PHP_VERSION, '7.4.0') < 0) {
    logErr("PHP 7.4+ required. Current: " . PHP_VERSION);
} else {
    logMsg("PHP " . PHP_VERSION . " ✓");
}

$requiredExtensions = ['pdo', 'pdo_sqlite', 'json', 'mbstring'];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        logErr("Missing PHP extension: $ext");
    } else {
        logMsg("Extension: $ext ✓");
    }
}

// ============================
// 2. CREATE DIRECTORIES
// ============================
logInfo("Creating required directories...");

$dirs = [
    __DIR__ . '/database',
    __DIR__ . '/public/images/uploads',
    __DIR__ . '/data',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            logMsg("Created: " . str_replace(__DIR__, '.', $dir));
        } else {
            logErr("Failed to create: " . str_replace(__DIR__, '.', $dir));
        }
    } else {
        logMsg("Directory exists: " . str_replace(__DIR__, '.', $dir));
    }
}

// ============================
// 3. DATABASE SETUP
// ============================
logInfo("Initializing SQLite database...");

$dbPath = __DIR__ . '/database/travelhub.db';
try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db->exec('PRAGMA foreign_keys = ON');
    logMsg("SQLite database created/opened: $dbPath");
} catch (Exception $e) {
    logErr("Database failed: " . $e->getMessage());
    die("</body></html>");
}

// ============================
// 4. RUN SCHEMA
// ============================
logInfo("Running database schema...");
$schema = <<<'SQL'
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role VARCHAR(20) NOT NULL DEFAULT 'user',
    is_verified INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS homepage_content (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_key VARCHAR(100) UNIQUE NOT NULL,
    content TEXT,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER,
    item VARCHAR(255),
    type VARCHAR(50),
    date VARCHAR(50),
    amount DECIMAL(10,2) DEFAULT 0,
    status VARCHAR(50) DEFAULT 'pending',
    user VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vendors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    description TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    rating DECIMAL(3,2) DEFAULT 0,
    total_bookings INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS blogs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(500) NOT NULL,
    content TEXT,
    author VARCHAR(255) DEFAULT 'Admin',
    image VARCHAR(500),
    status VARCHAR(50) DEFAULT 'published',
    category VARCHAR(100),
    read_time VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
SQL;

try {
    $db->exec($schema);
    logMsg("Database schema applied successfully");
} catch (Exception $e) {
    logErr("Schema error: " . $e->getMessage());
}

// ============================
// 5. SEED ADMIN USER
// ============================
logInfo("Creating admin account...");

$adminEmail = 'admin@travelhub.com';
$adminPassword = 'admin123';
$adminHash = password_hash($adminPassword, PASSWORD_DEFAULT);

$existing = $db->prepare('SELECT id FROM users WHERE email = ?');
$existing->execute([$adminEmail]);
if ($existing->fetch()) {
    // Update password in case it changed
    $db->prepare('UPDATE users SET password_hash = ?, role = ?, is_verified = 1 WHERE email = ?')
       ->execute([$adminHash, 'admin', $adminEmail]);
    logMsg("Admin account updated: $adminEmail");
} else {
    $db->prepare('INSERT INTO users (email, password_hash, name, role, is_verified) VALUES (?,?,?,?,1)')
       ->execute([$adminEmail, $adminHash, 'Admin User', 'admin']);
    logMsg("Admin account created: $adminEmail");
}

// ============================
// 6. SEED BLOG POSTS
// ============================
logInfo("Seeding blog posts...");

$blogs = [
    [
        'title'     => 'Decoding the ancient murals of Ajanta Caves',
        'content'   => '<h2>Introduction</h2><p>Ajanta Caves, carved into a horseshoe-shaped cliff face along the Waghora River, contain some of the finest surviving examples of ancient Indian art. Dating back over 2,000 years, these 30 rock-cut Buddhist cave monuments span two distinct periods.</p><h2>The Murals</h2><p>The murals that adorn the interior walls are nothing short of miraculous. Painted using mineral pigments, the artists depicted scenes from the Jataka tales with extraordinary detail and emotional depth.</p><blockquote>The artistry of Ajanta is a window into a civilization at its creative peak.</blockquote><h2>Visiting Tips</h2><ul><li>Best time to visit: October to March</li><li>Opening hours: 9 AM to 5:30 PM (closed Mondays)</li><li>105 km from Chhatrapati Sambhajinagar</li><li>UNESCO World Heritage Site since 1983</li></ul>',
        'author'    => 'TravelHub Team',
        'image'     => '/images/ajanta.png',
        'category'  => 'Heritage',
        'read_time' => '8 min read',
        'status'    => 'published',
    ],
    [
        'title'     => 'Bibi Ka Maqbara: The Deccan\'s Taj Mahal',
        'content'   => '<h2>Introduction</h2><p>Often called the "Taj of the Deccan," Bibi Ka Maqbara was built in 1660 CE by Prince Azam Shah in memory of his mother Dilras Banu Begum. This architectural masterpiece echoes the grandeur of the Taj Mahal.</p><h2>Architecture</h2><p>Built from lime plaster over a rubble and brick core, the monument features four minarets, a central dome, and a reflecting pool. The octagonal chamber inside houses some of the finest marble jaali (lattice screen) work in Mughal architecture.</p><blockquote>From a distance, the resemblance to the Taj Mahal is breathtaking.</blockquote><h2>Visiting Tips</h2><ul><li>Located in the heart of Chhatrapati Sambhajinagar</li><li>Entry: ₹25 for Indians, ₹300 for foreigners</li><li>Best at sunrise or sunset</li><li>Allow 1–2 hours for a full visit</li></ul>',
        'author'    => 'TravelHub Team',
        'image'     => '/images/bibi.png',
        'category'  => 'History',
        'read_time' => '5 min read',
        'status'    => 'published',
    ],
    [
        'title'     => 'The best street food spots in Chhatrapati Sambhajinagar',
        'content'   => '<h2>Introduction</h2><p>Chhatrapati Sambhajinagar\'s culinary scene is a vibrant tapestry woven from Mughal traditions, Marathwada influences, and the flavors of a city that truly loves its food.</p><h2>Must-Try Foods</h2><h3>Naan Qalia</h3><p>The signature dish of the city — tender mutton in rich, spiced gravy served with fluffy naan bread. Best found in the old city areas where recipes have been passed down for generations.</p><h3>Misal Pav</h3><p>A fiery Maharashtrian breakfast staple. The usal cooked with red gravy, topped with farsan and coriander, served with soft pav buns.</p><h3>Haleem</h3><p>In the evenings, the old city fills with the aroma of slow-cooked haleem — meat, lentils, and broken wheat cooked for hours.</p><h2>Top Spots</h2><ul><li>Station Road — Misal Pav</li><li>Shahaganj Market — Naan Qalia</li><li>Roshan Gate — Haleem (evenings)</li><li>Sunday Market — street snacks</li></ul>',
        'author'    => 'TravelHub Team',
        'image'     => '/images/travel-streetfood.jpg',
        'category'  => 'Food & Drink',
        'read_time' => '6 min read',
        'status'    => 'published',
    ],
];

foreach ($blogs as $blog) {
    $check = $db->prepare('SELECT id FROM blogs WHERE title = ?');
    $check->execute([$blog['title']]);
    if ($check->fetch()) {
        $db->prepare('UPDATE blogs SET content=?,author=?,image=?,category=?,read_time=?,status=?,updated_at=? WHERE title=?')
           ->execute([$blog['content'],$blog['author'],$blog['image'],$blog['category'],$blog['read_time'],$blog['status'],date('Y-m-d H:i:s'),$blog['title']]);
        logMsg("Blog updated: " . $blog['title']);
    } else {
        $db->prepare('INSERT INTO blogs (title,content,author,image,category,read_time,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?)')
           ->execute([$blog['title'],$blog['content'],$blog['author'],$blog['image'],$blog['category'],$blog['read_time'],$blog['status'],date('Y-m-d H:i:s'),date('Y-m-d H:i:s')]);
        logMsg("Blog created: " . $blog['title']);
    }
}

// ============================
// 7. WRITE .htaccess PROTECTION FOR DB
// ============================
$dbHtaccess = __DIR__ . '/database/.htaccess';
if (!file_exists($dbHtaccess)) {
    file_put_contents($dbHtaccess, "Order deny,allow\nDeny from all\n");
    logMsg("Protected database directory with .htaccess");
}
$dataHtaccess = __DIR__ . '/data/.htaccess';
if (!file_exists($dataHtaccess)) {
    file_put_contents($dataHtaccess, "Order deny,allow\nDeny from all\n");
    logMsg("Protected data directory with .htaccess");
}

// ============================
// 8. CHECK FILE PERMISSIONS
// ============================
logInfo("Checking file permissions...");
if (is_writable(__DIR__ . '/database')) {
    logMsg("database/ folder is writable ✓");
} else {
    logErr("database/ folder is NOT writable — chmod it to 755 via cPanel File Manager");
}
if (is_writable(__DIR__ . '/public/images/uploads')) {
    logMsg("public/images/uploads/ is writable ✓");
} else {
    logErr("public/images/uploads/ is NOT writable — chmod it to 755 in cPanel");
}

// ============================
// DONE — SHOW SUMMARY
// ============================
?>

<div class="<?= empty($errors) ? 'success' : 'error' ?> box">
<?php if (empty($errors)): ?>
    <h2>🎉 Installation Complete!</h2>
    <p><strong>Your TravelHub site is ready.</strong></p>
    <table style="width:100%; border-collapse:collapse; font-size:14px">
        <tr><td style="padding:6px 0; color:#475569">Admin URL</td><td><a href="/admin-dashboard.html">/admin-dashboard.html</a></td></tr>
        <tr><td style="padding:6px 0; color:#475569">Admin Email</td><td><code>admin@travelhub.com</code></td></tr>
        <tr><td style="padding:6px 0; color:#475569">Admin Password</td><td><code>admin123</code></td></tr>
        <tr><td style="padding:6px 0; color:#475569">Homepage</td><td><a href="/">/</a></td></tr>
        <tr><td style="padding:6px 0; color:#475569">Blogs Page</td><td><a href="/blogs.html">/blogs.html</a></td></tr>
    </table>
<?php else: ?>
    <h2>⚠️ Installation completed with errors</h2>
    <p>Fix the errors listed above, then refresh this page.</p>
<?php endif; ?>
</div>

<div class="warn">
    <strong>⚠️ IMPORTANT: Delete this file immediately!</strong><br>
    Go to cPanel File Manager → find <code>install.php</code> in your root folder → Delete it.<br>
    Leaving this file on the server is a security risk.
</div>

<hr>
<p style="color:#94a3b8; font-size:13px">TravelHub Installer — <?= date('Y-m-d H:i:s') ?></p>
</body>
</html>
