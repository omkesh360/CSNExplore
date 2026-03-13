<?php
/**
 * Cache Management System
 * Handles caching for API responses and database queries
 */

class CacheManager {
    private $cacheDir;
    private $cachePrefix = 'cache_';
    private $cacheTTL = 3600; // 1 hour default
    
    public function __construct($cacheDir = null) {
        $this->cacheDir = $cacheDir ?? __DIR__ . '/../cache';
        $this->ensureCacheDir();
    }
    
    private function ensureCacheDir() {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get($key) {
        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = json_decode(file_get_contents($file), true);
        
        // Check if cache has expired
        if (isset($data['expires']) && time() > $data['expires']) {
            unlink($file);
            return null;
        }
        
        return $data['value'] ?? null;
    }
    
    public function set($key, $value, $ttl = null) {
        $ttl = $ttl ?? $this->cacheTTL;
        $file = $this->getCacheFile($key);
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl,
            'created' => time(),
            'key' => $key
        ];
        
        file_put_contents($file, json_encode($data), LOCK_EX);
        return true;
    }
    
    public function delete($key) {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            unlink($file);
            return true;
        }
        return false;
    }
    
    public function clear() {
        $files = glob($this->cacheDir . '/' . $this->cachePrefix . '*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        return $count;
    }
    
    public function getStats() {
        $files = glob($this->cacheDir . '/' . $this->cachePrefix . '*');
        $totalSize = 0;
        $itemCount = 0;
        $items = [];
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $size = filesize($file);
                $totalSize += $size;
                $itemCount++;
                
                $data = json_decode(file_get_contents($file), true);
                $items[] = [
                    'key' => $data['key'] ?? basename($file),
                    'size' => $size,
                    'created' => $data['created'] ?? 0,
                    'expires' => $data['expires'] ?? 0,
                    'expired' => isset($data['expires']) && time() > $data['expires']
                ];
            }
        }
        
        return [
            'total_items' => $itemCount,
            'total_size' => $totalSize,
            'total_size_mb' => round($totalSize / 1024 / 1024, 2),
            'cache_dir' => $this->cacheDir,
            'items' => $items
        ];
    }
    
    private function getCacheFile($key) {
        $hash = md5($key);
        return $this->cacheDir . '/' . $this->cachePrefix . $hash . '.json';
    }
}

// Initialize cache manager
$cache = new CacheManager();
