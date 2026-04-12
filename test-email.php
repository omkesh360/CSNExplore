<?php
// Quick SMTP test — DELETE THIS FILE after testing
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/php/vendor/phpmailer/SMTP.php';
require_once __DIR__ . '/php/vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$to = $_GET['to'] ?? 'test@example.com';

echo "<h2>SMTP Config Check</h2>";
echo "HOST: " . SMTP_HOST . "<br>";
echo "PORT: " . SMTP_PORT . "<br>";
echo "USER: " . SMTP_USERNAME . "<br>";
echo "PASS: " . (SMTP_PASSWORD ? str_repeat('*', strlen(SMTP_PASSWORD)) : '<b style=color:red>EMPTY — THIS IS THE PROBLEM</b>') . "<br>";
echo "FROM: " . MAILERLITE_FROM_EMAIL . "<br><br>";

if (!SMTP_PASSWORD || SMTP_PASSWORD === 'PASTE_YOUR_16_CHAR_APP_PASSWORD_HERE') {
    die("<b style='color:red'>❌ SMTP_PASSWORD is not set in .env — emails cannot send until you add your Gmail App Password.</b>");
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->Port       = SMTP_PORT;
    $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
    $mail->setFrom(SMTP_USERNAME, MAILERLITE_FROM_NAME);
    $mail->addAddress($to);
    $mail->isHTML(true);
    $mail->Subject = 'CSNExplore SMTP Test';
    $mail->Body    = '<h2>✅ Email is working!</h2><p>SMTP is configured correctly on CSNExplore.</p>';
    $mail->send();
    echo "<b style='color:green'>✅ Email sent successfully to $to</b>";
} catch (Exception $e) {
    echo "<b style='color:red'>❌ Failed: " . htmlspecialchars($e->getMessage()) . "</b>";
    echo "<br><br>PHPMailer error: " . htmlspecialchars($mail->ErrorInfo);
}
