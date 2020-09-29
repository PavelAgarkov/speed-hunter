<?php

namespace src\settings;

/**
 * Class MultipleAsyncProcessesSettings
 * @package src\settings
 */
class MultipleAsyncProcessesSettings extends Settings
{
    /**
     * MultipleAsyncProcessesSettings constructor.
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