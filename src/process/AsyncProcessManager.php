<?php

namespace src\process;

use src\process\ProcessManagerInterface;
use src\ResourcePool;
use src\settings\Settings;

/**
 * Class AsyncProcessManager
 * @package src\process
 */
class AsyncProcessManager extends ProcessManager implements ProcessManagerInterface
{
    /**
     * AsyncProcessManager constructor.
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    /**
     *
     */
    public function single(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool($this->getSettings())
            );

        $process->getResourcePool()
            ->configurePoolForSingleProcess($this);

        $process->singleProcessOpen();
    }

    /**
     *
     */
    public function multiple(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool($this->getSettings())
            );

        $pool = $process->getResourcePool();
        $pool->configureResourcePoolForParallelProcesses($this);

        $process->multipleProcessesOpen();
    }
}