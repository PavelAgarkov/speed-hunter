<?php

namespace src\process;

use src\data_manager\DataManagerForWorkers;
use src\ResourcePool;
use src\settings\Settings;

class ProcessManager
{
    /**
     * @var array - массив объектов DataManager для каждого набора WorkerProcess
     */
    protected array $dataManagerForWorkers;

    protected Settings $settings;

    private ResourcePool $ResourcePool;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function getDataManagerForWorkers(): array
    {
        return $this->dataManagerForWorkers;
    }

    public function setDataManagerForWorkers(string $key, DataManagerForWorkers $dataManagerForWorkers): void
    {
        $this->dataManagerForWorkers[$key] = $dataManagerForWorkers;
    }

    public function setResourcePool(ResourcePool $resourcePool) : void
    {
        $this->ResourcePool = $resourcePool;
    }

    public function getResourcePool() : ResourcePool
    {
        return $this->ResourcePool;
    }

}