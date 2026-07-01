<?php
$page_title       = "Gallery | CSNExplore";
$current_page     = "gallery.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Explore our fleet, beautiful destinations and happy moments from our amazing journeys.",
    'canonical'   => "https://csnexplore.com/gallery",
    'type'        => 'website',
    'image'       => 'https://csnexplore.com/images/travelhub.png',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => '/'],
        ['name' => 'Gallery', 'url' => '/gallery'],
    ],
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Gallery | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "' . $page_meta['canonical'] . '"
}
</script>
<link rel="stylesheet" href="' . BASE_PATH . '/css/gallery.css" />
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet" />';
include 'header.php';

$db = getDB();

$limit = 10;
$stays = $db->fetchAll("SELECT id, name, location, image, slug FROM stays WHERE is_active=1 ORDER BY RAND() LIMIT $limit");
foreach($stays as &$s) { $s['cat'] = 'hotels'; $s['cat_name'] = 'Hotels'; }
$dines = $db->fetchAll("SELECT id, name, location, image, slug FROM restaurants WHERE is_active=1 ORDER BY RAND() LIMIT $limit");
foreach($dines as &$d) { $d['cat'] = 'restaurants'; $d['cat_name'] = 'Restaurants'; }
$attrs = $db->fetchAll("SELECT id, name, location, image, slug FROM attractions WHERE is_active=1 ORDER BY RAND() LIMIT $limit");
foreach($attrs as &$a) { $a['cat'] = 'attractions'; $a['cat_name'] = 'Attractions'; }
$cars = $db->fetchAll("SELECT id, name, location, image, slug FROM cars WHERE is_active=1 ORDER BY RAND() LIMIT $limit");
foreach($cars as &$c) { $c['cat'] = 'cars'; $c['cat_name'] = 'Cars'; }
$bikes = $db->fetchAll("SELECT id, name, location, image, slug FROM bikes WHERE is_active=1 ORDER BY RAND() LIMIT $limit");
foreach($bikes as &$b) { $b['cat'] = 'bikes'; $b['cat_name'] = 'Bikes'; }

$all_items = array_merge($stays, $dines, $attrs, $cars, $bikes);
shuffle($all_items);
?>
<!-- ===== HERO ===== -->
  <section class="hero">
    <div class="hero-bg-img"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="hero-title">Memories On <span class="hero-accent">Every Road</span></h1>
      <p class="hero-sub">Thousands of trips. Countless smiles. One family of travellers who chose the road less taken.
      </p>
      <div class="hero-btns">
        <a href="#gallery" class="hero-btn-primary">Explore Gallery</a>
      </div>
    </div>
  </section>

  <!-- ===== GALLERY HEADING + FILTER ===== -->
  <section class="gallery-intro" id="gallery">
    <div class="gallery-intro-inner">
      <h2 class="gallery-heading">Memories, Journeys <span class="maroon-accent">&amp; Experiences</span></h2>
      <p class="gallery-desc">Explore our fleet, beautiful destinations and happy moments from our amazing journeys.</p>
    </div>
    <div class="filter-wrap">
      <div class="filter-tabs" id="filterTabs">
        <button class="filter-btn active" data-filter="all">All</button>
        <button class="filter-btn" data-filter="hotels">Hotels</button>
        <button class="filter-btn" data-filter="restaurants">Restaurants</button>
        <button class="filter-btn" data-filter="attractions">Attractions</button>
        <button class="filter-btn" data-filter="cars">Cars</button>
        <button class="filter-btn" data-filter="bikes">Bikes</button>
      </div>
    </div>
  </section>

  <!-- ===== PHOTO GRID ===== -->
  <section class="gallery-section">
    <div class="gallery-grid" id="galleryGrid">
      <?php foreach($all_items as $item): ?>
      <div class="gallery-item" data-category="<?php echo $item['cat']; ?>">
        <a href="<?php echo BASE_PATH; ?>/listing-detail/<?php echo urlencode($item['slug']); ?>" class="gallery-card" style="display:block; height:100%;">
          <img src="<?php echo htmlspecialchars(get_working_image_url($item['image'])); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy" />
          <div class="card-overlay">
            <p class="card-title"><?php echo htmlspecialchars($item['name']); ?></p>
            <p class="card-loc"><?php echo htmlspecialchars($item['location']); ?> &bull; <?php echo $item['cat_name']; ?></p>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <script src="<?php echo BASE_PATH; ?>/js/gallery.js"></script>
<?php include 'footer.php'; ?>
