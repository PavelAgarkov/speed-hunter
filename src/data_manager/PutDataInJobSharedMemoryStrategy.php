<?php

namespace src\data_manager;

use src\SharedMemory;

class PutDataInJobSharedMemoryStrategy
{
    private DataManagerForWorkers $manager;

    private SharedMemory $memory;

    public function __construct(DataManagerForWorkers $dataManagerForWorkers, SharedMemory &$sharedMemory)
    {
        $this->manager = $dataManagerForWorkers;
        $this->memory = $sharedMemory;
    }

    public function putData() : void
    {
        if ((int)$this->manager->getDataForSet()['flagPartitioning'] == 1) {
            $this->manager->putDataIntoWorkerSharedMemory($this->memory);
        } else {
            $this->manager->putCommonDataIntoWorkers($this->memory);
        }
    }

}