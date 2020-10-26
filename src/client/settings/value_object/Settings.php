<?php

namespace src\client\settings\value_object;

use src\client\settings\settings_validator\BaseValidator;
use src\client\settings\settings_validator\BaseValidatorInterface;
use src\client\settings\settings_validator\MultipleProcessesSettingsValidator;
use src\client\settings\settings_validator\SingleProcessSettingsValidator;

/**
 * Class Settings
 * @package src\settings
 */
abstract class Settings
{
    /**
     * @var string|null
     */
    protected ?string $phpPath;

    /**
     * @var string|null
     */
    protected ?string $jobName;

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
     * @param string|null $phpPath
     * @param string|null $jobName
     * @param int|null $shSizeForOneJob
     * @param int $numberJobs
     * @param BaseValidatorInterface $validator
     */
    public function __construct(?string $phpPath,
                                ?string $jobName,
                                ?int $shSizeForOneJob,
                                int $numberJobs,
                                BaseValidatorInterface $validator)
    {
        $this->phpPath = $phpPath;
        $this->jobName = $jobName;
        $this->shSizeForOneJob = $shSizeForOneJob ?? 1;
        $this->numberJobs = $numberJobs;

        $validator->validate();
    }

    /**
     * @return string|null
     */
    public function getPhpPath(): ?string
    {
        return $this->phpPath;
    }

    /**
     * @return string|null
     */
    public function getJobName(): ?string
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