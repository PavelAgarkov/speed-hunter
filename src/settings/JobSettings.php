<?php

namespace src\settings;

/**
 * Class JobSettings
 * @package src\settings
 */
class JobSettings
{
    /**
     * @var array
     */
    private array $jobTypeSettings;

    /**
     * JobSettings constructor.
     * @param array $jobSettings
     */
    public function __construct(array $jobSettings)
    {
        $this->jobTypeSettings = $jobSettings;
    }

    /**
     * @return array
     */
    public function getJobTypeSettings(): array
    {
        return $this->jobTypeSettings;
    }
}