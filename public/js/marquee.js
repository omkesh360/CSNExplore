// Marquee Announcements Display — CSNExplore
// Dynamically shows listings from database
(function () {
    let marqueeData = {
        announcements: [],
        settings: { enabled: true, speed: 20, separator: ' • ' }
    };

    async function loadMarqueeData() {
        try {
            // Try to load custom announcements from homepage content
            const response = await fetch('/api/homepage-content');
            if (response.ok) {
                const data = await response.json();
                if (data.marqueeAnnouncements && data.marqueeAnnouncements.length > 0) {
                    marqueeData.announcements = data.marqueeAnnouncements;
                }
                if (data.marqueeSettings) {
                    marqueeData.settings = { ...marqueeData.settings, ...data.marqueeSettings };
                }
            }
            
            // If no custom announcements, generate from database listings
            if (marqueeData.announcements.length === 0 || !marqueeData.announcements.some(a => a.enabled && a.text)) {
                await generateListingAnnouncements();
            }
            
            renderMarquee();
        } catch (error) {
            // Try to generate from listings as fallback
            await generateListingAnnouncements();
            renderMarquee();
        }
    }

    async function generateListingAnnouncements() {
        try {
            // Fetch all categories
            const categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions'];
            const announcements = [];
            
            for (const category of categories) {
                try {
                    const res = await fetch(`/api/${category}`);
                    if (res.ok) {
                        const items = await res.json();
                        const activeItems = items.filter(item => parseInt(item.is_active) === 1);
                        
                        if (activeItems.length > 0) {
                            // Pick top 2 items by rating
                            const topItems = activeItems
                                .sort((a, b) => (parseFloat(b.rating) || 0) - (parseFloat(a.rating) || 0))
                                .slice(0, 2);
                            
                            topItems.forEach(item => {
                                const categoryName = category === 'stays' ? 'Hotels' : 
                                                   category === 'cars' ? 'Car Rentals' :
                                                   category === 'bikes' ? 'Bike Rentals' :
                                                   category === 'restaurants' ? 'Restaurants' :
                                                   'Attractions';
                                const emoji = category === 'stays' ? '🏨' :
                                            category === 'cars' ? '🚗' :
                                            category === 'bikes' ? '🚴' :
                                            category === 'restaurants' ? '🍽️' :
                                            '🎫';
                                announcements.push({
                                    text: `${emoji} ${item.name} - ${categoryName} in ${item.location}`,
                                    enabled: true
                                });
                            });
                        }
                    }
                } catch (e) {
                    console.log(`Could not fetch ${category}:`, e);
                }
            }
            
            if (announcements.length > 0) {
                marqueeData.announcements = announcements;
            } else {
                // Ultimate fallback
                marqueeData.announcements = [
                    { text: '✨ Explore amazing destinations with CSNExplore', enabled: true },
                    { text: '🚀 Book your perfect stay today', enabled: true }
                ];
            }
        } catch (error) {
            console.error('Error generating announcements:', error);
            marqueeData.announcements = [
                { text: '✨ Welcome to CSNExplore', enabled: true }
            ];
        }
    }

    function renderMarquee() {
        if (!marqueeData.settings.enabled) return;

        const active = marqueeData.announcements.filter(a => a.enabled && a.text && a.text.trim());
        if (active.length === 0) return;

        const text = active.map(a => a.text).join(marqueeData.settings.separator);
        const speed = marqueeData.settings.speed || 20;
        const doubled = text + marqueeData.settings.separator + text;

        // Remove existing marquee if any
        const existing = document.getElementById('site-marquee-bar');
        if (existing) existing.remove();

        const bar = document.createElement('div');
        bar.id = 'site-marquee-bar';
        bar.style.cssText = `
            background: linear-gradient(90deg, #003580 0%, #0066cc 100%);
            color: white;
            padding: 0.45rem 0;
            overflow: hidden;
            white-space: nowrap;
            position: relative;
            z-index: 9999;
            font-family: 'Be Vietnam Pro', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.01em;
        `;
        bar.innerHTML = `
            <div style="display:inline-block;padding-left:100%;animation:marquee-scroll ${speed}s linear infinite;">
                ${doubled}
            </div>
            <style>@keyframes marquee-scroll{0%{transform:translateX(0)}100%{transform:translateX(-100%)}}</style>
        `;

        // Insert after header or at top of body
        const header = document.querySelector('header');
        if (header && header.parentNode) {
            header.parentNode.insertBefore(bar, header.nextSibling);
        } else {
            document.body.prepend(bar);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadMarqueeData);
    } else {
        loadMarqueeData();
    }
})();
