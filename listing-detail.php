<?php
// Dynamic Listing Detail Page - Handles all listing types (stays, cars, bikes, restaurants, attractions, buses)
require_once 'php/config.php';
require_once 'php/jwt.php';

// Get category and ID from URL
$category = sanitize($_GET['category'] ?? '');
$id = (int)($_GET['id'] ?? 0);

// Validate inputs
$valid_categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
if (!in_array($category, $valid_categories) || !$id) {
    http_response_code(404);
    die('Listing not found');
}

// Fetch listing from database
$db = getDB();
$listing = $db->fetchOne("SELECT * FROM $category WHERE id = ? AND is_active = 1", [$id]);

if (!$listing) {
    http_response_code(404);
    die('Listing not found');
}

// Get room types if it's a stay listing
$room_types = [];
if ($category === 'stays') {
    $room_types = $db->fetchAll(
        "SELECT rt.*, (SELECT COUNT(*) FROM rooms WHERE room_type_id = rt.id) as rooms_count 
         FROM room_types rt 
         WHERE rt.stay_id = ? OR (rt.vendor_id = (SELECT vendor_id FROM stays WHERE id = ?) AND rt.stay_id IS NULL)
         ORDER BY rt.created_at DESC",
        [$id, $id]
    );
}

// Get user info if logged in
$user = null;
$token = getAuthToken();
if ($token) {
    $payload = verifyJWT($token, JWT_SECRET);
    if ($payload) {
        $user = $db->fetchOne("SELECT id, email, name FROM users WHERE id = ?", [$payload['user_id'] ?? 0]);
    }
}

// Determine price column name
$price_columns = [
    'stays' => 'price_per_night',
    'cars' => 'price_per_day',
    'bikes' => 'price_per_day',
    'restaurants' => 'price_per_person',
    'attractions' => 'entry_fee',
    'buses' => 'price',
];
$price_col = $price_columns[$category] ?? 'price';
$price = $listing[$price_col] ?? 0;

// Get similar listings (4 items)
$similar_listings = $db->fetchAll(
    "SELECT * FROM $category WHERE id != ? AND is_active = 1 ORDER BY RAND() LIMIT 4",
    [$id]
);

// Format title
$title = htmlspecialchars($listing['name'] ?? 'Listing');
$description = htmlspecialchars(substr($listing['description'] ?? '', 0, 160));
$image = $listing['image'] ?? '';
$map_embed = $listing['map_embed'] ?? '';
$page_meta = [
    'description' => htmlspecialchars(substr($listing['description'] ?? '', 0, 160)),
    'canonical' => "https://csnexplore.com/listing-detail/" . generateSlug($category, $id, $title) . ".html",
    'image' => (strpos($image, 'http') === 0) ? $image : "https://csnexplore.com/" . ltrim($image, '/'),
    'type' => 'place'
];

$schemaTypes = [
    'stays' => 'Hotel',
    'cars' => 'AutoRental',
    'bikes' => 'AutoRental',
    'attractions' => 'TouristAttraction',
    'restaurants' => 'Restaurant',
    'buses' => 'TransitAgency'
];
$schemaType = $schemaTypes[$category] ?? 'LocalBusiness';

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "' . $schemaType . '",
  "name": "' . $title . '",
  "image": "' . $page_meta['image'] . '",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '",
  "telephone": "+91-8600968888",
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "Chhatrapati Sambhajinagar",
    "addressRegion": "MH",
    "addressCountry": "IN"
  }
}
</script>';

$extra_styles = '
<style>
body{opacity:0;will-change:opacity;}
body.page-ready{animation:pageFadeIn 0.2s ease forwards;}
@keyframes pageFadeIn{from{opacity:0;}to{opacity:1;}}
.material-symbols-outlined{font-variation-settings:"FILL" 0,"wght" 400,"GRAD" 0,"opsz" 24;font-family:"Material Symbols Outlined";font-style:normal;display:inline-block;line-height:1;}
.glass{background:rgba(255,255,255,0.08);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.15);box-shadow:0 8px 32px rgba(0,0,0,0.1);}
.glass-button{background:rgba(236,91,19,0.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.2);box-shadow:0 4px 16px rgba(236,91,19,0.3);}
.glass-button:hover{background:rgba(236,91,19,0.95);box-shadow:0 6px 24px rgba(236,91,19,0.4);}
.gallery-thumb{cursor:zoom-in;position:relative;overflow:hidden;border-radius:12px;aspect-ratio:4/3;background:#f1f5f9;border:1px solid #e2e8f0;}
.gallery-thumb img{width:100%;height:100%;object-fit:cover;transition:all 0.4s cubic-bezier(0.4, 0, 0.2, 1);}
.gallery-thumb:hover img{transform:scale(1.08);filter:brightness(1.05);}
.line-clamp-2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;}
</style>';

require_once 'header.php';
?>
<!-- Main Content -->
<div class="max-w-6xl mx-auto px-4 py-8 mt-20">
    <!-- Listing Header -->
    <div class="flex flex-col lg:grid lg:grid-cols-3 gap-8 mb-12 lg:items-start">
        <!-- Left: Image & Details -->
        <div class="lg:col-span-2 order-1 lg:order-1 w-full">
            <!-- Main Image -->
            <div class="mb-6 rounded-lg overflow-hidden bg-gray-200 h-96">
                <?php if ($image): ?>
                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo $title; ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                        <span class="material-symbols-outlined text-6xl text-gray-400">image</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Title & Info -->
            <h1 class="text-4xl font-bold mb-2"><?php echo $title; ?></h1>
            <div class="flex items-center gap-4 mb-6 text-gray-600">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-lg">location_on</span>
                    <?php echo htmlspecialchars($listing['location'] ?? 'Location not specified'); ?>
                </span>
                <?php if ($listing['rating'] ?? 0): ?>
                    <span class="flex items-center gap-1">
                        <span class="material-symbols-outlined text-lg text-yellow-500">star</span>
                        <?php echo number_format($listing['rating'], 1); ?>/5
                    </span>
                <?php endif; ?>
            </div>

            <!-- Description -->
            <?php if ($listing['description']): ?>
                <div class="prose prose-sm max-w-none mb-8">
                    <p><?php echo nl2br(htmlspecialchars($listing['description'])); ?></p>
                </div>
            <?php endif; ?>

            <!-- Amenities/Features -->
            <?php if ($listing['amenities'] ?? $listing['features']): ?>
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4">Amenities & Features</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <?php 
                        $amenities = $listing['amenities'] ?? $listing['features'] ?? '';
                        $items = array_filter(array_map('trim', explode(',', $amenities)));
                        foreach ($items as $item): 
                        ?>
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                                <span class="material-symbols-outlined text-primary text-lg">check_circle</span>
                                <span><?php echo htmlspecialchars($item); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div> <!-- End of Left Volume 1 -->

        <!-- Right: Booking Card (order-2 on mobile) -->
        <div class="lg:col-span-1 order-2 lg:order-2 w-full">
            <div class="sticky top-24 bg-white border border-gray-200 rounded-lg p-6 shadow-lg">
                <!-- Price -->
                <div class="mb-4">
                    <div class="text-3xl font-bold text-primary mb-1">₹<?php echo number_format($price, 0); ?></div>
                    <div class="text-sm text-gray-600">
                        <?php 
                        if ($category === 'stays') echo 'per night';
                        elseif (in_array($category, ['cars', 'bikes'])) echo 'per day';
                        elseif ($category === 'restaurants') echo 'per person';
                        elseif ($category === 'attractions') echo 'entry fee';
                        elseif ($category === 'buses') echo 'per ticket';
                        ?>
                    </div>
                </div>

                <!-- Verified Badge -->
                <div class="mb-4 flex items-center gap-2 text-sm text-green-600">
                    <span class="material-symbols-outlined text-lg">verified</span>
                    <span class="font-medium">Verified</span>
                </div>

                <!-- Free Cancellation Notice -->
                <div class="mb-6 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800 font-medium">Free cancellation · No hidden charges</p>
                </div>

                <!-- Room Type Selection (for stays) -->
                <?php if ($category === 'stays' && count($room_types) > 0): ?>
                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-2">Select Room Type</label>
                        <select id="room-type-select" class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            <option value="">Choose a room type...</option>
                            <?php foreach ($room_types as $rt): ?>
                                <option value="<?php echo $rt['id']; ?>">
                                    <?php echo htmlspecialchars($rt['name']); ?> - ₹<?php echo number_format($rt['base_price'], 0); ?>/night
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Booking / Login Container -->
                <div id="check-availability-gate">
                    <button type="button" id="btn-check-availability" class="w-full bg-[#ec5b13] text-white font-black py-4 rounded-xl shadow-md hover:bg-orange-600 transition-all text-base">
                        Check Availability
                    </button>
                    <p class="text-center text-xs text-slate-600 font-medium mt-3">Free cancellation · No hidden charges</p>
                </div>

                <div id="booking-actions-container" class="hidden">
                    <!-- Booking Form -->
                    <?php if ($user): ?>
                        <!-- User is logged in - show booking form -->
                        <form id="booking-form" class="space-y-4">
                            <input type="hidden" name="service_type" value="<?php echo $category; ?>">
                            <input type="hidden" name="listing_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="listing_name" value="<?php echo htmlspecialchars($listing['name']); ?>">
                            
                            <div>
                                <label class="block text-sm font-bold mb-1">Full Name</label>
                                <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1">Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1">Phone</label>
                                <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-lg p-2 text-sm" placeholder="+91 XXXXX XXXXX">
                            </div>

                            <?php if ($category === 'stays'): ?>
                                <div>
                                    <label class="block text-sm font-bold mb-1">Check-in Date</label>
                                    <input type="date" name="checkin_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-1">Check-out Date</label>
                                    <input type="date" name="checkout_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                </div>
                            <?php else: ?>
                                <div>
                                    <label class="block text-sm font-bold mb-1">Booking Date</label>
                                    <input type="date" name="booking_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-bold mb-1">Number of People</label>
                                <input type="number" name="number_of_people" value="1" min="1" required class="w-full border border-gray-300 rounded-lg p-2 text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1">Notes (Optional)</label>
                                <textarea name="notes" class="w-full border border-gray-300 rounded-lg p-2 text-sm" rows="3" placeholder="Any special requests?"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-lg hover:bg-orange-600 transition">
                                Book Now
                            </button>
                        </form>
                    <?php else: ?>
                        <!-- User not logged in - show login prompt -->
                        <div class="text-center bg-amber-50 border border-amber-200 rounded-xl p-5" id="login-prompt">
                            <span class="material-symbols-outlined text-amber-500 text-3xl mb-2 block">lock</span>
                            <p class="font-bold text-slate-900 mb-1">Sign in to book</p>
                            <p class="text-sm text-slate-700 mb-4">Please log in to make a booking request.</p>
                            <a href="login" class="block w-full bg-[#ec5b13] text-white font-black py-3 rounded-xl hover:bg-orange-600 transition mb-2">
                                Sign In
                            </a>
                            <a href="register" class="block w-full bg-transparent border-2 border-primary text-primary font-bold py-2.5 rounded-xl hover:bg-orange-50 transition text-sm mb-3">
                                Create Account
                            </a>
                            <div class="relative flex py-3 items-center">
                                <div class="flex-grow border-t border-amber-200"></div>
                                <span class="flex-shrink-0 mx-4 text-amber-500 text-xs font-bold uppercase">or</span>
                                <div class="flex-grow border-t border-amber-200"></div>
                            </div>
                            <button type="button" onclick="showGuestForm()" class="block w-full bg-white border border-amber-200 text-slate-800 font-bold py-2.5 rounded-xl hover:bg-slate-50 transition text-sm">
                                Continue as Guest
                            </button>
                        </div>

                        <!-- Guest Booking Form (hidden by default) -->
                        <form id="booking-form" class="space-y-4 hidden" autocomplete="off">
                            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                                <h3 class="text-lg font-bold text-slate-800">Guest Booking</h3>
                                <button type="button" onclick="hideGuestForm()" class="text-sm text-slate-500 hover:text-primary transition font-bold flex items-center">
                                    <span class="material-symbols-outlined text-[16px] mr-1">arrow_back</span> Back
                                </button>
                            </div>
                            
                            <input type="hidden" name="service_type" value="<?php echo $category; ?>">
                            <input type="hidden" name="listing_id" value="<?php echo $id; ?>">
                            <input type="hidden" name="listing_name" value="<?php echo htmlspecialchars($listing['name']); ?>">
                            
                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Full Name</label>
                                <input type="text" name="full_name" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Email</label>
                                <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Phone</label>
                                <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" placeholder="+91 XXXXX XXXXX">
                            </div>

                            <?php if ($category === 'stays'): ?>
                                <div>
                                    <label class="block text-sm font-bold mb-1 text-slate-700">Check-in Date</label>
                                    <input type="date" name="checkin_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-1 text-slate-700">Check-out Date</label>
                                    <input type="date" name="checkout_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                                </div>
                            <?php else: ?>
                                <div>
                                    <label class="block text-sm font-bold mb-1 text-slate-700">Booking Date</label>
                                    <input type="date" name="booking_date" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                                </div>
                            <?php endif; ?>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Number of People</label>
                                <input type="number" name="number_of_people" value="1" min="1" required class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-bold mb-1 text-slate-700">Notes (Optional)</label>
                                <textarea name="notes" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition" rows="3" placeholder="Any special requests?"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-primary text-white font-bold py-3.5 rounded-xl hover:bg-orange-600 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                                Submit Guest Booking
                            </button>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Contact Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-3">Need help?</p>
                    <div class="flex gap-2">
                        <a href="tel:+918600968888" class="flex-1 flex items-center justify-center gap-1 bg-blue-50 text-blue-600 py-2 rounded-lg hover:bg-blue-100 transition text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">call</span> Call
                        </a>
                        <a href="https://wa.me/918600968888" target="_blank" class="flex-1 flex items-center justify-center gap-1 bg-green-50 text-green-600 py-2 rounded-lg hover:bg-green-100 transition text-sm font-bold">
                            <span class="material-symbols-outlined text-lg">chat</span> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Location Map (Below Booking Card) -->
            <div class="mt-6 bg-white border border-gray-200 rounded-lg p-6 shadow-lg">
                <h3 class="text-xl font-bold mb-4">Location Map</h3>
                <div class="rounded-lg overflow-hidden border border-gray-200" style="height: 350px;">
                    <?php 
                    if ($map_embed) {
                        echo $map_embed;
                    } else {
                        // Default map if none provided
                        echo '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16426.329596104577!2d75.30037121099188!3d19.851617685624074!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f39ca15447%3A0x96c2632e2aaa42c!2sKamalnayan%20Bajaj%20Hospital!5e0!3m2!1sen!2sin!4v1775290483319!5m2!1sen!2sin" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>

<script>
function showGuestForm() {
    document.getElementById('login-prompt').style.display = 'none';
    document.getElementById('booking-form').classList.remove('hidden');
}

function hideGuestForm() {
    document.getElementById('booking-form').classList.add('hidden');
    document.getElementById('login-prompt').style.display = 'block';
}

// Check Availability toggler
document.getElementById('btn-check-availability')?.addEventListener('click', function() {
    document.getElementById('check-availability-gate').style.display = 'none';
    document.getElementById('booking-actions-container').classList.remove('hidden');
});

// Booking form submission
document.getElementById('booking-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('php/api/bookings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + (localStorage.getItem('csn_user_token') || '')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success || response.ok) {
            alert('Booking request submitted successfully! We will contact you soon.');
            e.target.reset();
        } else {
            alert('Error: ' + (result.error || 'Failed to submit booking'));
        }
    } catch (error) {
        console.error('Booking error:', error);
        alert('Error submitting booking. Please try again.');
    }
});

// Mark page as ready
document.body.classList.add('page-ready');
</script>
</body>
</html>