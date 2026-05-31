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
if (!flock($fp, LOCK_EX | LOCK_NB)) {
    echo "Worker is already running. Exiting.\n";
    exit;
}

$db = getDB();
$startTime = time();
$maxExecutionTime = 55; // Stop before the next minute cron starts

echo "Starting worker loop...\n";

while (time() - $startTime < $maxExecutionTime) {
    // Fetch one pending job, lock for update
    $job = $db->fetchOne("SELECT * FROM jobs WHERE status = 'pending' ORDER BY id ASC LIMIT 1");
    
    if (!$job) {
        sleep(2); // No jobs, sleep and check again
        continue;
    }

    // Mark as processing
    $db->update('jobs', ['status' => 'processing', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $job['id']]);
    
    echo "Processing job {$job['id']} of type {$job['type']}...\n";
    
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
        } else {
            throw new Exception("Unknown job type: {$job['type']}");
        }
        
        // Mark as completed
        $db->update('jobs', ['status' => 'completed', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $job['id']]);
        echo "Job {$job['id']} completed.\n";
        
    } catch (Exception $e) {
        error_log("Job {$job['id']} failed: " . $e->getMessage());
        // Mark as failed
        $db->update('jobs', ['status' => 'failed', 'updated_at' => date('Y-m-d H:i:s')], 'id = :id', [':id' => $job['id']]);
        echo "Job {$job['id']} failed: " . $e->getMessage() . "\n";
    }
}

echo "Worker shutting down gracefully.\n";
flock($fp, LOCK_UN);
fclose($fp);
