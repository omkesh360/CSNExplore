<?php
$page_title       = "Terms of Service | CSNExplore";
$current_page     = "terms.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Read CSNExplore's Terms of Service governing the use of our travel portal and booking services.",
    'canonical'   => "https://csnexplore.com/terms",
    'type'        => 'website',
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Terms of Service | CSNExplore",
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
                <span class="material-symbols-outlined text-[16px]">gavel</span> Legal
            </span>
            <h1 class="text-4xl md:text-5xl font-serif font-black text-slate-900 mb-4">Terms of Service</h1>
            <p class="text-slate-500 text-lg">Last updated: January 1, 2025</p>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 md:p-12 space-y-10 text-slate-700 leading-relaxed">

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">info</span>
                    1. Acceptance of Terms
                </h2>
                <p>By accessing or using CSNExplore ("the Service"), you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our website or services. These terms apply to all visitors, users, and others who access or use the Service.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">description</span>
                    2. Description of Service
                </h2>
                <p>CSNExplore is a travel portal providing information and booking facilitation for:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2 mt-3">
                    <li>Hotel and accommodation stays in Chhatrapati Sambhajinagar</li>
                    <li>Car and bike rentals</li>
                    <li>Bus ticket information</li>
                    <li>Tourist attractions and heritage sites</li>
                    <li>Restaurant recommendations</li>
                    <li>Travel guides and blog content</li>
                </ul>
                <p class="mt-3">We act as an intermediary between travelers and service providers. Actual services are provided by third-party vendors.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">account_circle</span>
                    3. User Accounts
                </h2>
                <p>When you create an account, you agree to:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2 mt-3">
                    <li>Provide accurate, current, and complete information</li>
                    <li>Maintain the security of your password</li>
                    <li>Accept responsibility for all activities under your account</li>
                    <li>Notify us immediately of any unauthorized use</li>
                </ul>
                <p class="mt-3">We reserve the right to terminate accounts that violate these terms or engage in fraudulent activity.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">book_online</span>
                    4. Bookings and Payments
                </h2>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2">
                    <li>All bookings are subject to availability and confirmation by the service provider</li>
                    <li>Prices displayed are indicative and may vary at the time of booking</li>
                    <li>Booking confirmations will be sent to your registered email address</li>
                    <li>Cancellation policies vary by service provider — please review before booking</li>
                    <li>CSNExplore is not responsible for disputes between users and service providers</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">cancel</span>
                    5. Cancellation & Refund Policy
                </h2>
                <p>Cancellation and refund policies are determined by individual service providers. CSNExplore will facilitate communication between you and the provider but cannot guarantee refunds. We recommend reviewing the specific cancellation policy before confirming any booking.</p>
                <p class="mt-3">For assistance with cancellations, contact us at <a href="mailto:supportcsnexplore@gmail.com" class="text-primary font-semibold hover:underline">supportcsnexplore@gmail.com</a> or call <a href="tel:+918600968888" class="text-primary font-semibold hover:underline">+91 86009 68888</a>.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">block</span>
                    6. Prohibited Activities
                </h2>
                <p>You agree not to:</p>
                <ul class="list-disc list-inside space-y-2 text-slate-600 ml-2 mt-3">
                    <li>Use the Service for any unlawful purpose</li>
                    <li>Submit false or misleading information</li>
                    <li>Attempt to gain unauthorized access to any part of the Service</li>
                    <li>Scrape, crawl, or harvest data without permission</li>
                    <li>Transmit viruses, malware, or other harmful code</li>
                    <li>Harass, abuse, or harm other users</li>
                    <li>Impersonate any person or entity</li>
                </ul>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">copyright</span>
                    7. Intellectual Property
                </h2>
                <p>All content on CSNExplore — including text, images, logos, and software — is the property of CSNExplore or its content suppliers and is protected by applicable intellectual property laws. You may not reproduce, distribute, or create derivative works without our express written permission.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">warning</span>
                    8. Disclaimer of Warranties
                </h2>
                <p>The Service is provided "as is" and "as available" without warranties of any kind, either express or implied. We do not warrant that the Service will be uninterrupted, error-free, or free of viruses. We are not responsible for the accuracy of third-party listings or the quality of services provided by vendors.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">balance</span>
                    9. Limitation of Liability
                </h2>
                <p>To the maximum extent permitted by law, CSNExplore shall not be liable for any indirect, incidental, special, consequential, or punitive damages arising from your use of the Service, including but not limited to loss of profits, data, or goodwill.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">location_city</span>
                    10. Governing Law
                </h2>
                <p>These Terms shall be governed by and construed in accordance with the laws of India. Any disputes arising under these Terms shall be subject to the exclusive jurisdiction of the courts in Chhatrapati Sambhajinagar, Maharashtra.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">update</span>
                    11. Changes to Terms
                </h2>
                <p>We reserve the right to modify these Terms at any time. We will provide notice of significant changes by updating the date at the top of this page. Your continued use of the Service after changes constitutes acceptance of the new Terms.</p>
            </section>

            <section>
                <h2 class="text-xl font-bold text-slate-900 mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">contact_support</span>
                    12. Contact Us
                </h2>
                <p>For questions about these Terms, please contact us:</p>
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
