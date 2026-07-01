$ErrorActionPreference = 'Stop'
[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12

$targetDir = "c:\xampp\htdocs\CSNExplore\images\gallery"
if (!(Test-Path -Path $targetDir)) { New-Item -ItemType Directory -Path $targetDir | Out-Null }

$urlCache = @{}

function Process-File($filePath) {
    $content = Get-Content $filePath -Raw
    
    $regex = 'src="(https?://[^"]+)"'
    $matches = [regex]::Matches($content, $regex)
    
    foreach ($match in $matches) {
        $url = $match.Groups[1].Value
        if ($url -match "unsplash|magnific|pinimg") {
            if ($urlCache.ContainsKey($url)) {
                $filename = $urlCache[$url]
            } else {
                $filename = "img-" + [guid]::NewGuid().ToString().Substring(0,8) + ".jpg"
                $destPath = Join-Path $targetDir $filename
                try {
                    Invoke-WebRequest -Uri $url -OutFile $destPath -UseBasicParsing -TimeoutSec 15
                    $urlCache[$url] = $filename
                    Write-Host "Downloaded: $filename"
                } catch {
                    Write-Host "Failed to download: $url"
                    continue
                }
            }
            $newSrc = 'src="<?php echo BASE_PATH; ?>/images/gallery/' + $filename + '"'
            $content = $content.Replace($match.Value, $newSrc)
        }
    }
    
    $regexBg = 'url\([''"]?(https?://[^''")]+)[''"]?\)'
    $matchesBg = [regex]::Matches($content, $regexBg)
    
    foreach ($match in $matchesBg) {
        $url = $match.Groups[1].Value
        if ($url -match "unsplash|magnific|pinimg") {
            if ($urlCache.ContainsKey($url)) {
                $filename = $urlCache[$url]
            } else {
                $filename = "bg-" + [guid]::NewGuid().ToString().Substring(0,8) + ".jpg"
                $destPath = Join-Path $targetDir $filename
                try {
                    Invoke-WebRequest -Uri $url -OutFile $destPath -UseBasicParsing -TimeoutSec 15
                    $urlCache[$url] = $filename
                    Write-Host "Downloaded bg: $filename"
                } catch {
                    Write-Host "Failed to download bg: $url"
                    continue
                }
            }
            $newUrl = "url('<?php echo BASE_PATH; ?>/images/gallery/" + $filename + "')"
            $content = $content.Replace($match.Value, $newUrl)
        }
    }
    
    Set-Content $filePath $content -Encoding UTF8
    Write-Host "Finished processing $filePath"
}

Process-File "c:\xampp\htdocs\CSNExplore\gallery.php"
Process-File "c:\xampp\htdocs\CSNExplore\blogs.php"
