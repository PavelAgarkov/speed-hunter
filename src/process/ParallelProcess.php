<?php

namespace src\process;

use src\ResourcePool;

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
     * @param ParallelProcessesManager $manager
     */
    public function processOpen(
        string $phpPath,
        string $workerName,
        int $processNumber,
        int $numberMemoryKey,
        array $descriptors,
        int $memorySize,
        ParallelProcessesManager &$manager
    ): void
    {

        $unserializeFlag = 0;
        if (array_key_exists(
                $workerName,
                $manager->getDataManagerForWorkers()
            ) &&
            (!empty($manager
                ->getSettingsList()
                ->getSettingsObject($workerName)
                ->getDataPartitioning()))) {

            $unserializeFlag = 1;
        }

        $proc = proc_open(
            "{$phpPath} {$workerName}.php {$processNumber} {$numberMemoryKey} {$memorySize} {$unserializeFlag}",
            $descriptors,
            $manager->getProcessPipes()
        );
        $manager->setProcesses($processNumber, $proc);
        $manager->setPipes($processNumber, $manager->getProcessPipes());
    }
}