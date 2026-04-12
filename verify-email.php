<?php
$page_title = "Verify Email | CSNExplore";
$current_page = "verify-email.php";
require_once 'php/config.php';

$page_meta = [
    'description' => "Verify your email address for CSNExplore to activate your account and start your travel journey.",
    'canonical'   => BASE_PATH . '/verify-email',
    'type'        => 'website'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Verify Email | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/verify-email"
}
</script>';
require 'header.php';
?>
<div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-16">
  <div class="w-full max-w-md text-center space-y-6">

    <!-- Spinner shown while verifying -->
    <div id="state-loading">
      <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
      <h2 class="text-2xl font-bold text-slate-800">Verifying your email...</h2>
      <p class="text-slate-500 mt-2">Please wait a moment.</p>
    </div>

    <!-- Success state -->
    <div id="state-success" class="hidden">
      <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-green-600 text-4xl">check_circle</span>
      </div>
      <h2 class="text-2xl font-bold text-slate-800">Email Verified!</h2>
      <p class="text-slate-500 mt-2 mb-6">Your account is now active. You're being signed in...</p>
      <a href="index.php" class="inline-block bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-orange-600 transition-all">Go to Homepage</a>
    </div>

    <!-- Error state -->
    <div id="state-error" class="hidden">
      <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <span class="material-symbols-outlined text-red-500 text-4xl">error</span>
      </div>
      <h2 class="text-2xl font-bold text-slate-800">Verification Failed</h2>
      <p id="error-msg" class="text-slate-500 mt-2 mb-6">This link is invalid or has expired.</p>
      <a href="register" class="inline-block bg-primary text-white font-bold px-8 py-3 rounded-xl hover:bg-orange-600 transition-all">Create New Account</a>
    </div>

  </div>
</div>

<script>
(async function() {
  const token = new URLSearchParams(window.location.search).get('token');
  if (!token) { showError('No verification token found.'); return; }

  try {
    const res  = await fetch('php/api/auth.php?action=verify_email&token=' + encodeURIComponent(token));
    const data = await res.json();

    if (data.success && data.token) {
      localStorage.setItem('csn_token', data.token);
      localStorage.setItem('csn_user', JSON.stringify(data.user));
      show('state-success');
      setTimeout(() => window.location.replace('index.php'), 2000);
    } else {
      showError(data.error || 'Verification failed.');
    }
  } catch(e) {
    showError('Something went wrong. Please try again.');
  }

  function show(id) {
    document.getElementById('state-loading').classList.add('hidden');
    document.getElementById(id).classList.remove('hidden');
  }
  function showError(msg) {
    document.getElementById('error-msg').textContent = msg;
    show('state-error');
  }
})();
</script>
<?php require 'footer.php'; ?>
