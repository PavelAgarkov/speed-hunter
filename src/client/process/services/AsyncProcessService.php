<?php

namespace src\client\process\services;

use src\client\process\AsyncProcess;
use src\client\process\services\ProcessServiceInterface;
use src\client\ResourcePool;
use src\client\settings\SettingsList;
use src\client\process\services\ProcessService;

/**
 * Class AsyncProcessManager
 * @package src\process
 */
class AsyncProcessService extends ProcessService implements ProcessServiceInterface
{
    /**
     * AsyncProcessManager constructor.
     * @param SettingsList $settingsList
     */
    public function __construct(SettingsList $settingsList)
    {
        parent::__construct($settingsList);
    }

    public function single(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool($this->getSettingsList())
            );

        $process->getResourcePool()
            ->configurePoolForSingleProcess($this);

        $process->singleProcessOpen();
    }

    public function multiple(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool($this->getSettingsList())
            );

        $pool = $process->getResourcePool();
        $pool->configureResourcePoolForParallelProcesses($this);

        $process->multipleProcessesOpen();
    }
}