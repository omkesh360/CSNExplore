<?php
$page_title   = "Bus Routes from Chhatrapati Sambhajinagar | CSNExplore";
$current_page = "bus.php";
$page_meta = [
    'description' => 'Book intercity bus travel from Chhatrapati Sambhajinagar (Aurangabad). AC sleepers, Volvo coaches & MSRTC buses to Mumbai, Pune, Nashik and more.',
    'canonical'   => 'https://csnexplore.com/bus',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/travelhub.png'
];
$extra_styles = "
    .glassy { background:rgba(255,255,255,0.07); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12); }
    .hide-scrollbar::-webkit-scrollbar{display:none} .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
";
require_once 'php/config.php';
$db = getDB();
$routes = $db->fetchAll("SELECT * FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT 6");
require 'header.php';

$category_nav = [
    ['href' => BASE_PATH . '/listing/stays',       'icon' => 'bed',                'label' => 'Stays'],
    ['href' => BASE_PATH . '/listing/cars',        'icon' => 'directions_car',     'label' => 'Car Rentals'],
    ['href' => BASE_PATH . '/listing/bikes',       'icon' => 'motorcycle',         'label' => 'Bike Rentals'],
    ['href' => BASE_PATH . '/listing/attractions', 'icon' => 'confirmation_number','label' => 'Attractions'],
    ['href' => BASE_PATH . '/listing/restaurants', 'icon' => 'restaurant',         'label' => 'Restaurant'],
    ['href' => BASE_PATH . '/bus',                 'icon' => 'directions_bus',     'label' => 'Buses'],
];
?>
<nav class="bg-white border-b border-slate-100 relative top-16 z-40">
    <div class="max-w-[1140px] mx-auto px-5 overflow-x-auto hide-scrollbar">
        <div class="flex items-center gap-1 h-12">
            <?php foreach ($category_nav as $cat):
                $active = ($cat['href'] === 'bus.php');
            ?>
            <a href="<?php echo $cat['href']; ?>"
               class="flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all
                      <?php echo $active
                          ? 'bg-primary text-white shadow shadow-primary/30'
                          : 'text-slate-500 hover:text-primary hover:bg-primary/10'; ?>">
                <span class="material-symbols-outlined text-[15px]"><?php echo $cat['icon']; ?></span>
                <?php echo $cat['label']; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<main class="bg-white min-h-screen flex items-center justify-center py-16">
    <div class="max-w-2xl w-full mx-auto px-4">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8 md:p-12 flex flex-col items-center text-center">

            <!-- Bus Icon Animation -->
            <div class="w-32 h-32 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-primary" style="font-size:72px;">directions_bus</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-serif font-black text-primary mb-4">Bus Booking — On Request</h1>
            <p class="text-slate-500 text-lg mb-4 max-w-lg">
                This service is available <strong class="text-slate-700">on request only</strong>. Contact us directly to book intercity bus travel across Maharashtra and beyond.
            </p>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-4 mb-8 max-w-lg text-left w-full">
                <p class="text-amber-800 text-sm font-medium flex items-start gap-2">
                    <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">info</span>
                    <span>Bus bookings are processed manually. Call or WhatsApp us with your travel details and we'll arrange the best available option for you.</span>
                </p>
            </div>

            <!-- Popular Routes -->
            <div class="w-full mb-8">
                <h2 class="text-lg font-bold mb-4 text-left">Popular Routes</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <?php if (!empty($routes)): foreach ($routes as $route): ?>
                    <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 border border-slate-100 text-left">
                        <div>
                            <div class="flex items-center gap-2 text-sm font-bold">
                                <span><?php echo htmlspecialchars($route['from_location']); ?></span>
                                <span class="material-symbols-outlined text-primary text-base">arrow_forward</span>
                                <span><?php echo htmlspecialchars($route['to_location']); ?></span>
                            </div>
                            <div class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($route['duration']); ?> · <?php echo htmlspecialchars($route['bus_type']); ?></div>
                        </div>
                        <span class="text-primary font-black text-sm">₹<?php echo number_format($route['price']); ?></span>
                    </div>
                    <?php endforeach; else: ?>
                    <div class="col-span-2 text-center text-slate-400 py-4">Contact us for available routes.</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contact CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center mb-6">
                <a href="tel:<?php echo CONTACT_PHONE; ?>"
                   class="flex-1 flex items-center justify-center gap-2 bg-primary text-white font-bold py-3 px-6 rounded-xl hover:bg-orange-600 transition-all shadow-lg shadow-primary/20">
                    <span class="material-symbols-outlined text-[20px]">call</span>
                    Call Us Now
                </a>
                <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>?text=Hi%2C%20I%20want%20to%20book%20a%20bus%20from%20Sambhajinagar"
                   target="_blank"
                   class="flex-1 flex items-center justify-center gap-2 bg-green-500 text-white font-bold py-3 px-6 rounded-xl hover:bg-green-600 transition-all shadow-lg shadow-green-500/20">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861 4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z"/></svg>
                    WhatsApp Us
                </a>
            </div>

            <div class="flex gap-4">
                <a href="<?php echo BASE_PATH; ?>/" class="flex items-center gap-1.5 text-slate-500 hover:text-primary text-sm font-bold transition-colors">
                    <span class="material-symbols-outlined text-base">home</span> Back to Home
                </a>
                <a href="<?php echo BASE_PATH; ?>/listing/stays" class="flex items-center gap-1.5 text-slate-500 hover:text-primary text-sm font-bold transition-colors">
                    <span class="material-symbols-outlined text-base">bed</span> Explore Stays
                </a>
            </div>
        </div>
    </div>
</main>

<?php require 'footer.php'; ?>
