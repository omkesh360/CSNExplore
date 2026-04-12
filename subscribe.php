<?php
/**
 * subscribe.php — Newsletter subscription handler
 * Accepts both form POST (redirect) and JSON fetch (API response)
 */
require_once __DIR__ . '/php/config.php';

$isAjax = (
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
    || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
    || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if ($isAjax) sendError('Method not allowed', 405);
    header('Location: index.php');
    exit;
}

// Support both JSON body and form POST
$input = [];
$raw   = file_get_contents('php://input');
if ($raw && ($decoded = json_decode($raw, true))) {
    $input = $decoded;
} else {
    $input = $_POST;
}

$email = strtolower(trim($input['email'] ?? ''));

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) sendError('Valid email address required', 400);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?subscribe=invalid');
    exit;
}

// Rate limiting via cache file
$cacheDir  = __DIR__ . '/cache';
if (!is_dir($cacheDir)) mkdir($cacheDir, 0755, true);
$cacheFile = $cacheDir . '/sub_' . md5($email) . '.json';
if (file_exists($cacheFile)) {
    $cached = json_decode(file_get_contents($cacheFile), true);
    if ($cached && isset($cached['ts']) && (time() - $cached['ts']) < 86400) {
        if ($isAjax) sendJson(['success' => true, 'message' => 'You are already subscribed.']);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?subscribe=already');
        exit;
    }
}

try {
    $db = getDB();
    $conn = $db->getConnection();

    // Ensure newsletter_subscribers table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        subscribed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_active TINYINT(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Upsert subscriber
    $existing = $db->fetchOne("SELECT id, is_active FROM newsletter_subscribers WHERE email = ?", [$email]);
    if ($existing) {
        if (!$existing['is_active']) {
            $db->update('newsletter_subscribers', ['is_active' => 1], 'email = :where_email', [':where_email' => $email]);
        }
    } else {
        $db->insert('newsletter_subscribers', ['email' => $email]);
    }

    // Write rate-limit cache
    file_put_contents($cacheFile, json_encode(['ts' => time(), 'email' => $email]));

    if ($isAjax) sendJson(['success' => true, 'message' => 'Thank you for subscribing!']);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?subscribe=success');
    exit;

} catch (Exception $e) {
    error_log('Subscribe error: ' . $e->getMessage());
    if ($isAjax) sendError('Subscription failed. Please try again.', 500);
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php') . '?subscribe=error');
    exit;
}
