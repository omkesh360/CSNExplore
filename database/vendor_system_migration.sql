-- Multi-Vendor System Migration for CSNExplore
-- Run this SQL to add vendor functionality

-- 1. Create vendors table
CREATE TABLE IF NOT EXISTS `vendors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255),
  `phone` VARCHAR(50),
  `business_name` VARCHAR(255),
  `status` ENUM('active','inactive') DEFAULT 'active',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add vendor_id to stays table (rooms/hotels)
ALTER TABLE `stays` 
ADD COLUMN `vendor_id` INT NULL AFTER `id`,
ADD COLUMN `map_embed` LONGTEXT NULL AFTER `gallery`,
ADD INDEX `idx_vendor_stays` (`vendor_id`);

-- 3. Add vendor_id to cars table
ALTER TABLE `cars` 
ADD COLUMN `vendor_id` INT NULL AFTER `id`,
ADD COLUMN `map_embed` LONGTEXT NULL AFTER `gallery`,
ADD INDEX `idx_vendor_cars` (`vendor_id`);

-- 4. Create room_types table for room management
CREATE TABLE IF NOT EXISTS `room_types` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `vendor_id` INT NULL,
  `stay_id` INT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `base_price` DECIMAL(10,2) DEFAULT 0,
  `max_guests` INT DEFAULT 2,
  `amenities` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_vendor_room_types` (`vendor_id`),
  INDEX `idx_stay_room_types` (`stay_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Create rooms table for individual room inventory
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_type_id` INT NOT NULL,
  `vendor_id` INT NULL,
  `room_number` VARCHAR(50) NOT NULL,
  `floor` VARCHAR(20),
  `price` DECIMAL(10,2) NOT NULL,
  `is_available` TINYINT(1) DEFAULT 1,
  `status` ENUM('available','occupied','maintenance') DEFAULT 'available',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_type_id`) REFERENCES `room_types`(`id`) ON DELETE CASCADE,
  INDEX `idx_vendor_rooms` (`vendor_id`),
  INDEX `idx_room_type` (`room_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Add availability toggle to cars
ALTER TABLE `cars` 
ADD COLUMN `is_available` TINYINT(1) DEFAULT 1 AFTER `is_active`;

-- Note: Admin-created listings will have vendor_id = NULL
-- Vendor-created listings will have their vendor_id assigned
