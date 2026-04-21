<?php
require_once '../php/config.php';
$page_title = 'Trip Requests | Admin';
$admin_page = 'trip-requests';

$db = getDB();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $id = (int)$_POST['request_id'];
    $status = $_POST['status'];
    $db->query("UPDATE trip_requests SET status = ? WHERE id = ?", [$status, $id]);
    header('Location: trip-requests.php?updated=1');
    exit;
}

// Fetch all trip requests
$requests = $db->fetchAll("SELECT * FROM trip_requests ORDER BY created_at DESC");

$admin_title = 'Trip Requests';
require 'admin-header.php';
?>

<div class="animate-slide-in">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900 mb-1">Trip Requests</h1>
            <p class="text-xs md:text-sm text-slate-600">Manage customer trip planning requests</p>
        </div>
        <div class="admin-card px-4 py-2 self-start">
            <p class="text-xs text-slate-500 font-medium">Total Requests</p>
            <p class="text-2xl font-bold text-primary" id="total-count"><?php echo count($requests); ?></p>
        </div>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="mb-4 md:mb-6 p-3 md:p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">check_circle</span>
        Status updated successfully!
    </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
            <input type="text" id="search-input" placeholder="Search by name, email, phone, or interests..." 
                   class="w-full pl-12 pr-4 py-3 border border-slate-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                   oninput="filterRequests()">
        </div>
    </div>

    <?php if (empty($requests)): ?>
    <div class="admin-card text-center py-16 md:py-20">
        <span class="material-symbols-outlined text-6xl md:text-7xl text-slate-300 mb-4">travel_explore</span>
        <p class="text-slate-600 text-base md:text-lg font-medium">No trip requests yet</p>
        <p class="text-slate-500 text-sm mt-2">New requests will appear here</p>
    </div>
    <?php else: ?>
    
    <!-- Mobile Cards View -->
    <div class="block md:hidden space-y-3" id="mobile-cards">
        <?php foreach ($requests as $req): ?>
        <div class="admin-card p-4 hover:shadow-lg transition-all request-card"
             data-name="<?php echo htmlspecialchars($req['full_name']); ?>"
             data-email="<?php echo htmlspecialchars($req['email']); ?>"
             data-phone="<?php echo htmlspecialchars($req['phone']); ?>"
             data-interests="<?php echo htmlspecialchars($req['interests'] ?? ''); ?>">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <p class="font-bold text-slate-900 text-sm"><?php echo htmlspecialchars($req['full_name']); ?></p>
                    <p class="text-xs text-slate-500 mt-1">#<?php echo $req['id']; ?> • <?php echo date('M d, Y', strtotime($req['created_at'])); ?></p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-semibold <?php 
                    echo $req['status'] === 'new' ? 'bg-blue-100 text-blue-700' : 
                        ($req['status'] === 'contacted' ? 'bg-yellow-100 text-yellow-700' : 
                        ($req['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')); 
                ?>"><?php echo ucfirst($req['status']); ?></span>
            </div>
            <div class="space-y-2 mb-3 pb-3 border-b border-slate-200">
                <a href="mailto:<?php echo htmlspecialchars($req['email']); ?>" class="text-xs text-primary hover:text-orange-600 block transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">mail</span>
                    <?php echo htmlspecialchars($req['email']); ?>
                </a>
                <a href="tel:<?php echo htmlspecialchars($req['phone']); ?>" class="text-xs text-primary hover:text-orange-600 block transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">call</span>
                    <?php echo htmlspecialchars($req['phone']); ?>
                </a>
            </div>
            <div class="flex gap-2">
                <button onclick="showDetails(<?php echo htmlspecialchars(json_encode($req)); ?>)" class="flex-1 text-xs bg-primary hover:bg-orange-600 text-white font-bold py-2.5 px-3 rounded-lg transition-all shadow-sm">
                    View Details
                </button>
                <form method="POST" class="flex-1">
                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                    <select name="status" onchange="this.form.submit()" class="w-full text-xs font-semibold px-2 py-2.5 rounded-lg bg-white border border-slate-300 text-slate-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-all">
                        <option value="new" <?php echo $req['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                        <option value="contacted" <?php echo $req['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                        <option value="completed" <?php echo $req['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $req['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                    <input type="hidden" name="update_status" value="1">
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Desktop Table View -->
    <div class="hidden md:block admin-card overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Contact</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Details</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-200" id="requests-tbody">
                <?php foreach ($requests as $req): ?>
                <tr class="hover:bg-slate-50 transition-colors request-row" 
                    data-name="<?php echo htmlspecialchars($req['full_name']); ?>"
                    data-email="<?php echo htmlspecialchars($req['email']); ?>"
                    data-phone="<?php echo htmlspecialchars($req['phone']); ?>"
                    data-interests="<?php echo htmlspecialchars($req['interests'] ?? ''); ?>">
                    <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">
                        #<?php echo $req['id']; ?>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-900 font-semibold">
                        <div class="max-w-[150px] truncate"><?php echo htmlspecialchars($req['full_name']); ?></div>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <div class="space-y-1">
                            <a href="mailto:<?php echo htmlspecialchars($req['email']); ?>" class="text-primary hover:text-orange-600 transition-colors block truncate max-w-[180px]" title="<?php echo htmlspecialchars($req['email']); ?>">
                                <?php echo htmlspecialchars($req['email']); ?>
                            </a>
                            <a href="tel:<?php echo htmlspecialchars($req['phone']); ?>" class="text-slate-600 hover:text-primary transition-colors block">
                                <?php echo htmlspecialchars($req['phone']); ?>
                            </a>
                        </div>
                    </td>
                    <td class="px-4 py-4 text-sm text-slate-600">
                        <div class="max-w-[200px]">
                            <div class="text-xs space-y-1">
                                <div class="truncate"><strong>Interests:</strong> <?php echo htmlspecialchars($req['interests'] ?? 'N/A'); ?></div>
                                <div><strong>Stay:</strong> <?php echo htmlspecialchars($req['stay_type'] ?? 'N/A'); ?></div>
                                <div><strong>Travel:</strong> <?php echo htmlspecialchars($req['travel_mode'] ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <form method="POST" class="inline">
                            <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                            <select name="status" onchange="this.form.submit()" class="px-2 py-1 rounded-full text-xs font-semibold cursor-pointer border-0 focus:outline-none focus:ring-2 focus:ring-primary <?php 
                                echo $req['status'] === 'new' ? 'bg-blue-100 text-blue-700' : 
                                    ($req['status'] === 'contacted' ? 'bg-yellow-100 text-yellow-700' : 
                                    ($req['status'] === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700')); 
                            ?>">
                                <option value="new" <?php echo $req['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                <option value="contacted" <?php echo $req['status'] === 'contacted' ? 'selected' : ''; ?>>Contacted</option>
                                <option value="completed" <?php echo $req['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo $req['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600">
                        <?php echo date('M d, Y', strtotime($req['created_at'])); ?>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                        <button onclick="showDetails(<?php echo htmlspecialchars(json_encode($req)); ?>)" class="text-primary hover:text-orange-600 font-semibold transition-colors">
                            View
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Details Modal -->
<div id="details-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white max-w-2xl w-full max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl p-6 md:p-8 animate-slide-in">
        <div class="flex items-center justify-between mb-4 md:mb-6 pb-4 border-b border-slate-200">
            <h2 class="text-xl md:text-2xl font-bold text-slate-900">Trip Request Details</h2>
            <button onclick="closeDetails()" class="text-slate-400 hover:text-primary p-2 transition-colors rounded-lg hover:bg-slate-100">
                <span class="material-symbols-outlined text-2xl md:text-3xl">close</span>
            </button>
        </div>
        <div id="details-content" class="space-y-3 md:space-y-4">
            <!-- Content will be inserted here -->
        </div>
    </div>
</div>

<script>
// Search functionality - OPTIMIZED
let searchTimeout;
function filterRequests() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        const searchTerm = document.getElementById('search-input').value.toLowerCase();
        
        // Filter desktop table rows
        const rows = document.querySelectorAll('.request-row');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const matches = searchTerm === '' || 
                           row.dataset.name.toLowerCase().includes(searchTerm) || 
                           row.dataset.email.toLowerCase().includes(searchTerm) || 
                           row.dataset.phone.includes(searchTerm) || 
                           row.dataset.interests.toLowerCase().includes(searchTerm);
            
            row.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });
        
        // Filter mobile cards
        const cards = document.querySelectorAll('.request-card');
        cards.forEach(card => {
            const matches = searchTerm === '' || 
                           card.dataset.name.toLowerCase().includes(searchTerm) || 
                           card.dataset.email.toLowerCase().includes(searchTerm) || 
                           card.dataset.phone.includes(searchTerm) || 
                           card.dataset.interests.toLowerCase().includes(searchTerm);
            
            card.style.display = matches ? '' : 'none';
        });
        
        // Update count
        document.getElementById('total-count').textContent = visibleCount;
    }, 150);
}

function showDetails(req) {
    const modal = document.getElementById('details-modal');
    const content = document.getElementById('details-content');
    
    // Fast innerHTML update
    content.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Full Name</label>
                <p class="text-sm text-slate-900 font-semibold">${req.full_name}</p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Email</label>
                <p class="text-sm"><a href="mailto:${req.email}" class="text-primary hover:text-orange-600 break-all">${req.email}</a></p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Phone</label>
                <p class="text-sm"><a href="tel:${req.phone}" class="text-primary hover:text-orange-600">${req.phone}</a></p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Status</label>
                <p class="text-sm text-slate-900 font-semibold capitalize">${req.status}</p>
            </div>
            <div class="admin-card p-3 md:col-span-2">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Interests</label>
                <p class="text-sm text-slate-700">${req.interests || 'N/A'}</p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Stay Type</label>
                <p class="text-sm text-slate-700">${req.stay_type || 'N/A'}</p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Travel Mode</label>
                <p class="text-sm text-slate-700">${req.travel_mode || 'N/A'}</p>
            </div>
            <div class="admin-card p-3 md:col-span-2">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Travel Details</label>
                <p class="text-sm text-slate-700">${req.travel_details || 'N/A'}</p>
            </div>
            <div class="admin-card p-3 md:col-span-2">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Extra Notes</label>
                <p class="text-sm text-slate-700">${req.extra_notes || 'No special requests'}</p>
            </div>
            <div class="admin-card p-3">
                <label class="text-xs font-semibold text-slate-500 uppercase block mb-1">Submitted</label>
                <p class="text-sm text-slate-700">${new Date(req.created_at).toLocaleString()}</p>
            </div>
        </div>
        <div class="mt-4 flex flex-col sm:flex-row gap-3">
            <a href="tel:${req.phone}" class="flex-1 bg-primary hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl text-center text-sm shadow-lg flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">call</span>
                Call Now
            </a>
            <a href="https://wa.me/${req.phone.replace(/[^0-9]/g, '')}" target="_blank" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl text-center text-sm shadow-lg flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-lg">chat</span>
                WhatsApp
            </a>
        </div>
    `;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetails() {
    document.getElementById('details-modal').classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.getElementById('details-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDetails();
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDetails();
});
</script>

<?php require 'admin-footer.php'; ?>
