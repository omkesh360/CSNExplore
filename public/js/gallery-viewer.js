/**
 * gallery-viewer.js
 * Fullscreen image viewer with zoom functionality for all listing detail pages
 */

(function() {
    let currentImageIndex = 0;
    let galleryImages = [];

    // Create fullscreen viewer HTML
    const viewerHTML = `
        <div id="gallery-viewer" class="fixed inset-0 bg-black/95 z-[9999] hidden items-center justify-center">
            <button id="viewer-close" class="absolute top-4 right-4 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors z-10">
                <span class="material-symbols-outlined text-[28px]">close</span>
            </button>
            
            <button id="viewer-prev" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors z-10">
                <span class="material-symbols-outlined text-[28px]">chevron_left</span>
            </button>
            
            <button id="viewer-next" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors z-10">
                <span class="material-symbols-outlined text-[28px]">chevron_right</span>
            </button>
            
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white/10 backdrop-blur-md px-4 py-2 rounded-full text-white text-sm font-medium">
                <span id="viewer-counter">1 / 1</span>
            </div>
            
            <div class="w-full h-full flex items-center justify-center p-4">
                <img id="viewer-image" src="" alt="Gallery image" class="max-w-full max-h-full object-contain cursor-zoom-in transition-transform duration-300" />
            </div>
        </div>
    `;

    // Initialize viewer on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Add viewer to body
        document.body.insertAdjacentHTML('beforeend', viewerHTML);

        const viewer = document.getElementById('gallery-viewer');
        const viewerImage = document.getElementById('viewer-image');
        const viewerCounter = document.getElementById('viewer-counter');
        const closeBtn = document.getElementById('viewer-close');
        const prevBtn = document.getElementById('viewer-prev');
        const nextBtn = document.getElementById('viewer-next');

        let isZoomed = false;

        // Close viewer
        function closeViewer() {
            viewer.classList.add('hidden');
            viewer.classList.remove('flex');
            document.body.style.overflow = '';
            isZoomed = false;
            viewerImage.style.transform = 'scale(1)';
            viewerImage.style.cursor = 'zoom-in';
        }

        // Show image at index
        function showImage(index) {
            if (index < 0) index = galleryImages.length - 1;
            if (index >= galleryImages.length) index = 0;
            
            currentImageIndex = index;
            viewerImage.src = galleryImages[index];
            viewerCounter.textContent = `${index + 1} / ${galleryImages.length}`;
            
            // Reset zoom
            isZoomed = false;
            viewerImage.style.transform = 'scale(1)';
            viewerImage.style.cursor = 'zoom-in';
        }

        // Toggle zoom
        viewerImage.addEventListener('click', () => {
            isZoomed = !isZoomed;
            if (isZoomed) {
                viewerImage.style.transform = 'scale(2)';
                viewerImage.style.cursor = 'zoom-out';
            } else {
                viewerImage.style.transform = 'scale(1)';
                viewerImage.style.cursor = 'zoom-in';
            }
        });

        // Event listeners
        closeBtn.addEventListener('click', closeViewer);
        prevBtn.addEventListener('click', () => showImage(currentImageIndex - 1));
        nextBtn.addEventListener('click', () => showImage(currentImageIndex + 1));

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!viewer.classList.contains('hidden')) {
                if (e.key === 'Escape') closeViewer();
                if (e.key === 'ArrowLeft') showImage(currentImageIndex - 1);
                if (e.key === 'ArrowRight') showImage(currentImageIndex + 1);
            }
        });

        // Close on background click
        viewer.addEventListener('click', (e) => {
            if (e.target === viewer) closeViewer();
        });

        // Global function to open viewer
        window.openGalleryViewer = function(images, startIndex = 0) {
            galleryImages = images;
            currentImageIndex = startIndex;
            showImage(startIndex);
            viewer.classList.remove('hidden');
            viewer.classList.add('flex');
            document.body.style.overflow = 'hidden';
        };
    });
})();
