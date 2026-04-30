<?php
http_response_code(404);
$page_title = "404 - Page Not Found | CSNExplore";
$current_page = "404";
$page_meta = [
    'description' => 'The page you are looking for does not exist.',
    'canonical'   => '',
    'type'        => 'website',
];
require_once 'php/config.php';
require 'header.php';
?>

<main class="min-h-screen bg-white flex items-center justify-center py-20 px-4">
    <div class="max-w-xl w-full text-center">
        <h1 class="text-9xl font-serif font-black text-slate-100 mb-4 select-none">404</h1>
        <div class="relative -mt-16 mb-8">
            <span class="material-symbols-outlined text-6xl text-primary drop-shadow-md">location_off</span>
        </div>
        <h2 class="text-3xl md:text-4xl font-black text-slate-900 mb-4 tracking-tight">Oops! Looks like you're lost.</h2>
        <p class="text-slate-500 mb-8 text-lg">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
        <a href="<?php echo BASE_PATH; ?>/" class="inline-flex items-center gap-2 bg-primary text-white font-bold py-4 px-8 rounded-2xl hover:bg-orange-600 transition-all shadow-lg hover:shadow-primary/30 uppercase tracking-widest text-sm">
            <span class="material-symbols-outlined text-lg">home</span> Back to Homepage
        </a>
    </div>
</main>

<?php require 'footer.php'; ?>
