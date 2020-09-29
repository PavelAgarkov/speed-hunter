<?php

namespace src\process;

use src\data_manager\DataManagerForWorkers;
use src\ResourcePool;
use src\settings\Settings;

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
     * @var Settings
     */
    protected Settings $settings;

    /**
     * @var ResourcePool
     */
    private ResourcePool $ResourcePool;

    /**
     * ProcessManager constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return Settings
     */
    public function getSettings(): Settings
    {
        return $this->settings;
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