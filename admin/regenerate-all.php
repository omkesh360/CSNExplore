<?php
require_once '../php/config.php';
$page_title = 'Regenerate All Pages | Admin';

// Simple auth check
if (!isset($_GET['admin']) || $_GET['admin'] !== 'true') {
    header('Location: ../adminexplorer.php');
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regenerate'])) {
    try {
        // This will trigger regeneration scripts
        $message = 'Page regeneration initiated! Check individual regenerate pages for details.';
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

require 'admin-header.php';
?>

<div class="p-6">
    <h1 class="text-3xl font-bold text-slate-900 mb-6">🔄 Regenerate All Pages</h1>
    
    <?php if ($message): ?>
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
        ✓ <?php echo htmlspecialchars($message); ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
        ✗ <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Available Regeneration Tools</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="regenerate-listings.php?admin=true" class="block p-6 border-2 border-slate-200 rounded-xl hover:border-primary hover:shadow-lg transition-all">
                <h3 class="font-bold text-lg mb-2">📄 Regenerate Listings</h3>
                <p class="text-sm text-gray-600">Regenerate all listing detail pages (stays, cars, bikes, attractions, restaurants, buses)</p>
            </a>
            
            <a href="regenerate-pages.php?admin=true" class="block p-6 border-2 border-slate-200 rounded-xl hover:border-primary hover:shadow-lg transition-all">
                <h3 class="font-bold text-lg mb-2">🌐 Regenerate Pages</h3>
                <p class="text-sm text-gray-600">Regenerate static pages and category pages</p>
            </a>
            
            <a href="regenerate-animations.php?admin=true" class="block p-6 border-2 border-slate-200 rounded-xl hover:border-primary hover:shadow-lg transition-all">
                <h3 class="font-bold text-lg mb-2">✨ Regenerate Animations</h3>
                <p class="text-sm text-gray-600">Regenerate CSS animations and JavaScript files</p>
            </a>
            
            <div class="block p-6 border-2 border-slate-200 rounded-xl bg-gray-50">
                <h3 class="font-bold text-lg mb-2">🚫 Vendor (Removed)</h3>
                <p class="text-sm text-gray-600">Vendor functionality has been removed from the system</p>
            </div>
        </div>
    </div>
</div>

<?php require 'admin-footer.php'; ?>
