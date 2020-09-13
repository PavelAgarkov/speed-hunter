<?php

namespace src\settings;

class SingleProcessSettings extends Settings
{
    public function __construct(array $jobSettings)
    {
        parent::__construct();
        $this->settingsObjects[] = new JobSettings($jobSettings);
    }
}