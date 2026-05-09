<?php
/**
 * Database Cleanup Script
 * Deletes: all bookings, all trip requests, all non-admin users
 * Keeps: only users with role = 'admin'
 *
 * Access: http://localhost/CSNExplore/admin/cleanup-database.php
 * IMPORTANT: Delete this file after use!
 */

// Basic security – only run from command line OR with a secret key
$secret = 'CSN_CLEANUP_2026';
$provided = $_GET['key'] ?? '';

if (php_sapi_name() !== 'cli' && $provided !== $secret) {
    http_response_code(403);
    echo '<h2 style="font-family:sans-serif;color:#dc2626;">403 Forbidden</h2>';
    echo '<p style="font-family:sans-serif;">Pass <code>?key=CSN_CLEANUP_2026</code> to run this script.</p>';
    exit;
}

require_once __DIR__ . '/../php/config.php';

$db  = Database::getInstance();
$pdo = $db->getConnection();

$results = [];

// ── 1. Delete all bookings ──────────────────────────────────────────────
try {
    $stmt = $pdo->exec('DELETE FROM bookings');
    $results[] = ['table'=>'bookings', 'status'=>'OK', 'rows'=>$stmt];
} catch (Exception $e) {
    $results[] = ['table'=>'bookings', 'status'=>'ERROR', 'msg'=>$e->getMessage()];
}

// ── 2. Delete all trip requests ─────────────────────────────────────────
try {
    $stmt = $pdo->exec('DELETE FROM trip_requests');
    $results[] = ['table'=>'trip_requests', 'status'=>'OK', 'rows'=>$stmt];
} catch (Exception $e) {
    $results[] = ['table'=>'trip_requests', 'status'=>'ERROR', 'msg'=>$e->getMessage()];
}

// ── 3. Delete all non-admin users ───────────────────────────────────────
try {
    $stmt = $pdo->exec("DELETE FROM users WHERE role != 'admin'");
    $results[] = ['table'=>'users (non-admin)', 'status'=>'OK', 'rows'=>$stmt];
} catch (Exception $e) {
    $results[] = ['table'=>'users (non-admin)', 'status'=>'ERROR', 'msg'=>$e->getMessage()];
}

// ── 4. Reset auto-increment (optional, keeps IDs clean) ────────────────
foreach (['bookings', 'trip_requests'] as $tbl) {
    try { $pdo->exec("ALTER TABLE `$tbl` AUTO_INCREMENT = 1"); } catch (Exception $e) {}
}

// ── 5. Clear query cache ────────────────────────────────────────────────
$db->clearCache();
$results[] = ['table'=>'query cache', 'status'=>'OK', 'rows'=>'cleared'];

// ── Show remaining admin users ──────────────────────────────────────────
$admins = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// ── Output ──────────────────────────────────────────────────────────────
if (php_sapi_name() === 'cli') {
    echo "=== CSNExplore Database Cleanup ===\n";
    foreach ($results as $r) {
        $row = $r['rows'] ?? ($r['msg'] ?? '?');
        echo "[{$r['status']}] {$r['table']}: {$row}\n";
    }
    echo "\nRemaining users:\n";
    foreach ($admins as $a) {
        echo "  #{$a['id']} {$a['name']} <{$a['email']}> [{$a['role']}]\n";
    }
    exit(0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>DB Cleanup | CSNExplore Admin</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Inter', system-ui, sans-serif; background: #0f172a; color: #e2e8f0; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; max-width: 640px; width: 100%; }
  h1 { font-size: 20px; font-weight: 800; margin-bottom: 8px; color: #f8fafc; display: flex; align-items: center; gap: 10px; }
  .sub { font-size: 12px; color: #64748b; margin-bottom: 24px; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
  th { text-align: left; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .05em; padding: 8px 12px; background: #0f172a; }
  td { font-size: 13px; padding: 10px 12px; border-top: 1px solid #1e293b; }
  tr:nth-child(even) td { background: rgba(255,255,255,0.02); }
  .ok   { color: #4ade80; font-weight: 700; }
  .err  { color: #f87171; font-weight: 700; }
  .warn { background: #78350f; border: 1px solid #b45309; border-radius: 10px; padding: 14px 16px; font-size: 13px; color: #fde68a; line-height: 1.5; margin-top: 8px; }
  .section-title { font-size: 13px; font-weight: 700; color: #94a3b8; margin-bottom: 8px; margin-top: 24px; text-transform: uppercase; letter-spacing: .05em; }
  .admin-pill { display: inline-flex; align-items: center; gap: 6px; background: rgba(96,165,250,0.1); border: 1px solid rgba(96,165,250,0.3); border-radius: 8px; padding: 6px 12px; font-size: 12px; color: #93c5fd; margin-bottom: 6px; }
  .btn { display: inline-flex; align-items: center; gap: 6px; margin-top: 20px; padding: 10px 20px; background: #dc2626; border: none; border-radius: 8px; color: #fff; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; }
</style>
</head>
<body>
<div class="card">
  <h1>🗑️ Database Cleanup</h1>
  <p class="sub">Ran at <?php echo date('Y-m-d H:i:s'); ?></p>

  <table>
    <thead><tr><th>Table / Action</th><th>Status</th><th>Rows Affected</th></tr></thead>
    <tbody>
    <?php foreach ($results as $r): ?>
    <tr>
      <td><?php echo htmlspecialchars($r['table']); ?></td>
      <td class="<?php echo $r['status']==='OK'?'ok':'err'; ?>"><?php echo $r['status']; ?></td>
      <td><?php echo htmlspecialchars((string)($r['rows'] ?? $r['msg'] ?? '')); ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>

  <div class="section-title">👑 Remaining admin users (<?php echo count($admins); ?>)</div>
  <?php foreach ($admins as $a): ?>
  <div class="admin-pill">
    #<?php echo $a['id']; ?> &nbsp;
    <strong><?php echo htmlspecialchars($a['name']); ?></strong>
    &nbsp;—&nbsp;<?php echo htmlspecialchars($a['email']); ?>
    &nbsp;[<?php echo $a['role']; ?>]
  </div><br>
  <?php endforeach; ?>
  <?php if (empty($admins)): ?>
  <p style="color:#f87171;font-size:13px;">⚠️ No admin users found! You may be locked out.</p>
  <?php endif; ?>

  <div class="warn">
    ⚠️ <strong>Security:</strong> Please delete <code>cleanup-database.php</code> from the server after this run to prevent accidental re-execution.
  </div>

  <a href="dashboard.php" class="btn">← Back to Dashboard</a>
</div>
</body>
</html>
