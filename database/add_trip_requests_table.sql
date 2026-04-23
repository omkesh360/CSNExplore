-- Add trip_requests table for Trip Planner functionality
-- Run this SQL to fix the "Something went wrong" error

USE `csnexplore`;

--
-- Table structure for table `trip_requests`
--

DROP TABLE IF EXISTS `trip_requests`;
CREATE TABLE IF NOT EXISTS `trip_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `interests` text DEFAULT NULL,
  `stay_type` varchar(100) DEFAULT NULL,
  `travel_mode` varchar(100) DEFAULT NULL,
  `travel_details` text DEFAULT NULL,
  `car_service_type` varchar(50) DEFAULT NULL,
  `car_sub_type` varchar(100) DEFAULT NULL,
  `bike_sub_type` varchar(100) DEFAULT NULL,
  `num_people` int(11) DEFAULT 1,
  `extra_notes` text DEFAULT NULL,
  `status` enum('new','contacted','completed','cancelled') DEFAULT 'new',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created` (`created_at`),
  KEY `idx_phone` (`phone`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Success message
SELECT 'trip_requests table created successfully!' AS message;
