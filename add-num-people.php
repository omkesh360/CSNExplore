<?php
require 'php/config.php';
$db = getDB();
$pdo = $db->getConnection();
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `trip_requests` (
          `id`             INT          NOT NULL AUTO_INCREMENT,
          `full_name`      VARCHAR(255) NOT NULL,
          `email`          VARCHAR(255) NOT NULL,
          `phone`          VARCHAR(50)  NOT NULL,
          `interests`      TEXT,
          `stay_type`      VARCHAR(100),
          `travel_mode`    VARCHAR(100),
          `travel_details` TEXT,
          `num_people`     INT          DEFAULT 1,
          `extra_notes`    TEXT,
          `status`         ENUM('new','contacted','completed','cancelled') DEFAULT 'new',
          `created_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP,
          `updated_at`     DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_status` (`status`),
          KEY `idx_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "OK: trip_requests table created\n";
    // Add num_people if table already existed without it
    try {
        $pdo->exec("ALTER TABLE trip_requests ADD COLUMN num_people INT DEFAULT 1 AFTER travel_details");
        echo "OK: num_people column added\n";
    } catch (Exception $e) { echo "num_people already exists\n"; }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
