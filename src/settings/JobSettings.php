<?php

namespace src\settings;

class JobSettings
{
    private array $jobTypeSettings;

    public function __construct(array $jobSettings)
    {
        $this->jobTypeSettings = $jobSettings;
    }

    public function getJobTypeSettings() : array
    {
        return $this->jobTypeSettings;
    }
}