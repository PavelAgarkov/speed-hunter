<?php

namespace src\process;

use src\process\ProcessManagerInterface;
use src\ResourcePool;
use src\settings\Settings;

class AsyncProcessManager extends ProcessManager implements ProcessManagerInterface
{
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }

    public function single(): void
    {
        $process = new AsyncProcess(
            new ResourcePool(
                $this->getSettings()
            )
        );
        $process->getResourcePool()
            ->configurePoolForSingleProcess($this);

        $process->singleProcessOpen();
    }

    public function multiple(): void
    {

    }
}