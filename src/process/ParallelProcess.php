<?php

namespace src\process;

class ParallelProcess extends Process
{
    public function __construct()
    {
        parent::__construct();
    }

    public function processOpen(
        string $workerName,
        int $processNumber,
        int $numberMemoryKey,
        array $descriptors,
        int $memorySize,
        ParallelProcessesManager &$manager
    ): void

    {
        $unserializeFlag = 0;
        if (array_key_exists($workerName, $manager->getDataManagerForWorkers())) {
            $unserializeFlag = 1;
        } else $unserializeFlag = 0;

        $proc = proc_open(
            "php {$workerName}.php {$processNumber} {$numberMemoryKey} {$memorySize} {$unserializeFlag}",
            $descriptors,
            $manager->getProcessPipes());
        $manager->processes[$processNumber] = $proc;
        $manager->getPipes()[$processNumber] = $manager->getProcessPipes();
    }

}