<?php

namespace W3speedster;

require_once(__DIR__ . '/W3speedster.php');

/**
 * Class Cron
 * Handles custom cron job registration and execution.
 */
class CronManager extends W3speedster
{
    public $cronJobs = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->cronJobs = $this->w3GetOption('cron_jobs', 1);
        $this->w3RemoveCronJob('w3GeneratePreloadCss');
        $this->w3RemoveCronJob('w3speedsterOptimizeImageCallback');
        $this->w3RemoveCronJob('createSiteMapJsonFile');
        $this->w3RemoveCronJob('w3speedsterSetPreloadCache');
        $this->w3AddCronJob('deleteCoreWebVitalsLog', 'deleteCoreWebVitalsLog', 86400);
        $this->w3AddCronJob('deleteSettingsChangeLog', 'deleteSettingsChangeLog', 86400);
        $this->w3AddCronJob('w3CheckLicenseKey', 'w3CheckLicenseKey', 86400);
    }

    /**
     * Adds a cron job if it doesn't already exist.
     *
     * @param string $jobName
     * @param string $functionName
     * @param int $interval Duration in seconds between executions
     */
    public function w3AddCronJob(string $jobName, string $functionName, int $interval): void
    {
        $cronJobs = $this->cronJobs;
        if (!isset($cronJobs[$jobName]) || $cronJobs[$jobName]['function_name'] != $functionName || $cronJobs[$jobName]['duration'] != $interval) {
            $cronJobs[$jobName] = [
                'function_name' => $functionName,
                'duration' => $interval,
                'last_run' => 0
            ];
            $this->cronJobs = $cronJobs;
            $this->w3UpdateOption('cron_jobs', $this->cronJobs, 0, 1);
        }
    }

    /**
     * Removes a cron job by name.
     *
     * @param string $jobName
     */
    public function w3RemoveCronJob(string $jobName): void
    {
        $cronJobs = $this->cronJobs;
        if (isset($cronJobs[$jobName])) {
            unset($cronJobs[$jobName]);
            $this->cronJobs = $cronJobs;
            $this->w3UpdateOption('cron_jobs', $cronJobs, 0, 1);
        }
    }

    /**
     * Runs due cron jobs.
     */
    public function runCronJobs(): void
    {
        $cronJobs = $this->cronJobs;
        $currentTime = time();
        foreach ($cronJobs as $jobName => &$job) {
            if (($currentTime - $job['last_run']) >= $job['duration']) {
                $function = $job['function_name'] ?? null;
                if ($function && method_exists($this, $function)) {
                    try {
                        $this->$function();
                        $job['last_run'] = $currentTime;
                    } catch (\Throwable $e) {
                        $this->logError("[W3speedster Cron] Error running '$function': " . $e->getMessage());
                    }
                } else {
                    $this->logError("[W3speedster Cron] Method '$function' does not exist.");
                }
            }
        }
        unset($job);
        $this->cronJobs = $cronJobs;
        $this->w3UpdateOption('cron_jobs', $cronJobs, 0, 1);
    }
}
