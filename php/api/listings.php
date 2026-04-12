<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../jwt.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$method   = $_SERVER['REQUEST_METHOD'];
$category = sanitize($_GET['category'] ?? 'stays');
$id       = isset($_GET['id']) ? (int)$_GET['id'] : null;
$action   = sanitize($_GET['action'] ?? '');

$valid = ['stays','cars','bikes','restaurants','attractions','buses'];
if (!in_array($category, $valid)) sendError('Invalid category', 400);

$priceCol = [
    'stays'       => 'price_per_night',
    'cars'        => 'price_per_day',
    'bikes'       => 'price_per_day',
    'restaurants' => 'price_per_person',
    'attractions' => 'entry_fee',
    'buses'       => 'price',
];
$pc = $priceCol[$category];

try {
    $db = getDB();

    // ── GET ──────────────────────────────────────────────────────────────────
    if ($method === 'GET') {
        if ($id) {
            // Single item
            $item = $db->fetchOne("SELECT * FROM $category WHERE id = ?", [$id]);
            if (!$item) sendError('Not found', 404);
            // Decode JSON fields
            foreach (['amenities','features','gallery','menu_highlights'] as $f) {
                if (isset($item[$f]) && is_string($item[$f])) {
                    $item[$f] = json_decode($item[$f], true) ?: [];
                }
            }
            sendJson($item);
        } else {
            // List
            $where  = ['1=1'];
            $params = [];
            if (!empty($_GET['search'])) {
                $where[] = '(name LIKE ? OR location LIKE ?)';
                $s = '%' . $_GET['search'] . '%';
                $params = array_merge($params, [$s, $s]);
            }
            $sql = "SELECT * FROM $category WHERE " . implode(' AND ', $where) . " ORDER BY display_order ASC, id ASC";
            $items = $db->fetchAll($sql, $params);
            foreach ($items as &$item) {
                foreach (['amenities','features','gallery','menu_highlights'] as $f) {
                    if (isset($item[$f]) && is_string($item[$f])) {
                        $item[$f] = json_decode($item[$f], true) ?: [];
                    }
                }
            }
            unset($item);
            sendJson($items);
        }
    }

    // ── POST (create or reorder) ──────────────────────────────────────────────
    elseif ($method === 'POST') {
        requireAdmin();

        // Batch reorder
        if ($action === 'reorder') {
            $input   = getJsonInput();
            $updates = $input['updates'] ?? [];
            foreach ($updates as $u) {
                $db->update($category, ['display_order' => (int)$u['display_order']], 'id = :id', [':id' => (int)$u['id']]);
            }
            sendJson(['success' => true]);
        }

        $data = buildData($category, $pc, $db);
        $newId = $db->insert($category, $data);
        sendJson(['success' => true, 'id' => $newId], 201);
    }

    // ── PUT (update) ──────────────────────────────────────────────────────────
    elseif ($method === 'PUT' && $id) {
        requireAdmin();
        $input = getJsonInput();

        // Quick toggle (is_active only)
        if (count($input) === 1 && isset($input['is_active'])) {
            $db->update($category, ['is_active' => (int)$input['is_active']], 'id = :id', [':id' => $id]);
            sendJson(['success' => true]);
        }

        $data = buildData($category, $pc, $db);
        $db->update($category, $data, 'id = :id', [':id' => $id]);
        sendJson(['success' => true]);
    }

    // ── DELETE ────────────────────────────────────────────────────────────────
    elseif ($method === 'DELETE' && $id) {
        requireAdmin();
        $db->delete($category, 'id = ?', [$id]);
        sendJson(['success' => true]);
    }

    else {
        sendError('Not found', 404);
    }

} catch (Exception $e) {
    error_log('Listings API error: ' . $e->getMessage());
    sendError('Server error: ' . $e->getMessage(), 500);
}

// ── Build data array from input ───────────────────────────────────────────────
function buildData($category, $pc, $db) {
    $input = getJsonInput();

    $data = [
        'name'          => sanitize($input['name'] ?? ''),
        'location'      => sanitize($input['location'] ?? ''),
        'description'   => sanitize($input['description'] ?? ''),
        'rating'        => min(5, max(0, (float)($input['rating'] ?? 0))),
        'badge'         => sanitize($input['badge'] ?? ''),
        'image'         => sanitize($input['image'] ?? ''),
        'is_active'     => (int)($input['is_active'] ?? 1),
        'display_order' => (int)($input['display_order'] ?? 0),
        'map_embed'     => $input['map_embed'] ?? null,
        'updated_at'    => date('Y-m-d H:i:s'),
    ];

    // Price
    $data[$pc] = (float)($input[$pc] ?? 0);

    // Type field
    if (isset($input['type'])) $data['type'] = sanitize($input['type']);

    // Gallery (JSON)
    if (isset($input['gallery'])) {
        $g = is_array($input['gallery']) ? $input['gallery'] : (json_decode($input['gallery'], true) ?: []);
        $data['gallery'] = json_encode($g);
    }

    // Category-specific fields
    switch ($category) {
        case 'stays':
            if (isset($input['amenities'])) {
                $a = is_array($input['amenities']) ? $input['amenities'] : array_filter(array_map('trim', explode(',', $input['amenities'])));
                $data['amenities'] = json_encode(array_values($a));
            }
            if (isset($input['room_type']))  $data['room_type']  = sanitize($input['room_type']);
            if (isset($input['max_guests'])) $data['max_guests'] = (int)$input['max_guests'];
            break;
        case 'cars':
            if (isset($input['features'])) {
                $f = is_array($input['features']) ? $input['features'] : array_filter(array_map('trim', explode(',', $input['features'])));
                $data['features'] = json_encode(array_values($f));
            }
            if (isset($input['fuel_type']))        $data['fuel_type']        = sanitize($input['fuel_type']);
            if (isset($input['transmission']))     $data['transmission']     = sanitize($input['transmission']);
            if (isset($input['seats']))            $data['seats']            = (int)$input['seats'];
            if (isset($input['driver_available'])) $data['driver_available'] = (int)$input['driver_available'];
            if (isset($input['price_with_driver'])) $data['price_with_driver'] = (float)$input['price_with_driver'];
            break;
        case 'bikes':
            if (isset($input['features'])) {
                $f = is_array($input['features']) ? $input['features'] : array_filter(array_map('trim', explode(',', $input['features'])));
                $data['features'] = json_encode(array_values($f));
            }
            if (isset($input['fuel_type'])) $data['fuel_type'] = sanitize($input['fuel_type']);
            if (isset($input['cc']))        $data['cc']        = sanitize($input['cc']);
            break;
        case 'restaurants':
            if (isset($input['cuisine'])) $data['cuisine'] = sanitize($input['cuisine']);
            if (isset($input['menu_highlights'])) {
                $m = is_array($input['menu_highlights']) ? $input['menu_highlights'] : array_filter(array_map('trim', explode(',', $input['menu_highlights'])));
                $data['menu_highlights'] = json_encode(array_values($m));
            }
            break;
        case 'attractions':
            if (isset($input['opening_hours'])) $data['opening_hours'] = sanitize($input['opening_hours']);
            if (isset($input['best_time']))     $data['best_time']     = sanitize($input['best_time']);
            break;
        case 'buses':
            if (isset($input['operator']))       $data['operator']       = sanitize($input['operator']);
            if (isset($input['from_location']))  $data['from_location']  = sanitize($input['from_location']);
            if (isset($input['to_location']))    $data['to_location']    = sanitize($input['to_location']);
            if (isset($input['departure_time'])) $data['departure_time'] = sanitize($input['departure_time']);
            if (isset($input['arrival_time']))   $data['arrival_time']   = sanitize($input['arrival_time']);
            if (isset($input['duration']))       $data['duration']       = sanitize($input['duration']);
            break;
    }

    return $data;
}
