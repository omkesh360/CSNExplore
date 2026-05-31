<?php // footer.php – shared footer for all CSNExplore pages ?>
<footer class="bg-slate-900 text-white pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <!-- Brand -->
            <div data-reveal data-reveal="left">
                <img loading="lazy" decoding="async" width="120" height="36" src="<?php echo BASE_PATH; ?>/images/Logo-light-optimized.webp" alt="CSNExplore"
                    class="h-9 object-contain mb-4"
                    onerror="this.style.display='none'; document.getElementById('footer-logo-text').style.display='flex'" />
                <span id="footer-logo-text" style="display:none" class="items-center gap-1.5 mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">explore</span>
                    <span class="font-serif font-black text-white text-lg tracking-tight">CSNExplore</span>
                </span>
                <p class="text-white/50 text-sm leading-relaxed mb-5">Your premium gateway to the wonders of Chhatrapati
                    Sambhajinagar, Maharashtra.</p>
                <div class="flex gap-3 mt-6">
                    <button id="footer-share-btn" aria-label="Share" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white/80 hover:bg-primary hover:border-primary hover:text-white hover:-translate-y-1 hover:shadow-[0_6px_20px_rgba(236,91,19,0.5)] transition-all duration-300">
                        <svg class="w-[18px] h-[18px] fill-current" viewBox="0 0 448 512"><path d="M352 320c-22.61 0-43.33 8.35-59.39 22.18l-123.7-72.16c1.15-5.91 1.76-11.96 1.76-18.17 0-6.17-.61-12.18-1.76-18.06l123.7-72.16c16.03 13.78 36.68 22.06 59.23 22.06 49.33 0 89.33-40 89.33-89.35S401.33 5 352 5 262.67 45 262.67 94.35c0 6.17 .61 12.18 1.76 18.06l-123.7 72.16c-16.03-13.78-36.68-22.06-59.23-22.06-49.33 0-89.33 40-89.33 89.35s40 89.35 89.33 89.35c22.61 0 43.33-8.35 59.39-22.18l123.7 72.16c-1.15 5.91-1.76 11.96-1.76 18.17 0 49.35 40 89.35 89.33 89.35s89.33-40 89.33-89.35-40-89.35-89.33-89.35z"/></svg>
                    </button>
                    <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" target="_blank" rel="noopener noreferrer" aria-label="Email Us" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white/80 hover:bg-primary hover:border-primary hover:text-white hover:-translate-y-1 hover:shadow-[0_6px_20px_rgba(236,91,19,0.5)] transition-all duration-300">
                        <svg class="w-[18px] h-[18px] fill-current" viewBox="0 0 512 512"><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg>
                    </a>
                    <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Us" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white/80 hover:bg-[#25D366] hover:border-[#25D366] hover:text-white hover:-translate-y-1 hover:shadow-[0_6px_20px_rgba(37,211,102,0.5)] transition-all duration-300">
                        <svg class="w-[18px] h-[18px] fill-current" viewBox="0 0 448 512"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
                    </a>
                    <a href="https://www.instagram.com/csnexplore_/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center text-white/80 hover:bg-gradient-to-tr hover:from-[#f09433] hover:via-[#dc2743] hover:to-[#bc1888] hover:border-transparent hover:text-white hover:-translate-y-1 hover:shadow-[0_6px_20px_rgba(220,39,67,0.5)] transition-all duration-300">
                        <svg class="w-[18px] h-[18px] fill-current" viewBox="0 0 448 512"><path d="M224.1 141.6c-45.6 0-82.6 37-82.6 82.6s37 82.6 82.6 82.6 82.6-37 82.6-82.6-37.1-82.6-82.6-82.6zm0 136.4c-29.7 0-53.8-24.1-53.8-53.8s24.1-53.8 53.8-53.8 53.8 24.1 53.8 53.8-24.1 53.8-53.8 53.8zm76.5-115.5c-9.5 0-17.2-7.7-17.2-17.2s7.7-17.2 17.2-17.2 17.2 7.7 17.2 17.2-7.8 17.2-17.2 17.2zM448 224v113c0 60.1-48.9 109-109 109H109C48.9 446 0 397.1 0 337V109C0 48.9 48.9 0 109 0h230C399.1 0 448 48.9 448 109v115h0zm-41.6 0v-115c0-37.2-30.2-67.4-67.4-67.4H109c-37.2 0-67.4 30.2-67.4 67.4v228c0 37.2 30.2 67.4 67.4 67.4h230c37.2 0 67.4-30.2 67.4-67.4v-113h0z"/></svg>
                    </a>
                </div>
            </div>
            <!-- Quick Links -->
            <div data-reveal data-delay="2">
                <h4 class="font-bold text-sm mb-4">Quick Links</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2.5 text-white/50 text-sm">
                    <a href="<?php echo BASE_PATH; ?>/listing?type=stays"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Hotel
                        Bookings</a>
                    <a href="<?php echo BASE_PATH; ?>/listing?type=cars"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Car
                        Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing?type=bikes"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bike
                        Rentals</a>
                    <a href="<?php echo BASE_PATH; ?>/listing?type=attractions"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Heritage
                        Sites</a>
                    <a href="<?php echo BASE_PATH; ?>/listing?type=restaurants"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Restaurants</a>
                    <a href="<?php echo BASE_PATH; ?>/bus"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Bus
                        Tickets</a>
                    <a href="<?php echo BASE_PATH; ?>/blogs"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Travel
                        Guide</a>
                    <a href="<?php echo BASE_PATH; ?>/about"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">About
                        Us</a>
                    <a href="<?php echo BASE_PATH; ?>/contact"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Contact
                        Us</a>
                    <a href="<?php echo BASE_PATH; ?>/privacy"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Privacy
                        Policy</a>
                    <a href="<?php echo BASE_PATH; ?>/terms"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Terms
                        of Service</a>
                    <a href="<?php echo BASE_PATH; ?>/my-booking"
                        class="hover:text-primary transition-colors hover:translate-x-1 inline-block transition-transform duration-200">Track
                        Booking</a>
                </div>
            </div>
            <!-- Contact Info -->
            <div data-reveal data-delay="3">
                <h4 class="font-bold text-sm mb-4">Contact Info</h4>
<ul class="flex flex-col gap-4 text-white/50 text-sm">
                    <li class="flex items-start gap-4">
                        <span class="size-9 rounded-full border border-white/15 flex items-center justify-center shrink-0 mt-0.5 bg-white/5">
                            <span class="material-symbols-outlined text-primary text-[19px]">location_on</span>
                        </span>
                        <div class="flex flex-col">
                            <span class="text-white font-bold text-xs uppercase tracking-widest mb-1">Our Office</span>
                            <a href="https://maps.app.goo.gl/1yfmPM7jrKAxtn6s5" target="_blank" rel="noopener noreferrer" class="hover:text-primary transition-colors leading-relaxed">Jay Tower, Padampura, Chhatrapati Sambhajinagar, Maharashtra 431005</a>
                        </div>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="size-9 rounded-full border border-white/15 flex items-center justify-center shrink-0 bg-white/5">
                            <span class="material-symbols-outlined text-primary text-[19px]">call</span>
                        </span>
                        <div class="flex flex-col">
                            <span class="text-white font-bold text-xs uppercase tracking-widest mb-0.5">Phone</span>
                            <a href="tel:<?php echo CONTACT_PHONE; ?>" class="hover:text-primary transition-colors font-semibold tracking-tight"><?php echo CONTACT_PHONE; ?></a>
                        </div>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="size-9 rounded-full border border-white/15 flex items-center justify-center shrink-0 bg-white/5">
                            <span class="material-symbols-outlined text-primary text-[19px]">mail</span>
                        </span>
                        <div class="flex flex-col">
                            <span class="text-white font-bold text-xs uppercase tracking-widest mb-0.5">Email</span>
                            <a href="mailto:<?php echo SUPPORT_EMAIL; ?>" class="hover:text-primary transition-colors font-semibold tracking-tight"><?php echo SUPPORT_EMAIL; ?></a>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- Newsletter -->
            <div data-reveal data-reveal="right" data-delay="4">
                <h4 class="font-bold text-sm mb-4">Stay Updated</h4>
                <p class="text-white/50 text-sm mb-4">Get travel tips and exclusive deals in your inbox.</p>
                <form method="POST" action="subscribe" class="flex flex-col gap-4">
                    <input type="email" name="email" placeholder="Your email address" required
                        class="bg-white/5 border border-white/10 text-white placeholder:text-white/30 px-4 py-3 rounded-xl text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner" />
                    <button type="submit"
                        class="bg-primary text-white font-bold py-3 rounded-xl text-sm hover:-translate-y-1 hover:bg-orange-600 transition-all duration-300 shadow-[0_4px_15px_rgba(236,91,19,0.3)] hover:shadow-[0_8px_25px_rgba(236,91,19,0.5)]">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
        
        <!-- SEO Keyword Block -->
        <div class="border-t border-white/10 pt-8 pb-4 mb-4">
            <h4 class="font-bold text-sm mb-3 text-white/80">Popular Searches in Chhatrapati Sambhajinagar (Aurangabad)</h4>
            <p class="text-white/40 text-xs leading-loose">
                <a href="<?php echo BASE_PATH; ?>/listing?type=stays" class="hover:text-primary transition-colors">Hotels CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=stays" class="hover:text-primary transition-colors">Budget Stays CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=stays" class="hover:text-primary transition-colors">Jain Hotels Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=stays" class="hover:text-primary transition-colors">Safe Stays for Women CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=cars" class="hover:text-primary transition-colors">Car Rentals Near Me Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=cars" class="hover:text-primary transition-colors">Cab Booking Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=cars" class="hover:text-primary transition-colors">Self Drive Cars CSNExplore.com</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=cars" class="hover:text-primary transition-colors">Innova Rental Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=bikes" class="hover:text-primary transition-colors">Bike Rental Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=bikes" class="hover:text-primary transition-colors">Electric Scooter Rental CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=bikes" class="hover:text-primary transition-colors">Scsnexplore Bike Rental</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=bikes" class="hover:text-primary transition-colors">Activa Rental Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=attractions" class="hover:text-primary transition-colors">Ajanta Caves Tour CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=attractions" class="hover:text-primary transition-colors">Ellora Caves Guide CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=attractions" class="hover:text-primary transition-colors">Bibi Ka Maqbara Tickets CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=attractions" class="hover:text-primary transition-colors">Daulatabad Fort Trip CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=restaurants" class="hover:text-primary transition-colors">Veg Restaurant Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=restaurants" class="hover:text-primary transition-colors">Best Misal Pav Aurangabad CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/listing?type=restaurants" class="hover:text-primary transition-colors">Maharashtrian Thali CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/blogs" class="hover:text-primary transition-colors">Solo Travelling CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/blogs" class="hover:text-primary transition-colors">Women Travel Safety CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/blogs" class="hover:text-primary transition-colors">Budget Under 2 Days CSNExplore</a> | 
                <a href="<?php echo BASE_PATH; ?>/" class="hover:text-primary transition-colors">Aurangabad Travel Guide CSNExplore.in</a> | 
                <a href="<?php echo BASE_PATH; ?>/" class="hover:text-primary transition-colors">CSNExplore Tourism Portal</a>
            </p>
        </div>

        <!-- Hidden Advanced SEO Content Block for Specific Keywords (Visually Hidden) -->
        <div style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0;" aria-hidden="true">
            <h1>csnexplore - CSN Explore</h1>
            <h2>Tour Packages & Itineraries</h2>
            <p>Explore Aurangabad on a Budget: Top Travel Tips</p>
            <p>Ultimate Aurangabad Itinerary: Best Places to Visit Under 2 Days</p>
            <p>The Perfect Aurangabad Trip Under 3 Days for Weekend Travelers</p>
            <p>Solo Traveling in Aurangabad: The Ultimate Backpacker's Guide</p>
            <p>Safe Solo Female Travel Guide to Aurangabad: Tips for Single Women Travelers</p>
            <h2>Accommodations (Hotels, Hostels & Dorms)</h2>
            <p>Best Hotels Near Aurangabad for Every Budget and Traveler</p>
            <p>Top Hostels in Aurangabad for Backpackers and Solo Travelers</p>
            <p>Affordable Hostels Near Satara Parisar, Aurangabad</p>
            <p>Top Spa Hotels in Aurangabad for a Relaxing Getaway</p>
            <p>Budget Hotels Under ₹2000 in Aurangabad (Chhatrapati Sambhajinagar)</p>
            <p>Affordable Dormitories in Aurangabad for Budget Backpackers</p>
            <p>Cheap Hotels on Cot Basis in Aurangabad for Solo or Group Stays</p>
            <h2>Transport & Vehicle Rentals</h2>
            <p>Reliable Cab Booking in Aurangabad for Ajanta & Ellora Tours</p>
            <p>Affordable Bike Rentals in Aurangabad: Top Bike Models Available</p>
            <p>Best Bike Rentals Under ₹5000 in Aurangabad for Long Trips</p>
            <p>Top Rated Car Rentals Near Me in Aurangabad</p>
            <p>Budget Car Rentals Under ₹3000 in Aurangabad</p>
            <p>Premium Sedan Car Rentals in Aurangabad for Comfortable Travel</p>
            <p>Spacious SUV Rentals in Aurangabad for Family Outstation Trips</p>
            <h2>Food, Dining & Restaurants</h2>
            <p>Best Places to Dine Near Aurangabad: Top Restaurants and Eateries</p>
            <p>Authentic Maharashtrian Thali and Local Cuisine in Aurangabad</p>
            <p>Best Continental Restaurants and Cafes in Aurangabad</p>
            <p>Top Pure Veg Restaurants in Aurangabad for Family Dining</p>
            <p>Best Jain Hotels in Aurangabad Serving Authentic Jain Food</p>
            <p>Famous Biryani Shops in Aurangabad You Absolutely Must Try</p>
            <h2>More Explore Options & Common Misspellings</h2>
            <p>csn explore, csnexplore, hotel aurnagabd, stays aunaagabd, destiation aiurnagabd, restraiunt aurabgabd, car rental aurangabad, bike rental aurangabad, tempo rental auataraafabd, bed, stays, cars, bikes, attractions, dine, buses in atranhgabad.</p>
            <p>Anything about Aurangabad, auranagabd, auaranagabd, aurangabd, aurangbad, auragabad, aurnagabad, aurangabda, awrangabad, orangabad, orangbad. Chhatrapati Sambhajinagar, Chtarapati sambhajinagar, chatrapati sambhajinagar, chatrpati sambhajinagar, chhatrpati sambhajinagar, sambhajinagar, shambhaji nagar, sambhaji nagar, shivaji nagar, sambhajinagr. csnexplore, csn explore, csnxplore, csnexlpor, cnsexplore, cs explore, csn explorer, cs nexplore, csnxplor.</p>
        </div>

        <!-- Comprehensive LocalBusiness Schema Injection -->
        <script type="application/ld+json">
        {
          "@context": "https://schema.org",
          "@type": "TravelAgency",
          "name": "CSNExplore",
          "alternateName": ["csn explore", "csnxplore", "cnsexplore", "cs explore", "Aurangabad Tourism", "Chhatrapati Sambhajinagar Travel"],
          "url": "https://csnexplore.com",
          "logo": "https://csnexplore.com/images/Logo-light-optimized.webp",
          "description": "CSNExplore is the ultimate tourism portal for Chhatrapati Sambhajinagar (Aurangabad). We offer budget car rentals, bike rentals, verified hotels, dormitories, and complete tour packages for Ajanta and Ellora Caves.",
          "telephone": "+91-8600968888",
          "email": "supportcsnexplore@gmail.com",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "Jay Tower, V896+MP9, Samadhan Colony, Padampura",
            "addressLocality": "Chhatrapati Sambhajinagar",
            "addressRegion": "Maharashtra",
            "postalCode": "431005",
            "addressCountry": "IN"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": 19.8686039,
            "longitude": 75.3131686
          },
          "areaServed": [
            {
              "@type": "City",
              "name": "Chhatrapati Sambhajinagar",
              "alternateName": "Aurangabad"
            },
            {
              "@type": "City",
              "name": "Maharashtra"
            }
          ],
          "priceRange": "₹₹",
          "openingHours": "Mo-Su 09:00-19:00",
          "sameAs": [
            "https://www.facebook.com/csnexplore",
            "https://www.instagram.com/csnexplore"
          ],
          "keywords": "auranagabd, auaranagabd, aurangabd, aurangbad, aurnagabad, Chtarapati sambhajinagar, chatrapati sambhajinagar, shambhaji nagar, csn explore, csnxplore, cnsexplore, rent a bike in Aurangabad, best budget car rentals in Aurangabad, top 10 dormitories near Chhatrapati Sambhajinagar"
        }
        </script>

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
    <a href="tel:<?php echo CONTACT_PHONE; ?>" id="call-float" class="flex lg:hidden" aria-label="Call Now"
        style="position:fixed;bottom:calc(88px + env(safe-area-inset-bottom, 0px));right:20px;z-index:9998;width:52px;height:52px;border-radius:50%;background:#2563eb;color:#fff;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(37,99,235,0.5);text-decoration:none;transition:transform .25s ease,box-shadow .25s ease;"
        ontouchstart="this.style.transform='scale(1.08)'" ontouchend="this.style.transform='scale(1)'">
        <span class="material-symbols-outlined"
            style="font-size:26px;font-variation-settings:'FILL' 1,'wght' 600,'GRAD' 0,'opsz' 24;">call</span>
    </a>

    <!-- WhatsApp Button - Mobile & Tablet -->
    <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', CONTACT_PHONE); ?>?text=Hi%20CSNExplore!%20I%20need%20help%20with%20my%20booking." target="_blank"
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
<?php $show_cookie = !isset($_COOKIE['csn_cookie_consent']) ? 'block' : 'none'; ?>
<div id="cookie-banner"
    style="display:<?php echo $show_cookie; ?>;position:fixed;bottom:0;left:0;right:0;z-index:99999;background:#1e293b;color:#f8fafc;padding:16px 24px;box-shadow:0 -4px 20px rgba(0,0,0,0.3);">
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
<script type="application/json" id="csn-locations-data">
<?php echo json_encode($locationsData); ?>
</script>
<script>
    let CSN_LOCATIONS = [];
    try { CSN_LOCATIONS = JSON.parse(document.getElementById('csn-locations-data').textContent); } catch(e){}
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
            }, { threshold: 0.05, rootMargin: '0px 0px -20px 0px' });
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
            if (e.defaultPrevented) return;
            var a = e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href === '#' || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:') || href.startsWith('javascript') || a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            try {
                var url = new URL(href, window.location.href);
                if (url.origin !== window.location.origin) return;
                // Don't intercept same page anchors/links
                if (url.pathname === window.location.pathname && url.search === window.location.search) return;
            } catch (err) { return; }
            e.preventDefault();
            
            // Create progress bar
            var pb = document.getElementById('page-loading-bar');
            if (!pb) {
                pb = document.createElement('div');
                pb.id = 'page-loading-bar';
                pb.style.cssText = 'position:fixed;top:0;left:0;height:3px;background:linear-gradient(to right, #ec5b13, #f97316, #fb923c);z-index:999999;transition:width 0.3s cubic-bezier(0.1, 0.8, 0.3, 1);width:0%;box-shadow:0 0 8px rgba(236,91,19,0.5);';
                document.body.appendChild(pb);
            }
            
            // Start progress bar animation
            setTimeout(function() {
                pb.style.width = '50%';
            }, 10);
            
            setTimeout(function() {
                pb.style.width = '85%';
            }, 150);

            // Start body fade out
            setTimeout(function() {
                document.body.style.transition = 'opacity 0.2s ease-out';
                document.body.style.opacity = '0';
            }, 100);

            // Safety: restore if navigation stalls
            var _st = setTimeout(function () { 
                document.body.style.opacity = '1'; 
                if (pb) pb.style.width = '0%';
            }, 1200);

            setTimeout(function () { 
                clearTimeout(_st); 
                window.location.href = href; 
            }, 300);
        });

        // ── Always restore opacity on any page show/load (fixes invisible text) ──
        function _restoreBody() {
            document.body.style.transition = '';
            document.body.style.opacity = '1';
            document.body.style.visibility = 'visible';
            var pb = document.getElementById('page-loading-bar');
            if (pb) pb.remove();
        }
        window.addEventListener('pageshow', _restoreBody);
        window.addEventListener('load', _restoreBody);
        // Hard safety net after 150ms in case events don't fire
        setTimeout(_restoreBody, 150);

        // ── Service Worker Registration (Progressive Web App) ──
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(reg) { console.log('SW registered'); })
                    .catch(function(err) { console.log('SW registration failed'); });
            });
        }

        // ── Cookie consent ──
        function getCookie(name) { var v = document.cookie.match('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)'); return v ? v.pop() : ''; }
        window.setCookieConsent = function (val) {
            document.cookie = 'csn_cookie_consent=' + val + ';max-age=31536000;path=/;SameSite=Lax';
            document.getElementById('cookie-banner').style.display = 'none';
        };

        // ── Reinitialize reveal on pageshow (back/forward cache) ──store opacity on page show (back/forward cache) ──
        window.addEventListener('pageshow', function (e) {
            document.body.classList.remove('page-fade-out');
            document.querySelectorAll('[data-reveal], [data-reveal-children]').forEach(function(el){ el.classList.add('revealed'); });
        });
    })();
</script>

<!-- Animations.js - DEFERRED for better performance -->
<script src="<?php echo BASE_PATH; ?>/animations.min.js?v=<?php echo filemtime(__DIR__ . '/animations.min.js'); ?>" defer></script>
<script src="<?php echo BASE_PATH; ?>/js/prefetch.js" defer></script>


</body>

</html>
