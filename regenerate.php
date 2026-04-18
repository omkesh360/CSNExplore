<?php
/**
 * Simple Regenerate Script
 * Updates database and regenerates all listing pages
 */

require_once 'php/config.php';
$db = getDB();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Regenerate Listings</title>
    <style>
        body { font-family: Arial; padding: 40px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #ec5b13; margin-bottom: 20px; }
        .success { color: #22c55e; font-weight: bold; margin: 10px 0; }
        .error { color: #ef4444; font-weight: bold; margin: 10px 0; }
        .btn { display: inline-block; padding: 12px 24px; background: #ec5b13; color: white; text-decoration: none; border-radius: 8px; font-weight: bold; margin-top: 20px; }
        .btn:hover { background: #d14d0f; }
        pre { background: #f9fafb; padding: 15px; border-radius: 8px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔄 Regenerate Listings</h1>
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<h2>Processing...</h2>";
            
            try {
                // Delete all stays
                $db->execute("DELETE FROM stays");
                echo "<p class='success'>✓ Cleared old stays</p>";
                
                // Insert 6 stays
                $stays = [
                    [1, 'ITS HOME – Home Stay Inn', 'Homestay', 'Chhatrapati Sambhajinagar', 'A cozy and comfortable homestay in the heart of Chhatrapati Sambhajinagar. Perfect for families and solo travelers looking for a home-like experience.', 999.00, 4.3, NULL, 'images/uploads/its-home-home-stay-inn-stays-main.jpeg', '["images/uploads/its-home-home-stay-inn-stays-gallery (1).jpeg","images/uploads/its-home-home-stay-inn-stays-gallery (2).jpeg","images/uploads/its-home-home-stay-inn-stays-gallery (3).jpeg","images/uploads/its-home-home-stay-inn-stays-gallery (4).jpeg"]', '["Wi-Fi","Parking","Hot Water","TV"]', 2],
                    [2, 'Its Home – Service Apartments', 'Service Apartment', 'Chhatrapati Sambhajinagar', 'Fully furnished service apartments with kitchen facilities. Ideal for long stays and business travelers in Chhatrapati Sambhajinagar.', 1499.00, 4.4, '', 'images/uploads/its-home-service-apartments-stays-main.webp', '["images/uploads/its-home-service-apartments-stays-gallery (1).webp","images/uploads/its-home-service-apartments-stays-gallery (2).webp","images/uploads/its-home-service-apartments-stays-gallery (3).webp","images/uploads/its-home-service-apartments-stays-gallery (4).webp","images/uploads/its-home-service-apartments-stays-gallery (5).webp","images/uploads/its-home-service-apartments-stays-gallery (6).webp"]', '["Wi-Fi","Kitchen","Parking","AC","Washing Machine"]', 0],
                    [3, 'Treebo Aroma Executive', 'Business Hotel', 'Chhatrapati Sambhajinagar', 'A premium business hotel offering executive rooms with modern amenities. Treebo certified quality with complimentary breakfast.', 1800.00, 4.2, '', 'images/uploads/treebo-aroma-executive-stays-main.webp', '["images/uploads/treebo-aroma-executive-stays-gallery (1).webp","images/uploads/treebo-aroma-executive-stays-gallery (2).webp","images/uploads/treebo-aroma-executive-stays-gallery (3).webp","images/uploads/treebo-aroma-executive-stays-gallery (4).webp","images/uploads/treebo-aroma-executive-stays-gallery (5).webp","images/uploads/treebo-aroma-executive-stays-gallery (6).webp"]', '["Wi-Fi","Breakfast","AC","Room Service","Parking"]', 1],
                    [4, 'Hotel Blossom', 'Budget Hotel', 'Chhatrapati Sambhajinagar', 'Clean and affordable hotel in Chhatrapati Sambhajinagar. Great value for money with all essential amenities for a comfortable stay.', 1200.00, 4.1, 'Budget', 'images/uploads/hotel-blossom-stays-main.jpeg', '[]', '["Wi-Fi","AC","TV","Hot Water"]', 3],
                    [5, 'Hotel The Gravity Inn', 'Premium Hotel', 'Silicon City, Indore', 'A top-rated premium hotel in Silicon City, Indore. Featuring free Wi-Fi, complimentary breakfast, and world-class amenities for a luxurious stay.', 2172.00, 4.7, 'Top Rated', 'images/uploads/hotel-the-gravity-inn-stays-main.webp', '["images/uploads/hotel-the-gravity-inn-stays-gallery (1).webp","images/uploads/hotel-the-gravity-inn-stays-gallery (2).webp","images/uploads/hotel-the-gravity-inn-stays-gallery (3).webp","images/uploads/hotel-the-gravity-inn-stays-gallery (4).webp"]', '["Free Wi-Fi","Free Breakfast","AC","Parking","Room Service","Swimming Pool"]', 4],
                    [6, 'Hotel O Indraprasth', 'Budget Hotel', 'Silicon City, Indore', 'Budget-friendly hotel in Silicon City, Indore with free parking and Wi-Fi. Perfect for travelers looking for affordable accommodation.', 840.00, 3.1, 'Budget', 'images/uploads/hotel-o-indraprasth-stays-main.jpeg', '["images/uploads/hotel-o-indraprasth-stays-gallery (1).jpeg","images/uploads/hotel-o-indraprasth-stays-gallery (2).jpeg","images/uploads/hotel-o-indraprasth-stays-gallery (3).jpeg","images/uploads/hotel-o-indraprasth-stays-gallery (4).jpeg"]', '["Free Parking","Free Wi-Fi","AC","TV"]', 5]
                ];
                
                foreach ($stays as $s) {
                    $sql = "INSERT INTO stays (id, vendor_id, name, type, location, description, price_per_night, rating, reviews, badge, image, gallery, amenities, room_type, max_guests, map_embed, is_active, display_order) 
                            VALUES (?, NULL, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, '', 2, NULL, 1, ?)";
                    $db->execute($sql, $s);
                }
                
                echo "<p class='success'>✓ Added 6 stays</p>";
                
                // Delete old pages
                $old_files = glob(__DIR__ . '/listing-detail/stays-*.html');
                foreach ($old_files as $file) {
                    unlink($file);
                }
                echo "<p class='success'>✓ Deleted old pages</p>";
                
                // Regenerate
                ob_start();
                require_once __DIR__ . '/php/regenerate-complete.php';
                $output = ob_get_clean();
                
                echo "<p class='success'>✓ Regenerated all pages</p>";
                
                // Delete listings.xtx
                if (file_exists('listings.xtx')) {
                    unlink('listings.xtx');
                    echo "<p class='success'>✓ Cleaned up listings.xtx</p>";
                }
                
                echo "<h2 style='color:#22c55e;'>✅ Done!</h2>";
                echo "<a href='listing/stays' class='btn'>View Stays Listing</a>";
                
            } catch (Exception $e) {
                echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        } else {
            ?>
            <p>This will:</p>
            <ul>
                <li>Clear old stays from database</li>
                <li>Add 6 correct stays</li>
                <li>Delete old generated pages</li>
                <li>Regenerate all listing pages</li>
                <li>Clean up listings.xtx file</li>
            </ul>
            
            <form method="POST">
                <button type="submit" class="btn">🚀 Regenerate Now</button>
            </form>
            <?php
        }
        ?>
    </div>
</body>
</html>
