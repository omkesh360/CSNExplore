import re

with open('public/bus.html', 'r') as f:
    content = f.read()

# Make it a Coming Soon page using Lottie animation
coming_soon_html = """
    <main class="listing-main max-w-[1140px] mx-auto px-4 md:px-6 w-full flex-grow mb-16 sm:mb-24 pt-6 flexCenter">
        <div class="coming-soon-container bg-white rounded-2xl shadow-card border border-gray-100 p-8 md:p-12 w-full max-w-2xl mx-auto flex flex-col items-center justify-center text-center">
            
            <div class="w-64 h-64 md:w-80 md:h-80 mb-6">
                <!-- Using the Lottie animation player for the bus coming soon -->
                <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_q7uarxmb.json" background="transparent" speed="1" style="width: 100%; height: 100%;" loop autoplay></lottie-player>
            </div>
            
            <h2 class="text-3xl md:text-4xl font-black text-primary mb-4">Bus Bookings Coming Soon!</h2>
            <p class="text-gray-500 text-lg mb-8 max-w-lg">We're working hard to bring you the best intercity bus booking experience. Stay tuned for seamless travel across Maharashtra and beyond.</p>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                <a href="index.html" class="bg-primary hover:bg-primary-hover text-white font-bold py-3 px-8 rounded-xl transition-all shadow-lg hover:shadow-primary/30 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">home</span>
                    Back to Home
                </a>
                <a href="stays.html" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-8 rounded-xl transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">explore</span>
                    Explore Stays
                </a>
            </div>
        </div>
    </main>
"""

# Replace the main content with the coming soon content
content = re.sub(r'<div class="listing-hero text-white">.*?</main>', coming_soon_html, content, flags=re.DOTALL)
content = content.replace('<title>TravelHub - Bus Schedule</title>', '<title>TravelHub - Bus Tickets (Coming Soon)</title>')
content = content.replace('<title>TravelHub - Booking Results</title>', '<title>TravelHub - Bus Tickets (Coming Soon)</title>')


with open('public/bus.html', 'w') as f:
    f.write(content)

print("Updated Bus page to Coming Soon with Lottie animation")
