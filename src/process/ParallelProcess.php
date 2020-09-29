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
     * @param string $workerName
     * @param int $processNumber
     * @param int $numberMemoryKey
     * @param array $descriptors
     * @param int $memorySize
     * @param ParallelProcessesManager $manager
     */
    public function processOpen(
        string $workerName,
        int $processNumber,
        int $numberMemoryKey,
        array $descriptors,
        int $memorySize,
        ParallelProcessesManager &$manager
    ): void {
        $unserializeFlag = 0;
        if (array_key_exists($workerName, $manager->getDataManagerForWorkers())) {
            $unserializeFlag = 1;
        } else {
            $unserializeFlag = 0;
        }

        $proc = proc_open(
            "php {$workerName}.php {$processNumber} {$numberMemoryKey} {$memorySize} {$unserializeFlag}",
            $descriptors,
            $manager->getProcessPipes()
        );
        $manager->setProcesses($processNumber, $proc);
        $manager->setPipes($processNumber, $manager->getProcessPipes());
    }
}