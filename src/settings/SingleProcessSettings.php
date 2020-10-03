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
     * @param int $shSizeForOneJob
     * @param array $data
     */
    public function __construct(string $phpPath,
                                string $jobName,
                                int $shSizeForOneJob,
                                array $data)
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