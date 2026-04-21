<?php
/**
 * Create Admin User - Run this once to create/reset admin account
 */
require_once 'php/config.php';

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Create Admin</title>";
echo "<style>body{font-family:Arial;max-width:800px;margin:50px auto;padding:20px;background:#f3f4f6;}";
echo ".box{background:white;padding:30px;border-radius:12px;box-shadow:0 4px 6px rgba(0,0,0,0.1);}";
echo ".success{background:#d1fae5;border:2px solid #10b981;color:#065f46;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".error{background:#fee2e2;border:2px solid #ef4444;color:#991b1b;padding:15px;border-radius:8px;margin:20px 0;}";
echo ".info{background:#dbeafe;border:2px solid #3b82f6;color:#1e40af;padding:15px;border-radius:8px;margin:20px 0;}";
echo "input,button{width:100%;padding:12px;margin:10px 0;border:2px solid #e5e7eb;border-radius:8px;font-size:16px;}";
echo "button{background:#ec5b13;color:white;border:none;font-weight:bold;cursor:pointer;}";
echo "button:hover{background:#d14a0f;}</style></head><body><div class='box'>";

echo "<h1>👤 Create/Reset Admin Account</h1>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDB();
        
        $username = trim($_POST['username']);
        $email = strtolower(trim($_POST['email']));
        $password = $_POST['password'];
        $name = trim($_POST['name']);
        
        if (empty($username) || empty($email) || empty($password) || empty($name)) {
            throw new Exception('All fields are required');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address');
        }
        
        if (strlen($password) < 6) {
            throw new Exception('Password must be at least 6 characters');
        }
        
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        // Check if admin exists
        $existing = $db->fetchOne("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
        
        if ($existing) {
            // Update existing admin
            $db->query("UPDATE users SET 
                password_hash = ?, 
                name = ?, 
                email = ?,
                role = 'admin',
                is_verified = 1
                WHERE id = ?", 
                [$passwordHash, $name, $email, $existing['id']]
            );
            
            echo "<div class='success'><h2>✅ Admin Updated!</h2>";
            echo "<p><strong>Username:</strong> $username</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Password:</strong> (updated)</p>";
            echo "<p><a href='adminexplorer.php'><strong>→ Go to Admin Login</strong></a></p></div>";
        } else {
            // Create new admin
            $db->query("INSERT INTO users (username, email, password_hash, name, phone, role, is_verified) 
                VALUES (?, ?, ?, ?, '', 'admin', 1)", 
                [$username, $email, $passwordHash, $name]
            );
            
            echo "<div class='success'><h2>🎉 Admin Created!</h2>";
            echo "<p><strong>Username:</strong> $username</p>";
            echo "<p><strong>Email:</strong> $email</p>";
            echo "<p><strong>Password:</strong> (set)</p>";
            echo "<p><a href='adminexplorer.php'><strong>→ Go to Admin Login</strong></a></p></div>";
        }
        
        echo "<div class='info'><strong>⚠️ IMPORTANT:</strong> Delete this file (create-admin.php) now for security!</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
} else {
    echo "<div class='info'>Fill in the details below to create or reset your admin account.</div>";
    echo "<form method='POST'>";
    echo "<label><strong>Full Name:</strong></label>";
    echo "<input type='text' name='name' placeholder='Admin Name' required>";
    echo "<label><strong>Username:</strong></label>";
    echo "<input type='text' name='username' placeholder='admin' required>";
    echo "<label><strong>Email:</strong></label>";
    echo "<input type='email' name='email' placeholder='admin@example.com' required>";
    echo "<label><strong>Password:</strong></label>";
    echo "<input type='password' name='password' placeholder='Minimum 6 characters' required>";
    echo "<button type='submit'>Create/Update Admin</button>";
    echo "</form>";
}

echo "</div></body></html>";
?>
