<?php
/**
 * Trip Request Admin Notification Email Template
 * CSN Explore - New Trip Planning Request
 */

// Sanitize data
$customer_name = htmlspecialchars($booking['full_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8');
$trip_id = htmlspecialchars($booking['id'] ?? '', ENT_QUOTES, 'UTF-8');
$email = htmlspecialchars($booking['email'] ?? 'Not provided', ENT_QUOTES, 'UTF-8');
$phone = htmlspecialchars($booking['phone'] ?? 'Not provided', ENT_QUOTES, 'UTF-8');
$interests = htmlspecialchars($booking['interests'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$stay_type = htmlspecialchars($booking['stay_type'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$travel_mode = htmlspecialchars($booking['travel_mode'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$travel_details = htmlspecialchars($booking['travel_details'] ?? 'Not specified', ENT_QUOTES, 'UTF-8');
$extra_notes = htmlspecialchars($booking['extra_notes'] ?? 'None', ENT_QUOTES, 'UTF-8');
$created_at = !empty($booking['created_at']) ? htmlspecialchars($booking['created_at'], ENT_QUOTES, 'UTF-8') : date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New Trip Request - CSN Explore Admin</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-spacing: 0; border-collapse: collapse; }
        .wrapper { width: 100%; table-layout: fixed; background-color: #f3f4f6; padding: 40px 10px; }
        .main { background-color: #ffffff; margin: 0 auto; width: 100%; max-width: 600px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); padding: 40px 30px; text-align: center; }
        .header-icon { background: rgba(255,255,255,0.2); width: 64px; height: 64px; border-radius: 50%; display: inline-block; line-height: 64px; font-size: 32px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 700; color: #ffffff; letter-spacing: -0.5px; }
        .header p { margin: 10px 0 0; font-size: 16px; color: rgba(255,255,255,0.9); }
        .content { padding: 40px 30px; }
        .alert { background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 0 12px 12px 0; margin-bottom: 30px; }
        .alert-title { font-size: 16px; font-weight: 700; color: #92400e; margin: 0 0 8px; }
        .alert-text { font-size: 14px; color: #78350f; margin: 0; line-height: 1.5; }
        .card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 25px; margin-bottom: 20px; }
        .card-title { font-size: 14px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px; }
        .detail-row { width: 100%; margin-bottom: 15px; }
        .detail-label { font-size: 15px; color: #6b7280; font-weight: 600; padding-bottom: 8px; }
        .detail-value { font-size: 15px; color: #111827; padding-bottom: 8px; line-height: 1.5; }
        .action-buttons { text-align: center; margin: 30px 0; }
        .btn { display: inline-block; padding: 14px 28px; margin: 0 8px; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: 15px; }
        .btn-primary { background-color: #10b981; color: #ffffff; }
        .btn-secondary { background-color: #3b82f6; color: #ffffff; }
        .footer { background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb; }
        .footer-text { font-size: 13px; color: #6b7280; margin: 0 0 5px; }
        @media screen and (max-width: 600px) {
            .wrapper { padding: 20px 0 !important; }
            .main { border-radius: 0 !important; }
            .header { padding: 30px 20px !important; }
            .content { padding: 30px 20px !important; }
            .btn { display: block; margin: 10px 0 !important; }
        }
    </style>
</head>
<body>
    <center class="wrapper">
        <table class="main" width="100%">
            <!-- Header -->
            <tr>
                <td class="header">
                    <div class="header-icon">🔔</div>
                    <h1>New Trip Request</h1>
                    <p>Request #<?php echo $trip_id; ?></p>
                </td>
            </tr>
            <!-- Content -->
            <tr>
                <td class="content">
                    <!-- Alert -->
                    <div class="alert">
                        <p class="alert-title">⏰ Action Required</p>
                        <p class="alert-text">A new trip planning request has been submitted. Please contact the customer within 2 hours.</p>
                    </div>
                    
                    <!-- Customer Info -->
                    <div class="card">
                        <p class="card-title">Customer Information</p>
                        <table class="detail-row">
                            <tr>
                                <td class="detail-label">Name:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $customer_name; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Email:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><a href="mailto:<?php echo $email; ?>" style="color: #ec5b13; text-decoration: none;"><?php echo $email; ?></a></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Phone:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><a href="tel:<?php echo $phone; ?>" style="color: #ec5b13; text-decoration: none;"><?php echo $phone; ?></a></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Submitted:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $created_at; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Trip Preferences -->
                    <div class="card">
                        <p class="card-title">Trip Preferences</p>
                        <table class="detail-row">
                            <tr>
                                <td class="detail-label">Interests:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $interests; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Stay Type:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $stay_type; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Travel Mode:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $travel_mode; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Travel Details:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $travel_details; ?></td>
                            </tr>
                            <tr>
                                <td class="detail-label">Special Requests:</td>
                            </tr>
                            <tr>
                                <td class="detail-value"><?php echo $extra_notes; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <a href="tel:<?php echo $phone; ?>" class="btn btn-primary">📞 Call Customer</a>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $phone); ?>" class="btn btn-secondary">💬 WhatsApp</a>
                    </div>
                </td>
            </tr>
            <!-- Footer -->
            <tr>
                <td class="footer">
                    <p class="footer-text">CSN Explore Admin Panel</p>
                    <p class="footer-text">© <?php echo date('Y'); ?> CSN Explore. All rights reserved.</p>
                </td>
            </tr>
        </table>
    </center>
</body>
</html>
