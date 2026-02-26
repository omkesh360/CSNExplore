#!/bin/bash

# TravelHub PHP Quick Start Script

echo "========================================="
echo "TravelHub PHP Backend - Quick Start"
echo "========================================="
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 7.4 or higher."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r 'echo PHP_VERSION;')
echo "✓ PHP Version: $PHP_VERSION"

# Check if data directory exists
if [ ! -d "data" ]; then
    echo "⚠ Creating data directory..."
    mkdir -p data
fi

# Check if uploads directory exists
if [ ! -d "public/images/uploads" ]; then
    echo "⚠ Creating uploads directory..."
    mkdir -p public/images/uploads
fi

# Set permissions
chmod 755 data
chmod 755 public/images/uploads

echo "✓ Directories configured"

# Run system check
echo ""
echo "Running system check..."
php php/test.php

if [ $? -eq 0 ]; then
    echo ""
    echo "========================================="
    echo "Starting PHP development server..."
    echo "========================================="
    echo ""
    echo "Server will be available at:"
    echo "  http://localhost:8000"
    echo ""
    echo "Press Ctrl+C to stop the server"
    echo ""
    php -S localhost:8000 router.php
else
    echo ""
    echo "❌ System check failed. Please fix the errors above."
    exit 1
fi
