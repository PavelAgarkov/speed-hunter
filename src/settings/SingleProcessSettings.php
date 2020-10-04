<?php

namespace src\settings;

/**
 * Class SingleProcessSettings
 * @package src\settings
 */
final class SingleProcessSettings extends Settings
{
    /**
     * @var array
     */
    private array $data = array();

    /**
     * SingleProcessSettings constructor.
     * @param string $phpPath
     * @param string $jobName
     * @param array $data
     * @param int $shSizeForOneJob
     */
    public function __construct(string $phpPath,
                                string $jobName,
                                array $data,
                                int $shSizeForOneJob = 1)
    {
        parent::__construct(
            $phpPath,
            $jobName,
            $shSizeForOneJob,
            1
        );

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}