// Marquee Announcements Display
(function () {
    let marqueeData = {
        announcements: [],
        settings: {
            enabled: true,
            speed: 20,
            separator: ' • '
        }
    };

    // Load marquee data
    async function loadMarqueeData() {
        try {
            const response = await fetch('/php/api/homepage.php');
            const data = await response.json();

            if (data.marqueeAnnouncements) {
                marqueeData.announcements = data.marqueeAnnouncements;
            }
            if (data.marqueeSettings) {
                marqueeData.settings = { ...marqueeData.settings, ...data.marqueeSettings };
            }

            renderMarquee();
        } catch (error) {
            console.error('Error loading marquee data:', error);
        }
    }

    // Render marquee
    function renderMarquee() {
        // Check if marquee is enabled
        if (!marqueeData.settings.enabled) {
            return;
        }

        // Get enabled announcements
        const enabledAnnouncements = marqueeData.announcements.filter(a => a.enabled && a.text && a.text.trim());

        // Don't render if no announcements
        if (enabledAnnouncements.length === 0) {
            return;
        }

        // Create marquee text
        const marqueeText = enabledAnnouncements.map(a => a.text).join(marqueeData.settings.separator);
        const duplicatedText = marqueeText + marqueeData.settings.separator + marqueeText;

        // Create marquee HTML
        const marqueeHTML = `
            <div class="marquee-container" style="background: linear-gradient(90deg, #003580 0%, #0066cc 100%); color: white; padding: 0.5rem 0; overflow: hidden; white-space: nowrap; position: relative; z-index: 40;">
                <div class="marquee-content" style="display: inline-block; padding-left: 100%; animation: marquee-scroll ${marqueeData.settings.speed}s linear infinite;">
                    ${duplicatedText}
                </div>
            </div>
            <style>
                @keyframes marquee-scroll {
                    0% { transform: translate(0, 0); }
                    100% { transform: translate(-100%, 0); }
                }
            </style>
        `;

        // Insert marquee after header or at top of body
        const header = document.querySelector('header');
        const nav = document.querySelector('nav');
        
        if (header) {
            header.insertAdjacentHTML('afterend', marqueeHTML);
        } else if (nav) {
            nav.insertAdjacentHTML('afterend', marqueeHTML);
        } else {
            document.body.insertAdjacentHTML('afterbegin', marqueeHTML);
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadMarqueeData);
    } else {
        loadMarqueeData();
    }
})();
