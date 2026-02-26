// Configuration
const CONFIG = {
    N8N_WEBHOOK_URL: 'https://primary-production-433c.up.railway.app/webhook/travel-hub-search',
    DEFAULT_CURRENCY: 'INR'
};

// State Management
const state = {
    currency: localStorage.getItem('selectedCurrency') || CONFIG.DEFAULT_CURRENCY,
    user: null,
    exchangeRates: { USD: 1, EUR: 0.92, INR: 83.12 },
    symbols: { USD: '$', EUR: '€', INR: '₹' }
};

// Utilities
const utils = {
    showNotification: (message, type = 'success') => {
        const existing = document.querySelector('.th-notification');
        if (existing) existing.remove();

        const notification = document.createElement('div');
        const bgColor = type === 'error' ? 'bg-red-600' : type === 'warning' ? 'bg-yellow-500' : 'bg-primary';
        notification.className = `th-notification fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-[9999] flex items-center gap-2 transition-all duration-300`;
        notification.style.opacity = '0';
        notification.style.transform = 'translateY(10px)';
        notification.innerHTML = `
            <span class="material-symbols-outlined">${type === 'error' ? 'error' : type === 'warning' ? 'warning' : 'check_circle'}</span>
            <span>${message}</span>
        `;
        document.body.appendChild(notification);

        // Animate in
        requestAnimationFrame(() => {
            notification.style.opacity = '1';
            notification.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(10px)';
            setTimeout(() => notification.remove(), 300);
        }, 3500);
    },

    getAuthHeaders: () => {
        const token = localStorage.getItem('token');
        return token ? { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' } : { 'Content-Type': 'application/json' };
    },

    formatPrice: (amount) => {
        return `₹${Number(amount).toLocaleString('en-IN')}`;
    }
};

// Expose to window for other scripts
window.utils = utils;
window.state = state;
window.CONFIG = CONFIG;

// Core Logic
document.addEventListener('DOMContentLoaded', () => {
    checkLoginState();
    setupAuthListeners();
    initializeSearch();
    initializeBookingButtons();
    initializeMobileInteractions();
    initializeGlobalTopBar();
});

// ==========================================
// GLOBAL DYNAMIC CONTENT
// ==========================================
async function initializeGlobalTopBar() {
    try {
        // Cache this lightly or just fetch. Since it's a small JSON, fetching is fine.
        const res = await fetch('/api/homepage-content');
        if (!res.ok) return;
        const hpData = await res.json();
        const hero = hpData.hero;

        if (!hero) return;

        // Contact Bar
        const phoneLink = document.getElementById('hp-phone-link');
        const phoneText = document.getElementById('hp-phone-text');
        if (phoneLink && hero.phone) phoneLink.href = `tel:${hero.phone.replace(/\\s+/g, '')}`;
        if (phoneText && hero.phone) phoneText.textContent = hero.phone;

        const waLink = document.getElementById('hp-wa-link');
        if (waLink && hero.whatsapp) waLink.href = `https://wa.me/${hero.whatsapp.replace(/\\s+/g, '')}`;

        // Save globally for other scripts (like listings.js)
        window.dynamicContact = {
            phone: hero.phone || '+918600968888',
            whatsapp: hero.whatsapp ? hero.whatsapp.replace(/\\D/g, '') : '918600968888'
        };

        // Marquee
        const marquee = document.getElementById('hp-marquee');
        if (marquee && hero.marqueeItems && hero.marqueeItems.length > 0) {
            // Function esc() might not exist globally yet, so duplicate a small esc here
            const esc = (str) => String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
            const itemsHtml = hero.marqueeItems.map(item => `<span class="mx-4">${esc(item)}</span>`).join('');
            const hiddenHtml = hero.marqueeItems.map(item => `<span class="mx-4" aria-hidden="true">${esc(item)}</span>`).join('');
            marquee.innerHTML = itemsHtml + hiddenHtml;
        }
    } catch (err) {
        console.warn('Failed to load global top bar:', err);
    }
}

// ==========================================
// MOBILE INTERACTIONS
// ==========================================
function initializeMobileInteractions() {
    const filterBtns = document.querySelectorAll('[data-action="open-filters"]');
    const sidebar = document.getElementById('listing-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const closeSidebarBtn = document.getElementById('close-sidebar');

    function toggleSidebar(show) {
        if (!sidebar) return;
        if (show) {
            sidebar.classList.remove('hidden');
            setTimeout(() => sidebar.classList.remove('-translate-x-full'), 10);
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            sidebar.classList.add('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            document.body.style.overflow = '';
            setTimeout(() => {
                if (sidebar.classList.contains('-translate-x-full')) sidebar.classList.add('hidden');
            }, 300);
        }
    }

    filterBtns.forEach(btn => btn.addEventListener('click', () => toggleSidebar(true)));
    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', () => toggleSidebar(false));

    // Sort Bottom Sheet
    const sortSheet = document.createElement('div');
    sortSheet.className = 'fixed bottom-0 left-0 w-full bg-white z-50 rounded-t-2xl shadow-2xl transform translate-y-full transition-transform duration-300 lg:hidden pb-6';
    sortSheet.innerHTML = `
        <div class="p-4">
            <div class="w-12 h-1 bg-gray-300 rounded-full mx-auto mb-6"></div>
            <h3 class="font-bold text-lg mb-4 text-center text-text-main">Sort by</h3>
            <div class="space-y-2">
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 font-medium text-primary bg-primary/5 transition-colors">Recommended</button>
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 font-medium text-text-main transition-colors">Price (Low to High)</button>
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 font-medium text-text-main transition-colors">Price (High to Low)</button>
                <button class="w-full text-left px-4 py-3 rounded-lg hover:bg-gray-50 font-medium text-text-main transition-colors">Top Rated</button>
            </div>
        </div>
    `;
    document.body.appendChild(sortSheet);

    function toggleSort(show) {
        if (show) {
            sortSheet.classList.remove('translate-y-full');
            if (overlay) overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            sortSheet.classList.add('translate-y-full');
            if (!sidebar || sidebar.classList.contains('-translate-x-full')) {
                if (overlay) overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }
    }

    document.querySelectorAll('button').forEach(btn => {
        const icon = btn.querySelector('.material-symbols-outlined');
        if (btn.textContent.includes('Sort') && icon && icon.textContent.includes('sort')) {
            btn.addEventListener('click', () => toggleSort(true));
        }
    });

    sortSheet.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', () => {
            sortSheet.querySelectorAll('button').forEach(b => {
                b.classList.remove('text-primary', 'bg-primary/5');
                b.classList.add('text-text-main');
            });
            btn.classList.add('text-primary', 'bg-primary/5');
            btn.classList.remove('text-text-main');
            utils.showNotification(`Sorted by ${btn.textContent.trim()}`);
            toggleSort(false);
        });
    });

    if (overlay) {
        overlay.addEventListener('click', () => {
            toggleSidebar(false);
            toggleSort(false);
        });
    }
}

// ==========================================
// SEARCH
// ==========================================
function initializeSearch() {
    // Homepage search button
    const searchBtn = document.getElementById('main-search-btn') ||
        document.querySelector('button.bg-primary.text-white.font-bold.text-lg');
    const locationInput = document.querySelector('input[placeholder*="Where"]') ||
        document.querySelector('input[placeholder*="where"]');

    if (searchBtn && locationInput) {
        searchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const location = locationInput.value.trim();

            // Determine active tab/category
            const activeTab = document.querySelector('.tab-btn.active, [data-active="true"]');
            let targetPage = 'stays.html';
            if (activeTab) {
                const text = activeTab.textContent.toLowerCase();
                if (text.includes('car')) targetPage = 'car-rentals.html';
                else if (text.includes('bike')) targetPage = 'bike-rentals.html';
                else if (text.includes('restaurant')) targetPage = 'restaurant.html';
                else if (text.includes('attraction')) targetPage = 'attraction.html';
                else if (text.includes('bus')) targetPage = 'bus.html';
            }

            // Navigate with search params
            const params = location ? `?q=${encodeURIComponent(location)}` : '';
            window.location.href = targetPage + params;
        });
    }
}

// ==========================================
// BOOKING BUTTONS
// ==========================================
function initializeBookingButtons() {
    const potentialButtons = [
        ...document.querySelectorAll('button'),
        ...document.querySelectorAll('a.bg-primary, a.bg-[#25D366], a.text-primary.font-bold')
    ];

    potentialButtons.forEach(btn => {
        const text = btn.textContent.trim().toLowerCase();
        if (text.includes('book') || text.includes('rent') || text.includes('reserve') || text.includes('check rates')) {
            btn.addEventListener('click', (e) => {
                let itemCard = btn.closest('[data-id]');
                let title, price, id;

                if (itemCard) {
                    title = itemCard.dataset.title;
                    price = itemCard.dataset.price;
                    id = itemCard.dataset.id;
                } else if (document.body.dataset.id) {
                    title = document.body.dataset.title;
                    price = document.body.dataset.price;
                    id = document.body.dataset.id;
                } else {
                    itemCard = btn.closest('.bg-white');
                    title = itemCard?.querySelector('h3')?.textContent || itemCard?.querySelector('h1')?.textContent || 'Unknown Item';
                    const priceEl = itemCard?.querySelector('[data-price-usd]') || itemCard?.querySelector('.text-primary');
                    price = priceEl ? priceEl.textContent : '0';
                }

                if (!title) title = 'Unknown Item';

                const user = state.user ? state.user.name : 'Guest User';
                const amount = parseFloat(price?.toString().replace(/[^0-9.]/g, '')) || 0;

                const newBooking = {
                    user: user,
                    item: title,
                    date: new Date().toISOString().split('T')[0],
                    status: 'Pending',
                    amount: amount
                };

                // Only save booking if user is logged in with a token
                const token = localStorage.getItem('token');
                if (token) {
                    fetch('/api/bookings', {
                        method: 'POST',
                        headers: utils.getAuthHeaders(),
                        body: JSON.stringify(newBooking)
                    }).catch(err => console.warn('Booking save skipped:', err));
                }

                utils.showNotification(`Booking initiated for ${title}!`);
            });
        }
    });
}

// ==========================================
// AUTHENTICATION
// ==========================================
function checkLoginState() {
    try {
        const userStr = localStorage.getItem('user');
        if (userStr) {
            state.user = JSON.parse(userStr);
            updateAuthUI(true);
        } else {
            updateAuthUI(false);
        }
    } catch (e) {
        console.error('User parse error', e);
        logout();
    }
}

function updateAuthUI(isLoggedIn) {
    const authButtons = document.getElementById('auth-buttons');
    const userProfile = document.getElementById('user-profile');
    const userNameDisplay = document.getElementById('user-name-display');

    if (isLoggedIn && state.user) {
        if (authButtons) authButtons.classList.add('hidden');
        if (userProfile) userProfile.classList.remove('hidden');
        if (userNameDisplay) userNameDisplay.textContent = state.user.name;
    } else {
        if (authButtons) authButtons.classList.remove('hidden');
        if (userProfile) userProfile.classList.add('hidden');
    }
}

function setupAuthListeners() {
    // Logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            logout();
        });
    }

    // Login form
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email')?.value?.trim();
            const password = document.getElementById('password')?.value;
            const submitBtn = loginForm.querySelector('button[type="submit"]');
            const errorDiv = document.getElementById('login-error');
            const errorText = document.getElementById('login-error-text');

            if (!email || !password) {
                utils.showNotification('Please enter email and password', 'error');
                return;
            }

            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Signing in...';
            }
            if (errorDiv) errorDiv.classList.add('hidden');

            await login(email, password, submitBtn, errorDiv, errorText);
        });
    }

    // Register form
    const registerForm = document.getElementById('registration-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const firstName = document.getElementById('first-name')?.value?.trim();
            const lastName = document.getElementById('last-name')?.value?.trim();
            const email = document.getElementById('email')?.value?.trim();
            const password = document.getElementById('password')?.value;
            const submitBtn = registerForm.querySelector('button[type="submit"]');

            if (!firstName || !lastName || !email || !password) {
                utils.showNotification('Please fill in all fields', 'error');
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Creating account...';
            }

            await register(`${firstName} ${lastName}`, email, password, submitBtn);
        });
    }
}

async function login(email, password, submitBtn, errorDiv, errorText) {
    try {
        const response = await fetch('/api/auth/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Invalid credentials');
        }

        // Store user and token
        localStorage.setItem('user', JSON.stringify(data.user));
        localStorage.setItem('token', data.token);
        state.user = data.user;

        updateAuthUI(true);
        utils.showNotification(`Welcome back, ${data.user.name}!`);

        setTimeout(() => {
            if (data.user.role === 'Admin') {
                window.location.href = 'admin-dashboard.html';
            } else {
                window.location.href = 'index.html';
            }
        }, 800);

    } catch (error) {
        console.error('Login error:', error);
        if (errorDiv && errorText) {
            errorText.textContent = error.message || 'Login failed. Please try again.';
            errorDiv.classList.remove('hidden');
        } else {
            utils.showNotification(error.message || 'Login failed', 'error');
        }
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Sign In';
        }
    }
}

async function register(name, email, password, submitBtn) {
    try {
        // Check if user already exists by trying to read users (public endpoint not available)
        // We'll POST to a register endpoint — if it doesn't exist, fall back gracefully
        const response = await fetch('/api/auth/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, email, password })
        });

        if (response.ok) {
            const data = await response.json();
            localStorage.setItem('user', JSON.stringify(data.user));
            if (data.token) localStorage.setItem('token', data.token);
            state.user = data.user;
        } else {
            // Fallback: store locally (server may not have register endpoint)
            const user = { name, email, role: 'user' };
            localStorage.setItem('user', JSON.stringify(user));
            state.user = user;
        }

        updateAuthUI(true);
        utils.showNotification(`Welcome to TravelHub, ${name}!`);

        setTimeout(() => window.location.href = 'index.html', 800);

    } catch (error) {
        // Network error or no register endpoint — store locally
        const user = { name, email, role: 'user' };
        localStorage.setItem('user', JSON.stringify(user));
        state.user = user;
        updateAuthUI(true);
        utils.showNotification(`Welcome to TravelHub, ${name}!`);
        setTimeout(() => window.location.href = 'index.html', 800);
    }

    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Create Account';
    }
}

function logout() {
    localStorage.removeItem('user');
    localStorage.removeItem('token');
    state.user = null;
    updateAuthUI(false);
    utils.showNotification('Logged out successfully');
    setTimeout(() => window.location.href = 'index.html', 500);
}
