<?php
// Migration script to import JSON data into SQLite database

require_once __DIR__ . '/database.php';

echo "🚀 Starting TravelHub Database Migration...\n\n";

try {
    $db = Database::getInstance();
    
    // Initialize database schema
    echo "📋 Creating database schema...\n";
    $db->initialize();
    echo "✅ Schema created successfully\n\n";
    
    // Import Users
    echo "👥 Importing users...\n";
    $usersFile = __DIR__ . '/../data/users.json';
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        foreach ($users as $user) {
            // Normalize role to lowercase
            $role = strtolower($user['role'] ?? 'user');
            if ($role === 'admin') {
                $role = 'admin';
            } elseif ($role === 'vendor') {
                $role = 'vendor';
            } else {
                $role = 'user';
            }
            
            $db->insert('users', [
                'email' => $user['email'],
                'password_hash' => $user['password'],
                'name' => $user['name'],
                'phone' => $user['phone'] ?? null,
                'role' => $role,
                'is_verified' => 1
            ]);
        }
        echo "✅ Imported " . count($users) . " users\n\n";
    }
    
    // Import Vendors
    echo "🏢 Importing vendors...\n";
    $vendorsFile = __DIR__ . '/../data/vendors.json';
    if (file_exists($vendorsFile)) {
        $vendors = json_decode(file_get_contents($vendorsFile), true);
        foreach ($vendors as $vendor) {
            $db->insert('vendors', [
                'name' => $vendor['name'],
                'email' => $vendor['email'] ?? null,
                'phone' => $vendor['phone'] ?? null,
                'address' => $vendor['address'] ?? null,
                'description' => $vendor['description'] ?? null,
                'rating' => $vendor['rating'] ?? 0,
                'total_bookings' => $vendor['totalBookings'] ?? 0
            ]);
        }
        echo "✅ Imported " . count($vendors) . " vendors\n\n";
    }
    
    // Import Stays
    echo "🏨 Importing stays...\n";
    $staysFile = __DIR__ . '/../data/stays.json';
    if (file_exists($staysFile)) {
        $stays = json_decode(file_get_contents($staysFile), true);
        foreach ($stays as $stay) {
            $db->insert('stays', [
                'name' => $stay['name'],
                'type' => $stay['type'] ?? null,
                'location' => $stay['location'],
                'description' => $stay['description'] ?? null,
                'price_per_night' => $stay['pricePerNight'] ?? $stay['price'] ?? 0,
                'rating' => $stay['rating'] ?? 0,
                'reviews' => $stay['reviews'] ?? 0,
                'badge' => $stay['badge'] ?? null,
                'image' => $stay['image'] ?? null,
                'amenities' => isset($stay['amenities']) ? json_encode($stay['amenities']) : null,
                'room_type' => $stay['roomType'] ?? null,
                'max_guests' => $stay['maxGuests'] ?? 2
            ]);
        }
        echo "✅ Imported " . count($stays) . " stays\n\n";
    }
    
    // Import Cars
    echo "🚗 Importing cars...\n";
    $carsFile = __DIR__ . '/../data/cars.json';
    if (file_exists($carsFile)) {
        $cars = json_decode(file_get_contents($carsFile), true);
        foreach ($cars as $car) {
            $db->insert('cars', [
                'name' => $car['name'],
                'type' => $car['type'] ?? null,
                'location' => $car['location'],
                'description' => $car['description'] ?? null,
                'price_per_day' => $car['pricePerDay'] ?? $car['price'] ?? 0,
                'rating' => $car['rating'] ?? 0,
                'reviews' => $car['reviews'] ?? 0,
                'badge' => $car['badge'] ?? null,
                'image' => $car['image'] ?? null,
                'features' => isset($car['features']) ? json_encode($car['features']) : null,
                'fuel_type' => $car['fuelType'] ?? null,
                'transmission' => $car['transmission'] ?? null,
                'seats' => $car['seats'] ?? 5
            ]);
        }
        echo "✅ Imported " . count($cars) . " cars\n\n";
    }
    
    // Import Bikes
    echo "🏍️ Importing bikes...\n";
    $bikesFile = __DIR__ . '/../data/bikes.json';
    if (file_exists($bikesFile)) {
        $bikes = json_decode(file_get_contents($bikesFile), true);
        foreach ($bikes as $bike) {
            $db->insert('bikes', [
                'name' => $bike['name'],
                'type' => $bike['type'] ?? null,
                'location' => $bike['location'],
                'description' => $bike['description'] ?? null,
                'price_per_day' => $bike['pricePerDay'] ?? $bike['price'] ?? 0,
                'rating' => $bike['rating'] ?? 0,
                'reviews' => $bike['reviews'] ?? 0,
                'badge' => $bike['badge'] ?? null,
                'image' => $bike['image'] ?? null,
                'features' => isset($bike['features']) ? json_encode($bike['features']) : null,
                'fuel_type' => $bike['fuelType'] ?? null,
                'cc' => $bike['cc'] ?? null
            ]);
        }
        echo "✅ Imported " . count($bikes) . " bikes\n\n";
    }
    
    // Import Restaurants
    echo "🍽️ Importing restaurants...\n";
    $restaurantsFile = __DIR__ . '/../data/restaurants.json';
    if (file_exists($restaurantsFile)) {
        $restaurants = json_decode(file_get_contents($restaurantsFile), true);
        foreach ($restaurants as $restaurant) {
            $db->insert('restaurants', [
                'name' => $restaurant['name'],
                'type' => $restaurant['type'] ?? null,
                'cuisine' => $restaurant['cuisine'] ?? null,
                'location' => $restaurant['location'],
                'description' => $restaurant['description'] ?? null,
                'price_per_person' => $restaurant['pricePerPerson'] ?? $restaurant['price'] ?? 0,
                'rating' => $restaurant['rating'] ?? 0,
                'reviews' => $restaurant['reviews'] ?? 0,
                'badge' => $restaurant['badge'] ?? null,
                'image' => $restaurant['image'] ?? null
            ]);
        }
        echo "✅ Imported " . count($restaurants) . " restaurants\n\n";
    }
    
    // Import Attractions
    echo "🎭 Importing attractions...\n";
    $attractionsFile = __DIR__ . '/../data/attractions.json';
    if (file_exists($attractionsFile)) {
        $attractions = json_decode(file_get_contents($attractionsFile), true);
        foreach ($attractions as $attraction) {
            $db->insert('attractions', [
                'name' => $attraction['name'],
                'type' => $attraction['type'] ?? null,
                'location' => $attraction['location'],
                'description' => $attraction['description'] ?? null,
                'entry_fee' => $attraction['entryFee'] ?? $attraction['price'] ?? 0,
                'rating' => $attraction['rating'] ?? 0,
                'reviews' => $attraction['reviews'] ?? 0,
                'badge' => $attraction['badge'] ?? null,
                'image' => $attraction['image'] ?? null,
                'opening_hours' => $attraction['openingHours'] ?? null,
                'best_time' => $attraction['bestTime'] ?? null
            ]);
        }
        echo "✅ Imported " . count($attractions) . " attractions\n\n";
    }
    
    // Import Buses
    echo "🚌 Importing buses...\n";
    $busesFile = __DIR__ . '/../data/buses.json';
    if (file_exists($busesFile)) {
        $buses = json_decode(file_get_contents($busesFile), true);
        foreach ($buses as $bus) {
            $db->insert('buses', [
                'operator' => $bus['operator'] ?? $bus['name'],
                'bus_type' => $bus['busType'] ?? $bus['type'] ?? null,
                'from_location' => $bus['from'] ?? $bus['fromLocation'] ?? 'CSN',
                'to_location' => $bus['to'] ?? $bus['toLocation'] ?? 'Unknown',
                'departure_time' => $bus['departureTime'] ?? null,
                'arrival_time' => $bus['arrivalTime'] ?? null,
                'duration' => $bus['duration'] ?? null,
                'price' => $bus['price'] ?? 0,
                'rating' => $bus['rating'] ?? 0,
                'reviews' => $bus['reviews'] ?? 0,
                'badge' => $bus['badge'] ?? null,
                'image' => $bus['image'] ?? null,
                'amenities' => isset($bus['amenities']) ? json_encode($bus['amenities']) : null,
                'seats_available' => $bus['seatsAvailable'] ?? 40
            ]);
        }
        echo "✅ Imported " . count($buses) . " buses\n\n";
    }
    
    // Import Homepage Content
    echo "🏠 Importing homepage content...\n";
    $homepageFile = __DIR__ . '/../data/homepage-content.json';
    if (file_exists($homepageFile)) {
        $homepage = json_decode(file_get_contents($homepageFile), true);
        $db->insert('homepage_content', [
            'section' => 'full_content',
            'content' => json_encode($homepage),
            'display_order' => 1
        ]);
        echo "✅ Imported homepage content\n\n";
    }
    
    echo "🎉 Migration completed successfully!\n";
    echo "✅ Database created at: database/travelhub.db\n";
    
} catch (Exception $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
