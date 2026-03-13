# CSNExplore Travel Hub - API Documentation

## Overview
This document provides comprehensive documentation for all API endpoints available in the CSNExplore Travel Hub platform.

**Base URL:** `https://csnexplore.com/api`

**Authentication:** Most endpoints require JWT token authentication via Bearer token in the Authorization header.

---

## Table of Contents
1. [Authentication APIs](#authentication-apis)
2. [Listings APIs](#listings-apis)
3. [Content Management APIs](#content-management-apis)
4. [Utility APIs](#utility-apis)
5. [Error Handling](#error-handling)
6. [Rate Limiting](#rate-limiting)

---

## Authentication APIs

### POST /api/auth
Login endpoint for user authentication.

**Request:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (Success):**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "email": "user@example.com",
    "name": "John Doe",
    "role": "user"
  }
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

---

## Listings APIs

### GET /api/listings
Retrieve listings by category.

**Query Parameters:**
- `category` (required): stays, cars, bikes, restaurants, attractions, buses
- `limit` (optional): Number of results (default: 50)
- `offset` (optional): Pagination offset (default: 0)

**Example:**
```
GET /api/listings?category=stays&limit=10
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Hotel Paradise",
      "category": "stays",
      "price": 2500,
      "rating": 4.5,
      "image": "/images/hotel1.jpg"
    }
  ],
  "total": 45
}
```

### POST /api/listings
Create a new listing (Admin only).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "category": "stays",
  "name": "New Hotel",
  "description": "Beautiful hotel in the city center",
  "price": 3000,
  "amenities": ["WiFi", "Pool", "Parking"],
  "images": ["/images/hotel-new.jpg"]
}
```

**Response:**
```json
{
  "success": true,
  "id": 46,
  "message": "Listing created successfully"
}
```

### PUT /api/listings/:id
Update an existing listing (Admin only).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "name": "Updated Hotel Name",
  "price": 3500
}
```

**Response:**
```json
{
  "success": true,
  "message": "Listing updated successfully"
}
```

### DELETE /api/listings/:id
Delete a listing (Admin only).

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "message": "Listing deleted successfully"
}
```

---

## Content Management APIs

### GET /api/homepage
Retrieve homepage content.

**Response:**
```json
{
  "success": true,
  "data": {
    "hero": {
      "title": "Explore Chhatrapati Sambhajinagar",
      "subtitle": "Your Way"
    },
    "categories": [...],
    "trendingTransport": [...]
  }
}
```

### PUT /api/homepage
Update homepage content (Admin only).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "hero": {
    "title": "New Title",
    "subtitle": "New Subtitle"
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Homepage updated successfully"
}
```

### GET /api/about-contact
Retrieve About or Contact page content.

**Query Parameters:**
- `section` (required): about or contact

**Example:**
```
GET /api/about-contact?section=about
```

**Response:**
```json
{
  "success": true,
  "data": {
    "hero": {...},
    "mission": {...},
    "story": [...],
    "team": [...]
  }
}
```

### PUT /api/about-contact
Update About or Contact page content (Admin only).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request:**
```json
{
  "section": "about",
  "data": {
    "hero": {
      "title": "New About Title"
    }
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Content updated successfully"
}
```

---

## Utility APIs

### POST /api/upload
Upload images (Admin only).

**Headers:**
```
Authorization: Bearer <token>
Content-Type: multipart/form-data
```

**Request:**
```
file: <image file>
```

**Response:**
```json
{
  "success": true,
  "url": "/images/uploaded-image.jpg",
  "filename": "uploaded-image.jpg"
}
```

### GET /api/cache
Get cache statistics (Admin only).

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "stats": {
    "total_files": 15,
    "total_size": 2048576,
    "oldest": "2024-01-01 10:00:00",
    "newest": "2024-01-15 15:30:00"
  }
}
```

### POST /api/cache/clear
Clear cache (Admin only).

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "message": "Cache cleared successfully"
}
```

### GET /api/dashboard
Get dashboard statistics (Admin only).

**Headers:**
```
Authorization: Bearer <token>
```

**Response:**
```json
{
  "success": true,
  "stats": {
    "stays": 45,
    "cars": 23,
    "bikes": 18,
    "restaurants": 67,
    "attractions": 34,
    "users": 156
  }
}
```

---

## Error Handling

All API endpoints follow a consistent error response format:

**Error Response:**
```json
{
  "success": false,
  "error": "Error message",
  "code": "ERROR_CODE"
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `429` - Too Many Requests
- `500` - Internal Server Error

**Common Error Codes:**
- `INVALID_INPUT` - Invalid request data
- `UNAUTHORIZED` - Missing or invalid authentication
- `FORBIDDEN` - Insufficient permissions
- `NOT_FOUND` - Resource not found
- `RATE_LIMIT_EXCEEDED` - Too many requests
- `SERVER_ERROR` - Internal server error

---

## Rate Limiting

API requests are rate-limited to prevent abuse:

**Limits:**
- Anonymous users: 100 requests per 15 minutes
- Authenticated users: 1000 requests per 15 minutes
- Admin users: 5000 requests per 15 minutes

**Rate Limit Headers:**
```
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640000000
```

When rate limit is exceeded:
```json
{
  "success": false,
  "error": "Rate limit exceeded",
  "retry_after": 300
}
```

---

## Authentication

Most endpoints require JWT token authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your-jwt-token>
```

**Token Expiration:** Tokens expire after 24 hours.

**Refresh Token:** Use the `/api/auth/refresh` endpoint to get a new token.

---

## CORS

Cross-Origin Resource Sharing (CORS) is enabled for the following origins:
- `https://csnexplore.com`
- `http://localhost:*` (development only)

---

## Webhooks

Webhooks are available for the following events:
- `listing.created`
- `listing.updated`
- `listing.deleted`
- `booking.created`
- `user.registered`

Configure webhooks in the admin panel under Settings > Webhooks.

---

## Support

For API support, contact:
- Email: support@csnexplore.com
- Documentation: https://csnexplore.com/docs
- Status Page: https://status.csnexplore.com

---

## Changelog

### Version 1.0.0 (2024-01-15)
- Initial API release
- Authentication endpoints
- Listings CRUD operations
- Content management endpoints
- File upload functionality
- Cache management
- Dashboard statistics

---

**Last Updated:** 2024-01-15
**API Version:** 1.0.0
