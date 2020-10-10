<?php

namespace src\client\process\process_manager;

use src\client\process\AsyncProcess;
use src\client\process\process_manager\ProcessManagerInterface;
use src\client\ResourcePool;
use src\client\settings\SettingsList;

/**
 * Class AsyncProcessManager
 * @package src\process
 */
class AsyncProcessManager extends ProcessManager implements ProcessManagerInterface
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