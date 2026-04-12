<?php
// ── API: Newsletter Subscribers [B3.1] ───────────────────────────────────────
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    $db = getDB();
    $conn = $db->getConnection();

    // Ensure table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    if ($method === 'GET') {
        requireAdmin();
        $subs = $db->fetchAll("SELECT * FROM newsletter_subscribers ORDER BY subscribed_at DESC");
        sendJson(['success' => true, 'subscribers' => $subs]);
    }

    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $db->delete('newsletter_subscribers', 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Method not allowed', 405);
    }

} catch (Exception $e) {
    error_log('Subscribers API error: ' . $e->getMessage());
    sendError('Server error', 500);
}
