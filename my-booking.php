<?php
require_once 'php/config.php';
$page_title = 'My Bookings | CSNExplore';
$current_page = 'my-booking.php';

$page_meta = [
    'description' => "View and manage all your travel bookings with CSNExplore from one place.",
    'canonical'   => BASE_PATH . '/my-booking',
    'type'        => 'website'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfilePage",
  "name": "My Bookings | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/my-booking"
}
</script>';

require 'header.php';
?>
<main class="min-h-screen bg-slate-50 py-12">
  <div class="max-w-4xl mx-auto px-4">
    <div class="mb-8">
      <h1 class="text-3xl font-serif font-black text-slate-900 mb-2">My Bookings</h1>
      <p class="text-slate-500">View and manage all your bookings</p>
    </div>

    <!-- Bookings container - will be populated by JavaScript -->
    <div id="bookings-container" class="space-y-4">
      <div class="text-center py-8">
        <div class="inline-block">
          <div class="animate-spin">
            <span class="material-symbols-outlined text-4xl text-primary">hourglass_empty</span>
          </div>
        </div>
        <p class="text-slate-500 mt-3">Loading...</p>
      </div>
    </div>
  </div>
</main>

<script>
// Load user bookings
async function loadUserBookings() {
  try {
    // Get user from localStorage
    var userStr = localStorage.getItem('csn_user');
    var token = localStorage.getItem('csn_token');
    
    if (!userStr || !token) {
      // Not logged in - show login prompt
      document.getElementById('bookings-container').innerHTML = '<div class="bg-white rounded-2xl shadow-sm p-8 text-center">'
        + '<span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">lock</span>'
        + '<h2 class="text-xl font-bold text-slate-900 mb-2">Sign in to view your bookings</h2>'
        + '<p class="text-slate-500 mb-6">You need to be logged in to see your booking history</p>'
        + '<a href="<?php echo BASE_PATH; ?>/login" class="inline-block bg-primary text-white font-bold px-6 py-3 rounded-xl hover:bg-orange-600 transition-all">Sign In</a>'
        + '</div>';
      return;
    }

    var user = JSON.parse(userStr);
    
    // Fetch bookings from API with token
    var res = await fetch('<?php echo BASE_PATH; ?>/php/api/user_bookings.php?token=' + encodeURIComponent(token), {
      method: 'GET',
      headers: { 
        'Content-Type': 'application/json'
      }
    });
    
    var data = await res.json();
    
    if (!res.ok) {
      if (res.status === 401) {
        // Token expired - show login prompt
        localStorage.removeItem('csn_token');
        localStorage.removeItem('csn_user');
        document.getElementById('bookings-container').innerHTML = '<div class="bg-white rounded-2xl shadow-sm p-8 text-center">'
          + '<span class="material-symbols-outlined text-5xl text-slate-300 mb-3 block">lock</span>'
          + '<h2 class="text-xl font-bold text-slate-900 mb-2">Session expired</h2>'
          + '<p class="text-slate-500 mb-6">Please sign in again to view your bookings</p>'
          + '<a href="<?php echo BASE_PATH; ?>/login" class="inline-block bg-primary text-white font-bold px-6 py-3 rounded-xl hover:bg-orange-600 transition-all">Sign In</a>'
          + '</div>';
      } else {
        document.getElementById('bookings-container').innerHTML = '<div class="bg-white rounded-2xl shadow-sm p-8 text-center"><p class="text-red-600">' + escHtml(data.error || 'Failed to load bookings') + '</p></div>';
      }
      return;
    }

    if (!data || !data.length) {
      document.getElementById('bookings-container').innerHTML = '<div class="bg-white rounded-2xl shadow-sm p-8 text-center">'
        + '<span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">inbox</span>'
        + '<p class="text-slate-500">No bookings yet</p>'
        + '<p class="text-xs text-slate-400 mt-1">Start booking to see your reservations here</p>'
        + '</div>';
      return;
    }

    // Render bookings
    var html = data.map(function(b) {
      var sc = b.status === 'pending'
        ? 'bg-orange-100 text-orange-700'
        : b.status === 'completed'
        ? 'bg-green-100 text-green-700'
        : 'bg-red-100 text-red-700';
      var icon = b.status === 'pending' ? 'schedule' : b.status === 'completed' ? 'check_circle' : 'cancel';
      var catLabels = {stays:'Hotel Stay', cars:'Car Rental', bikes:'Bike Rental', restaurants:'Restaurant', attractions:'Attraction', buses:'Bus'};
      var catLabel = catLabels[b.service_type] || b.service_type || '—';
      var listingDisplay = b.listing_name || b.service_type || '—';
      var img = b.listing_image ? '<img src="' + escHtml(b.listing_image) + '" alt="' + escHtml(listingDisplay) + '" class="w-full h-48 object-cover rounded-xl mb-4">' : '';
      var checkinDisplay = b.checkin_date ? '<div><p class="text-xs text-slate-400 font-semibold">Check-in</p><p class="text-slate-900">' + escHtml(b.checkin_date) + '</p></div>' : '';
      var checkoutDisplay = b.checkout_date ? '<div><p class="text-xs text-slate-400 font-semibold">Check-out</p><p class="text-slate-900">' + escHtml(b.checkout_date) + '</p></div>' : '';
      return '<div class="bg-white rounded-2xl shadow-sm p-6 border border-slate-100 hover:shadow-md transition-shadow">'
        + img
        + '<div class="flex items-start justify-between mb-4">'
        + '<div>'
        + '<p class="font-bold text-lg text-slate-900">' + escHtml(listingDisplay) + '</p>'
        + '<p class="text-xs text-slate-400 mt-1">Booking #' + b.id + ' · ' + escHtml(b.created_at.split(' ')[0]) + '</p>'
        + '</div>'
        + '<span class="flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold ' + sc + '">'
        + '<span class="material-symbols-outlined text-sm">' + icon + '</span>' + b.status.charAt(0).toUpperCase() + b.status.slice(1)
        + '</span>'
        + '</div>'
        + '<div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm mb-4">'
        + '<div><p class="text-xs text-slate-400 font-semibold">Name</p><p class="text-slate-900">' + escHtml(b.full_name) + '</p></div>'
        + '<div><p class="text-xs text-slate-400 font-semibold">Phone</p><p class="text-slate-900">' + escHtml(b.phone) + '</p></div>'
        + (b.email ? '<div><p class="text-xs text-slate-400 font-semibold">Email</p><p class="text-slate-900">' + escHtml(b.email) + '</p></div>' : '')
        + '<div><p class="text-xs text-slate-400 font-semibold">Category</p><p class="text-slate-900">' + escHtml(catLabel) + '</p></div>'
        + (b.booking_date ? '<div><p class="text-xs text-slate-400 font-semibold">Date</p><p class="text-slate-900">' + escHtml(b.booking_date) + '</p></div>' : '')
        + '<div><p class="text-xs text-slate-400 font-semibold">People</p><p class="text-slate-900">' + b.number_of_people + '</p></div>'
        + checkinDisplay
        + checkoutDisplay
        + '</div>'
        + (b.notes ? '<div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600 border border-slate-100"><span class="font-semibold">Notes:</span> ' + escHtml(b.notes) + '</div>' : '')
        + '</div>';
    }).join('');
    
    document.getElementById('bookings-container').innerHTML = html;
  } catch(e) {
    console.error('Error:', e);
    document.getElementById('bookings-container').innerHTML = '<div class="bg-white rounded-2xl shadow-sm p-8 text-center"><p class="text-red-600">Failed to load bookings. Please try again.</p></div>';
  }
}

function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// Load bookings on page load
document.addEventListener('DOMContentLoaded', loadUserBookings);
</script>

<?php require 'footer.php'; ?>
