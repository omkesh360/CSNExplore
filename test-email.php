<?php
/**
 * SMTP Test — visit /test-email.php?secret=csnexplore_seed&to=your@email.com
 * DELETE THIS FILE after confirming email works.
 */
require_once 'php/config.php';

$secret = $_GET['secret'] ?? '';
if ($secret !== 'csnexplore_seed') { http_response_code(403); die('Forbidden'); }

$to = filter_var($_GET['to'] ?? '', FILTER_VALIDATE_EMAIL);
if (!$to) { die('<p style="color:red">Provide ?to=valid@email.com</p>'); }

require_once 'php/vendor/phpmailer/PHPMailer.php';
require_once 'php/vendor/phpmailer/SMTP.php';
require_once 'php/vendor/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$log = [];
$log[] = 'SMTP_HOST: ' . SMTP_HOST;
$log[] = 'SMTP_PORT: ' . SMTP_PORT;
$log[] = 'SMTP_USERNAME: ' . SMTP_USERNAME;
$log[] = 'SMTP_ENCRYPTION: ' . SMTP_ENCRYPTION;
$log[] = 'APP_ENV: ' . APP_ENV;
$log[] = '---';

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
    $mail->Debugoutput = function($str, $level) use (&$log) { $log[] = trim($str); };

    $mail->setFrom(SMTP_USERNAME, 'CSN Explore Test');
    $mail->addAddress($to);
    $mail->Subject = 'CSNExplore SMTP Test — ' . date('Y-m-d H:i:s');
    $mail->isHTML(true);
    $mail->Body    = '<h2 style="color:#ec5b13">✅ SMTP is working!</h2><p>This test email confirms your Gmail SMTP is configured correctly on CSNExplore.</p>';
    $mail->AltBody = 'SMTP is working! CSNExplore email test.';

    $mail->send();
    $log[] = '--- ✅ EMAIL SENT SUCCESSFULLY ---';
    $success = true;
} catch (Exception $e) {
    $log[] = '--- ❌ FAILED: ' . $e->getMessage() . ' ---';
    $success = false;
}

// Also write to log file
@file_put_contents(__DIR__ . '/logs/smtp_debug.log',
    "\n\n=== TEST " . date('Y-m-d H:i:s') . " ===\n" . implode("\n", $log) . "\n",
    FILE_APPEND | LOCK_EX
);
?>
<!DOCTYPE html>
<html>
<head><title>SMTP Test</title>
<style>body{font-family:monospace;padding:24px;background:#0f172a;color:#e2e8f0}
h2{color:<?php echo $success ? '#4ade80' : '#f87171'; ?>}
pre{background:#1e293b;padding:16px;border-radius:8px;overflow-x:auto;font-size:12px;line-height:1.6}
.ok{color:#4ade80}.err{color:#f87171}.info{color:#94a3b8}
</style></head>
<body>
<h2><?php echo $success ? '✅ Email sent to ' . htmlspecialchars($to) : '❌ Email failed'; ?></h2>
<pre><?php
foreach ($log as $line) {
    $cls = strpos($line, 'FAILED') !== false || strpos($line, 'Error') !== false ? 'err'
         : (strpos($line, 'SUCCESS') !== false || strpos($line, 'OK') !== false ? 'ok' : 'info');
    echo '<span class="' . $cls . '">' . htmlspecialchars($line) . '</span>' . "\n";
}
?></pre>
<?php if (!$success): ?>
<h3 style="color:#fbbf24">Common fixes:</h3>
<ul style="color:#cbd5e1;line-height:2">
  <li>Gmail: Enable 2FA → generate an <strong>App Password</strong> at myaccount.google.com/apppasswords</li>
  <li>Make sure SMTP_PASSWORD in .env is the 16-char App Password (no spaces)</li>
  <li>XAMPP: Enable <code>extension=openssl</code> in php.ini</li>
  <li>Try port 465 with SMTP_ENCRYPTION=ssl if 587/tls fails</li>
</ul>
<?php endif; ?>
</body></html>
