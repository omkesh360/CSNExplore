<?php
/**
 * Master script to regenerate all HTML pages with animations
 * This includes:
 * - All listing detail pages (stays, cars, bikes, restaurants, attractions, buses)
 * - All blog pages
 */

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   CSNExplore - Regenerate All Pages with Animations       ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$start_time = microtime(true);

// Step 1: Regenerate listing detail pages
echo "📄 STEP 1: Regenerating Listing Detail Pages\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
include 'php/regenerate-with-animations.php';
echo "\n";

// Step 2: Regenerate blog pages
echo "📝 STEP 2: Regenerating Blog Pages\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
include 'php/regenerate-blogs-with-animations.php';
echo "\n";

$end_time = microtime(true);
$duration = round($end_time - $start_time, 2);

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                    ✅ ALL DONE!                            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";
echo "⏱️  Total time: {$duration} seconds\n\n";
echo "🎨 All pages now include:\n";
echo "   • Smooth scroll-based animations\n";
echo "   • Hover effects on cards and images\n";
echo "   • Stagger delays for sequential animations\n";
echo "   • Mobile-optimized animations\n";
echo "   • Accessibility support\n\n";
echo "🚀 Your website is now fully animated!\n\n";
?>
