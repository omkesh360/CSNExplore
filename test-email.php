<?php
/**
 * Email Configuration Test Script
 * 
 * This script tests your SMTP configuration to ensure emails can be sent.
 * Access: http://your-domain/test-email.php
 * 
 * IMPORTANT: Delete this file after testing for security!
 */

require_once 'php/config.php';
require_once 'php/services/EmailService.php';

// Security: Only allow access with a secret parameter
if (!isset($_GET['secret']) || $_GET['secret'] !== 'test123') {
    die('Access denied. Add ?secret=test123 to the URL to test.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Test - CSN Explore</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f3f4f6; }
        .container { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #111827; margin-top: 0; }
        .status { padding: 15px; border-radius: 8px; margin: 20px 0; }
        .success { background: #d1fae5; border: 2px solid #10b981; color: #065f46; }
        .error { background: #fee2e2; border: 2px solid #ef4444; color: #991b1b; }
        .info { background: #dbeafe; border: 2px solid #3b82f6; color: #1e40af; }
        .config { background: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .config-item { margin: 10px 0; font-family: monospace; }
        .config-label { font-weight: bold; color: #6b7280; }
        .config-value { color: #111827; }
        button { background: #ec5b13; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; font-weight: bold; cursor: pointer; }
        button:hover { background: #d14a0f; }
        .warning { background: #fef3c7; border: 2px solid #f59e0b; color: #92400e; padding: 15px; border-radius: 8px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Email Configuration Test</h1>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong> Delete this file after testing! It exposes your email configuration.
        </div>
        
        <div class="config">
            <h3>Current SMTP Configuration:</h3>
            <div class="config-item">
                <span class="config-label">SMTP Host:</span>
                <span class="config-value"><?php echo SMTP_HOST; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Port:</span>
                <span class="config-value"><?php echo SMTP_PORT; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Username:</span>
                <span class="config-value"><?php echo SMTP_USERNAME ?: '❌ NOT SET'; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Password:</span>
                <span class="config-value"><?php echo SMTP_PASSWORD ? '✅ SET (hidden)' : '❌ NOT SET'; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">SMTP Encryption:</span>
                <span class="config-value"><?php echo SMTP_ENCRYPTION; ?></span>
            </div>
            <div class="config-item">
                <span class="config-label">Admin Email:</span>
                <span class="config-value"><?php echo ADMIN_NOTIFICATION_EMAIL; ?></span>
            </div>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
            $testEmail = trim($_POST['test_email']);
            
            if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                echo '<div class="status error">❌ Please enter a valid email address.</div>';
            } elseif (empty(SMTP_USERNAME) || empty(SMTP_PASSWORD)) {
                echo '<div class="status error">❌ SMTP credentials not configured. Please set SMTP_USERNAME and SMTP_PASSWORD in your .env file.</div>';
            } else {
                echo '<div class="status info">📤 Sending test email to ' . htmlspecialchars($testEmail) . '...</div>';
                
                try {
                    require_once 'php/vendor/phpmailer/PHPMailer.php';
                    require_once 'php/vendor/phpmailer/SMTP.php';
                    require_once 'php/vendor/phpmailer/Exception.php';
                    
                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = SMTP_HOST;
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_USERNAME;
                    $mail->Password = SMTP_PASSWORD;
                    $mail->SMTPSecure = SMTP_ENCRYPTION;
                    $mail->Port = SMTP_PORT;
                    $mail->Timeout = 10;
                    
                    // Disable SSL verification for testing
                    $mail->SMTPOptions = array(
                        'ssl' => array(
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        )
                    );
                    
                    // Recipients
                    $fromEmail = SMTP_USERNAME ?: MAILERLITE_FROM_EMAIL;
                    $mail->setFrom($fromEmail, 'CSN Explore Test');
                    $mail->addAddress($testEmail);
                    
                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Test Email from CSN Explore';
                    $mail->Body = '
                        <div style="font-family: Arial, sans-serif; padding: 20px; background: #f3f4f6;">
                            <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; margin: 0 auto;">
                                <h1 style="color: #ec5b13;">✅ Email Test Successful!</h1>
                                <p style="font-size: 16px; color: #4b5563; line-height: 1.6;">
                                    Congratulations! Your SMTP configuration is working correctly.
                                </p>
                                <p style="font-size: 16px; color: #4b5563; line-height: 1.6;">
                                    This means your trip planner emails will be sent successfully.
                                </p>
                                <div style="background: #f9fafb; padding: 15px; border-radius: 8px; margin: 20px 0;">
                                    <p style="margin: 0; font-size: 14px; color: #6b7280;">
                                        <strong>SMTP Host:</strong> ' . SMTP_HOST . '<br>
                                        <strong>SMTP Port:</strong> ' . SMTP_PORT . '<br>
                                        <strong>Encryption:</strong> ' . SMTP_ENCRYPTION . '
                                    </p>
                                </div>
                                <p style="font-size: 14px; color: #9ca3af;">
                                    CSN Explore - Email Configuration Test
                                </p>
                            </div>
                        </div>
                    ';
                    $mail->AltBody = 'Email test successful! Your SMTP configuration is working correctly.';
                    
                    $mail->send();
                    
                    echo '<div class="status success">✅ <strong>Success!</strong> Test email sent to ' . htmlspecialchars($testEmail) . '. Check your inbox (and spam folder).</div>';
                    echo '<div class="status info">🎉 Your SMTP configuration is working! Trip planner emails will be sent successfully.</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="status error">❌ <strong>Failed to send email.</strong><br><br>';
                    echo '<strong>Error:</strong> ' . htmlspecialchars($mail->ErrorInfo ?? $e->getMessage()) . '<br><br>';
                    echo '<strong>Common Solutions:</strong><br>';
                    echo '• If using Gmail, make sure you\'re using an App Password (not your regular password)<br>';
                    echo '• Check that 2-Step Verification is enabled in your Google Account<br>';
                    echo '• Verify SMTP credentials in your .env file<br>';
                    echo '• Check if your hosting provider blocks outgoing SMTP connections<br>';
                    echo '• Try using port 465 with SSL instead of 587 with TLS</div>';
                }
            }
        }
        ?>

        <form method="POST" style="margin-top: 30px;">
            <h3>Send Test Email:</h3>
            <input type="email" name="test_email" placeholder="your-email@example.com" required 
                   style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 16px; margin-bottom: 15px;">
            <button type="submit">📧 Send Test Email</button>
        </form>

        <div class="info" style="margin-top: 30px;">
            <strong>📚 Next Steps:</strong><br>
            1. Enter your email address above and click "Send Test Email"<br>
            2. Check your inbox (and spam folder) for the test email<br>
            3. If successful, your trip planner emails will work!<br>
            4. <strong>Delete this file (test-email.php) after testing</strong>
        </div>
    </div>
</body>
</html>
