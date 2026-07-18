<?php
// car-rental.php
require_once 'php/config.php';
$type = 'cars';
$_canonicalSlug = 'car-rentals';
$_canonicalUrl = 'https://csnexplore.com/' . $_canonicalSlug;
$page_meta = [
    'description' => "Find the best car rental in Aurangabad with CSNExplore. We offer the top car rental service in Aurangabad, including self drive cars and outstation cabs."
];
require_once 'php/templates/listing_core.php';
