<?php

namespace src\client\process\services;

use src\client\process\AsyncProcess;
use src\client\process\services\ProcessServiceInterface;
use src\client\ResourcePool;
use src\client\settings\SettingsList;
use src\client\process\services\ProcessService;
use src\client\settings\value_object\Settings;

/**
 * Class AsyncProcessManager
 * @package src\process
 */
class AsyncProcessService extends ProcessService implements ProcessServiceInterface
{
    /**
     * AsyncProcessManager constructor.
     * @param SettingsList|null $settingsList
     * @param Settings|null $settings
     */
    public function __construct(?SettingsList $settingsList,
                                ?Settings $settings)
    {
        parent::__construct($settingsList, $settings);
    }

    public function single(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool(
                    null,
                    $this->settings
                )
            );

        $process->getResourcePool()
            ->configurePoolForSingleProcess($this);

        $process->singleProcessOpen();
    }

    public function multiple(): void
    {
        $process =
            new AsyncProcess(
                new ResourcePool(
                    $this->getSettingsList(),
                    $this->settings
                )
            );

        $pool = $process->getResourcePool();
        $pool->configureResourcePoolForParallelProcesses($this);

        $process->multipleProcessesOpen();
    }
}