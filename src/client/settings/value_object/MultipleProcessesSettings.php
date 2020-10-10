<?php

namespace src\client\settings\value_object;

use src\client\settings\value_object\Settings;

/**
 * Class MultipleAsyncProcessesSettings
 * @package src\settings
 */
final class MultipleProcessesSettings extends Settings
{
    /**
     * @var array
     */
    private array $dataPartitioning = array();

    /**
     * MultipleAsyncProcessesSettings constructor.
     * @param string $phpPath
     * @param string $jobName
     * @param int $numberJobs
     * @param array $dataPartitioning
     * @param int $shSizeForOneJob
     */
    public function __construct(string $phpPath,
                                string $jobName,
                                int $numberJobs,
                                array $dataPartitioning = [],
                                int $shSizeForOneJob = 1)
    {
        parent::__construct(
            $phpPath,
            $jobName,
            $shSizeForOneJob,
            $numberJobs);


        $this->dataPartitioning = $dataPartitioning;
    }

    /**
     * @return array
     */
    public function getDataPartitioning(): array
    {
        return $this->dataPartitioning;
    }
}