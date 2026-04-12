<?php
/**
 * Modern User Booking Cancelled Template
 * CSN Explore - Status Update
 */
$customer_name = htmlspecialchars($booking['full_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8');
$booking_id = htmlspecialchars($booking['id'] ?? '', ENT_QUOTES, 'UTF-8');
$first_name = explode(' ', $customer_name)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Booking Cancelled - CSN Explore</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-spacing: 0; border-collapse: collapse; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding: 40px 10px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 40px 30px; text-align: center; }
        .header-icon { background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; }
        .header p { margin: 10px 0 0; font-size: 16px; color: rgba(255,255,255,0.9); }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #111827; font-weight: 600; margin: 0 0 15px; }
        .message { font-size: 16px; color: #4b5563; line-height: 1.6; margin: 0 0 30px; }
        .timeline { background-color: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 0 12px 12px 0; margin-bottom: 30px; }
        .timeline-desc { font-size: 14px; color: #b91c1c; margin: 0; line-height: 1.5; font-weight: 500; }
        .support { text-align: center; border-top: 1px solid #e5e7eb; padding-top: 30px; }
        .support p { font-size: 15px; color: #4b5563; margin: 0 0 10px; }
        .support a { color: #ec5b13; text-decoration: none; font-weight: 600; }
        .footer { background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer-logo { font-size: 16px; font-weight: 700; color: #111827; margin: 0 0 10px; letter-spacing: -0.5px; }
        .footer-text { font-size: 13px; color: #6b7280; margin: 0 0 5px; }
        @media screen and (max-width: 600px) {
            .wrapper { padding: 20px 0 !important; }
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
                    <div class="header-icon">❌</div>
                    <h1>Booking Cancelled</h1>
                    <p>Reference #<?php echo $booking_id; ?></p>
                </td>
            </tr>
            <!-- Content -->
            <tr>
                <td class="content">
                    <p class="greeting">Hi <?php echo $first_name; ?>,</p>
                    <p class="message">We're writing to let you know that your booking request with CSN Explore unfortunately had to be <strong>cancelled</strong>.</p>
                    
                    <div class="timeline">
                        <p class="timeline-desc">This can happen due to unavailability on requested dates or incomplete details. Please reach out if you have concerns.</p>
                    </div>
                    
                    <!-- Support -->
                    <div class="support">
                        <p>We'd love to help you re-book!</p>
                        <p><a href="tel:+918600968888">📞 +91 86009 68888</a></p>
                        <p><a href="mailto:supportcsnexplore@gmail.com">✉️ supportcsnexplore@gmail.com</a></p>
                    </div>
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p class="footer-logo">CSN Explore</p>
                    <p class="footer-text">Chhatrapati Sambhajinagar, Maharashtra, India</p>
                    <p class="footer-text">© <?php echo date('Y'); ?> CSN Explore. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
