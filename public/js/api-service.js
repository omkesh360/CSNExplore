// API Service Layer - Centralized API calls with authentication
const API_BASE = '';

const API = {
    // Get auth headers from localStorage
    _authHeaders() {
        const token = localStorage.getItem('token');
        const headers = { 'Content-Type': 'application/json' };
        if (token) headers['Authorization'] = `Bearer ${token}`;
        return headers;
    },

    // Helper function to handle responses
    async handleResponse(response) {
        if (!response.ok) {
            const error = await response.json().catch(() => ({ error: 'Request failed' }));
            throw new Error(error.error || error.message || `HTTP ${response.status}`);
        }
        return response.json();
    },

    // ==========================================
    // PUBLIC LISTING APIs (no auth needed)
    // ==========================================
    async getStays(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/stays?${params}`);
        return this.handleResponse(response);
    },

    async getCars(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/cars?${params}`);
        return this.handleResponse(response);
    },

    async getBikes(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/bikes?${params}`);
        return this.handleResponse(response);
    },

    async getRestaurants(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/restaurants?${params}`);
        return this.handleResponse(response);
    },

    async getAttractions(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/attractions?${params}`);
        return this.handleResponse(response);
    },

    async getBuses(filters = {}) {
        const params = new URLSearchParams(filters);
        const response = await fetch(`${API_BASE}/api/buses?${params}`);
        return this.handleResponse(response);
    },

    // Get single item by ID
    async getStayById(id) {
        const response = await fetch(`${API_BASE}/api/stays/${id}`);
        return this.handleResponse(response);
    },

    async getCarById(id) {
        const response = await fetch(`${API_BASE}/api/cars/${id}`);
        return this.handleResponse(response);
    },

    async getBikeById(id) {
        const response = await fetch(`${API_BASE}/api/bikes/${id}`);
        return this.handleResponse(response);
    },

    async getRestaurantById(id) {
        const response = await fetch(`${API_BASE}/api/restaurants/${id}`);
        return this.handleResponse(response);
    },

    async getAttractionById(id) {
        const response = await fetch(`${API_BASE}/api/attractions/${id}`);
        return this.handleResponse(response);
    },

    async getBusById(id) {
        const response = await fetch(`${API_BASE}/api/buses/${id}`);
        return this.handleResponse(response);
    },

    // ==========================================
    // PROTECTED APIs (require auth token)
    // ==========================================

    // Bookings
    async getBookings() {
        const response = await fetch(`${API_BASE}/api/bookings`, {
            headers: this._authHeaders()
        });
        return this.handleResponse(response);
    },

    async createBooking(bookingData) {
        const response = await fetch(`${API_BASE}/api/bookings`, {
            method: 'POST',
            headers: this._authHeaders(),
            body: JSON.stringify(bookingData)
        });
        return this.handleResponse(response);
    },

    // Users (Admin only)
    async getUsers() {
        const response = await fetch(`${API_BASE}/api/users`, {
            headers: this._authHeaders()
        });
        return this.handleResponse(response);
    },

    // Vendors (Admin only)
    async getVendors() {
        const response = await fetch(`${API_BASE}/api/vendors`, {
            headers: this._authHeaders()
        });
        return this.handleResponse(response);
    },

    // ==========================================
    // ADMIN LISTING APIs
    // ==========================================
    async addListing(category, listingData) {
        const response = await fetch(`${API_BASE}/api/${category}`, {
            method: 'POST',
            headers: this._authHeaders(),
            body: JSON.stringify(listingData)
        });
        return this.handleResponse(response);
    },

    async updateListing(category, id, listingData) {
        const response = await fetch(`${API_BASE}/api/${category}/${id}`, {
            method: 'PUT',
            headers: this._authHeaders(),
            body: JSON.stringify(listingData)
        });
        return this.handleResponse(response);
    },

    async deleteListing(category, id) {
        const response = await fetch(`${API_BASE}/api/${category}/${id}`, {
            method: 'DELETE',
            headers: this._authHeaders()
        });
        return this.handleResponse(response);
    },

    // ==========================================
    // AUTH APIs
    // ==========================================
    async login(credentials) {
        const response = await fetch(`${API_BASE}/api/auth/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(credentials)
        });
        return this.handleResponse(response);
    },

    async register(userData) {
        const response = await fetch(`${API_BASE}/api/auth/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(userData)
        });
        return this.handleResponse(response);
    },

    async verifyToken() {
        const response = await fetch(`${API_BASE}/api/auth/me`, {
            headers: this._authHeaders()
        });
        return this.handleResponse(response);
    }
};

// Make API available globally
if (typeof window !== 'undefined') {
    window.API = API;
}
