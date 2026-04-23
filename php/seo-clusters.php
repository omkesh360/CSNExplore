<?php
/**
 * php/seo-clusters.php — Topic cluster / pillar page link system
 *
 * Usage in blog-detail.php sidebar:
 *   require_once 'php/seo-clusters.php';
 *   echo generateClusterLinks($blog['category'], $blog['id']);
 *
 * Usage in static blog HTML generation:
 *   $clusterHtml = generateClusterLinksStatic($category, $currentId, $allBlogs);
 */

// ── Pillar definitions ────────────────────────────────────────────────────────
// Maps pillar slug → display title + keyword categories that belong to it
const PILLAR_MAP = [
    'ajanta-ellora-guide' => [
        'title'      => 'Ajanta & Ellora Complete Guide 2026',
        'url'        => '/blogs/ajanta-ellora-complete-guide',
        'icon'       => '🏛️',
        'categories' => ['Attractions', 'Heritage', 'History', 'Travel Guide', 'UNESCO'],
        'keywords'   => ['ajanta', 'ellora', 'caves', 'heritage', 'monument', 'fort', 'temple', 'museum'],
    ],
    'stays-guide' => [
        'title'      => 'Chhatrapati Sambhajinagar Stays Guide',
        'url'        => '/blogs/sambhajinagar-stays-complete-guide',
        'icon'       => '🏨',
        'categories' => ['Hotels', 'Stays', 'Homestay', 'Accommodation'],
        'keywords'   => ['hotel', 'stay', 'homestay', 'hostel', 'resort', 'accommodation', 'room', 'lodge'],
    ],
    'car-rental-guide' => [
        'title'      => 'Car & Bike Rental Guide Aurangabad',
        'url'        => '/blogs/car-bike-rental-aurangabad-guide',
        'icon'       => '🚗',
        'categories' => ['Car Rental', 'Bike Rental', 'Transport'],
        'keywords'   => ['car', 'bike', 'rental', 'self-drive', 'vehicle', 'scooter', 'motorcycle', 'taxi'],
    ],
    'food-guide' => [
        'title'      => 'Best Food & Restaurants Sambhajinagar',
        'url'        => '/blogs/best-food-restaurants-sambhajinagar',
        'icon'       => '🍽️',
        'categories' => ['Food', 'Restaurants', 'Dining', 'Street Food'],
        'keywords'   => ['restaurant', 'food', 'biryani', 'thali', 'cafe', 'dine', 'eat', 'cuisine'],
    ],
    'travel-tips' => [
        'title'      => 'Chhatrapati Sambhajinagar Travel Tips 2026',
        'url'        => '/blogs/sambhajinagar-travel-tips-2026',
        'icon'       => '✈️',
        'categories' => ['Travel Tips', 'Budget Travel', 'Solo Travel', 'Family Travel'],
        'keywords'   => ['travel', 'trip', 'visit', 'tour', 'guide', 'tips', 'budget', 'itinerary'],
    ],
];

/**
 * Detect which pillar a blog belongs to based on category + title keywords.
 */
function detectPillar(string $category, string $title): ?string {
    $titleLower = strtolower($title);
    foreach (PILLAR_MAP as $pillarSlug => $pillar) {
        // Category match
        foreach ($pillar['categories'] as $cat) {
            if (stripos($category, $cat) !== false) return $pillarSlug;
        }
        // Keyword match in title
        foreach ($pillar['keywords'] as $kw) {
            if (strpos($titleLower, $kw) !== false) return $pillarSlug;
        }
    }
    return null;
}

/**
 * Generate cluster sidebar HTML for blog-detail.php (dynamic, DB-driven).
 * Returns HTML string.
 */
function generateClusterLinks(string $category, int $currentId, int $limit = 8): string {
    if (!function_exists('getDB')) return '';
    try {
        $db = getDB();
        // Get related blogs in same category
        $related = $db->fetchAll(
            "SELECT id, title FROM blogs WHERE status='published' AND category = ? AND id != ? ORDER BY id ASC LIMIT ?",
            [$category, $currentId, $limit]
        );
        if (empty($related)) return '';

        $pillarSlug = null;
        foreach (PILLAR_MAP as $slug => $p) {
            foreach ($p['categories'] as $cat) {
                if (stripos($category, $cat) !== false) { $pillarSlug = $slug; break 2; }
            }
        }
        $pillar = $pillarSlug ? PILLAR_MAP[$pillarSlug] : null;

        $html  = '<aside class="bg-orange-50 border border-orange-200 rounded-2xl p-5 mb-6">';
        $html .= '<h3 class="text-sm font-black text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">';
        $html .= '<span class="text-[#ec5b13]">🔗</span> Related Guides</h3>';

        if ($pillar) {
            $html .= '<a href="' . htmlspecialchars($pillar['url']) . '" class="flex items-center gap-2 bg-[#ec5b13] text-white text-xs font-bold px-3 py-2 rounded-xl mb-3 hover:bg-orange-600 transition-colors">';
            $html .= $pillar['icon'] . ' ' . htmlspecialchars($pillar['title']) . ' →</a>';
        }

        $html .= '<ul class="space-y-2">';
        foreach ($related as $r) {
            $slug  = $r['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $r['title']), '-'));
            $html .= '<li><a href="/blogs/' . htmlspecialchars($slug) . '" class="text-sm text-slate-700 hover:text-[#ec5b13] transition-colors flex items-start gap-1.5">';
            $html .= '<span class="text-[#ec5b13] mt-0.5 shrink-0">›</span>';
            $html .= '<span class="line-clamp-2">' . htmlspecialchars($r['title']) . '</span></a></li>';
        }
        $html .= '</ul></aside>';
        return $html;
    } catch (Exception $e) {
        return '';
    }
}

/**
 * Generate cluster sidebar HTML for static blog generation (array-driven).
 * $allBlogs = full array of blog rows already fetched.
 */
function generateClusterLinksStatic(string $category, string $title, int $currentId, array $allBlogs, int $limit = 8): string {
    $pillarSlug = detectPillar($category, $title);
    $pillar     = $pillarSlug ? PILLAR_MAP[$pillarSlug] : null;

    // Filter related blogs: same category or same pillar keywords
    $related = [];
    foreach ($allBlogs as $b) {
        if ((int)$b['id'] === $currentId) continue;
        $match = stripos($b['category'] ?? '', $category) !== false;
        if (!$match && $pillarSlug) {
            $tl = strtolower($b['title'] ?? '');
            foreach (PILLAR_MAP[$pillarSlug]['keywords'] as $kw) {
                if (strpos($tl, $kw) !== false) { $match = true; break; }
            }
        }
        if ($match) { $related[] = $b; if (count($related) >= $limit) break; }
    }

    if (empty($related) && !$pillar) return '';

    $html  = '<aside style="background:#fff7ed;border:1px solid #fed7aa;border-radius:16px;padding:20px;margin-bottom:24px;">';
    $html .= '<h3 style="font-size:12px;font-weight:900;color:#1e293b;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 12px;display:flex;align-items:center;gap:6px;">🔗 Related Guides</h3>';

    if ($pillar) {
        $html .= '<a href="' . htmlspecialchars($pillar['url']) . '" style="display:flex;align-items:center;gap:6px;background:#ec5b13;color:#fff;font-size:12px;font-weight:700;padding:8px 12px;border-radius:10px;margin-bottom:12px;text-decoration:none;">';
        $html .= $pillar['icon'] . ' ' . htmlspecialchars($pillar['title']) . ' →</a>';
    }

    if (!empty($related)) {
        $html .= '<ul style="list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:8px;">';
        foreach ($related as $r) {
            $slug  = $r['id'] . '-' . strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $r['title']), '-'));
            $html .= '<li><a href="../blogs/' . htmlspecialchars($slug) . '.html" style="font-size:13px;color:#374151;text-decoration:none;display:flex;align-items:flex-start;gap:6px;">';
            $html .= '<span style="color:#ec5b13;margin-top:2px;flex-shrink:0;">›</span>';
            $html .= '<span>' . htmlspecialchars($r['title']) . '</span></a></li>';
        }
        $html .= '</ul>';
    }
    $html .= '</aside>';
    return $html;
}

/**
 * Generate geo-targeted internal link block for blog content.
 * Injects contextual links to /stays-ellora-caves, /cars-ajanta-tour etc.
 */
function generateGeoLinks(string $title): string {
    $tl   = strtolower($title);
    $links = [];

    if (strpos($tl, 'ajanta') !== false || strpos($tl, 'ellora') !== false || strpos($tl, 'caves') !== false) {
        $links[] = ['url' => '/cars-ajanta-tour',        'label' => 'Book Car for Ajanta Caves Tour'];
        $links[] = ['url' => '/stays-ellora-caves',      'label' => 'Hotels Near Ellora Caves'];
        $links[] = ['url' => '/listing/attractions',     'label' => 'All Attractions in Sambhajinagar'];
    }
    if (strpos($tl, 'hotel') !== false || strpos($tl, 'stay') !== false || strpos($tl, 'homestay') !== false) {
        $links[] = ['url' => '/listing/stays',           'label' => 'Browse All Hotels & Homestays'];
        $links[] = ['url' => '/stays-ellora-caves',      'label' => 'Stays Near Ellora Caves'];
    }
    if (strpos($tl, 'car') !== false || strpos($tl, 'bike') !== false || strpos($tl, 'rental') !== false) {
        $links[] = ['url' => '/listing/cars',            'label' => 'Car Rentals Sambhajinagar'];
        $links[] = ['url' => '/listing/bikes',           'label' => 'Bike Rentals Aurangabad'];
        $links[] = ['url' => '/cars-ajanta-tour',        'label' => 'Cars for Ajanta Day Trip'];
    }
    if (empty($links)) {
        $links[] = ['url' => '/listing/stays',           'label' => 'Hotels in Sambhajinagar'];
        $links[] = ['url' => '/listing/cars',            'label' => 'Car Rentals Aurangabad'];
    }

    $html  = '<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:14px;padding:16px;margin:24px 0;">';
    $html .= '<p style="font-size:12px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:0.06em;margin:0 0 10px;">📍 Book on CSNExplore</p>';
    $html .= '<div style="display:flex;flex-wrap:wrap;gap:8px;">';
    foreach (array_slice($links, 0, 4) as $l) {
        $html .= '<a href="' . htmlspecialchars($l['url']) . '" style="font-size:12px;font-weight:600;color:#ec5b13;background:#fff;border:1px solid #ec5b13;padding:6px 12px;border-radius:20px;text-decoration:none;">';
        $html .= htmlspecialchars($l['label']) . '</a>';
    }
    $html .= '</div></div>';
    return $html;
}
