<?php
// CLI SMTP test — run: C:\xampp\php\php.exe smtp-test-cli.php
require_once __DIR__ . '/php/config.php';
require_once __DIR__ . '/php/vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/php/vendor/phpmailer/SMTP.php';
require_once __DIR__ . '/php/vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

echo "=== CSNExplore SMTP Test ===\n";
echo "SMTP_HOST:       " . SMTP_HOST . "\n";
echo "SMTP_PORT:       " . SMTP_PORT . "\n";
echo "SMTP_USERNAME:   " . SMTP_USERNAME . "\n";
echo "SMTP_PASSWORD:   " . (SMTP_PASSWORD ? str_repeat('*', strlen(SMTP_PASSWORD)) : '(EMPTY!)') . "\n";
echo "SMTP_ENCRYPTION: " . SMTP_ENCRYPTION . "\n";
echo "APP_ENV:         " . APP_ENV . "\n\n";

if (!SMTP_USERNAME || !SMTP_PASSWORD) {
    echo "ERROR: SMTP_USERNAME or SMTP_PASSWORD is empty in .env!\n";
    exit(1);
}

// Test 1: TCP connection
echo "--- Test 1: TCP connection to " . SMTP_HOST . ":" . SMTP_PORT . " ---\n";
$sock = @fsockopen(SMTP_HOST, (int)SMTP_PORT, $errno, $errstr, 10);
if ($sock) {
    echo "TCP connection: OK\n";
    fclose($sock);
} else {
    echo "TCP connection FAILED: $errstr ($errno)\n";
    echo "This means your firewall/ISP is blocking port " . SMTP_PORT . "\n";
    echo "Try: SMTP_PORT=465 and SMTP_ENCRYPTION=ssl in .env\n\n";
}

// Test 2: Send via PHPMailer
echo "\n--- Test 2: PHPMailer send ---\n";
$to = SMTP_USERNAME; // send to self

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = (int)SMTP_PORT;
    $mail->Timeout    = 30;
    $mail->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
    $mail->SMTPDebug  = 2;
    $mail->Debugoutput = function($str, $level) {
        echo "  [SMTP] " . trim($str) . "\n";
    };
    $mail->setFrom(SMTP_USERNAME, 'CSNExplore Test');
    $mail->addAddress($to);
    $mail->Subject = 'CSNExplore SMTP Test ' . date('H:i:s');
    $mail->isHTML(false);
    $mail->Body = 'SMTP is working! Sent at ' . date('Y-m-d H:i:s');
    $mail->send();
    echo "\n✅ EMAIL SENT SUCCESSFULLY to $to\n";
} catch (Exception $e) {
    echo "\n❌ FAILED: " . $e->getMessage() . "\n";
    echo "PHPMailer error: " . $mail->ErrorInfo . "\n";
}
