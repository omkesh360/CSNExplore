<?php
require_once 'php/config.php';
$page_title   = "Personalized Trip Planner | CSNExplore";
$current_page = "suggestor.php";

$page_meta = [
    'description' => 'Plan your perfect trip to Chhatrapati Sambhajinagar with our AI-powered Personalized Trip Planner. Consult local experts for free itineraries.',
    'canonical'   => BASE_PATH . '/suggestor',
    'type'        => 'website',
    'image'       => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=1600'
];

$extra_head = '<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "Trip Planner | CSNExplore",
  "description": "' . $page_meta['description'] . '",
  "url": "https://csnexplore.com/suggestor",
  "applicationCategory": "TravelApplication",
  "operatingSystem": "All"
}
</script>';

// Handle form submission — PRG pattern to prevent duplicate posts & stuck success screen
$success = isset($_GET['submitted']) && $_GET['submitted'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_plan'])) {
    try {
        $db = getDB();
        
        // Validate required fields
        $full_name = sanitize($_POST['full_name'] ?? '');
        $email     = sanitize($_POST['email']     ?? '');
        $phone     = sanitize($_POST['phone']     ?? '');
        
        if (empty($full_name) || empty($email) || empty($phone)) {
            $form_error = 'Please fill in your name, email, and phone number.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $form_error = 'Please enter a valid email address.';
        } else {
            // Process Interests
            $interests = [];
            if (!empty($_POST['interests']) && is_array($_POST['interests'])) {
                foreach ($_POST['interests'] as $cat) {
                    $subKey = 'sub_' . strtolower($cat);
                    $subs   = isset($_POST[$subKey]) && is_array($_POST[$subKey]) ? implode(', ', $_POST[$subKey]) : '';
                    $interests[] = $cat . ($subs ? " ($subs)" : '');
                }
            }
            
            // Process Travel Details
            $travel_mode    = sanitize($_POST['travel_mode'] ?? '');
            $travel_details = [];
            if ($travel_mode === 'Car') {
                $travel_details[] = 'Service: ' . sanitize($_POST['car_service_type'] ?? '');
                $travel_details[] = 'Type: '    . sanitize($_POST['car_sub_type']      ?? '');
            } elseif ($travel_mode === 'Bike') {
                $travel_details[] = 'Type: ' . sanitize($_POST['bike_sub_type'] ?? '');
            }

            $data = [
                'full_name'      => $full_name,
                'email'          => $email,
                'phone'          => $phone,
                'interests'      => implode(' | ', $interests),
                'stay_type'      => sanitize($_POST['stay_type']   ?? ''),
                'travel_mode'    => $travel_mode,
                'travel_details' => implode(' | ', $travel_details),
                'extra_notes'    => sanitize($_POST['extra_notes'] ?? ''),
            ];
            
            $cols = implode(', ', array_keys($data));
            $vals = implode(', ', array_fill(0, count($data), '?'));
            $db->query("INSERT INTO trip_requests ($cols) VALUES ($vals)", array_values($data));
            
            // Get the inserted ID for email notification
            $tripRequestId = $db->lastInsertId();
            
            // Send email notifications in background (non-blocking)
            // This happens AFTER redirect so user doesn't wait
            if (function_exists('fastcgi_finish_request')) {
                // PRG — redirect to success state IMMEDIATELY
                header('Location: ' . BASE_PATH . '/suggestor?submitted=1&id=' . $tripRequestId);
                fastcgi_finish_request(); // Close connection, continue processing
                
                // Send emails after user sees success page
                require_once 'php/services/EmailService.php';
                try {
                    EmailService::sendTripRequestEmails($tripRequestId);
                } catch (Exception $emailError) {
                    error_log('Trip request email failed: ' . $emailError->getMessage());
                }
            } else {
                // Fallback: redirect immediately, emails will be sent via AJAX
                header('Location: ' . BASE_PATH . '/suggestor?submitted=1&id=' . $tripRequestId);
            }
            exit;
        }
    } catch (Exception $e) {
        error_log('Trip Planner submit error: ' . $e->getMessage());
        error_log('Trip Planner error trace: ' . $e->getTraceAsString());
        
        // Show detailed error in development
        if (defined('APP_ENV') && APP_ENV === 'local') {
            $form_error = 'Error: ' . $e->getMessage();
        } else {
            $form_error = 'Something went wrong. Please try again.';
        }
    }
}

require 'header.php';
?>

<main class="bg-gray-50 min-h-screen">
    <!-- Premium Hero Section -->
    <section class="relative h-[450px] flex items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?q=80&w=1600&auto=format&fit=crop" 
                 alt="Travel Planning" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(0,0,0,0.85)_100%)] z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-transparent to-[#0a0705] z-10"></div>
        </div>
        
        <div class="relative z-10 text-center px-5 max-w-4xl mx-auto">
            <div data-reveal="scale" class="mb-6">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-white text-xs font-bold uppercase tracking-[0.2em] mb-6 animate-fade-in">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                AI-Powered Planner
            </div>
            <h1 class="text-5xl md:text-7xl font-serif font-black text-white mb-6 leading-tight">
                Plan Your <span class="text-primary italic">Dream</span> Trip
            </h1>
                <p class="text-xl text-white/90 leading-relaxed max-w-2xl mx-auto font-medium">
                    Talk to our local trip experts. We'll craft a customized, fully personalized itinerary for your entire trip at no extra cost.
                </p>
            </div>
        </div>
    </section>

    <!-- Interactive Multistep Planner -->
    <section class="max-w-[1140px] mx-auto px-5 -mt-24 pb-20 relative z-20">
        <div class="bg-white/95 rounded-[40px] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.2)] p-8 md:p-12 border border-white/40 backdrop-blur-xl">
            
            <?php if ($success): ?>
            <div data-reveal class="text-center py-20 relative overflow-hidden">
                <!-- Confetti particles -->
                <div id="confetti-container" class="absolute inset-0 pointer-events-none"></div>
                
                <div class="bg-green-100 text-green-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg shadow-green-200/50 animate-bounce" style="animation-duration: 2s">
                    <span class="material-symbols-outlined text-5xl">verified</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-serif font-black text-gray-900 mb-4">Request Received!</h2>
                <p class="text-gray-500 text-lg mb-10 max-w-md mx-auto leading-relaxed">
                    One of our <span class="text-primary font-bold">Local Trip Experts</span> will contact you within 2 hours with a personalized plan.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="<?php echo BASE_PATH; ?>/" class="bg-primary text-white font-black py-4 px-10 rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-primary/30 uppercase tracking-widest text-sm">
                        Back to Home
                    </a>
                    <a href="<?php echo BASE_PATH; ?>/listing" class="bg-gray-100 text-gray-700 font-bold py-4 px-10 rounded-2xl hover:bg-gray-200 transition-all text-sm uppercase tracking-widest">
                        Explore Listings
                    </a>
                </div>

                <script>
                // Send emails in background via AJAX (non-blocking)
                <?php if (isset($_GET['id'])): ?>
                fetch('<?php echo BASE_PATH; ?>/send-pending-emails.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'id=<?php echo (int)$_GET['id']; ?>'
                }).catch(function(e) {
                    console.log('Background email sending initiated');
                });
                <?php endif; ?>
                
                function createConfetti() {
                    const container = document.getElementById('confetti-container');
                    const colors = ['#ec5b13', '#ff8c42', '#34d399', '#3b82f6', '#f59e0b'];
                    for (let i = 0; i < 50; i++) {
                        const confetti = document.createElement('div');
                        confetti.classList.add('confetti-piece');
                        confetti.style.left = Math.random() * 100 + '%';
                        confetti.style.top = -10 + 'px';
                        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                        confetti.style.width = Math.random() * 8 + 4 + 'px';
                        confetti.style.height = Math.random() * 15 + 10 + 'px';
                        confetti.style.opacity = Math.random();
                        confetti.style.transform = `rotate(${Math.random() * 360}deg)`;
                        confetti.style.position = 'absolute';
                        confetti.style.zIndex = '0';
                        
                        container.appendChild(confetti);
                        
                        const animation = confetti.animate([
                            { transform: `translate3d(0, 0, 0) rotate(0deg)`, opacity: 1 },
                            { transform: `translate3d(${(Math.random() - 0.5) * 200}px, ${window.innerHeight * 0.8}px, 0) rotate(${Math.random() * 720}deg)`, opacity: 0 }
                        ], {
                            duration: Math.random() * 2000 + 3000,
                            easing: 'cubic-bezier(0, .9, .57, 1)',
                            delay: Math.random() * 2000
                        });
                        
                        animation.onfinish = () => confetti.remove();
                    }
                }
                setTimeout(createConfetti, 500);
                </script>
            </div>
            <?php else: ?>
            
            <form id="trip-planner-form" method="POST" action="" class="space-y-12">
                <!-- Progress Bar -->
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span id="step-count-badge" class="text-xs font-black inline-block py-2 px-4 uppercase rounded-full text-white bg-slate-900 shadow-lg shadow-black/10 transition-all duration-500">
                                STEP 1 / 3
                            </span>
                        </div>
                        <div class="text-right">
                            <span id="progress-percentage" class="text-xs font-bold inline-block text-primary">
                                33%
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100">
                        <div id="progress-bar-fill" style="width:33%" class="shadow-[0_0_15px_rgba(236,91,19,0.4)] flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-primary to-orange-400 transition-all duration-700 ease-[cubic-bezier(0.34,1.56,0.64,1)]"></div>
                    </div>
                </div>

                <!-- Step 1: Interests -->
                <div id="step-1" class="step-container transition-all duration-500 animate-in fade-in slide-in-from-right-10">
                    <div class="mb-10 text-center">
                        <h3 class="text-3xl font-serif font-black text-gray-900 mb-2">What kind of places do you love?</h3>
                        <p class="text-gray-500">Choose your interests to help us tailor your experience.</p>
                    </div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
                        <label class="relative cursor-pointer group rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 block h-48 md:h-56 transform hover:-translate-y-1">
                            <input type="checkbox" name="interests[]" value="Heritage" class="peer sr-only interest-toggle" data-target="sub-heritage">
                            <img src="images/uploads/daulatabad.png" alt="Historic Places" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent peer-checked:via-primary/50 transition-colors duration-300"></div>
                            <div class="absolute inset-0 border-[3px] border-transparent peer-checked:border-primary rounded-[2rem] transition-colors shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)]"></div>
                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity z-10 bg-white/20 backdrop-blur-md rounded-full shadow-lg">
                                <span class="material-symbols-outlined text-white text-2xl drop-shadow-md p-1">check_circle</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 z-10 flex flex-col items-start translate-y-2 group-hover:translate-y-0 transition-transform">
                                <span class="material-symbols-outlined text-3xl mb-1 text-white/80 group-hover:text-white transition-colors drop-shadow-md">castle</span>
                                <span class="font-bold text-white text-md md:text-lg leading-tight tracking-wide drop-shadow-lg">Historic Places</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 block h-48 md:h-56 transform hover:-translate-y-1">
                            <input type="checkbox" name="interests[]" value="Temple" class="peer sr-only interest-toggle" data-target="sub-temple">
                            <img src="images/uploads/grishneshwar.png" alt="Mandir & Temples" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent peer-checked:via-primary/50 transition-colors duration-300"></div>
                            <div class="absolute inset-0 border-[3px] border-transparent peer-checked:border-primary rounded-[2rem] transition-colors shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)]"></div>
                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity z-10 bg-white/20 backdrop-blur-md rounded-full shadow-lg">
                                <span class="material-symbols-outlined text-white text-2xl drop-shadow-md p-1">check_circle</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 z-10 flex flex-col items-start translate-y-2 group-hover:translate-y-0 transition-transform">
                                <span class="material-symbols-outlined text-3xl mb-1 text-white/80 group-hover:text-white transition-colors drop-shadow-md">temple_hindu</span>
                                <span class="font-bold text-white text-md md:text-lg leading-tight tracking-wide drop-shadow-lg">Mandir & Temples</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 block h-48 md:h-56 transform hover:-translate-y-1">
                            <input type="checkbox" name="interests[]" value="Nature" class="peer sr-only interest-toggle" data-target="sub-nature">
                            <img src="images/uploads/ellora.png" alt="Nature & Caves" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent peer-checked:via-primary/50 transition-colors duration-300"></div>
                            <div class="absolute inset-0 border-[3px] border-transparent peer-checked:border-primary rounded-[2rem] transition-colors shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)]"></div>
                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity z-10 bg-white/20 backdrop-blur-md rounded-full shadow-lg">
                                <span class="material-symbols-outlined text-white text-2xl drop-shadow-md p-1">check_circle</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 z-10 flex flex-col items-start translate-y-2 group-hover:translate-y-0 transition-transform">
                                <span class="material-symbols-outlined text-3xl mb-1 text-white/80 group-hover:text-white transition-colors drop-shadow-md">forest</span>
                                <span class="font-bold text-white text-md md:text-lg leading-tight tracking-wide drop-shadow-lg">Nature & Caves</span>
                            </div>
                        </label>

                        <label class="relative cursor-pointer group rounded-[2rem] overflow-hidden shadow-md hover:shadow-2xl transition-all duration-500 block h-48 md:h-56 transform hover:-translate-y-1">
                            <input type="checkbox" name="interests[]" value="Food" class="peer sr-only interest-toggle" data-target="sub-food">
                            <img src="images/uploads/indian-thali.jpg" alt="Local Cuisine" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent peer-checked:via-primary/50 transition-colors duration-300"></div>
                            <div class="absolute inset-0 border-[3px] border-transparent peer-checked:border-primary rounded-[2rem] transition-colors shadow-[inset_0_0_0_1px_rgba(255,255,255,0.2)]"></div>
                            <div class="absolute top-4 right-4 opacity-0 peer-checked:opacity-100 transition-opacity z-10 bg-white/20 backdrop-blur-md rounded-full shadow-lg">
                                <span class="material-symbols-outlined text-white text-2xl drop-shadow-md p-1">check_circle</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 z-10 flex flex-col items-start translate-y-2 group-hover:translate-y-0 transition-transform">
                                <span class="material-symbols-outlined text-3xl mb-1 text-white/80 group-hover:text-white transition-colors drop-shadow-md">restaurant</span>
                                <span class="font-bold text-white text-md md:text-lg leading-tight tracking-wide drop-shadow-lg">Local Cuisine</span>
                            </div>
                        </label>
                    </div>

                    <!-- Sub-options for Interests -->
                    <div id="interests-sub-options" class="space-y-6">
                        <div id="sub-heritage" class="hidden animate-in fade-in slide-in-from-top-4">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 block">Select Heritage Interests</label>
                            <div class="flex flex-wrap gap-3">
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_heritage[]" value="Forts" class="sr-only"> Forts & Palaces
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_heritage[]" value="Museums" class="sr-only"> Museums
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_heritage[]" value="AncientGates" class="sr-only"> Ancient Gates
                                </label>
                            </div>
                        </div>

                        <div id="sub-temple" class="hidden animate-in fade-in slide-in-from-top-4">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 block">Select Temple Interests</label>
                            <div class="flex flex-wrap gap-3">
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_temple[]" value="Jyotirlinga" class="sr-only"> Jyotirlinga (Grishneshwar)
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_temple[]" value="AncientTemples" class="sr-only"> Ancient Temples
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_temple[]" value="Pilgrimage" class="sr-only"> Pilgrimage Sites
                                </label>
                            </div>
                        </div>

                        <div id="sub-nature" class="hidden animate-in fade-in slide-in-from-top-4">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 block">Select Nature Interests</label>
                            <div class="flex flex-wrap gap-3">
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_nature[]" value="Ellora" class="sr-only"> Ellora Caves
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_nature[]" value="Ajanta" class="sr-only"> Ajanta Caves
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_nature[]" value="Lakes" class="sr-only"> Lakes & Gardens
                                </label>
                            </div>
                        </div>

                        <div id="sub-food" class="hidden animate-in fade-in slide-in-from-top-4">
                            <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-4 block">Select Cuisine Preferences</label>
                            <div class="flex flex-wrap gap-3">
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_food[]" value="Maharashtrian" class="sr-only"> Maharashtrian
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_food[]" value="Mughlai" class="sr-only"> Mughlai / Non-Veg
                                </label>
                                <label class="px-4 py-2 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:bg-primary has-[:checked]:text-white has-[:checked]:border-primary transition-all text-sm font-bold">
                                    <input type="checkbox" name="sub_food[]" value="StreetFood" class="sr-only"> Street Food
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Stay & Travel -->
                <div id="step-2" class="step-container hidden transition-all duration-500">
                    <div class="mb-10 text-center">
                        <h3 class="text-3xl font-serif font-black text-gray-900 mb-2">Stay & Travel Preferences</h3>
                        <p class="text-gray-500">How would you like to experience your journey?</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                        <!-- Stay Type -->
                        <div>
                            <label class="text-xs font-black uppercase tracking-widest text-primary mb-6 block">Accommodation Style</label>
                            <div class="grid grid-cols-1 gap-4">
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="stay_type" value="Luxury" class="sr-only">
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">bedroom_parent</span></div>
                                    <div>
                                        <div class="font-bold text-gray-900">Luxury Hotels</div>
                                        <div class="text-xs text-gray-500">Premium 4-5 star resorts</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="stay_type" value="Budget" class="sr-only" checked>
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">bed</span></div>
                                    <div>
                                        <div class="font-bold text-gray-900">Budget Friendly</div>
                                        <div class="text-xs text-gray-500">Comfortable stays at best price</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="stay_type" value="Homestay" class="sr-only">
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">home_work</span></div>
                                    <div>
                                        <div class="font-bold text-gray-900">Local Homestay</div>
                                        <div class="text-xs text-gray-500">Authentic local living experience</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Travel Mode -->
                        <div>
                            <label class="text-xs font-black uppercase tracking-widest text-primary mb-6 block">Prefered Travel Mode</label>
                            <div class="grid grid-cols-1 gap-4 mb-6">
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="travel_mode" value="Car" class="sr-only travel-toggle" data-target="sub-car" checked>
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">directions_car</span></div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900">Private Car</div>
                                        <div class="text-xs text-gray-500">Convenient & private for families</div>
                                    </div>
                                    <div class="text-primary opacity-0 has-[:checked]:opacity-100"><span class="material-symbols-outlined">check_circle</span></div>
                                </label>
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="travel_mode" value="Bike" class="sr-only travel-toggle" data-target="sub-bike">
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">motorcycle</span></div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900">Bike Rental</div>
                                        <div class="text-xs text-gray-500">Adventure on two wheels</div>
                                    </div>
                                    <div class="text-primary opacity-0 has-[:checked]:opacity-100"><span class="material-symbols-outlined">check_circle</span></div>
                                </label>
                                <label class="flex items-center gap-4 p-5 rounded-2xl border-2 border-gray-100 bg-gray-50 cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                    <input type="radio" name="travel_mode" value="Bus" class="sr-only travel-toggle" data-target="none">
                                    <div class="bg-white p-3 rounded-xl shadow-sm"><span class="material-symbols-outlined text-primary">directions_bus</span></div>
                                    <div class="flex-1">
                                        <div class="font-bold text-gray-900">City Bus / Public</div>
                                        <div class="text-xs text-gray-500">Economical & sustainable</div>
                                    </div>
                                    <div class="text-primary opacity-0 has-[:checked]:opacity-100"><span class="material-symbols-outlined">check_circle</span></div>
                                </label>
                            </div>

                            <!-- Sub-options for Travel -->
                            <div id="sub-car" class="animate-in fade-in slide-in-from-top-4 space-y-6">
                                <!-- Service Type Selection -->
                                <div>
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3 block text-center">Driver Preference</label>
                                    <div class="flex justify-center gap-4">
                                        <label class="flex-1 max-w-[150px] flex flex-col items-center p-4 rounded-2xl border-2 border-gray-100 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all shadow-sm">
                                            <input type="radio" name="car_service_type" value="SelfDrive" class="sr-only" checked>
                                            <span class="material-symbols-outlined text-gray-400 mb-2">person_off</span>
                                            <span class="font-bold text-gray-800 text-sm">Self Driven</span>
                                        </label>
                                        <label class="flex-1 max-w-[150px] flex flex-col items-center p-4 rounded-2xl border-2 border-gray-100 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all shadow-sm">
                                            <input type="radio" name="car_service_type" value="WithDriver" class="sr-only">
                                            <span class="material-symbols-outlined text-gray-400 mb-2">hail</span>
                                            <span class="font-bold text-gray-800 text-sm">With Driver</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Vehicle Type Selection -->
                                <div>
                                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-3 block text-center">Select Vehicle Type</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                            <input type="radio" name="car_sub_type" value="SUV" class="sr-only">
                                            <span class="font-bold text-gray-800 text-sm">SUV</span>
                                            <span class="text-[10px] text-gray-500">Scorpio/N</span>
                                        </label>
                                        <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                            <input type="radio" name="car_sub_type" value="Sedan" class="sr-only" checked>
                                            <span class="font-bold text-gray-800 text-sm">Sedan</span>
                                            <span class="text-[10px] text-gray-500">Honda City</span>
                                        </label>
                                        <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                            <input type="radio" name="car_sub_type" value="MUV" class="sr-only">
                                            <span class="font-bold text-gray-800 text-sm">MUV</span>
                                            <span class="text-[10px] text-gray-500">Ertiga/Innova</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div id="sub-bike" class="hidden animate-in fade-in slide-in-from-top-4 space-y-3">
                                <label class="text-xs font-black uppercase tracking-widest text-gray-400 mb-2 block text-center">Select Bike Type</label>
                                <div class="grid grid-cols-3 gap-3">
                                    <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                        <input type="radio" name="bike_sub_type" value="Cruiser" class="sr-only">
                                        <span class="font-bold text-gray-800 text-sm">Cruiser</span>
                                        <span class="text-[10px] text-gray-500">RE Bullet</span>
                                    </label>
                                    <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                        <input type="radio" name="bike_sub_type" value="Scooter" class="sr-only" checked>
                                        <span class="font-bold text-gray-800 text-sm">Scooter</span>
                                        <span class="text-[10px] text-gray-500">Activa/Access</span>
                                    </label>
                                    <label class="flex flex-col items-center p-3 rounded-xl border border-gray-200 bg-white cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5 transition-all">
                                        <input type="radio" name="bike_sub_type" value="Sports" class="sr-only">
                                        <span class="font-bold text-gray-800 text-sm">Sports</span>
                                        <span class="text-[10px] text-gray-500">Pulsar/Duke</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Personal Details -->
                <div id="step-3" class="step-container hidden transition-all duration-500">
                    <div class="mb-10 text-center">
                        <h3 class="text-3xl font-serif font-black text-gray-900 mb-2">Final Details</h3>
                        <p class="text-gray-500 mb-3">Where should we send your personalized itinerary?</p>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-600 border border-green-100 rounded-full text-xs font-bold uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[14px]">lock_open</span> No signup or login required
                        </span>
                        <?php if (!empty($form_error)): ?>
                        <div class="mt-4 flex items-center gap-2 justify-center px-4 py-3 bg-red-50 border border-red-100 rounded-2xl text-red-600 text-sm font-bold">
                            <span class="material-symbols-outlined text-base">error</span>
                            <?php echo htmlspecialchars($form_error); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Full Name</label>
                            <input name="full_name" type="text" required placeholder="John Doe"
                                   class="w-full rounded-2xl px-6 py-4 bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400 text-sm font-medium"/>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Email Address</label>
                            <input name="email" type="email" required placeholder="john@example.com"
                                   class="w-full rounded-2xl px-6 py-4 bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400 text-sm font-medium"/>
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">WhatsApp / Phone</label>
                            <input name="phone" type="tel" required placeholder="+91 00000 00000"
                                   class="w-full rounded-2xl px-6 py-4 bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400 text-sm font-medium"/>
                        </div>
                        <div class="md:col-span-2 space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Any special requests?</label>
                            <textarea name="extra_notes" rows="4" placeholder="e.g. Traveling with kids, elderly parents, or have specific dietary needs?"
                                      class="w-full rounded-2xl px-6 py-4 bg-gray-50 border border-gray-200 focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-gray-400 text-sm font-medium resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between border-t border-gray-100 pt-10">
                    <button type="button" id="prev-btn" class="hidden flex items-center gap-2 text-gray-400 font-bold hover:text-primary transition-all">
                        <span class="material-symbols-outlined">arrow_back</span> Back
                    </button>
                    <div class="flex-grow"></div>
                    <button type="button" id="next-btn" class="bg-primary text-white font-black py-4 px-10 rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-primary/30 uppercase tracking-widest flex items-center gap-2">
                        Next <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                    <div class="flex flex-col items-end gap-2">
                        <button type="submit" name="submit_plan" id="submit-btn" class="hidden bg-primary text-white font-black py-4 px-10 rounded-2xl hover:bg-orange-600 transition-all shadow-xl shadow-primary/30 uppercase tracking-widest flex items-center gap-2">
                            Get Plan <span class="material-symbols-outlined">magic_button</span>
                        </button>
                    </div>
                </div>
            </form>
            <?php endif; ?>
        </div>
    </section>

    <!-- Expert section & Dynamic Images -->
    <section class="max-w-[1140px] mx-auto px-5 py-24 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-reveal="left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100/50 border border-blue-200 text-blue-600 font-bold text-xs uppercase tracking-widest mb-6 backdrop-blur-sm">
                    <span class="material-symbols-outlined text-sm">support_agent</span>
                    Trip Expert
                </div>
                <h2 class="text-4xl md:text-5xl font-serif font-black text-gray-900 mb-8 leading-tight">
                    Plan your trip <span class="text-primary italic">now</span>
                </h2>
                <p class="text-gray-500 text-lg leading-relaxed mb-6">
                    Not sure where to go? Let us plan it.
                </p>
                <p class="text-gray-500 text-base leading-relaxed mb-10">
                    Talk to our local trip experts. We'll craft a customized, fully personalized itinerary for your entire trip—including cars, hotels, and ancient site guides—at no extra cost.
                </p>
                
                <a href="#trip-planner-form" onclick="document.getElementById('trip-planner-form').scrollIntoView({behavior: 'smooth'})" class="inline-flex items-center gap-2 bg-slate-900 text-white font-bold py-4 px-8 rounded-2xl hover:bg-primary transition-all shadow-xl hover:shadow-primary/30 uppercase tracking-widest text-sm group">
                    Start Planning <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            
            <div data-reveal="right" class="relative h-[450px] w-full flex items-center justify-center" id="suggestor-stack-wrap">
                <style>
                    .img-stack-card {
                        position: absolute;
                        width: 80%;
                        height: 380px;
                        border-radius: 2rem;
                        overflow: hidden;
                        border: 3px solid rgba(255,255,255,0.85);
                        will-change: transform, opacity;
                        transition: transform 0.6s cubic-bezier(0.22,1,0.36,1),
                                    opacity   0.6s cubic-bezier(0.22,1,0.36,1),
                                    box-shadow 0.6s cubic-bezier(0.22,1,0.36,1);
                    }
                    .img-stack-overlay {
                        position: absolute;
                        inset: 0;
                        background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 55%);
                        z-index: 1;
                    }
                    .img-stack-title {
                        position: absolute;
                        bottom: 24px; left: 24px; right: 24px;
                        color: white; z-index: 2;
                        font-weight: 900; font-size: 1.5rem;
                        font-family: "Playfair Display", serif;
                        letter-spacing: 1px;
                        text-shadow: 0 4px 12px rgba(0,0,0,0.5);
                    }
                </style>

                <div class="img-stack-card">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/ellora.png" alt="Ellora Caves" class="w-full h-full object-cover">
                    <div class="img-stack-overlay"></div>
                    <div class="img-stack-title">Ellora Caves Retreat</div>
                </div>
                <div class="img-stack-card">
                    <div class="absolute inset-0 bg-primary/20 z-10 mix-blend-overlay"></div>
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/ajanta.png" alt="Ajanta Caves" class="w-full h-full object-cover">
                    <div class="img-stack-overlay"></div>
                    <div class="img-stack-title">Ancient Ajanta</div>
                </div>
                <div class="img-stack-card">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/masala-dosa.jpg" alt="Local Cuisine" class="w-full h-full object-cover">
                    <div class="img-stack-overlay"></div>
                    <div class="img-stack-title">Exquisite Local Dining</div>
                </div>
                <div class="img-stack-card">
                    <img src="<?php echo BASE_PATH; ?>/images/uploads/daulatabad.png" alt="Daulatabad Fort" class="w-full h-full object-cover">
                    <div class="img-stack-overlay"></div>
                    <div class="img-stack-title">Daulatabad Fort</div>
                </div>

                <!-- Decorative blobs -->
                <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-orange-100/50 blur-xl pointer-events-none" style="animation:float 4s ease-in-out infinite;"></div>
                <div class="absolute -bottom-10 -left-6 w-32 h-32 rounded-full bg-blue-100/50 blur-xl pointer-events-none" style="animation:float 5s 1.5s ease-in-out infinite;"></div>
            </div>
            <script>
            (function(){
                var wrap  = document.getElementById('suggestor-stack-wrap');
                if (!wrap) return;
                var cards = wrap.querySelectorAll('.img-stack-card');
                if (!cards.length) return;
                var n = cards.length, current = 0;

                var states = [
                    { z:4, opacity:1,    transform:'translateY(0px) scale(1) rotate(0deg)',       shadow:'0 28px 56px -12px rgba(0,0,0,0.5), 0 0 40px -10px rgba(236,91,19,0.2)' },
                    { z:3, opacity:0.7,  transform:'translateY(12px) scale(0.93) rotate(-2.5deg)',shadow:'0 16px 32px -8px rgba(0,0,0,0.3)' },
                    { z:2, opacity:0.42, transform:'translateY(22px) scale(0.86) rotate(3deg)',   shadow:'0 8px 16px -4px rgba(0,0,0,0.18)' },
                    { z:1, opacity:0,    transform:'translateY(30px) scale(0.8) rotate(-1.5deg)', shadow:'none' },
                ];

                function applyState(card, state) {
                    card.style.zIndex    = state.z;
                    card.style.opacity   = state.opacity;
                    card.style.transform = state.transform;
                    card.style.boxShadow = state.shadow;
                }

                // Init without transition
                cards.forEach(function(card, i) {
                    card.style.transition = 'none';
                    applyState(card, states[i % n]);
                });
                void wrap.offsetWidth; // reflow
                cards.forEach(function(card) {
                    card.style.transition = 'transform .55s cubic-bezier(.22,1,.36,1),'
                                          + 'opacity .55s cubic-bezier(.22,1,.36,1),'
                                          + 'box-shadow .55s cubic-bezier(.22,1,.36,1)';
                });

                setInterval(function() {
                    current = (current + 1) % n;
                    cards.forEach(function(card, i) {
                        applyState(card, states[(i - current + n) % n]);
                    });
                }, 1000);
            })();
            </script>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;
    
    const steps = document.querySelectorAll('.step-container');
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    const progressBar = document.getElementById('progress-bar-fill');
    const stepBadge = document.getElementById('step-count-badge');
    const progressText = document.getElementById('progress-percentage');

    function updateView() {
        steps.forEach((step, idx) => {
            if (idx + 1 === currentStep) {
                step.classList.remove('hidden');
                step.classList.add('animate-in', 'fade-in', 'slide-in-from-bottom-8', 'duration-700');
            } else {
                step.classList.add('hidden');
            }
        });

        // Update Progress
        const percent = (currentStep / totalSteps) * 100;
        progressBar.style.width = percent + '%';
        progressText.innerText = Math.round(percent) + '%';
        stepBadge.innerText = `Step ${currentStep} of ${totalSteps}`;

        // Update Buttons
        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (currentStep === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
        
        // Removed jarring auto-scroll
        // window.scrollTo({ top: 300, behavior: 'smooth' });
    }

    nextBtn.addEventListener('click', () => {
        if (currentStep < totalSteps) {
            currentStep++;
            updateView();
        }
    });

    prevBtn.addEventListener('click', () => {
        if (currentStep > 1) {
            currentStep--;
            updateView();
        }
    });

    // If returning from a failed POST (validation error), jump straight to step 3
    <?php if (!empty($form_error)): ?>
    currentStep = 3;
    <?php endif; ?>
    updateView();

    // Sub-options logic for Interests
    const interestToggles = document.querySelectorAll('.interest-toggle');
    interestToggles.forEach(toggle => {
        toggle.addEventListener('change', () => {
            const targetId = toggle.getAttribute('data-target');
            const targetEl = document.getElementById(targetId);
            if (toggle.checked) {
                targetEl.classList.remove('hidden');
            } else {
                targetEl.classList.add('hidden');
            }
        });
    });

    // Sub-options logic for Travel Mode
    const travelToggles = document.querySelectorAll('.travel-toggle');
    travelToggles.forEach(toggle => {
        toggle.addEventListener('change', () => {
            document.getElementById('sub-car').classList.add('hidden');
            document.getElementById('sub-bike').classList.add('hidden');
            
            const targetId = toggle.getAttribute('data-target');
            if (targetId !== 'none') {
                document.getElementById(targetId).classList.remove('hidden');
            }
        });
    });
});
</script>

<?php require 'footer.php'; ?>
