#!/bin/bash

# API Test Script - Tests both Node.js and PHP backends

echo "========================================="
echo "TravelHub API Test Script"
echo "========================================="
echo ""

# Check which backend to test
BACKEND=${1:-"php"}
if [ "$BACKEND" = "node" ]; then
    BASE_URL="http://localhost:3000"
    echo "Testing Node.js backend at $BASE_URL"
elif [ "$BACKEND" = "php" ]; then
    BASE_URL="http://localhost:8000"
    echo "Testing PHP backend at $BASE_URL"
else
    echo "Usage: ./test-api.sh [php|node]"
    exit 1
fi

echo ""
echo "Make sure the server is running!"
echo ""

# Test counter
PASSED=0
FAILED=0

# Helper function to test endpoint
test_endpoint() {
    local method=$1
    local endpoint=$2
    local description=$3
    local data=$4
    local token=$5
    
    echo -n "Testing: $description... "
    
    if [ -n "$token" ]; then
        HEADERS="-H 'Authorization: Bearer $token'"
    else
        HEADERS=""
    fi
    
    if [ "$method" = "GET" ]; then
        RESPONSE=$(curl -s -w "\n%{http_code}" $HEADERS "$BASE_URL$endpoint")
    elif [ "$method" = "POST" ]; then
        RESPONSE=$(curl -s -w "\n%{http_code}" -X POST $HEADERS -H "Content-Type: application/json" -d "$data" "$BASE_URL$endpoint")
    fi
    
    HTTP_CODE=$(echo "$RESPONSE" | tail -n1)
    BODY=$(echo "$RESPONSE" | head -n-1)
    
    if [ "$HTTP_CODE" -ge 200 ] && [ "$HTTP_CODE" -lt 300 ]; then
        echo "✓ PASSED (HTTP $HTTP_CODE)"
        PASSED=$((PASSED + 1))
    else
        echo "✗ FAILED (HTTP $HTTP_CODE)"
        echo "  Response: $BODY"
        FAILED=$((FAILED + 1))
    fi
}

# Run tests
echo "Running API tests..."
echo ""

# Public endpoints (no auth)
test_endpoint "GET" "/api/stays" "Get all stays"
test_endpoint "GET" "/api/cars" "Get all cars"
test_endpoint "GET" "/api/bikes" "Get all bikes"
test_endpoint "GET" "/api/restaurants" "Get all restaurants"
test_endpoint "GET" "/api/attractions" "Get all attractions"
test_endpoint "GET" "/api/buses" "Get all buses"
test_endpoint "GET" "/api/homepage-content" "Get homepage content"

# Auth endpoints
echo ""
echo "Testing authentication..."
test_endpoint "POST" "/api/auth/login" "Login with admin credentials" '{"email":"admin@travelhub.com","password":"password123"}'

# Extract token from last response (simplified - in real test would parse JSON)
# For now, just test that login endpoint responds

echo ""
echo "========================================="
echo "Test Results"
echo "========================================="
echo "Passed: $PASSED"
echo "Failed: $FAILED"
echo ""

if [ $FAILED -eq 0 ]; then
    echo "✓ All tests passed!"
    exit 0
else
    echo "✗ Some tests failed"
    exit 1
fi
