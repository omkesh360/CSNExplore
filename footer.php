<?php // footer.php – shared footer for all CSNExplore pages ?>
<footer class="bg-slate-900 text-white pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <!-- Brand -->
            <div data-reveal data-reveal="left">
                <img src="<?php echo BASE_PATH; ?>/images/travelhub.png" alt="CSNExplore"
                    class="h-9 object-contain mb-4"
                    onerror="this.style.display='none'; document.getElementById('footer-logo-text').style.display='flex'" />
                <span id="footer-logo-text" style="display:none" class="items-center gap-1.5 mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">explore</span>
                    <span class="font-serif font-black text-white text-lg tracking-tight">CSNExplore</span>
                </span>
                <p class="text-white/50 text-sm leading-relaxed mb-5">Your premium gateway to the wonders of Chhatrapati
                    Sambhajinagar, Maharashtra.</p>
                <div class="flex gap-3 mt-6">
                    <button id="footer-share-btn" aria-label="Share"
                        class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300"><span
                            class="material-symbols-outlined text-base">share</span></button>
                    <a href="mailto:supportcsnexplore@gmail.com" target="_blank" rel="noopener noreferrer"
                        aria-label="Email Us"
                        class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300"><span
                            class="material-symbols-outlined text-base">mail</span></a>
                    <a href="https://wa.me/918600968888" target="_blank" rel="noopener noreferrer"
                        aria-label="WhatsApp Us"
                        class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-primary hover:border-primary hover:scale-110 transition-all duration-300">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.305 1.652zm6.599-3.825c1.63.975 3.41 1.487 5.23 1.488 5.439 0 9.861-4.422 9.863-9.861.001-2.636-1.024-5.115-2.884-6.977-1.862-1.864-4.341-2.887-6.979-2.888-5.439 0-9.861-4.422-9.863 9.862 0 1.842.511 3.641 1.478 5.187l-.995 3.637 3.73-.978zm11.367-7.643c-.31-.155-1.837-.906-2.12-.108-.285.103-.55.515-.674.654-.124.14-.248.155-.558.001-.31-.155-1.31-.483-2.498-1.543-.924-.824-1.548-1.841-1.73-2.15-.181-.31-.019-.477.135-.631.14-.139.31-.36.465-.541.155-.181.206-.31.31-.515.103-.206.052-.386-.026-.541-.077-.155-.674-1.626-.924-2.228-.243-.585-.491-.504-.674-.513-.175-.008-.375-.01-.575-.01s-.525.075-.8.375c-.275.3-1.05 1.026-1.05 2.5s1.075 2.9 1.225 3.1c.15.2 2.11 3.221 5.113 4.513.714.307 1.272.49 1.706.629.718.227 1.37.195 1.886.118.575-.085 1.837-.75 2.096-1.475.258-.725.258-1.346.181-1.475-.077-.129-.283-.206-.593-.361z" />
                        </svg>
                    </a>
                </div>
            </div>
            <!-- Quick Links -->
            <div data-reveal data-delay="2">
                <h5 class="font-bold text-sm mb-4">Quick Links</h5>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-white/50 text-sm">
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=stays"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Hotel
                        Bookings</a>
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=cars"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Car
                        Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=bikes"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bike
                        Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=attractions"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Heritage
                        Sites</a>
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=restaurants"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Restaurants</a>
                    <a href="<?php echo BASE_PATH; ?>/listing.php?type=buses"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bus
                        Tickets</a>
                    <a href="<?php echo BASE_PATH; ?>/blogs.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Travel
                        Guide</a>
                    <a href="<?php echo BASE_PATH; ?>/about.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">About
                        Us</a>
                    <a href="<?php echo BASE_PATH; ?>/contact.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Contact
                        Us</a>
                    <a href="<?php echo BASE_PATH; ?>/privacy.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Privacy
                        Policy</a>
                    <a href="<?php echo BASE_PATH; ?>/terms.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Terms
                        of Service</a>
                    <a href="<?php echo BASE_PATH; ?>/my-booking.php"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Track
                        Booking</a>
                </div>
            </div>
            <!-- Contact Info -->
            <div data-reveal data-delay="3">
                <h5 class="font-bold text-sm mb-4">Contact Info</h5>
                <ul class="flex flex-col gap-4 text-white/50 text-sm">
                    <li class="flex items-start gap-3">
                        <span
                            class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0 mt-0.5">
                            <span class="material-symbols-outlined text-primary text-base">location_on</span>
                        </span>
                        <a href="https://maps.app.goo.gl/k5L4CHe6gJ1tyDBY8" target="_blank" rel="noopener noreferrer"
                            class="hover:text-primary transition-colors pt-1">Jay Tower, V896+MP9, Samadhan Colony,
                            Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">call</span>
                        </span>
                        <a href="tel:+918600968888" class="hover:text-primary transition-colors">+91 86009 68888</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">mail</span>
                        </span>
                        <a href="mailto:supportcsnexplore@gmail.com"
                            class="hover:text-primary transition-colors">supportcsnexplore@gmail.com</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <span
                            class="size-8 rounded-full border border-white/20 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary text-base">schedule</span>
                        </span>
                        <span class="pt-0.5">Mon–Sat: 9am – 7pm</span>
                    </li>
                </ul>
            </div>
            <!-- Newsletter -->
            <div data-reveal data-reveal="right" data-delay="4">
                <h5 class="font-bold text-sm mb-4">Stay Updated</h5>
                <p class="text-white/50 text-sm mb-4">Get travel tips and exclusive deals in your inbox.</p>
                <form method="POST" action="subscribe.php" class="flex flex-col gap-4">
                    <input type="email" name="email" placeholder="Your email address" required
                        class="bg-white/5 border border-white/10 text-white placeholder:text-white/30 px-3 py-2.5 rounded-xl text-sm focus:outline-none focus:border-primary transition-colors" />
                    <button type="submit" style="background-color: #ec5b13;"
                        class="text-white font-bold py-2.5 rounded-xl text-sm hover:scale-[1.02] transition-all duration-200"
                        onmouseover="this.style.backgroundColor='#d44e0e'"
                        onmouseout="this.style.backgroundColor='#ec5b13'">Subscribe</button>
                </form>
            </div>
        </div>
        <div
            class="border-t border-white/10 pt-6 flex flex-col md:flex-row items-center justify-between gap-3 text-white/30 text-xs">
            <p>© <?php echo date('Y'); ?> CSNExplore. All rights reserved.</p>
            <div class="flex gap-5">
                <a href="<?php echo BASE_PATH; ?>/privacy" class="hover:text-primary transition-colors">Privacy
                    Policy</a>
                <a href="<?php echo BASE_PATH; ?>/terms" class="hover:text-primary transition-colors">Terms of
                    Service</a>
                <a href="<?php echo BASE_PATH; ?>/sitemap.xml" class="hover:text-primary transition-colors">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

<!-- Go to Top Button - Always there on homepage/mobile too -->
<button id="go-top-btn" onclick="window.scrollTo({top:0,behavior:'smooth'})" class="flex"
    style="position:fixed;bottom:calc(24px + env(safe-area-inset-bottom, 0px));z-index:9999;width:46px;height:46px;border-radius:50%;background:#ec5b13;color:#fff;border:none;cursor:pointer;box-shadow:0 4px 20px rgba(236,91,19,0.5);align-items:center;justify-content:center;opacity:0;visibility:hidden;transform:translateY(12px);transition:opacity .25s ease,visibility .25s ease,transform .25s ease;"
    aria-label="Go to top">
    <span class="material-symbols-outlined"
        style="font-size:22px;line-height:1;pointer-events:none;">arrow_upward</span>
</button>
<style>
    @media (max-width: 1024px) { #go-top-btn { left: 20px !important; right: auto !important; } }
    @media (min-width: 1025px) { #go-top-btn { right: 20px !important; left: auto !important; } }
</style>

<?php
// Hide floating buttons on login and register pages
$hide_floating_buttons = in_array($current_page ?? '', ['login.php', 'register.php']);
?>
<?php if (!$hide_floating_buttons): ?>
    <!-- ── Floating Action Buttons - Mobile + Tablet (hidden on desktop) ──────── -->
    <!-- Call Button - Mobile & Tablet (Blue) -->
    <a href="tel:+918600968888" id="call-float" class="flex lg:hidden" aria-label="Call Now"
        style="position:fixed;bottom:calc(88px + env(safe-area-inset-bottom, 0px));right:20px;z-index:9998;width:52px;height:52px;border-radius:50%;background:#2563eb;color:#fff;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,99,235,0.5);text-decoration:none;transition:transform .25s ease,box-shadow .25s ease;"
        ontouchstart="this.style.transform='scale(1.08)'" ontouchend="this.style.transform='scale(1)'">
        <span class="material-symbols-outlined"
            style="font-size:26px;font-variation-settings:'FILL' 1,'wght' 600,'GRAD' 0,'opsz' 24;">call</span>
    </a>

    <!-- WhatsApp Button - Mobile & Tablet -->
    <a href="https://wa.me/918600968888?text=Hi%20CSNExplore!%20I%20need%20help%20with%20my%20booking." target="_blank"
        rel="noopener noreferrer" id="whatsapp-float" class="flex lg:hidden" aria-label="Chat on WhatsApp"
        style="position:fixed;bottom:calc(24px + env(safe-area-inset-bottom, 0px));right:20px;z-index:9998;width:52px;height:52px;border-radius:50%;background:#25D366;color:#fff;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,211,102,0.5);text-decoration:none;transition:transform .25s ease,box-shadow .25s ease;"
        ontouchstart="this.style.transform='scale(1.08)'" ontouchend="this.style.transform='scale(1)'">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
            <path
                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.417-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
        </svg>
    </a>
    <!-- WhatsApp pulse ring -->
    <style>
        #whatsapp-float {
            position: relative;
        }

        #whatsapp-float::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #25D366;
            opacity: .4;
            animation: wa-pulse 2s infinite;
        }

        @keyframes wa-pulse {
            0% {
                transform: scale(1);
                opacity: .4;
            }

            70% {
                transform: scale(1.4);
                opacity: 0;
            }

            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }
    </style>
<?php endif; ?>

<!-- ── Cookie Consent Banner [B4.1] ──────────────────────────────────────── -->
<div id="cookie-banner"
    style="display:none;position:fixed;bottom:0;left:0;right:0;z-index:99999;background:#1e293b;color:#f8fafc;padding:16px 24px;box-shadow:0 -4px 20px rgba(0,0,0,0.3);">
    <div
        style="max-width:1200px;margin:0 auto;display:flex;flex-wrap:wrap;align-items:center;gap:16px;justify-content:space-between;">
        <p style="margin:0;font-size:13px;color:#cbd5e1;line-height:1.6;flex:1;min-width:280px;">
            🍪 We use cookies to enhance your experience, analyze traffic, and improve our services.
            By continuing to use CSNExplore, you agree to our
            <a href="<?php echo BASE_PATH; ?>/privacy" style="color:#ec5b13;text-decoration:underline;">Privacy
                Policy</a>.
        </p>
        <div style="display:flex;gap:10px;flex-shrink:0;">
            <button onclick="setCookieConsent('declined')"
                style="padding:8px 18px;border:1px solid rgba(255,255,255,0.2);border-radius:8px;background:transparent;color:#94a3b8;font-size:13px;cursor:pointer;transition:all .2s"
                onmouseover="this.style.borderColor='#ec5b13';this.style.color='#ec5b13'"
                onmouseout="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='#94a3b8'">
                Decline
            </button>
            <button onclick="setCookieConsent('accepted')"
                style="padding:8px 22px;border:none;border-radius:8px;background:#ec5b13;color:#fff;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s"
                onmouseover="this.style.background='#d94a0a'" onmouseout="this.style.background='#ec5b13'">
                Accept All
            </button>
        </div>
    </div>
</div>

<?php
$locationsFile = __DIR__ . '/locations.txt';
$locationsData = [];
if (file_exists($locationsFile)) {
    $locs = file($locationsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($locs)) {
        foreach ($locs as $loc) {
            $locationsData[] = htmlspecialchars(trim($loc));
        }
    }
}
?>
<style>
.autocomplete-dropdown {
    position: absolute;
    background: #1c1410;
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.8);
    z-index: 999999;
    max-height: 250px;
    overflow-y: auto;
    display: none;
    flex-direction: column;
}
.autocomplete-dropdown.active {
    display: flex;
}
.autocomplete-item {
    padding: 12px 20px;
    color: #fff;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 10px;
}
.autocomplete-item:hover {
    background: rgba(255,255,255,0.1);
    color: #fff;
}
.autocomplete-item b {
    color: #38bdf8; /* Light blue highlight instead of orange */
}
.autocomplete-item.no-results {
    color: #94a3b8;
    cursor: default;
}
.autocomplete-item.no-results:hover {
    background: transparent;
}
.autocomplete-item .material-symbols-outlined {
    font-size: 18px;
    opacity: 0.7;
}

/* Scrollbar for dropdown */
.autocomplete-dropdown::-webkit-scrollbar { width: 6px; }
.autocomplete-dropdown::-webkit-scrollbar-track { background: transparent; }
.autocomplete-dropdown::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 10px; }
.autocomplete-dropdown::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
</style>
<script>
    const CSN_LOCATIONS = <?php echo json_encode($locationsData); ?>;
    (function () {
        // ── Custom Autocomplete Logic ──
        document.querySelectorAll('input[list="location-list"]').forEach(function(inp) {
            inp.removeAttribute('list'); // Disable native datalist
            
            const dropdown = document.createElement('div');
            dropdown.className = 'autocomplete-dropdown';
            document.body.appendChild(dropdown);

            function updatePosition() {
                if (!dropdown.classList.contains('active')) return;
                const rect = inp.getBoundingClientRect();
                dropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
                dropdown.style.left = (rect.left + window.scrollX) + 'px';
                dropdown.style.width = rect.width + 'px';
            }

            document.addEventListener('click', function(e) {
                if (e.target !== inp && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });

            window.addEventListener('resize', updatePosition);
            window.addEventListener('scroll', updatePosition, true);

            inp.addEventListener('input', function() {
                const val = this.value.trim().toLowerCase();
                dropdown.innerHTML = '';
                
                if (!val) {
                    dropdown.classList.remove('active');
                    return;
                }

                const matches = CSN_LOCATIONS.filter(l => l.toLowerCase().includes(val));

                if (matches.length > 0) {
                    matches.slice(0, 10).forEach(match => {
                        const item = document.createElement('div');
                        item.className = 'autocomplete-item';
                        
                        const startIdx = match.toLowerCase().indexOf(val);
                        const beforeStr = match.substring(0, startIdx);
                        const matchStr = match.substring(startIdx, startIdx + val.length);
                        const afterStr = match.substring(startIdx + val.length);

                        item.innerHTML = `<span class="material-symbols-outlined">location_on</span><span>${beforeStr}<b>${matchStr}</b>${afterStr}</span>`;
                        
                        item.addEventListener('click', function() {
                            inp.value = match;
                            dropdown.classList.remove('active');
                        });
                        dropdown.appendChild(item);
                    });
                    dropdown.classList.add('active');
                    updatePosition();
                } else {
                    dropdown.classList.remove('active');
                }
            });

            inp.addEventListener('focus', function() {
                if (this.value.trim().length > 0) {
                    this.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        });

        // ── Go-to-top ──
        var btn = document.getElementById('go-top-btn');
        function updateBtn() {
            if (window.scrollY > 200) {
                btn.style.opacity = '1';
                btn.style.visibility = 'visible';
                btn.style.transform = 'translateY(0)';
            } else {
                btn.style.opacity = '0';
                btn.style.visibility = 'hidden';
                btn.style.transform = 'translateY(12px)';
            }
        }
        updateBtn();
        window.addEventListener('scroll', updateBtn, { passive: true });

        // ── Share button (Web Share API with clipboard fallback) ──
        var shareBtn = document.getElementById('footer-share-btn');
        if (shareBtn) {
            shareBtn.addEventListener('click', function () {
                var shareData = {
                    title: document.title || 'CSNExplore',
                    text: 'Discover hotels, bikes, cars & attractions in Chhatrapati Sambhajinagar!',
                    url: window.location.href
                };
                if (navigator.share) {
                    navigator.share(shareData).catch(function () { });
                } else {
                    // Clipboard fallback
                    var url = window.location.href;
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(url).then(function () {
                            shareBtn.innerHTML = '<span class="material-symbols-outlined text-base">check</span>';
                            setTimeout(function () { shareBtn.innerHTML = '<span class="material-symbols-outlined text-base">share</span>'; }, 2000);
                        });
                    } else {
                        var ta = document.createElement('textarea');
                        ta.value = url; ta.style.position = 'fixed'; ta.style.opacity = '0';
                        document.body.appendChild(ta); ta.select();
                        try { document.execCommand('copy'); } catch (e) { }
                        document.body.removeChild(ta);
                        shareBtn.innerHTML = '<span class="material-symbols-outlined text-base">check</span>';
                        setTimeout(function () { shareBtn.innerHTML = '<span class="material-symbols-outlined text-base">share</span>'; }, 2000);
                    }
                }
            });
        }

        // ── Scroll Reveal (IntersectionObserver) ──
        if ('IntersectionObserver' in window) {
            var revealObs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        revealObs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
            document.querySelectorAll('[data-reveal], [data-reveal-children]').forEach(function (el) {
                revealObs.observe(el);
            });
        } else {
            // Fallback: reveal all immediately
            document.querySelectorAll('[data-reveal], [data-reveal-children]').forEach(function (el) {
                el.classList.add('revealed');
            });
        }

        // ── Smooth page transitions (fade out on link click) ──
        document.addEventListener('click', function (e) {
            var a = e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript') || a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            try {
                var url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
            } catch (err) { return; }
            e.preventDefault();
            document.body.classList.add('page-fade-out');
            // Safety: restore if navigation stalls
            var _st = setTimeout(function () { document.body.classList.remove('page-fade-out'); }, 1200);
            setTimeout(function () { clearTimeout(_st); window.location.href = href; }, 400);
        });

        // ── Always restore opacity on any page show/load (fixes invisible text) ──
        function _restoreBody() {
            document.body.classList.remove('page-fade-out');
            document.body.style.visibility = 'visible';
        }
        window.addEventListener('pageshow', _restoreBody);
        window.addEventListener('load', _restoreBody);
        // Hard safety net after 300ms in case events don't fire
        setTimeout(_restoreBody, 300);

        // ── Cookie consent ──
        function getCookie(name) { var v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)'); return v ? v.pop() : ''; }
        window.setCookieConsent = function (val) {
            document.cookie = 'csn_cookie_consent=' + val + ';max-age=31536000;path=/;SameSite=Lax';
            document.getElementById('cookie-banner').style.display = 'none';
        };
        if (!getCookie('csn_cookie_consent')) {
            setTimeout(function () { document.getElementById('cookie-banner').style.display = 'block'; }, 1200);
        }

        // ── Restore opacity on page show (back/forward cache) ──
        window.addEventListener('pageshow', function (e) {
            document.body.classList.remove('page-fade-out');
        });
    })();
</script>
</body>

</html>