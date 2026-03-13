<?php
// Simple file-based rate limiter

class RateLimiter {
    private $storageFile;
    private $maxRequests;
    private $windowSeconds;
    
    public function __construct($maxRequests = 1000, $windowSeconds = 900) {
        $this->storageFile = sys_get_temp_dir() . '/travelhub_rate_limit.json';
        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
    }
    
    private function getClientIp() {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    private function loadData() {
        if (!file_exists($this->storageFile)) {
            return [];
        }
        $content = file_get_contents($this->storageFile);
        return json_decode($content, true) ?: [];
    }
    
    private function saveData($data) {
        file_put_contents($this->storageFile, json_encode($data));
    }
    
    public function check() {
        $ip = $this->getClientIp();
        $now = time();
        $data = $this->loadData();
        
        // Clean old entries
        foreach ($data as $key => $entry) {
            if ($now - $entry['startTime'] > $this->windowSeconds) {
                unset($data[$key]);
            }
        }
        
        if (!isset($data[$ip])) {
            $data[$ip] = ['count' => 1, 'startTime' => $now];
            $this->saveData($data);
            return true;
        }
        
        $entry = $data[$ip];
        
        if ($now - $entry['startTime'] > $this->windowSeconds) {
            $data[$ip] = ['count' => 1, 'startTime' => $now];
            $this->saveData($data);
            return true;
        }
        
        if ($entry['count'] >= $this->maxRequests) {
            sendError('Too many requests, please try again later.', 429);
        }
        
        $data[$ip]['count']++;
        $this->saveData($data);
        return true;
    }
}

// Apply rate limiting
$rateLimiter = new RateLimiter();
$rateLimiter->check();
