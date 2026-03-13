// Admin Sidebar Component
// This script generates the admin sidebar for all admin pages

function generateAdminSidebar(activePage = '') {
    return `
        <aside id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col border-r border-slate-200 bg-white shadow-[4px_0_24px_rgba(0,0,0,0.02)] shrink-0 transform -translate-x-full md:translate-x-0 md:static md:flex transition-transform duration-300">
            <div class="flex h-20 items-center px-6 border-b border-primary-dark bg-primary shrink-0">
                <a href="index.html" class="flex items-center gap-2">
                    <img loading="lazy" decoding="async" src="/images/travelhub.png" alt="CSNExplore" class="h-8 filter brightness-0 invert">
                </a>
            </div>
            <div class="flex-1 overflow-y-auto px-4 py-6">
                <nav class="flex flex-col gap-1.5">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-3 mb-2">Main Menu</p>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'dashboard' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'dashboard' ? 'relative' : ''}"
                        href="admin-dashboard.html">
                        ${activePage === 'dashboard' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">dashboard</span>
                        <span class="text-[13px]">Dashboard</span>
                    </a>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-3 mb-2 mt-4">Page Editor</p>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'homepage' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'homepage' ? 'relative' : ''}"
                        href="admin-homepage.html">
                        ${activePage === 'homepage' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">home</span>
                        <span class="text-[13px]">Homepage Editor</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'about' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'about' ? 'relative' : ''}"
                        href="admin-about-editor.html">
                        ${activePage === 'about' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">info</span>
                        <span class="text-[13px]">About Page Editor</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'contact' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'contact' ? 'relative' : ''}"
                        href="admin-contact-editor.html">
                        ${activePage === 'contact' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">mail</span>
                        <span class="text-[13px]">Contact Page Editor</span>
                    </a>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-3 mb-2 mt-4">Listings</p>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'add-listing' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'add-listing' ? 'relative' : ''}"
                        href="admin-add-listing.html">
                        ${activePage === 'add-listing' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        <span class="text-[13px]">Add Listing</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'manage-listings' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'manage-listings' ? 'relative' : ''}"
                        href="admin-manage-listings.html">
                        ${activePage === 'manage-listings' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">edit_document</span>
                        <span class="text-[13px]">Manage Listings</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'listing-reorder' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'listing-reorder' ? 'relative' : ''}"
                        href="admin-listing-reorder.html">
                        ${activePage === 'listing-reorder' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">reorder</span>
                        <span class="text-[13px]">Listing Order</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'cards-editor' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'cards-editor' ? 'relative' : ''}"
                        href="admin-cards-editor.html">
                        ${activePage === 'cards-editor' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">view_list</span>
                        <span class="text-[13px]">Cards Editor</span>
                    </a>

                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest pl-3 mb-2 mt-4">Management</p>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'users' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'users' ? 'relative' : ''}"
                        href="admin-users.html">
                        ${activePage === 'users' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">group</span>
                        <span class="text-[13px]">Users</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'images' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'images' ? 'relative' : ''}"
                        href="admin-images.html">
                        ${activePage === 'images' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">perm_media</span>
                        <span class="text-[13px]">Images Manager</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'blogs' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'blogs' ? 'relative' : ''}"
                        href="admin-blogs-generator.html">
                        ${activePage === 'blogs' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">edit_note</span>
                        <span class="text-[13px]">Blogs Generator</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'marquee' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'marquee' ? 'relative' : ''}"
                        href="admin-marquee.html">
                        ${activePage === 'marquee' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">campaign</span>
                        <span class="text-[13px]">Marquee Announcements</span>
                    </a>
                    <a class="flex items-center gap-3 rounded-xl px-3 py-2.5 ${activePage === 'performance' ? 'bg-blue-50 text-primary font-bold' : 'text-slate-500 font-medium hover:bg-slate-50 hover:text-slate-900'} transition-all ${activePage === 'performance' ? 'relative' : ''}"
                        href="admin-performance.html">
                        ${activePage === 'performance' ? '<div class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-primary rounded-r-full"></div>' : ''}
                        <span class="material-symbols-outlined text-[20px]">speed</span>
                        <span class="text-[13px]">Performance Monitor</span>
                    </a>
                </nav>
            </div>
            <div class="border-t border-slate-100 p-4 shrink-0 bg-slate-50/50">
                <button id="admin-logout-btn"
                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-slate-600 transition-colors hover:bg-red-50 hover:text-red-600 font-medium w-full text-left">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span class="text-[13px]">Logout</span>
                </button>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div id="sidebar-overlay"
            class="fixed inset-0 bg-slate-900/60 z-40 hidden md:hidden backdrop-blur-sm transition-opacity"></div>
    `;
}

// Initialize sidebar on page load
document.addEventListener('DOMContentLoaded', function() {
    const sidebarContainer = document.getElementById('admin-sidebar-container');
    if (sidebarContainer) {
        const activePage = sidebarContainer.dataset.activePage || '';
        sidebarContainer.innerHTML = generateAdminSidebar(activePage);
        
        // Mobile sidebar toggle
        const mobileToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('admin-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        if (mobileToggle && sidebar && overlay) {
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });
            
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    }
});
