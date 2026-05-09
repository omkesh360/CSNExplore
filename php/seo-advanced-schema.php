<?php
/**
 * php/seo-advanced-schema.php — Advanced Schema Markup Generation
 * Implements rich snippets for all content types
 * Purpose: Maximize SERP visibility and CTR
 */

class AdvancedSchemaMarkup {
    
    /**
     * Generate Video Schema for blog posts with embedded videos
     */
    public static function videoSchema($videoUrl, $title, $description, $thumbnailUrl, $uploadDate = null) {
        $uploadDate = $uploadDate ?? date('c');
        return [
            '@context' => 'https://schema.org',
            '@type' => 'VideoObject',
            'name' => $title,
            'description' => $description,
            'thumbnailUrl' => [$thumbnailUrl],
            'uploadDate' => $uploadDate,
            'url' => $videoUrl,
            'contentUrl' => $videoUrl,
            'embedUrl' => str_replace('youtube.com/watch?v=', 'youtube.com/embed/', $videoUrl),
            'interactionCount' => '1000000'
        ];
    }
    
    /**
     * Generate Event Schema for bookings/activities
     */
    public static function eventSchema($eventName, $eventUrl, $startDate, $endDate, $location, $description, $price, $currency = 'INR') {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $eventName,
            'url' => $eventUrl,
            'description' => $description,
            'startDate' => date('c', strtotime($startDate)),
            'endDate' => date('c', strtotime($endDate)),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'location' => [
                '@type' => 'Place',
                'name' => $location,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $location,
                    'addressLocality' => 'Chhatrapati Sambhajinagar',
                    'addressRegion' => 'Maharashtra',
                    'postalCode' => '431005',
                    'addressCountry' => 'IN'
                ]
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => $currency,
                'availability' => 'https://schema.org/InStock',
                'url' => $eventUrl,
                'validFrom' => date('c')
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => 'CSNExplore',
                'url' => 'https://csnexplore.com',
                'telephone' => '+91-8600968888'
            ]
        ];
    }
    
    /**
     * Generate How-To Schema for travel guides and tutorials
     */
    public static function howToSchema($title, $description, $steps, $image = null) {
        $toolSteps = [];
        foreach ($steps as $idx => $step) {
            $toolSteps[] = [
                '@type' => 'HowToStep',
                'position' => $idx + 1,
                'name' => $step['title'] ?? '',
                'text' => $step['description'] ?? '',
                'image' => $step['image'] ?? $image
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $title,
            'description' => $description,
            'image' => $image,
            'step' => $toolSteps,
            'totalTime' => 'PT1H',
            'estimatedCost' => [
                '@type' => 'PriceSpecification',
                'currency' => 'INR',
                'price' => 'Variable'
            ]
        ];
    }
    
    /**
     * Generate AggregateRating Schema with enhanced properties
     */
    public static function aggregateRatingSchema($ratingValue, $ratingCount, $reviewCount, $name, $url, $image = null) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'AggregateRating',
            'ratingValue' => $ratingValue,
            'ratingCount' => $ratingCount,
            'reviewCount' => $reviewCount,
            'bestRating' => '5',
            'worstRating' => '1',
            'itemReviewed' => [
                '@type' => 'LocalBusiness',
                'name' => $name,
                'url' => $url,
                'image' => $image
            ]
        ];
    }
    
    /**
     * Generate Trip Schema for multi-day itineraries
     */
    public static function tripSchema($tripName, $description, $startDate, $duration, $stops) {
        $days = [];
        foreach ($stops as $idx => $stop) {
            $startTime = date('c', strtotime($startDate . ' +' . $idx . ' days'));
            $days[] = [
                '@type' => 'Trip',
                'name' => $stop['name'],
                'description' => $stop['description'],
                'startTime' => $startTime,
                'location' => [
                    '@type' => 'Place',
                    'name' => $stop['location'] ?? 'Chhatrapati Sambhajinagar',
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => $stop['lat'] ?? '19.8762',
                        'longitude' => $stop['lng'] ?? '75.3433'
                    ]
                ]
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $tripName,
            'description' => $description,
            'startDate' => $startDate,
            'duration' => $duration,
            'destinationBased' => array_merge([
                '@type' => 'Place',
                'name' => 'Chhatrapati Sambhajinagar',
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => '19.8762',
                    'longitude' => '75.3433'
                ]
            ], $days)
        ];
    }
    
    /**
     * Generate ListItem Schema for comparisons
     */
    public static function listItemSchema($items) {
        $itemList = [];
        foreach ($items as $idx => $item) {
            $itemList[] = [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'name' => $item['name'],
                'description' => $item['description'],
                'url' => $item['url'],
                'image' => $item['image'] ?? null
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'itemListElement' => $itemList,
            'numberOfItems' => count($items)
        ];
    }
    
    /**
     * Generate Place Schema with multiple languages
     */
    public static function placeSchema($name, $description, $address, $phone, $website, $coordinates, $businessType = 'LocalBusiness') {
        return [
            '@context' => 'https://schema.org',
            '@type' => $businessType,
            'name' => $name,
            '@language' => 'en',
            'alternateName' => $name . ' Aurangabad',
            'description' => $description,
            'url' => $website,
            'telephone' => $phone,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $address['street'] ?? '',
                'addressLocality' => $address['city'] ?? 'Chhatrapati Sambhajinagar',
                'addressRegion' => $address['state'] ?? 'Maharashtra',
                'postalCode' => $address['zipcode'] ?? '431005',
                'addressCountry' => 'IN'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $coordinates['lat'],
                'longitude' => $coordinates['lng'],
                'elevation' => '596'
            ],
            'serviceArea' => [
                '@type' => 'City',
                'name' => 'Chhatrapati Sambhajinagar'
            ],
            'priceRange' => '₹₹',
            'sameAs' => [
                'https://www.facebook.com/csnexplore',
                'https://www.instagram.com/csnexplore_/',
                'https://www.twitter.com/csnexplore'
            ]
        ];
    }
    
    /**
     * Generate Offer Schema for deals and promotions
     */
    public static function offerSchema($productName, $productUrl, $price, $currency = 'INR', $availability = 'InStock', $validUntil = null) {
        $validUntil = $validUntil ?? date('c', strtotime('+30 days'));
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Offer',
            'url' => $productUrl,
            'priceCurrency' => $currency,
            'price' => $price,
            'availability' => 'https://schema.org/' . $availability,
            'validFrom' => date('c'),
            'validThrough' => $validUntil,
            'offeredBy' => [
                '@type' => 'Organization',
                'name' => 'CSNExplore'
            ]
        ];
    }
    
    /**
     * Generate Breadcrumb with advanced properties
     */
    public static function breadcrumbSchema($items) {
        $elements = [];
        foreach ($items as $idx => $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $idx + 1,
                'name' => $item['name'],
                'item' => strpos($item['url'], 'http') === 0 ? $item['url'] : 'https://csnexplore.com' . $item['url']
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements
        ];
    }
    
    /**
     * Generate QA Schema for common questions
     */
    public static function qaSchema($questions) {
        $mainEntity = [];
        foreach ($questions as $qa) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $qa['question'],
                'answerCount' => 1,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $qa['answer'],
                    'author' => [
                        '@type' => 'Organization',
                        'name' => 'CSNExplore'
                    ]
                ],
                'suggestedAnswer' => $qa['related'] ?? []
            ];
        }
        
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity
        ];
    }
    
    /**
     * Generate PersonOrganization Reference
     */
    public static function authorSchema($name, $bio = null, $image = null, $url = null) {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $name,
            'bio' => $bio,
            'image' => $image,
            'url' => $url ?? 'https://csnexplore.com',
            'worksFor' => [
                '@type' => 'Organization',
                'name' => 'CSNExplore'
            ]
        ];
    }
    
    /**
     * Generate Language Alternative Schema (for hreflang)
     */
    public static function languageAlternative($pageUrl, $languages = ['en', 'hi']) {
        $alternates = [];
        foreach ($languages as $lang) {
            $alternates[] = [
                '@context' => 'https://schema.org',
                '@type' => 'Language',
                'name' => $lang,
                'url' => $pageUrl . '?lang=' . $lang
            ];
        }
        return $alternates;
    }
}

// Helper function for easy access in templates
function getAdvancedSchema($type, ...$args) {
    $method = ucfirst($type) . 'Schema';
    if (method_exists('AdvancedSchemaMarkup', $method)) {
        return AdvancedSchemaMarkup::$method(...$args);
    }
    return null;
}
