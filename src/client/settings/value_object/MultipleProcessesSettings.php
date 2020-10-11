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
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        parent::__construct(
            $settings["phpPath"],
            $settings["jobName"],
            $settings["shSizeForOneJob"] ?? 1,
            $settings["numberJobs"]);


        $this->dataPartitioning = $settings["dataPartitioning"] ?? [];
    }

    /**
     * @return array
     */
    public function getDataPartitioning(): array
    {
        return $this->dataPartitioning;
    }
}