-- CSNExplore – Add Comments Table (Migration)
-- Run this in phpMyAdmin or MySQL CLI to fix the commenting error:
-- SQLSTATE[42S22]: Column not found: 1054 Unknown column 'guest_name' in 'field list'
--
-- This table was missing from the original schema. The comments API (php/api/comments.php)
-- requires this table with the guest_name column to function.

USE csnexplore;

CREATE TABLE IF NOT EXISTS `comments` (
  `id`          int(11)       NOT NULL AUTO_INCREMENT,
  `user_id`     int(11)       DEFAULT NULL COMMENT 'NULL = guest comment',
  `guest_name`  varchar(100)  DEFAULT NULL COMMENT 'Name for unauthenticated commenters',
  `ref_type`    varchar(50)   NOT NULL    COMMENT 'blog | listing | stays | cars | bikes | attractions | restaurants | buses',
  `ref_id`      int(11)       NOT NULL    COMMENT 'ID of the referenced item',
  `content`     text          NOT NULL,
  `status`      enum('pending','approved','spam') NOT NULL DEFAULT 'approved',
  `created_at`  datetime      DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  datetime      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ref`     (`ref_type`, `ref_id`),
  KEY `idx_status`  (`status`),
  KEY `idx_user`    (`user_id`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `comments_ibfk_1`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
