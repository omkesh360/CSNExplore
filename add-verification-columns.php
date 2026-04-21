<?php
/**
 * Add Email Verification Columns to Users Table
 */
require_once 'php/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Add Verification Columns</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;background:#f3f4f6;}";
echo ".box{background:white;padding:30px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1);}";
echo ".success{background:#d1fae5;border:2px solid #10b981;color:#065f46;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".error{background:#fee2e2;border:2px solid #ef4444;color:#991b1b;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".info{background:#dbeafe;border:2px solid #3b82f6;color:#1e40af;padding:15px;border-radius:8px;margin:20px 0;}";
echo "</style></head><body><div class='box'>";

echo "<h1>📧 Add Email Verification Columns</h1>";

try {
    $db = getDB();
    
    // Check if columns already exist
    $columns = $db->fetchAll("DESCRIBE users");
    $hasVerificationToken = false;
    $hasTokenExpires = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'verification_token') $hasVerificationToken = true;
        if ($col['Field'] === 'token_expires_at') $hasTokenExpires = true;
    }
    
    if (!$hasVerificationToken) {
        echo "<div class='info'>Adding verification_token column...</div>";
        $db->query("ALTER TABLE users ADD COLUMN verification_token VARCHAR(255) NULL AFTER is_verified");
        echo "<div class='success'>✅ verification_token column added</div>";
    } else {
        echo "<div class='info'>✓ verification_token column already exists</div>";
    }
    
    if (!$hasTokenExpires) {
        echo "<div class='info'>Adding token_expires_at column...</div>";
        $db->query("ALTER TABLE users ADD COLUMN token_expires_at DATETIME NULL AFTER verification_token");
        echo "<div class='success'>✅ token_expires_at column added</div>";
    } else {
        echo "<div class='info'>✓ token_expires_at column already exists</div>";
    }
    
    echo "<div class='success'><h2>🎉 Success!</h2>";
    echo "<p>Email verification columns are ready!</p>";
    echo "<p><strong>Next:</strong> Delete this file (add-verification-columns.php) for security</p></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "</div></body></html>";
?>
