<?php
$page_title       = "Terms of Service | CSNExplore";
$current_page     = "terms.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Read CSNExplore's Terms of Service governing the use of our travel portal and booking services.",
    'canonical'   => "https://csnexplore.com/terms",
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/travelhub.png',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Terms of Service', 'url' => '/terms'],
    ],
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Terms of Service | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '"
}
</script>
<link rel="stylesheet" href="' . BASE_PATH . '/css/terms-condition.css" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />';
include 'header.php';
?>
<!-- ===== HERO ===== -->
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="hero-title">Terms & <span class="hero-accent">Conditions</span></h1>
      <p class="hero-sub">Please read these terms carefully before using WanderWheels services. By booking with us, you agree to the conditions below.</p>
      <p class="hero-date">Last Updated: June 2026</p>
      <div class="hero-btns">
        <button onclick="window.print()" class="hero-btn-primary">Download PDF</button>
        <a href="#contact" class="hero-btn-secondary">Contact Support</a>
      </div>
    </div>
  </section>

  <!-- ===== QUICK SUMMARY ===== -->
  <div class="summary-bar">
    <div class="summary-inner">
      <div class="summary-track">
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Valid ID required for booking</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Transparent pricing, no hidden charges</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✗</span><span class="summary-text">No illegal use of vehicles</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Governed by Indian law</span></div>
        <div class="summary-div"></div>
      </div>
      <div class="summary-track" aria-hidden="true">
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Valid ID required for booking</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Transparent pricing, no hidden charges</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✗</span><span class="summary-text">No illegal use of vehicles</span></div>
        <div class="summary-div"></div>
        <div class="summary-item"><span class="summary-icon">✓</span><span class="summary-text">Governed by Indian law</span></div>
        <div class="summary-div"></div>
      </div>
    </div>
  </div>

  <!-- PILL NAV — tab/mobile only -->
  <nav class="toc-pills">
    <a href="#introduction" class="toc-pill">1. Introduction</a>
    <a href="#booking-terms" class="toc-pill">2. Booking Terms</a>
    <a href="#vehicle-usage" class="toc-pill">3. Vehicle Usage</a>
    <a href="#pricing-payment" class="toc-pill">4. Pricing & Payment</a>
    <a href="#cancellation-ref" class="toc-pill">5. Cancellation</a>
    <a href="#liability" class="toc-pill">6. Liability</a>
    <a href="#user-responsibilities" class="toc-pill">7. User Responsibilities</a>
    <a href="#indemnification" class="toc-pill">8. Indemnification</a>
    <a href="#termination" class="toc-pill">9. Termination</a>
    <a href="#governing-law" class="toc-pill">10. Governing Law</a>
    <a href="#changes" class="toc-pill">11. Changes to Terms</a>
    <a href="#contact" class="toc-pill">12. Contact Us</a>
  </nav>

  <!-- ===== MAIN CONTENT ===== -->
  <div class="page-wrapper">

    <!-- ===== TABLE OF CONTENTS ===== -->
    <aside class="toc-sidebar" id="tocSidebar">
      <div class="toc-inner">
        <p class="toc-title">Table of Contents</p>
        <nav class="toc-nav">
          <a href="#introduction" class="toc-link active" data-section="introduction">1. Introduction & Acceptance</a>
          <a href="#booking-terms" class="toc-link" data-section="booking-terms">2. Booking & Reservation Terms</a>
          <a href="#vehicle-usage" class="toc-link" data-section="vehicle-usage">3. Vehicle Usage Rules</a>
          <a href="#pricing-payment" class="toc-link" data-section="pricing-payment">4. Pricing & Payment Terms</a>
          <a href="#cancellation-ref" class="toc-link" data-section="cancellation-ref">5. Cancellation & Refund</a>
          <a href="#liability" class="toc-link" data-section="liability">6. Liability & Disclaimers</a>
          <a href="#user-responsibilities" class="toc-link" data-section="user-responsibilities">7. User Responsibilities</a>
          <a href="#indemnification" class="toc-link" data-section="indemnification">8. Limitation & Indemnification</a>
          <a href="#termination" class="toc-link" data-section="termination">9. Termination of Service</a>
          <a href="#governing-law" class="toc-link" data-section="governing-law">10. Governing Law & Grievance</a>
          <a href="#changes" class="toc-link" data-section="changes">11. Changes to Terms</a>
          <a href="#contact" class="toc-link" data-section="contact">12. Contact Us</a>
        </nav>
      </div>
    </aside>

    <!-- ===== TERMS CONTENT ===== -->
    <main class="policy-content">

      <!-- 1. INTRODUCTION -->
      <section class="policy-section" id="introduction">
        <h2 class="section-title">Introduction & <span class="accent">Acceptance of Terms</span></h2>
        <p class="section-desc">These Terms & Conditions ("Terms") govern your use of the WanderWheels website and booking services, operated by WanderWheels Tours & Travels ("Company", "we", "us"). By accessing our website, making a booking, or using our services, you agree to be bound by these Terms.</p>

        <div class="text-block">
          <ul class="bullet-list">
            <li>These Terms apply to all users, including tourists, corporate clients, travel agents, and partner drivers.</li>
            <li>If you do not agree with any part of these Terms, please discontinue use of our services immediately.</li>
            <li>We may update these Terms from time to time — continued use of the website implies acceptance of the revised Terms.</li>
          </ul>
        </div>

        <div class="info-note">
          <span class="note-icon">ℹ</span>
          <p>WanderWheels operates as a vehicle rental and tour booking platform under applicable Indian laws. These Terms should be read alongside our Cancellation & Refund Policy and Privacy Policy.</p>
        </div>
      </section>

      <!-- 2. BOOKING TERMS -->
      <section class="policy-section" id="booking-terms">
        <h2 class="section-title">Booking & <span class="accent">Reservation Terms</span></h2>
        <p class="section-desc">The following conditions apply to all bookings made through our website, phone, or WhatsApp.</p>

        <div class="terms-grid">
          <div class="term-card">
            <div class="tc-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <h3 class="tc-title">Booking Confirmation</h3>
            <p class="tc-desc">A booking is confirmed only after advance or full payment is received and a confirmation message/email is sent by our team.</p>
          </div>
          <div class="term-card">
            <div class="tc-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <h3 class="tc-title">Valid Identification</h3>
            <p class="tc-desc">A valid government-issued photo ID is mandatory at the time of booking and pickup. Self-drive bookings require a valid driving license.</p>
          </div>
          <div class="term-card">
            <div class="tc-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <h3 class="tc-title">Age Requirement</h3>
            <p class="tc-desc">Customers must be 21 years or older to rent a self-drive vehicle, and 18+ for chauffeur-driven bookings, unless stated otherwise.</p>
          </div>
          <div class="term-card">
            <div class="tc-icon">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            </div>
            <h3 class="tc-title">Accurate Information</h3>
            <p class="tc-desc">You are responsible for providing accurate travel details, contact information, and passenger count at the time of booking.</p>
          </div>
        </div>
      </section>

      <!-- 3. VEHICLE USAGE -->
      <section class="policy-section" id="vehicle-usage">
        <h2 class="section-title">Vehicle <span class="accent">Usage Rules</span></h2>
        <p class="section-desc">All vehicles booked through WanderWheels must be used responsibly and within the scope of the agreed booking.</p>

        <div class="text-block">
          <ul class="bullet-list">
            <li><strong>Authorized Use Only —</strong> Vehicles may only be used for the trip purpose declared at booking. Subletting or re-renting to a third party is strictly prohibited.</li>
            <li><strong>Passenger & Luggage Limits —</strong> The number of passengers and luggage must not exceed the vehicle's stated capacity.</li>
            <li><strong>Prohibited Activities —</strong> Smoking, consumption of alcohol, and carrying illegal substances inside the vehicle are strictly prohibited.</li>
            <li><strong>Damage Charges —</strong> Any damage caused to the vehicle during the rental period will be charged to the customer as per assessed repair cost.</li>
            <li><strong>Traffic Violations —</strong> Any traffic fines, challans, or penalties incurred during self-drive bookings are the customer's sole responsibility.</li>
          </ul>
        </div>
      </section>

      <!-- 4. PRICING & PAYMENT -->
      <section class="policy-section" id="pricing-payment">
        <h2 class="section-title">Pricing & <span class="accent">Payment Terms</span></h2>
        <p class="section-desc">Our pricing is transparent and calculated based on distance, duration, and vehicle type.</p>

        <div class="refund-table-wrap">
          <table class="refund-table">
            <thead>
              <tr>
                <th>Charge Type</th>
                <th>Included</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tbody>
              <tr class="row-green">
                <td>Base Fare (per-km / per-day)</td>
                <td>Yes</td>
                <td>As per selected vehicle category</td>
              </tr>
              <tr class="row-yellow">
                <td>Driver Allowance</td>
                <td>Outstation only</td>
                <td>Applicable for trips beyond city limits</td>
              </tr>
              <tr class="row-orange">
                <td>Toll, Parking & State Tax</td>
                <td>No</td>
                <td>Billed extra as per actuals</td>
              </tr>
              <tr class="row-red">
                <td>GST</td>
                <td>No</td>
                <td>Charged additionally as per government rate</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="info-note warning">
          <span class="note-icon">⚠</span>
          <p>In case of a failed or pending online payment, the booking will not be confirmed until successful payment is verified by our team.</p>
        </div>
      </section>

      <!-- 5. CANCELLATION REFERENCE -->
      <section class="policy-section" id="cancellation-ref">
        <h2 class="section-title">Cancellation & <span class="accent">Refund</span></h2>
        <p class="section-desc">Cancellations, refund timelines, and rescheduling rules are governed by our dedicated policy page.</p>

        <div class="info-note">
          <span class="note-icon">ℹ</span>
          <p>For complete details on cancellation charges, refund timelines, and rescheduling rules, please refer to our <a href="<?php echo BASE_PATH; ?>/terms" class="inline-link">Cancellation & Refund Policy</a> page.</p>
        </div>
      </section>

      <!-- 6. LIABILITY -->
      <section class="policy-section" id="liability">
        <h2 class="section-title">Liability & <span class="accent">Disclaimers</span></h2>
        <p class="section-desc">WanderWheels strives to provide safe and reliable service, subject to the following limitations.</p>

        <div class="special-grid">
          <div class="special-col">
            <h3 class="special-col-title eligible">✓ Company Covers</h3>
            <ul class="special-list">
              <li>
                <span class="sp-icon eligible">✓</span>
                <div>
                  <strong>Vehicle Insurance</strong>
                  <p>All fleet vehicles carry valid insurance as per Indian motor vehicle laws</p>
                </div>
              </li>
              <li>
                <span class="sp-icon eligible">✓</span>
                <div>
                  <strong>Driver Verification</strong>
                  <p>All assigned drivers are background-verified and licensed</p>
                </div>
              </li>
            </ul>
          </div>
          <div class="special-col">
            <h3 class="special-col-title not-eligible">✗ Company Is Not Liable For</h3>
            <ul class="special-list">
              <li>
                <span class="sp-icon not-eligible">✗</span>
                <div>
                  <strong>Personal Belongings</strong>
                  <p>Loss, theft, or damage to personal belongings inside the vehicle</p>
                </div>
              </li>
              <li>
                <span class="sp-icon not-eligible">✗</span>
                <div>
                  <strong>Force Majeure Events</strong>
                  <p>Delays or disruptions caused by natural disasters, strikes, or government restrictions</p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </section>

      <!-- 7. USER RESPONSIBILITIES -->
      <section class="policy-section" id="user-responsibilities">
        <h2 class="section-title">User Responsibilities & <span class="accent">Prohibited Activities</span></h2>
        <p class="section-desc">As a user of our platform, you agree to the following conduct standards.</p>

        <div class="text-block">
          <ul class="bullet-list">
            <li>Do not use WanderWheels vehicles or services for any illegal, fraudulent, or unauthorized activity.</li>
            <li>Do not provide false, misleading, or incomplete information during booking or registration.</li>
            <li>Any misuse of your account, including fraudulent bookings or chargebacks, may result in account suspension and legal action.</li>
            <li>Respect drivers, staff, and fellow passengers at all times during the service.</li>
          </ul>
        </div>
      </section>

      <!-- 8. INDEMNIFICATION -->
      <section class="policy-section" id="indemnification">
        <h2 class="section-title">Limitation of Liability & <span class="accent">Indemnification</span></h2>
        <p class="section-desc">This section limits the Company's liability and outlines when the user must indemnify WanderWheels.</p>

        <div class="text-block">
          <ul class="bullet-list">
            <li>WanderWheels' total liability for any claim shall not exceed the total booking amount paid by the customer for that specific service.</li>
            <li>The Company is not liable for indirect, incidental, or consequential damages arising from use of our services.</li>
            <li>The user agrees to indemnify and hold harmless WanderWheels from any claims, damages, or legal costs arising from misuse of the vehicle, violation of these Terms, or breach of applicable law.</li>
          </ul>
        </div>
      </section>

      <!-- 9. TERMINATION -->
      <section class="policy-section" id="termination">
        <h2 class="section-title">Termination of <span class="accent">Service</span></h2>
        <p class="section-desc">WanderWheels reserves the right to suspend or terminate a booking, account, or service access under the following circumstances.</p>

        <div class="steps-wrap">
          <div class="step-item">
            <div class="step-num">01</div>
            <div class="step-body">
              <h3 class="step-title">Misuse of Service</h3>
              <p class="step-desc">Using the vehicle for illegal purposes, exceeding agreed usage terms, or violating safety guidelines.</p>
            </div>
          </div>
          <div class="step-connector"></div>
          <div class="step-item">
            <div class="step-num">02</div>
            <div class="step-body">
              <h3 class="step-title">Fraudulent Activity</h3>
              <p class="step-desc">Providing false information, fraudulent payments, or repeated chargebacks.</p>
            </div>
          </div>
          <div class="step-connector"></div>
          <div class="step-item">
            <div class="step-num">03</div>
            <div class="step-body">
              <h3 class="step-title">Breach of Terms</h3>
              <p class="step-desc">Any violation of these Terms & Conditions may result in immediate booking cancellation without refund.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 10. GOVERNING LAW -->
      <section class="policy-section" id="governing-law">
        <h2 class="section-title">Governing Law & <span class="accent">Grievance Officer</span></h2>
        <p class="section-desc">These Terms are governed by the laws of India.</p>

        <div class="text-block">
          <ul class="bullet-list">
            <li>Any disputes arising out of these Terms shall be subject to the exclusive jurisdiction of the courts located in [City, State].</li>
            <li>In accordance with the Information Technology Act, 2000 and rules made thereunder, the contact details of the Grievance Officer are provided below.</li>
          </ul>
        </div>

        <div class="contact-card" style="margin-top:20px;">
          <div class="cc-icon hours">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
          </div>
          <div>
            <p class="cc-label">Grievance Officer</p>
            <p class="cc-value">grievance@wanderwheels.in</p>
            <p class="cc-sub">Response within 7 business days</p>
          </div>
        </div>
      </section>

      <!-- 11. CHANGES TO TERMS -->
      <section class="policy-section" id="changes">
        <h2 class="section-title">Changes to <span class="accent">These Terms</span></h2>
        <p class="section-desc">We may revise these Terms periodically to reflect changes in our services, legal requirements, or business practices.</p>

        <div class="info-note">
          <span class="note-icon">ℹ</span>
          <p>Updated Terms will be posted on this page with a revised "Last Updated" date. Continued use of our website or services after changes are posted constitutes acceptance of the revised Terms.</p>
        </div>
      </section>

      <!-- 12. CONTACT -->
      <section class="policy-section" id="contact">
        <h2 class="section-title">Contact <span class="accent">Us</span></h2>
        <p class="section-desc">For any questions regarding these Terms & Conditions, reach out to our support team.</p>

        <div class="contact-grid">
          <div class="contact-info">
            <div class="contact-card">
              <div class="cc-icon phone">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.68A2 2 0 012 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
              </div>
              <div>
                <p class="cc-label">Call / WhatsApp</p>
                <p class="cc-value">+91 98765 43210</p>
                <p class="cc-sub">Available 24x7</p>
              </div>
            </div>
            <div class="contact-card">
              <div class="cc-icon email">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
              </div>
              <div>
                <p class="cc-label">Email Us</p>
                <p class="cc-value">support@wanderwheels.in</p>
                <p class="cc-sub">Reply within 2 hours</p>
              </div>
            </div>
            <div class="contact-card">
              <div class="cc-icon hours">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              </div>
              <div>
                <p class="cc-label">Support Hours</p>
                <p class="cc-value">24 Hours / 7 Days</p>
                <p class="cc-sub">Including holidays</p>
              </div>
            </div>
          </div>

          <div class="cancel-form-wrap">
            <h3 class="form-title">Have a Question About Our Terms?</h3>
            <form class="cancel-form" id="termsContactForm">
              <div class="cf-group">
                <label>Full Name *</label>
                <input type="text" placeholder="Enter your name" required />
              </div>
              <div class="cf-row">
                <div class="cf-group">
                  <label>Email *</label>
                  <input type="email" placeholder="you@example.com" required />
                </div>
                <div class="cf-group">
                  <label>Phone Number *</label>
                  <input type="tel" placeholder="+91 XXXXX XXXXX" required />
                </div>
              </div>
              <div class="cf-group">
                <label>Your Question</label>
                <textarea rows="3" placeholder="Type your question regarding our Terms & Conditions..."></textarea>
              </div>
              <button type="submit" class="cf-submit">Submit Query</button>
              <div class="cf-success" id="cfSuccess" style="display:none;">
                ✓ Your query has been submitted. Our team will respond within 2 business hours.
              </div>
            </form>
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ===== CTA ===== -->
  <section class="cta-outer">
    <div class="cta-box">
      <div class="cta-bg"></div>
      <div class="cta-overlay"></div>
      <div class="cta-content">
        <h2 class="cta-title">Ready to Book Your <span class="cta-accent">Next Trip?</span></h2>
        <p class="cta-sub">Now that you know our terms, travel with WanderWheels confidently — safe, transparent, and reliable every time.</p>
        <div class="cta-btns">
          <a href="<?php echo BASE_PATH; ?>/" class="cta-btn-primary">Book Your Trip</a>
          <a href="<?php echo BASE_PATH; ?>/contact" class="cta-btn-secondary">Talk to Us</a>
        </div>
      </div>
    </div>
  </section>

  <script src="<?php echo BASE_PATH; ?>/js/terms_condition.js"></script>

<?php include 'footer.php'; ?>

