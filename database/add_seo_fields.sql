-- Add SEO fields to all listing tables and blogs
-- Run this migration to add SEO capabilities

-- Add SEO fields to stays table
ALTER TABLE `stays` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `description`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`;

-- Add SEO fields to cars table
ALTER TABLE `cars` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `description`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`,
ADD COLUMN `pricing_packages` TEXT DEFAULT NULL AFTER `price_with_driver`,
ADD COLUMN `policies` TEXT DEFAULT NULL AFTER `pricing_packages`;

-- Add SEO fields to bikes table
ALTER TABLE `bikes` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `description`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`,
ADD COLUMN `policies` TEXT DEFAULT NULL AFTER `driver_available`;

-- Add SEO fields to attractions table
ALTER TABLE `attractions` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `description`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`;

-- Add SEO fields to restaurants table
ALTER TABLE `restaurants` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `description`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`;

-- Add SEO fields to buses table
ALTER TABLE `buses` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `map_embed`,
ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `mini_description` TEXT DEFAULT NULL AFTER `to_location`,
ADD COLUMN `keywords` TEXT DEFAULT NULL AFTER `mini_description`;

-- Add SEO fields to blogs table
ALTER TABLE `blogs` 
ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `meta_description`,
ADD COLUMN `meta_keywords` TEXT DEFAULT NULL AFTER `meta_title`,
ADD COLUMN `slug` VARCHAR(255) DEFAULT NULL AFTER `meta_keywords`,
ADD COLUMN `focus_keyword` VARCHAR(100) DEFAULT NULL AFTER `slug`,
ADD COLUMN `seo_score` INT DEFAULT 0 AFTER `focus_keyword`,
ADD COLUMN `excerpt` TEXT DEFAULT NULL AFTER `content`;

-- Create keywords table for SEO keyword management
CREATE TABLE IF NOT EXISTS `seo_keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) NOT NULL,
  `usage_count` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`),
  KEY `idx_usage` (`usage_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add indexes for SEO fields
ALTER TABLE `stays` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `cars` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `bikes` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `attractions` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `restaurants` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `buses` ADD INDEX `idx_slug` (`slug`);
ALTER TABLE `blogs` ADD INDEX `idx_slug` (`slug`);

-- Add fulltext indexes for better search
ALTER TABLE `stays` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `cars` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `bikes` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `attractions` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `restaurants` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `buses` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_description`,`meta_keywords`);
ALTER TABLE `blogs` ADD FULLTEXT KEY `ft_seo` (`meta_title`,`meta_keywords`);
