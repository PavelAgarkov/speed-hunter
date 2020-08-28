<?php

namespace src;

use src\process\ParallelProcessesManager;
use src\settings\ParallelProcessSettings;
use src\settings\Settings;

class Starting
{
    private ParallelProcessesManager $ParallelProcessManager;

    private Settings $Settings;

    public function __construct()
    {
        $this->ParallelProcessManager = new ParallelProcessesManager();
    }

    public function parallel(array $jobs): void
    {
        $this->Settings = $settings = new ParallelProcessSettings($jobs);
        $this->ParallelProcessManager
            ->configureProcessesLoop($settings)
            ->startProcessLoop()
            ->closeProcessLoop()
            ->clearResourcePool();
    }

    public function oneAsyncProcess(): void
    {

    }

    public function getProcessManager(): ParallelProcessesManager
    {
        return $this->ParallelProcessManager;
    }
}