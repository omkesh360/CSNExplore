<?php
$page_title       = "Privacy Policy | CSNExplore";
$current_page     = "privacy.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Read CSNExplore's Privacy Policy to understand how we collect, use, and protect your personal information.",
    'canonical'   => "https://csnexplore.com/privacy",
    'type'        => 'website',
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Privacy Policy | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '"
}
</script>';
include 'header.php';
?>

<main class="min-h-screen bg-slate-50 py-16">
    <div class="max-w-4xl mx-auto px-6">

        <!-- Hero -->
        <div class="text-center mb-12">
            <span class="inline-flex items-center gap-2 bg-primary/10 text-primary font-bold text-xs px-4 py-1.5 rounded-full mb-4">
                <span class="material-symbols-outlined text-[16px]">shield</span> Legal
            </span>
            <h1 class="text-4xl md:text-5xl font-serif font-black text-slate-900 mb-4">Privacy Policy</h1>
            <p class="text-slate-500 text-lg">Last updated: January 1, 2025</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 md:p-12 space-y-10 text-slate-700 leading-relaxed">

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">info</span>
                    1. Introduction
                </h2>
                <p>Welcome to CSNExplore ("we", "our", or "us"). We are committed to protecting your personal information and your right to privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website <strong>csnexplore.com</strong> and use our services.</p>
                <p class="mt-3">Please read this policy carefully. If you disagree with its terms, please discontinue use of our site.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">database</span>
                    2. Information We Collect
                </h2>
                <p class="mb-3">We collect information you provide directly to us, including:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2">
                    <li>Name, email address, and phone number when you register or make a booking</li>
                    <li>Payment information (processed securely — we do not store card details)</li>
                    <li>Messages you send via our contact form</li>
                    <li>Newsletter subscription email addresses</li>
                    <li>Profile information you choose to provide</li>
                </ul>
                <p class="mt-3">We also automatically collect certain information when you visit our site, such as IP address, browser type, pages visited, and time spent on pages.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">settings</span>
                    3. How We Use Your Information
                </h2>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2">
                    <li>To process and manage your bookings and reservations</li>
                    <li>To create and manage your account</li>
                    <li>To send booking confirmations and service updates</li>
                    <li>To send newsletters (only if you subscribed — you can unsubscribe anytime)</li>
                    <li>To respond to your inquiries and provide customer support</li>
                    <li>To improve our website and services</li>
                    <li>To comply with legal obligations</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">share</span>
                    4. Sharing Your Information
                </h2>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information with:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2 mt-3">
                    <li>Service providers (hotels, car/bike rental operators, bus operators) solely to fulfill your booking</li>
                    <li>Legal authorities when required by law</li>
                    <li>Business partners with your explicit consent</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">lock</span>
                    5. Data Security
                </h2>
                <p>We implement industry-standard security measures including SSL encryption, hashed passwords (bcrypt), and secure database storage. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">cookie</span>
                    6. Cookies
                </h2>
                <p>We use localStorage and session storage to maintain your login state and preferences. We do not use third-party tracking cookies. You can clear your browser storage at any time through your browser settings.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">person</span>
                    7. Your Rights
                </h2>
                <p>You have the right to:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2 mt-3">
                    <li>Access the personal data we hold about you</li>
                    <li>Request correction of inaccurate data</li>
                    <li>Request deletion of your account and data</li>
                    <li>Opt out of marketing communications at any time</li>
                    <li>Lodge a complaint with a supervisory authority</li>
                </ul>
                <p class="mt-3">To exercise these rights, contact us at <a href="mailto:supportcsnexplore@gmail.com" class="text-primary font-semibold hover:underline">supportcsnexplore@gmail.com</a>.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">child_care</span>
                    8. Children's Privacy
                </h2>
                <p>Our services are not directed to children under 13. We do not knowingly collect personal information from children. If you believe we have inadvertently collected such information, please contact us immediately.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">update</span>
                    9. Changes to This Policy
                </h2>
                <p>We may update this Privacy Policy from time to time. We will notify you of significant changes by posting the new policy on this page with an updated date. Continued use of our services after changes constitutes acceptance of the updated policy.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">contact_support</span>
                    10. Contact Us
                </h2>
                <p>If you have questions about this Privacy Policy, please contact us:</p>
                <div class="mt-4 bg-slate-50 rounded-2xl p-6 space-y-2 text-sm">
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[18px]">business</span> <strong>CSNExplore</strong></p>
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[18px]">location_on</span> Jay Tower, V896+MP9, Samadhan Colony, Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005</p>
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[18px]">mail</span> <a href="mailto:supportcsnexplore@gmail.com" class="text-primary hover:underline">supportcsnexplore@gmail.com</a></p>
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-primary text-[18px]">call</span> <a href="tel:+918600968888" class="text-primary hover:underline">+91 86009 68888</a></p>
                </div>
            </section>

        </div>

        <!-- Back link -->
        <div class="text-center mt-10">
            <a href="index" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span> Back to Home
            </a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
