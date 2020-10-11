<?php

namespace src\client\process\services;

use src\client\data_manager\DataManagerForWorkers;
use src\client\ResourcePool;
use src\client\settings\SettingsList;
use src\client\settings\value_object\Settings;

/**
 * Class ProcessManager
 * @package src\process
 */
class ProcessService
{
    /**
     * @var array - массив объектов DataManager для каждого набора WorkerProcess
     */
    protected array $dataManagerForWorkers;

    /**
     * @var SettingsList|null
     */
    protected ?SettingsList $settingsList;

    /**
     * @var Settings|null
     */
    protected ?Settings $settings;

    /**
     * @var ResourcePool
     */
    private ResourcePool $ResourcePool;

    /**
     * ProcessManager constructor.
     * @param SettingsList|null $settingsList
     * @param Settings|null $settings
     */
    public function __construct(?SettingsList $settingsList,
                                ?Settings $settings)
    {
        $this->settingsList = $settingsList;
        $this->settings = $settings;
    }

    /**
     * @return SettingsList
     */
    public function getSettingsList(): SettingsList
    {
        return $this->settingsList;
    }

    /**
     * @return array
     */
    public function getDataManagerForWorkers(): array
    {
        return $this->dataManagerForWorkers;
    }

    /**
     * @param string $key
     * @param DataManagerForWorkers $dataManagerForWorkers
     */
    public function setDataManagerForWorkers(string $key,
                                             DataManagerForWorkers $dataManagerForWorkers): void {
        $this->dataManagerForWorkers[$key] = $dataManagerForWorkers;
    }

    /**
     * @param ResourcePool $resourcePool
     */
    public function setResourcePool(ResourcePool $resourcePool): void
    {
        $this->ResourcePool = $resourcePool;
    }

    /**
     * @return ResourcePool
     */
    public function getResourcePool(): ResourcePool
    {
        return $this->ResourcePool;
    }

    /**
     * @return Settings|null
     */
    public function getSettings(): ?Settings
    {
        return $this->settings;
    }
}