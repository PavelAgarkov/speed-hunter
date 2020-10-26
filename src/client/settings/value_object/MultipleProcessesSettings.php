<?php

namespace src\client\settings\value_object;

use src\client\Client;
use src\client\settings\settings_validator\MultipleProcessesSettingsValidator;
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
        if(!isset($settings["shSizeForOneJob"])) {
            $settings["shSizeForOneJob"] = Client::weighData([]);
        }

        $validator = new MultipleProcessesSettingsValidator($this);

        $this->dataPartitioning = $settings["dataPartitioning"] ?? [
                "flagPartitioning" => 0,
                "dataToPartitioning" => []
            ];

        parent::__construct(
            $settings["phpPath"] ?? null,
            $settings["jobName"] ?? null,
            $settings["shSizeForOneJob"] ?? 1,
            $settings["numberJobs"],
            $validator);
    }

    /**
     * @return array
     */
    public function getDataPartitioning(): array
    {
        return $this->dataPartitioning;
    }

    /**
     * @return int
     */
    public function getFlagPartitioning(): int
    {
        return $this->dataPartitioning["flagPartitioning"];
    }

    /**
     * @return array
     */
    public function getDataToPartitioning(): array
    {
        return $this->dataPartitioning["dataToPartitioning"];
    }
}