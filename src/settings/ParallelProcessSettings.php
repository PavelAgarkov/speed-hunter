<?php

namespace src\settings;

use src\settings\JobSettings;

/**
 * Class ParallelProcessSettings
 * @package src\settings
 */
class ParallelProcessSettings extends Settings implements SettingsInterface
{
    /**
     * ParallelProcessSettings constructor.
     * @param array $jobs
     */
    public function __construct(array $jobs)
    {
        parent::__construct();

        foreach ($jobs as $job) {
            $this->settingsObjects[$job['jobName']] = new JobSettings($job);
        }
    }


}