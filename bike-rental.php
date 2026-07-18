<?php
// bike-rental.php
require_once 'php/config.php';
$type = 'bikes';
$_canonicalSlug = 'bike-rentals';
$_canonicalUrl = 'https://csnexplore.com/' . $_canonicalSlug;
$page_meta = [
    'description' => "Find the best bike rental in Aurangabad with CSNExplore. We offer the top bike rental service for scooters, cruisers, and sports bikes."
];
require_once 'php/templates/listing_core.php';
