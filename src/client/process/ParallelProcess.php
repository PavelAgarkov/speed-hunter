<?php

namespace src\client\process;

use src\client\process\services\ParallelProcessesService;
use src\client\ResourcePool;
use src\client\settings\value_object\MultipleProcessesSettings;
use src\client\settings\value_object\SingleProcessSettings;

/**
 * Class ParallelProcess
 * @package src\process
 */
class ParallelProcess extends Process
{
    /**
     * ParallelProcess constructor.
     * @param ResourcePool $pool
     */
    public function __construct(ResourcePool $pool)
    {
        parent::__construct($pool);
    }

    /** Метод для открытия нового процесса php передающего в открытый процесс данные о номере процесса
     *  относительно родительского, а так же данные для заполнения разделяемой памяти из созданного процесса.
     * @param string $phpPath
     * @param string $workerName
     * @param int $processNumber
     * @param int $numberMemoryKey
     * @param array $descriptors
     * @param int $memorySize
     * @param ParallelProcessesService $service
     */
    public function processOpen(string $phpPath,
                                string $workerName,
                                int $processNumber,
                                int $numberMemoryKey,
                                array $descriptors,
                                int $memorySize,
                                ParallelProcessesService &$service): void
    {

        $unserializeFlag = 0;

        // переписать это
        if (array_key_exists(
            $workerName,
            $service->getDataManagerForWorkers()
        )) {

            if ($this->getResourcePool()->getSettings() instanceof MultipleProcessesSettings) {
                if (!empty($service
                    ->getSettingsList()
                    ->getSettingsObject($workerName)
                    ->getDataPartitioning())) {
                    $unserializeFlag = 1;
                }
            }

            if ($this->getResourcePool()->getSettings() instanceof SingleProcessSettings) {
                if (!empty($service->getSettings()->getData())) {
                    $unserializeFlag = 1;
                }
            }
        }

        $proc = proc_open(
            "{$phpPath} {$workerName} {$processNumber} {$numberMemoryKey} {$memorySize} {$unserializeFlag}",
            $descriptors,
            $service->getProcessPipes()
        );
        $service->setProcesses($processNumber, $proc);
        $service->setPipes($processNumber, $service->getProcessPipes());
    }
}