<?php

namespace src\settings;

use src\settings\JobSettings;

class ParallelProcessSettings extends Settings implements SettingsInterface
{
    public function __construct(array $jobs)
    {
        parent::__construct();
        foreach ($jobs as $job) {
            $this->settingsObjects[$job['jobName']] = new JobSettings($job);
        }
    }


}