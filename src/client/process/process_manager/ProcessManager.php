<?php

namespace src\client\process\process_manager;

use src\client\data_manager\DataManagerForWorkers;
use src\client\ResourcePool;
use src\client\settings\SettingsList;

/**
 * Class ProcessManager
 * @package src\process
 */
class ProcessManager
{
    /**
     * @var array - массив объектов DataManager для каждого набора WorkerProcess
     */
    protected array $dataManagerForWorkers;

    /**
     * @var SettingsList
     */
    protected SettingsList $settingsList;

    /**
     * @var ResourcePool
     */
    private ResourcePool $ResourcePool;

    /**
     * ProcessManager constructor.
     * @param SettingsList $settingsList
     */
    public function __construct(SettingsList $settingsList)
    {
        $this->settingsList = $settingsList;
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
    public function setDataManagerForWorkers(
        string $key,
        DataManagerForWorkers $dataManagerForWorkers
    ): void {
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
}