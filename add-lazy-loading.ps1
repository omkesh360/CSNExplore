# PowerShell Script: Add Lazy Loading to All Images
# CSNExplore - Performance Optimization
# Date: May 10, 2026

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Add Lazy Loading to Images" -ForegroundColor Cyan
Write-Host "  CSNExplore Performance Optimization" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$rootPath = "c:\xampp\htdocs\CSNExplore"
$backupPath = "$rootPath\backups\lazy-loading-backup-$(Get-Date -Format 'yyyy-MM-dd-HHmmss')"
$excludePaths = @("vendor", "node_modules", "cache", "backups", ".git")
$fileExtensions = @("*.php", "*.html")

# Statistics
$filesProcessed = 0
$imagesUpdated = 0
$errors = 0

# Create backup directory
Write-Host "Creating backup..." -ForegroundColor Yellow
New-Item -ItemType Directory -Path $backupPath -Force | Out-Null

# Function to check if image should NOT be lazy loaded
function Should-Skip-LazyLoad {
    param($imgTag)
    
    # Skip if already has loading attribute
    if ($imgTag -match 'loading=') {
        return $true
    }
    
    # Skip if has fetchpriority="high" (LCP images)
    if ($imgTag -match 'fetchpriority=["\']high["\']') {
        return $true
    }
    
    # Skip if in hero section
    if ($imgTag -match 'hero|banner|header-logo|site-logo') {
        return $true
    }
    
    # Skip if has data-no-lazy attribute
    if ($imgTag -match 'data-no-lazy') {
        return $true
    }
    
    return $false
}

# Function to add lazy loading to image tag
function Add-LazyLoading {
    param($imgTag)
    
    if (Should-Skip-LazyLoad $imgTag) {
        return $imgTag
    }
    
    # Add loading="lazy" before the closing >
    if ($imgTag -match '^(.*?)(\s*/?>)$') {
        $before = $matches[1]
        $closing = $matches[2]
        
        # Check if already has loading attribute (double check)
        if ($before -notmatch 'loading=') {
            return "$before loading=`"lazy`"$closing"
        }
    }
    
    return $imgTag
}

# Function to process file
function Process-File {
    param($filePath)
    
    try {
        $content = Get-Content -Path $filePath -Raw -Encoding UTF8
        $originalContent = $content
        $fileImagesUpdated = 0
        
        # Find all <img> tags
        $imgPattern = '<img[^>]*>'
        $matches = [regex]::Matches($content, $imgPattern)
        
        foreach ($match in $matches) {
            $originalImg = $match.Value
            $updatedImg = Add-LazyLoading $originalImg
            
            if ($originalImg -ne $updatedImg) {
                $content = $content.Replace($originalImg, $updatedImg)
                $fileImagesUpdated++
                $script:imagesUpdated++
            }
        }
        
        # Only write if changes were made
        if ($fileImagesUpdated -gt 0) {
            # Backup original file
            $relativePath = $filePath.Substring($rootPath.Length + 1)
            $backupFile = Join-Path $backupPath $relativePath
            $backupDir = Split-Path $backupFile -Parent
            
            if (-not (Test-Path $backupDir)) {
                New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
            }
            
            Copy-Item -Path $filePath -Destination $backupFile -Force
            
            # Write updated content
            Set-Content -Path $filePath -Value $content -Encoding UTF8 -NoNewline
            
            Write-Host "  ✓ $relativePath - Updated $fileImagesUpdated images" -ForegroundColor Green
        }
        
        $script:filesProcessed++
        
    } catch {
        Write-Host "  ✗ Error processing $filePath : $_" -ForegroundColor Red
        $script:errors++
    }
}

# Get all PHP and HTML files
Write-Host "Scanning for files..." -ForegroundColor Yellow
$files = Get-ChildItem -Path $rootPath -Include $fileExtensions -Recurse | 
    Where-Object { 
        $path = $_.FullName
        $shouldInclude = $true
        foreach ($exclude in $excludePaths) {
            if ($path -like "*\$exclude\*") {
                $shouldInclude = $false
                break
            }
        }
        $shouldInclude
    }

Write-Host "Found $($files.Count) files to process" -ForegroundColor Cyan
Write-Host ""

# Process each file
Write-Host "Processing files..." -ForegroundColor Yellow
foreach ($file in $files) {
    Process-File $file.FullName
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Summary" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Files Processed: $filesProcessed" -ForegroundColor White
Write-Host "Images Updated: $imagesUpdated" -ForegroundColor Green
Write-Host "Errors: $errors" -ForegroundColor $(if ($errors -gt 0) { "Red" } else { "Green" })
Write-Host "Backup Location: $backupPath" -ForegroundColor Yellow
Write-Host ""

if ($imagesUpdated -gt 0) {
    Write-Host "✓ Lazy loading added successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor Cyan
    Write-Host "1. Test your website thoroughly" -ForegroundColor White
    Write-Host "2. Check that images load correctly" -ForegroundColor White
    Write-Host "3. Verify hero images are NOT lazy loaded" -ForegroundColor White
    Write-Host "4. Run PageSpeed Insights test" -ForegroundColor White
    Write-Host "5. If issues occur, restore from backup" -ForegroundColor White
} else {
    Write-Host "No images needed updating (already optimized)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
