<?php
/**
 * Automated Backup System
 * Creates backups of database and important files
 */

class BackupManager {
    private $backupDir;
    private $maxBackups = 10;
    
    public function __construct() {
        $this->backupDir = __DIR__ . '/../backups';
        
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Create full backup
     */
    public function createFullBackup() {
        $timestamp = date('Y-m-d_H-i-s');
        $backupName = "backup_{$timestamp}";
        $backupPath = $this->backupDir . '/' . $backupName;
        
        mkdir($backupPath, 0755, true);
        
        $results = [
            'timestamp' => $timestamp,
            'database' => false,
            'data_files' => false,
            'uploads' => false,
            'config' => false
        ];
        
        // Backup database
        $results['database'] = $this->backupDatabase($backupPath);
        
        // Backup data files
        $results['data_files'] = $this->backupDataFiles($backupPath);
        
        // Backup uploads
        $results['uploads'] = $this->backupUploads($backupPath);
        
        // Backup config
        $results['config'] = $this->backupConfig($backupPath);
        
        // Create backup info file
        file_put_contents(
            $backupPath . '/backup_info.json',
            json_encode($results, JSON_PRETTY_PRINT)
        );
        
        // Compress backup
        $this->compressBackup($backupPath, $backupName);
        
        // Clean old backups
        $this->cleanOldBackups();
        
        // Log backup
        if (file_exists(__DIR__ . '/logger.php')) {
            require_once __DIR__ . '/logger.php';
            Logger::info("Backup created: {$backupName}", $results);
        }
        
        return $results;
    }
    
    /**
     * Backup database
     */
    private function backupDatabase($backupPath) {
        try {
            $dbPath = __DIR__ . '/../database/travelhub.db';
            
            if (file_exists($dbPath)) {
                $destPath = $backupPath . '/database';
                mkdir($destPath, 0755, true);
                
                copy($dbPath, $destPath . '/travelhub.db');
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Backup data files
     */
    private function backupDataFiles($backupPath) {
        try {
            $dataDir = __DIR__ . '/../data';
            $destPath = $backupPath . '/data';
            
            if (is_dir($dataDir)) {
                $this->copyDirectory($dataDir, $destPath);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Backup uploads
     */
    private function backupUploads($backupPath) {
        try {
            $uploadsDir = __DIR__ . '/../public/images';
            $destPath = $backupPath . '/images';
            
            if (is_dir($uploadsDir)) {
                $this->copyDirectory($uploadsDir, $destPath);
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Backup config files
     */
    private function backupConfig($backupPath) {
        try {
            $configFiles = [
                __DIR__ . '/config.php',
                __DIR__ . '/../.env',
                __DIR__ . '/../.htaccess'
            ];
            
            $destPath = $backupPath . '/config';
            mkdir($destPath, 0755, true);
            
            foreach ($configFiles as $file) {
                if (file_exists($file)) {
                    copy($file, $destPath . '/' . basename($file));
                }
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $dest) {
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        
        $dir = opendir($source);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcPath = $source . '/' . $file;
                $destPath = $dest . '/' . $file;
                
                if (is_dir($srcPath)) {
                    $this->copyDirectory($srcPath, $destPath);
                } else {
                    copy($srcPath, $destPath);
                }
            }
        }
        
        closedir($dir);
    }
    
    /**
     * Compress backup folder
     */
    private function compressBackup($backupPath, $backupName) {
        if (class_exists('ZipArchive')) {
            $zip = new ZipArchive();
            $zipFile = $this->backupDir . '/' . $backupName . '.zip';
            
            if ($zip->open($zipFile, ZipArchive::CREATE) === true) {
                $this->addDirectoryToZip($zip, $backupPath, '');
                $zip->close();
                
                // Remove uncompressed backup
                $this->removeDirectory($backupPath);
            }
        }
    }
    
    /**
     * Add directory to zip
     */
    private function addDirectoryToZip($zip, $source, $localPath) {
        $dir = opendir($source);
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcPath = $source . '/' . $file;
                $zipPath = $localPath . '/' . $file;
                
                if (is_dir($srcPath)) {
                    $zip->addEmptyDir($zipPath);
                    $this->addDirectoryToZip($zip, $srcPath, $zipPath);
                } else {
                    $zip->addFile($srcPath, $zipPath);
                }
            }
        }
        
        closedir($dir);
    }
    
    /**
     * Remove directory recursively
     */
    private function removeDirectory($dir) {
        if (!is_dir($dir)) {
            return;
        }
        
        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
    
    /**
     * Clean old backups
     */
    private function cleanOldBackups() {
        $backups = glob($this->backupDir . '/backup_*.zip');
        
        // Sort by modification time (newest first)
        usort($backups, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Remove old backups
        $toRemove = array_slice($backups, $this->maxBackups);
        
        foreach ($toRemove as $backup) {
            unlink($backup);
        }
    }
    
    /**
     * List available backups
     */
    public function listBackups() {
        $backups = glob($this->backupDir . '/backup_*.zip');
        $list = [];
        
        foreach ($backups as $backup) {
            $list[] = [
                'name' => basename($backup),
                'size' => filesize($backup),
                'date' => date('Y-m-d H:i:s', filemtime($backup))
            ];
        }
        
        // Sort by date (newest first)
        usort($list, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return $list;
    }
    
    /**
     * Restore backup
     */
    public function restoreBackup($backupName) {
        $backupFile = $this->backupDir . '/' . $backupName;
        
        if (!file_exists($backupFile)) {
            return false;
        }
        
        // Extract backup
        $zip = new ZipArchive();
        
        if ($zip->open($backupFile) === true) {
            $extractPath = $this->backupDir . '/restore_temp';
            $zip->extractTo($extractPath);
            $zip->close();
            
            // Restore files
            // Note: This is a basic implementation
            // In production, you'd want more sophisticated restore logic
            
            if (file_exists(__DIR__ . '/logger.php')) {
                require_once __DIR__ . '/logger.php';
                Logger::info("Backup restored: {$backupName}");
            }
            
            return true;
        }
        
        return false;
    }
}

// CLI usage
if (php_sapi_name() === 'cli') {
    $backup = new BackupManager();
    
    if (isset($argv[1])) {
        switch ($argv[1]) {
            case 'create':
                echo "Creating backup...\n";
                $result = $backup->createFullBackup();
                echo "Backup created successfully!\n";
                print_r($result);
                break;
                
            case 'list':
                echo "Available backups:\n";
                $backups = $backup->listBackups();
                foreach ($backups as $b) {
                    echo "- {$b['name']} ({$b['size']} bytes) - {$b['date']}\n";
                }
                break;
                
            default:
                echo "Usage: php backup.php [create|list]\n";
        }
    } else {
        echo "Usage: php backup.php [create|list]\n";
    }
}
