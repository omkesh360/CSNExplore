-- Fix bike images: replace wrong hotel images with correct bike images
UPDATE `bikes` SET `image` = 'images/uploads/hero-splendor-bike-rental-chhatrapati-sambhajinagar.webp'      WHERE `id` = 1;  -- Hero Splendor
UPDATE `bikes` SET `image` = 'images/uploads/honda-dio-scooter-rental-chhatrapati-sambhajinagar.webp'       WHERE `id` = 2;  -- Honda Dio
UPDATE `bikes` SET `image` = 'images/uploads/yamaha-ray-scooter-rental-chhatrapati-sambhajinagar.webp'      WHERE `id` = 3;  -- Yamaha Ray
UPDATE `bikes` SET `image` = 'images/uploads/honda-activa-scooter-rental-chhatrapati-sambhajinagar.webp'    WHERE `id` = 4;  -- Honda Activa
UPDATE `bikes` SET `image` = 'images/uploads/honda-activa-scooter-rental-chhatrapati-sambhajinagar.webp'    WHERE `id` = 5;  -- Honda Activa 6G
UPDATE `bikes` SET `image` = 'images/uploads/tvs-jupiter-scooter-rental-chhatrapati-sambhajinagar.webp'     WHERE `id` = 6;  -- TVS Jupiter
UPDATE `bikes` SET `image` = 'images/uploads/suzuki-access-125-scooter-rental-chhatrapati-sambhajinagar.webp' WHERE `id` = 7; -- Suzuki Access 125
UPDATE `bikes` SET `image` = 'images/uploads/honda-grazia-scooter-rental-chhatrapati-sambhajinagar.webp'    WHERE `id` = 8;  -- Honda Grazia
UPDATE `bikes` SET `image` = 'images/uploads/honda-shine-125-bike-rental-chhatrapati-sambhajinagar.webp'    WHERE `id` = 9;  -- Honda X-Blade (closest match)
UPDATE `bikes` SET `image` = 'images/uploads/honda-shine-125-bike-rental-chhatrapati-sambhajinagar.webp'    WHERE `id` = 10; -- Honda Shine 125cc
UPDATE `bikes` SET `image` = 'images/uploads/bajaj-pulsar.jpg'                                              WHERE `id` = 11; -- Bajaj Pulsar
UPDATE `bikes` SET `image` = 'images/uploads/royal-enfield-classic-350-bike-rental-chhatrapati-sambhajinagar.webp' WHERE `id` = 12; -- Royal Enfield Classic 350
UPDATE `bikes` SET `image` = 'images/uploads/royal-enfield.jpg'                                             WHERE `id` = 13; -- Royal Enfield Himalayan
UPDATE `bikes` SET `image` = 'images/uploads/royal-enfield.jpg'                                             WHERE `id` = 14; -- Bajaj Avenger (closest match)
UPDATE `bikes` SET `image` = 'images/uploads/hero-splendor-bike-rental-chhatrapati-sambhajinagar.webp'      WHERE `id` = 15; -- Yamaha R15 (fallback)
