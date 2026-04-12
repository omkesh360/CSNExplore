<?php
require_once 'php/config.php';
$page_title   = "Contact Us | CSNExplore – Chhatrapati Sambhajinagar";
$current_page = "contact.php";

$page_meta = [
    'description' => 'Get in touch with CSNExplore. We are here to help you plan your perfect Chhatrapati Sambhajinagar exploration with 24/7 support.',
    'canonical'   => 'https://csnexplore.com/contact',
    'type'        => 'website',
    'image'       => 'images/uploads/ellora.png'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "name": "Contact Us | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '",
  "mainEntity": {
    "@type": "Organization",
    "name": "CSNExplore",
    "telephone": "+91-8600968888",
    "email": "supportcsnexplore@gmail.com",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Jay Tower, V896+MP9, Samadhan Colony, Padampura",
      "addressLocality": "Chhatrapati Sambhajinagar",
      "addressRegion": "Maharashtra",
      "postalCode": "431005",
      "addressCountry": "IN"
    }
  }
}
</script>';

$extra_styles = "
    .glass-effect { background:#fff; border:1px solid rgba(0,0,0,0.07); box-shadow:0 2px 16px rgba(0,0,0,0.06); }
";

// Handle form submission
$success = false;
$error   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first   = trim($_POST['first_name'] ?? '');
    $last    = trim($_POST['last_name']  ?? '');
    $email   = trim($_POST['email']      ?? '');
    $interest= trim($_POST['interest']   ?? '');
    $message = trim($_POST['message']    ?? '');
    if ($first && $email && $message) {
        try {
            require_once __DIR__ . '/php/config.php';
            $db = getDB();
            $db->insert('contact_messages', [
                'first_name' => htmlspecialchars($first),
                'last_name'  => htmlspecialchars($last),
                'email'      => htmlspecialchars($email),
                'interest'   => htmlspecialchars($interest),
                'message'    => htmlspecialchars($message),
            ]);
            $success = true;
        } catch (Exception $e) {
            $error = 'Something went wrong. Please try again.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
require 'header.php';
?>

<main>
<!-- Hero -->
<section class="relative h-[420px] flex items-center justify-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img alt="Ellora Caves Heritage Site" class="w-full h-full object-cover"
             src="images/uploads/ellora.png"/>
        <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/50 to-[#0a0705]"></div>
    </div>
    <!-- Breadcrumb at very top of hero -->
    <div class="absolute top-0 left-0 right-0 z-20 pt-5">
        <div class="max-w-[1140px] mx-auto px-5 flex items-center gap-2 text-sm text-white/60 flex-wrap">
            <a href="<?php echo BASE_PATH; ?>/index.php" class="hover:text-white transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-base">home</span>Home
            </a>
            <span class="material-symbols-outlined text-base">chevron_right</span>
            <span class="text-white font-semibold">Contact Us</span>
        </div>
    </div>
    <div data-reveal class="relative z-10 text-center px-5 max-w-[1140px] mx-auto w-full">
        <div class="max-w-4xl mx-auto">
            <span class="inline-block px-4 py-1.5 rounded-full bg-primary/20 text-primary font-bold text-xs uppercase tracking-widest mb-6">Got Questions?</span>
            <h1 class="text-5xl md:text-6xl font-serif font-black text-white mb-6 leading-tight">We're Here to <span class="text-primary">Help</span></h1>
            <p class="text-lg text-white/80 max-w-2xl mx-auto leading-relaxed">
                Our travel experts are available to help you plan the perfect Sambhajinagar experience. Reach out anytime.
            </p>
        </div>
    </div>
</section>

<!-- Boxed Content Area -->
<div class="max-w-[1140px] mx-auto px-5 py-12 space-y-12">
    <!-- Contact Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Info Cards -->
        <div class="space-y-6">
            <div data-reveal="left" class="p-8 rounded-3xl shadow-xl border border-slate-100" style="background: white;">
                <!-- Call -->
                <div class="flex items-start gap-4 mb-8">
                    <div class="bg-primary/10 p-3 rounded-2xl shrink-0">
                        <span class="material-symbols-outlined text-primary text-3xl">call</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0f172a;">Call Us</h3>
                        <p class="text-sm mb-2" style="color: #64748b;">Mon–Sat, 9am – 7pm IST</p>
                        <a class="text-xl font-bold text-primary hover:underline" href="tel:+918600968888">+91 86009 68888</a>
                    </div>
                </div>
                
                <div class="border-t border-slate-100 pt-8"></div>

                <!-- WhatsApp -->
                <div class="flex items-start gap-4">
                    <div class="bg-green-500/10 p-3 rounded-2xl shrink-0">
                        <span class="material-symbols-outlined text-green-600 text-3xl">chat</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0f172a;">WhatsApp</h3>
                        <p class="text-sm mb-3" style="color: #64748b;">Instant chat with our team</p>
                        <a href="https://wa.me/918600968888"
                           class="inline-block bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-green-700 transition-colors shadow-lg shadow-green-500/20">
                            Start Chat
                        </a>
                    </div>
                </div>
            </div>

            <div data-reveal="left" data-delay="1" class="p-8 rounded-3xl shadow-xl border border-slate-100" style="background: white;">
                <div class="flex items-start gap-4">
                    <div class="bg-primary/10 p-3 rounded-2xl shrink-0">
                        <span class="material-symbols-outlined text-primary text-3xl">mail</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0f172a;">Email Us</h3>
                        <p class="text-sm mb-2" style="color: #64748b;">We reply within 2 hours</p>
                        <a href="mailto:supportcsnexplore@gmail.com" class="text-primary font-bold text-sm hover:underline">
                            supportcsnexplore@gmail.com
                        </a>
                    </div>
                </div>
            </div>

            <div data-reveal="left" data-delay="2" class="p-8 rounded-3xl shadow-xl border border-slate-100" style="background: white;">
                <div class="flex items-start gap-4">
                    <div class="bg-primary/10 p-3 rounded-2xl shrink-0">
                        <span class="material-symbols-outlined text-primary text-3xl">location_on</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold mb-2" style="color: #0f172a;">Our Office</h3>
                        <p class="text-sm leading-relaxed" style="color: #64748b;">
                            Jay Tower, V896+MP9,<br/>
                            Samadhan Colony, Padampura,<br/>
                            Chhatrapati Sambhajinagar,<br/>
                            Maharashtra 431005
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div data-reveal="right" class="lg:col-span-2">
            <div class="p-8 md:p-12 rounded-3xl shadow-2xl border border-slate-100" style="background: white; height: 100%;">
                <div class="mb-8">
                    <h2 class="text-3xl font-serif font-black mb-2" style="color: #0f172a;">Send us a Message</h2>
                    <p style="color: #64748b;">Fill out the form and we'll get back to you within 2 hours.</p>
                </div>

                <?php if ($success): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    Message sent! We'll be in touch shortly.
                </div>
                <?php elseif ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                    <span class="material-symbols-outlined">error</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="contact" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">First Name *</label>
                            <input name="first_name" type="text" required placeholder="Rahul"
                                   value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                   class="w-full rounded-2xl px-5 py-4 bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 text-sm"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Last Name</label>
                            <input name="last_name" type="text" placeholder="Sharma"
                                   value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                   class="w-full rounded-2xl px-5 py-4 bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 text-sm"/>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Email Address *</label>
                        <input name="email" type="email" required placeholder="rahul@example.com"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                               class="w-full rounded-2xl px-5 py-4 bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 text-sm"/>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Travel Interest</label>
                        <select name="interest" class="w-full rounded-2xl px-5 py-4 bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-primary outline-none transition-all appearance-none cursor-pointer text-sm">
                            <option>Heritage Tour (Ellora / Ajanta)</option>
                            <option>Hotel Booking</option>
                            <option>Car Rental</option>
                            <option>Bike Rental</option>
                            <option>Bus Ticket</option>
                            <option>Restaurant Reservation</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Your Message *</label>
                        <textarea name="message" rows="5" required placeholder="Tell us how we can help..."
                                  class="w-full rounded-2xl px-5 py-4 bg-slate-50 border border-slate-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400 text-sm resize-none"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit"
                            class="w-full md:w-auto min-w-[200px] bg-primary text-white font-black py-4 px-10 rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-primary/30 uppercase tracking-widest flex items-center justify-center gap-2">
                        Send Message
                        <span class="material-symbols-outlined">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div data-reveal="scale" class="rounded-3xl overflow-hidden shadow-2xl h-[400px] relative">
        <iframe
            src="
https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3752.0!2d75.3433!3d19.8762!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bdb98f0a8a8a8a8%3A0x0!2sSamarth+Nagar%2C+Chhatrapati+Sambhajinagar!5e0!3m2!1sen!2sin!4v1"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade" title="CSNExplore Office Location">
        </iframe>
        <div class="absolute bottom-6 left-6 z-20 bg-white p-4 rounded-2xl shadow-lg border border-slate-100">
            <p class="font-bold text-slate-900 text-sm">CSNExplore Office</p>
            <p class="text-xs text-slate-500">Samadhan Colony, Padampura</p>
        </div>
    </div>
</div>
</main>

<?php require 'footer.php'; ?>
