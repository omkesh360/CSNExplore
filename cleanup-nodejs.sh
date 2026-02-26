#!/bin/bash

# Cleanup Node.js Files Script
# This script removes Node.js-specific files, keeping only the PHP backend

echo "========================================="
echo "TravelHub - Node.js Files Cleanup"
echo "========================================="
echo ""
echo "This script will remove Node.js-specific files:"
echo "  - server.js and duplicates"
echo "  - package.json and package-lock.json"
echo "  - node_modules/"
echo "  - reset_password.js"
echo "  - vercel.json"
echo "  - All duplicate files (*\ 2.*)"
echo ""
echo "⚠️  WARNING: This action cannot be undone!"
echo ""
read -p "Do you want to continue? (yes/no): " confirm

if [ "$confirm" != "yes" ]; then
    echo "Cleanup cancelled."
    exit 0
fi

echo ""
echo "Creating backup..."
BACKUP_DIR="nodejs-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# Backup Node.js files
echo "Backing up Node.js files to $BACKUP_DIR/"
[ -f "server.js" ] && cp server.js "$BACKUP_DIR/"
[ -f "server 2.js" ] && cp "server 2.js" "$BACKUP_DIR/"
[ -f "reset_password.js" ] && cp reset_password.js "$BACKUP_DIR/"
[ -f "reset_password 2.js" ] && cp "reset_password 2.js" "$BACKUP_DIR/"
[ -f "package.json" ] && cp package.json "$BACKUP_DIR/"
[ -f "package 2.json" ] && cp "package 2.json" "$BACKUP_DIR/"
[ -f "package-lock.json" ] && cp package-lock.json "$BACKUP_DIR/"
[ -f "package-lock 2.json" ] && cp "package-lock 2.json" "$BACKUP_DIR/"
[ -f "vercel.json" ] && cp vercel.json "$BACKUP_DIR/"
[ -f "vercel 2.json" ] && cp "vercel 2.json" "$BACKUP_DIR/"

echo "✓ Backup created"
echo ""

echo "Removing Node.js files..."

# Remove Node.js backend
[ -f "server.js" ] && rm server.js && echo "  ✓ Removed server.js"
[ -f "server 2.js" ] && rm "server 2.js" && echo "  ✓ Removed server 2.js"
[ -f "reset_password.js" ] && rm reset_password.js && echo "  ✓ Removed reset_password.js"
[ -f "reset_password 2.js" ] && rm "reset_password 2.js" && echo "  ✓ Removed reset_password 2.js"

# Remove npm files
[ -f "package.json" ] && rm package.json && echo "  ✓ Removed package.json"
[ -f "package 2.json" ] && rm "package 2.json" && echo "  ✓ Removed package 2.json"
[ -f "package-lock.json" ] && rm package-lock.json && echo "  ✓ Removed package-lock.json"
[ -f "package-lock 2.json" ] && rm "package-lock 2.json" && echo "  ✓ Removed package-lock 2.json"

# Remove Vercel config
[ -f "vercel.json" ] && rm vercel.json && echo "  ✓ Removed vercel.json"
[ -f "vercel 2.json" ] && rm "vercel 2.json" && echo "  ✓ Removed vercel 2.json"

# Remove node_modules
if [ -d "node_modules" ]; then
    echo "  Removing node_modules/ (this may take a moment)..."
    rm -rf node_modules
    echo "  ✓ Removed node_modules/"
fi

# Remove other duplicate files
echo ""
echo "Removing duplicate files..."
for file in *\ 2.*; do
    if [ -f "$file" ]; then
        rm "$file"
        echo "  ✓ Removed $file"
    fi
done

echo ""
echo "========================================="
echo "Cleanup Complete!"
echo "========================================="
echo ""
echo "Removed:"
echo "  ✓ Node.js backend files"
echo "  ✓ npm configuration files"
echo "  ✓ node_modules directory"
echo "  ✓ Duplicate files"
echo ""
echo "Backup saved to: $BACKUP_DIR/"
echo ""
echo "Your application is now 100% PHP!"
echo ""
echo "To restore Node.js files, copy them back from the backup directory."
echo ""
echo "Next steps:"
echo "  1. Test the PHP backend: ./start-php.sh"
echo "  2. Run diagnostics: php php/test.php"
echo "  3. Test API: ./test-api.sh php"
echo ""
