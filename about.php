<?php
require_once 'php/config.php';
$page_title  = "About Us | CSNExplore – Chhatrapati Sambhajinagar";
$current_page = "about.php";

$page_meta = [
    'description' => 'Discover the story behind CSNExplore. Bridging the gap between ancient heritage and modern exploration in Chhatrapati Sambhajinagar (Aurangabad).',
    'canonical'   => 'https://csnexplore.com/about',
    'type'        => 'website',
    'image'       => 'https://images.unsplash.com/photo-1524492412937-b28074a5d7da?w=1600&q=80'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "AboutPage",
  "name": "About Us | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '",
  "publisher": {
    "@type": "Organization",
    "name": "CSNExplore"
  }
}
</script>';

$extra_styles = "
    .glass-panel { background:rgba(255,255,255,0.07); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12); }
";
require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative h-[480px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img alt="Bibi Ka Maqbara Heritage Site" class="w-full h-full object-cover"
             src="images/uploads/bibi.png"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-black"></div>
    </div>
    <div class="absolute top-0 left-0 right-0 z-20 pt-5">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/index.php" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold">About Us</span>
        </div>
    </div>
    <div data-reveal class="relative z-10 text-center px-5 max-w-[1140px] mx-auto w-full">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-4 py-1.5 rounded-full bg-primary/20 text-primary font-bold text-xs uppercase tracking-widest mb-6">Established 2012</span>
            <h2 class="text-5xl md:text-6xl font-serif font-black mb-6 text-white leading-tight">
                Discover the Soul of <span class="text-primary">Maharashtra</span>
            </h2>
            <p class="text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                Bridging the gap between ancient heritage and modern exploration in the heart of Chhatrapati Sambhajinagar.
            </p>
        </div>
    </div>
</section>

<!-- Boxed Content Area -->
<div class="max-w-[1140px] mx-auto px-5 py-12 space-y-20">
    <!-- Our Story -->
    <section data-reveal class="bg-white rounded-3xl overflow-hidden p-8 md:p-12 shadow-sm border border-slate-100">
        <div class="grid md:grid-cols-2 gap-16 items-center">
            <div class="space-y-6">
                <h3 class="text-3xl md:text-4xl font-serif font-bold text-slate-900">Our Story</h3>
                <div class="w-20 h-1.5 bg-primary rounded-full"></div>
                <p class="text-slate-600 text-lg leading-relaxed">
                    We began our journey with a simple goal: to showcase the architectural marvels of Ellora and Ajanta to the world. What started as a small local passion project has grown into the premier travel portal of the region.
                </p>
                <p class="text-slate-600 text-lg leading-relaxed">
                    Our deep-rooted connection to this historic land drives us to provide authentic and immersive experiences. We don't just book tours; we curate memories that last a lifetime, connecting travelers with the spiritual and cultural pulse of Chhatrapati Sambhajinagar.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-4">
                    <img alt="Ajanta Caves" class="rounded-xl h-64 w-full object-cover shadow-2xl"
                         src="https://lh3.googleusercontent.com/aida-public/AB6AXuBF_R0V30Dh4YkFUxDOjhVDzUF271-ci2ylVPn_7zievZVdJAI8RsILb_bFfZMnWH4KlNwlTT9m65v1Q7xrINmrlW2rVXxFO8-g_CfGp81zdaPYjBK0XhOd4m-lWjJMNQbxQbwYzuhVwYQvGC3w9wZ9IWgHOilmKd97q-0jw0nOoLNCx402BN4apDGh7MkA_S0rBvad2YS6_uSe96DmuELCYRpnS_vg2tuNfZV4F9gQ8h8CU_ea6ivDt_1d-9fMhVegXOpRnG9gS3vD"/>
                    <img alt="Kailasa Temple" class="rounded-xl h-48 w-full object-cover shadow-2xl"
                         src="https://lh3.googleusercontent.com/aida-public/AB6AXuBiWWm7tleVlV89m0OpAU8sE1C0LJ0Vy5o_LEMdFCHZbUZdFV7d2AKsl0pmdjpWgZm1zScw2UoxsWF3y3MUHiPhR8T31U5X7if8AVvRJvShq63hPDBpR_NmTTdEesW5ClqUPUfdrBp1gTods8Ah60teqAvNOQ5iJVaCdH_eX3up3bcKsOofnfdbLX7iA7K-_TpW1GVcjq3LkVIwLbxuK8IqQjyjLw73zvP23TCV8NNouA4CR15q6IwMk0R9avBfJ8_pMD_AYJgCdcoA"/>
                </div>
                <div class="pt-8 space-y-4">
                    <img alt="Bibi Ka Maqbara" class="rounded-xl h-48 w-full object-cover shadow-2xl"
                         src="https://lh3.googleusercontent.com/aida-public/AB6AXuBs5mILVmU084j65DcidaLjrnTIKOz3DRU0L6wGZ70yfgayEfQ8xMP8d7JfuBznWIZuVwEmcd8hF5kW3OTJ6cJCVPIK6DMfk4BFnnhfF7CshPcgDO42W9TkL5IXycpQiqaNDc6VuJMMz2VAOTJlyAdeLb0A_ct7lg64X-efrTyVXpvgJr9HyGz6D30dFcTod5kFk_sg739EDjwOqwdknDwmKL5r2-Tm2hR07PPPjMANK0jtXrdGwv83JLh8PWeI0u2_KAVgdmelIwhj"/>
                    <img alt="Aurangabad Gates" class="rounded-xl h-64 w-full object-cover shadow-2xl"
                         src="https://lh3.googleusercontent.com/aida-public/AB6AXuDMGkF-GU5txWzIac4pjFsFvkh0BsvYWpIGVpXn7URMLEZlQECUQPWhd8o5RZiQ4qfszSI1f5H3IaUAoPYR8MB8Hg_Kn4ltvxwcKRKbU09xgeEJ-ZvRJGetw-PsfSL3CXvd8pnMzLNzzqOfKkgRHBtbQd6VYy8Pv9Onoaty7w6V7GcbkdcdLfqBxAB53gerDL0Vm35WP9vhLBQiOkFN04jyPqXPwAwMSyPwRI2DWdYbtrubM7moA__q2c338atruVI-l53S36h5N6O6"/>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section>
        <div class="grid md:grid-cols-2 gap-8">
            <div data-reveal="left" class="p-10 rounded-3xl shadow-xl hover:-translate-y-1 transition-all border border-slate-100" style="background: white;">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-4xl">rocket_launch</span>
                </div>
                <h4 class="text-2xl font-bold mb-4" style="color: #0f172a;">Our Mission</h4>
                <p class="leading-relaxed text-lg" style="color: #475569;">
                    To deliver world-class tourism services that honor the sanctity and history of our heritage sites, ensuring every guest experiences the true hospitality of Maharashtra through personalized care and expert guidance.
                </p>
            </div>
            <div data-reveal="right" class="p-10 rounded-3xl shadow-xl hover:-translate-y-1 transition-all border border-slate-100" style="background: white;">
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6">
                    <span class="material-symbols-outlined text-primary text-4xl">visibility</span>
                </div>
                <h4 class="text-2xl font-bold mb-4" style="color: #0f172a;">Our Vision</h4>
                <p class="leading-relaxed text-lg" style="color: #475569;">
                    To become the global gateway to Chhatrapati Sambhajinagar's wonders, setting the benchmark for sustainable and culturally-rich travel while empowering local communities and preserving our historic legacy.
                </p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="bg-white rounded-3xl p-12 shadow-sm border border-slate-100">
        <div class="text-center mb-16">
            <h3 class="text-4xl font-serif font-bold mb-4 text-slate-900">Why Choose Us</h3>
            <p class="text-slate-500 max-w-2xl mx-auto text-lg">We pride ourselves on excellence, local expertise, and a traveler-first approach.</p>
        </div>
        <div data-reveal-stagger class="grid md:grid-cols-4 gap-6">
            <?php
            $features = [
                ['icon' => 'verified_user',  'title' => 'Local Expertise',      'desc' => 'Born and raised in the city, we know the hidden gems.'],
                ['icon' => 'support_agent',  'title' => '24/7 Support',         'desc' => 'Our team is always available to ensure smooth travels.'],
                ['icon' => 'history_edu',    'title' => 'Certified Guides',     'desc' => 'History experts with official state certifications.'],
                ['icon' => 'payments',       'title' => 'Transparent Pricing',  'desc' => 'No hidden costs. Fair value for premium experiences.'],
            ];
            foreach ($features as $f): ?>
            <div data-reveal="scale" class="p-8 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <span class="material-symbols-outlined text-5xl text-primary mb-4 block"><?php echo $f['icon']; ?></span>
                <h5 class="font-bold text-lg mb-2 text-slate-900"><?php echo $f['title']; ?></h5>
                <p class="text-sm text-slate-500"><?php echo $f['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>


    <!-- CTA -->
    <section data-reveal="scale">
        <div class="rounded-[3rem] bg-primary p-12 md:p-20 text-center relative overflow-hidden shadow-2xl">
            <div class="absolute inset-0 opacity-10">
                <img alt="Heritage Pattern" class="w-full h-full object-cover"
                     src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWlz272LF0IoJnEIdtroG6WMXcqOoHkudt7yBJ19PqufEI-7ISJ_zn1G8dnCkJGSuT6mi0qxXFqNrcEONccUuLNshgvjmSshh-Jz76I3m8QJW8nzY_MGhbCfHxiOLiE9UG83G9Efw1DjgBGtP9SOV-kceOiHL4wkeDxyVEwFUGwifUJtUlQYU2eTj5HdEm8TGa_qGjUtgI1dHODPNb15eQQRY_ZE4ATENcXErfoYbMGNR8QQw1xclUhx8RkpuhbA4h7Fv6uWkq4hil"/>
            </div>
            <div class="relative z-10">
                <h3 class="text-3xl md:text-5xl font-serif font-black text-white mb-8">Ready to explore history?</h3>
                <p class="text-white/80 text-lg mb-10 max-w-xl mx-auto">Join us for a journey through time and culture. Book your customized heritage tour today.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?php echo BASE_PATH; ?>/listing/stays" class="bg-white text-primary px-8 py-4 rounded-full font-bold text-lg shadow-xl hover:bg-slate-100 transition-colors">Start Your Journey</a>
                    <a href="<?php echo BASE_PATH; ?>/contact" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white/10 transition-colors">Contact Support</a>
                </div>
            </div>
        </div>
    </section>
</div>
</main>

<?php require 'footer.php'; ?>
