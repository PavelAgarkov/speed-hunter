<?php

namespace src\settings;

class MultipleAsyncProcessesSettings extends Settings
{
    public function __construct(array $jobs)
    {
        parent::__construct();
        foreach ($jobs as $job) {
            $this->settingsObjects[$job['jobName']] = new JobSettings($job);
        }
    }
}