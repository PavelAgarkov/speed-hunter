<?php

namespace src\settings\value_object;

/**
 * Class Settings
 * @package src\settings
 */
abstract class Settings
{
    /**
     * @var string
     */
    protected string $phpPath;

    /**
     * @var string
     */
    protected string $jobName;

    /**
     * @var int
     */
    protected int $shSizeForOneJob;

    /**
     * @var int
     */
    protected int $numberJobs;

    /**
     * Settings constructor.
     * @param string $phpPath
     * @param string $jobName
     * @param int $shSizeForOneJob
     * @param int $numberJobs
     */
    public function __construct(string $phpPath,
                                string $jobName,
                                int $shSizeForOneJob,
                                int $numberJobs)
    {
        $this->phpPath = $phpPath;
        $this->jobName = $jobName;
        $this->shSizeForOneJob = $shSizeForOneJob;
        $this->numberJobs = $numberJobs;
    }

    /**
     * @return string
     */
    public function getPhpPath(): string
    {
        return $this->phpPath;
    }

    /**
     * @return string
     */
    public function getJobName(): string
    {
        return $this->jobName;
    }

    /**
     * @return int
     */
    public function getShSizeForOneJob(): int
    {
        return $this->shSizeForOneJob;
    }


    /**
     * @return int
     */
    public function getNumberJobs(): int
    {
        return $this->numberJobs;
    }

}