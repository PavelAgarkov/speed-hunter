<?php

namespace src\settings;

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
     * @param int $shSizeForOneJob
     * @param array $dataPartitioning
     */
    public function __construct(string $phpPath,
                                string $jobName,
                                int $numberJobs,
                                int $shSizeForOneJob,
                                array $dataPartitioning = [])
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