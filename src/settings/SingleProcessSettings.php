<?php

namespace src\settings;

/**
 * Class SingleProcessSettings
 * @package src\settings
 */
class SingleProcessSettings extends Settings
{
    /**
     * SingleProcessSettings constructor.
     * @param array $jobSettings
     */
    public function __construct(array $jobSettings)
    {
        parent::__construct();

        $this->settingsObjects[] = new JobSettings($jobSettings);
    }
}