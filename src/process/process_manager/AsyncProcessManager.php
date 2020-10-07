<?php

namespace src\process\process_manager;

use src\process\AsyncProcess;
use src\process\process_manager\ProcessManagerInterface;
use src\ResourcePool;
use src\settings\SettingsList;

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