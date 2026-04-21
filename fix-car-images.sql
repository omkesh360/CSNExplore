-- Fix car images: replace wrong hotel images with correct car images
-- Run this in phpMyAdmin or MySQL CLI

UPDATE `cars` SET `image` = 'images/uploads/maruti-suzuki-ertiga-car-rental-chhatrapati-sambhajinagar.webp' WHERE `id` = 1; -- Maruti Suzuki Ertiga
UPDATE `cars` SET `image` = 'images/uploads/maruti-suzuki-swift-car-rental-chhatrapati-sambhajinagar.webp'  WHERE `id` = 2; -- Maruti Suzuki Swift
UPDATE `cars` SET `image` = 'images/uploads/honda-amaze-sedan-rental-chhatrapati-sambhajinagar.webp'        WHERE `id` = 3; -- Honda Amaze
UPDATE `cars` SET `image` = 'images/uploads/maruti-suzuki-baleno-car-rental-chhatrapati-sambhajinagar.webp' WHERE `id` = 4; -- Maruti Suzuki Baleno
UPDATE `cars` SET `image` = 'images/uploads/tata-punch-suv-rental-chhatrapati-sambhajinagar.webp'           WHERE `id` = 5; -- Tata Punch
UPDATE `cars` SET `image` = 'images/uploads/hyundai-grand-i10-nios-car-rental-chhatrapati-sambhajinagar.webp' WHERE `id` = 6; -- Hyundai Grand i10
UPDATE `cars` SET `image` = 'images/uploads/mahindra-bolero-suv-rental-chhatrapati-sambhajinagar.webp'      WHERE `id` = 7; -- Mahindra Bolero
UPDATE `cars` SET `image` = 'images/uploads/kia-sonet-suv-rental-chhatrapati-sambhajinagar.webp'            WHERE `id` = 8; -- Kia Sonet
UPDATE `cars` SET `image` = 'images/uploads/tata-tiago-hatchback-rental-chhatrapati-sambhajinagar.webp'     WHERE `id` = 9; -- Tata Tiago
UPDATE `cars` SET `image` = 'images/uploads/kia-carens-mpv-rental-chhatrapati-sambhajinagar.webp'           WHERE `id` = 10; -- Kia Carens
