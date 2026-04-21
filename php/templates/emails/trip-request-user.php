<?php
/**
 * Trip Request User Confirmation Email Template
 * CSN Explore - Trip Planning Request Received
 */

// Sanitize data
$customer_name = htmlspecialchars($booking['full_name'] ?? 'Valued Customer', ENT_QUOTES, 'UTF-8');
$trip_id = htmlspecialchars($booking['id'] ?? '', ENT_QUOTES, 'UTF-8');
$interests = htmlspecialchars($booking['interests'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$stay_type = htmlspecialchars($booking['stay_type'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$travel_mode = htmlspecialchars($booking['travel_mode'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($booking['phone'] ?? '', ENT_QUOTES, 'UTF-8');
$first_name = explode(' ', $customer_name)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Trip Request Received - CSN Explore</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-spacing: 0; border-collapse: collapse; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding: 40px 10px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #ec5b13 0%, #ff8e5e 100%); padding: 40px 30px; text-align: center; }
        .header-icon { background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; }
        .header p { margin: 10px 0 0; font-size: 16px; color: rgba(255,255,255,0.9); }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #111827; font-weight: 600; margin: 0 0 15px; }
        .message { font-size: 16px; color: #4b5563; line-height: 1.6; margin: 0 0 30px; }
        .card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; margin-bottom: 30px; }
        .card-title { font-size: 14px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px; }
        .detail-row { width: 100%; margin-bottom: 15px; }
        .detail-label { font-size: 15px; color: #6b7280; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }
        .detail-value { font-size: 15px; color: #111827; font-weight: 600; text-align: right; padding-bottom: 12px; border-bottom: 1px solid #f3f4f6; }
        .timeline { background-color: #fffaf7; border-left: 4px solid #ec5b13; padding: 20px; border-radius: 0 12px 12px 0; margin-bottom: 30px; }
        .timeline-item { margin-bottom: 15px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-title { font-size: 15px; font-weight: 700; color: #111827; margin: 0 0 5px; }
        .timeline-desc { font-size: 14px; color: #6b7280; margin: 0; line-height: 1.5; }
        .highlight-box { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 30px; }
        .highlight-box h2 { margin: 0 0 10px; font-size: 20px; font-weight: 700; }
        .highlight-box p { margin: 0; font-size: 15px; opacity: 0.95; }
        .support { text-align: center; border-top: 1px solid #e5e7eb; padding-top: 30px; }
        .support p { font-size: 15px; color: #4b5563; margin: 0 0 10px; }
        .support a { color: #ec5b13; text-decoration: none; font-weight: 600; }
        .footer { background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer-logo { font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 10px; letter-spacing: -0.5px; }
        .footer-text { font-size: 13px; color: #6b7280; margin: 0 0 5px; }
        @media screen and (max-width: 600px) {
            .wrapper { padding: 20px 0 !important; }
            .main { border-radius: 0 !important; }
            .header { padding: 30px 20px !important; }
            .content { padding: 30px 20px !important; }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <div class="header-icon">✈️</div>
                    <h1>Trip Request Received!</h1>
                    <p>Request #<?php echo $trip_id; ?></p>
                </td>
            </tr>
            <!-- Content -->
            <tr>
                <td class="content">
                    <p class="greeting">Hi <?php echo $first_name; ?>,</p>
                    <p class="message">Thank you for choosing CSN Explore for your trip planning! We've received your personalized trip request and our local experts are excited to help you create an unforgettable experience.</p>
                    
                    <!-- Highlight Box -->
                    <div class="highlight-box">
                        <h2>🎉 We'll Contact You Soon!</h2>
                        <p>Our trip experts will reach out within 2 hours with a customized itinerary</p>
                    </div>
                    
                    <!-- Details Card -->
                    <div class="card">
                        <p class="card-title">Your Trip Preferences</p>
                        <table class="detail-row">
                            <tr>
                                <td class="detail-label">Interests</td>
                                <td class="detail-value"><?php echo $interests; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Stay Type</td>
                                <td class="detail-value"><?php echo $stay_type; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label" style="border:none;padding-bottom:0;">Travel Mode</td>
                                <td class="detail-value" style="border:none;padding-bottom:0;"><?php echo $travel_mode; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Timeline -->
                    <div class="timeline">
                        <div class="timeline-item">
                            <p class="timeline-title">1. Expert Review (Within 2 Hours)</p>
                            <p class="timeline-desc">Our local trip experts will review your preferences and craft a personalized itinerary.</p>
                        </div>
                        <div class="timeline-item">
                            <p class="timeline-title">2. Personal Contact</p>
                            <p class="timeline-desc">We'll call or WhatsApp you at <?php echo $phone; ?> to discuss your trip details.</p>
                        </div>
                        <div class="timeline-item">
                            <p class="timeline-title">3. Customized Plan</p>
                            <p class="timeline-desc">Receive a complete itinerary including hotels, cars, guides, and local recommendations—all at no extra cost!</p>
                        </div>
                    </div>
                    
                    <!-- Support -->
                    <div class="support">
                        <p>Have questions? We're here to help!</p>
                        <p><a href="tel:+918600968888">📞 +91 86009 68888</a></p>
                        <p><a href="mailto:supportcsnexplore@gmail.com">✉️ supportcsnexplore@gmail.com</a></p>
                    </div>
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p class="footer-logo">CSN Explore</p>
                    <p class="footer-text">Your Local Travel Experts in Chhatrapati Sambhajinagar</p>
                    <p class="footer-text">© <?php echo date('Y'); ?> CSN Explore. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
