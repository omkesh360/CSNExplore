<?php
/**
 * CSNExplore Background Worker
 * Processes asynchronous jobs (e.g., sending emails).
 * Run via cron every minute: * * * * * php /path/to/csnexplore/php/cron/worker.php
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../services/EmailService.php';

$lockFile = __DIR__ . '/worker.lock';
$fp = fopen($lockFile, 'c');
if (!$fp || !flock($fp, LOCK_EX | LOCK_NB)) {
    // Already running, exit quietly without echoing to prevent cron noise
    exit;
}

$db = getDB();
$startTime = time();
$maxExecutionTime = 55; // Safety margin to stop before next cron starts

while (time() - $startTime < $maxExecutionTime) {
    // Fetch one pending job
    $job = $db->fetchOne("SELECT * FROM jobs WHERE status = 'pending' ORDER BY id ASC LIMIT 1");
    
    if (!$job) {
        // No more pending jobs, exit worker immediately (P6: run once and exit pattern)
        break;
    }

    // Mark as processing and increment attempts count
    $attempts = (int)($job['attempts'] ?? 0) + 1;
    $db->update('jobs', [
        'status' => 'processing', 
        'attempts' => $attempts,
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = :id', [':id' => $job['id']]);
    
    try {
        $payload = json_decode($job['payload'], true);
        
        if ($job['type'] === 'booking_email') {
            $bookingId = $payload['booking_id'] ?? 0;
            if ($bookingId) {
                EmailService::sendBookingEmails($bookingId);
            }
        } elseif ($job['type'] === 'status_update_email') {
            $bookingId = $payload['booking_id'] ?? 0;
            $status = $payload['status'] ?? '';
            if ($bookingId && $status) {
                EmailService::sendStatusUpdateEmail($bookingId, $status);
            }
        } elseif ($job['type'] === 'auth_email_verification') {
            $email = $payload['email'] ?? '';
            $name = $payload['name'] ?? '';
            $verifyLink = $payload['verify_link'] ?? '';
            if ($email && $name && $verifyLink) {
                EmailService::sendVerificationEmail($email, $name, $verifyLink);
            }
        } elseif ($job['type'] === 'auth_email_password_reset') {
            $email = $payload['email'] ?? '';
            $name = $payload['name'] ?? '';
            $resetLink = $payload['reset_link'] ?? '';
            if ($email && $name && $resetLink) {
                EmailService::sendPasswordResetEmail($email, $name, $resetLink);
            }
        } else {
            throw new Exception("Unknown job type: {$job['type']}");
        }
        
        // Mark as completed
        $db->update('jobs', ['status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $job['id']]);
        
    } catch (Exception $e) {
        $errMessage = $e->getMessage();
        error_log("Job {$job['id']} failed (Attempt $attempts): " . $errMessage);
        
        if ($attempts < 3) {
            // Retry later: set status back to pending, delay will occur naturally next run
            $db->update('jobs', [
                'status' => 'pending', 
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', [':id' => $job['id']]);
        } else {
            // Permanently failed
            $db->update('jobs', [
                'status' => 'failed', 
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = :id', [':id' => $job['id']]);
        }
    }
}

flock($fp, LOCK_UN);
fclose($fp);
@unlink($lockFile); // Clean up lock file on normal exit
