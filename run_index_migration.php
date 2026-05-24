<?php
/**
 * CSNExplore – Database Index Migration
 * Run this once to add performance indexes to all listing tables.
 *
 * Access: http://localhost/CSNExplore/php/run_index_migration.php
 * (Protected by admin check; DELETE THIS FILE after running)
 */
declare(strict_types=1);

// Basic protection — only run from CLI or admin session
if (PHP_SAPI !== 'cli') {
    // Check for admin token in header (same as API endpoints)
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['HTTP_X_ADMIN_KEY'] ?? '';
    $adminKey   = getenv('ADMIN_MIGRATION_KEY') ?: 'csn-migrate-2025';
    if ($authHeader !== 'Bearer ' . $adminKey) {
        http_response_code(403);
        exit('Forbidden. Set Authorization: Bearer <ADMIN_MIGRATION_KEY> header.');
    }
}

require_once __DIR__ . '/php/config.php';
$db  = getDB()->getConnection();
$log = [];

function safeExec(PDO $pdo, string $sql, string $desc): string {
    try {
        $pdo->exec($sql);
        return "✅ $desc";
    } catch (PDOException $e) {
        // "Duplicate key name" is expected when index already exists
        if (str_contains($e->getMessage(), 'Duplicate key name') || 
            str_contains($e->getMessage(), 'already exists')) {
            return "⏭  $desc (already exists)";
        }
        return "❌ $desc — " . $e->getMessage();
    }
}

// ── Listing tables: composite index on is_active + display_order + rating ──
// These are the columns used in every homepage and listing page query
$listingTables = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];
foreach ($listingTables as $t) {
    $log[] = safeExec($db,
        "ALTER TABLE `$t` ADD INDEX `idx_active_order_rating` (`is_active`, `display_order` ASC, `rating` DESC)",
        "$t: composite(is_active, display_order, rating)"
    );
    $log[] = safeExec($db,
        "ALTER TABLE `$t` ADD INDEX `idx_slug` (`slug`(191))",
        "$t: slug index"
    );
}

// ── Users table ─────────────────────────────────────────────────────────────
$log[] = safeExec($db,
    "ALTER TABLE `users` ADD INDEX `idx_email` (`email`)",
    "users: email index"
);
$log[] = safeExec($db,
    "ALTER TABLE `users` ADD INDEX `idx_username` (`username`(100))",
    "users: username index (if column exists)"
);
$log[] = safeExec($db,
    "ALTER TABLE `users` ADD INDEX `idx_role_verified` (`role`, `is_verified`)",
    "users: role+is_verified composite index"
);

// ── Blogs table ─────────────────────────────────────────────────────────────
$log[] = safeExec($db,
    "ALTER TABLE `blogs` ADD INDEX `idx_status_created` (`status`, `created_at` DESC)",
    "blogs: status+created_at index"
);

// ── Bookings table ───────────────────────────────────────────────────────────
$log[] = safeExec($db,
    "ALTER TABLE `bookings` ADD INDEX `idx_service_type` (`service_type`, `listing_id`)",
    "bookings: service_type+listing_id index"
);
$log[] = safeExec($db,
    "ALTER TABLE `bookings` ADD INDEX `idx_status_created` (`status`, `created_at` DESC)",
    "bookings: status+created_at index"
);

// ── Token tables ─────────────────────────────────────────────────────────────
$log[] = safeExec($db,
    "ALTER TABLE `password_resets` ADD INDEX `idx_expires` (`expires_at`)",
    "password_resets: expires_at index"
);
$log[] = safeExec($db,
    "ALTER TABLE `email_verification_tokens` ADD INDEX `idx_expires` (`expires_at`)",
    "email_verification_tokens: expires_at index"
);

// ── Activity logs ─────────────────────────────────────────────────────────────
$log[] = safeExec($db,
    "ALTER TABLE `activity_logs` ADD INDEX `idx_action_created` (`action_type`, `created_at` DESC)",
    "activity_logs: action_type+created_at index"
);

// ── Output results ─────────────────────────────────────────────────────────
if (PHP_SAPI === 'cli') {
    echo implode("\n", $log) . "\n\nDone. DELETE this file after running.\n";
} else {
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html><html><head><title>Index Migration</title></head><body>";
    echo "<h2>CSNExplore DB Index Migration</h2><pre>";
    echo htmlspecialchars(implode("\n", $log));
    echo "</pre><p><strong>Done. DELETE this file from the server after running!</strong></p></body></html>";
}
