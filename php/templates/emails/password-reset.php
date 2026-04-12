<!DOCTYPE html>
<html>
<head>
    <style>
        .email-body { font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .button { display: inline-block; padding: 12px 24px; background-color: #ec5b13; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
        .footer { font-size: 12px; color: #666; margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="email-body">
        <h2>Password Reset Request</h2>
        <p>Hello <?php echo htmlspecialchars($booking['name'] ?? 'User'); ?>,</p>
        <p>You recently requested to reset your password for your CSN Explore account. Click the button below to reset it:</p>
        <a href="<?php echo $booking['resetLink']; ?>" class="button">Reset Password</a>
        <p>If you did not request a password reset, please ignore this email or contact support if you have questions.</p>
        <p>This link will expire in 30 minutes.</p>
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> CSN Explore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
