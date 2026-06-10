param([string]$sourcePath, [string]$destDir)

Add-Type -AssemblyName System.Drawing

$sourceImg = [System.Drawing.Image]::FromFile($sourcePath)

$sizes = @(
    @{Name="apple-icon-57x57"; Size=57},
    @{Name="apple-icon-60x60"; Size=60},
    @{Name="apple-icon-72x72"; Size=72},
    @{Name="apple-icon-76x76"; Size=76},
    @{Name="apple-icon-114x114"; Size=114},
    @{Name="apple-icon-120x120"; Size=120},
    @{Name="apple-icon-144x144"; Size=144},
    @{Name="apple-icon-152x152"; Size=152},
    @{Name="apple-icon-180x180"; Size=180},
    @{Name="android-icon-192x192"; Size=192},
    @{Name="android-icon-512x512"; Size=512},
    @{Name="favicon-16x16"; Size=16},
    @{Name="favicon-32x32"; Size=32},
    @{Name="favicon-48x48"; Size=48},
    @{Name="favicon-96x96"; Size=96},
    @{Name="ms-icon-144x144"; Size=144},
    @{Name="apple-touch-icon"; Size=180},
    @{Name="android-chrome-192x192"; Size=192},
    @{Name="android-chrome-512x512"; Size=512}
)

foreach ($sizeInfo in $sizes) {
    $size = $sizeInfo.Size
    $name = $sizeInfo.Name
    $destPath = Join-Path $destDir "$name.png"
    
    $bmp = New-Object System.Drawing.Bitmap $size, $size
    $graphics = [System.Drawing.Graphics]::FromImage($bmp)
    
    $graphics.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
    $graphics.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
    $graphics.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
    
    $graphics.DrawImage($sourceImg, 0, 0, $size, $size)
    $bmp.Save($destPath, [System.Drawing.Imaging.ImageFormat]::Png)
    
    $graphics.Dispose()
    $bmp.Dispose()
    
    Write-Host "Created $destPath"
}

# favicon.ico
$icoPath = Join-Path $destDir "favicon.ico"
$bmpIco = New-Object System.Drawing.Bitmap 32, 32
$graphicsIco = [System.Drawing.Graphics]::FromImage($bmpIco)
$graphicsIco.CompositingQuality = [System.Drawing.Drawing2D.CompositingQuality]::HighQuality
$graphicsIco.InterpolationMode = [System.Drawing.Drawing2D.InterpolationMode]::HighQualityBicubic
$graphicsIco.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::HighQuality
$graphicsIco.DrawImage($sourceImg, 0, 0, 32, 32)
# Save as PNG first, then rename to ICO (which works in modern browsers, though not true ICO format)
$bmpIco.Save($icoPath, [System.Drawing.Imaging.ImageFormat]::Png)
$graphicsIco.Dispose()
$bmpIco.Dispose()

Write-Host "Created favicon.ico"

$sourceImg.Dispose()
Write-Host "All done!"
