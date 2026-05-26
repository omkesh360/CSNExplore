<?php
require_once 'php/config.php';
$page_title   = "Bus Routes from Chhatrapati Sambhajinagar (Aurangabad) | CSNExplore";
$current_page = "listing.php";
$listing_type = "buses";
$page_meta = [
    'description' => 'Book intercity bus travel from Chhatrapati Sambhajinagar (Aurangabad). AC sleepers, Volvo coaches & MSRTC buses to Mumbai, Pune, Nashik and more. Please contact us to book.',
    'canonical'   => 'https://csnexplore.com/bus',
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/Logo-light-optimized.webp'
];
$extra_styles = "
    .glassy { background:rgba(255,255,255,0.07); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12); }
    .hide-scrollbar::-webkit-scrollbar{display:none} .hide-scrollbar{-ms-overflow-style:none;scrollbar-width:none}
";
$db = getDB();
$routes = $db->fetchAll("SELECT * FROM buses WHERE is_active=1 ORDER BY display_order ASC LIMIT 6");
require 'header.php';

$c = [
    'hero_bg'  => BASE_PATH . '/images/bus-hero-section%20(2).webp',
    'label'    => 'Buses',
    'icon'     => 'directions_bus',
    'hero_h1'  => 'Travel by Bus',
    'hero_sub' => 'Intercity buses on request — call or WhatsApp to book.'
];
?>

<!-- Hero Banner with breadcrumb at top -->
<div class="relative h-52 md:h-72 overflow-hidden pt-28">
    <img fetchpriority="high" loading="eager" decoding="sync" width="800" height="600" src="<?php echo htmlspecialchars($c['hero_bg']); ?>"
         alt="<?php echo htmlspecialchars($c['label']); ?>"
         class="absolute inset-0 w-full h-full object-cover"/>
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    <!-- Breadcrumb at very top -->
    <div class="absolute top-0 left-0 right-0 pt-28">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold"><?php echo htmlspecialchars($c['label']); ?></span>
        </div>
    </div>
    <!-- Title at bottom -->
    <div class="absolute bottom-0 left-0 right-0 pb-6">
        <div class="max-w-[1140px] mx-auto px-5">
            <h1 class="text-white text-2xl md:text-4xl font-serif font-black flex items-center gap-3">
                <span class="material-symbols-outlined text-primary text-3xl"><?php echo htmlspecialchars($c['icon']); ?></span>
                <?php echo htmlspecialchars($c['hero_h1']); ?>
            </h1>
            <p class="text-white/60 text-sm mt-1"><?php echo htmlspecialchars($c['hero_sub']); ?></p>
        </div>
    </div>
</div>

<main class="min-h-screen" style="background:#f8f6f6">
<div class="max-w-[1140px] mx-auto px-3 sm:px-5 py-4 sm:py-8" style="width:100%;box-sizing:border-box;overflow-x:hidden">
    <div class="max-w-2xl w-full mx-auto">
        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 p-8 md:p-12 flex flex-col items-center text-center mt-4">

            <!-- Bus Icon Animation -->
            <div class="w-32 h-32 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-primary" style="font-size:72px;">directions_bus</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-serif font-black text-primary mb-4">Bus Booking — Coming Soon</h1>
            <p class="text-slate-500 text-lg mb-4 max-w-lg">
                This service is <strong class="text-slate-700">coming soon</strong>. Please contact us by Call or WhatsApp for booking a bus.
            </p>

            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-6 py-4 mb-8 max-w-lg text-left w-full">
                <p class="text-amber-800 text-sm font-medium flex items-start gap-2">
                     <span class="material-symbols-outlined text-[18px] mt-0.5 shrink-0">info</span>
                     <span>We are currently building our online bus reservation system. In the meantime, you can book buses manually by calling or messaging us on WhatsApp.</span>
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
</div>
</main>

<?php require 'footer.php'; ?>
