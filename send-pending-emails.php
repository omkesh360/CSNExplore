<?php
/**
 * Background Email Sender
 * This can be run via cron job to send pending emails
 * Or called via AJAX after form submission
 */

require_once 'php/config.php';
require_once 'php/services/EmailService.php';

// Only allow POST requests or CLI
if (php_sapi_name() !== 'cli' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Get trip request ID
$tripRequestId = isset($_POST['id']) ? (int)$_POST['id'] : (isset($argv[1]) ? (int)$argv[1] : 0);

if ($tripRequestId <= 0) {
    error_log('Invalid trip request ID for email sending');
    exit;
}

try {
    $result = EmailService::sendTripRequestEmails($tripRequestId);
    error_log("Trip request #{$tripRequestId} emails sent: " . json_encode($result));
} catch (Exception $e) {
    error_log("Failed to send emails for trip request #{$tripRequestId}: " . $e->getMessage());
}
?>
