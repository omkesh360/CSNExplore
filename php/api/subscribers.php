// ── API: Newsletter Subscribers [B3.1] ───────────────────────────────────────
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: ' . (defined('CORS_ORIGIN') ? CORS_ORIGIN : 'https://csnexplore.com'));
header('Access-Control-Allow-Methods: GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Vary: Origin');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

try {
    validateCsrf();
    $db = getDB();
    $conn = $db->getConnection();
    // Note: newsletter_subscribers table is created via database.php schema init
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
